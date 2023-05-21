<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('rest_api_init', 'bbh_register_shop_filter_rest_routes', 10);
function bbh_register_shop_filter_rest_routes(){
	// path = domain.com/wp-json/vinforsyning/v1/shop_filter
	register_rest_route(
		'vinforsyning/v1', // base
		'shop_filter', // route
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbh_shop_filter_search_callback',
			'permission_callback' => '__return_true'

		)
	);
	/*----------- refresh list of products -----------*/
	register_rest_route(
		'vinforsyning/v1', // base
		'refresh_products', // route
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbh_refresh_products',
			'permission_callback' => '__return_true'
		)
	);

	/*----------- refresh a single product -----------*/
	register_rest_route(
		'vinforsyning/v1', // base
		'refresh_product', // route
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbh_refresh_product',
			'permission_callback' => '__return_true'
		)
	);

	/*----------- Handler to refresh list of product -----------*/
	// TODO: Should break request into batches - Doesn't currently work.
	register_rest_route(
		'vinforsyning/v1', // base
		'refresh_products_handler', // route
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbh_refresh_products_handler',
			'permission_callback' => '__return_true'
		)
	);

	/*----------- Clear transient lists -----------*/
	register_rest_route(
		'vinforsyning/v1', // base
		'clear_pricelists', // route
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbh_clear_pricelists',
			'permission_callback' => '__return_true'
		)
	);

}


