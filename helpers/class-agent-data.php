<?php
namespace Real_Estate_CRM\Helpers;

use Real_Estate_CRM\Models\Property_Agent_Manager;

class Agent_Data {
    public static function prepare_agent_data( $agent ) {
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
            'agent_rating',
        ];

        $data = [
            'id'          => ( int ) $agent->ID,
            'title'       => $agent->post_title,
            'permalink'   => get_permalink( $agent->ID ),
        ];

        // Fetch featured image.
        $featured_image_id = ( int ) get_post_thumbnail_id( $agent->ID );
        $featrued_image_url = $featured_image_id ? wp_get_attachment_image_url( $featured_image_id, 'thumbnail' ) : null;

        $data['featured_image'] = [
            'id'  => ( int ) $featured_image_id,
            'url' => $featrued_image_url,
        ];

        // Fetch meta fields.
        foreach ( $meta_fields as $field ) {
            $meta_value = get_post_meta( $agent->ID, "_$field", true );
            $data[ $field ] = is_numeric( $meta_value ) ? ( int ) $meta_value : $meta_value;
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

        // Fetch properties assigned to agent.
        $data['agent_properties'] = Property_Agent_Manager::get_properties_for_agent( $agent->ID );

        return $data;
    }
}
