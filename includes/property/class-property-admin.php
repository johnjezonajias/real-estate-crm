<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_Admin {
    public static function init() {
        add_filter( 'manage_property_posts_columns', [__CLASS__, 'add_custom_columns'] );
        add_action( 'manage_property_posts_custom_column', [__CLASS__, 'custom_column_content'], 10, 2 );
    }

    public static function add_custom_columns( $columns ) {
        $ordered_columns = [
            'cb'                   => $columns['cb'],
            'title'                => $columns['title'],
            'developer-name'       => __( 'Developer', 'real-estate-crm' ),
            'listing-price'        => __( 'Price (PHP)', 'real-estate-crm' ),
            'availability-status'  => __( 'Availability', 'real-estate-crm' ),
            'date'                 => $columns['date'],
        ];

        return $ordered_columns;
    }

    public static function custom_column_content( $column, $post_id ) {
        if ( $column == 'developer-name' ) {
            echo get_post_meta( $post_id, '_developer_name', true ) ?: '--';
        }

        if ( $column == 'listing-price' ) {
            $price = get_post_meta( $post_id, '_listing_price', true );
            $price = is_numeric($price) ? number_format( ( float ) $price, 2 ) : '0.00';
            echo $price;
        }

        if ( $column == 'availability-status' ) {
            echo get_post_meta( $post_id, '_availability_status', true ) ?: '--';
        }
    }
}
