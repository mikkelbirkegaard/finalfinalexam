<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*========================================================
= Calculate total engross price and discounts on checkout =
==========================================================*/
add_action('woocommerce_review_order_after_cart_contents', 'bbh_custom_discounts_checkout_table', 10);
function bbh_custom_discounts_checkout_table() {
	$cart = WC()->cart->get_cart();
	$totalEngross = 0;
	$totalDiscount = 0;
	foreach ($cart as $cart_item_key => $cart_item) {
		$prod = $cart_item['data'];
		$product_id = $cart_item['product_id'];
   		$quantity = $cart_item['quantity'];

		// this get's the money saved if a discount rule was applied.
		$discount = bbh_get_product_user_discount($product_id) ?: 0;
		$totalDiscount += ($discount * $quantity);
		$totalEngross += ($prod->get_regular_price() * $quantity);
	}

	if ($totalEngross && $totalDiscount) {
		echo sprintf(
			"<tr class'total-engross'><th>%s</th><td>%s</td></tr>",
			__('Total brutto', 'bbh'),
			wc_price($totalEngross)
		);
		echo sprintf(
			"<tr class'total-discount'><th>%s</th><td>%s</td></tr>",
			__('Total rabat', 'bbh'),
			wc_price($totalDiscount)
		);
	}
};


/*=============================================
  = Get total order discounts for an order =
===============================================*/
function bbh_get_total_order_discounts($order = false){
	if(!$order){
		return false;
	}
	$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
	$totalEngross = 0;
	$totalDiscount = 0;
	foreach ($order_items as $order_product) {
		$prod = $order_product->get_product();
		$product_id = $order_product->get_product_id();
		$quantity = $order_product->get_quantity();

		$discount = bbh_get_product_user_discount($product_id) ?: 0;
		$totalDiscount += ($discount * $quantity);
		$totalEngross += ($prod->get_regular_price() * $quantity);
	}

	if ($totalEngross && $totalDiscount) {
		return array(
			'engross' => $totalEngross,
			'discount' => $totalDiscount
		);
	}
	return false;
}


/*=============================================
          = Add discounts to order rows =
===============================================*/
add_filter( 'woocommerce_get_order_item_totals', 'bbh_custom_discounts_order_email', 10, 3);
function bbh_custom_discounts_order_email($total_rows, $order, $tax_display){
	$custom_rows = array();
	$data = bbh_get_total_order_discounts($order);

	if ($data) {
		$custom_rows['total_engross'] = array(
			'label' => __('Total brutto', 'bbh'),
			'value' => wc_price($data['engross'])
		);
		$custom_rows['total_discount'] = array(
			'label' => __('Total rabat', 'bbh'),
			'value' => wc_price($data['discount'])
		);
	}

	return $custom_rows + $total_rows;

}


add_action( 'woocommerce_checkout_order_processed', 'bbh_custom_discounts_save_on_order', 10, 1 );
function bbh_custom_discounts_save_on_order( $order_id ) {
	$order = wc_get_order($order_id);
	$data = bbh_get_total_order_discounts($order);

	if($data){
		update_post_meta($order_id, 'custom_discount_value', $data['discount']);
		update_post_meta($order_id, 'custom_brutto_value', $data['engross']);
	}
}

/*=============================================
 = Add warehouse checkbox to checkout page =
===============================================*/
add_action( 'woocommerce_after_order_notes', 'bbh_warehouse_before_billing_details', 20 );
function bbh_warehouse_before_billing_details(){
	$user_id = get_current_user_id();
	$warehouse_number = get_user_meta($user_id,'customer_warehouse_nr');
	if($warehouse_number[0] && $warehouse_number[0] != '0' && $warehouse_number[0] != 0 && $warehouse_number[0] != '') {
		$domain = 'woocommerce';
	    $checkout = WC()->checkout;

	    echo '<div id="my_custom_checkout_field">';

	    echo '<h3>' . __('Lagerordre') . '</h3>';

	    woocommerce_form_field( 'bbh_warehouse_order', array(
	        'type'          => 'checkbox',
	        'label'         => __('Er dette en lagerordre?', $domain ),
	        'class'         => array('my-field-class form-row-wide'),
	        'required'      => false,
		), $checkout->get_value( 'bbh_warehouse_order' ));

	    echo '</div>';
	}
}

// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_warehouse_field_update_meta', 10, 2 );
function bbh_warehouse_field_update_meta( $order, $data ){
    if( isset($_POST['bbh_warehouse_order']) && ! empty($_POST['bbh_warehouse_order']) )
        $order->update_meta_data( 'bbh_warehouse_order', sanitize_text_field( 'Ja' ) );
	else
		$order->update_meta_data( 'bbh_warehouse_order', sanitize_text_field( 'Nej' ) );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_warehouse_display_order_data_in_admin', 10, 1 );
function bbh_warehouse_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_warehouse_order' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "Lagerordre", "woocommerce" ) . '</h4>
        <p>' . $value . '</p>
        </div>';
    }
}


/*=============================================
 = Add warehouse checkbox to checkout page =
===============================================*/
add_action( 'woocommerce_before_checkout_shipping_form', 'bbh_remember_shipping_field', 12 );
function bbh_remember_shipping_field(){
	$user_id = get_current_user_id();
	$bbh_remember_shipping = get_user_meta($user_id,'bbh_remember_shipping');
	$domain = 'woocommerce';
    $checkout = WC()->checkout;

    echo '<div id="bbh-remember-shipping">';

    echo '<h4>' . __('Send altid varerne til denne adresse?') . '</h4>';

	$user_check = 0;
	if ($bbh_remember_shipping[0] && $bbh_remember_shipping[0] != '0' && $bbh_remember_shipping[0] != 0 && $bbh_remember_shipping[0] != '') {
		$user_check = 1;
	}
    woocommerce_form_field( 'bbh_remember_shipping_field', array(
        'type'          => 'checkbox',
        'label'         => __('Ja, husk mig', $domain ),
        'class'         => array('my-field-class form-row-wide'),
        'required'      => false,
	), $user_check);
    echo '</div>';
}

// Save custom checkout fields the data to user
add_action( 'woocommerce_checkout_create_order', 'bbh_remember_shipping_update', 12, 2 );
function bbh_remember_shipping_update( $order, $data ){
	$user_id = get_current_user_id();
    update_field('bbh_remember_shipping', isset($_POST['bbh_remember_shipping_field']), 'user_'.$user_id);
}

/*=============================================
   = Add delivery datepicker to checkout page =
===============================================*/
// NOTE: see bbh_scripts for datepicker code
add_action( 'woocommerce_after_order_notes', 'bbh_datepicker_before_billing_details', 20 );
function bbh_datepicker_before_billing_details(){
	//get checkout
    $domain = 'woocommerce';
    $checkout = WC()->checkout;
	//get delivery rules
	$user_id = get_current_user_id();
	$delivery_days = get_user_meta($user_id,'customer_delivery_days');
	$delivery_days = intval($delivery_days[0]);
	$delivery_hours = get_user_meta($user_id, 'customer_delivery_hours');
	$delivery_hours = intval($delivery_hours[0]) -1;
	$delivery_time_add = intval(date('H:i')) +1;
	if(!$delivery_days || $delivery_days == '' || $delivery_days == 0) {
		$delivery_days = 1;
	}
	//if time of day is later than 12 OR user defined time
	if($delivery_hours){
		if ($delivery_time_add  >= $delivery_hours) {
			$delivery_days++;
		}
	}elseif ($delivery_time_add >= 12) {
		$delivery_days++;
	}


    echo '<div id="my_custom_checkout_field">';

    echo '<h3>' . __('Ønsket leveringsdato') . '</h3>';
	// echo '<h3>' . $delivery_hours . '</h3>';
	// echo '<h3>' . $delivery_time_add . '</h3>';

    woocommerce_form_field( 'bbh_delivery_datepicker', array(
        'type'          => 'text',
        'label'         => __('Ønsket leveringsdato', $domain ),
        'class'         => array('my-field-class form-row-wide'),
        'required'      => true,
		'autocomplete'  => 'off',
		'label_class' => '' . $delivery_days . '',
	), $checkout->get_value( 'bbh_delivery_datepicker' ));

    echo '</div>';
}

// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_datepicker_field_update_meta', 10, 2 );
function bbh_datepicker_field_update_meta( $order, $data ){
	if( isset($_POST['bbh_delivery_datepicker']) && ! empty($_POST['bbh_delivery_datepicker']) )
        $order->update_meta_data( 'bbh_delivery_datepicker', sanitize_text_field( $_POST['bbh_delivery_datepicker'] ) );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_datepicker_display_order_data_in_admin', 10, 1 );
function bbh_datepicker_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_delivery_datepicker' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "Leveringsdato", "woocommerce" ) . '</h4>
        <p>' . $value . '</p>
        </div>';
    }
}
/*===============================================
=     Add CVR and customer ID with order data   =
===============================================*/
/*
CVR
*/
// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_cvr_field_update_meta', 10, 2 );
function bbh_cvr_field_update_meta( $order, $data ){
    if( is_user_logged_in() )
		$user_id = get_current_user_id();
		$cvr = get_user_meta($user_id,'customer_cvr');
        $order->update_meta_data( 'bbh_customer_cvr', $cvr );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_cvr_display_order_data_in_admin', 10, 1 );
function bbh_cvr_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_customer_cvr' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "CVR", "woocommerce" ) . '</h4>
        <p>' . $value[0] . '</p>
        </div>';
    }
}
/*
CUSTOMER ID
*/
// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_customerid_field_update_meta', 10, 2 );
function bbh_customerid_field_update_meta( $order, $data ){
    if( is_user_logged_in() )
		$user_id = get_current_user_id();
		$customer_id = get_user_meta($user_id,'customer_number');
        $order->update_meta_data( 'bbh_customer_id', $customer_id );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_customerid_display_order_data_in_admin', 10, 1 );
function bbh_customerid_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_customer_id' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "Kunde ID", "woocommerce" ) . '</h4>
        <p>'.$value[0].'</p>
        </div>';
    }
}


/*
CUSTOMER EAN locationcode
*/
// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_customerean_field_update_meta', 10, 2 );
function bbh_customerean_field_update_meta( $order, $data ){
    if( is_user_logged_in() )
		$user_id = get_current_user_id();
		$customer_id = get_user_meta($user_id,'customer_ean_location');
        $order->update_meta_data( 'bbh_customer_ean', $customer_id );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_customerean_display_order_data_in_admin', 10, 1 );
function bbh_customerean_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_customer_ean' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "EAN lokationskode", "woocommerce" ) . '</h4>
        <p>'.$value[0].'</p>
        </div>';
    }
}


/*
CUSTOMER P-nr
*/
// Save custom checkout fields the data to the order
add_action( 'woocommerce_checkout_create_order', 'bbh_customerp_nr_field_update_meta', 10, 2 );
function bbh_customerp_nr_field_update_meta( $order, $data ){
    if( is_user_logged_in() )
		$user_id = get_current_user_id();
		$customer_id = get_user_meta($user_id,'customer_p_nr');
        $order->update_meta_data( 'bbh_customer_p_nr', $customer_id );
}

// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'bbh_customerp_nr_display_order_data_in_admin', 10, 1 );
function bbh_customerp_nr_display_order_data_in_admin( $order ){
    if( $value = $order->get_meta( 'bbh_customer_p_nr' ) ) {
        echo '<div class="order_data_column">
        <h4>' . __( "P-nr", "woocommerce" ) . '</h4>
        <p>'.$value[0].'</p>
        </div>';
    }
}


/*===============================================
=          Additional email - BCC           =
===============================================*/
// https://www.businessbloomer.com/woocommerce-add-to-cc-bcc-order-email-recipients/

add_filter( 'woocommerce_email_headers', 'bbloomer_order_completed_email_add_cc_bcc', 9999, 3 );

function bbloomer_order_completed_email_add_cc_bcc( $headers, $email_id, $order ) {
	if ( in_array($email_id, array('customer_invoice', 'customer_processing_order')) ) {
		$user_id = get_current_user_id();
		$extra_email = get_field('extra_email_address', 'user_'.$user_id);
		$order_email_list = get_field('order_email_list', 'user_'.$user_id);
		if ($order_email_list) {
			$email_list = implode(', ', array_column($order_email_list, 'email'));
		}
		$email_bcc = "Bcc: ";
		if ($extra_email && !$order_email_list) {
			$headers .= $email_bcc.$extra_email;
		}elseif(!$extra_email && $order_email_list){
			$headers .= $email_bcc.$email_list;
		}elseif($extra_email && $order_email_list){
			$headers .= $email_bcc.$extra_email.', '.$email_list;
		}
	}
    return $headers;
}
