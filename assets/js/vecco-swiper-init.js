/**
 * Vecco Timeline Swiper Integration
 * Initializes Swiper for .vecco-timeline on mobile, keeps native drag on desktop
 */
(function(){
  'use strict';

  function initVeccoSwiper() {
    if (typeof window.Swiper !== 'function') {
      console.warn('Vecco Timeline: Swiper not loaded, falling back to native scroll');
      return;
    }

    var containers = document.querySelectorAll('.vecco-timeline');
    containers.forEach(function(wrapper){
      if (wrapper.dataset.veccoSwiper === '1') return; // already initialized
      
      var track = wrapper.querySelector('.vecco-tl-track');
      if (!track) return;

      var items = track.querySelectorAll('.vecco-tl-item');
      var seps = track.querySelectorAll('.vecco-tl-sep');
      if (!items.length) return;

      // Mark elements with Swiper classes
      wrapper.classList.add('swiper');
      track.classList.add('swiper-wrapper');
      items.forEach(function(item){ item.classList.add('swiper-slide'); });
      seps.forEach(function(sep){ sep.classList.add('swiper-slide'); });

      wrapper.dataset.veccoSwiper = '1';

      // Disable native drag/momentum from timeline.js
      track.dataset.vtlInit = '1'; // prevents timeline.js from initializing
      track.dataset.disableWheel = '1';

      // Initialize Swiper with mobile-first responsive config
      var swiper = new Swiper(wrapper, {
        wrapperClass: 'swiper-wrapper',
        slideClass: 'swiper-slide',
        
        // Desktop: auto width (6 items visible per CSS)
        // Mobile: force exactly 2 items visible
        slidesPerView: 'auto',
        spaceBetween: 0,
        
        // Responsive breakpoints - mobile shows 2, desktop shows auto
        breakpoints: {
          320: {
            slidesPerView: 2,
            spaceBetween: 0,
            slidesPerGroup: 1
          },
          480: {
            slidesPerView: 2,
            spaceBetween: 0,
            slidesPerGroup: 1
          },
          768: {
            slidesPerView: 'auto',
            spaceBetween: 0
          },
          1024: {
            slidesPerView: 'auto',
            spaceBetween: 0
          }
        },

        // Enable smooth dragging
        freeMode: {
          enabled: true,
          momentum: true,
          momentumRatio: 0.5,
          momentumVelocityRatio: 0.5
        },

        // Touch/mouse settings
        grabCursor: true,
        simulateTouch: true,
        touchStartPreventDefault: false,
        resistanceRatio: 0.85,

        // Keyboard navigation
        keyboard: {
          enabled: true
        },

        // Watch for DOM/size changes
        observer: true,
        observeParents: true,
        watchOverflow: true,

        // Prevent click propagation during swipe
        preventClicks: true,
        preventClicksPropagation: true,

        // Enable CSS mode for better performance on some devices
        cssMode: false
      });

      // Sync custom scrollbar if it exists
      var scrollbar = wrapper.querySelector('.vecco-scrollbar');
      var scrollbarDrag = scrollbar ? scrollbar.querySelector('.vecco-scrollbar-drag') : null;
      
      if (scrollbar && scrollbarDrag) {
        swiper.on('progress', function(s, progress){
          var barWidth = scrollbar.clientWidth || 1;
          var thumbWidth = scrollbarDrag.offsetWidth || 40;
          var maxLeft = Math.max(0, barWidth - thumbWidth);
          var left = Math.round(maxLeft * progress);
          scrollbarDrag.style.transform = 'translate3d(' + left + 'px, 0, 0)';
        });
      }

      // Disable text selection while dragging
      wrapper.addEventListener('touchstart', function(){
        document.body.style.userSelect = 'none';
        document.body.style.webkitUserSelect = 'none';
      }, { passive: true });

      wrapper.addEventListener('touchend', function(){
        document.body.style.userSelect = '';
        document.body.style.webkitUserSelect = '';
      }, { passive: true });
    });
  }

  // Initialize after DOM ready and again after full page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVeccoSwiper);
  } else {
    initVeccoSwiper();
  }
  
  window.addEventListener('load', function(){
    setTimeout(initVeccoSwiper, 100);
  });

})();
