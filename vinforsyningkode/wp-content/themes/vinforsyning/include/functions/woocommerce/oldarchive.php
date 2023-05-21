<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/*=====================================================
= Run structure hooks on both wp and custom rest action =
=======================================================*/
/*
	Saving hooks in a function so we can do it in both wp and before we return REST API data.
 */
add_action('wp', 'bbh_setup_woo_archive_structure', 10);
add_action('bbh_run_woo_rest', 'bbh_setup_woo_archive_structure', 10);
function bbh_setup_woo_archive_structure(){
	add_action('woocommerce_before_shop_loop_item', 'bbh_archive_product_top', 11);

	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	add_action('woocommerce_before_shop_loop_item_title', 'bbh_woo_archive_image_and_meta', 10);

	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	add_action('woocommerce_before_shop_loop_item', 'bbh_woo_archive_product_title', 15);


	add_action('woocommerce_after_shop_loop_item', 'bbh_woo_archive_show_stock', 50);

	add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_loop_ajax_add_to_cart', 99, 2 );
	add_filter( 'woocommerce_product_add_to_cart_text', 'bbh_woo_archive_add_to_cart_text', 10 ,2 );


	remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);


	// These are the exact same as the default woocommerce ones, but need to be run in the rest call for some reason. Otherwise they are missing from the rest response.
	if(defined('REST_REQUEST') && REST_REQUEST == true ){
	   	add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	   	add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}

}

/*=============================================
          = Remove breadcrumbs =
===============================================*/
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

/*=============================================
          = Add product top data =
===============================================*/
function bbh_archive_product_top(){
	global $product;
	global $userFavourites;
	$isFavourite = in_array($product->get_ID(), $userFavourites);
	$product_group = $product->get_attribute( 'pa_product_group' );

	?>

	<div class="product-top-data">
		<div class="country">
			<?php
			$country = wp_get_post_terms($product->get_ID(), 'district', array('parent' => 0));
			if(count($country) == 0) :
				$term = get_the_terms($product->get_ID(), 'district');
				if($term) :
					$country = ($term[0]->parent == 0) ? $term : get_term($term[0]->parent, 'district');
				endif;
			endif;
			if($country && is_array($country)) :
				$country = $country[0];
			endif;

			$countryCode = get_field( 'country_code', $country );
			if($countryCode){
				$countryCode = get_stylesheet_directory_uri().'/assets/images/flags/'.mb_strtoupper($countryCode).'.png';
				echo "<img class='country-flag' src='{$countryCode}'>";
			} ?>
		</div>
		<div class="product-group">
			<?php if ($product_group): ?>
				<?php echo $product_group ?>
			<?php endif; ?>
		</div>
		<div class="favourite">
			<?php if (is_user_logged_in()): ?>
				<div class="favourite-btn <?php echo $isFavourite ? 'active' : ''; ?>" data-post-id="<?php the_id(); ?>"></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/*=============================================
          = product image and meta =
===============================================*/
function bbh_woo_archive_image_and_meta() {
	global $product;
	$year = get_field( 'wine_year' );
	$kolli = get_field( 'kolli' );
	$size = $product->get_attribute( 'pa_size' );
	$image = get_post_thumbnail_id();
	$placeholder = get_option( 'woocommerce_placeholder_image', 0 );
	?>
	<div class="product-image-wrap">
		<div class="image">
			<img <?php lazySrc( ( $image ?: $placeholder ) ) ?> data-sizes="auto" alt="" class="lazyload">
		</div>
		<div class="meta">
			<?php if ($year):
				echo sprintf(
					'<span class="wine-year">%s %s</span>',
					__('Årgang', 'bbh'),
					$year
				);
			endif; ?>
			<?php if ($size):
				echo sprintf(
					'<span class="wine-size">%s</span>',
					$size
				);
			endif; ?>
			<?php if ($kolli):
				echo sprintf(
					'<span class="wine-year">%s %s</span>',
					__('Kolli', 'bbh'),
					$kolli
				);
			endif; ?>
		</div>
	</div>
	<?php
};

/*=============================================
          = Product title =
===============================================*/
function bbh_woo_archive_product_title() {
	$districtIds = wp_get_post_terms(get_the_id(), 'district', array('fields' => 'ids'));
	$show_district = false;
	?>
	<div class="product-title">
		<?php woocommerce_template_loop_product_title(); ?>
	</div>
	<?php if ($districtIds && $show_district):
		?>
		<div class="product-district">
			<?php foreach ($districtIds as $term_id):
				$ancestors = get_ancestors($term_id, 'district');

				if( count($ancestors) > 1){
					$display = get_term( $ancestors[0] )->name;
					break;
				} else {
					$display = get_term($term_id)->name;
				}
				?>
			<?php endforeach; ?>
			<?php if (isset($display)): ?>
				<div class="district">
					<?php
						echo $display;
						echo '<br>';
						echo $manufacturer = get_lowest_level_post_term('manufacturer')->name;
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
};
/*=============================================
          = Quantity on archive =
===============================================*/
//See more info https://stackoverflow.com/questions/48722178/add-a-quantity-field-to-ajax-add-to-cart-button-on-woocommerce-shop-page
function quantity_inputs_for_loop_ajax_add_to_cart( $html, $product ) {
	//only show add to cart if user is logged in
	if(is_user_logged_in()) :
	    if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
	        // Get the necessary classes
	        $class = implode( ' ', array_filter( array(
	            'button',
	            'product_type_' . $product->get_type(),
	            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
	            $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
	        ) ) );
				// Embedding the quantity field to Ajax add to cart button
		        $html = sprintf( '<div class="add-to-cart-wrap">%s<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a></div>',
		            woocommerce_quantity_input( array('min_value' => 1), $product, false ),
		            esc_url( $product->add_to_cart_url() ),
		            esc_attr( isset( $quantity ) ? $quantity : 1 ),
		            esc_attr( $product->get_id() ),
		            esc_attr( $product->get_sku() ),
		            esc_attr( isset( $class ) ? $class : 'button' ),
		            esc_html( $product->add_to_cart_text() )
		        );
	    }
		else{
			$html = sprintf('<div class="add-to-cart-wrap">%s</div>',
				$html
			);
		}
	else :
		$html = '<span class="login-btn">Log ind</span>';
	endif;
    return $html;
}

