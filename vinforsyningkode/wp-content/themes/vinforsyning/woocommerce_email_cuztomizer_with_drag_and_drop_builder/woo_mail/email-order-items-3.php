<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/plugin-folder-name/woo_mail/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align = is_rtl() ? 'right' : 'left';

foreach ( $items as $item_id => $item ) :
	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		$product = $item->get_product();
		$product_id = $product->get_id();
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;word-wrap:break-word;"><?php

				// Show title/image etc
				if ( $args['show_image'] && is_object( $product ) ) {
					echo apply_filters( 'woocommerce_order_item_thumbnail', '<div style="margin-bottom: 5px"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( $args['image_size'][1] ) . '" width="' . esc_attr( $args['image_size'][0] ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item );
				}

				// Product name
				echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false );

				// SKU
				if ( $args['show_sku'] && is_object( $product ) && $product->get_sku() ) {
					echo ' (#' . $product->get_sku() . ')';
				}

				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $args['plain_text']);

				wc_display_item_meta( $item );

				if ( $args['show_download_links'] ) {
					wc_display_item_downloads( $item );
				}

				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $args['plain_text']);
			?></td>
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php $wine_year = get_field( 'wine_year', $product_id);
			if ($wine_year && $wine_year != 0):
				echo $wine_year;
			endif; ?></td>
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php $size = $product->get_attribute( 'pa_size' );
				if ($size && $size != 0 && $size != "0,00") {
					$clean_size = str_replace(',00', "", $size);
					echo $clean_size." cl";
				} ?></td>
			<?php
			$qty = apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item );
			$subtotal = $item->get_subtotal();
			$bbh_price = $subtotal / $qty;
			?>
			<td class="td" style="min-width: 75px;text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php echo number_format((float)$bbh_price, 2, ',', '')." DKK" ?></td>
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php $kolli = get_field( 'kolli', $product_id);
			if ($kolli):
				echo $kolli;
			endif; ?></td>
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php echo $qty; ?></td>
			<td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
		</tr>
		<?php
	}

	if ( $args['show_purchase_note'] && is_object( $product ) && ( $purchase_note = $product->get_purchase_note() ) ) : ?>
		<tr>
			<td colspan="3" style="text-align:<?php echo $text_align; ?>; vertical-align:middle;"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
		</tr>
	<?php endif; ?>

<?php endforeach; ?>
