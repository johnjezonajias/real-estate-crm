<?php
/**
 * Plugin Name: Real Estate CRM
 * Plugin URI: https://johnjezonajias.one/
 * Description: A modular Real Estate Marketplace & Brokerage CRM system.
 * Version: 1.0.0
 * Author: John Jezon Ajias
 * Author URI: https://johnjezonajias.one/
 * License: GPL2
 * Text Domain: real-estate-crm
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'RECRM_PATH', plugin_dir_path( __FILE__ ) );
define( 'RECRM_URL', plugin_dir_url( __FILE__ ) );

// Autoload classes.
require_once RECRM_PATH . 'includes/class-crm-core.php';

// Initialize plugin.
Real_Estate_CRM\Core::init();

/**
 * Enqueue scripts and styles for property gallery.
 */
class Real_Estate_CRM_Property {
    public static function enqueue_scripts( $hook ) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_media();
            wp_enqueue_script('main-script', RECRM_URL . 'assets/js/scripts.js', ['jquery'], null, true);
            wp_enqueue_style('main-style', RECRM_URL . 'assets/css/styles.css');
        }
    }
}

// Hook into admin scripts.
add_action( 'admin_enqueue_scripts', ['Real_Estate_CRM_Property', 'enqueue_scripts'] );
