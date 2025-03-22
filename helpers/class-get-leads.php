<?php
namespace Real_Estate_CRM\Helpers;

require_once RECRM_PATH . 'helpers/class-lead-data.php';
use Real_Estate_CRM\Helpers\Lead_Data;

defined ( 'ABSPATH' ) || exit;

class Get_Leads {
    public static function get_leads() {
        $leads = get_posts(
            [
                'post_type'      => 'lead',
                'posts_per_page' => -1,
                'post_status'    => 'publish'
            ]
        );

        $data = [];
        foreach ( $leads as $lead ) {
            $data[] = Lead_Data::prepare_lead_data( $lead );
        }

        return $data;
    }
}