<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('OUTLET', 'bbh')
);

add_filter( 'woocommerce_get_catalog_ordering_args', 'outlet_woocommerce_get_catalog_ordering_args', 100, 1 );
function outlet_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

	// Price custom
	if ( 'product_price' == $orderby_value ) {
		$key = 'asc';
		if(isset($_GET['order'])){
			$key = mb_strtolower($_GET['order']);
		}
		$args['orderby'] = 'meta_value_num';
		$args['order'] = $key;
		$args['meta_key'] = '_regular_price';
	}

	if(isset($_GET['order']) &&  'product_price' != $orderby_value){
		$args['order'] = $_GET['order'];
	}

    $meta_query = array();

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

    $args['meta_query'] = $meta_query;

	return $args;
}

$posts_per_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

?>
<div class="outlet-page">
    <?php echo do_shortcode('[products attribute="secondary_cat" paginate="true" limit="'.$posts_per_page.'" terms="outlet" columns="4"]'); ?>
</div>
<?php
