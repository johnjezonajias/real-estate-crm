<?php
namespace Real_Estate_CRM\Lead;

defined( 'ABSPATH' ) || exit;

require_once RECRM_PATH . 'helpers/class-get-properties.php';
require_once RECRM_PATH . 'helpers/class-get-agents.php';

use Real_Estate_CRM\Helpers\Get_Properties;
use Real_Estate_CRM\Helpers\Get_Agents;

class Lead_Meta {
    private static $meta_fields = [];

    public static function init() {
        self::define_meta_fields();
        add_action( 'add_meta_boxes', [__CLASS__, 'register_meta_boxes'] );
        add_action( 'save_post', [__CLASS__, 'save_meta'] );
    }

    public static function define_meta_fields() {
        self::$meta_fields = [
            'Personal Information' => [
                'lead_name'     => ['label' => 'Name', 'type' => 'text'],
                'lead_email'    => ['label' => 'Email', 'type' => 'email'],
                'lead_phone'    => ['label' => 'Phone', 'type' => 'text'],
            ],
            'Property Information' => [
                'lead_property' => [
                    'label'   => 'Property of Interest',
                    'type'    => 'select',
                    'options' => Get_Properties::get_properties(),
                ],
            ],
            'Agent Information' => [
                'lead_agent'    => [
                    'label'   => 'Agent Assigned',
                    'type'    => 'select',
                    'options' => Get_Agents::get_agents(),
                ],
            ],
            'Notes' => [
                'lead_notes'    => ['label' => 'Lead Notes', 'type' => 'wysiwyg'],
            ],
        ];
    }

    public static function register_meta_boxes() {
        add_meta_box( 
            'lead_details', 
            __( 'Lead Details', 'real-estate-crm' ), 
            [__CLASS__, 'render_meta_box'], 
            'lead', 
            'normal', 
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'save_lead_meta', 'lead_meta_nonce' );

        echo '<div class="lead-meta-container">';

        foreach ( self::$meta_fields as $section => $fields ) {
            echo "<h3>$section</h3><table class='form-table'>";
            foreach ( $fields as $key => $field ) {
                $value = get_post_meta( $post->ID, "_$key", true );
                echo "<tr><th><label for='$key'>{$field['label']}</label></th><td>";

                switch ( $field['type'] ) {
                    case 'text':
                    case 'email':
                        $step = isset( $field['step'] ) ? " step='{$field['step']}'" : '';
                        $min = isset( $field['min'] ) ? " min='{$field['min']}'" : '';
                        $max = isset( $field['max'] ) ? " max='{$field['max']}'" : '';
                        echo "<input type='{$field['type']}' id='$key' name='$key' value='" . esc_attr( $value ) . "'$step$min$max />";
                        break;
                    case 'select':
                        echo "<select id='$key' name='$key'>";
                        echo "<option value=''>Select an option</option>";
                        foreach ( $field['options'] as $option_value => $option_label ) {
                            $selected = selected( $value, $option_value, false );
                            echo "<option value='$option_value' $selected>$option_label</option>";
                        }
                        echo "</select>";
                        break;
                    case 'wysiwyg':
                        wp_editor( $value, $key, [
                            'textarea_name' => $key,
                            'media_buttons' => true,  
                            'textarea_rows' => 5,  
                            'teeny'         => false, 
                        ]);
                        break;
                }

                echo '</td></tr>';
            }
            echo '</table>';
        }

        echo '</div>';
    }

    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['lead_meta_nonce'] ) || ! wp_verify_nonce( $_POST['lead_meta_nonce'], 'save_lead_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        foreach ( self::$meta_fields as $fields ) {
            foreach ( $fields as $key => $field ) {
                if ( isset( $_POST[$key] ) ) {
                    $value = ( $field['type'] === 'wysiwyg' ) ? wp_kses_post( $_POST[$key] ) : sanitize_text_field( $_POST[$key] );
                    update_post_meta( $post_id, "_$key", $value );
                }
            }
        }
    }
}

Lead_Meta::init();
