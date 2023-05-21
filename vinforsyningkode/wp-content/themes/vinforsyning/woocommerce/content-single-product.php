<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
wp_enqueue_script('slickjs');
wp_enqueue_style('slick');

$districts = wp_get_post_terms(get_the_id(), 'district');
$districtsDisplay = array();
$district = get_lowest_level_post_term('district');
$manufacturer = get_lowest_level_post_term('manufacturer');

$product_group = $product->get_attribute( 'pa_product_group' );
$size = $product->get_attribute( 'pa_size' );
$country = wp_get_post_terms(get_the_id(), 'district', array('parent' => 0));
if(count($country) == 0) :
	$term = get_the_terms($product->get_ID(), 'district');
	if($term) :
		$country = ($term[0]->parent == 0) ? $term : get_term($term[0]->parent, 'district');
	endif;
endif;

$isFavourite = is_post_favourite($product->get_ID(), get_current_user_id());

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<div class="product-header">
		<h1 class="product-title">
			<?php the_title(); ?>
		</h1>
		<?php $img_tops = get_field('topbillede');?>
		<div class="img-container">
			<div class="image lazyload" data-bgset="<?php echo $img_tops ?>"></div>
        </div>

		<?php
		?>
		<?php if ($districts):
			?>
			<div class="product-district">
				<?php foreach ($districts as $term):
					$term_id = $term->term_id;
					$ancestors = get_ancestors($term_id, 'district');

					if( count($ancestors) == 2){
						$districtsDisplay[] = get_term( $ancestors[0] )->name;
						$districtsDisplay[] = get_term( $term_id )->name;
						break;
					}
					?>
				<?php endforeach; ?>
				<?php if (count($districtsDisplay)): ?>
					<div class="district">
						<?php
							echo implode($districtsDisplay, ', ');
						?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="product-content-container grid-container">

		<div class="product-meta-sidebar single-product-sidebar">

			<div class="product-gallery product-sidebar-box">
				<?php
				$attachment_ids = $product->get_gallery_attachment_ids();
				if(get_post_thumbnail_id()){
					array_unshift($attachment_ids, get_post_thumbnail_id());
				}
				if($product->get_type() == 'variable'){
					$variations = $product->get_available_variations();
					foreach ( $variations as $variation ) {
						if(isset($variation['image_id'])){
							$attachment_ids[$variation['image_id']] = $variation['image_id'];
						}
					}
				}
				$slider = count($attachment_ids) > 1 ? 'gallery-slider' : 'single-image';
				?>
				<div class="gallery-wrap <?php echo $slider; ?>">
					<?php foreach ($attachment_ids as $varId => $img):
						echo sprintf(
							'<div class="img-wrap" data-image-id="%s"><img %s class="lazyload lazypreload"></div>',
							$varId,
							lazySrc($img, false)
						);
						?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="product-purchase-sidebar single-product-sidebar mobile" style="display:none;">


				<?php if(is_user_logged_in()) : ?>
					<div class="favourite">
						<span class="favourite-btn <?php echo $isFavourite ? 'active' : '' ?>" data-post-id="<?php echo $product->get_ID(); ?>">
							<?php _e('Tilføj vinen til din liste', 'bbh'); ?>
							<span class="icon icomoon">
							</span>
						</span>
					</div>
				<?php endif; ?>
				<?php if(is_user_logged_in()) : ?>
					<?php
					$stock = $product->get_stock_quantity();
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
					} ?>
						<div class="add-to-cart product-sidebar-box">
							<div class="price-wrap">
									<div class="price">
										<?php echo $product->get_price_html(); ?>
									</div>
									<div class="kolli-wrap">
										<span>Kolli: <?php echo get_field('kolli');?></span>
									</div>
							</div>
							<?php if($stock > 0) {
								woocommerce_template_single_add_to_cart();
							}
							$data = new WC_Structured_Data();
							$data->generate_product_data($product);
							bbh_woo_archive_show_stock();
							?>

						</div>
				<?php else : ?>
					<div class="login-text">
						<?php the_field('login_text','theme_shop_settings'); ?>
					</div>
					<span class="login-btn">
						Log ind
					</span>
				<?php endif; ?>
			</div>
			<?php if(get_the_terms($product->get_ID(),'pa_recommended_for') && count(get_the_terms($product->get_ID(),'pa_recommended_for')) > 0) : ?>
				<div class="recommended-food mobile" style="display:none;">
					<span class="heading"><?php _e('Anbefales til:','bbh') ?></span>
					<div class="food-icons">
					<?php $recommended_food = get_the_terms($product->get_ID(),'pa_recommended_for') ?>
					<?php $recommended_names = [] ?>
					<?php foreach($recommended_food as $recommended) : ?>
						<?php array_push($recommended_names,$recommended->name) ?>
					<?php endforeach; ?>
					<?php $recommended_food = get_field( 'recommended_food' ); ?>
					<?php if(in_array('Skaldyr',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Skaldyr" data-title="Skaldyr"></div>
					<?php endif; ?>
					<?php if(in_array('Fisk',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Fisk" data-title="Fisk"></div>
					<?php endif; ?>
					<?php if(in_array('Okse',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Okse" data-title="Okse"></div>
					<?php endif; ?>
					<?php if(in_array('Lam',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Lam" data-title="Lam"></div>
					<?php endif; ?>
					<?php if(in_array('Gris',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Gris" data-title="Gris"></div>
					<?php endif; ?>
					<?php if(in_array('Kylling',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Kylling" data-title="Kylling"></div>
					<?php endif; ?>
					<?php if(in_array('Dessert',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Dessert" data-title="Dessert"></div>
					<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="read-more" style="display: none;">
				<a href="#product-content-main-ancher">Læs mere om produktet</a>
			</div>

			<div class="product-meta product-sidebar-box">
				<?php

				// Product group attribute
				if ($product_group): ?>
					<div class="product-group">
						<p>
							<span class="meta-title">
								<?php _e('Gruppe', 'bbh') ?>:
							</span>
							<span class="meta-val">
								<?php echo $product_group ?>
							</span>
						</p>
					</div>
				<?php endif;

				// product country tax
				if ($country): ?>
				<?php if(is_array($country)) :
					$country = $country[0];
				endif; ?>

					<div class="product-country">
						<p>
							<span class="meta-title">
								<?php _e('Land', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $country->name; ?>
								<?php if ($countryCode = get_field( 'country_code', $country )): ?>
									<?php echo sprintf('<img class="country-flag lazyload" data-src="%1$s">',
										get_stylesheet_directory_uri() . '/assets/images/flags/' . $countryCode . '.png'
									); ?>
								<?php endif; ?>
							</span>
						</p>
					</div>
				<?php endif;

				// product year meta field
				if ($year = get_field( 'wine_year' )): ?>
					<div class="product-year">
						<p>
							<span class="meta-title">
								<?php _e('Årgang', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $year; ?>
							</span>
						</p>
					</div>
				<?php endif;

				// product size attribute
				if ($size): ?>
					<div class="product-size">
						<p>
							<span class="meta-title">
								<?php _e('Størrelse', 'bbh') ?>:
							</span>
							<span class="meta-val">
								<?php echo $size ?>
							</span>
						</p>
					</div>
				<?php endif;

				// product grape_variety meta field
				if ($grapes = get_field( 'grape_variety' )): ?>
					<div class="product-year">
						<p>
							<span class="meta-title">
								<?php _e('Druer', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $grapes; ?>
							</span>
						</p>
					</div>
				<?php endif;

				// product aging meta field
				if ($grapes = get_field( 'aging' )): ?>
					<div class="product-aging">
						<p>
							<span class="meta-title">
								<?php _e('Lagring', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $grapes; ?>
							</span>
						</p>
					</div>
				<?php endif;

				//alcohol percentage meta field
				if ($alcohol = get_field( 'alcohol' )): ?>
					<div class="product-alcohol">
						<p>
							<span class="meta-title">
								<?php _e('Alkohol', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $alcohol; ?>
							</span>
						</p>
					</div>
				<?php endif;

				// secondary categories attributes
				if( $secondCats = $product->get_attribute('pa_secondary_cat')):
					$toArray = explode(', ', $secondCats);
					if ($toArray) :
						foreach ($toArray as $name) :
							?>
							<div class="product-secondary-category <?php echo sanitize_key($name); ?>">
								<p>
									<span class="meta-title">
										<?php _e($name, 'bbh'); ?>:
									</span>
									<span class="meta-val">
										<?php _e('Ja', 'bbh'); ?>
									</span>
								</p>
							</div>
							<?php
						endforeach;
					endif;
				endif;

				// product SKU
				if($product->get_sku()): ?>
					<div class="product-sku">
						<p>
							<span class="meta-title">
								<?php _e('Nr', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $product->get_sku(); ?>
							</span>
						</p>
					</div>
				<?php endif;

				// Product EAN number meta field
				if($ean = get_field( 'ean' )): ?>
					<div class="product-ean">
						<p>
							<span class="meta-title">
								<?php _e('EAN Nr', 'bbh'); ?>:
							</span>
							<span class="meta-val">
								<?php echo $ean; ?>
							</span>
						</p>
					</div>
				<?php endif;
				?>
			</div>
			<?php if(get_field('pdf_map') || get_field('pdf_map_no_logo') || get_field('pdf_map_vinoble')) : ?>
				<div class="product-sidebar-box pdf-download">
					<span class="meta-title">
						<?php _e('Hent produktark','bbh') ?>
					</span>
					<br>

					<div class="pdfs">
						<?php if(get_field('pdf_map')) : ?>
							<div class="button-wrap">
								<a href="<?php the_field('pdf_map') ?>" download>
									<span><?php _e('Download','bbh') ?> <span class="icon-download"></span></span>
									<span><?php _e('Med vinforsyning logo') ?></span>
								</a>
							</div>
						<?php endif; ?>
						<?php if(get_field('pdf_map_no_logo')) : ?>
							<div class="button-wrap">
								<a href="<?php the_field('pdf_map_no_logo') ?>" download>
									<span><?php _e('Download','bbh') ?> <span class="icon-download"></span></span>
									<span><?php _e('Med Vinoble logo') ?></span>
								</a>
							</div>
						<?php endif; ?>
						<?php if(get_field('pdf_map_vinoble')) : ?>
							<div class="button-wrap">
								<a href="<?php the_field('pdf_map_vinoble') ?>" download>
									<span><?php _e('Download','bbh') ?> <span class="icon-download"></span></span>
									<span><?php _e('Uden vinforsyning logo') ?></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>


		</div>
		<div class="product-content-main" id="product-content-main-ancher">
			<?php
			/**
			 * Hook: woocommerce_before_single_product.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 */

			do_action( 'woocommerce_before_single_product' );

			?>

			<?php if ($district): ?>
				<div class="district">
					<h3 class="product-content-heading">
						<?php _e('Distrikt', 'bbh') ?>
					</h3>
					<p>
						<?php
						echo sprintf(
							__('%s. Læs mere om %s distriktet %s', 'bbh'),
							$district->name,
							'<a href="'.get_term_link($district).'">',
							'</a>'
						); ?>
					</p>
				</div>
			<?php endif; ?>
			<?php if($manufacturer): ?>
				<div class="manufacturer">
					<h3 class="product-content-heading">
						<?php _e('Producent', 'bbh') ?>
					</h3>
					<p>
						<?php
						echo sprintf(
							__('%s. Læs mere om %s producenten %s', 'bbh'),
							$manufacturer->name,
							'<a href="'.get_term_link($manufacturer).'">',
							'</a>'
						); ?>
					</p>
				</div>
			<?php endif; ?>
			<div class="description">
				<h3 class="product-content-heading">
					<?php _e('Om produktet', 'bbh') ?>
				</h3>
				<?php the_content(); ?>
			</div>

			<?php $product_id = $product->get_id();
			$rows = get_field('cf_repeater', $product_id);
			if ($rows) { ?>
				<div class="scores">
					<h3 class="product-content-heading"><?php _e('Scoreplads') ?></h3>
					<p><?php _e('En kort forklarende tekst om hvad nedenstående logoer betyder for denne vin.') ?></p>
				<?php
				if (is_string($rows)) {
					// new - string
					for ($i = 0; $i <= $rows-1; $i++) {
						$img = get_field('cf_repeater_'.$i.'_img', $product_id);
						$name = get_field('cf_repeater_'.$i.'_name', $product_id);
						$text = get_field('cf_repeater_'.$i.'_text', $product_id);
						$score = get_field('cf_repeater_'.$i.'_score', $product_id);
						$priority = get_field('cf_repeater_'.$i.'_priority', $product_id); ?>
						<div class="score" data-prio="<?php echo $priority ?>">
							<div class="image lazyload" data-bgset="<?php echo $img ?>"></div>
							<div class="content">
								<div class="heading">
									<span><?php echo $name ?></span><span class="score"><?php echo $score ?></span>
								</div>
								<div class="text">
									<?php echo $text ?>
								</div>
							</div>
						</div>
					<?php }
				}else{
					// old - array
					if (have_rows('cf_repeater',$product_id)) {
						while( have_rows( 'cf_repeater' ) ): the_row();
							$img = get_sub_field('img');
							$name = get_sub_field('name');
							$text = get_sub_field('text');
							$score = get_sub_field( 'score' );
							$priority = get_sub_field('priority');
							?>
							<div class="score" data-prio="<?php echo $priority ?>">
								<div class="image lazyload" data-bgset="<?php echo $img ?>"></div>
								<div class="content">
									<div class="heading">
										<span><?php echo $name ?></span><span class="score"><?php echo $score ?></span>
									</div>
									<div class="text">
										<?php echo $text ?>
									</div>
								</div>
							</div>
					    <?php endwhile;
					}
				} ?>
				</div>
			<?php } ?>
		</div>
		<div class="product-purchase-sidebar single-product-sidebar desktop">
			<?php if(is_user_logged_in()) : ?>
				<div class="favourite">
					<span class="favourite-btn <?php echo $isFavourite ? 'active' : '' ?>" data-post-id="<?php echo $product->get_ID(); ?>">
						<?php _e('Tilføj vinen til din liste', 'bbh'); ?>
						<span class="icon icomoon">
						</span>
					</span>
				</div>
			<?php endif; ?>
			<?php if(is_user_logged_in()) : ?>
				<?php
				$stock = $product->get_stock_quantity();
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
				} ?>
				<?php $exclusive_wine = get_all_exlusive_products(); ?>
				 <?php if (!in_array($product->get_id(), $exclusive_wine)): ?>
					<div class="add-to-cart product-sidebar-box">
						<div class="price-wrap">
								<div class="price">
									<?php echo $product->get_price_html(); ?>
								</div>
								<div class="kolli-wrap">
									<span>Kolli: <?php echo get_field('kolli');?></span>
								</div>
						</div>
						<?php if($stock > 0) {
							woocommerce_template_single_add_to_cart();
						}
						$data = new WC_Structured_Data();
						$data->generate_product_data($product);
						bbh_woo_archive_show_stock();
						?>

					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="login-text">
					<?php the_field('login_text','theme_shop_settings'); ?>
				</div>
				<span class="login-btn">
					Log ind
				</span>
			<?php endif; ?>
			<?php if(get_the_terms($product->get_ID(),'pa_recommended_for') && count(get_the_terms($product->get_ID(),'pa_recommended_for')) > 0) : ?>
				<div class="recommended-food">
					<span class="heading"><?php _e('Anbefales til:','bbh') ?></span>
					<?php $recommended_food = get_the_terms($product->get_ID(),'pa_recommended_for') ?>
					<?php $recommended_names = [] ?>
					<?php foreach($recommended_food as $recommended) : ?>
						<?php array_push($recommended_names,$recommended->name) ?>
					<?php endforeach; ?>
					<?php $recommended_food = get_field( 'recommended_food' ); ?>
					<?php if(in_array('Skaldyr',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Skaldyr" data-title="Skaldyr"></div>
					<?php endif; ?>
					<?php if(in_array('Fisk',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Fisk" data-title="Fisk"></div>
					<?php endif; ?>
					<?php if(in_array('Okse',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Okse" data-title="Okse"></div>
					<?php endif; ?>
					<?php if(in_array('Lam',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Lam" data-title="Lam"></div>
					<?php endif; ?>
					<?php if(in_array('Gris',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Gris" data-title="Gris"></div>
					<?php endif; ?>
					<?php if(in_array('Kylling',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Kylling" data-title="Kylling"></div>
					<?php endif; ?>
					<?php if(in_array('Dessert',$recommended_names)) : ?>
						<div class="icon-madpiktogram_Dessert" data-title="Dessert"></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
