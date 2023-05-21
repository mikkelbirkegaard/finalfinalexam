<?php

$vinskolen = false;
if (isset($_GET['vinskolen'])) {
    $vinskolen = $_GET['vinskolen'];
}
if ($vinskolen == "true") {
    add_filter( 'body_class', 'vinskolen_body_classes' );
}

function vinskolen_body_classes( $classes ) {
    $classes[] = 'single-vinskolen';  // add custom class to all single posts
return $classes;
}

/*Check if post is duplicated*/
function disable_save( $maybe_empty, $postarr ) {
	if ( ! function_exists( 'post_exists' )) {
    require_once( ABSPATH . 'wp-admin/includes/post.php' );
	}
    if(post_exists($postarr['post_title']) && $postarr['post_type'] == 'vinkort' )
    {
        /*This if statment important to allow update and trash of the post and only prevent new posts with new ids*/
        if(!get_post($postarr['ID']))
    	{
    	      $maybe_empty = true;
        }
    }
    else
    {
    	$maybe_empty = false;
    }

    return $maybe_empty;
}
add_filter( 'wp_insert_post_empty_content', 'disable_save', 999999, 2 );

add_action( 'wp_head', 'inject_acf_form_head', 1 );
function inject_acf_form_head( ) {
    acf_form_head();
}

function automatically_log_me_in( $user_id ) {
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );
    wp_redirect( home_url( '/vinskolen' ) );
    exit();
}
