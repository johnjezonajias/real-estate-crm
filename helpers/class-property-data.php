<?php

namespace Real_Estate_CRM\Helpers;

class Property_Data {
    public static function prepare_property_data( $property ) {
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
                'id'  => ( int ) $id,
                'url' => wp_get_attachment_image_url( $id, 'large' )
            ];
        }, $gallery_ids );
    
        return $data;
    }
}
