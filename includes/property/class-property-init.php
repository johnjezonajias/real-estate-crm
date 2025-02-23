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

        // Hook to modify the property post.
        add_filter( 'enter_title_here', [ __CLASS__, 'change_post_title_placeholder' ], 10, 2 );
    }

    public static function change_post_title_placeholder( $title, $post ) {
        if ( 'property' === $post->post_type ) {
            return 'Enter property title';
        }

        return $title;
    }
}