function bbh_shop_filter_search_callback($request){
	// Need to run this for our markup changes to be applied to rest response
	do_action('bbh_run_woo_rest');
	// Save all our values to variables.
	$search = $request->get_param( 'search' );
	$perPage = $request->get_param( 'posts_per_page' ) ? intval($request->get_param('posts_per_page')) : apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
	$page = $request->get_param( 'page' ) ? intval($request->get_param('page')) : 1;
	$token = $request->get_param('token');
	//$productCat = $request->get_param('product_cat');
	$secondCat = $request->get_param('pa_secondary_cat');
	$wineSize = $request->get_param('pa_size');
	$grapeType = $request->get_param('pa_grape_type');
	$productCatAtt = $request->get_param('pa_pro_cat_att');
	$countryAtt = $request->get_param('pa_country_att');
	$districtAtt = $request->get_param('pa_distrikt_att');
	$productGroup = $request->get_param('pa_product_group');
	$yearFrom = $request->get_param('year-from-val') ?: '0';
	$yearTo = $request->get_param('year-to-val');
	$priceFrom = $request->get_param('price-from-val') ?: 0;
	$priceTo = $request->get_param('price-to-val');
	$district = $request->get_param('district') ?: 0;
	$order = $request->get_param('order') ?: 'desc';
	if($priceTo == 500 || $priceTo == null){
		$priceTo = 9999999;
	}

	// Get base args from WooCommerce
	$baseQ = new WC_Shortcode_Products();
	$baseQ = $baseQ->get_query_args();

	$args = array(
		'post_type' => 'product',
		'fields' => 'all',
		'posts_per_page' => $perPage,
		'offset' => $perPage * ($page - 1),
		'order' => $order,
		'post_status' => 'publish',
		's' => $search,
		'no_found_rows' => false,
		'post_parent' => 0,
		'include_children' => false,

		// function from filter.php
		'meta_query' => array(
			// array(
		    //     'key' => '_sku',
		    //     'value' => 'instock',
		    //     'compare' => 'LIKE'
			// ),
			//'relation' => 'AND',
			array(
				'relation' => 'AND',
				array(
					'key' => '_price',
					'value' => 100000,
					'compare' => '<',
					'type' => 'NUMERIC'
				),
				array(
					'key' => '_price',
					'value' => 0,
					'compare' => '>',
					'type' => 'NUMERIC'
				),
				array(
	            	'key' => 'vivino',
	            	'value' => 'ehandel',
					'compare' => 'LIKE'
		        ),
			),
			array(
				'relation' => 'OR',
				array(
					'key' => '_stock_status',
					'value' => 'instock'
				),
				array(
					'key' => 'temporary_out_of_stock',
					'value' => '1',
				),
				// array(
			    //     'key' => '_sku',
			    //     'value' => $search,
			    //     'compare' => 'LIKE'
				// ),
			),
		),
	);
	$args = wp_parse_args($args, $baseQ);



	// If we're ordering by price
	if($args['orderby'] == 'product_price' || $args['orderby'] == 'post__in'){
		$key = 'user_pricing_list_'.get_current_user_id();
		$cache = get_transient($key);
		// We have a cached price list
		if($cache){
			// Flip it if descending
			if(mb_strtoupper($args['order']) == 'DESC'){
				$cache = array_reverse($cache);
			}
			$args['post__in'] = $cache;
			$args['orderby'] = 'post__in';
		} else{
			// Let default WooCommerce order functionality take over
			// This is handled in the regular woocommerce orderby hooks and filters
		}
	}

	// filter product category
	// if($productCat){
	// 	array_push(
	// 		$args['tax_query'],
	// 		array(
	// 			'taxonomy' => 'product_cat',
	// 			'terms' => $productCat
	// 		)
	// 	);
	// }
	// filter secondary cat
	if($secondCat){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_secondary_cat',
				'terms' => $secondCat,
				'operator' => 'AND' // This should be an AND relation so product have all specified attributes
			)
		);
	}

	// filter product group
	if($productGroup){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_product_group',
				'terms' => $productGroup
			)
		);
	}

	// product wine size attribute
	if($wineSize){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_size',
				'terms' => $wineSize
			)
		);
	}

	// product grape type attribute
	if($grapeType){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_grape_type',
				'terms' => $grapeType
			)
		);
	}

	//New product cat type attribute
	if($productCatAtt){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_pro_cat_att',
				'terms' => $productCatAtt
			)
		);
	}
	if($countryAtt){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_country_att',
				'terms' => $countryAtt
			)
		);
	}
	if($districtAtt){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'pa_distrikt_att',
				'terms' => $districtAtt
			)
		);
	}


	// filter product district
	if($district){
		array_push(
			$args['tax_query'],
			array(
				'taxonomy' => 'district',
				'terms' => $district,
				// 'orderby'   => $district,
        		// 'order' => 'ASC',
				// 'order'	=> 'ASC',
			)
		);
	}
	// if($district){
	// 	array_push(
	// 		$args['tax_query'],
	// 		array(
	// 			'relation' => 'AND',
	// 			array(
	// 				'taxonomy' => 'product_cat',
	// 				'terms' => $productCat
	// 			),
	// 			array(
	// 			'taxonomy' => 'district',
	// 			'terms' => $district,
	// 			'operator' => 'IN',
	// 			// 'orderby'   => $district,
    //     		// 'order' => 'ASC',
	// 			// 'order'	=> 'ASC',
	// 		),
	// 		)
	// 	);
	// }

	// filter wine production year
	if($yearFrom !== null && $yearTo){
		array_push(
			$args['meta_query'],
			array(
				'key' => 'wine_year',
				'compare' => 'BETWEEN',
				'value' => array($yearFrom, $yearTo),
				'type' => 'NUMERIC'
			)
		);
	}

	//filter price
	// if($priceFrom !== null && $priceTo){
	// 	array_push(
	// 		$args['meta_query'],
	// 		array(
	// 			'key' => '_regular_price',
	// 			'compare' => 'BETWEEN',
	// 			'value' => array($priceFrom, $priceTo),
	// 			'type' => 'NUMERIC'
	// 		)
	// 	);
	// }

	// if($priceFrom !== null && $priceTo){
	// 	$key = 'user_pricing_list_'.get_current_user_id();
	// 	$cache = get_transient($key);
	// 	$price_between = [];
	// 	if($args['orderby'] == 'product_price' || $args['orderby'] == 'post__in'){
	// 		// We have a cached price list
	// 		if($cache){
	// 			// Flip it if descending
	// 			if(mb_strtoupper($args['order']) == 'DESC'){
	// 				$cache = array_reverse($cache);
	// 			}
	// 		}
	// 	}
	// 	foreach ($cache as $product_id) {
	// 		//$product = wc_get_product( $product_id );
	// 		$price = bbh_filter_get_price_by_id(0, $product_id);
	// 		if ($price > $priceFrom  && $price < $priceTo) {
	// 			//$price_arr[] = $price;
	// 			$price_between[] = $product_id;
	// 		}
	// 	}
	// 	$args['post__in'] = $price_between;
	// }


	// Get the Query Object. This function returns cached result if available.
	//$term_query = bbh_get_term_query($args);

	// $data_args = $args;
	// $data_args['posts_per_page'] = -1;
	// $data_query = new WP_Query($data_args);
	// if ($data_query->have_posts()):
	// 	$terms_grape = array();
	// 	$terms_pro_cat = array();
	// 	$terms_size = array();
	// 	$terms_groupe = array();
	// 	$terms_country = array();
	// 	$terms_district = array();
	// 	while($data_query->have_posts()): $data_query->the_post();
	// 	/*Grape*/
	// 	$term_grape = get_the_terms(get_the_id(), 'pa_grape_type');
	// 	if (!in_array($term_grape[0]->term_id, $terms_grape)) {
	// 		if ($term_grape[0]->term_id != null) {
	// 			array_push($terms_grape, $term_grape[0]->term_id);
	// 		}
	// 	}
	// 	/* Product cat*/
	// 	$term_pro_cat = get_the_terms(get_the_id(), 'pa_pro_cat_att');
	// 	if (!in_array($term_pro_cat[0]->term_id, $terms_pro_cat)) {
	// 		if ($term_pro_cat[0]->term_id != null) {
	// 			array_push($terms_pro_cat, $term_pro_cat[0]->term_id);
	// 		}
	// 	}
	// 	/* Size*/
	// 	$term_size = get_the_terms(get_the_id(), 'pa_size');
	// 	if (!in_array($term_size[0]->term_id, $terms_size)) {
	// 		if ($term_size[0]->term_id != null) {
	// 			array_push($terms_size, $term_size[0]->term_id);
	// 		}
	// 	}
	// 	/* Groupe*/
	// 	$term_groupe = get_the_terms(get_the_id(), 'pa_product_group');
	// 	if (!in_array($term_groupe[0]->term_id, $terms_groupe)) {
	// 		if ($term_groupe[0]->term_id != null) {
	// 			array_push($terms_groupe, $term_groupe[0]->term_id);
	// 		}
	// 	}
	// 	/* Country*/
	// 	$term_country = get_the_terms(get_the_id(), 'pa_country_att');
	// 	if (!in_array($term_country[0]->term_id, $terms_country)) {
	// 		if ($term_country[0]->term_id != null) {
	// 			array_push($terms_country, $term_country[0]->term_id);
	// 		}
	// 	}
	// 	/* Distrikt*/
	// 	$term_distrikt = get_the_terms(get_the_id(), 'pa_distrikt_att');
	// 	if (!in_array($term_distrikt[0]->term_id, $terms_district)) {
	// 		if ($term_distrikt[0]->term_id != null) {
	// 			array_push($terms_district, $term_distrikt[0]->term_id);
	// 		}
	// 	}
	//
	// 	endwhile;
	// endif;


	//$exclude_exclusive = array_diff($args['post__in'], $exclusive_wine_array);
	//$args['post__in'] = array_diff($args['post__in'], $exclusive_wine_array);

	/* NEW FILTER BBH - SØ */
	$check_price = false;
	if($priceFrom !== 0 || $priceTo !== 9999999){
		$price_between = [];
		$check_price = true;
	}

	$bbh_term_array = false;
	if ($grapeType || $wineSize || $districtAtt || $productGroup || $productCatAtt || $countryAtt || $check_price) {
		$data_args = $args;
		$data_args['posts_per_page'] = -1;
		$bbh_data_query = new WP_Query($data_args);
		$bbh_taxes = array('pa_grape_type', 'pa_pro_cat_att', 'pa_size', 'pa_product_group', 'pa_country_att', 'pa_distrikt_att' );
		$bbh_term_array = array(
			'pa_grape_type' => array(),
			'pa_pro_cat_att' => array(),
			'pa_size' => array(),
			'pa_product_group' => array(),
			'pa_country_att' => array(),
			'pa_distrikt_att' => array(),
		);
		$bbh_price_array = array();

		if ($bbh_data_query->have_posts()):
			while($bbh_data_query->have_posts()): $bbh_data_query->the_post();
			$product_id = get_the_id();
			foreach ($bbh_taxes as $tax) {
				$term = get_the_terms($product_id, $tax);
				if ($term) {
					$term_id = $term[0]->term_id;
					if (!in_array($term_id, $bbh_term_array[$tax])) {
						array_push($bbh_term_array[$tax], $term_id);
					}
				}

			}
			if ($check_price == true) {
				$price = bbh_filter_get_price_by_id(0, $product_id);
				if ($price >= $priceFrom && $price < $priceTo) {
					$bbh_price_array[] = $price;
					$price_between[] = $product_id;
				}
			}
			endwhile;
		endif;
		if ($check_price == true) {
			$args['post__in'] = $price_between;
		}
	}



	$exclusive_wine_array = get_all_exlusive_products();
	$post_in_array = isset($args['post__in']) ? $args['post__in'] : array();
	if (count($post_in_array) >= 1) {
		$args['post__in'] = array_diff($post_in_array, $exclusive_wine_array);
		if ($args['post__in'] == NULL) {
			$args['post__in'] = array(0);
		}
	}else{
		$args['post__not_in'] = $exclusive_wine_array;
	}

	$query = bbh_get_product_filter_query($args);
	ob_start(); ?>
	<?php if ($query->have_posts()):
		while($query->have_posts()): $query->the_post();
			$product_id = get_the_id();
			$product = wc_get_product(get_the_id());
			$stock = $product->get_stock_quantity();
			$temp_stock = get_field('temporary_out_of_stock',$product_id);
			if($stock > 0) {
				//subtract reservations
				$total_reservations = get_field('total_reserved',$product_id);
				if($total_reservations > 0){
					$stock = $stock - $total_reservations;
				}
				//add user reserved stock to available stock
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
			//temporary out of stock
			if($stock == 0 && $temp_stock == true || $stock > 0) {
				wc_get_template_part( 'content', 'product' );
			}
		endwhile;


		wp_reset_postdata();
	else:
		wc_get_template( 'loop/no-products-found.php' );
 	endif;

	$results = array();
	$results['posts'] = ob_get_clean(); // This is required for frontend stuff
	$results['found'] = $query->found_posts; // This is required for frontend stuff
	//$results['query'] = json_encode($query);
	if ($bbh_term_array) {
		$results['terms'] = json_encode($bbh_term_array);
	}
	// $results['total'] = $query->post_count;
	// $results['shown'] = $perPage * ($page);
	$results['end'] = ($perPage * $page) >= $query->found_posts; // This is required for frontend stuff
	// $results['args'] = $query->query; // Enabled for debugging

	$response = new WP_REST_Response($results, 200);
	$response->header('Cache-Control', 'public, max-age=3600');

	return $response;
}


