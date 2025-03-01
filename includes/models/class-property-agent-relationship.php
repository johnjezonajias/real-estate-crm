<?php
namespace Real_Estate_CRM\Models;

defined( 'ABSPATH' ) || exit;

class Property_Agent_Relationship {
    private static $table_name = 'property_agents';

    public static function create_relationship_table() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'property_agents';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            property_id BIGINT(20) UNSIGNED NOT NULL,
            agent_id BIGINT(20) UNSIGNED NOT NULL,
            assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY property_agent (property_id, agent_id),
            INDEX idx_property (property_id),
            INDEX idx_agent (agent_id),
            FOREIGN KEY (property_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE,
            FOREIGN KEY (agent_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
