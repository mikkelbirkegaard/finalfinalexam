<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

function create_employee_cpt() {
    $cpt = 'employee';
    $cpt_singular = 'Medarbejder';
    $cpt_plural = 'Medarbejdere';

    $labels = array(
        'add_new_item' => __('Tilføj ny '.$cpt_singular,'bbh'),
        'add_new' => __( 'Tilføj ny','bbh'),
        'all_items' => __('Alle '.$cpt_plural ,'bbh'),
        'edit_item' => __('Rediger '.$cpt_singular,'bbh'),
        'name' => __($cpt_singular,'bbh'),
        'name_admin_bar' => __($cpt_singular,'bbh'),
        'new_item' => __('Ny '.$cpt_singular,'bbh'),
        'not_found' => __('Ingen '.$cpt_singular.' fundet','bbh'),
        'not_found_in_trash' => __('Ingen '.$cpt_plural .' fundet i papirkurv','bbh'),
        'parent_item_colon' => __('Forældre '.$cpt_singular,'bbh'),
        'search_items' => __('Søg '.$cpt_plural ,'bbh'),
        'view_item' => __('Se '.$cpt_singular,'bbh'),
        'view_items' => __('Se '.$cpt_plural ,'bbh'),
		'menu_name' => __($cpt_plural, 'bbh')
    );
    $args = array(
        'labels' => $labels,
        'supports' => array( 'thumbnail', 'title' ),
        'taxonomies' => array(),
        'menu_icon' => 'dashicons-groups',
	    'public' => true,
	    'show_ui' => true,
	    'show_in_menu' => true,
	    'menu_position' => 20,
	    'show_in_admin_bar' => true,
	    'show_in_nav_menus' => false,
	    'can_export' => true,
	    'has_archive' => false,
	    'hierarchical' => false,
	    'exclude_from_search' => true,
	    'show_in_rest' => false,
	    'publicly_queryable' => false,
	    'capability_type' => 'post',
    );
    register_post_type($cpt, $args);
}
add_action( 'init', 'create_employee_cpt', 0 );


/*=============================================
          = Get employee image =
===============================================*/
function bbh_get_employee_image($id = false){
	if(!get_the_post_thumbnail_url()){
        $placeholder = get_stylesheet_directory_uri() . '/assets/images/employee-placeholder2.png';
		return $placeholder;
	}
	$attachment = get_the_post_thumbnail_url($id, 'small');
	return $attachment;
}