function bbh_get_term_query($args = array()){

}


function bbh_get_product_filter_query($args = array()){
	$hash = md5(json_encode($args)); // create a distinguishable key from array
	$key = 'bbh_shop_q_'; // base of key
	$transient = get_transient($key.$hash);
	$orderby = $args['orderby'] ?? false;
	// If we don't have transient or we are ordering by prices, do a fresh query
	// If we are ordering by price, the result will be unique for that user and should not be cached.
	if(!$transient || $orderby == 'post__in' || $orderby == 'product_price'){
		// no transient. we're building a new WP_Query.
		$query = new WP_Query($args);
		// Remember only to cache if orderby is not price
		if($orderby != 'post__in' && $orderby != 'product_price'){
			// save the IDs of found posts as transient.
			$postIDs = wp_list_pluck($query->posts, 'ID');
			set_transient($key.$hash, $postIDs, HOUR_IN_SECONDS * 24);
			// also save how many products were found in total. Later we will pull only the ID's from transient, so we need to know how many the total query would have found.
			set_transient($key.'found_'.$hash, $query->found_posts, HOUR_IN_SECONDS * 24);
		}
	} else{
		// Query post by transient IDs. This will be faster than complex queries.
		$query = new WP_Query(array(
			'post_type' => 'product',
			'post__in' => $transient,
			'orderby' => 'post__in',
			'posts_per_page' => $args['posts_per_page'] ?? 20
		));
		// check how many was found when transient was set.
		$transientFound = get_transient($key.'found_'.$hash);
		if($transientFound){
			// Manipulate the query with that data.
			$query->found_posts = $transientFound;
		}
	}
	return $query;

}

