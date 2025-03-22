<?php
namespace Real_Estate_CRM\Helpers;

class Get_Properties {
    public static function get_properties() {
        $properties = get_posts(
            [
                'post_type'      => 'property',
                'posts_per_page' => -1,
                'post_status'    => 'publish'
            ]
        );

        $options = [];
        foreach ( $properties as $property ) {
            $options[$property->ID] = $property->post_title;
        }

        return $options;
    }
}
