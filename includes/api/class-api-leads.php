<?php
namespace Real_Estate_CRM\API;

require_once RECRM_PATH . 'helpers/class-get-leads.php';
use Real_Estate_CRM\Helpers\Get_Leads;

defined( 'ABSPATH' ) || exit;

class API_Leads {
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        register_rest_route( 'real-estate-crm/v1', '/leads', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_leads' ],
            'permission_callback' => [ __CLASS__, 'validate_permission' ],
        ] );
    }

    public static function get_leads( $request ) {

        $leads = Get_Leads::get_leads();

        return rest_ensure_response( $leads );
    }

    public static function validate_permission( $request ) {
        require_once RECRM_PATH . 'includes/api/class-api-authenticator.php';

        $authentication_result = API_Authenticator::validate_api_key( $request );

        if ( is_wp_error( $authentication_result ) ) {
            return $authentication_result;
        }

        // Check if the user has the required capabilities.
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'delete_posts' ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to see any leads.', 'real-estate-crm' ),
                [ 'status' => 403 ]
            );
        }

        return true;
    }
}