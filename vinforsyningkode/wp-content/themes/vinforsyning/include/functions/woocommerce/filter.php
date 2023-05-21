<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;




/*=============================================
          = Add filter to shop page =
===============================================*/

add_action('woocommerce_before_shop_loop', 'bbh_woo_shop_filter', 10);
function bbh_woo_shop_filter(){
	if(!is_shop()){
		return;
	}

	if(file_exists(get_stylesheet_directory() . '/template-parts/shop-filter.php')){
		wp_enqueue_script('nouislider');
		wp_enqueue_script('bbh_shop_filter');
		include(get_stylesheet_directory() . '/template-parts/shop-filter.php');
	}

}


/*=============================================
          = remove pagination =
===============================================*/
add_action('wp', 'bbh_remove_pagination_shop_filter', 10);
function bbh_remove_pagination_shop_filter() {
	if(is_shop()){
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}
};




/*=============================================
          = Add more btn after loop =
===============================================*/
add_action('woocommerce_after_shop_loop', 'bbh_add_filter_more_btn', 1);
function bbh_add_filter_more_btn() {
	global $wp_query;
	if(!is_shop()){
		return;
	}
	$more = ($wp_query->found_posts > $wp_query->post_count) ? '' : 'hidden';

	echo sprintf(
		'<div class="filter-more"><a href="#" class="more-btn %1$s">%2$s</a></div>',
		$more,
		__('Vis flere', 'bbh')
	);
};

/*=============================================
          = Add body class if search =
===============================================*/
add_filter('body_class', 'bbh_filter_search_body_class',10, 1);
function bbh_filter_search_body_class($classes){
	if(isset($_GET['search']) && $_GET['search'] !== ''){
		$classes[] = 'filter-search';
		$classes[] = 'hide-search-bar';
	}
	return $classes;
}

/*=============================================
          = Get term input list =
===============================================*/

function bbh_tax_list($args = array()){
	$defaults = array(
		'taxonomy' => 'product_cat',
		'type' => 'checkbox',
		'include_children' => 'true',
		'hide_empty' => true,
		'orderby' => 'title',
		'order' => 'ASC',
		'echo' => false,
		'parent' => 0,
	);
	$args = wp_parse_args($args, $defaults);


	$terms = get_terms(array(
		'taxonomy' => $args['taxonomy'],
		'include_children' => $args['include_children'],
		'hide_empty' => $args['hide_empty'],
		'orderby' => $args['orderby'],
		'order' => $args['order'],
		'parent' => $args['parent']
	));
	if(!$terms){
		return false;
	}

	ob_start();

	echo '<div class="input-list type-'.$args['type'].' tax-'.$args['taxonomy'].'">';
		foreach($terms as $term):
			$id = $args['taxonomy'].'_'.$term->term_id;
			$type = $args['type'];
			$name = $args['type'] == 'checkbox' ? $args['taxonomy'].'[]' : $args['taxonomy'];
			$title = $term->name;
			//remove commas from cl size
			$title = str_replace(',00','',$title);
			echo sprintf(
				'<div class="input-wrap"><label for="%1$s"><input type="%2$s" name="%3$s" id="%1$s" value="%4$s"><span class="input-title"><span class="input-title-el" title="%5$s" data-waschecked="true">%5$s</span></span></label></div>',
				$id,
				$type,
				$name,
				$term->term_id,
				$title
			);
		endforeach;
	echo '</div>';

	$html = ob_get_clean();


	if($args['echo']){
		echo $html;
		return true;
	}

	return $html;

}

/*=============================================
          = Build popup box for filter =
===============================================*/


function bbh_filter_box($name = '', $title = '', $icon = '', $content = ''){
	?>
	<div class="filter-box-wrap <?php echo $name ?>">
		<div class="filter-header trigger">
			<div class="filter-title">
				<?php echo $title; ?>
			</div>
			<span class="dropdown-icon <?php echo $icon ?>"></span>
		</div>
		<div class="filter-content">
			<?php echo $content ?>
		</div>
	</div>
	<?php
}



function bbh_filter_range_slider($args = array()){
	$args = wp_parse_args($args, array(
			'name' => 'slider',
			'min' => 0,
			'max' => 100,
			'step' => 1,
			'echo' => true
		)
	);
	ob_start();
	?>
	<div id="<?php echo $args['name'] ?>-slider" class="filter-range-slider">

		<input type="text" name="<?php echo $args['name']; ?>-from" id="<?php echo $args['name']; ?>-slider-from" value="<?php echo esc_attr($args['min']) ?>" class="slider-from">

		<input type="text" name="<?php echo $args['name']; ?>-to" id="<?php echo $args['name']; ?>-slider-to" value="<?php echo esc_attr($args['max']) ?>" class="slider-to">

		<input type="hidden" name="<?php echo $args['name']; ?>-from-val" id="<?php echo $args['name']; ?>-slider-from-val" class="slider-from-val" value="">

		<input type="hidden" name="<?php echo $args['name']; ?>-to-val" id="<?php echo $args['name']; ?>-slider-to-val" class="slider-to-val">

		<div class="slider-el" id="<?php echo $args['name']; ?>-slider-el" data-to-start="<?php echo esc_attr($args['max']) ?>" data-from-start="<?php echo esc_attr($args['min']) ?>" data-step="<?php echo esc_attr($args['step']) ?>"></div>

	</div>
	<?php
	if($args['echo']){
		echo ob_get_clean();
		return true;
	}

	return ob_get_clean();
}

