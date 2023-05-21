<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/*=============================================
      = Create custom pricing tables =
===============================================*/
/*
	Create the 2 tables used to find a users product price or product discount
*/
add_action('after_switch_theme', 'bbh_create_custom_pricing_db_tables', 10);
function bbh_create_custom_pricing_db_tables() {
 	global $wpdb;

 	$charset_collate = $wpdb->get_charset_collate();
 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// Table for percentage discounts
	$table_name = $wpdb->prefix . 'bbh_product_discount';
	$sql = "
		CREATE TABLE IF NOT EXISTS $table_name (
			pricing_id BIGINT(20) NOT NULL AUTO_INCREMENT,
			discount FLOAT NOT NULL,
			user_ref VARCHAR(255) NOT NULL,
			product_ref VARCHAR(255) NOT NULL,
			type INTEGER NOT NULL,
			PRIMARY KEY (pricing_id),
			INDEX (user_ref, product_ref, type)
		) $charset_collate;";
	dbDelta( $sql );

	// Table for unique product prices
	$table_name = $wpdb->prefix . 'bbh_product_prices';
	$sql = "
		CREATE TABLE IF NOT EXISTS $table_name (
			pricing_id BIGINT(20) NOT NULL AUTO_INCREMENT,
			price FLOAT NOT NULL,
			user_ref VARCHAR(255) NOT NULL,
			product_ref VARCHAR(255) NOT NULL,
			type INTEGER NOT NULL,
            amount INTEGER NOT NULL DEFAULT 1,
			PRIMARY KEY (pricing_id),
			INDEX (user_ref, product_ref, type)
		) $charset_collate;";
	dbDelta( $sql );

    // Table for unique product gifts
	$table_name = $wpdb->prefix . 'bbh_product_gifts';
	$sql = "
		CREATE TABLE IF NOT EXISTS $table_name (
			pricing_id BIGINT(20) NOT NULL AUTO_INCREMENT,
			price_discount FLOAT NOT NULL,
			user_ref VARCHAR(255) NOT NULL,
			product_ref VARCHAR(255) NOT NULL,
			type INTEGER NOT NULL,
            amount INTEGER NOT NULL DEFAULT 12,
            gift VARCHAR(255) NOT NULL,
            gift_count INTEGER NOT NULL DEFAULT 1,
			PRIMARY KEY (pricing_id),
			INDEX (user_ref, product_ref, type)
		) $charset_collate;";
	dbDelta( $sql );
}

/*=============================================
          = Get user lowest discount =
===============================================*/
/*
	Get the lowest discount percentage for a product and a user.
*/
function bbh_get_user_lowest_discount($productID = false, $userID = false){
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$productID || is_wp_error($productID)){
		return false;
	}
	$productGroup = get_field('product_group_id', $productID) ?: false;
	$debitGroup = get_user_meta($userID, 'customer_debitor_discount_group', true) ?: 0;
	$customerID = get_user_meta($userID, 'customer_number', true) ?: 0;
	$sku = get_post_meta($productID, '_sku', true);

	global $wpdb;
	$discountTable = $wpdb->prefix . 'bbh_product_discount';
	// Build the query and check for all different discounts rule types. Get only the largest percentage number.
	$query = $wpdb->prepare(
        "	SELECT `discount`, `type` FROM {$discountTable}
			WHERE ( type = 5 AND user_ref = %s AND product_ref = %s )
			OR ( type = 6 AND user_ref = %s AND product_ref = %s )
			OR ( type = 7 AND user_ref = %s AND product_ref = %s )
			ORDER BY `discount` DESC
			LIMIT 1
		",
		$debitGroup, $sku,
		$debitGroup, $productGroup,
		$customerID, $sku
	);

    /*
    "   SELECT `discount`, `type` FROM {$discountTable}
        WHERE
        ( type = 8 AND product_ref = %s )
        OR ( (user_ref IN ( %s ))
        AND (product_ref IN ( %s) )
        AND (type IN ( %s )) )
        ORDER BY `discount` DESC
        LIMIT 1
    ",
    $sku,
    implode(",", array($debitGroup, $customerID)),
    implode(",", array($sku, $productGroup)),
    implode(",", array(5, 6, 7))
    */

	$return = $wpdb->get_row($query, ARRAY_A) ?: false;

	return $return;
}
/*=============================================
          = Get user lowest price =
===============================================*/
/*
	Get the lowest price from the fixed special price table.
*/
function bbh_get_user_lowest_price($productID = false, $userID = false){
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$productID || is_wp_error($productID)){
		return false;
	}
	$debitPriceGroup = get_user_meta($userID, 'customer_debitor_price_group', true) ?: 0;
	$customerID = get_user_meta($userID, 'customer_number', true) ?: 0;
	$sku = get_post_meta($productID, '_sku', true);

	global $wpdb;
	$pricesTable = $wpdb->prefix . 'bbh_product_prices';
	$query = $wpdb->prepare(
		"	SELECT `price`, `type` FROM {$pricesTable}
			WHERE ( type = 3 AND user_ref = %s AND product_ref = %s AND amount <= 1 )
			OR ( type = 4 AND user_ref = %s AND product_ref = %s AND amount <= 1 )
			ORDER BY `price` ASC
			LIMIT 1
		",
		$debitPriceGroup, $sku,
		$customerID, $sku,
	);
	$return = $wpdb->get_row($query, ARRAY_A) ?: false;

	return $return;
}

