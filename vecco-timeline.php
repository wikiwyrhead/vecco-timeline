<?php
/**
 * Plugin Name: Vecco Timeline
 * Description: Horizontal timeline with draggable scroll, custom CPT, shortcode rendering, and admin controls.
 * Version: 1.5.0
 * Author: arneLG.
 * Plugin URI: https://github.com/wikiwyrhead/vecco-timeline
 * Author URI: https://github.com/wikiwyrhead
 * Text Domain: vecco-timeline
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'VECCO_TL_VERSION', '1.5.0' );
define( 'VECCO_TL_DIR', plugin_dir_path( __FILE__ ) );
define( 'VECCO_TL_URL', plugin_dir_url( __FILE__ ) );

require_once VECCO_TL_DIR . 'includes/class-vecco-timeline.php';
require_once VECCO_TL_DIR . 'includes/admin.php';

// Register CPT and hooks
add_action( 'init', [ 'Vecco_Timeline', 'register_cpt' ] );
add_action( 'init', [ 'Vecco_Timeline', 'register_meta' ] );

// Shortcode
add_shortcode( 'vecco_timeline', function( $atts ) {
    $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'vecco_timeline' );
    $post_id = absint( $atts['id'] );
    if ( ! $post_id ) return '';
    return Vecco_Timeline::render_shortcode( $post_id );
});

// Enqueue when shortcode renders (safe and scoped)
add_action( 'wp_enqueue_scripts', function(){
    // No-op here; assets are enqueued during render to avoid site-wide load.
}, 20 );

// Activation: flush rewrite for CPT
register_activation_hook( __FILE__, function(){
    Vecco_Timeline::register_cpt();
    flush_rewrite_rules();
});

register_deactivation_hook( __FILE__, function(){
    flush_rewrite_rules();
});

// Add settings link on plugins page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
    $settings_link = '<a href="' . admin_url( 'edit.php?post_type=vecco_timeline&page=vecco_tl_settings' ) . '">' . __( 'Settings', 'vecco-timeline' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
});
