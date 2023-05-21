<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Mine reservationer', 'bbh')
);
//Function from /httpdocs/httpdocs/wp-content/themes/vinforsyning/include/functions/woocommerce/my_account.php
$reservations_array = get_all_reservations();
?>
<div class="previous-orders">
        <div class="order"> <?php
            $product_ids = array();
            foreach ( $reservations_array as $product_id ) {
                if ($product_id > 0) {
                ?> <div class="item">
                        <span data-qty="<?php echo $product_id['quantity'] ?>" class="prev-quantity">Reserveret: <?php echo $product_id['quantity'] ?></span><?php
                            echo do_shortcode('[products ids="'.$product_id['id'].'" columns="4" orderby="post__in"]');
                ?> </div> <?php }
    		} ?>
        </div>
</div>
<?php