/*===============================================
=   Quantity discount / Volume discount      =
===============================================*/
function bbh_get_quantity_discount($productID = false, $userID = false){
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$productID || is_wp_error($productID)){
		return false;
	}
	$debitPriceGroup = get_user_meta($userID, 'customer_debitor_price_group', true) ?: 0;
	$customerID = get_user_meta($userID, 'customer_number', true) ?: 0;
	$sku = get_post_meta($productID, '_sku', true);

	global $wpdb;
	$pricesTable = $wpdb->prefix . 'bbh_product_prices';
	$query = $wpdb->prepare(
		"	SELECT `price`, `amount` FROM {$pricesTable}
			WHERE ( type = 3 AND user_ref = %s AND product_ref = %s AND amount > 1 )
			OR ( type = 4 AND user_ref = %s AND product_ref = %s AND amount > 1 )
			OR ( type = 8 AND user_ref = %s AND product_ref = %s AND amount > 1 )
			ORDER BY `amount` ASC
		",
		$debitPriceGroup, $sku,
		$customerID, $sku,
		$debitPriceGroup, $sku
	);
	$return = $wpdb->get_results($query) ?: false;

	// return $return;
    return $return;
}

/*=============================================
          = Get indicative price if set =
===============================================*/
/*
	Get markup to show indicative prices (market price) for users, who should be shown this.
*/
function bbh_get_indicative_price_html($product = false){
	if(is_int($product)){
		$product = wc_get_product($product);
	}
	$indicative_users = get_field('indicative_price_users', 'theme_indicative_price_settings') ?: array();

	if(!$product || is_wp_error($product) || !in_array(get_current_user_id(), $indicative_users)){
		return false;
	}
	$indicative_price = get_field('indicative_price', $product->get_ID());

	if($product->is_type('variable')){
		return false;
	}

	if(!$indicative_price){
		return false;
	}

	return sprintf(
		'<div class="indicative-price price-row">
			<span class="label">%1$s:&nbsp;</span>
			<span class="value">%2$s</span>
		</div>',
		__('Vejl. udsalgspris', 'bbh'),
		wc_price($indicative_price)
	);

}

function bbh_get_quantity_discount_price_html($product = false){
	if(is_int($product)){
		$product = wc_get_product($product);
	}
    $userID = get_current_user_id();

	$quantity_discount = bbh_get_quantity_discount($product->get_ID(), $userID);
	if($product->is_type('variable')){
		return false;
	}

	if(!$quantity_discount){
		return false;
	}

    $table_rows = '';
    foreach ($quantity_discount as $discount => $value) {
        $table_rows .= '
        <tr>
          <td>v. køb af '.$value->amount.' stk</td>
          <td>'.wc_price($value->price).'</td>
        </tr>';
    };

    return sprintf(
		'<div class="quantity-discount-price price-row" style="flex-direction:column;color:black!important;">
            <table>
              %1$s
            </table>
		</div>',
		$table_rows,
	);

}

