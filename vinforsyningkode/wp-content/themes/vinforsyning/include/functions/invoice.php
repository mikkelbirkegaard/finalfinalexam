<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

function create_invoice_cpt() {
    $cpt = 'invoice';
    $cpt_singular = 'Faktura';
    $cpt_plural = 'Fakturaer';

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
        'supports' => array( 'title', 'thumbnail', 'custom-fields', 'revisions' ),
        'taxonomies' => array(),
        'menu_icon' => 'dashicons-media-text',
	    'public' => true,
	    'show_ui' => true,
	    'show_in_menu' => true,
	    'menu_position' => 71,
	    'show_in_admin_bar' => true,
	    'show_in_nav_menus' => false,
	    'can_export' => true,
	    'has_archive' => false,
	    'hierarchical' => false,
	    'exclude_from_search' => true,
	    'show_in_rest' => true,
	    'publicly_queryable' => true,
	    'capability_type' => 'post',
    );
    register_post_type($cpt, $args);
}
add_action( 'init', 'create_invoice_cpt', 0 );

// add_action( 'rest_api_init', 'slug_register_fields' );
// function slug_register_fields() {
//     $fieldArray = array( 'invoice_user_id', 'invoice_date' );
//     register_rest_field( 'invoice',
//         'test_invoice_user_id',
//         array(
//             'get_callback'    => 'slugGetPostMeta',
//             'update_callback' => 'slugUpdatePostMeta',
//             'show_in_rest'    => true,
//         )
//     );
// };
// register_meta('post', 'dateYMD', [
// 		'object_subtype'	=> 'invoice',
//         'show_in_rest'		=> true,
//         'single'		=> true,
// ]);
//
// function update_invoice_date(){
//     $args = array(
//         'post_type' => 'invoice',
//         'posts_per_page' => -1,
//         'post_status' => 'publish',
//     );
//     $the_query = new WP_Query( $args );
//
//     d($the_query);
//     if ($the_query->have_posts()):
//         while($the_query->have_posts()): $the_query->the_post(); endwhile;
//     wp_reset_postdata(); endif;
// }
// 
// add_action('save_post','save_post_callback');
// function save_post_callback($post_id){
//     global $post;
//     if ($post->post_type != 'invoice'){
//         return;
//     }
//     //update_invoice_date();
//     $invoice_date = get_field('invoice_date', $post_id);
//     $dateYMD = date('Ymd', strtotime($invoice_date));
//     update_post_meta( $post_id, 'dateYMD', $dateYMD );
// }

	// register_meta('post', 'last_name', [
	// 	'object_subtype'	=> 'invoice',
    //     'show_in_rest'		=> true,
    //     'single'		=> true,
	// ]);
	// register_meta('post', 'email_address', [
	// 	'object_subtype'	=> 'invoice',
    //     'show_in_rest'		=> true,
    //     'single'		=> true,
	// ]);
