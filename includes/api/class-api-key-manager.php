<?php
namespace Real_Estate_CRM\API;

defined( 'ABSPATH' ) || exit;

class API_Key_Manager {
    const OPTION_NAME = 'real_estate_crm_api_key';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
        add_action( 'admin_post_generate_api_key', [ __CLASS__, 'generate_api_key' ] );
    }

    public static function add_settings_page() {
        add_submenu_page(
            'options-general.php',
            'Real Estate CRM API',
            'API Key',
            'manage_options',
            'real-estate-crm-api',
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    public static function render_settings_page() {
        $api_key = get_option( self::OPTION_NAME, '' );
        ?>
        <div class="wrap">
            <h1>Real Estate CRM API Key</h1>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="generate_api_key">
                <?php wp_nonce_field( 'generate_api_key_action', 'api_key_nonce' ); ?>

                <?php if ( $api_key ) : ?>
                    <p><strong>Current API Key:</strong> <code><?php echo esc_html( $api_key ); ?></code></p>
                    <p><em>Use this API key to authenticate API requests.</em></p>
                <?php else : ?>
                    <p>No API key generated yet.</p>
                <?php endif; ?>

                <button type="submit" class="button button-primary"><?php echo $api_key ? 'Regenerate API Key' : 'Generate API Key'; ?></button>
            </form>
        </div>
        <?php
    }

    public static function generate_api_key() {
        if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'generate_api_key_action', 'api_key_nonce' ) ) {
            wp_die( esc_html__( 'Unauthorized action.', 'real-estate-crm' ) );
        }

        $new_api_key = wp_generate_password( 32, false );
        update_option( self::OPTION_NAME, $new_api_key );

        wp_redirect( admin_url( 'options-general.php?page=real-estate-crm-api' ) );
        exit;
    }
}

API_Key_Manager::init();
