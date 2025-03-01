<?php
namespace Real_Estate_CRM\Models;

defined( 'ABSPATH' ) || exit;

class Property_Agent_Manager {
    private static $table_name = 'property_agents';

    public static function assign_agent_to_property( $property_id, $agent_id ) {
        global $wpdb;

        $table = $wpdb->prefix . self::$table_name;

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE property_id = %d AND agent_id = %d",
                $property_id,
                $agent_id
            )
        );

        if ( $exists ) {
            return new \WP_Error( 'already_assigned', 'Agent is already assigned.', [ 'status' => 400 ] );
        }

        $wpdb->insert( $table,
            [
                'property_id' => $property_id,
                'agent_id'    => $agent_id
            ]
        );

        return true;
    }

    public static function get_agents_for_property( $property_id ) {
        global $wpdb;

        $table = $wpdb->prefix . self::$table_name;
    
        // Get all agents assigned to this property.
        $agent_ids = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT agent_id FROM $table WHERE property_id = %d",
                $property_id
            ),
            ARRAY_A
        );
    
        $agents = array_map( function ( $agent ) {
            $agent_id = ( int ) $agent['agent_id'];
            return [
                'id'    => $agent_id,
                'name'  => get_the_title( $agent_id ),
                'url'   => get_permalink( $agent_id ) 
            ];
        }, $agent_ids );
    
        return $agents;
    }

    public static function get_properties_for_agent( $agent_id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;
    
        // Get all properties assigned to this agent.
        $property_ids = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT property_id FROM $table WHERE agent_id = %d",
                $agent_id
            ),
            ARRAY_A
        );

        $properties = array_map( function ( $property ) {
            $property_id = (int) $property['property_id'];
            return [
                'id'    => $property_id,
                'name'  => get_the_title( $property_id ),
                'url'   => get_permalink( $property_id )
            ];
        }, $property_ids );
    
        return $properties;
    }

    public static function remove_agent_from_property( $property_id, $agent_id ) {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $wpdb->delete( $table, [
            'property_id' => $property_id,
            'agent_id'    => $agent_id
        ]);

        return true;
    }
}
