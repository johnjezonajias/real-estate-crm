<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Agents {
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        register_rest_route( 'real-estate-crm/v1', '/agents', 
            [
                'methods'               => 'GET',
                'callback'              => [ __CLASS__, 'get_agents' ],
                'permission_callback'   => '__return_true',
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/agents/(?P<id>\d+)', 
            [
                'methods'               => 'GET',
                'callback'              => [ __CLASS__, 'get_agent' ],
                'permission_callback'   => '__return_true',
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/agents', 
            [
                'methods'               => 'POST',
                'callback'              => [ __CLASS__, 'create_agent' ],
                'permission_callback'   => function() {
                    return current_user_can( 'edit_posts' );
                },
                'args'  => [
                    'title' => [
                        'required' => true,
                        'type'     => 'string',
                    ],
                ],
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/agents/bulk', 
            [
                'methods'               => 'POST',
                'callback'              => [ __CLASS__, 'create_multiple_agents' ],
                'permission_callback'   => function() {
                    return current_user_can( 'edit_posts' );
                },
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/agents/(?P<id>\d+)', 
            [
                'methods'               => 'PUT, PATCH',
                'callback'              => [ __CLASS__, 'update_agent' ],
                'permission_callback'   => function() {
                    return current_user_can( 'edit_posts' );
                },
                'args'  => [
                    'title' => [
                        'required' => true,
                        'type'     => 'string',
                    ],
                ],
            ]
        );

        register_rest_route( 'real-estate-crm/v1', '/agents/(?P<id>\d+)', 
            [
                'methods'               => 'DELETE',
                'callback'              => [ __CLASS__, 'delete_agent' ],
                'permission_callback'   => function() {
                    return current_user_can( 'delete_posts' );
                },
            ]
        );
    }

    public static function get_agents( $request ) {
        $args = [
            'post_type'      => 'agent',
            'posts_per_page' => -1,
        ];

        $agents = get_posts( $args );
        $data = [];

        foreach ( $agents as $agent ) {
            $data[] = self::prepare_agent_data( $agent );
        }

        return rest_ensure_response( $data );
    }

    public static function get_agent( $request ) {
        $agent_id = (int) $request['id'];
        if ( ! $agent_id ) {
            return new \WP_Error( 'no_agent', 'No agent found with that ID', [ 'status' => 404 ] );
        }

        $agent = get_post( $agent_id );
        if ( ! $agent || $agent->post_type !== 'agent' ) {
            return new \WP_Error( 'not_found', 'Agent not found.', [ 'status' => 404 ] );
        }

        return rest_ensure_response( self::prepare_agent_data( $agent ) );
    }

    public static function create_agent( $request ) {
        $params = $request->get_params();

        if ( isset( $params[0] ) ) {
            return new \WP_Error( 'invalid_params', 'Invalid parameters', [ 'status' => 400 ] );
        }

        $response = self::insert_agent( $params );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return rest_ensure_response( 
            [
                'success'   => true,
                'message'   => 'Agent created successfully',
                'count'     => 1,
                'agent'     => $response,
            ]
        );
    }

    public static function create_multiple_agents( $request ) {
        $params = $request->get_params();

        if ( empty( $params ) || !is_array( $params ) ) {
            return new \WP_Error( 'invalid_params', 'Invalid parameters', [ 'status' => 400 ] );
        }

        $responses = [];
        foreach ( $params as $agent ) {
            $responses[] = self::insert_agent( $agent );
        }

        return rest_ensure_response( 
            [
                'success'   => true,
                'message'   => 'Agents created successfully',
                'count'     => count( $responses ),
                'agents'    => $responses,
            ]
        );
    }

    public static function update_agent( $request ) {
        $agent_id = (int) $request['id'];

        if ( ! get_post( $agent_id ) || get_post_type( $agent_id ) !== 'agent' ) {
            return new \WP_Error( 'not_found', 'Agent not found.', [ 'status' => 404 ] );

        }

        $params = $request->get_params();
        $update_data = [ 'ID' => $agent_id] ;

        if ( isset( $params['title'] ) ) {
            $update_data['post_title'] = sanitize_text_field( $params['title'] );
        }

        // Update post data.
        if ( count( $update_data ) > 1 ) {
            $result = wp_update_post( $update_data, true );
            if ( is_wp_error( $result ) ) {
                return new \WP_Error( 'could_not_update', 'Could not update agent', [ 'status' => 500 ] );
            }

        }

        // Update meta fields.
        if ( ! empty( $params['meta'] ) || is_array( $params['meta'] ) ) {
            $existing_meta_keys = array_keys( get_post_meta( $agent_id ) );

            foreach ( $params['meta'] as $key => $value ) {
                $meta_key = "_{$key}";

                if ( in_array( $meta_key, $existing_meta_keys, true ) ) {
                    update_post_meta( $agent_id, $meta_key, sanitize_text_field( $value ) );
                }
            }
        }

        // Update featured image.
        if ( isset( $params['featured_image'] ) && is_numeric( $params['featured_image'] ) ) {
            set_post_thumbnail( $agent_id, (int) $params['featured_image'] );
        }

        // Update taxonomies.
        if ( isset( $params['taxonomies'] ) && is_array( $params['taxonomies'] ) ) {
            foreach ( $params['taxonomies'] as $taxonomy => $terms ) {
                if ( taxonomy_exists( $taxonomy ) ) {
                    wp_set_object_terms( $agent_id, array_map( 'sanitize_text_field', $terms ), $taxonomy );
                }
            }
        }

        return rest_ensure_response(
            [
                'success'   => true,
                'message'   => sprintf( 'Agent %d updated successfully', $agent_id ),
                'agent'     => self::prepare_agent_data( get_post( $agent_id ) ),
            ]
        );
    }

    public static function delete_agent( $request ) {
        $agent_id = (int) $request['id'];

        if ( ! get_post( $agent_id ) || get_post_type( $agent_id ) !== 'agent' ) {
            return new \WP_Error( 'not_found', 'Agent not found.', [ 'status' => 404 ] );
        }

        $deleted = wp_delete_post( $agent_id, true );

        if ( ! $deleted ) {
            return new \WP_Error( 'delete_failed', 'Failed to delete agent', [ 'status' => 500 ] );
        }

        return rest_ensure_response(
            [
                'success'   => true,
                'message'   => sprintf( 'Agent %d deleted successfully', $agent_id ),
            ]
        );
    }

    private static function insert_agent( $params ) {
        if ( empty( $params['title'] ) ) {
            return new \WP_Error( 'missing_title', 'Agent title is required.', [ 'status' => 400 ] );
        }
    
        $agent_id = wp_insert_post(
            [
                'post_title'   => sanitize_text_field( $params['title'] ),
                'post_type'    => 'agent',
                'post_status'  => 'publish',
            ]
        );
    
        if ( is_wp_error( $agent_id ) ) {
            return new \WP_Error( 'could_not_create', 'Could not create agent', [ 'status' => 500 ] );
        }
    
        if ( isset( $params['featured_image'] ) && is_numeric( $params['featured_image'] ) ) {
            set_post_thumbnail( $agent_id, (int) $params['featured_image'] );
        }
    
        if ( isset( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $key => $value ) {
                update_post_meta( $agent_id, "_$key", sanitize_text_field( $value ) );
            }
        }
    
        if ( isset( $params['taxonomies'] ) && is_array( $params['taxonomies'] ) ) {
            foreach ( $params['taxonomies'] as $taxonomy => $terms ) {
                if ( taxonomy_exists( $taxonomy ) ) {
                    wp_set_object_terms( $agent_id, array_map( 'sanitize_text_field', $terms ), $taxonomy );
                }
            }
        }
    
        return self::prepare_agent_data( get_post( $agent_id ) );
    }    

    private static function prepare_agent_data( $agent ) {
        $meta_fields = [
            'agent_display_name',
            'agent_bio',
            'agent_facebook',
            'agent_twitter',
            'agent_linkedin',
            'agent_instagram',
            'agent_license',
            'agent_agency',
            'agent_experience',
            'agent_specialties',
            'agent_phone',
            'agent_email',
            'agent_whatsapp',
            'agent_availability',
            'agent_rating',
        ];

        $data = [
            'id'          => $agent->ID,
            'title'       => $agent->post_title,
            'permalink'   => get_permalink( $agent->ID ),
        ];

        // Fetch featured image.
        $featured_image_id = get_post_thumbnail_id( $agent->ID );
        $featrued_image_url = $featured_image_id ? wp_get_attachment_image_url( $featured_image_id, 'thumbnail' ) : null;

        $data['featured_image'] = [
            'id'  => $featured_image_id,
            'url' => $featrued_image_url,
        ];

        // Fetch meta fields.
        foreach ( $meta_fields as $field ) {
            $data[ $field ] = get_post_meta( $agent->ID, "_$field", true );
        }

        // Fetch taxonomies.
        $taxonomies = get_object_taxonomies( 'agent', 'names' );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $agent->ID, $taxonomy );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $data[ $taxonomy ] = wp_list_pluck( $terms, 'name' );
            } else {
                $data[ $taxonomy ] = [];
            }
        }

        return $data;
    }
}
