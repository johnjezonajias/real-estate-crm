<?php
namespace Real_Estate_CRM;

use Real_Estate_CRM\Property\Property_Init;
use Real_Estate_CRM\API\API_Init;
use Real_Estate_CRM\API\API_Key_Manager;

defined( 'ABSPATH' ) || exit;

class Core {
    public static function init() {
        // Initial API authentication.
        require_once RECRM_PATH . 'includes/api/class-api-key-manager.php';
        API_Key_Manager::init();

        // Initial and Load REST API classes.
        require_once RECRM_PATH . 'includes/api/class-api-init.php';
        API_Init::init();

        // Initialize property modules.
        require_once RECRM_PATH . 'includes/property/class-property-init.php';
        Property_Init::init();

        // Initialize agent modules.
        require_once RECRM_PATH . 'includes/agent/class-agent-init.php';
        Agent\Agent_Init::init();
    }
}
