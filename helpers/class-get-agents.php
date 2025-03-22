<?php
namespace Real_Estate_CRM\Helpers;

defined( 'ABSPATH' ) || exit;

class Get_Agents {
    public static function get_agents() {
        $agents = get_posts(
            [
                'post_type'      => 'agent',
                'posts_per_page' => -1,
                'post_status'    => 'publish'
            ]
        );

        $options = [];
        foreach ( $agents as $agent ) {
            $options[$agent->ID] = $agent->post_title;
        }

        return $options;
    }
}