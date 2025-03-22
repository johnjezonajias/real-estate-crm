<?php
namespace Real_Estate_CRM\Lead;

defined( 'ABSPATH' ) || exit;

class Lead_Taxonomies {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 0 );
        add_action( 'init', [ __CLASS__, 'register_status_terms' ], 11 );
    }

    public static function register_taxonomies() {
        register_taxonomy( 'lead_status', 'lead', [
            'labels'            => [
                'name'              => __( 'Lead Statuses', 'real-estate-crm' ),
                'singular_name'     => __( 'Lead Status', 'real-estate-crm' ),
                'search_items'      => __( 'Search Lead Statuses', 'real-estate-crm' ),
                'all_items'         => __( 'All Lead Statuses', 'real-estate-crm' ),
                'edit_item'         => __( 'Edit Lead Status', 'real-estate-crm' ),
                'update_item'       => __( 'Update Lead Status', 'real-estate-crm' ),
                'add_new_item'      => __( 'Add New Lead Status', 'real-estate-crm' ),
                'new_item_name'     => __( 'New Lead Status Name', 'real-estate-crm' ),
                'menu_name'         => __( 'Lead Status', 'real-estate-crm' ),
            ],
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'lead-status'],
            'show_in_rest'      => true,
            'meta_box_cb'       => [__CLASS__, 'lead_status_radio_meta_box'],
        ]);
    }

    public static function lead_status_radio_meta_box( $post, $box ) {
        $taxonomy = 'lead_status';
        $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
        $selected = wp_get_object_terms( $post->ID, $taxonomy, [ 'fields' => 'ids' ] );
        ?>
        <div id="taxonomy-<?php echo esc_attr( $taxonomy ); ?>" class="categorydiv">
            <ul class="categorychecklist">
                <?php foreach ( $terms as $term ) : ?>
                    <li>
                        <label>
                            <input type="radio" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="<?php echo esc_attr( $term->term_id ); ?>"
                            <?php checked( !empty( $selected ) && in_array( $term->term_id, $selected ) ); ?>>
                            <?php echo esc_html( $term->name ); ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }    

    public static function register_status_terms() {
        $terms = [
            'new'       => [
                'name'        => 'New', 
                'description' => 'A newly created lead'
            ],
            'contacted' => [
                'name'        => 'Contacted', 
                'description' => 'Lead has been contacted'
            ],
            'qualified' => [
                'name'        => 'Qualified', 
                'description' => 'Lead is qualified for follow-up'
            ],
            'lost'      => [
                'name'        => 'Lost', 
                'description' => 'Lead is no longer interested'
            ],
            'closed'    => [
                'name'        => 'Closed', 
                'description' => 'Lead has been converted into a client'
            ],
        ];
    
        foreach ( $terms as $slug => $term_data ) {
            $existing_term = get_term_by( 'slug', $slug, 'lead_status' );
    
            if ( ! $existing_term ) {
                wp_insert_term(
                    $term_data['name'], 
                    'lead_status', 
                    [
                        'slug'        => $slug,
                        'description' => $term_data['description'],
                    ]
                );
            } elseif ( $existing_term && empty( $existing_term->description ) ) {
                // Update the term to ensure the description is set.
                wp_update_term( $existing_term->term_id, 'lead_status', 
                    [
                        'description' => $term_data['description'],
                    ]
                );
            }
        }
    }
}
