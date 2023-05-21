<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * My account custom pages
 */
class BbhAccountPages {
	public $pages;


	function __construct() {
		$this->pages = array(
			'../shop' => __('Gå til shop', 'bbh'),
			'cart' => __('Min indkøbskurv', 'bbh'),
			'favourites' => __('Mine Favoritter', 'bbh'),
			'checkout' => __('Checkout', 'bbh'),
			'outlet' => __('OUTLET', 'bbh'),
			'invoices' => __('Fakturaer', 'bbh'),
			'ecology' => __('Økologi', 'bbh'),
			'repurchase' => __('Tidligere køb', 'bbh'),
			'vinskolen' => __('Vinskolen', 'bbh'),
			//'reservations' => __('Dine reservationer', 'bbh'),
			//'customer_info' => __('Kundeinfo', 'bbh'),
		);
		$current_user_id = get_current_user_id();
		$focusWines = get_field( 'focus_wines', 'user_'.$current_user_id );
		if ($focusWines) {
			$this->pages['focus_wine'] = __('Fokus vinliste', 'bbh');
		}
		function get_all_reservations(){
			$args = array(
			    'post_type' => 'product',
			    'posts_per_page' => -1,
			    'post_status' => 'publish',
			    'meta_query' => array(
			        array(
			            'key' => 'reservations',
			            'value' => 0,
			            'compare' => '>'
			        ),
			    ),
			);

			$user_id = get_current_user_id();
			$customer_id = get_field('customer_number', 'user_'.$user_id);
			$the_query = new WP_Query( $args );
			$reservations_array = array();

			if ($the_query->have_posts()):
			    while($the_query->have_posts()): $the_query->the_post();
			    $product_id = get_the_ID();
			    $rows = get_field('reservations', $product_id);
			    if ($rows) {
			        if (is_string($rows)) {
			            // new - string
			            for ($i = 0; $i <= $rows-1; $i++) {
			                $res_user_id = get_field('reservations_'.$i.'_user_id', $product_id);
			                $res_quantity = get_field('reservations_'.$i.'_quantity', $product_id);
			                if ($customer_id == $res_user_id) {
			                    $reservations_array[] = array(
			                        'id' => $product_id,
			                        'quantity' => $res_quantity
			                    );
			                }
			            }
			        }else{
			            // old - array
			            if (have_rows('reservations',$product_id)) {
			                while (have_rows('reservations',$product_id)) {
			                    the_row();
			                    if (get_sub_field('user_id') == $customer_id) {
			                        $res_quantity = get_sub_field('quantity');
			                        $reservations_array[] = array(
			                            'id' => $product_id,
			                            'quantity' => $res_quantity
			                        );
			                    }
			                }
			            }
			        }
			    }
			    endwhile;
			    wp_reset_postdata();
				return $reservations_array;
			else:
				return false;
			endif;

		}
		if (get_all_reservations()) {
			$this->pages['my-reservations'] = __('Mine reservationer', 'bbh');
		}
		// add cart count to sidebar nav menu. Need to run this later than constructor for cart to be available.
		add_action('wp', array($this, 'add_menu_cart_count'), 10);

		// Add rewrite points
		add_action('wp_loaded', array( $this, 'add_rewrite_endpoints' ), 10);

		// Add query vars
		add_filter('query_vars', array( $this, 'add_query_vars' ), 0, 1);

		// Register accounts pages
		add_filter( 'woocommerce_account_menu_items', array( $this, 'register_account_pages' ), 10, 1);

		// //Check for user focus products
		// add_action('wp_loaded', array($this, 'check_focus_page'), 9);

		// register template files for account pages
		add_action('wp_loaded', array($this, 'register_template_pages'));


	}

	// public function check_focus_page(){
	// 	if(!is_user_logged_in()){
	// 		return;
	// 	}
	// 	$current_user_id = get_current_user_id();
	// 	$focus_products = get_field( 'focus_wines', 'user_'.$current_user_id );
	// 	if(!$focus_products){
	// 		return;
	// 	}
	// 	$this->pages['focus_winelist'] = __('Fokus vinliste', 'bbh') ;
	// }


