<?php if (is_user_logged_in()): ?>
	<div class="my-account-menu-left" style="position: fixed; left:0px;">
		<nav class="nav">
			<a class="link-icon checkout icon-checkout1" href="/min-konto/checkout/"><span class="link-text">Checkout</span></a>
			<a class="link-icon checkout icon-sortiment" href="/min-konto/cart/"><span class="link-text">Indkøbskurv</span></a>
			<a class="link-icon checkout icon-vinpakker" href="/min-konto/favourites/"><span class="link-text">Mine favoritter</span></a>
			<a class="link-icon checkout icon-tidligerekob2" href="/min-konto/repurchase/"><span class="link-text">Tidligere køb</span></a>
			<a class="link-icon checkout icon-fakturarer" href="/min-konto/invoices/"><span class="link-text">Fakturaer</span></a>
		</nav>
	</div>
<?php endif; ?>
<div id="shop-filter">
	<form id="shop-filter-form">
		<div id="shop-filter-box">
			<div class="hidden-fields">
				<input type="hidden" name="page" value="1">
				<input type="hidden" name="posts_per_page" value="<?php echo apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page()) ?>">
				<input type="text" name="orderby" value="" style="display:none">
				<input type="text" name="order" value="" style="display:none">
			</div>
			<div class="filter-heading">
				<h1 class="heading">
					<?php _e('Specificer din <u>søgning</u>', 'bbh'); ?>
				</h1>
			</div>
			<div class="filter-fields">
				<div class="inside-fields">
					<div class="top-row flex-row wrap jc-start ai-top">
						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_pro_cat_att',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
								'type' => 'checkbox'
							));
							bbh_filter_box('pro_cat_att', __('Vælg produktkategori', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>
						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_country_att',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
								'type' => 'checkbox'
							));
							bbh_filter_box('country_att', __('Vælg land', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>
						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_grape_type',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
								'type' => 'checkbox'
							));
							bbh_filter_box('grape_type', __('Vælg drue sort', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>


						<!-- <div class="field">
							<?php
							// $list = bbh_tax_list(array(
							// 	'taxonomy' => 'product_cat',
							// 	'echo' => false,
							// 	'hide_empty' => !current_user_can('administrator'),
							// 	'type' => 'checkbox'
							// ));
							// bbh_filter_box('product_cat', __('Vælg produktkategori', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div> -->


						<!-- <div class="field">
							<?php

							// $list = bbh_tax_list(array(
							// 	'taxonomy' => 'district',
							// 	'echo' => false,
							// 	'hide_empty' => !current_user_can('administrator'),
							// 	'type' => 'checkbox',
							// 	//'order' => 'ASC',
							// 	'parent' => 0,
							// ));
							//
							// $placeholder = __('Skriv landet du søger...', 'bbh');
							// $listWrap = "
							// <div class='filter-list-search'>
							// 	<div class='list-search-wrap'>
							// 		<input name='district_list_search' type='text' placeholder='{$placeholder}' class='filter-list-search-input'/>
							// 		<span class='search-icon icon-lup icomoon'></span>
							// 	</div>
							// 	<div class='list-search-results'>
							// 		{$list}
							// 	</div>
							// </div>
							// ";
							// bbh_filter_box('country_search search-box', __('Vælg Land', 'bbh'), 'icon-lup', $listWrap);
							// ?>
						</div> -->

						<div class="field">
							<?php
							$minYear = get_end_meta_key('min','wine_year', 1);
							$maxYear = date("Y");
							$yearSlider = bbh_filter_range_slider(array(
								'echo' => false,
								'name' => 'year',
								'min' => $minYear,
								'max' => $maxYear,
							));
							?>
							<?php bbh_filter_box('wine_year', __('Vælg år'), 'icon-cirkel', $yearSlider ); ?>
							<!-- Year slider filter -->
							<!-- Missing -->
							<!-- When press slider and out side slider, without chaning value, dont change the color of border -->
							 <input type="hidden" name="year-from-val" id="bbh-year-from" value="0">
							<input type="hidden" name="year-to-val" id="bbh-year-to" value="<?php echo $maxYear;?>">

						</div>




						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_product_group',
								'type' => 'checkbox',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
							));
							bbh_filter_box('product_group', __('Vælg produktgruppe', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>

						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_distrikt_att',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
								'type' => 'checkbox'
							));
							bbh_filter_box('distrikt_att', __('Vælg område', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>

						<div class="field">
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_size',
								'type' => 'checkbox',
								'echo' => false,
								'hide_empty' => !current_user_can('administrator'),
								'orderby' => 'description',
								'order' => 'ASC'
							));
							bbh_filter_box('wine_size', __('Vælg cl-størrelse', 'bbh'), 'icon-st-arrow-down', $list);
							?>
						</div>
						<div class="field">
							<?php
							// price slider need to be made manually due to some price formatting stuff
							$minPrice = wc_price(0, array('decimals'=> 0));
							$maxPrice = wc_price(get_end_meta_key('max','_regular_price'), array('decimals'=> 0));
							//$maxPrice = 1000;
							$priceSlider = '<div id="price-slider">
							<span id="price-slider-from"class="slider-from">Min. '.$minPrice.'</span>
							<span id="price-slider-to" class="slider-to">Max. <bdi>500</bdi><span class="plus">+</span></span>
							<input type="hidden" name="price-from-val" id="price-slider-from-val" value="1">
							<input type="hidden" name="price-to-val" id="price-slider-to-val" value="500">
							<div class="slider-el" id="price-slider-el" data-to-start="500" data-from-start="0"></div>
							</div>';
							?>
							<?php bbh_filter_box('price', __('Sæt prisen'), 'icon-cirkel', $priceSlider ); ?>

						</div>


					</div>



					<div class="bottom-row flex-row wrap jc-end ai-center">
						<div class="field secondary_cat">
							<h4 class="filter-heading">
								<?php _e('Vælg sekundær kategori', 'bbh'); ?>
							</h4>
							<?php
							$list = bbh_tax_list(array(
								'taxonomy' => 'pa_secondary_cat',
								'type' => 'checkbox',
								'echo' => true,
								'hide_empty' => !current_user_can('administrator'),
							)); ?>
						</div>
						<div class="field search-field">
							<?php
							$searchValue = $_GET['search'] ?? '';
							?>
							<div class="search-bar">
								<input type="text" name="search" id="header-search-input" class="search-input" placeholder="<?php _e('Skriv navnet på vinen du søger', 'bbh') ?>" value="<?php echo esc_attr($searchValue) ?>">
							</div>
						</div>
						<div class="field submit-field">
							<button type="submit"><?php _e('Søg', 'bbh') ?></button>
						</div>

					</div>
					<strong class="filter">Filter: </strong>
					<div class="bottom-container">
						<div class="bbh-output-tags"></div><div class="reset"><?php _e('Nulstil', 'bbh'); ?></div>
					</div>
				</div>
			</div>


			<!-- <div id="UI_output" class="test">
				<strong class="filter">Filter: </strong>
				<strong class="product-cat"><span></span></strong>
		 		<strong class="countries"><span></span></strong>
				<strong class="district"><span></span></strong>
				<strong class="cl-size"><span></span></strong>
				<strong class="product-group"><span></span></strong>
				<strong class="grape"><span></span></strong>
				<strong class="year-from"><span></span></strong>
				<strong class="year-to"><span></span></strong>
				<strong class="price-from"><span></span></strong>
				<strong class="price-to"><span></span></strong>

		 	</div>
			<div id="UI_input_ids">
				<strong class="array-string-countery" style="display:none;"><span></span></strong>
				<strong class="array-string-district" style="display:none;"><span></span></strong>
				<strong class="array-string-grape" style="display:none;"><span></span></strong>
				<strong class="array-string-size" style="display:none;"><span></span></strong>
				<strong class="array-string-group" style="display:none;"><span></span></strong>
				<strong class="array-string-product-cat" style="display:none;"><span></span></strong>
			</div> -->

		</div>
		<?php /*
		<div class="order-row">
			<div id="post-results">
				<p>
					<?php
					global $wp_query;
					echo sprintf(
						'<span class="count">%1$s</span> ud af %2$s %3$s',
						$wp_query->found_posts,
						$wp_query->found_posts,
						__('vine', 'bbh')

					);
					?>
				</p>
			</div>

		</div>
		*/ ?>

	</form>
	<!-- <input type="text" id="UI_input" value="test value"> -->


	<script type="text/javascript"
	src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</div>
