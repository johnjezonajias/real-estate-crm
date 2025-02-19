<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_Init {
    public static function init() {
        require_once RECRM_PATH . 'includes/property/class-property-cpt.php';
        require_once RECRM_PATH . 'includes/property/class-property-meta.php';
        require_once RECRM_PATH . 'includes/property/class-property-taxonomies.php';
        require_once RECRM_PATH . 'includes/property/class-property-admin.php';

        Property_CPT::init();
        Property_Meta::init();
        Property_Taxonomies::init();
        Property_Admin::init();
    }
}