<?php
namespace Real_Estate_CRM\Property;

defined( 'ABSPATH' ) || exit;

require_once RECRM_PATH . 'includes/models/class-property-agent-manager.php';
use Real_Estate_CRM\Models\Property_Agent_Manager;


class Property_Meta {
    private static $meta_fields = [];

    public static function init() {
        self::define_meta_fields();
        add_action( 'add_meta_boxes',  [ __CLASS__, 'register_meta_boxes' ] );
        add_action( 'save_post', [ __CLASS__, 'save_meta' ] );
        add_action( 'save_post_property', [ __CLASS__,'save_property_agents' ] );
    }

    private static function define_meta_fields() {
        self::$meta_fields = [
            'Property Gallery' => [
                'property_gallery' => ['label' => 'Gallery Images', 'type' => 'gallery'],
            ],
            'General Information' => [
                'developer_name'        => ['label' => 'Developer Name', 'type' => 'text'],
                'listing_price'         => ['label' => 'Listing Price (PHP)', 'type' => 'number'],
                'location'              => ['label' => 'Location (City, State, Country)', 'type' => 'text'],
                'address'               => ['label' => 'Address', 'type' => 'text'],
                'google_maps_link'      => ['label' => 'Google Maps Link', 'type' => 'url'],
                'year_built'            => ['label' => 'Year Built', 'type' => 'number'],
                'availability_status'   => [
                    'label'     => 'Availability Status', 
                    'type'      => 'select', 
                    'options'   => [
                        'available'      => 'Available',
                        'reserved'       => 'Reserved',
                        'pre-selling'    => 'Pre-selling',
                        'under-contract' => 'Under Contract',
                        'sold'           => 'Sold',
                    ]
                ],
                'property_description'  => ['label' => 'Property Description/Notes', 'type' => 'textarea'],
            ],
            'Property Specifications' => [
                'lot_area'              => ['label' => 'Lot Area (sqm)', 'type' => 'number'],
                'floor_area'            => ['label' => 'Floor Area (sqm)', 'type' => 'number'],
                'bedrooms'              => ['label' => 'Bedrooms (0 for Studio type)', 'type' => 'number'],
                'bathrooms'             => ['label' => 'Bathrooms', 'type' => 'number'],
                'carport'               => ['label' => 'Carport Availability', 'type' => 'checkbox'],
                'garage_capacity'       => ['label' => 'Garage Capacity', 'type' => 'number'],
                'furnishing_status'     => [
                    'label'     => 'Furnishing Status', 
                    'type'      => 'select', 
                    'options'   => [
                        'fully-furnished'  => 'Fully Furnished',
                        'semi-furnished'   => 'Semi-furnished',
                        'unfurnished'      => 'Unfurnished',
                        'not-applicable'   => 'Not Applicable',
                    ]
                ],
            ],
            'Additional Details for Commercial/Office Spaces' => [
                'office_space'          => ['label' => 'Office Space (sqm)', 'type' => 'number'],
                'floor_number'          => ['label' => 'Floor Number', 'type' => 'number'],
                'parking_slots'         => ['label' => 'Parking Slots', 'type' => 'number'],
                'building_amenities'    => ['label' => 'Building Amenities', 'type' => 'text'],
            ],
            'Utilities & Features' => [
                'water_supply'          => ['label' => 'Water Supply', 'type' => 'text'],
                'electricity_provider'  => ['label' => 'Electricity Provider', 'type' => 'text'],
                'internet_connection'   => [
                    'label'     => 'Internet Connection', 
                    'type'      => 'select', 
                    'options'   => [
                        'fiber-cable'    => 'Fiber Cable', 
                        'dsl-line'       => 'DSL Line', 
                        'satellite'      => 'Satellite',
                        'prepaid-data'   => 'Prepaid Data',
                        'no-reception'   => 'No Reception',
                        'not-applicable' => 'Not Applicable',
                    ]
                ],
                'security_features'     => ['label' => 'Security Features', 'type' => 'text'],
                'pet_friendly'          => ['label' => 'Pet-Friendly', 'type' => 'checkbox'],
                'nearby_landmarks'      => ['label' => 'Nearby Landmarks', 'type' => 'textarea'],
            ],
            'Financial Information' => [
                'association_dues'      => ['label' => 'Association Dues (PHP/month)', 'type' => 'number'],
                'property_tax'          => ['label' => 'Property Tax (PHP/year)', 'type' => 'number'],
                'payment_options'       => ['label' => 'Payment Options', 'type' => 'text'],
                'estimated_mortgage'    => ['label' => 'Estimated Monthly Mortgage (PHP)', 'type' => 'number'],
            ],
        ];
    }

