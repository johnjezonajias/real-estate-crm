<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Init {
    public static function init() {
        add_action( 'rest_api_init', [__CLASS__, 'register_routes'] );
    }

    public static function register_routes() {
        require_once RECRM_PATH . 'includes/api/class-api-properties.php';
        API_Properties::register_routes();
    }
}