add_action( 'wp_footer' , 'archives_quantity_fields_script' );

function archives_quantity_fields_script(){
    //if( is_shop() || is_product_category() || is_product_tag() ): ?>
    <script type='text/javascript'>
		jQuery( document ).ajaxComplete(function() {
		 	update_data_quantity()
		});
		function update_data_quantity(){
			jQuery( '.quantity .qty' ).each(function(){
				jQuery(this).parent( '.quantity' ).next( '.add_to_cart_button' ).attr( 'data-quantity', jQuery( this ).val() );
			})
		}
        jQuery( document ).ready( function( $ ) {
		update_data_quantity();
        jQuery( document ).on( 'change', '.quantity .qty', function() {
            jQuery( this ).parent( '.quantity' ).next( '.add_to_cart_button' ).attr( 'data-quantity', jQuery( this ).val() );
            //alert("Changed");
        });
    });

        jQuery(function($) {
            // Update quantity on 'a.button' in 'data-quantity' attribute (for ajax)
            $(".add_to_cart_button.product_type_simple").on('click', function() {
                var $button = $(this);
                $button.data('quantity', $button.parent().find('input.qty').val());
            });
            // remove old "view cart" text, only need latest one thanks!
            $(document.body).on("adding_to_cart", function() {
                $("a.added_to_cart").remove();
            });
        });
    </script>
    <?php //endif;
}


