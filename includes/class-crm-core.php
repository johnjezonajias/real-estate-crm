<?php
namespace Real_Estate_CRM;

use Real_Estate_CRM\Property\Property_Init;
use Real_Estate_CRM\API\API_Init;

defined( 'ABSPATH' ) || exit;

class Core {
    public static function init() {
        // Initialize property modules.
        require_once RECRM_PATH . 'includes/property/class-property-init.php';
        Property_Init::init();

        // Load REST API classes.
        require_once RECRM_PATH . 'includes/api/class-api-init.php';
        API_Init::init();
    }
}