/*=============================================
      = Get product user discount price =
===============================================*/
/*
	If a users get's a discount calculated on a product, this function will find the saved value on that product
	for example, if a discount rule giving 20% discount on a 150 DKK product is the cheapest available price, this function will return 30.
	Used for checkout tables to show total discount on a purchase.
*/
function bbh_get_product_user_discount($productID = false, $userID = false){
	$_product = wc_get_product($productID);
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$_product || is_wp_error($_product)){
		return false;
	}
	$price = $_product->get_regular_price();
	$priceRef = $price;

	$maybePrice = bbh_get_user_lowest_price($_product->get_ID(), $userID);
	if($maybePrice && isset($maybePrice['price'])){
		if($maybePrice['price'] < $price){
			$price = $maybePrice['price'];
		}
	}

	$discountPrice = false;
	$maybeDiscount = bbh_get_user_lowest_discount($_product->get_ID(), $userID);
	if( $maybeDiscount && isset($maybeDiscount['discount']) ){
		$discountPrice = $priceRef - ($priceRef * ($maybeDiscount['discount']/100));
	}

	if($discountPrice !== false && $discountPrice < $price ){
		return ($priceRef * ($maybeDiscount['discount']/100));
	}

	return false;
}

/*=============================================
          = Filter prices in WooCommerce =
===============================================*/
// add_filter('woocommerce_product_get_price', 'bbh_filter_get_price', 99, 2 );
// add_filter('woocommerce_product_variation_get_price', 'bbh_filter_get_price' , 99, 2 );
// add_filter('woocommerce_variation_prices_price', 'bbh_filter_get_price', 99, 3 );
/*
	This function/filter finds the cheapest available price for a product and current user.
*/
function bbh_filter_get_price($price, $_product){
	$price = $_product->is_on_sale() && $_product->get_sale_price() != '0' ? $_product->get_sale_price() : $_product->get_regular_price();
	if(!is_user_logged_in() || is_admin()){
		return $price;
	}
	// check if we cached the price before
	$cachePrice = wp_cache_get('calculated_product_price_'.$_product->get_ID());
	if($cachePrice){
		return $cachePrice;
	}
	$userID = get_current_user_id();
	$priceRef = $price;

	$maybePrice = bbh_get_user_lowest_price($_product->get_ID(), $userID);
	if($maybePrice && isset($maybePrice['price'])){
		if($maybePrice['price'] < $priceRef && $maybePrice['price'] != 0){
			$price = $maybePrice['price'];
		}
	}

	$discountPrice = false;
	$maybeDiscount = bbh_get_user_lowest_discount($_product->get_ID(), $userID);
	if( $maybeDiscount && isset($maybeDiscount['discount']) && $maybeDiscount['discount'] != 0 ){
		$discountPrice = $priceRef - ($priceRef * ($maybeDiscount['discount']/100));
	}

	if($discountPrice != false && $discountPrice < $price && $discountPrice !== 0 ){
		$price = $discountPrice;
	}
	// cache the price so we don't have to make queries anymore in current request.
	wp_cache_set('calculated_product_price_'.$_product->get_ID(), $price);
	return $price;
}

