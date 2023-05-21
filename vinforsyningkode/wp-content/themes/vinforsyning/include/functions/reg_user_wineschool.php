<?php
/** Validation Check*/

// function registration_validation( $username, $password, $email, $vinkortid)  {
// global $reg_errors;
// $reg_errors = new WP_Error;
// if ( empty( $username ) || empty( $password ) || empty( $email ) || empty( $vinkortid )) {
//     $reg_errors->add('field', 'Felt mangler at blive udfyldt');
// } else {
// if ( 4 > strlen( $username ) ) {
// $reg_errors->add( 'username_length', 'Brugernavnet er for kort' );
// }
// if ( username_exists( $username ) ){
// $reg_errors->add('user_name', 'Brugernavnet findes allerede!');
// }
// if ( 5 > strlen( $password ) ) {
//     $reg_errors->add( 'password', 'Adgangskoden skal være længere end 5' );
// }
// if ( !is_email( $email ) ) {
// $reg_errors->add( 'email_invalid', 'Emailen er ikke gyldig' );
// }
// if ( email_exists( $email ) ) {
// $reg_errors->add( 'email', 'Emailen bruges allerede' );
// }
// }}
// /** Registration in WP*/
//
// function registration($username, $password, $email, $roletype, $vinkortid) {
//     $userdata = array(
//     'user_login'    =>   $username,
//     'user_email'    =>   $email,
//     'user_pass'     =>   $password,
//     'role'          =>  $roletype,
//     'vinkort_id'    =>  $vinkortid,
//     );
//     $user = wp_insert_user( $userdata );
//     add_user_meta( $user, "vinkort_id", $vinkortid);
//     echo 'Bruger registreret.';
//     // nocache_headers();
//     // wp_redirect( home_url('/vinskolenn') );
//
//
// }
// function wpdocs_custom_login($username, $password) {
    // $creds = array(
    //     'user_login'    => $username,
    //     'user_pass' => $password,
    //     'remember'      => true
    // );
    //
    // $user = wp_signon( $creds, false );
    //
    // if ( is_wp_error( $user ) ) {
    //     echo $user->get_error_message();
    // }
//     $args = array(
//         'redirect' => home_url(),
//         'id_username' => $username,
//         'id_password' => $password,
//     );
//     wp_login_form($args);
// }

// Run before the headers and cookies are sent.
// add_action( 'after_setup_theme', 'wpdocs_custom_login' );
?>
<?php
/*===============================================
Auto login to site after GF User Registration Form Submittal
===============================================*/
add_action( 'gform_user_registered','we_autologin_gfregistration', 10, 4 );
function we_autologin_gfregistration( $user_id, $config, $entry, $password ) {
wp_set_auth_cookie( $user_id, false, '' );
wp_redirect( home_url('/vinskolenn') ); exit;
}
