<?php
namespace Real_Estate_CRM\Lead;

defined( 'ABSPATH' ) || exit;

class Lead_Admin {
    public static function init() {
        add_filter( 'manage_lead_posts_columns', [__CLASS__, 'add_custom_columns'] );
        add_action( 'manage_lead_posts_custom_column', [__CLASS__, 'custom_column_content'], 10, 2 );
    }

    public static function add_custom_columns( $columns ) {
        $ordered_columns = [
            'cb'                   => $columns['cb'],
            'title'                => $columns['title'],
            'lead-property'        => __( 'Property', 'real-estate-crm' ),
            'lead-agent'           => __( 'Agent', 'real-estate-crm' ),
            'taxonomy-lead_status' => $columns['taxonomy-lead_status'] ?? __( 'Status', 'real-estate-crm' ),
            'date'                 => $columns['date'],
        ];
    
        return $ordered_columns;
    }

    public static function custom_column_content( $column, $post_id ) {
        if ( $column == 'lead-property' ) {
            $property_id    = get_post_meta( $post_id, '_lead_property', true );
            $property_title = $property_id ? get_the_title( $property_id ) : '--';

            echo esc_html( $property_title );
        }
    
        if ( $column == 'lead-agent' ) {
            $agent_id   = get_post_meta( $post_id, '_lead_agent', true );
            $agent_name = $agent_id ? get_the_title( $agent_id ) : '--';

            echo esc_html( $agent_name );
        }
    }
}