    public static function register_meta_boxes() {
        add_meta_box(
            'property_meta_box',
            __( 'Property Details', 'real-estate-crm' ),
            [ __CLASS__, 'render_meta_box' ],
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'property_agents_metabox',
            __( 'Assigned Agents', 'real-estate-crm' ),
            [ __CLASS__, 'render_meta_box_agents' ],
            'property',
            'side',
            'default'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'save_property_meta', 'property_meta_nonce' );

        echo '<div class="property-meta-container">';

        foreach ( self::$meta_fields as $section => $fields ) {
            echo "<h3>$section</h3><table class='form-table'>";
            foreach ( $fields as $key => $field ) {
                $value = get_post_meta( $post->ID, "_$key", true );
                echo "<tr><th><label for='$key'>{$field['label']}</label></th><td>";

                switch ( $field['type'] ) {
                    case 'gallery':
                        $gallery_ids = is_string( $value ) ? array_filter( explode(',', $value ) ) : ( is_array($value) ? $value : [] );
                        $value_str = implode( ',', array_map( 'intval', $gallery_ids ) );

                        echo '<div id="property-gallery-container">';
                            foreach ( $gallery_ids as $image_id ) {
                                $image_id = intval( $image_id );
                                if ( $image_id > 0 ) {
                                    $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
                                    if ( $image_url ) {
                                        echo "<div class='gallery-image' data-id='{$image_id}'>
                                                <img src='{$image_url}' />
                                                <button type='button' class='remove-image'>×</button>
                                            </div>";
                                    }
                                }
                            }
                        echo '</div>';
                        echo "<input type='hidden' id='property_gallery' name='property_gallery' value='" . esc_attr( $value_str ) . "' />";
                        echo '<button type="button" id="add-property-gallery" class="button">Add Gallery Images</button>';
                        break;
                    case 'text':
                    case 'number':
                    case 'url':
                        echo "<input type='{$field['type']}' id='$key' name='$key' value='" . esc_attr( $value ) . "' class='large-text' />";
                        break;
                    case 'select':
                        echo "<select id='$key' name='$key' class='regular-text'>";
                        foreach ( $field['options'] as $option ) {
                            $selected = ( $value == $option ) ? 'selected' : '';
                            echo "<option value='$option' $selected>$option</option>";
                        }
                        echo "</select>";
                        break;
                    case 'checkbox':
                        $checked = ( $value ) ? 'checked' : '';
                        echo "<input type='checkbox' id='$key' name='$key' value='1' $checked />";
                        break;
                    case 'textarea':
                        echo "<textarea id='$key' name='$key' class='large-text' rows='10'>" . esc_textarea( $value ) . "</textarea>";
                        break;
                }

                echo '</td></tr>';
            }
            echo '</table>';
        }

        echo '</div>';
    }

    public static function render_meta_box_agents( $post ) {
        global $wpdb;
        $agents = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'agent' AND post_status = 'publish'" );
        
        // Get selected agents for this property.
        $selected_agents = $wpdb->get_col( $wpdb->prepare( "SELECT agent_id FROM {$wpdb->prefix}property_agents WHERE property_id = %d", $post->ID ) );

        echo '<select name="property_agents[]" multiple style="width:100%;">';
            foreach ( $agents as $agent ) {
                $selected = in_array( $agent->ID, $selected_agents ) ? 'selected' : '';
                echo "<option value='{$agent->ID}' $selected>{$agent->post_title}</option>";
            }
        echo '</select>';
    }

    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['property_meta_nonce'] ) || ! wp_verify_nonce( $_POST['property_meta_nonce'], 'save_property_meta' ) ) {
            return;
        }

        if ( isset( $_POST['property_gallery'] ) ) {
            $gallery_input = $_POST['property_gallery'];
            $gallery_ids = is_array( $gallery_input ) ? array_map( 'sanitize_text_field', $gallery_input ) : explode( ',', sanitize_text_field( $gallery_input ) );
            
            if ( !empty( $gallery_ids ) ) {
                update_post_meta( $post_id, '_property_gallery', implode( ',', $gallery_ids ) );
            } else {
                delete_post_meta( $post_id, '_property_gallery' );
            }
        } else {
            delete_post_meta( $post_id, '_property_gallery' );
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        foreach ( self::$meta_fields as $fields ) {
            foreach ( $fields as $key => $field ) {
                if ( $field['type'] === 'gallery' ) {
                    if ( isset( $_POST[$key] ) ) {
                        $value = sanitize_text_field( $_POST[$key] );
                        update_post_meta( $post_id, "_$key", $value );
                    }
                } elseif ( $field['type'] === 'textarea' ) {
                    $value = wp_kses_post( $_POST[$key] );
                    update_post_meta( $post_id, "_$key", $value );
                } else {
                    if ( isset( $_POST[$key] ) ) {
                        $value = sanitize_text_field( $_POST[$key] );
                        update_post_meta( $post_id, "_$key", $value );
                    }
                }
            }
        }
    }

    public static function save_property_agents( $post_id ) {
        global $wpdb;

        if ( isset( $_POST['property_agents'] ) ) {
            $agents = $_POST['property_agents'];

            $wpdb->delete( "{$wpdb->prefix}property_agents", ['property_id' => $post_id] );

            foreach ( $agents as $agent_id ) {
                $wpdb->insert( "{$wpdb->prefix}property_agents", [
                    'property_id' => $post_id,
                    'agent_id'    => intval( $agent_id ),
                    'assigned_at' => current_time( 'mysql' )
                ] );
            }
        }
    }
}

Property_Meta::init();
