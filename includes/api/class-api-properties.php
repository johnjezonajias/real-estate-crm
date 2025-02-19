<?php
namespace Real_Estate_CRM\API;

use WP;

defined( 'ABSPATH' ) || exit;

class API_Properties {
    public static function register_routes() {
        register_rest_route( 'real-estate-crm/v1', '/properties', 
            [
                'methods'               => 'GET',
                'callback'              => [__CLASS__, 'get_properties'],
                'permission_callback'   => '__return_true',
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties/(?P<id>\d+)', 
            [
                'methods'               => 'GET',
                'callback'              => [__CLASS__, 'get_property'],
                'permission_callback'   => '__return_true',
            ]
        );
    }

    public static function get_properties( $request ) {
        $args = [
            'post_type'      => 'property',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $properties = get_posts( $args );
        $data = [];

        foreach ( $properties as $property ) {
            $data[] = self::prepare_property_data( $property );
        }

        return rest_ensure_response( $data );
    }

    public static function get_property( $request ) {
        $property_id = (int) $request['id'];
        if ( !$property_id ) {
            return new \WP_Error( 'no_property', 'Invalid property ID.', ['status' => 404] );
        }

        $property = get_post( $property_id );
        if ( !$property || $property->post_type !== 'property' ) {
            return new \WP_Error( 'not_found', 'Property not found.', ['status' => 404] );
        }

        return rest_ensure_response( self::prepare_property_data( $property ) );
    }

    private static function prepare_property_data( $property ) {
        $meta_fields = [
            'developer_name',
            'listing_price',
            'location',
            'address',
            'google_maps_link',
            'year_built',
            'availability_status',
            'lot_area',
            'floor_area',
            'bedrooms',
            'bathrooms',
            'carport',
            'garage_capacity',
            'furnishing_status',
            'office_space',
            'floor_number',
            'parking_slots',
            'building_amenities',
            'water_supply',
            'electricity_provider',
            'internet_connection',
            'security_features',
            'pet_friendly',
            'nearby_landmarks',
            'association_dues',
            'property_tax',
            'payment_options',
            'estimated_mortgage'
        ];
    
        $data = [
            'id'        => $property->ID,
            'title'     => $property->post_title,
            'permalink' => get_permalink( $property->ID ),
        ];
    
        // Fetch meta fields.
        foreach ( $meta_fields as $field ) {
            $data[$field] = get_post_meta( $property->ID, "_$field", true );
        }
    
        // Fetch taxonomies.
        $taxonomies = get_object_taxonomies( 'property', 'names' );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $property->ID, $taxonomy );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $data[$taxonomy] = wp_list_pluck( $terms, 'name' );
            } else {
                $data[$taxonomy] = [];
            }
        }

        // Fetch image gallery.
        $gallery = get_post_meta( $property->ID, '_property_gallery', true );
        $gallery_ids = ! empty( $gallery ) ? explode( ',', $gallery ) : [];

        $data['property_gallery'] = array_map( function ( $id ) {
            return [
                'id'  => $id,
                'url' => wp_get_attachment_image_url( $id, 'large' )
            ];
        }, $gallery_ids );
    
        return $data;
    }
}