/*=============================================
          = Add custom orderby options =
===============================================*/

add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args', 99, 1 );
function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	// Årgang
	if ( 'year' == $orderby_value ) {
		$args['orderby'] = 'meta_value_num';
		$args['order'] = '';
		$args['meta_key'] = 'wine_year';
	}

	// CL
	if ( 'cl' == $orderby_value ) {
		$args['orderby'] = 'meta_value_num';
		$args['order'] = '';
		$args['meta_key'] = '_cl_size';
	}

	// Kolli
	if ( 'kolli' == $orderby_value ) {
		$args['orderby'] = 'meta_value_num';
		$args['order'] = '';
		$args['meta_key'] = 'kolli';
	}

	// Pris
	if ( 'price' == $orderby_value ) {
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'desc';
		$args['meta_key'] = '_price';
	}

	// Price custom
	if ( 'product_price' == $orderby_value ) {
		if(is_user_logged_in()){
			$key = 'asc';
			$userID = get_current_user_id();
			if(isset($_GET['order'])){
				$key = mb_strtolower($_GET['order']);
			}
			$trans = get_transient('user_pricing_list_'.$userID);
			if($key == 'desc'){
				$trans = array_reverse($trans);
			}
			$args['post__in'] = $trans;
			$args['orderby'] = 'post__in';
			$args['order'] = 'asc';
		} else{
			$args['orderby'] = 'meta_value_num';
			$args['order'] = 'asc';
			$args['meta_key'] = '_regular_price';
		}
	}

	// Lager
	if ( 'stock' == $orderby_value ) {
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'desc';
		$args['meta_key'] = '_stock';
	}

	if(isset($_GET['order']) &&  'product_price' != $orderby_value){
		$args['order'] = $_GET['order'];
	}
	return $args;
}


/*=============================================
      = Filter woocommerce orderby options =
===============================================*/

add_filter( 'woocommerce_default_catalog_orderby_options', 'bbh_custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'bbh_custom_woocommerce_catalog_orderby' );
function bbh_custom_woocommerce_catalog_orderby( $sortby ) {
	unset($sortby['price']);
	unset($sortby['popularity']);
	unset($sortby['price-desc']);
	unset($sortby['menu_order']);

	$sortby['product_price'] = __('Pris', 'bbh'); // named product price so the built in 'price' doesn't interfere with order
	$sortby['year'] = __('År', 'bbh');
	$sortby['cl'] = __('Cl', 'bbh');
	$sortby['country'] = __('Land', 'bbh');
	$sortby['stock'] = __('Lager', 'bbh');
	$sortby['kolli'] = __('Kolli', 'bbh');
	$sortby['date'] = __('Nyeste', 'bbh');



	return $sortby;
}
/*=============================================
  = Filter by price on static archive pages =
===============================================*/

add_action( 'woocommerce_product_query', 'custom_product_price_ordering', 10, 2 );
function custom_product_price_ordering( $q, $that ) {
	if(!is_user_logged_in()){
		return $q;
	}
	if(isset($q->query['orderby']) && $q->query['orderby'] == 'product_price'){
		$key = isset($q->query['order']) ? $q->query['order'] : 'asc';
		$userID = get_current_user_id();
		$trans = get_transient('user_pricing_list_'.$userID);
		if($trans){
			if(mb_strtolower($key) == 'desc'){
				$trans = array_reverse($trans);
			}
			$q->set('post__in', $trans);
		}
	}
}


// add_filter( 'woocommerce_thankyou_order_received_text', 'webroom_change_thankyou_sub_title', 20, 2 );
//
// function webroom_change_thankyou_sub_title( $thank_you_title, $order ){
//
// 	return $order->get_billing_first_name() . ', thank you very much for your order!';
//
// }

// add this to functions.php
//register acf fields to Wordpress API
//https://support.advancedcustomfields.com/forums/topic/json-rest-api-and-acf/

/*=============================================
			= Exclusive products =
===============================================*/

function get_all_exlusive_products(){
	if (!is_admin()) {
	$user_id = get_current_user_id();
	$customer_id = get_field('customer_number', 'user_'.$user_id);
	//$customer_no = get_field('customer_number', 'user_'.$user_id);
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'meta_query' => array(
			array(
		    	'key' => 'exclusive_wine_$_user_id',
		    	'value' => "",
		    	'compare' => '!='
		    ),
		),
	);
	$exlusive_array = array();

	$the_query = new WP_Query( $args );
	if ($the_query->have_posts()):
		while($the_query->have_posts()): $the_query->the_post();
		$product_id = get_the_ID();
		$rows = get_field('exclusive_wine', $product_id);
		if ($rows) {
			if (is_string($rows)) {
				// new - string
				$eligible = false;
				$ex_user_array = array();
				for ($i = 0; $i <= $rows-1; $i++) {
					$ex_user_id = get_field('exclusive_wine_'.$i.'_user_id', $product_id);
					$ex_user_array[] = $ex_user_id;
					if ($customer_id == $ex_user_id) {
						$eligible = "true";
					}
				}
				if ($eligible == false) {
					array_push($exlusive_array, $product_id);
				}
			}else{
				$eligible = false;
				$arr_user_id = array();
				if (have_rows('exclusive_wine',$product_id)) {
					$eligible = false;
					while (have_rows('exclusive_wine',$product_id)) {
						the_row();
						if (get_sub_field('user_id') == $customer_id) {
							$eligible = true;
						}
					}
				}
				if ($eligible == false) {
					array_push($exlusive_array, $product_id);
				}
			}
		}

		endwhile;
		wp_reset_postdata();
 	endif;

	return $exlusive_array;
	}
}




