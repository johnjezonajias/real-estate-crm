<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_Taxonomies {
    public static function init() {
        add_action( 'init', [__CLASS__, 'register_taxonomies'], 11 );
    }

    public static function register_taxonomies() {
        register_taxonomy( 'property_type', 'property',
            [
                'labels'        => [
                    'name'          => __( 'Property Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Type', 'real-estate-crm' ),
                ],
                'public'        => true,
                'hierarchical'  => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'property_status', 'property',
            [
                'labels'        => [
                    'name'          => __( 'Property Status', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Status', 'real-estate-crm' ),
                ],
                'public'        => true,
                'hierarchical'  => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'zoning_type', 'property',
            [
                'labels'        => [
                    'name'          => __( 'Zoning Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Zoning Type', 'real-estate-crm' ),
                ],
                'public'        => true,
                'hierarchical'  => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );                 
    }
}
