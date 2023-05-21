<?php
// function ps_acf_save_post( $post_id ) {
//     // Don't do this on the ACF post type
//     if ( get_post_type( $post_id ) == 'acf' ) return;
//
//     // Get the Fields
//     $fields = get_field_objects( $post_id );
//
//     // Prevent Infinite Looping...
//     remove_action( 'acf/save_post', 'my_acf_save_post' );
//
//     // Grab Post Data from the Form
//     $post = array(
//         'ID'           => $post_id,
//         'post_type'    => 'vinkort',
//         'post_title'   => $fields['new_title']['value'],
//         'post_content' => $fields['new_description']['value'],
//         'post_status'  => 'publish'
//     );
//
//     // Update the Post
//     wp_update_post( $post );
//
//     // Continue save action
//     add_action( 'acf/save_post', 'my_save_post' );
//
//     // Set the Return URL in Case of 'new' Post
//     $_POST['return'] = add_query_arg( 'updated', 'true', get_permalink( $post_id ) );
// }
// add_action( 'acf/save_post', 'ps_acf_save_post', 10, 1 );
