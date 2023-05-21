<?php
/**
 * Generate child theme functions and definitions
 *
 * @package Generate
 */

/*=================================================
=         Enqueue all files from folder           =
=================================================*/
// Standard files
foreach(glob(get_theme_file_path() . "/include/standards/*.php") as $file){
	require $file;
}
// Custom files
foreach(glob(get_theme_file_path() . "/include/functions/*.php") as $file){
	require $file;
}

add_filter( 'send_password_change_email', '__return_false' );

//Remove required field requirement for first/last name in My Account Edit form
    add_filter('woocommerce_save_account_details_required_fields', 'remove_required_fields');

        function remove_required_fields( $required_fields ) {
            unset($required_fields['account_first_name']);
            unset($required_fields['account_last_name']);

            return $required_fields;
        }
@ini_set( 'upload_max_size' , '720M' );

@ini_set( 'post_max_size', '64M');

@ini_set( 'max_execution_time', '180' );
