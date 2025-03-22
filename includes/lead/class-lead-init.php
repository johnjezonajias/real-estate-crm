<?php
namespace Real_Estate_CRM\Lead;

defined( 'ABSPATH' ) || exit;

class Lead_Init {
    public static function init() {
        require_once RECRM_PATH . 'includes/lead/class-lead-cpt.php';
        require_once RECRM_PATH . 'includes/lead/class-lead-meta.php';
        require_once RECRM_PATH . 'includes/lead/class-lead-taxonomies.php';

        Lead_CPT::init();
        Lead_Meta::init();
        Lead_Taxonomies::init();
    }
}