	public function register_template_pages(){
		// Register page templates
		foreach ($this->pages as $key => $value) {
			add_action( 'woocommerce_account_' . $key . '_endpoint', array( $this, 'register_account_page_templates' ), 10 );
		}
	}
	public function add_menu_cart_count() {
		if(!is_account_page())
			return;
		global $woocommerce;

		$count = $woocommerce->cart->cart_contents_count;
		$countMarkup = $count > 0 ? ' ('.$count.')' : '';

		$this->pages['cart'] .= $countMarkup;

	}

	public function add_rewrite_endpoints(){
		foreach ($this->pages as $key => $value) {
			add_rewrite_endpoint( $key, EP_ROOT | EP_PAGES );
		}
		add_rewrite_endpoint( 'min-konto/checkout/order-received', EP_ROOT | EP_PAGES );
	}

	public function add_query_vars($qvars){
		foreach ($this->pages as $key => $value) {
			$qvars[] = $key;
		}
		return $qvars;
	}

	public function register_account_pages( $items ) {
	  	if($this->pages){
	 		$items = array_merge($this->pages, $items);
	  	}
	  	return $items;
	}

	public function register_account_page_templates() {
		global $wp_query;
		foreach ($this->pages as $key => $value) {
			if(isset($wp_query->query[$key])){
				if(file_exists(get_stylesheet_directory() . "/template-parts/myaccount/{$key}.php"  ) ){
					include(get_stylesheet_directory() . "/template-parts/myaccount/{$key}.php"  );
					break;
				}
			}
		}
	}
};

new BbhAccountPages();


/*=============================================
  = Make is_checkout and is_cart work on account page =
===============================================*/

add_filter('woocommerce_is_checkout', 'bbh_is_checkout_on_account', 10);
function bbh_is_checkout_on_account($val) {
	global $wp_query;
	if(is_account_page() && isset($wp_query->query['checkout'])){
		return true;
	}
	return $val;
};

add_filter('woocommerce_is_cart', 'bbh_is_cart_on_account', 10);
function bbh_is_cart_on_account($val) {
	global $wp_query;
	if(is_account_page() && isset($wp_query->query['cart'])){
		return true;
	}
	return $val;
};


/*=============================================
          = Add wrapper to my account nav =
===============================================*/
add_action('woocommerce_before_account_navigation', 'bbh_myaccount_nav_wrapper_start', 5);
function bbh_myaccount_nav_wrapper_start() {
echo "<div class='account-nav-wrapper'>";
};


add_action('woocommerce_after_account_navigation', 'bbh_myaccount_nav_wrapper_end', 55);
function bbh_myaccount_nav_wrapper_end() {
echo "</div>";
};


/*=============================================
          = Add account welcome message =
===============================================*/
add_action('woocommerce_before_account_navigation', 'bbh_woo_myaccount_welcome', 10);
function bbh_woo_myaccount_welcome() {
	$user_id = get_current_user_id();
	$name = '';
	$user_info = $user_id ? new WP_User( $user_id ) : wp_get_current_user();
	if ( $user_info->first_name ) {
		if ( $user_info->last_name ) {
			$name = $user_info->first_name . ' ' . $user_info->last_name;
		}
		$name = $user_info->first_name;
	} else{
		$name = $user_info->display_name;
	}

	echo sprintf(
		'<div class="welcome">
		<h4 class="welcome-message">
		%s,<br>
		%s
		</h4>
		</div>',
		__('Velkommen', 'bbh'),
		$name
	);


}
/*=============================================
          = Redirect to homepage after log out =
===============================================*/
add_action('wp_logout','bbh_redirect_after_logout');
function bbh_redirect_after_logout(){
  	wp_redirect( home_url() );
  	exit();
}


/*=============================================
          = Change checkout to account =
===============================================*/
add_filter( 'woocommerce_get_checkout_url', 'custom_checkout_url', 30 );
function custom_checkout_url( $checkout_url ) {
	if(is_cart()){
		// Define your product categories (term Id, slug or name)
		$checkout_url = esc_url( wc_get_account_endpoint_url( 'checkout' ) );
	}

    return $checkout_url;
}


add_filter( 'woocommerce_get_cart_url', 'custom_cart_url', 30 );
function custom_cart_url( $cart_url ) {

    // Define your product categories (term Id, slug or name)
    $custom_url = esc_url( wc_get_account_endpoint_url( 'cart' ) );

    return $custom_url;
}






