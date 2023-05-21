<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Tidligere køb', 'bbh')
);



$user_id = get_current_user_id();
$customer_id = get_user_meta( $user_id, 'customer_number');
$user_query = new WP_User_Query( array( 'meta_key' => 'customer_number', 'meta_value' => $customer_id ) );
$user_id_array = array();
foreach ($user_query->results as $uid => $id) {
    $data = $id->data;
    $user_id_array[] = $data->ID;
}
$args = array(
    'numberposts' => -1,
    'meta_key' => '_customer_user',
    'meta_value' => $user_id_array,
    'post_type' => wc_get_order_types(),
    'post_status' => array_keys( wc_get_is_paid_statuses() ),
);
// $args = array(
//     'numberposts' => -1,
//     'meta_key' => '_customer_user',
//     'meta_value' => $user_id,
//     'post_type' => wc_get_order_types(),
//     'post_status' => array_keys( wc_get_is_paid_statuses() ),
// );
//d($user_query);
$customer_orders = get_posts( $args);

if ( ! $customer_orders ) return; ?>

<div class="repurchase previous-orders">
    <?php $product_ids = array();
	foreach ( $customer_orders as $customer_order ) { ?>
        <div class="order">
        <?php
    		$order = wc_get_order( $customer_order->ID );
    		$items = $order->get_items();
            //setlocale(LC_ALL, 'da_DK');
            ?> <h3>Ordredato: <?php echo strftime("%e %b, %Y",strtotime($order->get_date_created())) ?></h3> <?php
            $product_ids = array();
            foreach ( $items as $item ) {
                $product_id = $item->get_product_id();
                if ($product_id > 0) {
                ?> <div class="item">
                        <span data-qty="<?php echo $item->get_quantity() ?>" class="prev-quantity">Købte: <?php echo $item->get_quantity() ?></span><?php

                            echo do_shortcode('[products ids="'.$product_id.'" columns="4" orderby="post__in"]');

                ?> </div> <?php }
    		}
            //echo do_shortcode('[products ids="'.implode(',',$product_ids).'" columns="4" orderby="post__in"]'); ?>
        </div>
    <?php } ?>
</div>
<?php
