<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;



add_filter( 'woocommerce_product_single_add_to_cart_text', 'bbh_woo_single_add_to_cart_btn_text' );  // 2.1 +
function bbh_woo_single_add_to_cart_btn_text() {

    return __( 'Tilføj', 'bbh' );

}


// Avoid add to cart for non logged user (or not registered)
add_filter( 'woocommerce_add_to_cart_validation', 'logged_in_customers_validation', 10, 3 );
function logged_in_customers_validation( $passed, $product_id, $quantity) {
    if( ! is_user_logged_in() ) {
        $passed = false;

        // Displaying a custom message
        $message = __("Du skal være logget ind for at tilføje produkter til din kurv.", "bbh");
        $button_link = get_permalink( get_option('woocommerce_myaccount_page_id') );
        $button_text = __("Log ind", "woocommerce");
        $message .= ' <a href="'.$button_link.'" class="login-btn button" style="float:right;">'.$button_text.'</a>';

        wc_add_notice( $message, 'error' );
    }
    return $passed;
}


/*----------- product meta -----------*/
add_action('woocommerce_before_add_to_cart_form','bbh_meta_test',15);
function bbh_meta_test()
{
    global $product;
    $product_id = $product->get_id();
    //d(get_the_terms($product_id,'district'));
}
