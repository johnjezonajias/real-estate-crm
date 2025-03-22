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
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );                
    }
}