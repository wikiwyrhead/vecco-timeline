(function(){
  // Performance constants - avoiding magic numbers
  const CONFIG = {
    MOBILE_BREAKPOINT: 560,
    MOBILE_PADDING: { MIN: 26, MAX: 44, VIEWPORT_RATIO: 0.09 },
    DESKTOP_EDGE_FACTOR: 0.20,
    BOUNCE_AMPLITUDE: 12,
    BOUNCE_DURATION: 180,
    MOMENTUM_DURATION: 700,
    WHEEL_DURATION: 420,
    VELOCITY_CLAMP: { MIN: -1.5, MAX: 1.5 },
    VELOCITY_DISTANCE_FACTOR: 0.45,
    SCROLLBAR_THUMB_MIN: 28,
    SAMPLE_WINDOW: 100, // ms
    EDGE_SETTLE_DELAY: 200,
    RESIZE_DEBOUNCE: 150
  };
  
  // Debounce helper for resize events
  function debounce(fn, delay) {
    var timer = null;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function() { fn.apply(context, args); }, delay);
    };
  }
  
  function initTrack(track){
    if (!track || track.dataset.vtlInit === '1') return;
    track.dataset.vtlInit = '1';
    
    // Store cleanup function for memory leak prevention (P2)
    var cleanupFns = [];
    
    // Error handling wrapper
    function safeCall(fn, errorMsg) {
      try { return fn(); }
      catch(e) { 
        if (console && console.warn) console.warn('Vecco Timeline: ' + errorMsg, e);
        return null;
      }
    }
    // Drag-to-scroll
    var isDown=false, startX=0, startScroll=0, dragged=false, rect=null;
    var lastX=0, lastT=0, vx=0, rafId=0;
    var samples=[]; // recent {x,t} samples for better velocity
    var wheelAnim=null; // {from,to,start,dur,raf}
    var transX=0, bouncing=false; // edge rubber band & bounce state
    track.style.cursor = 'grab';
    // Custom scrollbar elements under the same wrapper (must be inside initTrack)
    var wrap = track.closest('.vecco-timeline');
    var isCentered = !!(wrap && wrap.classList.contains('is-centered'));
    var bar  = wrap && wrap.querySelector('.vecco-scrollbar.vecco-scrollbar-horizontal');
    var isFullwidth = !!(wrap && wrap.classList.contains('is-fullwidth'));
    var dragEl = bar && bar.querySelector('.vecco-scrollbar-drag');
    var sb = { dragging:false, startX:0, startLeft:0 };
    // Ensure an end spacer exists to extend scrollWidth on mobile
    var endSpacer = track.querySelector('.vecco-tl-endspacer');
    if (!endSpacer){
      endSpacer = document.createElement('div');
      endSpacer.className = 'vecco-tl-endspacer';
      endSpacer.style.flex = '0 0 0px';
      endSpacer.style.width = '0px';
      endSpacer.style.alignSelf = 'stretch';
      track.appendChild(endSpacer);
    }
    function sbUpdate(){
      if(!bar || !dragEl) return;
      safeCall(function() {
        var barW = bar.clientWidth || 1;
        var scrollW = track.scrollWidth || 1;
        var viewW = track.clientWidth || 1;
        var ratio = Math.max(0, Math.min(1, viewW/scrollW));
        var thumbW = Math.max(CONFIG.SCROLLBAR_THUMB_MIN, Math.round(barW * ratio));
        dragEl.style.width = thumbW + 'px';
        var maxLeft = Math.max(0, barW - thumbW);
        var posRatio = (track.scrollLeft / Math.max(1, scrollW - viewW));
        var left = Math.round(maxLeft * posRatio);
        dragEl.style.transform = 'translate3d(' + left + 'px,0,0)';
      }, 'scrollbar update failed');
    }
    function sbScrollTo(clientX){
      if(!bar || !dragEl) return;
      var rectBar = bar.getBoundingClientRect();
      var barW = bar.clientWidth || 1;
      var scrollW = track.scrollWidth || 1;
      var viewW = track.clientWidth || 1;
      var thumbW = dragEl.offsetWidth || 1;
      var maxLeft = Math.max(0, barW - thumbW);
      var x = Math.max(0, Math.min(maxLeft, clientX - rectBar.left - thumbW/2));
      var posRatio = x / Math.max(1, maxLeft);
      track.scrollLeft = Math.round(posRatio * Math.max(0, scrollW - viewW));
      sbUpdate();
    }
    function clamp(v,min,max){ return v<min?min:(v>max?max:v); }
    function easeOutCubic(t){ t=1-Math.max(0,Math.min(1,t)); return 1-(t*t*t); }
    function cancelMomentum(){ if(rafId){ cancelAnimationFrame(rafId); rafId=0; } vx=0; }
    function cancelWheel(){ if(wheelAnim && wheelAnim.raf){ cancelAnimationFrame(wheelAnim.raf); } wheelAnim=null; }
    function setTransform(x){ safeCall(function() { transX=x; track.style.transform = x?('translateX('+x+'px)'):''; }, 'transform failed'); }
    function clearTransform(){ setTransform(0); }
    function bounce(dir){
      if (bouncing) return; bouncing=true;
      var start = performance.now();
      var amp = CONFIG.BOUNCE_AMPLITUDE * (window.devicePixelRatio>1?1:1);
      function step(t){
        var p = (t - start)/CONFIG.BOUNCE_DURATION; if (p>1) p=1;
        // out-and-back: ease out to amp, then ease back to 0
        var half = 0.5;
        var dx;
        if (p < half){ dx = dir * amp * easeOutCubic(p/half); }
        else { dx = dir * amp * (1 - easeOutCubic((p-half)/half)); }
        setTransform(dx);
        if (p < 1) requestAnimationFrame(step); else { clearTransform(); bouncing=false; }
      }
      requestAnimationFrame(step);
    }
    function onDown(e){
      if (e.button !== undefined && e.button !== 0) return;
      isDown = true; dragged=false; samples.length=0; cancelMomentum(); cancelWheel();
      rect = track.getBoundingClientRect();
      var pageX = e.touches ? e.touches[0].pageX : e.pageX;
      startX = pageX - rect.left;
      startScroll = track.scrollLeft;
      track.classList.add('vtl-dragging');
      document.body.style.userSelect = 'none';
      lastX = pageX; lastT = performance.now();
      samples.push({x:pageX,t:lastT});
    }
    function onMove(e){
      if(!isDown) return;
      e.preventDefault();
      var pageX = e.touches ? e.touches[0].pageX : e.pageX;
      var x = pageX - rect.left;
      var walk = x - startX;
      if (Math.abs(walk) > 2) dragged = true;
      var now = performance.now();
      var dt = Math.max(1, now - lastT);
      // instantaneous velocity in px/ms (negative to match scroll direction)
      vx = - (pageX - lastX) / dt;
      var desired = startScroll - walk;
      var maxScroll = track.scrollWidth - track.clientWidth;
      // Edge resistance with rubber band via translateX following finger/wheel direction
      if (desired < 0){
        track.scrollLeft = 0;
        var over = desired - 0; // negative when dragging right at left edge
        setTransform(-over * 0.25); // make it positive (follow finger)
      } else if (desired > maxScroll){
        track.scrollLeft = maxScroll;
        var overR = desired - maxScroll; // positive when dragging left at right edge (since walk < 0)
        setTransform(-overR * 0.25); // make it negative (follow finger)
      } else {
        track.scrollLeft = desired;
        if (transX) clearTransform();
      }
      lastX = pageX; lastT = now;
      samples.push({x:pageX,t:now});
      // Keep last samples within time window
      while(samples.length && (now - samples[0].t) > CONFIG.SAMPLE_WINDOW){ samples.shift(); }
    }
    function onUp(){
      if(!isDown) return;
      isDown=false; track.classList.remove('vtl-dragging'); document.body.style.userSelect = '';
      // Snap back if we are visually stretched
      if (transX){
        // quick ease back
        var start = performance.now(); var startXform = transX;
        function back(t){ var p=(t-start)/160; if(p>1)p=1; setTransform(startXform*(1-easeOutCubic(p))); if(p<1) requestAnimationFrame(back); else clearTransform(); }
        requestAnimationFrame(back);
      }
      // Compute averaged velocity over recent samples to avoid jitter
      if(samples.length>=2){
        var first=samples[0], last=samples[samples.length-1];
        var dx = last.x - first.x, dt = Math.max(1, last.t-first.t);
        vx = - dx / dt; // px/ms
      }
      var maxScroll = track.scrollWidth - track.clientWidth;
      // Convert velocity to momentum distance with exponential decay
      var velocity = clamp(vx, CONFIG.VELOCITY_CLAMP.MIN, CONFIG.VELOCITY_CLAMP.MAX);
      var duration = CONFIG.MOMENTUM_DURATION;
      var distance = velocity * 1000 * CONFIG.VELOCITY_DISTANCE_FACTOR;
      var start = performance.now();
      var from = track.scrollLeft;
      var to = clamp(from + distance* (window.devicePixelRatio>1?1.1:1), 0, maxScroll);
      if (Math.abs(to-from) < 2) return;
      function step(now){
        var t = (now - start)/duration; if(t>1) t=1;
        var eased = easeOutCubic(t);
        var next = from + (to-from)*eased;
        // Handle hitting edges: clamp and bounce
        if (next < 0){ track.scrollLeft = 0; if (!bouncing) bounce(1); rafId=0; return; }
        if (next > maxScroll){ track.scrollLeft = maxScroll; if (!bouncing) bounce(-1); rafId=0; return; }
        track.scrollLeft = next;
        if(t<1) { rafId = requestAnimationFrame(step); } else { rafId=0; }
      }
      rafId = requestAnimationFrame(step);
    }
    track.addEventListener('mousedown', onDown);
    track.addEventListener('mousemove', onMove);
    ['mouseup','mouseleave'].forEach(function(evt){ track.addEventListener(evt, onUp); });
    track.addEventListener('touchstart', onDown, {passive:true});
    track.addEventListener('touchmove', onMove, {passive:false});
    track.addEventListener('touchend', onUp, {passive:true});
    track.addEventListener('click', function(e){ if (dragged) { e.preventDefault(); e.stopPropagation(); }}, true);
    // Sync scrollbar with track
    if (bar && dragEl){
      sbUpdate();
      var scrollHandler = function() { sbUpdate(); };
      var resizeHandler = function() { sbUpdate(); };
      track.addEventListener('scroll', scrollHandler);
      window.addEventListener('resize', resizeHandler, { passive:true });
      cleanupFns.push(function() {
        track.removeEventListener('scroll', scrollHandler);
        window.removeEventListener('resize', resizeHandler);
      });
      // Click bar to jump
      var barClickHandler = function(e){
        if (e.target === dragEl) return; // handled by drag below
        sbScrollTo(e.clientX);
      };
      bar.addEventListener('mousedown', barClickHandler);
      cleanupFns.push(function() { bar.removeEventListener('mousedown', barClickHandler); });
      // Drag thumb
      dragEl.addEventListener('mousedown', function(e){
        e.preventDefault(); sb.dragging = true; sb.startX = e.clientX;
        var m = dragEl.style.transform.match(/translate3d\(([-0-9.]+)px/);
        sb.startLeft = m ? parseFloat(m[1]) : 0;
        document.body.style.userSelect = 'none';
      });
      window.addEventListener('mousemove', function(e){
        if(!sb.dragging) return;
        e.preventDefault(); sbScrollTo(sb.startLeft + (e.clientX - sb.startX) + bar.getBoundingClientRect().left + (dragEl.offsetWidth||0)/2);
      });
      window.addEventListener('mouseup', function(){ if(sb.dragging){ sb.dragging=false; document.body.style.userSelect=''; } });
      // Touch support for thumb
      dragEl.addEventListener('touchstart', function(e){ sb.dragging=true; sb.startX = e.touches[0].clientX; var m=dragEl.style.transform.match(/translate3d\(([-0-9.]+)px/); sb.startLeft = m ? parseFloat(m[1]) : 0; }, {passive:true});
      window.addEventListener('touchmove', function(e){ if(!sb.dragging) return; sbScrollTo(sb.startLeft + (e.touches[0].clientX - sb.startX) + bar.getBoundingClientRect().left + (dragEl.offsetWidth||0)/2); }, {passive:false});
      window.addEventListener('touchend', function(){ if(sb.dragging){ sb.dragging=false; } }, {passive:true});
    }
    // Smooth wheel scroll to horizontal with improved desktop experience
    if (!track.dataset.disableWheel) {
      var wheelHandler = function(e){
        // MUST preventDefault() FIRST before any logic (browser requirement)
        e.preventDefault();
        
        // Use whichever axis has more movement
        var rawX = Math.abs(e.deltaX);
        var rawY = Math.abs(e.deltaY);
        
        // Most desktop mice only send deltaY (vertical), use that for horizontal timeline scroll
        var raw = rawX > rawY ? e.deltaX : e.deltaY;
        
        // Don't ignore small movements - they matter!
        if (Math.abs(raw) < 0.1) return;
        
        cancelMomentum();
        
        var maxScroll = track.scrollWidth - track.clientWidth;
        if (maxScroll <= 0) { return; }
        
        // Normalize by deltaMode (0=pixel, 1=line, 2=page)
        var unit = 1;
        if (e.deltaMode === 1) unit = 25; // lines -> px (moderate)
        else if (e.deltaMode === 2) unit = Math.max(200, track.clientWidth * 0.8);
        
        // Smooth, controlled sensitivity - increased for faster scroll
        var scale = 1.4;
        var delta = raw * unit * scale;
        
        var current = track.scrollLeft;
        var target = clamp(current + delta, 0, maxScroll);
        
        // Smooth animation with instant start - extended for smoother feel
        var now = performance.now();
        var dur = 280; // Slightly longer for extra smoothness
        
        // Cancel previous animation
        if (wheelAnim && wheelAnim.raf) {
          cancelAnimationFrame(wheelAnim.raf);
        }
        
        wheelAnim = { from: current, to: target, start: now, dur: dur, raf: 0 };
        
        function step(t){
          if(!wheelAnim) return;
          var elapsed = t - wheelAnim.start;
          var p = elapsed / wheelAnim.dur;
          if (p > 1) p = 1;
          
          // Ease out for smooth deceleration
          var eased = 1 - Math.pow(1 - p, 3);
          var pos = wheelAnim.from + (wheelAnim.to - wheelAnim.from) * eased;
          
          track.scrollLeft = pos;
          
          if (p < 1) {
            wheelAnim.raf = requestAnimationFrame(step);
          } else {
            wheelAnim = null;
          }
        }
        
        wheelAnim.raf = requestAnimationFrame(step);
      };
      
      track.addEventListener('wheel', wheelHandler, { passive:false });
      cleanupFns.push(function() { track.removeEventListener('wheel', wheelHandler); });
    }
    
    // Symmetric edge padding so hard-left and hard-right feel balanced
    function updateEdgePadding(){
      var viewW = track.clientWidth || 0;
      var items = track.querySelectorAll('.vecco-tl-item, .timeline-item');
      var first = items[0];
      var last  = items[items.length-1];
      var isMobile = (window.innerWidth || 0) <= CONFIG.MOBILE_BREAKPOINT;
      // On mobile, use symmetric edge padding that scales with viewport width
      if (isMobile){
        var vw = (window.innerWidth || 0);
        // Proportional viewport padding with configured limits
        var mobilePad = Math.max(CONFIG.MOBILE_PADDING.MIN, Math.min(CONFIG.MOBILE_PADDING.MAX, Math.round(vw * CONFIG.MOBILE_PADDING.VIEWPORT_RATIO)));
        track.style.paddingLeft = mobilePad + 'px';
        track.style.paddingRight = mobilePad + 'px';
        // Help some browsers consider end padding in scroll calculations
        track.style.scrollPaddingRight = mobilePad + 'px';
        // Disable end spacer on mobile for true symmetry
        if (endSpacer){ endSpacer.style.flex = '0 0 0px'; endSpacer.style.width = '0px'; }
        // Ensure scroll bounds and update scrollbar
        var maxScrollM = Math.max(0, track.scrollWidth - track.clientWidth);
        if (track.scrollLeft > maxScrollM) track.scrollLeft = maxScrollM;
        if (track.scrollLeft < 0) track.scrollLeft = 0;
        sbUpdate();
        return;
      }
      // Desktop/tablet: disable end spacer and use computed symmetric edges
      if (endSpacer){ endSpacer.style.flex = '0 0 0px'; endSpacer.style.width = '0px'; }
      function sidePadFor(el){
        var w = el ? Math.round((el.getBoundingClientRect().width || el.offsetWidth || 0)) : Math.round(viewW/2);
        var base = Math.max(0, Math.round((viewW - w)/2));
        // isMobile handled above; only desktop/tablet reach here
        // Tuning knobs: closer-to-edge factor and min/max clamps
        var factor = CONFIG.DESKTOP_EDGE_FACTOR;
        var minPad = 6;
        var maxPad = Math.max(40, Math.round(w * 0.45));
        var pad = Math.round(base * factor);
        // Hard cap keeps it near edge even after late resizes/font swaps
        var hardCap = 6;
        return Math.min(hardCap, Math.max(minPad, Math.min(pad, maxPad)));
      }
      if (isCentered){
        // Centered mode: rely on CSS padding/mask; ensure JS-created endSpacer is off
        track.style.paddingLeft  = '0px';
        track.style.paddingRight = '0px';
        if (endSpacer){ endSpacer.style.flex = '0 0 0px'; endSpacer.style.width = '0px'; }
      } else {
        // Original mode: do not alter paddings or spacers here
      }
      // Ensure scrollLeft remains within bounds after padding change
      var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      if (track.scrollLeft > maxScroll) track.scrollLeft = maxScroll;
      if (track.scrollLeft < 0) track.scrollLeft = 0;
      // Update custom scrollbar thumb after geometry change
      sbUpdate();
    }
    // Center once after geometry settles so left/right gaps are balanced on first paint
    var didCenter = false;
    function centerOnce(){
      if (didCenter) return;
      // Center on initial load for centered variants and full width (balanced first view)
      if (!isCentered && !isFullwidth) { didCenter = true; return; }
      // Honor global setting from data attribute
      if (!track.hasAttribute('data-center-initial')) { didCenter = true; return; }
      try{
        var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
        var mid = Math.round(maxScroll / 2);
        track.scrollLeft = mid;
        sbUpdate();
        didCenter = true;
      }catch(_){ /* noop */ }
    }

    // Initial and responsive updates
    requestAnimationFrame(function(){
      updateEdgePadding();
      // Try centering shortly after first layout
      setTimeout(centerOnce, CONFIG.EDGE_SETTLE_DELAY);
    });
    // Re-run shortly after load to settle after fonts/images
    var loadHandler = function(){ setTimeout(function(){ updateEdgePadding(); centerOnce(); }, CONFIG.EDGE_SETTLE_DELAY); };
    var resizeEdgeHandler = debounce(updateEdgePadding, CONFIG.RESIZE_DEBOUNCE);
    window.addEventListener('load', loadHandler, { once:true, passive:true });
    window.addEventListener('resize', resizeEdgeHandler, { passive:true });
    cleanupFns.push(function() {
      window.removeEventListener('load', loadHandler);
      window.removeEventListener('resize', resizeEdgeHandler);
    });
    
    // Store cleanup function on track for potential future cleanup
    track._vtlCleanup = function() {
      cleanupFns.forEach(function(fn) { try { fn(); } catch(e) {} });
      cleanupFns = [];
    };
  }

  function init(){
    document.querySelectorAll('.vecco-timeline .vecco-tl-track').forEach(initTrack);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
