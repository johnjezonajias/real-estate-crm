<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

class Property_Taxonomies {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 11 );
        add_action( 'init', [ __CLASS__, 'register_default_terms' ], 12 );
    }

    public static function register_taxonomies() {
        register_taxonomy( 'property_type', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Property Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Type', 'real-estate-crm' ),
                    'search_items'  => __( 'Search Property Type', 'real-estate-crm' ),
                    'all_items'     => __( 'All Property Types', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Property Type', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Property Type', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Property Type', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Property Type Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Property Types', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'property_status', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Property Status', 'real-estate-crm' ),
                    'singular_name' => __( 'Property Status', 'real-estate-crm' ),
                    'all_items'     => __( 'All Property Status', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Property Status', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Property Status', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Property Status', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Property Status Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Property Status', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );

        register_taxonomy( 'zoning_type', 'property',
            [
                'labels'            => [
                    'name'          => __( 'Zoning Types', 'real-estate-crm' ),
                    'singular_name' => __( 'Zoning Type', 'real-estate-crm' ),
                    'all_items'     => __( 'All Zoning Types', 'real-estate-crm' ),
                    'edit_item'     => __( 'Edit Zoning Type', 'real-estate-crm' ),
                    'update_item'   => __( 'Update Zoning Type', 'real-estate-crm' ),
                    'add_new_item'  => __( 'Add New Zoning Type', 'real-estate-crm' ),
                    'new_item_name' => __( 'New Zoning Type Name', 'real-estate-crm' ),
                    'menu_name'     => __( 'Zoning Types', 'real-estate-crm' ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
            ]
        );
    }

    public static function register_default_terms() {
        $taxonomies = [
            'property_type'   => [
                'apartment' => [
                    'name'        => __( 'Apartment', 'real-estate-crm' ),
                    'description' => __( 'A residential unit within a multi-story building.', 'real-estate-crm' ),
                ],
                'house' => [
                    'name'        => __( 'House', 'real-estate-crm' ),
                    'description' => __( 'A standalone residential building.', 'real-estate-crm' ),
                ],
                'villa' => [
                    'name'        => __( 'Villa', 'real-estate-crm' ),
                    'description' => __( 'A luxurious standalone residence.', 'real-estate-crm' ),
                ],
            ],
            'property_status' => [
                'for-sale' => [
                    'name'        => __( 'For Sale', 'real-estate-crm' ),
                    'description' => __( 'The property is available for purchase.', 'real-estate-crm' ),
                ],
                'for-rent' => [
                    'name'        => __( 'For Rent', 'real-estate-crm' ),
                    'description' => __( 'The property is available for rental.', 'real-estate-crm' ),
                ],
                'sold' => [
                    'name'        => __( 'Sold', 'real-estate-crm' ),
                    'description' => __( 'The property has been sold.', 'real-estate-crm' ),
                ],
            ],
            'zoning_type'     => [
                'residential' => [
                    'name'        => __( 'Residential', 'real-estate-crm' ),
                    'description' => __( 'Area designated for housing purposes.', 'real-estate-crm' ),
                ],
                'commercial' => [
                    'name'        => __( 'Commercial', 'real-estate-crm' ),
                    'description' => __( 'Area designated for business activities.', 'real-estate-crm' ),
                ],
                'industrial' => [
                    'name'        => __( 'Industrial', 'real-estate-crm' ),
                    'description' => __( 'Area designated for factories and production.', 'real-estate-crm' ),
                ],
            ],
        ];

        foreach ( $taxonomies as $taxonomy => $terms ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            foreach ( $terms as $slug => $term_data ) {
                $existing_term = get_term_by( 'slug', $slug, $taxonomy );

                if ( ! $existing_term ) {
                    wp_insert_term(
                        $term_data['name'],
                        $taxonomy,
                        [
                            'slug'        => sanitize_title( $slug ),
                            'description' => sanitize_text_field( $term_data['description'] ),
                        ]
                    );
                } elseif ( $existing_term && empty( $existing_term->description ) ) {
                    wp_update_term(
                        $existing_term->term_id,
                        $taxonomy,
                        [
                            'description' => sanitize_text_field( $term_data['description'] ),
                        ]
                    );
                }
            }
        }
    }
}
