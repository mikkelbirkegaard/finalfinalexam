<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

// Register Taxonomy manufacturer
function create_manufacturer_tax() {
    $tax = 'manufacturer';
    $tax_singular = 'Producent';
    $tax_plural = 'Producenter';

  	$labels = array(
        'name'              => _x( $tax_plural, 'taxonomy general name', 'bbh' ),
    	'singular_name'     => _x( $tax_singular, 'taxonomy singular name', 'bbh' ),
	    'search_items'      => __( 'Søg ' . $tax_plural, 'bbh' ),
	    'all_items'         => __( 'Alle ' . $tax_plural, 'bbh' ),
	    'parent_item'       => __( 'Forældre ' . $tax_singular, 'bbh' ),
	    'parent_item_colon' => __( 'Forældre ' . $tax_singular . ':', 'bbh' ),
	    'edit_item'         => __( 'Rediger ' . $tax_singular, 'bbh' ),
	    'update_item'       => __( 'Opdater ' . $tax_singular, 'bbh' ),
	    'add_new_item'      => __( 'Tilføj ny ' . $tax_singular, 'bbh' ),
	    'new_item_name'     => __( 'Nyt '. $tax_singular .' navn', 'bbh' ),
	    'menu_name'         => __( $tax_plural, 'bbh' ),
  	);
  	$args = array(
    	'labels' => $labels,
    	'description' => __( '', 'bbh' ),
		'hierarchical' => true,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'show_in_quick_edit' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array(
			'slug' => 'producent',
		),
  );
  register_taxonomy( $tax, array('product'), $args );

}
add_action( 'init', 'create_manufacturer_tax' );


// Register Taxonomy manufacturer
// This tax has a structure of %country% -> %area% -> %district%
function create_district_tax() {
    $tax = 'district';
    $tax_singular = 'Distrikt';
    $tax_plural = 'Distrikter';

  	$labels = array(
        'name'              => _x( $tax_plural, 'taxonomy general name', 'bbh' ),
    	'singular_name'     => _x( $tax_singular, 'taxonomy singular name', 'bbh' ),
	    'search_items'      => __( 'Søg ' . $tax_plural, 'bbh' ),
	    'all_items'         => __( 'Alle ' . $tax_plural, 'bbh' ),
	    'parent_item'       => __( 'Forældre ' . $tax_singular, 'bbh' ),
	    'parent_item_colon' => __( 'Forældre ' . $tax_singular . ':', 'bbh' ),
	    'edit_item'         => __( 'Rediger ' . $tax_singular, 'bbh' ),
	    'update_item'       => __( 'Opdater ' . $tax_singular, 'bbh' ),
	    'add_new_item'      => __( 'Tilføj ny ' . $tax_singular, 'bbh' ),
	    'new_item_name'     => __( 'Nyt '. $tax_singular .' navn', 'bbh' ),
	    'menu_name'         => __( $tax_plural, 'bbh' ),
  	);
  	$args = array(
    	'labels' => $labels,
    	'description' => __( '', 'bbh' ),
		'hierarchical' => true,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'show_in_quick_edit' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array(
			'slug' => 'distrikt',
		),
  );
  register_taxonomy( $tax, array('product'), $args );

}
add_action( 'init', 'create_district_tax' );



function api_add_extra_data( $data, $post, $context ) {
    // We only want to modify the 'view' context, for reading posts
    if ( $context !== 'view' || is_wp_error( $data ) ) {
        // Here, we unset any data we don't want to see on the front end:
        $data->data ['country_code'] = get_term_meta($post->term_id,'country_code');
        // continue unsetting whatever other fields you want
        return $data;
    }

}
add_filter( 'rest_district_query', function( $args )
{
    add_filter( 'rest_prepare_district', 'api_add_extra_data', 12, 3 );
    return $args;
} );
//add country_code in rest post request https://wordpress.stackexchange.com/questions/290757/how-to-update-insert-custom-fieldpost-meta-data-with-wordpress-rest-api
