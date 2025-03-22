<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_Taxonomies {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 11 );
    }

    public static function register_taxonomies() {
        register_taxonomy( 'property_type', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Property Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Type', 'real-estate-crm' ),
                    'search_items'  => __( 'Search Property Type', 'real-estate-crm' ),
                    'all_items'     => __( 'All Property Types', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Property Type', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Property Type', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Property Type', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Property Type Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Property Types', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'property_status', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Property Status', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Status', 'real-estate-crm' ),
                    'all_items'     => __( 'All Property Status', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Property Status', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Property Status', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Property Status', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Property Status Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Property Status', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'zoning_type', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Zoning Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Zoning Type', 'real-estate-crm' ),
                    'all_items'     => __( 'All Zoning Types', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Zoning Type', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Zoning Type', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Zoning Type', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Zoning Type Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Zoning Types', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );
    }
}