/*====================================================
          = Change checkout so there is no Last Name =
=====================================================*/

add_filter( 'woocommerce_billing_fields' , 'ced_remove_billing_fields' );
function ced_remove_billing_fields( $fields ) {
         unset($fields['billing_last_name']);
         return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'ced_rename_checkout_fields' );
// Change placeholder and label text
function ced_rename_checkout_fields( $fields ) {
$fields['billing']['billing_first_name']['placeholder'] = 'Navn';
$fields['billing']['billing_first_name']['label'] = 'Navn';
return $fields;
}


/*===========================================================
    = Add countinue shopping button on "my indkøbskurv" =
============================================================*/
add_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_shop_more', 20 );
function woocommerce_button_shop_more(){ ?>
	<a href="https://vinforsyning.dk/shop/" class="checkout-button button alt wc-forward">
	Shop videre</a>
<?php }


// Displaying quantity setting fields on admin product pages
add_action( 'woocommerce_product_options_pricing', 'wc_qty_add_product_field' );
function wc_qty_add_product_field() {
    global $product_object;

    $values = $product_object->get_meta('_qty_args');

    echo '</div><div class="options_group quantity hide_if_grouped">
    <style>div.qty-args.hidden { display:none; }</style>';

    woocommerce_wp_checkbox( array( // Checkbox.
        'id'            => 'qty_args',
        'label'         => __( 'Kolli', 'woocommerce' ),
        'value'         => empty($values) ? 'no' : 'yes',
        'description'   => __( 'Aktiver dette for at vise og aktivere kolli felter indstillinger.', 'woocommerce' ),
    ) );

    echo '<div class="qty-args hidden">';

    woocommerce_wp_text_input( array(
            'id'                => 'qty_min',
            'type'              => 'number',
            'label'             => __( 'Minimum Kolli', 'woocommerce-max-quantity' ),
            'placeholder'       => '',
            'desc_tip'          => 'true',
            'description'       => __( 'Indstil en mindste tilladte mængdegrænse (et tal større end 0). ',' Woocommerce' ),
            'custom_attributes' => array( 'step'  => 'any', 'min'   => '0'),
            'value'             => isset($values['qty_min']) && $values['qty_min'] > 0 ? (int) $values['qty_min'] : 0,
    ) );

    woocommerce_wp_text_input( array(
            'id'                => 'qty_max',
            'type'              => 'number',
            'label'             => __( 'Maximum Kolli', 'woocommerce-max-quantity' ),
            'placeholder'       => '',
            'desc_tip'          => 'true',
            'description'       => __( 'Indstil den maksimalt tilladte mængdegrænse (et tal større end 0). Værdien "-1" er ubegrænset ',' woocommerce' ),
            'custom_attributes' => array( 'step'  => 'any', 'min'   => '-1'),
            'value'             => isset($values['qty_max']) && $values['qty_max'] > 0 ? (int) $values['qty_max'] : -1,
    ) );

    woocommerce_wp_text_input( array(
            'id'                => 'qty_step',
            'type'              => 'number',
            'label'             => __( 'Kolli trin', 'woocommerce-quantity-step' ),
            'placeholder'       => '',
            'desc_tip'          => 'true',
            'description'       => __( 'Valgfri. Indstil mængdetrin (et tal større end 0)', 'woocommerce' ),
            'custom_attributes' => array( 'step'  => 'any', 'min'   => '1'),
            'value'             => isset($values['qty_step']) && $values['qty_step'] > 1 ? (int) $values['qty_step'] : 1,
    ) );

    echo '</div>';
}

// Show/hide setting fields (admin product pages)
add_action( 'admin_footer', 'product_type_selector_filter_callback' );
function product_type_selector_filter_callback() {
    global $pagenow, $post_type;

    if( in_array($pagenow, array('post-new.php', 'post.php') ) && $post_type === 'product' ) :
    ?>
    <script>
    jQuery(function($){
        if( $('input#qty_args').is(':checked') && $('div.qty-args').hasClass('hidden') ) {
            $('div.qty-args').removeClass('hidden')
        }
        $('input#qty_args').click(function(){
            if( $(this).is(':checked') && $('div.qty-args').hasClass('hidden')) {
                $('div.qty-args').removeClass('hidden');
            } else if( ! $(this).is(':checked') && ! $('div.qty-args').hasClass('hidden')) {
                $('div.qty-args').addClass('hidden');
            }
        });
    });
    </script>
    <?php
    endif;
}

