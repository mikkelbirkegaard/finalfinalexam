<?php
add_action('wp_ajax_filter_ajax', 'filter_ajax'); // Change names according to your form
add_action('wp_ajax_nopriv_filter_ajax', 'filter_ajax'); // Change names according to your form
/*-------------- Function to execute -------------*/
function filter_ajax(){
    // check for nonce security
    $nonce = $_POST['security'];
    if ( ! wp_verify_nonce( $nonce, 'filter-ajax-nonce' ) ){
        die;
    }
    // Need to run this for our markup changes to be applied to rest response
    // Save all our values to variables.
    $search = $_POST['search'];
    $perPage = $_POST[ 'posts_per_page' ] ? intval($_POST['posts_per_page']) : apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
    $page = $_POST[ 'page' ] ? intval($_POST['page']) : 1;
    $token = $_POST['token'];
    //$productCat = $_POST['product_cat');
    $secondCat = $_POST['pa_secondary_cat'];
    $wineSize = $_POST['pa_size'];
    $grapeType = $_POST['pa_grape_type'];
    $productCatAtt = $_POST['pa_pro_cat_att'];
    $countryAtt = $_POST['pa_country_att'];
    $districtAtt = $_POST['pa_distrikt_att'];
    $productGroup = $_POST['pa_product_group'];
    $yearFrom = $_POST['year-from-val'] ?: '0';
    $yearTo = $_POST['year-to-val'];
    $priceFrom = $_POST['price-from-val'] ?: 0;
    $priceTo = $_POST['price-to-val'];
    $district = $_POST['district'] ?: 0;
    $order = $_POST['order'] ?: 'desc';
    if($priceTo == 500 || $priceTo == null){
        $priceTo = 999999;
    }

    // Get base args from WooCommerce
    // $baseQ = new WC_Shortcode_Products();
    // $baseQ = $baseQ->get_query_args();

    $args = array(
        'post_type' => 'product',
        'fields' => 'all',
        'posts_per_page' => $perPage,
        'offset' => $perPage * ($page - 1),
        'order' => $order,
        'post_status' => 'publish',
        //'post__not_in' => get_all_exlusive_products(),
        //'offset' => $perPage * ($page ),
        //'posts_per_page' => $number_of_posts_per_page,
        //'offset' => ($page * $perPage) - $perPage,

        //'offset' => $offset,
        //'offset' => 1,
        // 'paged'  => $paged,
        // 'offset' => $paged,
        //'paged' => $pagede * ($page - 1),
        //'posts_per_page' => $per_page,
        //'offset'         => $offset,
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
    //$args = wp_parse_args($args, $baseQ);



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


    /* NEW FILTER BBH - SØ */
    $check_price = false;
    if($priceFrom !== 0 || $priceTo !== 999999){
        $price_between = [];
        $check_price = true;
    }
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
                $term_id = get_the_terms($product_id, $tax)[0]->term_id;
                if (!in_array($term_id, $bbh_term_array[$tax])) {
                    array_push($bbh_term_array[$tax], $term_id);
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

    // $exclusive_wine_array = get_all_exlusive_products();
    //
    // if (isset($args['post__in'])) {
    //     $post_in_array = $args['post__in'];
    //     if (count($post_in_array) >= 1) {
    //         $args['post__in'] = array_diff($post_in_array, $exclusive_wine_array);
    //         if ($args['post__in'] == NULL) {
    //             $args['post__in'] = array(0);
    //         }
    //     }else{
    //         $args['post__not_in'] = $exclusive_wine_array;
    //     }
    // }else{
    //     $args['post__not_in'] = $exclusive_wine_array;
    // }

    $query = new WP_Query($args);
    ob_start(); ?>
    <?php if ($query->have_posts()):
        while($query->have_posts()): $query->the_post();
            $product_id = get_the_id();
            $product = wc_get_product(get_the_id());
            $stock = $product->get_stock_quantity();
            $temp_stock = get_field('temporary_out_of_stock',$product_id);
            // if($stock > 0) {
            //     //subtract reservations
            //     $total_reservations = get_field('total_reserved',$product_id);
            //     if($total_reservations > 0){
            //         $stock = $stock - $total_reservations;
            //     }
            //     //add user reserved stock to available stock
            //     $user_id = get_current_user_id();
            //     $customer_id = get_user_meta($user_id,'customer_number');
            //     $rows = get_field('reservations', $product_id);
            //     if ($rows) {
            //         if (is_string($rows)) {
            //             // new - string
            //             for ($i = 0; $i <= $rows-1; $i++) {
            //                 $res_user_id = get_field('reservations_'.$i.'_user_id', $product_id);
            //                 $res_quantity = 0;
            //                 $res_quantity += get_field('reservations_'.$i.'_quantity', $product_id);
            //                 if ($customer_id[0] == $res_user_id) {
            //                     $stock += $res_quantity;
            //                 }
            //             }
            //         }else{
            //             // old - array
            //             if (have_rows('reservations',$product_id)) {
            //                 while (have_rows('reservations',$product_id)) {
            //                     the_row();
            //                     if (get_sub_field('user_id') == $customer_id[0]) {
            //                         $stock += get_sub_field('quantity');
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }
            //temporary out of stock
            if($stock == 0 && $temp_stock == true || $stock > 0) {
                wc_get_template_part( 'content', 'product' );
            }
        endwhile;


        wp_reset_postdata();
    else:
        wc_get_template( 'loop/no-products-found.php' );
    endif;

    $posts = ob_get_clean();



    $send = array(
        'load_more' => $_POST['load-more'],
        'page_number' => $_POST['page-number'],
        'max_pages' => $query->max_num_pages,
        'bbh_posts' => $posts,
        'bbh_terms' => $bbh_term_array,
        'have_posts' => $query->have_posts(),
    );
    wp_send_json($send); // Send json

    wp_die();
}
