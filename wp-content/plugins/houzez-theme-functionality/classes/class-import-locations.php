<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Import_Locations {
    
    private static $_instance = null;

    public function __construct() {

        add_action( 'init', array( $this, 'setup' ) );
        
    }


    public static function instance() {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    }


    public function setup() {

        add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'enqueue_scripts' ) );
        add_action( 'wp_ajax_get_locations_csv_headers', array( __CLASS__ , 'get_locations_csv_headers' ) );
        add_action( 'wp_ajax_locations_process_field_mapping', array( __CLASS__ , 'locations_process_field_mapping' ) );
    }

    public static function locations_process_field_mapping() {
        $selected_csv_file = isset($_POST['selected_csv_file']) ? ($_POST['selected_csv_file']) : '';
        $field_mapping = isset($_POST['field_mapping']) ? $_POST['field_mapping'] : array();
        $file_path = str_replace(home_url(), untrailingslashit(ABSPATH), $selected_csv_file);
        $errors = array();

        // Sanitize and validate field mappings, exclude empty mappings
        $valid_field_mapping = array();
        foreach ($field_mapping as $db_field => $csv_header) {
            // Check if the mapping is a non-empty string
            if (!empty($csv_header) && is_string($csv_header) && trim($csv_header) !== '') {
                $valid_field_mapping[$db_field] = sanitize_text_field($csv_header);
            }
        }

        if( empty( $valid_field_mapping ) ) {
            wp_send_json_error(esc_html__('Please map at least one field.', 'houzez-theme-functionality'));
        }

        // Open the file and read it
        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            
            // Fetch CSV headers
            $csv_headers = fgetcsv($handle, 1000, ',');

            // Create an associative array mapping CSV headers to their index
            $header_index = array_flip($csv_headers);

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $temp_data = array();

                // Process only valid mappings
                foreach ($valid_field_mapping as $db_field => $csv_header) {
                    $temp_data[$db_field] = sanitize_text_field($data[$header_index[$csv_header]]);
                }

                $country_name = $temp_data['country'] ?? '';
                $state_name = $temp_data['state'] ?? '';
                $city_name = $temp_data['city'] ?? '';
                $area_name = $temp_data['area'] ?? '';

                $import_results = self::insert_or_update_locations($area_name, $city_name, $state_name, $country_name);

                if (!$import_results['success']) {
                    $errors = array_merge($errors, $import_results['errors']);
                }

            }
            fclose($handle);
            
            if (!empty($errors)) {
                wp_send_json_error(implode("\n", $errors));
                wp_die();
            }

            // If successful
            wp_send_json_success('Data imported successfully.');
            wp_die();

        } else {
            wp_send_json_error('Error opening the file.');
        }
        
    }

    public static function insert_or_update_locations($area_name, $city_name, $state_name, $country_name) {
        $errors = array();
        $country_slug = $state_slug = $city_slug = '';

        /// First, get or create the country term
        if(!empty($country_name)) {
            $country_term = term_exists($country_name, 'property_country');
            if (!$country_term) {
                $country_term = wp_insert_term($country_name, 'property_country');
            }
            if (is_wp_error($country_term)) {
                $errors[] = 'Error inserting country term: ' . $country_name . '. Error: ' . implode('; ', $country_term->get_error_messages());
            } else {
                $country_term_id = $country_term['term_id'];
                $country_term_data = get_term($country_term_id, 'property_country');
                if (!is_wp_error($country_term_data)) {
                    $country_slug = $country_term_data->slug;
                } else {
                    $errors[] = 'Error retrieving country term data: ' . $country_name;
                }
            }
        }

        /*----------------------------------------------------------------------
        * Insert or update state term
        * ---------------------------------------------------------------------*/
        if( ! empty( $state_name ) ) {
            $state_term_id = '';
            $state_term = term_exists($state_name, 'property_state'); // Check if the state term exists in the 'property_state' taxonomy
            if (!$state_term) {
                $state_term = wp_insert_term($state_name, 'property_state');
                if (is_wp_error($state_term)) {
                    $errors[] = 'Error inserting country term: ' . $state_name . '. Error: ' . implode('; ', $state_term->get_error_messages());
                } else {
                    $state_term_id = $state_term['term_id'];
                }
            } else {
                $state_term_id = is_array($state_term) ? $state_term['term_id'] : $state_term;
            }
            // Get the state slug
            $state_term_data = get_term($state_term_id, 'property_state');
            $state_slug = $state_term_data->slug;

            // Set the parent country meta data using country slug
            update_option('_houzez_property_state_' . $state_term_id, array('parent_country' => $country_slug));
        }

        /*----------------------------------------------------------------------
        * Insert or update city term
        * ---------------------------------------------------------------------*/
        if( ! empty( $city_name ) ) {
            $city_term_id = '';
            $city_term = term_exists($city_name, 'property_city');
            if (!$city_term) {
                $city_term = wp_insert_term($city_name, 'property_city');
                if (is_wp_error($city_term)) {
                    $errors[] = 'Error inserting city term: ' . $city_name . '. Error: ' . implode('; ', $city_term->get_error_messages());
                } else {
                    $city_term_id = $city_term['term_id'];
                }
            } else {
                $city_term_id = is_array($city_term) ? $city_term['term_id'] : $city_term;
            }
            // Get the city slug
            $city_term_data = get_term($city_term_id, 'property_city');
            $city_slug = $city_term_data->slug;

            // Set the parent state meta data using state slug
            update_option('_houzez_property_city_' . $city_term_id, array('parent_state' => $state_slug));
        }

        /*----------------------------------------------------------------------
        * Insert or update area term
        * ---------------------------------------------------------------------*/
        if( ! empty( $area_name ) ) {
            $area_term_id = '';
            $area_term = term_exists($area_name, 'property_area');
            if (!$area_term) {
                $inserted_area_term = wp_insert_term($area_name, 'property_area');
                if (is_wp_error($inserted_area_term)) {
                    $errors[] = 'Error inserting area term: ' . $area_name . '. Error: ' . implode('; ', $area_term->get_error_messages());
                } else {
                    $area_term_id = $inserted_area_term['term_id'];
                }
            } else {
                $area_term_id = is_array($area_term) ? $area_term['term_id'] : $area_term;
            }

            // Set the parent city meta data using city slug
            update_option('_houzez_property_area_' . $area_term_id, array('parent_city' => $city_slug));
        }

        // After processing all terms
        if (!empty($errors)) {
            return array('success' => false, 'errors' => $errors);
        }

        return array('success' => true);

    }


    public static function get_locations_csv_headers() {
        // Verify nonce and user permission

        $file_url = isset($_POST['file_name']) ? ($_POST['file_name']) : '';
        $file_path = str_replace(home_url(), untrailingslashit(ABSPATH), $file_url);

        // Check if file exists and then get headers
        if (file_exists($file_path)) {
            $headers = self::get_csv_headers($file_path);
            wp_send_json_success($headers);
        } else {
            wp_send_json_error('File not found.');
        }
    }

    public static function get_csv_headers($file_path) {
        if (($handle = fopen($file_path, 'r')) !== false) {
            if (($data = fgetcsv($handle, 1000, ',')) !== false) {
                fclose($handle);
                return $data; // Returns an array of headers
            }
            fclose($handle);
        }
        return array();
    }

    public static function enqueue_scripts( $hook ) {
        $js_path = 'js/';
        $css_path = 'css/';

        if( isset( $_GET['page'] ) && $_GET['page'] == 'import_locations' ) {
            wp_enqueue_media();

            wp_enqueue_style('locations-css', FAVE_WHITE_LABEL_PLUGIN_URL . $css_path . 'style.css', array(), '1.0.0', 'all');
        }
    }

    /**
     * Render dashboard
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'houzez_locations_template_path', HOUZEZ_TEMPLATES . 'locations/form.php' );

        if ( file_exists( $template ) ) {
            include_once( $template );
        }
    }
        
}