/*=============================================
          = Add stock display =
===============================================*/
function bbh_woo_archive_show_stock() {
	//only show stock if logged in
	if(is_user_logged_in()) :
		global $product;

		//general stock fields
		$useStock = $product->get_manage_stock();
		$stock = $product->get_stock_quantity();
		$status = $product->get_stock_status();
		$temp_stock = get_field('temporary_out_of_stock',$product->get_id());
		//reservations
		if($stock > 0) {
			//subtract reservations
			$total_reservations = get_field('total_reserved',$product->get_id());
			if($total_reservations > 0){
				$stock = $stock - $total_reservations;
			}
			//add user reserved stock to available stock
			$user_id = get_current_user_id();
			$customer_id = get_user_meta($user_id,'customer_number');
			$rows = get_field('reservations', $product->get_id());
			if ($rows) {
				if (is_string($rows)) {
					// new - string
					for ($i = 0; $i <= $rows-1; $i++) {
						$res_user_id = get_field('reservations_'.$i.'_user_id', $product->get_id());
						$res_quantity = 0;
						$res_quantity += get_field('reservations_'.$i.'_quantity', $product->get_id());
						if ($customer_id[0] == $res_user_id) {
							$stock += $res_quantity;
						}
					}
				}else{
					// old - array
					if (have_rows('reservations',$product->get_id())) {
						while (have_rows('reservations',$product->get_id())) {
							the_row();
							if (get_sub_field('user_id') == $customer_id[0]) {
								$stock += get_sub_field('quantity');
							}
						}
					}
				}
			}
		}
		//markup
		if ($useStock == true) {
			//in stock
			if($stock > 0) {
				$stockMessage = sprintf(
					__('%s på lager', 'bbh'),
					$stock >= 120 ? '120+' : $stock
				);
			}
			//temporary out of stock
			if($stock == 0 && $temp_stock == true) {
				$stockMessage = 'På vej hjem';
				$status = 'outofstock';
			}
			//out of stock
			if($stock == 0 && $temp_stock == false) {
				$stockMessage = 'Udsolgt';
				$status = 'outofstock';
			}
			echo sprintf(
				'<div class="stock %s">%s</div>',
				$status,
				$stockMessage
			);
		} elseif($useStock == false){
			switch ($status) {
				case 'instock':
					$stockMessage = __('På lager', 'bbh');
					break;
				case 'outofstock':
					$stockMessage = __('Ikke på lager', 'bbh');
					break;
				case 'onbackorder':
					$stockMessage = __('På restordre', 'bbh');
					break;
				default:
					$stockMessage = __('På lager', 'bbh');
					break;
			}
			echo sprintf(
				'<div class="stock %s">%s</div>',
				$status,
				$stockMessage
			);
		}
	endif;
};


/*=============================================
          = Change add to cart button text =
===============================================*/
// To change add to cart text on product archives(Collection) page
function bbh_woo_archive_add_to_cart_text($string, $product) {
	if($product->get_type() == 'simple' && $product->is_purchasable() && ($product->get_stock_quantity() > 0 || $product->backorders_allowed() || $product->is_in_stock())){
		return __( 'Tilføj', 'bbh' );
	} else{
		return __('Se mere', 'bbh');
	}
}
function add_cond_to_where( $where ) {

	   //Replace showings_$ with repeater_slug_$
	   $where = str_replace("meta_key = 'exclusive_wine_$", "meta_key LIKE 'exclusive_wine_%", $where);

	   return $where;
   }

add_filter('posts_where', 'add_cond_to_where');
/*===============================================
= Add meta_query to standard woocommerce params =
(First view)
===============================================*/
function bbh_expand_product_query( $q ) {
	$meta_query = $q->get( 'meta_query' );
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => '_stock_status',
				'value' => 'instock'
			),
			array(
				'key' => 'temporary_out_of_stock',
				'value' => '1',
			),
		);
		$meta_query[] = array(
			'key' => '_price',
			'value' => 0,
			'compare' => '>',
			'type' => 'NUMERIC'
		);
		$meta_query[] = array(
			'key' => '_price',
			'value' => 100000,
			'compare' => '<',
			'type' => 'NUMERIC'
		);
		$meta_query[] = array(
			'key' => 'vivino',
			'value' => 'ehandel',
			'compare' => 'LIKE'
		);
		$q->set( 'meta_query', $meta_query );
}
add_action( 'woocommerce_product_query', 'bbh_expand_product_query' );

add_action( 'woocommerce_product_query', 'bbh_hide_product_by_exclusive' );
function bbh_hide_product_by_exclusive( $query ) {
	$query->set( 'post__not_in', get_all_exlusive_products());
}

function wpd_sort_by_meta( $query ) {
        $query->set( 'meta_key', '_stock' );
        $query->set( 'orderby', 'meta_value_num' );
		$query->set('order', 'desc');
}
add_action( 'woocommerce_product_query', 'wpd_sort_by_meta' );



add_filter( 'woocommerce_post_class', 'add_attribute_to_product_classes', 21, 3 ); //woocommerce use priority 20, so if you want to do
function add_attribute_to_product_classes( $classes ) {
    if ( 'product' == get_post_type() ) {
		global $product;
		$secondary_cat = $product->get_attribute('secondary_cat');
		if ($secondary_cat && strpos($secondary_cat, 'Outlet') !== false ) {
			$classes[] = 'outlet';
		}
    }
    return $classes;
}


function add_attribute_badge_to_item(  ) {
	global $product;
	$secondary_cat = $product->get_attribute('secondary_cat');
	if ($secondary_cat && strpos($secondary_cat, 'Outlet') !== false ) {
		echo '<div class="badge outlet-discount">Outlet</div>';
	}
};
add_action( 'woocommerce_before_shop_loop_item', 'add_attribute_badge_to_item', 10, 0 );
