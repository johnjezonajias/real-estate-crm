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

        register_rest_route( 'real-estate-crm/v1', '/properties', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'create_property'],
            'permission_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
            'args' => [
                'title'  => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );

        register_rest_route( 'real-estate-crm/v1', '/properties/bulk', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'create_multiple_properties'],
            'permission_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        ] );        
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

    public static function create_property( $request ) {
        $params = $request->get_params();
    
        if ( isset( $params[0] ) ) {
            return new \WP_Error( 'invalid_request', 'Use the /properties/bulk endpoint for multiple properties.', [ 'status' => 400 ] );
        }
    
        $response = self::insert_property( $params );
    
        if ( is_wp_error( $response ) ) {
            return $response;
        }
    
        return rest_ensure_response( [
            'success'  => true,
            'message'  => 'Property added successfully.',
            'count'    => 1,
            'property' => $response
        ] );
    }    

    public static function create_multiple_properties( $request ) {
        $params = $request->get_params();
    
        if ( ! is_array( $params ) || empty( $params ) ) {
            return new \WP_Error( 'invalid_data', 'Invalid property data.', [ 'status' => 400 ] );
        }
    
        $added_count = 0;
        $failed_count = 0;
        $added_properties = [];
    
        foreach ( $params as $property ) {
            $response = self::insert_property( $property );
    
            if ( is_wp_error( $response ) ) {
                $failed_count++;
                continue;
            }
    
            $added_properties[] = $response;
            $added_count++;
        }
    
        return rest_ensure_response( [
            'success'   => true,
            'message'   => "{$added_count} properties added successfully.",
            'count'     => $added_count,
            'failed'    => $failed_count,
            'properties' => $added_properties
        ] );
    }

    private static function insert_property( $params ) {
        if ( empty( $params['title'] ) ) {
            return new \WP_Error( 'missing_title', 'Property title is required.', [ 'status' => 400 ] );
        }
    
        $post_id = wp_insert_post(
            [
                'post_title'  => sanitize_text_field( $params['title'] ),
                'post_type'   => 'property',
                'post_status' => 'publish',
            ]
        );
    
        if ( is_wp_error( $post_id ) ) {
            return new \WP_Error( 'insert_failed', 'Failed to create property.', [ 'status' => 500 ] );
        }
    
        if ( isset( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $key => $value ) {
                update_post_meta( $post_id, "_$key", sanitize_text_field( $value ) );
            }
        }
    
        if ( isset( $params['gallery'] ) && is_array( $params['gallery'] ) ) {
            $gallery_ids = array_map( 'intval', $params['gallery'] );
            update_post_meta( $post_id, '_property_gallery', implode( ',', $gallery_ids ) );
        }
    
        if ( isset( $params['taxonomies'] ) && is_array( $params['taxonomies'] ) ) {
            foreach ( $params['taxonomies'] as $taxonomy => $terms ) {
                if ( taxonomy_exists( $taxonomy ) ) {
                    wp_set_object_terms( $post_id, array_map( 'sanitize_text_field', $terms ), $taxonomy );
                }
            }
        }
    
        return self::prepare_property_data( get_post( $post_id ) );
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