/*=============================================
          = Price display html =
===============================================*/
add_filter('woocommerce_get_price_html', 'bbh_get_custom_price_html', 9999, 2);
function bbh_get_custom_price_html($price, $product) {
    //only show prices is user is logged in
    if(is_user_logged_in()) :
    	// not simple product, yikes.
    	$indicative_price = bbh_get_indicative_price_html($product);
        $quantity_discount_table = bbh_get_quantity_discount_price_html($product);
        $bbh_quantity_gift = bbh_get_quantity_gift_html($product);
        $price_text = 'Din pris';
        $secondary_cat = $product->get_attribute('secondary_cat');
    	if ($secondary_cat && strpos($secondary_cat, 'Outlet') !== false ) {
    		$price_text = 'OUTLET PRIS';
    	}
    	if($product->get_type() == 'variable'){
    		if($product->get_price() !== $product->get_variation_price('min')){
    			return sprintf(
    				'
    					<div class="custom-price-wrap">
    						%1$s
    						<div class="inactive-price price-row price-row">
    							<span class="label">%2$s:&nbsp;</span>
    							<span class="value">%3$s</span>
    						</div>
    						<div class="active-price price-row">
    							<span class="label">%4$s:&nbsp;</span>
    							<span class="value">%5$s</span>
    						</div>
                            <div class="secondary-price price-row">
                                <span class="label">Secondary Price:&nbsp;</span>
                                <span class="value">%6$s</span>
                            </div>
    					</div>
    				',
    				$indicative_price,
    				__('Normalpris', 'bbh'),
    				wc_price($product->get_price()),
    				__($price_text, 'bbh'),
    				wc_price($product->get_variation_price()),
                    __($secondary_cat, 'bbh'),
                    wc_price($product->get_attribute('secondary_cat'))
    			);
    		} else{
    			return sprintf(
    				'
    					<div class="custom-price-wrap">
    						%s
    						<div class="active-price price-row">
    							<span class="label">%s:&nbsp;</span>
    							<span class="value">%s</span>
    						</div>
    					</div>
    				',
    				$indicative_price,
    				__($price_text, 'bbh'),
    				wc_price($product->get_variation_price('min'))
    			);
    		}

    	}

    	// if regular (core price) is not the active price.
    	if($product->get_price() !== $product->get_regular_price()){
            return sprintf(
    			'
    				<div class="custom-price-wrap">
    					%s
    					<div class="inactive-price price-row price-row">
    						<span class="label">%s:&nbsp;</span>
    						<span class="value">%s</span>
    					</div>
    					<div class="active-price price-row">
    						<span class="label">%s:&nbsp;</span>
    						<span class="value">%s</span>
    					</div>
                        %s
                        %s
    				</div>
    			',
    			$indicative_price,
    			__('Normalpris', 'bbh'),
    			wc_price($product->get_regular_price()),
    			__($price_text, 'bbh'),
    			wc_price($product->get_price()),
                $quantity_discount_table,
                $bbh_quantity_gift
    		);
    	} else{
    		return sprintf(
    			'
    				<div class="custom-price-wrap">
    					%s
    					<div class="active-price price-row">
    						<span class="label">%s:&nbsp;</span>
    						<span class="value">%s</span>
    					</div>
                        %s
                        %s
    				</div>
    			',
    			$indicative_price,
    			__($price_text, 'bbh'),
    			wc_price($product->get_price()),
                $quantity_discount_table,
                $bbh_quantity_gift
    		);
    	}
    	return $price;
    else :
        return '';
    endif;
};


/*=============================================
          = cache user custom prices list =
===============================================*/
add_action('wp', 'bbh_save_cache_user_prices', 10, 0);
// set hook accepted arguments to 0 to avoid global $wp in function arguments
function bbh_save_cache_user_prices($userID = false) {
	$userID = $userID ?: get_current_user_id(); // defalt to current user but allow running the function for a specifik user.
	if(!$userID){
		return false;
	}
	$ascTrans = get_transient('user_pricing_list_'.$userID);
	if(!$ascTrans){
		$pricesASC = get_user_product_price_list('asc');
		$pricesDESC = array_reverse($pricesASC);
		set_transient('user_pricing_list_'.$userID, $pricesDESC, HOUR_IN_SECONDS * 1);
		set_transient('user_pricing_list_'.$userID, $pricesASC, HOUR_IN_SECONDS * 1);
	}
};

/*=============================================
    = Get list of user product prices =
===============================================*/
/*
	Generate a list of product based on a price. Price changes with the user. Value is saved in transient.
	Used for ordering products by price.

	// WARNING: This is an extremely demanding operation
*/

// sort values of product prices. Used for uasort function.
function get_user_product_price_list_sort($a, $b) {
	return $a < $b;
}

