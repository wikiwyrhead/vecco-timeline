<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Vecco_Timeline {

    const META_ITEMS = '_vecco_timeline_items';
    const META_STYLE = '_vecco_timeline_style';

    public static function register_cpt() {
        $labels = [
            'name' => __( 'Timelines', 'vecco-timeline' ),
            'singular_name' => __( 'Timeline', 'vecco-timeline' ),
            'add_new' => __( 'Add New', 'vecco-timeline' ),
            'add_new_item' => __( 'Add New Timeline', 'vecco-timeline' ),
            'edit_item' => __( 'Edit Timeline', 'vecco-timeline' ),
            'new_item' => __( 'New Timeline', 'vecco-timeline' ),
            'view_item' => __( 'View Timeline', 'vecco-timeline' ),
            'search_items' => __( 'Search Timelines', 'vecco-timeline' ),
            'not_found' => __( 'No timelines found', 'vecco-timeline' ),
            'menu_name' => __( 'Timelines', 'vecco-timeline' ),
        ];
        register_post_type( 'vecco_timeline', [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-clock',
            'supports' => [ 'title' ],
        ]);
    }

    public static function register_meta() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_metabox' ] );
        add_action( 'save_post_vecco_timeline', [ __CLASS__, 'save_metabox' ] );
    }

    public static function add_metabox() {
        add_meta_box(
            'vecco_timeline_items',
            __( 'Timeline Items', 'vecco-timeline' ),
            [ __CLASS__, 'render_metabox' ],
            'vecco_timeline',
            'normal',
            'high'
        );
    }

    public static function render_metabox( $post ) {
        wp_nonce_field( 'vecco_timeline_save', 'vecco_timeline_nonce' );
        // Ensure WP media is available for image selection
        if ( function_exists( 'wp_enqueue_media' ) ) { wp_enqueue_media(); }
        wp_enqueue_script( 'jquery-ui-sortable' );
        // Show shortcode helper
        $sc = '[vecco_timeline id="' . intval( $post->ID ) . '"]';
        echo '<div class="notice-inline" style="margin:0 0 12px;padding:10px;border:1px solid #e5e5e5;background:#fff;border-radius:4px">'
           . '<strong>' . esc_html__( 'Shortcode', 'vecco-timeline' ) . ':</strong> '
           . '<input type="text" readonly value="' . esc_attr( $sc ) . '" style="width:260px;margin:0 6px;" />'
           . '<button type="button" class="button vecco-tl-copy" data-copy="' . esc_attr( $sc ) . '">' . esc_html__( 'Copy', 'vecco-timeline' ) . '</button>'
           . '</div>';
        $items = get_post_meta( $post->ID, self::META_ITEMS, true );
        if ( ! is_array( $items ) ) { $items = []; }
        $style = get_post_meta( $post->ID, self::META_STYLE, true );
        if ( ! is_array( $style ) ) { $style = [ 'font' => '', 'sep_color' => '' ]; }
        $font = isset( $style['font'] ) ? (int) $style['font'] : '';
        $sep_color = isset( $style['sep_color'] ) ? $style['sep_color'] : '';
        $year_color  = isset($style['year_color']) ? $style['year_color'] : '';
        $year_size   = isset($style['year_size']) ? (int)$style['year_size'] : '';
        $title_color = isset($style['title_color']) ? $style['title_color'] : '';
        $title_size  = isset($style['title_size']) ? (int)$style['title_size'] : '';
        $desc_color  = isset($style['desc_color']) ? $style['desc_color'] : '';
        $desc_size   = isset($style['desc_size']) ? (int)$style['desc_size'] : '';
        $font_year_family  = isset($style['font_year_family'])  ? $style['font_year_family']  : '';
        $font_title_family = isset($style['font_title_family']) ? $style['font_title_family'] : '';
        $font_desc_family  = isset($style['font_desc_family'])  ? $style['font_desc_family']  : '';
        $webfont_url_pt    = isset($style['webfont_url'])       ? $style['webfont_url']       : '';
        echo '<div class="vecco-tl-style" style="margin-bottom:16px;padding:0;border:1px solid #dfe3e8;background:#fff;border-radius:8px;overflow:hidden">';
        
        // Header
        echo '<div style="padding:12px 16px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">';
        echo '<strong style="display:block;font-size:13px;font-weight:600;color:#fff;letter-spacing:0.3px">' . esc_html__( 'Per-timeline Overrides', 'vecco-timeline' ) . '</strong>';
        echo '<p style="margin:3px 0 0;font-size:10px;color:rgba(255,255,255,0.85);line-height:1.5">These settings apply globally to all items in this timeline. Use them to set consistent colors, fonts, and sizes across your entire timeline. For individual customization, use the per-item settings below (icon colors, sizes, and custom icons).</p>';
        echo '</div>';
        
        echo '<div style="padding:14px 16px">';
        
        // General Settings Group
        echo '<div style="margin-bottom:14px">';
        echo '<div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">General Settings</div>';
        echo '<p style="margin:0 0 8px;font-size:10px;color:#64748b;line-height:1.4">Control base font size, external font URLs, and timeline separator color.</p>';
        echo '<div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">';
        printf('<label style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#475569">%s <input type="number" min="10" max="32" name="vecco_tl_style[font]" value="%s" placeholder="16" style="width:65px;padding:5px 7px;border:1px solid #cbd5e1;border-radius:4px;font-size:12px" /></label>', esc_html__( 'Base Font Size (px):', 'vecco-timeline' ), esc_attr( $font ) );
        echo '<label style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#475569;flex:1;min-width:300px">' . esc_html__( 'Webfont URL(s):', 'vecco-timeline' )
           . ' <input type="text" style="flex:1;padding:5px 7px;border:1px solid #cbd5e1;border-radius:4px;font-size:11px;font-family:monospace" name="vecco_tl_style[webfont_url]" value="' . esc_attr( $webfont_url_pt ) . '" placeholder="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" />'
           . '</label>';
        $sep_val = $sep_color ? $sep_color : '#d9d9d9';
        printf('<label style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#475569">%s <input type="color" name="vecco_tl_style[sep_color]" value="%s" style="width:70px;height:32px;border:1px solid #cbd5e1;border-radius:4px;cursor:pointer" /></label>', esc_html__( 'Separator Color:', 'vecco-timeline' ), esc_attr( $sep_val ) );
        echo '</div>';
        echo '</div>';
        
        // Typography Settings - Single compact grid
        echo '<div style="margin-bottom:14px">';
        echo '<div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Typography</div>';
        echo '<p style="margin:0 0 8px;font-size:10px;color:#64748b;line-height:1.4">Customize color, size, and font family for each timeline element. Leave empty to use defaults.</p>';
        echo '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;padding:10px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">';
        
        // Year Column
        echo '<div style="display:flex;flex-direction:column;gap:6px">';
        echo '<div style="font-size:9px;font-weight:700;color:#64748b;margin-bottom:2px">YEAR</div>';
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Color<br><input type="color" name="vecco_tl_style[year_color]" value="%s" style="width:100%%;height:32px;border:1px solid #cbd5e1;border-radius:3px;cursor:pointer" /></label>', esc_attr($year_color));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Size<br><input type="number" min="10" max="48" name="vecco_tl_style[year_size]" value="%s" placeholder="24" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($year_size));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Font<br><input list="vecco-tl-font-presets" type="text" name="vecco_tl_style[font_year_family]" value="%s" placeholder="Inter" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($font_year_family));
        echo '</div>';
        
        // Title Column
        echo '<div style="display:flex;flex-direction:column;gap:6px">';
        echo '<div style="font-size:9px;font-weight:700;color:#64748b;margin-bottom:2px">TITLE</div>';
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Color<br><input type="color" name="vecco_tl_style[title_color]" value="%s" style="width:100%%;height:32px;border:1px solid #cbd5e1;border-radius:3px;cursor:pointer" /></label>', esc_attr($title_color));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Size<br><input type="number" min="10" max="48" name="vecco_tl_style[title_size]" value="%s" placeholder="18" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($title_size));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Font<br><input list="vecco-tl-font-presets" type="text" name="vecco_tl_style[font_title_family]" value="%s" placeholder="Georgia" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($font_title_family));
        echo '</div>';
        
        // Description Column
        echo '<div style="display:flex;flex-direction:column;gap:6px">';
        echo '<div style="font-size:9px;font-weight:700;color:#64748b;margin-bottom:2px">DESCRIPTION</div>';
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Color<br><input type="color" name="vecco_tl_style[desc_color]" value="%s" style="width:100%%;height:32px;border:1px solid #cbd5e1;border-radius:3px;cursor:pointer" /></label>', esc_attr($desc_color));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Size<br><input type="number" min="10" max="48" name="vecco_tl_style[desc_size]" value="%s" placeholder="14" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($desc_size));
        printf('<label style="font-size:10px;font-weight:600;color:#475569">Font<br><input list="vecco-tl-font-presets" type="text" name="vecco_tl_style[font_desc_family]" value="%s" placeholder="SFMono" style="width:100%%;padding:5px 7px;border:1px solid #cbd5e1;border-radius:3px;font-size:11px" /></label>', esc_attr($font_desc_family));
        echo '</div>';
        
        echo '</div>'; // Close typography grid
        echo '</div>';
        
        // Datalist for font presets (used in typography section)
        echo '<datalist id="vecco-tl-font-presets">'
           . '<option value="Inter, system-ui, -apple-system, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\""></option>'
           . '<option value="Roboto, system-ui, -apple-system, \"Segoe UI\", Helvetica, Arial, sans-serif"></option>'
           . '<option value="Poppins, Arial, sans-serif"></option>'
           . '<option value="Montserrat, Arial, sans-serif"></option>'
           . '<option value="Open Sans, Arial, sans-serif"></option>'
           . '<option value="Lato, Arial, sans-serif"></option>'
           . '<option value="Source Sans 3, system-ui, Arial, sans-serif"></option>'
           . '<option value="Noto Sans, Arial, sans-serif"></option>'
           . '<option value="Nunito, Arial, sans-serif"></option>'
           . '<option value="Merriweather, Georgia, serif"></option>'
           . '<option value="Georgia, \"Times New Roman\", Times, serif"></option>'
           . '<option value="\"Times New Roman\", Times, serif"></option>'
           . '<option value="Arial, Helvetica, sans-serif"></option>'
           . '<option value="Helvetica, Arial, sans-serif"></option>'
           . '<option value="\"Trebuchet MS\", Helvetica, sans-serif"></option>'
           . '<option value="Tahoma, Verdana, Segoe, sans-serif"></option>'
           . '<option value="Verdana, Geneva, Tahoma, sans-serif"></option>'
           . '<option value="SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace"></option>'
           . '<option value="\"Courier New\", Courier, monospace"></option>'
           . '</datalist>';
        
        echo '</div>'; // Close padding wrapper
        // Custom scrollbar under the track
        echo '<div class="vecco-scrollbar vecco-scrollbar-horizontal"><div class="vecco-scrollbar-drag"></div></div>';
        echo '</div>';
        // Default empty row
        echo '<div id="vecco-tl-items" class="vecco-tl-items">';
        foreach ( $items as $idx => $it ) {
            self::render_item_row( $idx, $it );
        }
        echo '</div>';
        echo '<p><button type="button" class="button" id="vecco-tl-add">' . esc_html__( 'Add Item', 'vecco-timeline' ) . '</button></p>';
        // Template
        echo '<script type="text/html" id="tmpl-vecco-tl-row">';
        self::render_item_row( '{{INDEX}}', [ 'year' => '', 'title' => '', 'desc' => '', 'icon' => '', 'icon_size' => '', 'svg' => '', 'preset' => '', 'color' => '#00BCD4', 'year_color' => '' ] );
        echo '</script>';
        // Inline styles
        ?>
        <style>
            /* Container wrapper to scope grid layout */
            #vecco-tl-items{display:block;width:100%;clear:both}
            .vecco-tl-style{border:1px solid #e6e7eb;background:#fff;border-radius:10px;padding:18px 20px}
            #vecco-tl-items .vecco-tl-row{
                display:grid;
                grid-template-columns:20px minmax(400px,1.5fr) 240px 240px;
                gap:14px;
                align-items:start;
                margin-bottom:18px;
                background:#fff;
                border:2px solid #cbd5e1;
                padding:14px;
                border-radius:8px;
                box-shadow:0 2px 8px rgba(0,0,0,.08), 0 1px 3px rgba(0,0,0,.06);
                position:relative;
                transition:all .2s ease;
            }
            #vecco-tl-items .vecco-tl-row:hover{
                box-shadow:0 4px 12px rgba(0,0,0,.12), 0 2px 6px rgba(0,0,0,.08);
                border-color:#94a3b8;
            }
            #vecco-tl-items .vecco-tl-row label{font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;display:block;line-height:1.3}
            #vecco-tl-items .vecco-tl-row label small{font-weight:400;color:#9ca3af;font-size:10px;margin-left:3px}

            /* Inputs - elegant sizing */
            #vecco-tl-items .vecco-tl-row input[type=text],
            #vecco-tl-items .vecco-tl-row input[type=url],
            #vecco-tl-items .vecco-tl-row input[type=number],
            #vecco-tl-items .vecco-tl-row textarea,
            #vecco-tl-items .vecco-tl-row select{width:100%;font-size:13px;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;background:#fff;transition:all .2s ease;line-height:1.4;box-sizing:border-box}
            #vecco-tl-items .vecco-tl-row input[type=color],
            .vecco-tl-style input[type=color]{width:100%;height:38px;border:1px solid #d1d5db;border-radius:6px;padding:4px;cursor:pointer;background:#fff;box-sizing:border-box}
            #vecco-tl-items .vecco-tl-row input[type=color]::-webkit-color-swatch-wrapper,
            .vecco-tl-style input[type=color]::-webkit-color-swatch-wrapper{padding:3px}
            #vecco-tl-items .vecco-tl-row input[type=color]::-webkit-color-swatch,
            .vecco-tl-style input[type=color]::-webkit-color-swatch{border:none;border-radius:3px}
            #vecco-tl-items .vecco-tl-row input[type=color]::-moz-color-swatch,
            .vecco-tl-style input[type=color]::-moz-color-swatch{border:none;border-radius:3px}
            #vecco-tl-items .vecco-tl-row textarea{min-height:56px;resize:vertical;font-size:13px}
            #vecco-tl-items .vecco-tl-row select{appearance:auto;background:#fff;padding-right:28px}
            #vecco-tl-items .vecco-tl-row input:focus,
            #vecco-tl-items .vecco-tl-row textarea:focus,
            #vecco-tl-items .vecco-tl-row select:focus{outline:0;border-color:#2271b1;box-shadow:0 0 0 1px rgba(34,113,177,.25)}

            /* Icon section - right column aligned with actions */
            .vecco-icon-section{display:flex;flex-direction:column;gap:8px;padding:0}
            .vecco-icon-section label{font-size:10px;color:#374151;font-weight:600;margin-bottom:3px}
            .vecco-icon-section input[type=url],
            .vecco-icon-section textarea,
            .vecco-icon-section select{font-size:12px;padding:5px 8px}
            .vecco-icon-section textarea{min-height:48px;font-size:11px}
            .vecco-icon-section .vecco-tl-icon-wrap{gap:4px}
            .vecco-icon-section .vecco-tl-icon-preview{width:32px;height:32px;padding:3px}
            .vecco-icon-section .vecco-tl-media{padding:4px 8px!important;font-size:11px!important}

            /* Actions - in right column above icon section */
            .vecco-tl-actions{display:flex;gap:6px;margin-bottom:10px}
            .vecco-tl-actions .button{border-radius:5px;padding:5px 10px;font-size:11px;white-space:nowrap;flex:1;text-align:center;font-weight:500;transition:all .2s ease}
            .vecco-tl-actions .vecco-tl-dup{background:#f3f4f6;border-color:#d1d5db;color:#374151}
            .vecco-tl-actions .vecco-tl-dup:hover{background:#e5e7eb;border-color:#9ca3af}
            .vecco-tl-actions .vecco-tl-remove{color:#dc2626;border-color:#fca5a5;background:#fff}
            .vecco-tl-actions .vecco-tl-remove:hover{background:#fef2f2;border-color:#dc2626}

            /* Icon picker - better proportions */
            .vecco-tl-icon-wrap{display:flex;gap:8px;align-items:center}
            .vecco-tl-icon-preview{width:38px;height:38px;object-fit:contain;border:1px solid #d1d5db;background:#fafafa;border-radius:5px;padding:4px;flex-shrink:0}
            .vecco-tl-icon-input{flex:1;min-width:0}
            .vecco-tl-media{padding:6px 12px!important;font-size:12px!important;border-radius:5px!important;white-space:nowrap!important}

            /* Drag handle - refined */
            .vecco-tl-handle{cursor:move;opacity:.35;user-select:none;font-size:16px;line-height:1;transition:all .2s;color:#6b7280;align-self:center;padding:4px 0}
            .vecco-tl-handle:hover{opacity:.8;color:#374151}
            
            /* Sortable helper and placeholder */
            .ui-sortable-helper{opacity:0.8;cursor:move!important;box-shadow:0 8px 24px rgba(0,0,0,.15)!important}
            .ui-sortable-placeholder{visibility:visible!important;background:#f1f5f9!important;border:2px dashed #94a3b8!important;box-shadow:none!important}
            
            .vecco-tl-svgtxt{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-size:12px;line-height:1.5}

            /* Shortcode helper */
            .notice-inline{border-radius:10px;border:1px solid #e6e7eb;background:#fff}
            .notice-inline input[type=text]{border:1px solid #ccd0d4;border-radius:8px;padding:6px 10px;font-size:13px}
            .vecco-tl-copy{border-radius:8px !important}

            /* Responsive tightening */
            @media (max-width:1200px){ .vecco-tl-row{grid-template-columns:22px 100px 1fr 1fr 1fr 200px 100px 160px 100px 90px} }
            @media (max-width:960px){ .vecco-tl-row{grid-template-columns:22px 90px 1fr 1fr 1fr 180px 90px 150px 90px 80px} }
        </style>
        <script>
        jQuery(document).ready(function($){
          const wrap = document.getElementById('vecco-tl-items');
          const addBtn = document.getElementById('vecco-tl-add');
          if(!wrap||!addBtn) return;
          function nextIndex(){ return wrap.querySelectorAll('.vecco-tl-row').length; }
          function renumber(){
            const rows = wrap.querySelectorAll('.vecco-tl-row');
            rows.forEach(function(row, i){
              row.querySelectorAll('input, textarea, select').forEach(function(el){
                el.name = el.name.replace(/vecco_tl_items\[[0-9]+\]/, 'vecco_tl_items['+i+']');
              });
            });
          }
          function scrollIntoView(el){ try{ el.scrollIntoView({behavior:'smooth',block:'nearest'}); }catch(_){} }
          function duplicateRow(row){
            const clone = row.cloneNode(true);
            row.parentNode.insertBefore(clone, row.nextSibling);
            renumber();
            scrollIntoView(clone);
          }
          addBtn.addEventListener('click', function(){
            const html = document.getElementById('tmpl-vecco-tl-row').innerHTML.replace(/\{\{INDEX\}\}/g, String(nextIndex()));
            wrap.insertAdjacentHTML('beforeend', html);
            const last = wrap.querySelector('.vecco-tl-row:last-child');
            if(last) scrollIntoView(last);
          });
          if (jQuery && jQuery.fn.sortable) {
            jQuery(wrap).sortable({
              handle: '.vecco-tl-handle',
              items: '.vecco-tl-row',
              placeholder: 'ui-sortable-placeholder',
              helper: 'clone',
              opacity: 0.8,
              cursor: 'move',
              stop: function(){ renumber(); }
            });
          }
          wrap.addEventListener('click', function(e){
            if(e.target.classList.contains('vecco-tl-remove')){
              e.preventDefault();
              const row = e.target.closest('.vecco-tl-row');
              if(row) { row.remove(); renumber(); }
            }
            if(e.target.classList.contains('vecco-tl-dup')){
              e.preventDefault();
              const row = e.target.closest('.vecco-tl-row');
              if(row) duplicateRow(row);
            }
            if(e.target.classList.contains('vecco-tl-media')){
              e.preventDefault();
              const row = e.target.closest('.vecco-tl-row');
              if(!row) return;
              const input = row.querySelector('.vecco-tl-icon-input');
              const prev = row.querySelector('.vecco-tl-icon-preview');
              const frame = wp.media({ title: 'Select Icon', button: { text: 'Use this icon' }, multiple: false });
              frame.on('select', function(){
                const att = frame.state().get('selection').first().toJSON();
                if(input){ input.value = att.url; input.dispatchEvent(new Event('change')); }
                if(prev){ prev.src = att.url; prev.style.display = 'block'; }
              });
              frame.open();
            }
          });
          // Live update preview when URL is typed
          wrap.addEventListener('input', function(e){
            if(e.target.classList.contains('vecco-tl-icon-input')){
              const row = e.target.closest('.vecco-tl-row');
              const prev = row && row.querySelector('.vecco-tl-icon-preview');
              if(prev){ prev.src = e.target.value || ''; prev.style.display = e.target.value ? 'block' : 'none'; }
            }
          });
          
          // Shortcode copy button (outside the items wrapper)
          document.addEventListener('click', function(e){
            if(e.target.classList.contains('vecco-tl-copy')){
              e.preventDefault();
              const t = e.target.getAttribute('data-copy') || '';
              if(!t) return;
              if(navigator.clipboard && navigator.clipboard.writeText){
                navigator.clipboard.writeText(t).then(function(){
                  const old = e.target.textContent;
                  e.target.textContent = '<?= esc_js( __( 'Copied!', 'vecco-timeline' ) ); ?>';
                  setTimeout(function(){ e.target.textContent = old; }, 1200);
                }).catch(function(){
                  fallbackCopy(t, e.target);
                });
              } else {
                fallbackCopy(t, e.target);
              }
            }
          });
          
          function fallbackCopy(text, btn){
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try{ 
              document.execCommand('copy');
              const old = btn.textContent;
              btn.textContent = '<?= esc_js( __( 'Copied!', 'vecco-timeline' ) ); ?>';
              setTimeout(function(){ btn.textContent = old; }, 1200);
            }catch(_){}
            document.body.removeChild(ta);
          }
        });
        </script>
        <?php
    }

    private static function render_item_row( $index, $it ) {
        $year = isset($it['year']) ? $it['year'] : '';
        $title = isset($it['title']) ? $it['title'] : '';
        $desc = isset($it['desc']) ? $it['desc'] : '';
        $icon = isset($it['icon']) ? $it['icon'] : '';
        $icon_size = isset($it['icon_size']) ? (int) $it['icon_size'] : '';
        $svg = isset($it['svg']) ? $it['svg'] : '';
        $preset = isset($it['preset']) ? $it['preset'] : '';
        $color = isset($it['color']) ? $it['color'] : '#00BCD4';
        $year_color_item = isset($it['year_color']) ? $it['year_color'] : '';
        
        echo '<div class="vecco-tl-row">';
        
        // Column 1: Drag handle
        echo '<div class="vecco-tl-handle" title="Drag to reorder" style="align-self:stretch;display:flex;align-items:center">≡</div>';
        
        // Column 2: Event Info (Year, Title, Description)
        echo '<div style="display:flex;flex-direction:column;gap:8px">';
        echo '<div style="display:grid;grid-template-columns:80px 120px 1fr;gap:8px">';
        printf('<div><label>Year<br><input type="text" name="vecco_tl_items[%1$s][year]" value="%2$s" placeholder="2024" /></label></div>', esc_attr($index), esc_attr($year));
        printf('<div><label>Year Color<br><input type="color" name="vecco_tl_items[%1$s][year_color]" value="%2$s" /></label></div>', esc_attr($index), esc_attr($year_color_item));
        printf('<div><label>Title<br><input type="text" name="vecco_tl_items[%1$s][title]" value="%2$s" placeholder="Event title" /></label></div>', esc_attr($index), esc_attr($title));
        echo '</div>';
        printf('<div><label>Description<br><textarea rows="3" name="vecco_tl_items[%1$s][desc]" placeholder="Event description">%2$s</textarea></label></div>', esc_attr($index), esc_textarea($desc));
        
        // Action buttons below description
        echo '<div style="display:flex;gap:8px;padding-top:8px;border-top:1px solid #e2e8f0;margin-top:8px">';
        echo '<button type="button" class="button vecco-tl-dup" style="padding:6px 16px;font-size:12px">Duplicate</button>';
        echo '<button type="button" class="button vecco-tl-remove" style="padding:6px 16px;font-size:12px;color:#dc2626;border-color:#fca5a5">Delete</button>';
        echo '</div>';
        
        echo '</div>';
        
        // Column 3: Visual Settings (Icon Size, Accent)
        echo '<div style="display:flex;flex-direction:column;gap:8px;padding:10px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">';
        echo '<div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px">Visual Settings</div>';
        
        // Icon Preset first
        echo '<div><label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px">Icon Preset</label>';
        echo '<select name="vecco_tl_items['.esc_attr($index).'][preset]" style="width:100%;font-size:12px;padding:6px 8px">';
        $presets = [
            '' => '— None —',
            'shovel' => 'Shovel',
            'plug' => 'Plug',
            'beaker' => 'Beaker',
            'factory' => 'Factory',
            'bolt' => 'Bolt',
            'award' => 'Award',
            'truck' => 'Truck',
            'leaf' => 'Leaf',
            'globe' => 'Globe',
            'briefcase' => 'Briefcase',
            'chart' => 'Chart',
            'users' => 'Users',
            'wrench' => 'Wrench',
        ];
        foreach ( $presets as $key => $lab ) {
            printf('<option value="%s" %s>%s</option>', esc_attr($key), selected($preset, $key, false), esc_html($lab));
        }
        echo '</select></div>';
        
        printf('<div><label>Icon Size <small>px</small><br><input type="number" min="16" max="256" name="vecco_tl_items[%1$s][icon_size]" value="%2$s" placeholder="72" /></label></div>', esc_attr($index), esc_attr($icon_size));
        printf('<div><label>Accent Color<br><input type="color" name="vecco_tl_items[%1$s][color]" value="%2$s" /></label></div>', esc_attr($index), esc_attr($color));
        echo '</div>';
        
        // Column 4: Icon Settings
        echo '<div style="display:flex;flex-direction:column;gap:8px;padding:10px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">';
        echo '<div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px">Icon Settings</div>';
        
        // Icon URL with preview
        echo '<div style="margin-bottom:6px"><label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px">Icon URL</label>';
        echo '<div style="display:flex;gap:4px;align-items:center;margin-bottom:4px">';
        printf('<img class="vecco-tl-icon-preview" src="%s" style="%s;width:32px;height:32px;padding:2px" />', esc_url($icon), $icon ? '' : 'display:none;');
        printf('<input type="url" name="vecco_tl_items[%1$s][icon]" value="%2$s" placeholder="https://..." style="flex:1;font-size:12px;padding:5px 8px" />', esc_attr($index), esc_attr($icon));
        echo '</div>';
        echo '<button type="button" class="button vecco-tl-media" style="width:100%;font-size:11px;padding:5px 8px">Select Image</button>';
        echo '</div>';
        
        // SVG Code
        echo '<div><label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px">SVG Code</label>';
        echo '<textarea class="vecco-tl-svgtxt" rows="3" name="vecco_tl_items['.esc_attr($index).'][svg]" placeholder="<svg>...</svg>" style="width:100%;font-size:11px;padding:5px 8px">' . esc_textarea($svg) . '</textarea></div>';
        
        echo '</div>'; // Close icon section column
        
        echo '</div>'; // Close row
    }

    public static function save_metabox( $post_id ) {
        // Capability check first (before nonce)
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        if ( ! isset( $_POST['vecco_timeline_nonce'] ) || ! wp_verify_nonce( $_POST['vecco_timeline_nonce'], 'vecco_timeline_save' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        $items = isset( $_POST['vecco_tl_items'] ) && is_array( $_POST['vecco_tl_items'] ) ? $_POST['vecco_tl_items'] : [];
        $clean = [];
        foreach ( $items as $it ) {
            if ( empty( $it['title'] ) && empty( $it['year'] ) ) { continue; }
            $sz = isset($it['icon_size']) ? absint( $it['icon_size'] ) : 0;
            if ( $sz && $sz < 16 ) { $sz = 16; }
            if ( $sz && $sz > 256 ) { $sz = 256; }
            $clean[] = [
                'year' => sanitize_text_field( $it['year'] ?? '' ),
                'title' => sanitize_text_field( $it['title'] ?? '' ),
                'desc' => sanitize_textarea_field( $it['desc'] ?? '' ),
                'icon' => esc_url_raw( $it['icon'] ?? '' ),
                'icon_size' => $sz,
                // Sanitize SVG with allowed tags/attributes only (Security P1)
                'svg' => isset($it['svg']) ? wp_kses($it['svg'], [
                    'svg'    => ['viewBox' => true, 'xmlns' => true, 'fill' => true, 'stroke' => true, 'width' => true, 'height' => true],
                    'path'   => ['d' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true],
                    'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true],
                    'rect'   => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true, 'stroke' => true],
                    'line'   => ['x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true],
                    'polyline' => ['points' => true, 'fill' => true, 'stroke' => true],
                    'polygon'  => ['points' => true, 'fill' => true, 'stroke' => true],
                ]) : '',
                'preset' => sanitize_key( $it['preset'] ?? '' ),
                'color' => sanitize_hex_color( $it['color'] ?? '#00BCD4' ),
                'year_color' => isset($it['year_color']) ? sanitize_hex_color( $it['year_color'] ) : '',
            ];
        }
        update_post_meta( $post_id, self::META_ITEMS, $clean );
        // Save per-timeline overrides
        $style = isset($_POST['vecco_tl_style']) && is_array($_POST['vecco_tl_style']) ? $_POST['vecco_tl_style'] : [];
        $font = isset($style[ 'font' ]) ? max(10, min(32, (int)$style['font'])) : '';
        $sep_color   = isset($style['sep_color'])   ? sanitize_hex_color($style['sep_color']) : '';
        $year_color  = isset($style['year_color'])  ? sanitize_hex_color($style['year_color']) : '';
        $year_size   = isset($style['year_size'])   ? max(10, min(48, (int)$style['year_size'])) : '';
        $title_color = isset($style['title_color']) ? sanitize_hex_color($style['title_color']) : '';
        $title_size  = isset($style['title_size'])  ? max(10, min(48, (int)$style['title_size'])) : '';
        $desc_color  = isset($style['desc_color'])  ? sanitize_hex_color($style['desc_color']) : '';
        $desc_size   = isset($style['desc_size'])   ? max(10, min(48, (int)$style['desc_size'])) : '';
        update_post_meta( $post_id, self::META_STYLE, [
            'font'        => $font,
            'sep_color'   => $sep_color,
            'year_color'  => $year_color,
            'year_size'   => $year_size,
            'title_color' => $title_color,
            'title_size'  => $title_size,
            'desc_color'  => $desc_color,
            'desc_size'   => $desc_size,
            'font_year_family'  => isset($style['font_year_family'])  ? wp_strip_all_tags($style['font_year_family'])  : '',
            'font_title_family' => isset($style['font_title_family']) ? wp_strip_all_tags($style['font_title_family']) : '',
            'font_desc_family'  => isset($style['font_desc_family'])  ? wp_strip_all_tags($style['font_desc_family'])  : '',
            'webfont_url'       => isset($style['webfont_url']) ? implode("\n", array_filter(array_map('esc_url_raw', preg_split('/\r?\n+/', (string)$style['webfont_url'])))) : '',
        ] );
    }

    public static function render_shortcode( $post_id ) {
        $items = get_post_meta( $post_id, self::META_ITEMS, true );
        if ( ! is_array( $items ) || ! count( $items ) ) return '';

        // Enqueue front assets only when rendering
        wp_enqueue_style( 'vecco-timeline', VECCO_TL_URL . 'assets/css/timeline.css', [], VECCO_TL_VERSION );
        wp_enqueue_script( 'vecco-timeline', VECCO_TL_URL . 'assets/js/timeline.js', [], VECCO_TL_VERSION, true );

        // Defaults from settings
        if ( class_exists('Vecco_Timeline_Admin') ) {
            $defaults = Vecco_Timeline_Admin::get();
        } else { $defaults = [ 'icon' => 72, 'font' => 16, 'color' => '#00BCD4' ]; }

        // Per-timeline overrides
        $style = get_post_meta( $post_id, self::META_STYLE, true );
        $font_px = isset($style['font']) && $style['font'] ? (int)$style['font'] : 0;
        $sep_color   = isset($style['sep_color'])   && $style['sep_color']   ? $style['sep_color']   : '#d9d9d9';
        $year_color  = isset($style['year_color'])  ? $style['year_color']  : '';
        $year_size   = isset($style['year_size'])   ? (int)$style['year_size']   : 0;
        $title_color = isset($style['title_color']) ? $style['title_color'] : '';
        $title_size  = isset($style['title_size'])  ? (int)$style['title_size']  : 0;
        $desc_color  = isset($style['desc_color'])  ? $style['desc_color']  : '';
        $desc_size   = isset($style['desc_size'])   ? (int)$style['desc_size']   : 0;
        $pt_font_year  = isset($style['font_year_family'])  ? trim($style['font_year_family'])  : '';
        $pt_font_title = isset($style['font_title_family']) ? trim($style['font_title_family']) : '';
        $pt_font_desc  = isset($style['font_desc_family'])  ? trim($style['font_desc_family'])  : '';

        // Prepare scoped separator widths from settings
        $sep_w_desktop = isset($defaults['sep_w_desktop']) ? (int)$defaults['sep_w_desktop'] : 128;
        $sep_w_mobile  = isset($defaults['sep_w_mobile'])  ? (int)$defaults['sep_w_mobile']  : 16;
        // Global fonts from settings (optional)
        $gf_year  = isset($defaults['font_year'])  ? trim($defaults['font_year'])  : '';
        $gf_title = isset($defaults['font_title']) ? trim($defaults['font_title']) : '';
        $gf_desc  = isset($defaults['font_desc'])  ? trim($defaults['font_desc'])  : '';

        // Enqueue global and per‑timeline webfont URLs (limit to 5 for performance)
        $enqueued_webfonts = [];
        $webfont_limit = 5;
        if ( ! empty( $defaults['webfont_url'] ) ) {
            $lines = preg_split('/\r?\n+/', (string)$defaults['webfont_url']);
            foreach ( (array)$lines as $u ) {
                if ( count($enqueued_webfonts) >= $webfont_limit ) break;
                $u = trim($u);
                if ( $u && filter_var($u, FILTER_VALIDATE_URL) ) {
                    $key = md5($u);
                    if ( empty($enqueued_webfonts[$key]) ) {
                        wp_enqueue_style( 'vecco-tl-webfont-' . $key, $u, [], null );
                        $enqueued_webfonts[$key] = true;
                    }
                }
            }
        }
        $pt_webfonts = isset($style['webfont_url']) ? preg_split('/\r?\n+/', (string)$style['webfont_url']) : [];
        foreach ( (array)$pt_webfonts as $u ) {
            if ( count($enqueued_webfonts) >= $webfont_limit ) break;
            $u = trim($u);
            if ( $u && filter_var($u, FILTER_VALIDATE_URL) ) {
                $key = md5($u);
                if ( empty($enqueued_webfonts[$key]) ) {
                    wp_enqueue_style( 'vecco-tl-webfont-' . $key, $u, [], null );
                    $enqueued_webfonts[$key] = true;
                }
            }
        }

        // Unique wrapper id for scoped styles
        static $inst = 0; $inst++;
        $wrap_id = 'vecco-timeline-' . intval($post_id) . '-' . $inst;

        ob_start();
        // Wrapper classes and CSS vars for positioning style
        $pos_style = isset($defaults['position_style']) ? $defaults['position_style'] : 'original';
        $is_centered = ($pos_style === 'centered' || $pos_style === 'centered_no_fade');
        $is_fullwidth = ($pos_style === 'fullwidth');
        $wrap_style_vars = '';
        if ( $is_centered ) {
            $pad_d = isset($defaults['pad_desktop']) ? (int)$defaults['pad_desktop'] : 60;
            $pad_t = isset($defaults['pad_tablet'])  ? (int)$defaults['pad_tablet']  : 40;
            $pad_m = isset($defaults['pad_mobile'])  ? (int)$defaults['pad_mobile']  : 16;
            $fade_d = isset($defaults['fade_desktop']) ? (int)$defaults['fade_desktop'] : 22;
            $fade_t = isset($defaults['fade_tablet'])  ? (int)$defaults['fade_tablet']  : 18;
            $fade_m = isset($defaults['fade_mobile'])  ? (int)$defaults['fade_mobile']  : 14;
            $wrap_style_vars = '--vtl-pad-desktop:'.$pad_d.'px;--vtl-pad-tablet:'.$pad_t.'px;--vtl-pad-mobile:'.$pad_m.'px;--vtl-fade-desktop:'.$fade_d.'px;--vtl-fade-tablet:'.$fade_t.'px;--vtl-fade-mobile:'.$fade_m.'px;';
        } elseif ( $is_fullwidth ) {
            // Full width: optional safe-area gutters
            $fw_d = isset($defaults['fw_safe_desktop']) ? (int)$defaults['fw_safe_desktop'] : 0;
            $fw_t = isset($defaults['fw_safe_tablet'])  ? (int)$defaults['fw_safe_tablet']  : 0;
            $fw_m = isset($defaults['fw_safe_mobile'])  ? (int)$defaults['fw_safe_mobile']  : 0;
            $wrap_style_vars = '--vtl-fw-safe-desktop:'.$fw_d.'px;--vtl-fw-safe-tablet:'.$fw_t.'px;--vtl-fw-safe-mobile:'.$fw_m.'px;';
        } else {
            // Original style: provide margin variables
            $om_d = isset($defaults['orig_m_desktop']) ? (int)$defaults['orig_m_desktop'] : 30;
            $om_t = isset($defaults['orig_m_tablet'])  ? (int)$defaults['orig_m_tablet']  : 24;
            $om_m = isset($defaults['orig_m_mobile'])  ? (int)$defaults['orig_m_mobile']  : 16;
            $wrap_style_vars = '--vtl-orig-m-desktop:'.$om_d.'px;--vtl-orig-m-tablet:'.$om_t.'px;--vtl-orig-m-mobile:'.$om_m.'px;';
        }
        // Add separator width variables
        $wrap_style_vars .= '--vtl-sep-desktop:'.$sep_w_desktop.'px;--vtl-sep-mobile:'.$sep_w_mobile.'px;';
        // Derive extender overhang and item padding from spacing with safe minimums
        // For small gaps, extend the line more so it stays visually substantial
        $sep_ext_desktop = max(24, min(96, (int) round($sep_w_desktop * 2))); // 24..96px
        $sep_ext_mobile  = max(12, min(24, (int) round($sep_w_mobile * 2)));  // 12..24px
        $item_pad_desktop = max(2, min(12, (int) round($sep_w_desktop / 6))); // 2..12px
        $item_pad_mobile  = max(2, min(10, (int) round($sep_w_mobile  / 6))); // 2..10px
        $wrap_style_vars .= '--vtl-sep-ext-desktop:'.$sep_ext_desktop.'px;--vtl-sep-ext-mobile:'.$sep_ext_mobile.'px;';
        $wrap_style_vars .= '--vtl-item-pad-desktop:'.$item_pad_desktop.'px;--vtl-item-pad-mobile:'.$item_pad_mobile.'px;';
        // Bind items-per-view on desktop to spacing (smaller spacing -> more items per view)
        $items_desktop = 6;
        if ($sep_w_desktop <= 4)      { $items_desktop = 10; }
        elseif ($sep_w_desktop <= 8)  { $items_desktop = 8; }
        elseif ($sep_w_desktop <= 16) { $items_desktop = 7; }
        // Mobile stays at 2 per view to avoid layout breakage
        $items_mobile = 2;
        $wrap_style_vars .= '--vtl-items-desktop:'.$items_desktop.';--vtl-items-mobile:'.$items_mobile.';';
        $wrap_style = ' style="' . ( $font_px ? 'font-size:'.$font_px.'px;' : '' ) . $wrap_style_vars . '"';
        // Initialize CSS string
        $css = '';
        // Separator widths with highest specificity
        $css .= '#'.esc_attr($wrap_id).' .vecco-tl-sep{flex-basis:'.esc_attr($sep_w_desktop).'px!important;max-width:'.esc_attr($sep_w_desktop).'px!important;width:'.esc_attr($sep_w_desktop).'px!important}';
        $css .= '@media(max-width:768px){#'.esc_attr($wrap_id).' .vecco-tl-sep{flex-basis:'.esc_attr($sep_w_mobile).'px!important;max-width:'.esc_attr($sep_w_mobile).'px!important;width:'.esc_attr($sep_w_mobile).'px!important}}';
        // Edge spacers disabled (container padding controls edges)
        $edge_desktop = 0; $edge_mobile = 0;
        $css .= '#'.esc_attr($wrap_id).' .vecco-tl-edge{flex:0 0 '.esc_attr($edge_desktop).'px;max-width:'.esc_attr($edge_desktop).'px;width:'.esc_attr($edge_desktop).'px}';
        $css .= '@media(max-width:768px){#'.esc_attr($wrap_id).' .vecco-tl-edge{flex-basis:'.esc_attr($edge_mobile).'px;max-width:'.esc_attr($edge_mobile).'px;width:'.esc_attr($edge_mobile).'px}}';
        // Apply per-timeline fonts if set; otherwise fall back to global settings
        if ( $pt_font_year !== '' )  { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-year{font-family:'.$pt_font_year.'!important}'; }
        elseif ( $gf_year !== '' )  { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-year{font-family:'.$gf_year.'!important}'; }
        if ( $pt_font_title !== '' ) { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-title{font-family:'.$pt_font_title.'!important}'; }
        elseif ( $gf_title !== '' ) { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-title{font-family:'.$gf_title.'!important}'; }
        if ( $pt_font_desc !== '' )  { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-desc{font-family:'.$pt_font_desc.'!important}'; }
        elseif ( $gf_desc !== '' )  { $css .= '#'.esc_attr($wrap_id).' .vecco-tl-desc{font-family:'.$gf_desc.'!important}'; }
        
        echo '<style id="'.esc_attr($wrap_id).'-scoped">'.$css.'</style>';
        $wrap_classes = 'vecco-timeline';
        if ( $is_centered ) { $wrap_classes .= ' is-centered'; }
        if ( $is_fullwidth ) { $wrap_classes .= ' is-fullwidth'; }
        if ( $pos_style === 'centered_no_fade' ) { $wrap_classes .= ' no-fade'; }
        echo '<div id="'.esc_attr($wrap_id).'" class="'.esc_attr($wrap_classes).'" role="region" aria-label="'.esc_attr__('Interactive timeline', 'vecco-timeline').'"'.$wrap_style.'>';
        $wheel_off = !empty($defaults['disable_wheel']);
        $center_initial = isset($defaults['center_initial']) ? $defaults['center_initial'] : 'centered';
        echo '<div class="vecco-tl-track" role="list" aria-live="polite"'
           . ($wheel_off ? ' data-disable-wheel="1"' : '')
           . ' data-center-initial="'.esc_attr($center_initial).'"'
           . '>';
        // Start edge spacer only for centered style
        if ( $is_centered ) {
            echo '<div class="vecco-tl-edge vecco-tl-edge-start" aria-hidden="true"></div>';
        }
        $count = count( $items );
        foreach ( $items as $i => $it ) {
            $year = esc_html( $it['year'] ?? '' );
            $title = esc_html( $it['title'] ?? '' );
            $desc = (string)( $it['desc'] ?? '' );
            $icon = esc_url( $it['icon'] ?? '' );
            $color = esc_attr( $it['color'] ?? '#00BCD4' );
            $item_year_color = isset($it['year_color']) ? esc_attr($it['year_color']) : '';
            $size = isset($it['icon_size']) && (int)$it['icon_size'] ? (int)$it['icon_size'] : (int) ($defaults['icon'] ?? 72);
            $aria_label = sprintf(__('Timeline event: %s %s', 'vecco-timeline'), $year, $title);
            echo '<div class="vecco-tl-item" role="listitem" aria-label="'.esc_attr($aria_label).'">';
            echo '<div class="vecco-tl-pin" aria-hidden="true">';
            $svg = isset($it['svg']) ? (string)$it['svg'] : '';
            $preset = isset($it['preset']) ? (string)$it['preset'] : '';
            if ( $svg ) {
                // Trust limited inline SVG; width and color applied via wrapper span
                echo '<span class="vecco-tl-svg" style="display:inline-block;width:'.esc_attr($size).'px;color:'.esc_attr($color).'">' . $svg . '</span>';
            } elseif ( $preset ) {
                echo self::render_preset_svg( $preset, $size, $color );
            } elseif ( $icon ) {
                echo '<img src="' . $icon . '" alt="'.esc_attr($title).'" loading="lazy" decoding="async" style="width:' . esc_attr( $size ) . 'px; height:auto;" />';
            }
            echo '</div>';
            echo '<div class="vecco-tl-text">';
            $year_style  = [];
            $title_style = [];
            $desc_style  = [];
            // Colors priority: per-item year_color > per‑timeline year_color > per-item accent color
            if ( $item_year_color ) { $year_style[] = 'color:'.esc_attr($item_year_color); }
            elseif ( $year_color )  { $year_style[] = 'color:'.esc_attr($year_color); }
            else { $year_style[] = 'color:'.esc_attr($color); }
            if ( $title_color ) { $title_style[] = 'color:'.esc_attr($title_color); }
            if ( $desc_color )  { $desc_style[]  = 'color:'.esc_attr($desc_color); }
            // Sizes
            if ( $year_size )  { $year_style[]  = 'font-size:'.(int)$year_size.'px'; }
            if ( $title_size ) { $title_style[] = 'font-size:'.(int)$title_size.'px'; }
            if ( $desc_size )  { $desc_style[]  = 'font-size:'.(int)$desc_size.'px'; }
            if ( $year )  echo '<div class="vecco-tl-year" style="'.esc_attr(implode(';',$year_style)).'">'  . $year  . '</div>';
            if ( $title ) echo '<div class="vecco-tl-title" style="'.esc_attr(implode(';',$title_style)).'">' . $title . '</div>';
            if ( $desc )  echo '<div class="vecco-tl-desc" style="'.esc_attr(implode(';',$desc_style)).'">'  . nl2br( esc_html( $desc ) ) . '</div>';
            echo '</div>';
            echo '</div>';
            if ( $i < $count - 1 ) {
                echo '<div class="vecco-tl-sep"><div class="vecco-sep" style="background:'.esc_attr($sep_color).'"></div></div>';
            }
        }
        echo '</div>';
        // Custom scrollbar under the track (frontend) with ARIA attributes
        echo '<div class="vecco-scrollbar vecco-scrollbar-horizontal" role="scrollbar" aria-controls="'.esc_attr($wrap_id).'" aria-label="'.esc_attr__('Timeline scrollbar', 'vecco-timeline').'"><div class="vecco-scrollbar-drag"></div></div>';
        echo '</div>';
        return ob_get_clean();
    }

    private static function render_preset_svg( $key, $size, $color = '' ) {
        $svgAttrs = 'viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
        $svgs = [
            'shovel'    => '<svg '.$svgAttrs.'><path d="M14 3l7 7-4 4-7-7z"/><path d="M3 21l7-7"/></svg>',
            'plug'      => '<svg '.$svgAttrs.'><path d="M9 7v4"/><path d="M15 7v4"/><path d="M7 11h10v3a5 5 0 0 1-5 5 5 5 0 0 1-5-5v-3z"/></svg>',
            'beaker'    => '<svg '.$svgAttrs.'><path d="M6 2h12"/><path d="M9 2v6L4 20a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2L15 8V2"/></svg>',
            'factory'   => '<svg '.$svgAttrs.'><path d="M3 21h18"/><path d="M3 21V9l6 3V9l6 3V6l6 3v12"/><path d="M9 21v-4h3v4"/></svg>',
            'bolt'      => '<svg '.$svgAttrs.'><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>',
            'award'     => '<svg '.$svgAttrs.'><circle cx="12" cy="8" r="4"/><path d="M8 12l-2 8 6-3 6 3-2-8"/></svg>',
            'truck'     => '<svg '.$svgAttrs.'><rect x="1" y="7" width="13" height="8"/><path d="M14 10h4l3 3v2h-3"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>',
            'leaf'      => '<svg '.$svgAttrs.'><path d="M2 22C2 12 12 2 22 2c0 10-10 20-20 20z"/><path d="M2 22C12 22 22 12 22 2"/></svg>',
            'globe'     => '<svg '.$svgAttrs.'><circle cx="12" cy="12" r="9"/><path d="M2 12h20"/><path d="M12 3a15 15 0 0 1 0 18"/><path d="M12 3a15 15 0 0 0 0 18"/></svg>',
            'briefcase' => '<svg '.$svgAttrs.'><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
            'chart'     => '<svg '.$svgAttrs.'><path d="M3 3v18h18"/><rect x="7" y="13" width="3" height="5"/><rect x="12" y="9" width="3" height="9"/><rect x="17" y="5" width="3" height="13"/></svg>',
            'users'     => '<svg '.$svgAttrs.'><circle cx="9" cy="8" r="3"/><path d="M2 21a7 7 0 0 1 14 0"/><circle cx="17" cy="8" r="3"/><path d="M14 15a7 7 0 0 1 8 6"/></svg>',
            'wrench'    => '<svg '.$svgAttrs.'><path d="M14.7 6.3a4 4 0 1 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 2.4-8.4z"/></svg>',
        ];
        if ( empty( $svgs[$key] ) ) return '';
        $style = 'display:inline-block;width:'.esc_attr($size).'px';
        if ( $color ) { $style .= ';color:'.esc_attr($color); }
        return '<span class="vecco-tl-svg" style="'.$style.'">'.$svgs[$key].'</span>';
    }
}