// $customer_id = get_user_meta($user_id,'customer_number');
// $customer_no = get_field('customer_number', 'user_'.$user_id);
// $exclusive_wine = get_field('exclusive_wine', $product_id);
// $rows = get_field('exclusive_wine', $product_id);
// if ($rows) {
// 	if (is_string($rows)) {
// 		// new - string
// 		$eligible = false;
// 		for ($i = 0; $i <= $rows-1; $i++) {
// 			$ex_user_id = get_field('exclusive_wine_'.$i.'_user_id', $product_id);
// 			if ($customer_no[0] == $ex_user_id) {
// 				$eligible = true;
// 			}
// 		}
// 		if ($eligible == false) {
// 			array_push($exlusive_array, $product_id);
// 		}
// 	}else{
// 		// old - array
// 		$eligible = false;
// 		foreach ($exclusive_wine as $value) {
// 			$product_customer_id = $value['user_id'];
// 			if ($product_customer_id == $customer_id) {
// 				$eligible = true;
// 			}
// 		}
// 		if ($eligible == false) {
// 			array_push($exlusive_array, $product_id);
// 		}
// 	}
// }

/**
 * Search Within a Taxonomy
 *
 * Support search with tax_query args
 *
 * $query = new WP_Query( array(
 *  'search_tax_query' => true,
 *  's' => $keywords,
 *  'tax_query' => array( array(
 *      'taxonomy' => 'country',
 *      'field' => 'id',
 *      'terms' => $country,
 *  ) ),
 * ) );
 */
class WP_Query_Taxonomy_Search {
    public function __construct() {
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    public function pre_get_posts( $q ) {
        if ( is_admin() ) return;

        $wp_query_search_tax_query = filter_var(
            $q->get( 'search_tax_query' ),
            FILTER_VALIDATE_BOOLEAN
        );

        // WP_Query has 'tax_query', 's' and custom 'search_tax_query' argument passed
        if ( $wp_query_search_tax_query && $q->get( 'tax_query' ) && $q->get( 's' ) ) {
            add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 1 );
        }
    }

    public function posts_groupby( $groupby ) {
        return '';
    }
}

new WP_Query_Taxonomy_Search();

/*=============================================
			= Filter add search by sku =
===============================================*/
/* Add sku to product search */
function search_by_sku( $searcht, $query_varss ) {
    global $wpdb;
    if(isset($query_varss->query['s']) && !empty($query_varss->query['s'])){
        $args = array(
            'posts_per_page'  => -1,
            'post_type'       => 'product',
            'meta_query' => array(
                array(
                    'key' => '_sku',
                    'value' => $query_varss->query['s'],
                    'compare' => 'LIKE'
                )
            )
        );
        $posts = get_posts($args);
        if(empty($posts)) return $searcht;
        $get_post_ids = array();
        foreach($posts as $post){
            $get_post_ids[] = $post->ID;
        }
        if(sizeof( $get_post_ids ) > 0 ) {
                $searcht = str_replace( 'AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode( ',', $get_post_ids ) . ")) OR (", $searcht);
        }
    }
    return $searcht;

}
add_filter( 'posts_search', 'search_by_sku', 999, 2 );