// Save quantity setting fields values
add_action( 'woocommerce_admin_process_product_object', 'wc_save_product_quantity_settings' );
function wc_save_product_quantity_settings( $product ) {
    if ( isset($_POST['qty_args']) ) {
        $values = $product->get_meta('_qty_args');

        $product->update_meta_data( '_qty_args', array(
            'qty_min' => isset($_POST['qty_min']) && $_POST['qty_min'] > 0 ? (int) wc_clean($_POST['qty_min']) : 0,
            'qty_max' => isset($_POST['qty_max']) && $_POST['qty_max'] > 0 ? (int) wc_clean($_POST['qty_max']) : -1,
            'qty_step' => isset($_POST['qty_step']) && $_POST['qty_step'] > 1 ? (int) wc_clean($_POST['qty_step']) : 1,
        ) );
    } else {
        $product->update_meta_data( '_qty_args', array() );
    }
}

// The quantity settings in action on front end
add_filter( 'woocommerce_quantity_input_args', 'filter_wc_quantity_input_args', 99, 2 );
function filter_wc_quantity_input_args( $args, $product ) {
    if ( $product->is_type('variation') ) {
        $parent_product = wc_get_product( $product->get_parent_id() );
        $values  = $parent_product->get_meta( '_qty_args' );
    } else {
        $values  = $product->get_meta( '_qty_args' );
    }

    if ( ! empty( $values ) ) {
        // Min value
        if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
            $args['min_value'] = $values['qty_min'];

            if( ! is_cart() ) {
                $args['input_value'] = $values['qty_min']; // Starting value
            }
        }

        // Max value
        if ( isset( $values['qty_max'] ) && $values['qty_max'] > 0 ) {
            $args['max_value'] = $values['qty_max'];

            if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
                $args['max_value'] = min( $product->get_stock_quantity(), $args['max_value'] );
            }
        }

        // Step value
        if ( isset( $values['qty_step'] ) && $values['qty_step'] > 1 ) {
            $args['step'] = $values['qty_step'];
        }
    }
    return $args;
}

// Ajax add to cart, set "min quantity" as quantity on shop and archives pages
add_filter( 'woocommerce_loop_add_to_cart_args', 'filter_loop_add_to_cart_quantity_arg', 10, 2 );
function filter_loop_add_to_cart_quantity_arg( $args, $product ) {
    $values  = $product->get_meta( '_qty_args' );

    if ( ! empty( $values ) ) {
        // Min value
        if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
            $args['quantity'] = $values['qty_min'];
        }
    }
    return $args;
}

// The quantity settings in action on front end (For variable productsand their variations)
add_filter( 'woocommerce_available_variation', 'filter_wc_available_variation_price_html', 10, 3);
function filter_wc_available_variation_price_html( $data, $product, $variation ) {
    $values  = $product->get_meta( '_qty_args' );

    if ( ! empty( $values ) ) {
        if ( isset( $values['qty_min'] ) && $values['qty_min'] > 1 ) {
            $data['min_qty'] = $values['qty_min'];
        }

        if ( isset( $values['qty_max'] ) && $values['qty_max'] > 0 ) {
            $data['max_qty'] = $values['qty_max'];

            if ( $variation->managing_stock() && ! $variation->backorders_allowed() ) {
                $data['max_qty'] = min( $variation->get_stock_quantity(), $data['max_qty'] );
            }
        }
    }

    return $data;
}

/*===============================================
=      Account - ACF Frontend Repeater - Email list       =
===============================================*/
// https://www.advancedcustomfields.com/resources/acf_form/
// https://support.advancedcustomfields.com/forums/topic/acf-form-repeater-field/
add_action('init', 'bbh_acf_form_head_init');
function bbh_acf_form_head_init(){
	acf_form_head();
}

