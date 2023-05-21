<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Vinskolen',
		'menu_title'	=> 'Vinskolen',
		'menu_slug' 	=> 'vinskolen',
		'capability'	=> 'edit_posts',
        'position' => '20',
        'redirect' => false,
        'icon_url'    => 'dashicons-admin-home'
	));
    acf_add_options_sub_page(array(
          'page_title'  => __('Indstillinger'),
          'menu_title'  => __('Indstillinger'),
          'parent_slug' => 'vinskolen',
          'position' => 0,
      ));
	  acf_add_options_sub_page(array(
            'page_title'  => __('Druer'),
            'menu_title'  => __('Druer'),
            'parent_slug' => 'vinskolen',
            'position' => 3,
        ));

}
// optionspage costumer logo
/*===============================================
=          made by Mikkel Christiansen           =
===============================================*/
if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Kunde logoer',
		'menu_title'	=> 'Kunde logoer',
		'menu_slug' 	=> 'kunde-logoer',
		'capability'	=> 'edit_posts',
		'position' => '25',
		'redirect' => false,
		'icon_url'    => 'dashicons-businessperson'
	));


}
// optionspage popups
/*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ 
if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Popups',
		'menu_title'	=> 'Popups',
		'menu_slug' 	=> 'popups',
		'capability'	=> 'edit_posts',
		'position' => '26',
		'redirect' => false,
		'icon_url'    => 'dashicons-table-row-after'
	));


}
function create_foods_cpt() {
    $cpt = 'vinforslag-til-mad';
    $cpt_singular = 'Vinforslag';
    $cpt_plural = 'Vinforslag til mad';

    $labels = array(
        'add_new_item' => __('Tilføj nyt '.$cpt_singular,'bbh'),
        'add_new' => __( 'Tilføj ny','bbh'),
        'all_items' => __($cpt_plural ,'bbh'),
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
        'supports' => array( 'editor', 'thumbnail', 'title' ),
        'taxonomies' => array(),
        'menu_icon' => 'dashicons-food',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'vinskolen',
        'menu_position' => 4,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'rewrite' => true,
        'has_archive' => 'vinskolen/'.$cpt,
        'hierarchical' => true,
        'exclude_from_search' => false,
        'show_in_rest' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type($cpt, $args);
}
add_action( 'init', 'create_foods_cpt', 0 );

function create_mini_lex_cpt() {
    $cpt = 'mini-lex';
    $cpt_singular = 'Mini-lex';
    $cpt_plural = 'Mini-lex';

    $labels = array(
        'add_new_item' => __('Tilføj ny '.$cpt_singular,'bbh'),
        'add_new' => __( 'Tilføj ny','bbh'),
        'all_items' => __($cpt_plural ,'bbh'),
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
        'supports' => array( 'editor', 'thumbnail', 'title' ),
        'taxonomies' => array(),
        'menu_icon' => 'dashicons-book',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'vinskolen',
        'menu_position' => 3,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'rewrite' => true,
        'has_archive' => 'vinskolen/'.$cpt,
        'hierarchical' => true,
        'exclude_from_search' => false,
        'show_in_rest' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type($cpt, $args);
}
add_action( 'init', 'create_mini_lex_cpt', 0 );

function create_vinkort_cpt() {
    $cpt = 'vinkort';
    $cpt_singular = 'Vinkort';
    $cpt_plural = 'Vinkort';

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
        'supports' => array( 'editor', 'thumbnail', 'title' ),
        'taxonomies' => array(),
		'menu_icon' => 'dashicons-media-spreadsheet',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'vinskolen',
        'menu_position' => 4,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'rewrite' => true,
        'has_archive' => 'vinskolen/'.$cpt,
        'hierarchical' => true,
        'exclude_from_search' => false,
        'show_in_rest' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type($cpt, $args);
}
add_action( 'init', 'create_vinkort_cpt', 0 );
