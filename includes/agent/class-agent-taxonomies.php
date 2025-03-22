<?php
namespace Real_Estate_CRM\Agent;

defined( 'ABSPATH' ) || exit;

class Agent_Taxonomies {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 11 );
        add_action( 'init', [ __CLASS__, 'register_default_terms' ], 12 );
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

    public static function register_default_terms() {
        $taxonomies = [
            'agent_type'   => [
                'sales-agent' => [
                    'name'        => __( 'Sales Agent', 'real-estate-crm' ),
                    'description' => __( 'A real estate professional who assists clients in buying, selling, or renting properties.', 'real-estate-crm' ),
                ],
                'broker' => [
                    'name'        => __( 'Broker', 'real-estate-crm' ),
                    'description' => __( 'A licensed real estate professional authorized to oversee transactions and manage agents.', 'real-estate-crm' ),
                ],
            ],
            'agent_status' => [
                'active' => [
                    'name'        => __( 'Active', 'real-estate-crm' ),
                    'description' => __( 'Currently engaged in real estate activities and available for clients.', 'real-estate-crm' ),
                ],
                'inactive' => [
                    'name'        => __( 'Inactive', 'real-estate-crm' ),
                    'description' => __( 'Not currently working in real estate but may return.', 'real-estate-crm' ),
                ],
                'suspended' => [
                    'name'        => __( 'Suspended', 'real-estate-crm' ),
                    'description' => __( 'Temporarily prohibited from practicing due to regulatory or policy violations.', 'real-estate-crm' ),
                ],
                'dismissed' => [
                    'name'        => __( 'Dismissed', 'real-estate-crm' ),
                    'description' => __( 'Permanently removed from their role due to misconduct or other reasons.', 'real-estate-crm' ),
                ],
            ],
        ];

        foreach ( $taxonomies as $taxonomy => $terms ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }
    
            foreach ( $terms as $slug => $term_data ) {
                $existing_term = get_term_by( 'slug', $slug, $taxonomy );
    
                if ( ! $existing_term ) {
                    wp_insert_term(
                        $term_data['name'],
                        $taxonomy,
                        [
                            'slug'        => sanitize_title( $slug ),
                            'description' => sanitize_text_field( $term_data['description'] ),
                        ]
                    );
                } elseif ( $existing_term && empty( $existing_term->description ) ) {
                    wp_update_term(
                        $existing_term->term_id,
                        $taxonomy,
                        [
                            'description' => sanitize_text_field( $term_data['description'] ),
                        ]
                    );
                }
            }
        }
    }
}