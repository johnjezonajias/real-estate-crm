<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Authenticator {
    public static function validate_api_key( $request ) {
        $provided_key = $request->get_header( 'X-API-KEY' );
        $stored_key   = get_option( 'real_estate_crm_api_key' );

        // Check if API key is missing.
        if ( empty( $provided_key ) ) {
            return new \WP_Error( 'rest_missing_api_key', __( 'API Key is required.', 'real-estate-crm' ), [ 'status' => 401 ] );
        }

        // Check if API key is valid.
        if ( $provided_key !== $stored_key ) {
            return new \WP_Error( 'rest_invalid_api_key', __( 'Invalid API Key.', 'real-estate-crm' ), [ 'status' => 403 ] );
        }

        return true;
    }
}
