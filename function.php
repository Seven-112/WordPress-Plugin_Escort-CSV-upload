<?php

/**
 * Plugin Name: Escort-CSV-upload
 * Plugin URI:
 * Description:custom
 * Version: 1.0.0
 * Author: 
 * Author URI: 
 */



define("NEXT_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
function add_new_menu_items()
{
    add_menu_page("Escort-setting", "Escort-setting", "", "wp-escort-plugin", "next_wp_contact_form_call", "dashicons-admin-users");
    add_submenu_page("wp-escort-plugin", "Forms", "Forms", "manage_options", "wp-contact-form", "next_wp_contact_forms_call");
    // add_submenu_page("wp-service-provider-plugin", "Add Contact", "Add Contact", "manage_options", "wp-contact-add", "next_wp_add_call");
}
add_action("admin_menu", "add_new_menu_items");

// inside view uploadfile path is given
function next_wp_contact_forms_call()
{
    include_once NEXT_PLUGIN_DIR_PATH . '/views/uploadfile.php';
}

if (!function_exists('EFAQ_register_script')) {
    add_action('init', 'EFAQ_register_script');
    function EFAQ_register_script()
    {
        wp_register_style('boostrap_css', plugins_url('assets/css/bootstrap-min.css', __FILE__), false, '1.0.0', 'all');
        wp_enqueue_style('boostrap_css');
        wp_register_style('custom_css', plugins_url('assets/css/style.css', __FILE__), true, '1.0.0', 'all');
        wp_enqueue_style('custom_css');
    }
}

// file upload code


function process_csv_upload()
{
    if (isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file'];

        // Check if file upload has any errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo 'Error uploading file. Please try again.';
            return;
        }

        // Generate a unique file name
        $file_name = uniqid() . '_' . $file['name'];

        // Define the upload directory
        $upload_dir = wp_upload_dir();

        // Move the uploaded file to the desired directory
        $file_path = $upload_dir['path'] . '/' . $file_name;
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // File upload success
            // echo 'CSV file uploaded successfully.';

            // Process the CSV file
            if (($handle = fopen($file_path, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {

                    if (!empty($data[0]) && $data[1] != 'name') {
                        $my_post = array(
                            'post_title'    => $data[1],
                            'post_content'  => '',
                            'link'     => $data[0],
                            'name'     => $data[1],
                            'age'     => $data[2],
                            'city'     => $data[3],
                            'country'    => $data[4],
                            'height'     => $data[5],
                            'weight'     => $data[6],
                            'hair'     => $data[7],
                            'bodyart'     => $data[8],
                            'desc'     => $data[9],

                            'post_status' => 'publish',
                            'post_author' => 1,
                            'post_type'     => 'escort'
                        );

                        $new_post_id = wp_insert_post($my_post);
                        //    adding data in database

                        add_post_meta($new_post_id,  'link', $data[0]);
                        add_post_meta($new_post_id,  'name', $data[1]);
                        add_post_meta($new_post_id,  'age', $data[2]);
                        add_post_meta($new_post_id,  'city', $data[3]);
                        add_post_meta($new_post_id,  'country', $data[4]);
                        add_post_meta($new_post_id,  'height', $data[5]);
                        add_post_meta($new_post_id,  'weight', $data[6]);
                        add_post_meta($new_post_id,  'hair', $data[7]);
                        add_post_meta($new_post_id,  'bodyart', $data[8]);
                        add_post_meta($new_post_id,  'desc', $data[9]);
                    }
                }
                fclose($handle);
                $url=admin_url() . 'edit.php?post_type=escort';
                wp_redirect($url);

                // wp_redirect('https://www.vipescortsafrica.com/wp-admin/edit.php?post_type=escort');

            }
          

        } else {
            // File upload error
            echo 'Error uploading file. Please try again.';
        }
      
    }
   
}

// Register the form submission action
add_action('admin_post_process_csv_upload', 'process_csv_upload');
add_action('admin_post_nopriv_process_csv_upload', 'process_csv_upload');

// Our custom post type function

function create_posttype()
{

    $post_type_exists_post = post_type_exists('escort');
    if (!$post_type_exists_post) {

        register_post_type(
            'Escort',
            // CPT Options
            array(
                'labels' => array(
                    'name' => __('Escort'),
                    'singular_name' => __('escort'),


                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'escort'),
                'show_in_rest' => true,
                //  add custom fileld in custom post type without using plugin with this line and below function

                'supports' => ['link', 'name', 'age', 'city', 'country', 'height', 'weight', 'hair', 'bodyart', 'desc']


            )
        );
    }
}

// Hooking up our function to theme setup
add_action('init', 'create_posttype');

function add_custom_columns_to_post_type($data)
{
    $data['link'] = 'link';
    $data['name'] = 'name';
    $data['age'] = 'age';
    $data['city'] = 'city';
    $data['country'] = 'country';
    $data['height'] = 'height';
    $data['weight'] = 'weight';
    $data['hair'] = 'hair';
    $data['bodyart'] = 'bodyart';
    $data['desc'] = 'desc';


    // $columns['payment_status'] = 'Payment Status';
    return $data;
}
add_filter('manage_escort_posts_columns', 'add_custom_columns_to_post_type');



add_action('manage_escort_posts_custom_column', 'custom_escort_posts_column', 10, 2);
function custom_escort_posts_column($column, $post_id)
{
    switch ($column) {

        case 'link':
            echo $link = get_post_meta($post_id, 'link', true);
            break;

        case 'name':
            echo $name = get_post_meta($post_id, 'name', true);
            break;
        case 'age':
            echo $age = get_post_meta($post_id, 'age', true);
            break;

        case 'city':
            echo $city = get_post_meta($post_id, 'city', true);
            break;
        case 'country':
            echo $country = get_post_meta($post_id, 'country', true);
            break;
        case 'height':
            echo $height = get_post_meta($post_id, 'height', true);
            break;
        case 'weight':
            echo $weight = get_post_meta($post_id, 'weight', true);
            break;
        case 'hair':
            echo $hair = get_post_meta($post_id, 'hair', true);
            break;
        case 'bodyart':
            echo $bodyart = get_post_meta($post_id, 'bodyart', true);
            break;
        case 'desc':
            echo $desc = get_post_meta($post_id, 'desc', true);
            break;
    }
}



// new code 26 may for  add custom fileld in custom post type without using plugin

add_action('init', 'wpcodex_add_custom_fileds_support_for_cpt', 11);
function wpcodex_add_custom_fileds_support_for_cpt()
{
    add_post_type_support('escort', 'custom-fields'); // Change cpt to your post type
}