add_action( 'woocommerce_after_edit_account_form', 'bbh_acf_frontend_email_repeater' );
function bbh_acf_frontend_email_repeater(){
$user_id = get_current_user_id();
	acf_form(array(
		'id' => 'bbh-frontend-email-repeater',
		'form' => true,
		'html_before_fields' => '<h3>Email liste - Ordrebekræftelse</h3>',
		'submit_value' => __("Gem ændringer", 'acf'),
		'post_id' => 'user_'.$user_id,
		'fields' => array('field_618b804adc94b'),
		'updated_message' => false,
		)
   	);
};


/*===============================================
=          Vinskolen           =
===============================================*/

// function my_acf_render_field( $field ) {
// 	$current_user_id = get_current_user_id();
// 	$vinskolen_array = bbh_find_vinskole_on_customer_id($current_user_id);
// 	$vinskolen_featured = get_field('vinskolen_featured', 'user_1');
// 	echo '<pre id="vardump">';
// 	var_dump($vinskolen_featured);
// 	var_dump($vinskolen_array);
// 	echo '</pre>';
// }
//
// // Apply to all fields.
// add_action('acf/render_field/name=vinskolen_featured', 'my_acf_render_field');
//
// function bbh_vinskole_load_from_customer_id( $value, $post_id, $field ) {
//
// 	$current_user_id = get_current_user_id();
// 	$vinskolen_array = bbh_find_vinskole_on_customer_id(1);
//
//     return $value;
// }
//
// // Apply to all fields.
// add_filter('acf/load_value/name=vinskolen_featured', 'bbh_vinskole_load_from_customer_id', 10, 3);


function bbh_find_vinskole_on_customer_id($current_user_id){
	$customer_id = get_field('customer_number', 'user_'.$current_user_id);
	$args = array(
	    'fields' => 'ids',
	    'blog_id' => 0,
	    'meta_query' => array(
	        array(
	          'key'     => 'customer_number',
	          'value'   => $customer_id,
	          'compare' => 'LIKE'
	        ),
	    )
	);
	$users = get_users( $args );

	$vinskolen_array = array();

	foreach ($users as $user => $id) {
	    $user_id = $id;
	    // $vinskolen_featured = get_field('vinskolen_featured', 'user_'.$user_id);
		$vinskolen_vinkort = get_field('vinskolen_vinkort', 'user_'.$user_id);
		if ($vinskolen_vinkort) {
			foreach ($vinskolen_vinkort as $vinkort) {

				$vinskolen_array[] = array(
	    		"vinskolen_headline" => $vinkort['vinskolen_headline'],
	    		"vinskolen_featured" => $vinkort['vinskolen_featured'],
				);

			}
		}

	    // if ($vinskolen_featured) {
	    //     $vinskolen_array = $vinskolen_featured;
	    // }

	}
	return $vinskolen_array;
}



// add_action('after_switch_theme', 'bbh_create_customer_id_tables', 10);
// function bbh_create_customer_id_tables() {
// 	global $wpdb;
//
// 	$charset_collate = $wpdb->get_charset_collate();
// 	$table_name = $wpdb->prefix . 'bbh_customer_id_meta';
// 	$sql = "CREATE TABLE $table_name (
// 	  	customer_meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
// 		customer_id bigint(20) UNSIGNED NOT NULL,
// 		meta_key VARCHAR(255) NOT NULL,
// 	  	meta_value LONGTEXT NOT NULL,
// 	  	PRIMARY KEY (customer_meta_id)
// 	) $charset_collate;";
//
// 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// 	dbDelta( $sql );
// }
//
//
//
// add_filter('acf/pre_save_post', 'my_acf_pre_save_post', 10, 2);
// function my_acf_pre_save_post( $post_id, $form ) {
//     // Create post using $form and update $post_id.
// 	$current_user_id = get_current_user_id();
// 	$customer_id = get_field('customer_number', 'user_'.$current_user_id);
// 	$meta_key = 'vinskolen_featured2';
//
// 	global $wpdb;
//
// 	$table_name = $wpdb->prefix . 'bbh_customer_id_meta';
// 	$data = array('meta_key' => $meta_key, 'meta_value' => 'test12345');
// 	$where = array('meta_key' => $meta_key);
//
//
// 	$update = $wpdb->update($table_name, $data, $where);
// 	if ($update === FALSE || $update < 1) {
// 	    $wpdb->insert($table_name, $data);
// 	}
//
// 	return $post_id;
// }
