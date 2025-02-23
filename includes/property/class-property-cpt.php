<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_CPT {
    public static function init() {
        add_action( 'init', [__CLASS__, 'register_property_cpt'] );
    }

    public static function register_property_cpt() {
        $labels = [
            'name'               => __( 'Properties', 'real-estate-crm' ),
            'singular_name'      => __( 'Property', 'real-estate-crm' ),
            'menu_name'          => __( 'Properties', 'real-estate-crm' ),
            'add_new'            => __( 'Add New Property', 'real-estate-crm' ),
            'add_new_item'       => __( 'Add New Property', 'real-estate-crm' ),
            'edit_item'          => __( 'Edit Property', 'real-estate-crm' ),
            'new_item'           => __( 'New Property', 'real-estate-crm' ),
            'view_item'          => __( 'View Property', 'real-estate-crm' ),
            'search_items'       => __( 'Search Properties', 'real-estate-crm' ),
        ];

        $args = [
            'labels'             => $labels,
            'label'              => __( 'Properties', 'real-estate-crm' ),
            'public'             => true,
            'supports'           => ['title', 'thumbnail'],
            'taxonomies'         => ['property_type', 'property_status', 'zoning_type'],
            'menu_icon'          => 'dashicons-admin-home',
            'rewrite'            => ['slug' => 'properties'],
            'has_archive'        => true,
            'show_in_rest'       => true,
        ];

        register_post_type( 'property', $args );
    }
}
