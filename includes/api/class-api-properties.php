<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Properties {
    public static function register_routes() {
        register_rest_route( 'real-estate-crm/v1', '/properties', 
            [
                'methods'               => 'GET',
                'callback'              => [ __CLASS__, 'get_properties' ],
                'permission_callback'   => '__return_true',
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties/(?P<id>\d+)', 
            [
                'methods'               => 'GET',
                'callback'              => [ __CLASS__, 'get_property' ],
                'permission_callback'   => '__return_true',
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties',
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'create_property' ],
                'permission_callback' => [ __CLASS__, 'validate_permission' ],
                'args' => [
                    'title'  => [
                        'required' => true,
                        'type'     => 'string',
                    ],
                ],
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties/bulk',
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'create_multiple_properties' ],
                'permission_callback' => [ __CLASS__, 'validate_permission' ],
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties/(?P<id>\d+)',
            [
                'methods'             => 'PUT, PATCH',
                'callback'            => [ __CLASS__, 'update_property' ],
                'permission_callback' => [ __CLASS__, 'validate_permission' ],
                'args' => [
                    'title' => [
                        'type'     => 'string',
                        'required' => false,
                    ],
                ],
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/properties/(?P<id>\d+)',
            [
                'methods'             => 'DELETE',
                'callback'            => [ __CLASS__, 'delete_property' ],
                'permission_callback' => [  __CLASS__, 'validate_permission' ],
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
        if ( ! $property_id ) {
            return new \WP_Error( 'no_property', 'Invalid property ID.', [ 'status' => 404 ] );
        }

        $property = get_post( $property_id );
        if ( ! $property || $property->post_type !== 'property' ) {
            return new \WP_Error( 'not_found', 'Property not found.', [ 'status' => 404 ] );
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
    
        return rest_ensure_response(
            [
                'success'  => true,
                'message'  => 'Property added successfully.',
                'count'    => 1,
                'property' => $response
            ] 
        );
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
    
        return rest_ensure_response(
            [
                'success'    => true,
                'message'    => "{$added_count} properties added successfully.",
                'count'      => $added_count,
                'failed'     => $failed_count,
                'properties' => $added_properties
            ]
        );
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

        if ( isset( $params['featured_image'] ) && is_numeric( $params['featured_image'] ) ) {
            set_post_thumbnail( $post_id, (int) $params['featured_image'] );
        }
    
        if ( isset( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $key => $value ) {
                update_post_meta( $post_id, "_$key", sanitize_text_field( $value ) );
            }
        }
    
        if ( isset( $params['meta']['property_gallery'] ) && is_array( $params['meta']['property_gallery'] ) ) {
            $gallery_ids = array_map( 'intval', $params['meta']['property_gallery'] );
            update_post_meta( $post_id, '_property_gallery', $gallery_ids );
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

    public static function update_property( $request ) {
        $property_id = (int) $request['id'];
    
        if ( ! get_post( $property_id ) || get_post_type( $property_id ) !== 'property' ) {
            return new \WP_Error( 'not_found', 'Property not found.', [ 'status' => 404 ] );
        }
    
        $params = $request->get_params();
        $update_data = [ 'ID' => $property_id ];
    
        if ( isset( $params['title'] ) ) {
            $update_data['post_title'] = sanitize_text_field( $params['title'] );
        }
    
        // Update post data.
        if ( count( $update_data ) > 1 ) {
            $result = wp_update_post( $update_data, true );
            if ( is_wp_error( $result ) ) {
                return new \WP_Error( 'update_failed', 'Failed to update property.', [ 'status' => 500 ] );
            }
        }
    
        // Update specific meta fields with validation.
        if ( ! empty( $params['meta'] ) && is_array( $params['meta'] ) ) {
            $existing_meta_keys = array_keys( get_post_meta( $property_id ) );

            foreach ( $params['meta'] as $key => $value ) {
                $meta_key = "_{$key}";

                if ( $key === 'property_gallery' ) {
                    if ( is_string( $value ) ) {
                        $value = array_map( 'intval', explode( ',', $value ) );
                    } elseif ( is_array( $value ) ) {
                        $value = array_map( 'intval', $value );
                    } else {
                        continue;
                    }
                
                    $existing_gallery = get_post_meta( $property_id, $meta_key, true );
                
                    if ( ! is_array( $existing_gallery ) ) {
                        $existing_gallery = [];
                    }
                
                    // Merge new images with existing ones and remove duplicates.
                    $updated_gallery = array_unique( array_merge( $existing_gallery, $value ) );
                
                    update_post_meta( $property_id, $meta_key, $updated_gallery );
                }
            }
        }

        // Update featured image.
        if ( isset( $params['featured_image'] ) && is_numeric( $params['featured_image'] ) ) {
            set_post_thumbnail( $property_id, (int) $params['featured_image'] );
        }
    
        // Update taxonomies.
        if ( isset( $params['taxonomies'] ) && is_array( $params['taxonomies'] ) ) {
            foreach ( $params['taxonomies'] as $taxonomy => $terms ) {
                if ( taxonomy_exists( $taxonomy ) ) {
                    wp_set_object_terms( $property_id, array_map( 'sanitize_text_field', $terms ), $taxonomy );
                }
            }
        }
    
        return rest_ensure_response(
            [
                'success'  => true,
                'message'  => sprintf( 'Property with ID:%d updated successfully.', $property_id ),
                'property' => self::prepare_property_data( get_post( $property_id ) ),
            ]
        );
    }

    public static function delete_property( $request ) {
        $property_id = (int) $request['id'];
    
        $property = get_post( $property_id );
        if ( ! $property || $property->post_type !== 'property' ) {
            return new \WP_Error( 'not_found', 'Property not found.', [ 'status' => 404 ] );
        }
    
        $deleted = wp_delete_post( $property_id, true );
    
        if ( ! $deleted ) {
            return new \WP_Error( 'delete_failed', 'Failed to delete property.', [ 'status' => 500 ] );
        }
    
        return rest_ensure_response(
            [
                'success' => true,
                'message' => sprintf( 'Property with ID:%d is deleted successfully.', $property_id ),
            ]
        );
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

        // Fetch featured image.
        $featured_image_id = get_post_thumbnail_id( $property->ID );
        $featured_image_url = $featured_image_id ? wp_get_attachment_url( $featured_image_id ) : null;

        $data['featured_image'] = [
            'id'  => $featured_image_id,
            'url' => $featured_image_url,
        ];
    
        // Fetch meta fields.
        foreach ( $meta_fields as $field ) {
            $data[ $field ] = get_post_meta( $property->ID, "_$field", true );
        }
    
        // Fetch taxonomies.
        $taxonomies = get_object_taxonomies( 'property', 'names' );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $property->ID, $taxonomy );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $data[ $taxonomy ] = wp_list_pluck( $terms, 'name' );
            } else {
                $data[ $taxonomy ] = [];
            }
        }

        // Fetch image gallery.
        $gallery = get_post_meta( $property->ID, '_property_gallery', true );
        $gallery_ids = is_array( $gallery ) ? $gallery : ( ! empty( $gallery ) ? explode( ',', $gallery ) : [] );

        $data['property_gallery'] = array_map( function ( $id ) {
            return [
                'id'  => $id,
                'url' => wp_get_attachment_image_url( $id, 'large' )
            ];
        }, $gallery_ids );
    
        return $data;
    }

    public static function validate_permission( $request ) {
        require_once RECRM_PATH . 'includes/api/class-api-authenticator.php';

        $authentication_result = API_Authenticator::validate_api_key( $request );

        if ( is_wp_error( $authentication_result ) ) {
            return $authentication_result;
        }

        // Check if the user has the required capabilities.
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'delete_posts' ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to edit or delete agents.', 'real-estate-crm' ),
                [ 'status' => 403 ]
            );
        }

        return true;
    }
}