function get_user_product_price_list($order = 'ASC', $userID = false){
	$orig_user = get_current_user_id(); // save who is active user
	if($userID){
		wp_set_current_user($userID); // set simulated user from args
	}
	$order = mb_strtoupper($order);
	$array = array();

	global $wpdb;
	$posts = $wpdb->get_col("
		SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'product'
		AND post_status = 'publish'
	");

	if($posts && count($posts) > 0){
		foreach ($posts as $p) {
			// creating an array like this
			// 	[
			//		{{post->ID}} => {{product->price}}
			//	]
			$array[$p] = bbh_filter_get_price_by_id(0, $p);
		}
		uasort($array, "get_user_product_price_list_sort"); // sort the posts by price. Retain array keys.
		$array = array_keys($array); // get a list of the keys. Prices are irrelevant at this point.
	}

	wp_set_current_user($orig_user); // reset current user
	if($order == 'ASC'){
		return array_reverse($array);
	} else{
		return $array;
	}
}

/*=============================================
  = Get a products dynamic price from product ID =
===============================================*/
// This function is similar to the one used in the woo filters, only this gains a little speed by only using ID instead of a full product.
function bbh_filter_get_price_by_id($price, $_product){
	$sale = get_post_meta($_product, '_sale_price', true);
	$regular = get_post_meta($_product, '_regular_price', true);

	$price = $sale && $sale != '0' ? $sale : $regular; // set default price from either _sale_price or _regular_price
	if(!is_user_logged_in() || is_admin()){
		// no need if not logged in or in admin
		return $price;
	}

	// check if we found and cached the price before in request
	$cachePrice = wp_cache_get('calculated_product_price_'.$_product);
	if($cachePrice){
		return $cachePrice;
	}
	$userID = get_current_user_id();
	// reference to the original price;
	$priceRef = $price;

	// check if user has a special price, and assign it.
	$maybePrice = bbh_get_user_lowest_price($_product, $userID);
	if($maybePrice && isset($maybePrice['price'])){
		if($maybePrice['price'] < $priceRef && $maybePrice['price'] != 0){
			$price = $maybePrice['price'];
		}
	}
	// Check if user has a discount calculation
	$discountPrice = false;
	$maybeDiscount = bbh_get_user_lowest_discount($_product, $userID);
	if( $maybeDiscount && isset($maybeDiscount['discount']) && $maybeDiscount['discount'] != 0 ){
		$discountPrice = $priceRef - ($priceRef * ($maybeDiscount['discount']/100));
	}
	// if discount is cheaper than the special user price, use that instead.
	if($discountPrice != false && $discountPrice < $price && $discountPrice != 0 ){
		$price = $discountPrice;
	}
	// cache the price so we don't have to make queries anymore in this instance.
	wp_cache_set('calculated_product_price_'.$_product, $price);
	// finally return the price
	return $price;
}

/*===============================================
=          PANT + 3% Discount          =
===============================================*/

add_action('woocommerce_cart_calculate_fees', 'add_pant_and_discount_to_cart');
function add_pant_and_discount_to_cart($cart){
    if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}
    $pant_total = 0;
    foreach( WC()->cart->get_cart() as $cart_item){
        $product_id = $cart_item['data']->get_id();
        $pant = get_field('pant', $product_id);
        if ($pant) {
            if (strpos($pant, ',') !== false ) {
                $pant = str_replace(",", ".", $pant);
            }
            $qty = $cart_item['quantity'];
            $pant_int = (float)$pant;
            $pant_total = $pant_total + $pant_int * $qty;
        }
    }
    if ($pant_total > 0) {
        $cart->add_fee(__('Pant', 'txtdomain'), $pant_total, true);
    }


    /*----------- Discount for New Customer  -----------*/
    $customerID = get_user_meta(get_current_user_id(), 'customer_number', true);
    $new_customer_discount = get_field( 'new_customer_discount', 'theme_shop_settings' );
    if (!$customerID && $new_customer_discount) {
        $discount_text = get_field( 'new_customer_discount_text', 'theme_shop_settings' )?: 'Rabat';
        // Discount
        $cart_subtotal = (int)$cart->get_cart_contents_total();
        $shipping = (int)$cart->shipping_total;
        $cart_total = $cart_subtotal + $shipping + $pant_total;
        $percentage = $new_customer_discount;
        $discount = -abs(($percentage / 100) * $cart_total);
        $cart->add_fee(__($discount_text, 'txtdomain'), $discount, false);
    }


}
/*===============================================
=   Recalculate price with quantity discount     =
===============================================*/
function bbh_get_quantity_discount_price($productID = false, $userID = false, $amount){
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$productID || is_wp_error($productID)){
		return false;
	}
	$debitPriceGroup = get_user_meta($userID, 'customer_debitor_price_group', true) ?: 0;
	$customerID = get_user_meta($userID, 'customer_number', true) ?: 0;
	$sku = get_post_meta($productID, '_sku', true);

	global $wpdb;
	$pricesTable = $wpdb->prefix . 'bbh_product_prices';
	$query = $wpdb->prepare(
		"	SELECT `price` FROM {$pricesTable}
			WHERE ( type = 3 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 4 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 8 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			ORDER BY `price` ASC
            LIMIT 1
		",
		$debitPriceGroup, $sku, $amount,
		$customerID, $sku, $amount,
		$debitPriceGroup, $sku, $amount
	);
	$return = $wpdb->get_results($query) ?: false;

	// return $return;
    return $return;

}


