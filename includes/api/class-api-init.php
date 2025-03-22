<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Init {
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        // Properties API routes.
        require_once RECRM_PATH . 'includes/api/class-api-properties.php';
        API_Properties::register_routes();

        // Agents API routes.
        require_once RECRM_PATH . 'includes/api/class-api-agents.php';
        API_Agents::register_routes();

        // Leads API routes.
        require_once RECRM_PATH . 'includes/api/class-api-leads.php';
        API_Leads::register_routes();
    }
}
