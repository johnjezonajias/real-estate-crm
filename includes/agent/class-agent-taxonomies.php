<?php
namespace Real_Estate_CRM\Agent;

defined( 'ABSPATH' ) || exit;

class Agent_Taxonomies {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 11 );
    }

    public static function register_taxonomies() {
        register_taxonomy( 'agent_type', 'agent',
            [
                'labels'            => [
                    'name'          => __( 'Agent Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Agent Type', 'real-estate-crm' ),
                    'search_items'  => __( 'Search Agent Types', 'real-estate-crm' ),
                    'all_items'     => __( 'All Agent Types', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Agent Type', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Agent Types', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Agent Types', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Agent Type Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Agent Types', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'agent_status', 'agent',
            [
                'labels'            => [
                    'name'          => __( 'Agent Status', 'real-estate-crm' ),
                    'singular_name' => __( 'Agent Status', 'real-estate-crm' ),
                    'search_items'  => __( 'Search Agent Status', 'real-estate-crm' ),
                    'all_items'     => __( 'All Agent Statuses', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Agent Status', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Agent Status', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Agent Status', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Agent Status Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Agent Status', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );
    }
}