<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*=============================================
          = Add description no nav items =
===============================================*/

function bbh_add_nav_item_description( $item_output, $item, $depth, $args ) {
    if ( !empty( $item->description ) && $depth == 0) {
        $item_output = str_replace( $args->link_after . '</a>', '<p class="menu-item-description">' . $item->description . '</p>' . $args->link_after . '</a>', $item_output );
    }

    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'bbh_add_nav_item_description', 10, 4 );




/*=============================================
          = Add call to action in header =
===============================================*/
add_action('generate_after_header_content', 'bbh_add_nav_cta', 80);
function bbh_add_nav_cta() {
	$link = get_field( 'nav_cta', 'theme_header_settings' );

	if(!$link){
		return;
	}

	?>

    <?php if (is_user_logged_in()): ?>
    <?php global $woocommerce; ?>
    <div class="nav-cta-wrap"><a href="/min-konto/cart/" class="bbh-cart nav-cta btn">Kurv  ( <?php echo $woocommerce->cart->cart_contents_count; ?> )</a></div>
    <?php else: ?>
        <div class="nav-cta-wrap"><a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>" class="nav-cta btn"><?php echo $link['title'] ?></a></div>
    <?php endif; ?>
	<?php
};

add_filter( 'woocommerce_add_to_cart_fragments', 'misha_add_to_cart_fragment' );

function misha_add_to_cart_fragment( $fragments ) {

	global $woocommerce;

	$fragments['.bbh-cart'] = '<a href="/min-konto/cart/" class="bbh-cart nav-cta btn">Kurv  ( ' . $woocommerce->cart->cart_contents_count . ' )</a>';
 	return $fragments;

 }
/*=============================================
          = Add header search =
===============================================*/
add_action('generate_header', 'bbh_menu_search_button', 70);
function bbh_menu_search_button(){
	$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
	$searchValue = $_GET['search'] ?? '';
	?>
	<div class="bbh-search-container">

		<div class="inside-search-container">
			<div class="search-bar">
				<form action="<?php echo $shop_page_url ?>" class="header-search-form">
					<input type="text" name="search" required id="header-search-input" class="search-input" placeholder="<?php _e('Skriv navnet på vinen du søger', 'bbh') ?>" value="<?php echo esc_attr($searchValue) ?>">
					<button class="search-btn">
						<?php _e('Søg', 'bbh'); ?>
						<span class="icomoon icon icon-lup"></span>
					</button>
				</form>
			</div>

		</div>
	</div>
	<?php

}



/*=============================================
          = Wrap masthead to include top bar =
===============================================*/
add_action('generate_header', 'bbh_wrap_masthead_start', 1);
function bbh_wrap_masthead_start() {
	echo "<div id='fixed-header'>";
};

add_action('generate_header', 'bbh_wrap_masthead_end', 99);
function bbh_wrap_masthead_end() {
	echo "</div>";
};
/*=============================================
          = Top bar =
===============================================*/
add_action('generate_header', 'bbh_top_bar', 9);
function bbh_top_bar(){
	$options = 'theme_header_settings';
	?>
	<div id="top-bar" class="bbh-top-bar">
		<div class="inside-bbh-top-bar">

			<div class="left credentials">
				<?php if( have_rows( 'credentials', $options ) ): ?>
				    <?php while( have_rows( 'credentials', $options ) ): the_row();
						$image = get_sub_field( 'image' );
						$link = get_sub_field( 'link' );
						if(!$link)
							continue;
						if ($image) : ?>
							<div class="single-link type-image">
								<a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>">
									<img <?php lazySrc($image['ID']); ?> alt="<?php echo $image['alt']; ?>" class="lazyload">
								</a>
							</div>
						<?php else: ?>
							<div class="single-link type-text">
								<a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>">
									<span class="text"><?php echo $link['title'] ?></span>
								</a>
							</div>
						<?php endif; ?>
				    <?php endwhile; ?>
				<?php endif; ?>
			</div>

			<div class="right links">
				<?php if( have_rows( 'top_bar_links', $options ) ): ?>
					<?php while( have_rows( 'top_bar_links', $options ) ): the_row();
						$image = get_sub_field( 'image' );
						$link = get_sub_field( 'link' );
						if(!$link)
							continue;
						if ($image) : ?>
							<div class="single-link type-image">
								<a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>">
									<img <?php lazySrc($image['ID']); ?> alt="<?php echo $image['alt']; ?>" class="lazyload">
								</a>
							</div>
						<?php else: ?>
							<div class="single-link type-text">
								<a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>">
									<span class="text"><?php echo $link['title'] ?></span>
								</a>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>
                <div class="single-link type-text shop">
                    <a href="/shop">
                        <span class="text">Shop</span>
                    </a>
                </div>
				<div class="single-link type-text login">
					<a class="<?php echo is_user_logged_in() ? 'account' : 'login-btn' ?>" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
						<span class="text"><?php echo is_user_logged_in() ? __('Min konto', 'bbh') : __('Log Ind', 'bbh') ?></span>
					</a>
				</div>
			</div>


		</div>
	</div>



	<?php
}


/*=============================================
  = Move mobile header inside our sticky container =
===============================================*/
remove_action( 'generate_after_header', 'generate_menu_plus_mobile_header', 5 );
add_action('generate_header', 'generate_menu_plus_mobile_header', 60);


/*=============================================
          = Add call to action in mobile menu - Slideout =
===============================================*/
add_action('generate_inside_slideout_navigation', 'bbh_add_mobile_nav_cta', 80);
function bbh_add_mobile_nav_cta() {
	// $cta_mobile_repeater = get_field( 'nav_cta_mobile', 'theme_header_settings' );
    $options = 'theme_header_settings';
	?>
    <div class="mobile-cta-container">

    <?php if( have_rows( 'nav_cta_mobile', $options ) ): ?>
        <?php while( have_rows( 'nav_cta_mobile', $options ) ): the_row();
            $link = get_sub_field( 'nav_cta_mobile_btn' );
            $cta_mobile_icon_choice = get_sub_field('cta_mobile_icon_choice');
            if ($cta_mobile_icon_choice == true) {
                $icon = get_sub_field('icomoon');
            }
            else{
                $icon = "no-icon";
            }
            if(!$link)
                continue; ?>
                    <div class="nav-cta-wrap"><a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>" class="nav-cta btn"><?php echo $link['title'] ?> <span class="<?php echo $icon ?>"></span> </a></div>
            <?php endwhile; ?>
    <?php endif; ?>
</div>

	<?php
};

/*=============================================
          = jquery ui =
===============================================*/
add_action('wp_head','bbh_jqury_ui');
function bbh_jqury_ui(){
    ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
    <?php
}

/*===============================================
=          Page Loading Animation           =
===============================================*/
add_action( 'wp_body_open','bbh_page_loading_spinner' );
function bbh_page_loading_spinner() { ?>
    <!-- <div class="global-page-spinner">
        <div class="bbh-loader filter-loader"><div class="spinner round"><lottie-player src="https://vinforsyning.dk/wp-content/themes/vinforsyning/assets/json/lottie-vinglas.json" background="transparent" speed="1" style="width: 20px; margin:auto;" loop="" autoplay=""></lottie-player></div></div>
    </div> -->
<?php }
