<?php
namespace Real_Estate_CRM\Forms;

defined( 'ABSPATH' ) || exit;

class Forms_Init {
    public static function init() {
        add_action( 'plugins_loaded', [ __CLASS__, 'register_cf7_form' ], 20 );
    }

    public static function register_cf7_form() {
        if ( class_exists( 'WPCF7' ) ) {
            require_once RECRM_PATH . 'includes/forms/class-cf7-form-handler.php';
            CF7_Form_Handler::register_form();
        } else {
            error_log( 'CF7 is not installed or activated!' );
        }
    }
}
