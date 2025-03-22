<?php
namespace Real_Estate_CRM;

use Real_Estate_CRM\Models\Property_Agent_Relationship;
use Real_Estate_CRM\API\API_Key_Manager;
use Real_Estate_CRM\API\API_Init;
use Real_Estate_CRM\Agent\Agent_Init;
use Real_Estate_CRM\Property\Property_Init;
use Real_Estate_CRM\Lead\Lead_Init;

defined( 'ABSPATH' ) || exit;

class CRM_Core {
    public static function init() {
        // Initial property/agent relation table.
        require_once RECRM_PATH . 'includes/models/class-property-agent-relationship.php';
        Property_Agent_Relationship::create_relationship_table();

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
        Agent_Init::init();

        // Initialize lead modules.
        require_once RECRM_PATH . 'includes/lead/class-lead-init.php';
        Lead_Init::init();
    }
}