function bbh_make_cart_array(){
    $cart = WC()->cart;
    $bbh_cart_array = array();
    foreach ( $cart->cart_contents as $cart_item ) {
        $product_id = $cart_item['product_id'];
        $userID = get_current_user_id();
        $quantity =  $cart_item['quantity'];
        if ($quantity > 1) {
            $quantity_discount_price = bbh_get_quantity_discount_price($product_id, $userID, $quantity);
            if ($quantity_discount_price) {
                $new_price = (double)$quantity_discount_price[0]->price;
                $bbh_cart_array[$product_id] = $new_price;
            }
        }
    }
    return $bbh_cart_array;
}

function bbh_add_quantity_discount_price( $price, $_product) {
    $price = $price;
    if ( is_page( 'cart' ) || is_cart() || is_page( 'checkout' ) || is_checkout() ) {
        $bbh_cart_array = bbh_make_cart_array();
        if ($bbh_cart_array) {
            $product_id = $_product->get_id();
            if (isset($bbh_cart_array[$product_id])) {
                $new_price = $bbh_cart_array[$product_id];
                if ($new_price < $price) {
                    $price = $new_price;
                    return $price;
                }
            }
        }
    }
    return $price;
}
add_filter('woocommerce_product_get_price', 'bbh_add_quantity_discount_price', 100, 2);


