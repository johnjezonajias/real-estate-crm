<?php
namespace Real_Estate_CRM\Agent;

defined( 'ABSPATH' ) || exit;

class Agent_Init {
    public static function init() {
        require_once RECRM_PATH . 'includes/agent/class-agent-cpt.php';
        require_once RECRM_PATH . 'includes/agent/class-agent-meta.php';
        require_once RECRM_PATH . 'includes/agent/class-agent-taxonomies.php';

        Agent_CPT::init();
        Agent_Meta::init();
        Agent_Taxonomies::init();

        // Hook to modify the agent post.
        add_filter( 'enter_title_here', [ __CLASS__, 'change_post_title_placeholder' ], 10, 2 );
        add_action( 'admin_head', [ __CLASS__, 'change_featured_image_labels' ] );
    }

    public static function change_post_title_placeholder( $title, $post ) {
        if ( 'agent' === $post->post_type ) {
            return 'Enter agent\'s fullname';
        }

        return $title;
    }

    public static function change_featured_image_labels() {
        global $post;
    
        if ( isset( $post ) && $post->post_type === 'agent' ) {
            ?>
            <script>
                document.addEventListener( "DOMContentLoaded", function() {
                    // Change labels for featured image.
                    let featuredImageButton = document.querySelector( '#set-post-thumbnail' );
                    let removeImageButton = document.querySelector( '#remove-post-thumbnail' );
    
                    let featuredImageHeading = document.querySelector( '#postimagediv .postbox-header h2' );
                    if ( featuredImageHeading && featuredImageHeading.textContent.trim() === 'Featured image' ) {
                        featuredImageHeading.textContent = 'Profile Picture';
                    }
    
                    if (featuredImageButton) {
                        // Check if an <img> tag is already inside the button
                        let hasImage = featuredImageButton.querySelector("img") !== null;

                        if (!hasImage) {
                            // Only change text if no image is present
                            featuredImageButton.textContent = "Set profile picture";
                        }
                    }
    
                    if ( removeImageButton ) {
                        removeImageButton.textContent = 'Remove profile picture';
                    }
                } );
            </script>
            <?php
        }
    }
}