// fix stupid woocommerce error with rest api and authentification
// https://github.com/woocommerce/woocommerce/issues/23682 -> description of the error
add_filter( 'nonce_user_logged_out', 'bbh_fix_woo_cookie_issue' , 1000, 2 );
function bbh_fix_woo_cookie_issue( $uid, $action ) {
	if ( 'wp_rest' === $action ) {
		if ( ! is_user_logged_in() ) {
			return 0;
		}
	}
	return $uid;
}

/*=============================================
          = Force refresh products =
===============================================*/
/* This can be used to refresh products after import has been run
	// TODO: Should be able to do batches.
*/
function bbh_refresh_products_handler($request){
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'fields' => 'ids'
	);
	$q = new WP_Query($args);
	if($q->posts):
		$posts = $q->posts;
		$postsSplit = array_chunk($posts,50); // split into arrays of 100 elements
		$postsSplit = array_merge($postsSplit, $postsSplit, $postsSplit, $postsSplit, $postsSplit);
		foreach ($postsSplit as $arr) :

			$query = implode($arr, ',');
			$base = rest_url('vinforsyning/v1/refresh_products');
			$url = add_query_arg('ids', $query, $base);

			wp_remote_get( $url, array(
				'blocking'=>false,
				'timeout' => 1,
			) );


		endforeach;
	endif;

	$response = new WP_REST_Response($postsSplit, 200);
	return $response;
}

