<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Vecco_Timeline_Admin {
    const OPTION = 'vecco_tl_settings';

    public static function init(){
        add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register' ] );
    }

    public static function menu(){
        add_submenu_page(
            'edit.php?post_type=vecco_timeline',
            __( 'Timeline Settings', 'vecco-timeline' ),
            __( 'Settings', 'vecco-timeline' ),
            'manage_options',
            'vecco_tl_settings',
            [ __CLASS__, 'page' ]
        );
    }

    public static function register(){
        register_setting( 'vecco_tl_settings', self::OPTION );
        // No sections - we'll create custom HTML layout in page()
    }

    public static function get(){
        $defaults = [
            'color' => '#00BCD4',
            'icon' => 72,
            'font' => 16,
            'sep_w_desktop' => 128,
            'sep_w_mobile' => 16,
            'font_year' => '',
            'font_title' => '',
            'font_desc' => '',
            'webfont_url' => '',
            'disable_wheel' => 0,
            // Positioning defaults (global)
            'position_style' => 'original', // original | centered | fullwidth | centered_no_fade
            'center_initial' => 'centered',
            'pad_desktop' => 60,
            'pad_tablet'  => 40,
            'pad_mobile'  => 16,
            'fade_desktop' => 22,
            'fade_tablet'  => 18,
            'fade_mobile'  => 14,
            // Original style: margins per breakpoint
            'orig_m_desktop' => 30,
            'orig_m_tablet'  => 24,
            'orig_m_mobile'  => 16,
            // Full width: optional safe-area gutters (per breakpoint)
            'fw_safe_desktop' => 0,
            'fw_safe_tablet'  => 0,
            'fw_safe_mobile'  => 8,
        ];
        $opt = get_option( self::OPTION, [] );
        if ( ! is_array( $opt ) ) $opt = [];
        return wp_parse_args( $opt, $defaults );
    }

    public static function page(){
        $settings = self::get();
        ?>
        <div class="wrap vecco-tl-settings">
            <style>
            .vecco-tl-settings h1{margin-bottom:20px;font-size:23px}
            .vecco-tl-settings .settings-card{margin-bottom:16px;padding:0;border:1px solid #dfe3e8;background:#fff;border-radius:8px;overflow:hidden}
            .vecco-tl-settings .settings-header{padding:14px 16px;border-bottom:2px solid #e2e8f0;background:#fff}
            .vecco-tl-settings .settings-header h2{margin:0;font-size:14px;font-weight:700;color:#1e293b;letter-spacing:0.3px;text-transform:uppercase}
            .vecco-tl-settings .settings-header p{margin:4px 0 0;font-size:12px;color:#64748b;line-height:1.5;font-weight:400}
            .vecco-tl-settings .settings-body{padding:16px 20px}
            .vecco-tl-settings .settings-group{margin-bottom:20px}
            .vecco-tl-settings .settings-group:last-child{margin-bottom:0}
            .vecco-tl-settings .group-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px}
            .vecco-tl-settings .group-desc{margin:0 0 12px;font-size:12px;color:#64748b;line-height:1.6}
            .vecco-tl-settings .field-row{display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:16px}
            .vecco-tl-settings .field-row:last-child{margin-bottom:0}
            .vecco-tl-settings .field-item{display:flex;flex-direction:column;gap:6px}
            .vecco-tl-settings .field-item.flex-grow{flex:1;min-width:250px}
            .vecco-tl-settings .field-label{font-size:13px;font-weight:600;color:#475569;margin-bottom:2px}
            .vecco-tl-settings .field-help{font-size:12px;color:#64748b;line-height:1.5;margin-top:4px}
            .vecco-tl-settings input[type=text],
            .vecco-tl-settings input[type=url],
            .vecco-tl-settings input[type=number],
            .vecco-tl-settings textarea{width:100%;border:1px solid #cbd5e1;border-radius:4px;padding:7px 10px;font-size:13px;background:#fff;transition:border-color .15s ease}
            .vecco-tl-settings input[type=color]{width:80px;height:38px;border:1px solid #cbd5e1;border-radius:4px;padding:3px;cursor:pointer;background:#fff}
            .vecco-tl-settings input[type=number]{width:90px}
            .vecco-tl-settings textarea{min-height:70px;font-family:monospace;font-size:12px}
            .vecco-tl-settings input:focus,
            .vecco-tl-settings textarea:focus{outline:0;border-color:#5b9dd9}
            .vecco-tl-settings .button-primary{border-radius:6px;padding:8px 16px;font-size:13px}
            .vecco-tl-settings .typography-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;padding:10px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0}
            .vecco-tl-settings .typography-col{display:flex;flex-direction:column;gap:6px}
            .vecco-tl-settings .typography-col .col-label{font-size:10px;font-weight:700;color:#64748b;margin-bottom:2px}
            .vecco-tl-settings hr{margin:20px 0;border:0;border-top:1px solid #dfe3e8}
            .vecco-tl-settings .usage-card{border:1px solid #e6e7eb;background:#fff;border-radius:10px;padding:18px}
            .vecco-tl-settings code{background:#f6f7f7;border:1px solid #e2e4e7;padding:2px 6px;border-radius:4px;font-size:12px}
            </style>
            <h1><?php esc_html_e( 'Timeline Settings', 'vecco-timeline' ); ?></h1>
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:12px 16px;margin-bottom:20px">
                <p style="margin:0;font-size:12px;color:#0c4a6e;line-height:1.6">
                    <strong><?php esc_html_e( 'How Settings Work:', 'vecco-timeline' ); ?></strong>
                    <?php esc_html_e( 'These are global defaults that apply to ALL timelines. When editing an individual timeline, you\'ll see "Per-timeline Overrides" which let you customize that specific timeline. If a per-timeline setting is empty, it inherits from these global defaults. Use global settings for consistency across multiple timelines, and per-timeline overrides when you need unique styling for specific timelines.', 'vecco-timeline' ); ?>
                </p>
            </div>
        <script>
        window.addEventListener('DOMContentLoaded', function(){
          var radios = document.querySelectorAll('input[name="<?php echo esc_js( self::OPTION ); ?>[position_style]"]');
          var centered = document.getElementById('vtl-pos-centered-fields');
          var original = document.getElementById('vtl-pos-original-fields');
          var fullwidth = document.getElementById('vtl-pos-fullwidth-fields');
          var centeredNoFade = document.getElementById('vtl-pos-centered-nofade-fields');
          function sync(){
            var checked = document.querySelector('input[name="<?php echo esc_js( self::OPTION ); ?>[position_style]"]:checked');
            var val = checked ? checked.value : 'original';
            if (centered) centered.style.display = (val === 'centered') ? '' : 'none';
            if (original) original.style.display = (val === 'original') ? '' : 'none';
            if (fullwidth) fullwidth.style.display = (val === 'fullwidth') ? '' : 'none';
            if (centeredNoFade) centeredNoFade.style.display = (val === 'centered_no_fade') ? '' : 'none';
          }
          radios.forEach(function(r){ r.addEventListener('change', sync); });
          sync();
        });
        </script>
            <form method="post" action="options.php">
                <?php settings_fields( 'vecco_tl_settings' ); ?>
                
                <!-- Default Appearance Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2><?php esc_html_e( 'Default Appearance', 'vecco-timeline' ); ?></h2>
                        <p><?php esc_html_e( 'Global defaults for all timelines. These can be overridden per-timeline in the "Per-timeline Overrides" section when editing individual timelines.', 'vecco-timeline' ); ?></p>
                    </div>
                    <div class="settings-body">
                        <!-- General Defaults -->
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'General Defaults', 'vecco-timeline' ); ?></div>
                            <div class="group-desc"><?php esc_html_e( 'These settings affect all timeline elements globally unless customized per-timeline or per-item.', 'vecco-timeline' ); ?></div>
                            
                            <!-- Accent Color - Full Width -->
                            <div style="display:flex;align-items:center;gap:16px;padding:12px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0;margin-bottom:10px">
                                <div style="display:flex;flex-direction:column;gap:6px;min-width:140px">
                                    <label class="field-label"><?php esc_html_e( 'Accent Color', 'vecco-timeline' ); ?></label>
                                    <input type="color" name="<?php echo esc_attr( self::OPTION ); ?>[color]" value="<?php echo esc_attr( $settings['color'] ); ?>" />
                                </div>
                                <span class="field-help" style="margin-top:0;flex:1"><?php esc_html_e( 'Default color for icons and year text. Can be overridden per-item using "Accent Color" in the timeline editor.', 'vecco-timeline' ); ?></span>
                            </div>
                            
                            <!-- Icon Size & Base Font Size - Side by Side -->
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                <div style="display:flex;align-items:center;gap:16px;padding:12px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">
                                    <div style="display:flex;flex-direction:column;gap:6px;min-width:140px">
                                        <label class="field-label"><?php esc_html_e( 'Icon Size (px)', 'vecco-timeline' ); ?></label>
                                        <input type="number" min="16" max="256" name="<?php echo esc_attr( self::OPTION ); ?>[icon]" value="<?php echo esc_attr( $settings['icon'] ); ?>" />
                                    </div>
                                    <span class="field-help" style="margin-top:0;flex:1;font-size:11px"><?php esc_html_e( 'Default icon size (16-256px). Customize per-item in Visual Settings.', 'vecco-timeline' ); ?></span>
                                </div>
                                <div style="display:flex;align-items:center;gap:16px;padding:12px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">
                                    <div style="display:flex;flex-direction:column;gap:6px;min-width:140px">
                                        <label class="field-label"><?php esc_html_e( 'Base Font Size (px)', 'vecco-timeline' ); ?></label>
                                        <input type="number" min="10" max="32" name="<?php echo esc_attr( self::OPTION ); ?>[font]" value="<?php echo esc_attr( $settings['font'] ); ?>" />
                                    </div>
                                    <span class="field-help" style="margin-top:0;flex:1;font-size:11px"><?php esc_html_e( 'Base font size (10-32px). Override per-timeline in General Settings.', 'vecco-timeline' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Initial View', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[center_initial]" value="left" <?php checked( $settings['center_initial'], 'left' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Left', 'vecco-timeline' ); ?></span>
                                </label>
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[center_initial]" value="centered" <?php checked( $settings['center_initial'], 'centered' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Centered (balanced)', 'vecco-timeline' ); ?></span>
                                </label>
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[center_initial]" value="right" <?php checked( $settings['center_initial'], 'right' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Right', 'vecco-timeline' ); ?></span>
                                </label>
                            </div>
                            <p class="field-help"><?php esc_html_e( 'Applies when style is Centered (with or without fade) or Full width. Original ignores this.', 'vecco-timeline' ); ?></p>
                        </div>
                        
                        <!-- Spacing Settings -->
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Spacing Settings', 'vecco-timeline' ); ?></div>
                            <div class="group-desc"><?php esc_html_e( 'Control the gap between timeline items on different screen sizes. These are global and cannot be changed per-timeline.', 'vecco-timeline' ); ?></div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                <div style="display:flex;align-items:center;gap:16px;padding:12px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">
                                    <div style="display:flex;flex-direction:column;gap:6px;min-width:140px">
                                        <label class="field-label"><?php esc_html_e( 'Desktop Spacing (px)', 'vecco-timeline' ); ?></label>
                                        <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[sep_w_desktop]" value="<?php echo esc_attr( $settings['sep_w_desktop'] ); ?>" />
                                    </div>
                                    <span class="field-help" style="margin-top:0;flex:1;font-size:11px"><?php esc_html_e( 'Gap between items on screens >768px (recommended: 80-200px). Set as low as 0-8px for tight spacing. Applies globally.', 'vecco-timeline' ); ?></span>
                                </div>
                                <div style="display:flex;align-items:center;gap:16px;padding:12px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0">
                                    <div style="display:flex;flex-direction:column;gap:6px;min-width:140px">
                                        <label class="field-label"><?php esc_html_e( 'Mobile Spacing (px)', 'vecco-timeline' ); ?></label>
                                        <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[sep_w_mobile]" value="<?php echo esc_attr( $settings['sep_w_mobile'] ); ?>" />
                                    </div>
                                    <span class="field-help" style="margin-top:0;flex:1;font-size:11px"><?php esc_html_e( 'Gap between items on screens ≤768px (recommended: 8-32px). Set as low as 0-4px for tight spacing. Applies globally.', 'vecco-timeline' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Positioning Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2><?php esc_html_e( 'Positioning', 'vecco-timeline' ); ?></h2>
                        <p><?php esc_html_e( 'Choose how timelines are positioned. "Original" keeps the classic layout. "Centered with fade" centers the container with fixed side padding and a subtle edge fade while scrolling.', 'vecco-timeline' ); ?></p>
                    </div>
                    <div class="settings-body">
                        <?php $pos = isset($settings['position_style']) ? $settings['position_style'] : 'original'; ?>
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Style', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[position_style]" value="original" <?php checked( $pos, 'original' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Original (default)', 'vecco-timeline' ); ?></span>
                                </label>
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[position_style]" value="centered" <?php checked( $pos, 'centered' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Centered with fade', 'vecco-timeline' ); ?></span>
                                </label>
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[position_style]" value="fullwidth" <?php checked( $pos, 'fullwidth' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Full width', 'vecco-timeline' ); ?></span>
                                </label>
                                <label class="field-item" style="flex-direction:row;align-items:center;gap:8px">
                                    <input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[position_style]" value="centered_no_fade" <?php checked( $pos, 'centered_no_fade' ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Centered (no fade)', 'vecco-timeline' ); ?></span>
                                </label>
                            </div>
                        </div>
                        <!-- Original style settings -->
                        <div class="settings-group" id="vtl-pos-original-fields" style="<?php echo ($pos==='original') ? '' : 'display:none'; ?>">
                            <div class="group-title"><?php esc_html_e( 'Original – Settings', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Side Margin (px) – Desktop', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[orig_m_desktop]" value="<?php echo esc_attr( (int)$settings['orig_m_desktop'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Side Margin (px) – Tablet', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[orig_m_tablet]" value="<?php echo esc_attr( (int)$settings['orig_m_tablet'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Side Margin (px) – Mobile', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[orig_m_mobile]" value="<?php echo esc_attr( (int)$settings['orig_m_mobile'] ); ?>" />
                                </div>
                            </div>
                            <p class="field-help"><?php esc_html_e( 'These margins apply when the positioning style is set to "Original".', 'vecco-timeline' ); ?></p>
                        </div>
                        <div class="settings-group" id="vtl-pos-centered-fields" style="<?php echo ($pos==='centered') ? '' : 'display:none'; ?>">
                            <div class="group-title"><?php esc_html_e( 'Centered with fade – Settings', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Desktop Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_desktop]" value="<?php echo esc_attr( (int)$settings['pad_desktop'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Tablet Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_tablet]" value="<?php echo esc_attr( (int)$settings['pad_tablet'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Mobile Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_mobile]" value="<?php echo esc_attr( (int)$settings['pad_mobile'] ); ?>" />
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Fade Intensity – Desktop (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fade_desktop]" value="<?php echo esc_attr( (int)$settings['fade_desktop'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Fade Intensity – Tablet (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fade_tablet]" value="<?php echo esc_attr( (int)$settings['fade_tablet'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Fade Intensity – Mobile (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fade_mobile]" value="<?php echo esc_attr( (int)$settings['fade_mobile'] ); ?>" />
                                </div>
                            </div>
                            <p class="field-help"><?php esc_html_e( 'These values apply when the positioning style is set to "Centered with fade".', 'vecco-timeline' ); ?></p>
                        </div>
                        <!-- Full width style settings -->
                        <div class="settings-group" id="vtl-pos-fullwidth-fields" style="<?php echo ($pos==='fullwidth') ? '' : 'display:none'; ?>">
                            <div class="group-title"><?php esc_html_e( 'Full width – Settings', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Safe Area (px) – Desktop', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fw_safe_desktop]" value="<?php echo esc_attr( (int)$settings['fw_safe_desktop'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Safe Area (px) – Tablet', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fw_safe_tablet]" value="<?php echo esc_attr( (int)$settings['fw_safe_tablet'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Safe Area (px) – Mobile', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="60" name="<?php echo esc_attr( self::OPTION ); ?>[fw_safe_mobile]" value="<?php echo esc_attr( (int)$settings['fw_safe_mobile'] ); ?>" />
                                </div>
                            </div>
                            <p class="field-help"><?php esc_html_e( 'Optional inner gutters at screen edges while keeping the timeline full width. Set to 0 for edge-to-edge items.', 'vecco-timeline' ); ?></p>
                        </div>
                        <!-- Centered (no fade) style settings -->
                        <div class="settings-group" id="vtl-pos-centered-nofade-fields" style="<?php echo ($pos==='centered_no_fade') ? '' : 'display:none'; ?>">
                            <div class="group-title"><?php esc_html_e( 'Centered (no fade) – Settings', 'vecco-timeline' ); ?></div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Desktop Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_desktop]" value="<?php echo esc_attr( (int)$settings['pad_desktop'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Tablet Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_tablet]" value="<?php echo esc_attr( (int)$settings['pad_tablet'] ); ?>" />
                                </div>
                                <div class="field-item">
                                    <label class="field-label"><?php esc_html_e( 'Mobile Padding (px)', 'vecco-timeline' ); ?></label>
                                    <input type="number" min="0" max="200" name="<?php echo esc_attr( self::OPTION ); ?>[pad_mobile]" value="<?php echo esc_attr( (int)$settings['pad_mobile'] ); ?>" />
                                </div>
                            </div>
                            <p class="field-help"><?php esc_html_e( 'Same centering paddings as "Centered with fade" but without edge fading.', 'vecco-timeline' ); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Typography Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2><?php esc_html_e( 'Typography', 'vecco-timeline' ); ?></h2>
                        <p><?php esc_html_e( 'Global font defaults for all timelines. Per-timeline overrides are available in the Typography section when editing individual timelines. Leave empty to inherit from your theme.', 'vecco-timeline' ); ?></p>
                    </div>
                    <div class="settings-body">
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Font Families', 'vecco-timeline' ); ?></div>
                            <div class="group-desc"><?php esc_html_e( 'Set default fonts for year, title, and description text. These can be customized per-timeline in the "Typography" section with color, size, and font options. Use system fonts or load custom fonts via Web Fonts below.', 'vecco-timeline' ); ?></div>
                            <?php echo self::global_font_preset_datalist(); ?>
                            <div class="typography-grid">
                                <div class="typography-col">
                                    <div class="col-label"><?php esc_html_e( 'YEAR', 'vecco-timeline' ); ?></div>
                                    <label class="field-label"><?php esc_html_e( 'Font Family', 'vecco-timeline' ); ?></label>
                                    <input list="vecco-tl-global-font-presets" type="text" name="<?php echo esc_attr( self::OPTION ); ?>[font_year]" value="<?php echo esc_attr( $settings['font_year'] ); ?>" placeholder="e.g. Inter, sans-serif" />
                                    <span class="field-help" style="margin-top:4px"><?php esc_html_e( 'Override per-timeline in Typography > Year > Font', 'vecco-timeline' ); ?></span>
                                </div>
                                <div class="typography-col">
                                    <div class="col-label"><?php esc_html_e( 'TITLE', 'vecco-timeline' ); ?></div>
                                    <label class="field-label"><?php esc_html_e( 'Font Family', 'vecco-timeline' ); ?></label>
                                    <input list="vecco-tl-global-font-presets" type="text" name="<?php echo esc_attr( self::OPTION ); ?>[font_title]" value="<?php echo esc_attr( $settings['font_title'] ); ?>" placeholder="e.g. Georgia, serif" />
                                    <span class="field-help" style="margin-top:4px"><?php esc_html_e( 'Override per-timeline in Typography > Title > Font', 'vecco-timeline' ); ?></span>
                                </div>
                                <div class="typography-col">
                                    <div class="col-label"><?php esc_html_e( 'DESCRIPTION', 'vecco-timeline' ); ?></div>
                                    <label class="field-label"><?php esc_html_e( 'Font Family', 'vecco-timeline' ); ?></label>
                                    <input list="vecco-tl-global-font-presets" type="text" name="<?php echo esc_attr( self::OPTION ); ?>[font_desc]" value="<?php echo esc_attr( $settings['font_desc'] ); ?>" placeholder="e.g. Arial, sans-serif" />
                                    <span class="field-help" style="margin-top:4px"><?php esc_html_e( 'Override per-timeline in Typography > Description > Font', 'vecco-timeline' ); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Web Fonts', 'vecco-timeline' ); ?></div>
                            <div class="group-desc"><?php esc_html_e( 'Load external fonts globally (e.g., Google Fonts) for use across all timelines. You can also add per-timeline webfonts in "General Settings > Webfont URL(s)" when editing individual timelines. Separate multiple URLs with line breaks.', 'vecco-timeline' ); ?></div>
                            <div class="field-item flex-grow">
                                <label class="field-label"><?php esc_html_e( 'Global Webfont URL(s)', 'vecco-timeline' ); ?></label>
                                <textarea rows="2" name="<?php echo esc_attr( self::OPTION ); ?>[webfont_url]" placeholder="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"><?php echo esc_textarea( $settings['webfont_url'] ); ?></textarea>
                                <span class="field-help"><?php esc_html_e( 'Fonts loaded here are available to all timelines. For timeline-specific fonts, use the Webfont URL field in per-timeline General Settings.', 'vecco-timeline' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Behavior Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2><?php esc_html_e( 'Behavior', 'vecco-timeline' ); ?></h2>
                        <p><?php esc_html_e( 'Control how users interact with all timelines. These are global settings that cannot be changed per-timeline.', 'vecco-timeline' ); ?></p>
                    </div>
                    <div class="settings-body">
                        <div class="settings-group">
                            <div class="group-title"><?php esc_html_e( 'Mouse Wheel Scrolling', 'vecco-timeline' ); ?></div>
                            <div class="group-desc"><?php esc_html_e( 'By default, vertical mouse wheel movements scroll the timeline horizontally with smooth animation. This setting affects all timelines globally and improves user experience by making timelines easier to navigate. Disable this if you prefer manual drag-only navigation.', 'vecco-timeline' ); ?></div>
                            <div class="field-item">
                                <label style="display:flex;align-items:center;gap:6px">
                                    <input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[disable_wheel]" value="1" <?php checked( !empty($settings['disable_wheel']) ); ?> />
                                    <span class="field-label" style="margin:0"><?php esc_html_e( 'Disable mouse wheel scrolling on all timelines', 'vecco-timeline' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php submit_button(); ?>
            </form>
            <hr />
            <div class="usage-card">
            <h2><?php esc_html_e( 'Usage', 'vecco-timeline' ); ?></h2>
            <p><strong><?php esc_html_e( 'Shortcode:', 'vecco-timeline' ); ?></strong> <code>[vecco_timeline id="123"]</code></p>
            <ol>
                <li><?php esc_html_e( 'Go to Timelines > Add New and create a timeline.', 'vecco-timeline' ); ?></li>
                <li><?php esc_html_e( 'Add items. You may upload an icon, paste SVG code, or choose a preset icon.', 'vecco-timeline' ); ?></li>
                <li><?php esc_html_e( 'Optionally set per‑timeline Base Font Size and Separator Color.', 'vecco-timeline' ); ?></li>
                <li><?php esc_html_e( 'Publish the timeline and copy its shortcode below to insert into any page/post or builder.', 'vecco-timeline' ); ?></li>
            </ol>
            <h3><?php esc_html_e( 'Your Timelines', 'vecco-timeline' ); ?></h3>
            <div>
                <?php
                $q = new WP_Query([
                    'post_type' => 'vecco_timeline',
                    'post_status' => 'any',
                    'posts_per_page' => 100,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'no_found_rows' => true,
                ]);
                if ( $q->have_posts() ) {
                    echo '<ul style="margin:0;padding-left:18px;">';
                    while ( $q->have_posts() ) { $q->the_post();
                        $id = get_the_ID();
                        $title = get_the_title();
                        $sc = '[vecco_timeline id="' . $id . '"]';
                        echo '<li style="margin-bottom:8px;">' . esc_html( $title ) . ' (ID ' . esc_html( (string) $id ) . ') '
                           . '<input type="text" readonly value="' . esc_attr( $sc ) . '" style="width:280px;margin-left:6px;" /> '
                           . '<button class="button vecco-copy" data-copy="' . esc_attr( $sc ) . '">' . esc_html__( 'Copy', 'vecco-timeline' ) . '</button>'
                           . '</li>';
                    }
                    echo '</ul>';
                    wp_reset_postdata();
                } else {
                    esc_html_e( 'No timelines yet. Create one to get its shortcode.', 'vecco-timeline' );
                }
                ?>
            </div>
            <script>
            (function(){
                document.addEventListener('click', function(e){
                    if(e.target && e.target.classList.contains('vecco-copy')){
                        e.preventDefault();
                        var t = e.target.getAttribute('data-copy') || '';
                        if(!t) return;
                        navigator.clipboard && navigator.clipboard.writeText ? navigator.clipboard.writeText(t) : (function(txt){
                            var ta = document.createElement('textarea');
                            ta.value = txt; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); }catch(_){} document.body.removeChild(ta);
                        })(t);
                        e.target.textContent = '<?php echo esc_js( __( 'Copied', 'vecco-timeline' ) ); ?>';
                        setTimeout(function(){ e.target.textContent = '<?php echo esc_js( __( 'Copy', 'vecco-timeline' ) ); ?>'; }, 1200);
                    }
                });
            })();
            </script>
            <hr />
            <div class="donate-card" style="margin-top:18px;max-width:760px;display:flex;align-items:center;gap:16px">
                <div style="flex:1">
                    <h3 style="margin:0 0 6px;"><?php esc_html_e( 'Support Development', 'vecco-timeline' ); ?></h3>
                    <p style="margin:0;color:#555;"><?php esc_html_e( 'If this plugin helped your project, consider buying the developer a coffee.', 'vecco-timeline' ); ?></p>
                </div>
                <div>
                    <a class="button button-primary" target="_blank" rel="noopener" href="https://www.paypal.me/arnelborresgo" style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;font-size:14px;">
                        <span>❤️ <?php esc_html_e( 'Donate via PayPal', 'vecco-timeline' ); ?></span>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    public static function field_color(){
        $v = self::get()['color'];
        echo '<input type="color" name="' . esc_attr( self::OPTION ) . '[color]" value="' . esc_attr( $v ) . '" />';
        echo '<p class="description" style="max-width:760px">' . esc_html__( 'Default accent color for timeline items. This color is used for icons/SVGs and year text when no per-item color is specified. You can override this for individual items when editing a timeline.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_icon(){
        $v = self::get()['icon'];
        echo '<input type="number" min="16" max="256" name="' . esc_attr( self::OPTION ) . '[icon]" value="' . esc_attr( $v ) . '" />';
        echo '<p class="description" style="max-width:760px">' . esc_html__( 'Default size for timeline item icons in pixels. Controls the width of icons, images, and SVG graphics. You can override this for individual items when editing a timeline. Range: 16-256px. Recommended: 64-96px.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_font(){
        $v = self::get()['font'];
        echo '<input type="number" min="10" max="32" name="' . esc_attr( self::OPTION ) . '[font]" value="' . esc_attr( $v ) . '" />';
        echo '<p class="description" style="max-width:760px">' . esc_html__( 'Base font size for the timeline in pixels. All text sizes (year, title, description) are relative to this value. You can also set this per-timeline when editing. Range: 10-32px. Default: 16px.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_sep_w_desktop(){
        $v = isset(self::get()['sep_w_desktop']) ? (int) self::get()['sep_w_desktop'] : 128;
        echo '<input type="number" min="0" max="400" step="1" name="' . esc_attr( self::OPTION ) . '[sep_w_desktop]" value="' . esc_attr( $v ) . '" />';
        echo '<p class="description" style="max-width:760px">'
           . esc_html__( 'In pixels. This controls the gap between timeline items on desktop (screens wider than 768px). Larger value = wider spacing. Does not change the line thickness; it only sets the spacer column width. Recommended: 80–200px.', 'vecco-timeline' )
           . '</p>';
    }

    public static function field_sep_w_mobile(){
        $v = isset(self::get()['sep_w_mobile']) ? (int) self::get()['sep_w_mobile'] : 16;
        echo '<input type="number" min="0" max="200" step="1" name="' . esc_attr( self::OPTION ) . '[sep_w_mobile]" value="' . esc_attr( $v ) . '" />';
        echo '<p class="description" style="max-width:760px">'
           . esc_html__( 'In pixels. This controls the gap between timeline items on mobile (screens 768px and below). Larger value = wider spacing. Keep small if you display 3–4 items per row. Recommended: 8–32px.', 'vecco-timeline' )
           . '</p>';
    }

    public static function field_font_year(){
        $v = isset(self::get()['font_year']) ? self::get()['font_year'] : '';
        echo self::global_font_preset_datalist();
        echo '<input list="vecco-tl-global-font-presets" type="text" style="width:360px" name="' . esc_attr( self::OPTION ) . '[font_year]" value="' . esc_attr( $v ) . '" placeholder="e.g. \'' . esc_attr( 'Poppins' ) . '\', Arial, sans-serif" />';
        echo '<p class="description">' . esc_html__( 'CSS font-family for the Year text. Leave empty to inherit theme fonts.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_font_title(){
        $v = isset(self::get()['font_title']) ? self::get()['font_title'] : '';
        echo self::global_font_preset_datalist();
        echo '<input list="vecco-tl-global-font-presets" type="text" style="width:360px" name="' . esc_attr( self::OPTION ) . '[font_title]" value="' . esc_attr( $v ) . '" placeholder="e.g. \'' . esc_attr( 'Inter' ) . '\', Helvetica, sans-serif" />';
        echo '<p class="description">' . esc_html__( 'CSS font-family for the Title text. Leave empty to inherit theme fonts.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_font_desc(){
        $v = isset(self::get()['font_desc']) ? self::get()['font_desc'] : '';
        echo self::global_font_preset_datalist();
        echo '<input list="vecco-tl-global-font-presets" type="text" style="width:360px" name="' . esc_attr( self::OPTION ) . '[font_desc]" value="' . esc_attr( $v ) . '" placeholder="e.g. Georgia, serif" />';
        echo '<p class="description">' . esc_html__( 'CSS font-family for the Description text. Leave empty to inherit theme fonts.', 'vecco-timeline' ) . '</p>';
    }

    public static function field_webfont_url(){
        $v = isset(self::get()['webfont_url']) ? self::get()['webfont_url'] : '';
        echo '<textarea rows="2" style="width:560px" name="' . esc_attr( self::OPTION ) . '[webfont_url]" placeholder="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">' . esc_textarea( $v ) . '</textarea>';
        echo '<p class="description" style="max-width:760px">' . esc_html__( 'Optional. One or more Webfont CSS URLs (e.g., Google Fonts). If specifying multiple, separate by newline.', 'vecco-timeline' ) . '</p>';
    }

    private static function global_font_preset_datalist(){
        return '<datalist id="vecco-tl-global-font-presets">'
            . '<option value="Inter, system-ui, -apple-system, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif"></option>'
            . '<option value="Roboto, system-ui, -apple-system, \"Segoe UI\", Helvetica, Arial, sans-serif"></option>'
            . '<option value="Poppins, Arial, sans-serif"></option>'
            . '<option value="Montserrat, Arial, sans-serif"></option>'
            . '<option value="Open Sans, Arial, sans-serif"></option>'
            . '<option value="Lato, Arial, sans-serif"></option>'
            . '<option value="Merriweather, Georgia, serif"></option>'
            . '<option value="Georgia, \"Times New Roman\", Times, serif"></option>'
            . '<option value="Arial, Helvetica, sans-serif"></option>'
            . '<option value="Helvetica, Arial, sans-serif"></option>'
            . '<option value="SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace"></option>'
            . '<option value="\"Courier New\", Courier, monospace"></option>'
            . '</datalist>';
    }

    public static function field_disable_wheel(){
        $v = !empty(self::get()['disable_wheel']);
        echo '<label><input type="checkbox" name="' . esc_attr( self::OPTION ) . '[disable_wheel]" value="1" ' . checked( $v, true, false ) . ' /> ' . esc_html__( 'Disable mouse wheel scrolling', 'vecco-timeline' ) . '</label>';
        echo '<p class="description" style="max-width:760px">' . esc_html__( 'When enabled, mouse wheel scrolling on the timeline will be disabled. By default, vertical mouse wheel movements scroll the timeline horizontally with smooth animation. Toggle this if you prefer manual drag-only navigation.', 'vecco-timeline' ) . '</p>';
    }
}

Vecco_Timeline_Admin::init();
