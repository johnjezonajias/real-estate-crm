<?php
namespace Real_Estate_CRM\Helpers;

defined( 'ABSPATH' ) || exit;

class Lead_Data {
    public static function prepare_lead_data( $lead ) {
        $meta_fields = [
            'lead_name',
            'lead_email',
            'lead_phone',
            'lead_message',
            'lead_property_id',
            'lead_agent_id',
            'lead_status',
        ];

        $data = [
            'id'        => ( int ) $lead->ID,
            'title'     => $lead->post_title,
            'slug'      => get_post_field( 'post_name', $lead->ID ),
            'permalink' => get_permalink( $lead->ID ),
            'date'      => get_the_date( 'Y-m-d H:i:s', $lead->ID ),
            'status'    => get_post_status( $lead->ID ),
        ];

        // Fetch meta fields.
        foreach ( $meta_fields as $field ) {
            $meta_value = get_post_meta( $lead->ID, "_$field", true );
            $data[ $field ] = is_numeric( $meta_value ) ? ( int ) $meta_value : $meta_value;
        }

        return $data;
    }
}