/*===============================================
            =    Quantity Gift   =
===============================================*/
function bbh_get_quantity_gift($productID = false, $userID = false, $amount){
	$userID = $userID ?: get_current_user_id();
	if(!$userID || !$productID || is_wp_error($productID)){
		return false;
	}
	$debitPriceGroup = get_user_meta($userID, 'customer_debitor_price_group', true) ?: 0;
	$customerID = get_user_meta($userID, 'customer_number', true) ?: 0;
	$sku = get_post_meta($productID, '_sku', true);
    $productGroup = get_field('product_group_id', $productID) ?: false;
    $debitGroup = get_user_meta($userID, 'customer_debitor_discount_group', true) ?: 0;
	global $wpdb;
	$pricesTable = $wpdb->prefix . 'bbh_product_gifts';
	$query = $wpdb->prepare(
		"	SELECT `price_discount`, `type`, `amount`, `gift`, `gift_count` FROM {$pricesTable}
			WHERE ( type = 3 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 4 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 8 AND user_ref = %s AND product_ref = %s AND amount <= %s )
            OR ( type = 5 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 6 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			OR ( type = 7 AND user_ref = %s AND product_ref = %s AND amount <= %s )
			ORDER BY `amount` DESC
		",
		$debitPriceGroup, $sku, $amount,
		$customerID, $sku, $amount,
		$debitPriceGroup, $sku, $amount,
        $debitGroup, $sku, $amount,
        $debitGroup, $productGroup, $amount,
        $customerID, $sku, $amount
	);
	$return = $wpdb->get_results($query) ?: false;
    return $return;
}
function bbh_get_product_id_from_sku($sku){
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_sku', //Your Custom field name
        'meta_value' => $sku, //Custom field value
        'meta_compare' => '='
    );
    $the_query = new WP_Query( $args );
    if ($the_query->have_posts()) {
        $product_id = $the_query->posts[0]->ID;
        return $product_id;
    }else{
        return 0;
    }
}
function bbh_get_quantity_gift_html($product = false){
	if(is_int($product)){
		$product = wc_get_product($product);
	}
    $userID = get_current_user_id();
	$quantity_gift = bbh_get_quantity_gift($product->get_ID(), $userID, 100000);
	if($product->is_type('variable')){
		return false;
	}
	if(!$quantity_gift){
		return false;
	}
    //$reverse_quantity_gift = array_reverse($quantity_gift);
    foreach ($quantity_gift as $gift => $value) {
        $gift_id = bbh_get_product_id_from_sku($value->gift);
        $link = get_permalink($gift_id);
        $title = get_the_title($gift_id);
        $img = get_the_post_thumbnail_url($gift_id);
        $gift_amount = $value->amount;
    };
	return sprintf(
		'<div class="quantity-gift-price" style="display:none; opacity:0;" data-gift-amount="%1$s" data-gift-name="%2$s" data-gift-link="%3$s" data-gift-img="%4$s"></div>',
		$gift_amount,
        $title,
        $link,
        $img
	);
}
add_action( 'woocommerce_before_calculate_totals', 'bbh_gift_based_on_quantity');
function bbh_gift_based_on_quantity( $cart ) {
    // This is necessary for WC 3.0+
    if (is_user_logged_in()) {
        $userID =  get_current_user_id();
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }
        // Avoiding hook repetition (when using price calculations for example)
        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
             if ( isset( $cart_item['discounted'] ) ) {
                  $cart->remove_cart_item( $cart_item_key );
             }
        }
        // Loop through cart items
        foreach ( $cart->get_cart() as $cart_item ) {
            $product_id = $cart_item['product_id'];
            $quantity =  $cart_item['quantity'];
            $bbh_gift = bbh_get_quantity_gift($product_id, $userID, $quantity);
            if ($bbh_gift) {
                $gift_first_amount = $bbh_gift[0]->amount;
                foreach ($bbh_gift as $gift) {
                $gift_amount = $gift->amount;
                    if ($gift_first_amount == $gift_amount) {
                    $gift_sku = $gift->gift;
                    $gift_discount = (int)$gift->price_discount;
                    $gift_count = $gift->gift_count;
                    $mod = $quantity % $gift_amount;
                    $times = ($quantity-$mod)/$gift_amount;
                    $gift_quantity = $times * $gift_count;
                    $gift_discount_type = $gift->type;
                    $gift_product_id = bbh_get_product_id_from_sku($gift_sku);
                    //$stock = get_post_meta( $gift_product_id, '_stock', true );
                    if ($gift_product_id > 0 ) {
                        $cart->add_to_cart($gift_product_id,$gift_quantity,0,[],['discounted' => 'true','discount_type' => $gift_discount_type, 'discount_price' => $gift_discount]);
                    }
                    }
                }
            }
        }
        foreach ( $cart->get_cart() as $cart_item ) {
            if ( isset( $cart_item['discounted'] ) ) {
                $type = $cart_item['discount_type'];
                // can either be discounted price or discount%
                $discount = $cart_item['discount_price'];
                $price_array = array('3','4','8');
                $percent_array = array('5','6','7');
                if (in_array($type, $price_array)) {
                    $cart_item['data']->set_price( $discount );
                }else{
                    if ($discount == 100) {
                        $cart_item['data']->set_price( 0 );
                    }else{
                        $price = (float)$cart_item['data']->get_price();
                        $discount_value = ($price / 100) * $discount;
                        $new_price = $price - $discount_value;
                        $cart_item['data']->set_price( $new_price );
                        $cart_item['data']->set_sale_price( $new_price );
                    }
                }
            }
        }
    }
}
function bbh_Check_price( $price, $_product) {
    if ( is_page( 'cart' ) || is_cart() || is_page( 'checkout' ) || is_checkout() ) {
        $price = $price;
        $changes = $_product->get_changes();
        if (count($changes) > 0) {
            if (isset($changes['price'])) {
                $price = $changes['price'];
            }
        }
    }
    return $price;
}
add_filter('woocommerce_product_get_price', 'bbh_Check_price', 101, 2);




add_action( 'woocommerce_before_calculate_totals', 'adding_custom_price', 101, 1);
function adding_custom_price( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    foreach ( $cart->get_cart() as $cart_item ) {
        $product_price = $cart_item['data']->get_price(); // get the price
        $rounded_price = round( $product_price, 2, PHP_ROUND_HALF_UP );
        $cart_item['data']->set_price(floatval($rounded_price));
    }
}
