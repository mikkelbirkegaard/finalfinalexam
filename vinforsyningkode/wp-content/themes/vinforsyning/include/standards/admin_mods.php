<?php

/*-------------- Remove menu items -------------*/
add_action( 'admin_menu', 'bbh_remove_menus' );

function bbh_remove_menus() {
  remove_menu_page( 'edit-comments.php' );
}


/*-------------- Remove editor from pages -------------*/
add_action( 'admin_head', 'bbh_remove_content_editor', 11 );

function bbh_remove_content_editor() {
    // This will remove support for post thumbnails on ALL Post Types
    remove_post_type_support('page', 'editor');
}


/*-------------- Change login logo url -------------*/
add_filter( 'login_headerurl', 'custom_loginlogo_url' );

function custom_loginlogo_url($url) {
    return $_SERVER['SERVER_NAME'];
}


/*-------------- Remove toolbar nodes -------------*/
add_action( 'admin_bar_menu', 'bbh_remove_nodes', 999 );

function bbh_remove_nodes( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'comments' );
}

/*-------------- Move Yoast SEO metabox to low priority -------------*/
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

function yoasttobottom() {
    return 'low';
}

/*-------------- Remove meta boxes -------------*/
add_action( 'after_setup_theme','bbh_remove_metaboxes' );

function bbh_remove_metaboxes() {
    remove_action('add_meta_boxes', 'generate_add_footer_widget_meta_box'); // Footer widgets
    remove_action( 'add_meta_boxes', 'generate_add_de_meta_box' ); // Deactivate elements
    remove_action('add_meta_boxes', 'generate_add_page_builder_meta_box' ); // Page builder integration
}

/*-------------- Remove layout meta box -------------*/
add_action( 'add_meta_boxes', 'bbh_remove_layout_meta_box', 999 );

function bbh_remove_layout_meta_box() {
    $post_types = get_post_types();
    foreach ( $post_types as $post_type ) :
        remove_meta_box('generate_layout_options_meta_box', $post_type, 'normal');
    endforeach;
}

/*-------------- Update image sizes -------------*/
//Use small for mobile, medium for tablet(1024), and large for desktop

add_action( 'after_setup_theme', 'bbh_image_sizes' );
function bbh_image_sizes(){
    add_image_size( 'small', '420', '9999', false );

    update_option( 'medium_size_w', 1024 );
    update_option( 'medium_size_h', 9999 );
    update_option( 'medium_crop', 0 );

    update_option( 'large_size_w', 1920 );
    update_option( 'large_size_h', 9999 );
    update_option( 'large_crop', 0 );

}
/*-------------- remove wp shortlink -------------*/
add_filter('after_setup_theme', 'bbh_remove_shortlink');
function bbh_remove_shortlink() {
    // remove HTML meta tag
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    // remove HTTP header
    remove_action( 'template_redirect', 'wp_shortlink_header', 11);
}
