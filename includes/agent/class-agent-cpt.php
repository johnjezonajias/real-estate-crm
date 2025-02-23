<?php
namespace Real_Estate_CRM\Agent;

defined( 'ABSPATH' ) || exit;

class Agent_CPT {
    public static function init() {
        add_action( 'init', [__CLASS__, 'register_agent_cpt'] );
    }

    public static function register_agent_cpt() {
        $labels = [
            'name'               => __( 'Agents', 'real-estate-crm' ),
            'singular_name'      => __( 'Agent', 'real-estate-crm' ),
            'menu_name'          => __( 'Agents', 'real-estate-crm' ),
            'add_new'            => __( 'Add New Agent', 'real-estate-crm' ),
            'add_new_item'       => __( 'Add New Agent', 'real-estate-crm' ),
            'edit_item'          => __( 'Edit Agent', 'real-estate-crm' ),
            'new_item'           => __( 'New Agent', 'real-estate-crm' ),
            'view_item'          => __( 'View Agent', 'real-estate-crm' ),
            'search_items'       => __( 'Search Agents', 'real-estate-crm' ),
        ];

        $args = [
            'labels'             => $labels,
            'label'              => __( 'Agents', 'real-estate-crm' ),
            'public'             => true,
            'supports'           => ['title', 'thumbnail'],
            'taxonomies'         => ['agent_type', 'agent_status'],
            'menu_icon'          => 'dashicons-businessman',
            'rewrite'            => ['slug' => 'agents'],
            'has_archive'        => true,
            'show_in_rest'       => true,
        ];

        register_post_type( 'agent', $args );
    }
}