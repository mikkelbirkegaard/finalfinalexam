<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*=============================================
          = Reservation stock check =
===============================================*/
/*
Check if the user is trying to buy a product that is fully reserved
but the user did not have any reservations and therefor should not be able to
*/
function add_the_date_validation( $passed, $product_id,$quantity ) {
    //get stock data
    $product = wc_get_product($product_id);
    $stock = $product->get_stock_quantity();

    if($stock > 0) {
        //subtract reservations
        $total_reservations = get_field('total_reserved',$product_id);
        if($total_reservations > 0){
            $stock = $stock - $total_reservations;
        }
    }

    //check for user reservations
    if(is_user_logged_in()) {
        $user_id = get_current_user_id();
        $customer_id = get_user_meta($user_id,'customer_number');
        $rows = get_field('reservations', $product_id);
        if ($rows) {
            if (is_string($rows)) {
                // new - string
                for ($i = 0; $i <= $rows-1; $i++) {
                    $res_user_id = get_field('reservations_'.$i.'_user_id', $product_id);
                    $res_quantity = 0;
                    $res_quantity += get_field('reservations_'.$i.'_quantity', $product_id);
                    if ($customer_id[0] == $res_user_id) {
                        $stock += $res_quantity;
                    }
                }
            }else{
                // old - array
                if (have_rows('reservations',$product_id)) {
                    while (have_rows('reservations',$product_id)) {
                        the_row();
                        if (get_sub_field('user_id') == $customer_id[0]) {
                            $stock += get_sub_field('quantity');
                        }
                    }
                }
            }
        }
    }

    //if stock is zero throw error
    if($stock == 0) {
        wc_add_notice( __( 'Produktet er ikke på lager. Kontakt os hvis du mener dette er en fejl.', 'woocommerce' ), 'error' );
        $passed = false;
    }
    if($quantity > $stock) {
        wc_add_notice( __( 'Du kan ikke tilføje flere varer til kurven end der er på lager. Kontakt os hvis du mener dette er en fejl.', 'woocommerce' ), 'error' );
        $passed = false;
    }
    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'add_the_date_validation', 10, 5 );


function add_update_cart_validation( $passed, $cart_item_key,  $values,  $quantity  ) {
    //get stock data
    $product_id = $values['product_id'];
    $product = wc_get_product($product_id);
    $stock = $product->get_stock_quantity();

    if($stock > 0) {
        //subtract reservations
        $total_reservations = get_field('total_reserved',$product_id);
        if($total_reservations > 0){
            $stock = $stock - $total_reservations;
        }
    }

    //check for user reservations
    if(is_user_logged_in()) {
        $user_id = get_current_user_id();
        $customer_id = get_user_meta($user_id,'customer_number');
        $rows = get_field('reservations', $product_id);
        if ($rows) {
            if (is_string($rows)) {
                // new - string
                for ($i = 0; $i <= $rows-1; $i++) {
                    $res_user_id = get_field('reservations_'.$i.'_user_id', $product_id);
                    $res_quantity = 0;
                    $res_quantity += get_field('reservations_'.$i.'_quantity', $product_id);
                    if ($customer_id[0] == $res_user_id) {
                        $stock += $res_quantity;
                    }
                }
            }else{
                // old - array
                if (have_rows('reservations',$product_id)) {
                    while (have_rows('reservations',$product_id)) {
                        the_row();
                        if (get_sub_field('user_id') == $customer_id[0]) {
                            $stock += get_sub_field('quantity');
                        }
                    }
                }
            }
        }
    }

    //if stock is zero throw error
    if($stock == 0) {
        wc_add_notice( __( 'Produktet er ikke på lager. Kontakt os hvis du mener dette er en fejl.', 'woocommerce' ), 'error' );
        $passed = false;
    }
    if($quantity > $stock) {
        wc_add_notice( __( 'Du kan ikke tilføje flere varer til kurven end der er på lager. Kontakt os hvis du mener dette er en fejl.', 'woocommerce' ), 'error' );
        $passed = false;
    }
    return $passed;
}
add_filter( 'woocommerce_update_cart_validation', 'add_update_cart_validation', 10, 4 );





add_action( 'woocommerce_cart_collaterals', 'options_page_text_cart' );
function options_page_text_cart(){
    $text_info = get_field('text_info', 'theme_shop_settings');
    ?>
        <div class="">
            <?php echo $text_info;?>
        </div>
    <?php
}