/*===============================================================
 = Force woocommerce hooks to refresh a single product's data =
=================================================================*/
function bbh_refresh_product($request){
	$id = $request->get_param('id') ?: false;
	if(!$id){
		return false;
	}
	do_action('woocommerce_update_product', $id);

	return true;
}

/*=====================================================
  = Force woocommerce hooks to refresh product data =
=======================================================*/

function bbh_refresh_products($request){
	$ids = $request->get_param('ids') ?: array();
	if(!$ids){
		return false;
	}
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post__in' => $ids,
		'fields' => 'ids'
	);
	$q = new WP_Query($args);

	if($q->posts){
		foreach ($q->posts as $pid) {
			do_action('woocommerce_update_product', $pid);
		}
	}

	$response = new WP_REST_Response($q->found_posts, 200);
	return $response;
}

/*=============================================
      = Clear all price list transients =
===============================================*/
function bbh_clear_pricelists($request){
	global $wpdb;
	$trans = '_transient_user_pricing_list_%';
	$toTrans = '_transient_timeout_user_pricing_list_%';
	$count = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM `$wpdb->options` WHERE `option_name` LIKE %s OR `option_name` LIKE %s",
			$trans,
			$toTrans
		)
	);
	return $count;
}

/*===============================================
=          Extend woocommerce api           =
===============================================*/
/*----------- district -----------*/
//Register taxonomy API for WC
add_action( 'rest_api_init', 'register_rest_field_for_district' );
function register_rest_field_for_district() {


    register_rest_field('product', "district", array(
        'get_callback'    => 'product_get_callback_district',
        'update_callback'    => 'product_update_callback_district',
        'schema' => null,
    ));

}
//Get Taxonomy record in wc REST API
 function product_get_callback_district($post, $attr, $request, $object_type)
{
    $terms = array();

    // Get terms
    foreach (wp_get_post_terms( $post[ 'id' ],'district') as $term) {
        $terms[] = array(
            'id'        => $term->term_id,
            'name'      => $term->name,
            'slug'      => $term->slug,
			'parent'	=> $term->parent,
        );
    }

    return $terms;
}

 //Update Taxonomy record in wc REST API
 function product_update_callback_district($values, $post, $attr, $request, $object_type)
{
    // Post ID
    $postId = $post->get_id();

    // Set terms
   wp_set_object_terms( $postId, $values , 'district');


}

/*----------- manufacturer -----------*/
add_action( 'rest_api_init', 'register_rest_field_for_manufacturer' );
function register_rest_field_for_manufacturer() {


    register_rest_field('product', "manufacturer", array(
        'get_callback'    => 'product_get_callback_manufacturer',
        'update_callback'    => 'product_update_callback_manufacturer',
        'schema' => null,
    ));

}
//Get Taxonomy record in wc REST API
 function product_get_callback_manufacturer($post, $attr, $request, $object_type)
{
    $terms = array();

    // Get terms
    foreach (wp_get_post_terms( $post[ 'id' ],'manufacturer') as $term) {
        $terms[] = array(
            'id'        => $term->term_id,
            'name'      => $term->name,
            'slug'      => $term->slug,
			'parent'	=> $term->parent,
        );
    }

    return $terms;
}

 //Update Taxonomy record in wc REST API
 function product_update_callback_manufacturer($values, $post, $attr, $request, $object_type)
{
    // Post ID
    $postId = $post->get_id();
    // Set terms
	wp_set_object_terms( $postId, $values , 'manufacturer');
}


// ADD META TO ORDER REST API
function my_line_item_metadata( $item_id, $item, $order_id ) {
   // Here you have the item, his id, and the order's id
   // You can get the order, for example
   $order = new WC_Order( $order_id );
   $items = $order->get_items();
   foreach( $items as $line_item_id => $item ) {
      $product_id = $item->get_product_id();
	  $engross_price = get_field( 'engross_price', $product_id );

   }

   if ($engross_price) {
    wc_add_order_item_meta( $item_id,'engross_price', $engross_price );
   }
      // Save here the metadata for the item id of the hooked line item
     // wc_add_order_item_meta( $item_id, 'engross_price', 'my metadata value' );
}
add_action( 'woocommerce_new_order_item', 'my_line_item_metadata', 10, 3 );
