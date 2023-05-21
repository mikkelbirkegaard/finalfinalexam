<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp;
$currentOrder = $wp->query_vars['order'] ?? 'DESC';
$currentUrl = add_query_arg( [] );
$filter_text = get_field('filter_text','theme_shop_settings');
?>
<form class="woocommerce-ordering" method="get">
	<span class="filter-text"><?php echo esc_html($filter_text) ?></span>
	<div class="filter-right">
		<div id="post-results">
			<p>
				<?php
				global $wp_query;
				echo sprintf(
					'<span class="count">%1$s vine</span>',
					$wp_query->found_posts,
					$wp_query->found_posts,
					__('vine', 'bbh')

				);
				?>
			</p>
		</div>
		<span class="sort-text"><?php _e('| Sortér efter') ?></span>
		<div class="orderby-wrap">
			<div class="order-changer" id="order-changer">
				<a href="<?php echo add_query_arg('order', 'ASC', $currentUrl); ?>" class="order-changer-item asc <?php echo 'DESC' != $currentOrder ? 'active' : '' ?>" data-value="ASC" title="<?php _e('Stigende', 'bbh'); ?> ">
					 <span class="icon icomoon icon-pil-op"></span>
				</a>
				<a href="<?php echo add_query_arg('order', 'DESC', $currentUrl); ?>" class="order-changer-item desc <?php echo 'DESC' == $currentOrder ? 'active' : '' ?>" data-value="DESC" title="<?php _e('Faldende', 'bbh'); ?> ">
					 <span class="icon icomoon icon-pil-op"></span>
				</a>
			</div>
			<select name="orderby" class="orderby" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); if (esc_attr( $id ) == "stock") { echo 'selected="selected"'; }; ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<input type="hidden" name="paged" value="1" />
		<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
	</div>
</form>
