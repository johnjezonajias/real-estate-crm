<?php
namespace Real_Estate_CRM\Agent;

defined( 'ABSPATH' ) || exit;

class Agent_Meta {
    private static $meta_fields = [];

    public static function init() {
        self::define_meta_fields();
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_meta_boxes' ] );
        add_action( 'save_post', [ __CLASS__, 'save_meta' ] );
    }

    private static function define_meta_fields() {
        self::$meta_fields = [
            'Personal Information' => [
                'agent_display_name' => ['label' => 'Display Name', 'type' => 'text'],
                'agent_bio'          => ['label' => 'Bio', 'type' => 'textarea'],
                'agent_facebook'     => ['label' => 'Facebook', 'type' => 'url'],
                'agent_twitter'      => ['label' => 'Twitter', 'type' => 'url'],
                'agent_linkedin'     => ['label' => 'LinkedIn', 'type' => 'url'],
                'agent_instagram'    => ['label' => 'Instagram', 'type' => 'url'],
            ],
            'Professional Details' => [
                'agent_license'      => ['label' => 'License Number', 'type' => 'text'],
                'agent_agency'       => ['label' => 'Agency Name', 'type' => 'text'],
                'agent_experience'   => ['label' => 'Years of Experience', 'type' => 'number'],
                'agent_specialties'  => ['label' => 'Specialties', 'type' => 'text'],
            ],
            'Contact Information' => [
                'agent_phone'        => ['label' => 'Phone', 'type' => 'text'],
                'agent_email'        => ['label' => 'Email', 'type' => 'email'],
                'agent_whatsapp'     => ['label' => 'WhatsApp', 'type' => 'text'],
            ],
        ];
    }

    public static function register_meta_boxes() {
        add_meta_box(
            'agent_meta_box',
            __( 'Agent Information', 'real-estate-crm' ),
            [__CLASS__, 'render_meta_box'],
            'agent',
            'normal',
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'save_agent_meta', 'agent_meta_nonce' );

        echo '<div class="agent-meta-container">';

        foreach ( self::$meta_fields as $section => $fields ) {
            echo "<h3>$section</h3><table class='form-table'>";
            foreach ( $fields as $key => $field ) {
                $value = get_post_meta( $post->ID, "_$key", true );
                echo "<tr><th><label for='$key'>{$field['label']}</label></th><td>";

                switch ( $field['type'] ) {
                    case 'text':
                    case 'number':
                    case 'url':
                        echo "<input type='{$field['type']}' id='$key' name='$key' value='" . esc_attr( $value ) . "' />";
                        break;
                    case 'email':
                        $step = isset( $field['step'] ) ? " step='{$field['step']}'" : '';
                        $min = isset( $field['min'] ) ? " min='{$field['min']}'" : '';
                        $max = isset( $field['max'] ) ? " max='{$field['max']}'" : '';
                        echo "<input type='{$field['type']}' id='$key' name='$key' value='" . esc_attr( $value ) . "'$step$min$max />";
                        break;
                    case 'textarea':
                        echo "<textarea id='$key' name='$key'>" . esc_textarea( $value ) . "</textarea>";
                        break;
                }

                echo '</td></tr>';
            }
            echo '</table>';
        }

        echo '</div>';
    }

    public static function save_meta( $post_id ) {
        if ( !isset( $_POST['agent_meta_nonce'] ) || !wp_verify_nonce( $_POST['agent_meta_nonce'], 'save_agent_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        foreach ( self::$meta_fields as $fields ) {
            foreach ( $fields as $key => $field ) {
                if ( $field['type'] === 'gallery' ) {
                    if ( isset( $_POST[ $key ] ) ) {
                        $value = sanitize_text_field( $_POST[ $key ] );
                        update_post_meta( $post_id, "_$key", $value );
                    }
                } else {
                    if ( isset( $_POST[ $key ] ) ) {
                        $value = sanitize_text_field( $_POST[ $key ] );
                        update_post_meta( $post_id, "_$key", $value );
                    }
                }
            }
        }
    }
}

Agent_Meta::init();
