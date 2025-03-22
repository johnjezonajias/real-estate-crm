<?php
namespace Real_Estate_CRM\Lead;

defined( 'ABSPATH' ) || exit;

class Lead_CPT {
    public static function init() {
        add_action( 'init', [__CLASS__, 'register_lead_cpt'] );
    }

    public static function register_lead_cpt() {
        $labels = [
            'name'            => __('Leads', 'real-estate-crm'),
            'singular_name'   => __('Lead', 'real-estate-crm'),
            'menu_name'       => __('Leads', 'real-estate-crm'),
            'add_new'         => __('Add Lead', 'real-estate-crm'),
            'add_new_item'    => __('Add New Lead', 'real-estate-crm'),
            'edit_item'       => __('Edit Lead', 'real-estate-crm'),
            'new_item'        => __('New Lead', 'real-estate-crm'),
            'view_item'       => __('View Lead', 'real-estate-crm'),
            'search_items'    => __('Search Leads', 'real-estate-crm'),
        ];

        $args = [
            'labels'          => $labels,
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'menu_position'   => 25,
            'menu_icon'       => 'dashicons-welcome-view-site',
            'supports'        => ['title'],
            'taxonomies'      => ['lead_status'],
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => false,
            'show_in_rest'    => true,
        ];

        register_post_type( 'lead', $args );
    }
}
