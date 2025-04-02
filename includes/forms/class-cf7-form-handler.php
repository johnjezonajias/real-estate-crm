<?php
namespace Real_Estate_CRM\Forms;

defined( 'ABSPATH' ) || exit;

class CF7_Form_Handler {
    public static function register_form() {
        // Exit if Contact Form 7 (CF7) is not active
        if ( ! class_exists( 'WPCF7' ) ) {
            return;
        }

        // Define the form content with placeholders
        $form_content = '
            [text* prospect_name placeholder "Your Name"]
            [email* prospect_email placeholder "Your Email"]
            [tel prospect_phone placeholder "Your Phone"]
            [textarea prospect_message placeholder "Your Message"]
            [hidden property_id]
            [hidden agent_id]
            [submit "Send Inquiry"]
        ';

        // Check if the form with the same title exists
        $existing_forms = get_posts(
            [
                'post_type'      => 'wpcf7_contact_form',
                'title'          => 'Contact Agent Form',
                'posts_per_page' => 1,
                'fields'         => 'ids',
            ]
        );

        if ( empty( $existing_forms ) ) {
            // Sanitize the form title before using it
            $form_title = sanitize_text_field( 'Contact Agent Form' );

            // Insert the new form into the database
            $form_id = wp_insert_post(
                [
                    'post_title'  => $form_title,
                    'post_type'   => 'wpcf7_contact_form',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(), // Ensure the current user is the author
                ]
            );

            // If the form was created successfully, add the content
            if ( is_wp_error( $form_id ) ) {
                // Log or handle the error if needed (e.g., logging failed form creation)
                error_log( 'Failed to create Contact Agent Form: ' . $form_id->get_error_message() );
                return; // Exit if form creation failed
            }

            // Sanitize the form content before saving
            $form_content_sanitized = wp_kses_post( $form_content ); // Allow only safe HTML

            // Update the post meta with sanitized form content
            update_post_meta( $form_id, '_form', $form_content_sanitized );
        }
    }
}
