<?php
add_action( 'wp_enqueue_scripts', 'bbh_enqueue_scripts_styles', 99999 );

function bbh_enqueue_scripts_styles() {
    /*----------  Scripts  ----------*/
    //Lazy sizes
    wp_enqueue_script( 'picturefill', get_stylesheet_directory_uri() . '/assets/js/lazysizes/picturefill.min.js' );
    wp_enqueue_script( 'lazysizes', get_stylesheet_directory_uri() . '/assets/js/lazysizes/lazysizes.min.js' );
    wp_enqueue_script( 'lazysizesbackground', get_stylesheet_directory_uri() . '/assets/js/lazysizes/ls.bgset.min.js' );

    wp_register_script( 'slickjs', get_stylesheet_directory_uri() . '/assets/js/slick/slick.min.js', array( 'jquery' )); // slickjs
    wp_enqueue_script( 'slickjs' );

    wp_enqueue_style( 'slick', get_stylesheet_directory_uri() . '/assets/js/slick/slick.css', '1.0', 'all');
    wp_enqueue_style( 'slicktheme', get_stylesheet_directory_uri() . '/assets/js/slick/slick-theme.css', array( 'slick' ), '1.0', 'all');


	// enter-view.js
	wp_enqueue_script( 'bbh_enter_view', get_stylesheet_directory_uri() . '/assets/js/enter-view/enter-view.min.js', array(), '1.0.0', false);

	//bbh js
    wp_enqueue_script( 'brandbyhandscripts', get_stylesheet_directory_uri() . '/assets/js/bbh_scripts.js', array( 'jquery', 'bbh_enter_view' ), filemtime(STYLESHEETPATH . '/assets/js/bbh_scripts.js'), true );
    wp_enqueue_script( 'brandbyhandsearchajax', get_stylesheet_directory_uri() . '/assets/js/bbh_search_ajax.js', array( 'jquery' ), filemtime(STYLESHEETPATH . '/assets/js/bbh_search_ajax.js'), true );

	wp_register_script( 'nouislider', get_stylesheet_directory_uri() . '/assets/js/nouislider/nouislider.min.js', array( ), filemtime(STYLESHEETPATH . '/assets/js/nouislider/nouislider.min.js'), true );

	wp_register_script( 'bbh_shop_filter', get_stylesheet_directory_uri() . '/assets/js/shop_filter.js', array( 'nouislider' ), filemtime(STYLESHEETPATH . '/assets/js/shop_filter.js'), true );

	$filterData = array(
		'token' => wp_create_nonce('shop_filter_nonce'),
		'rest_token' => wp_create_nonce('wp_rest'),
		'route' => get_rest_url(null, 'vinforsyning/v1/shop_filter'),
	);
	wp_localize_script('bbh_shop_filter', 'filter_data', $filterData);


    /*----------  Styles  ----------*/
    //bootstrap
    wp_enqueue_style( 'bootstrapcss', get_stylesheet_directory_uri() . '/assets/bootstrap/bootstrap.css', '1.0', 'all');


	// theme style
    $themecsspath = get_stylesheet_directory() . '/assets/scss/style.css';
    $style_ver = filemtime( $themecsspath );
	wp_enqueue_style('bbh_style', get_stylesheet_directory_uri() . '/assets/scss/style.css', array(), $style_ver, 'all');
    $generate_settings = wp_parse_args(
        get_option( 'generate_settings', array() ),
        generate_get_defaults()
    );
    $grid = $generate_settings['container_width'];
	if(isset($_GET['container']) && !empty($_GET['container'])){
		$grid = $_GET['container'];
	}
    $custom_css = "
        :root {
            --grid-container-width: {$grid}px;
        }
    ";
    wp_add_inline_style( 'bbh_style', $custom_css );


	// icomoon
	wp_enqueue_style('vinforsyning_icomoon', 'https://i.icomoon.io/public/e9bd5dc1f6/stjyskVinforsyningtest/style.css', '1.0', 'all');

	// Slick
    wp_register_style( 'slick', get_stylesheet_directory_uri() . '/assets/js/slick/slick.css', '1.0', 'all');
    //wp_enqueue_style( 'slicktheme', get_stylesheet_directory_uri() . '/assets/js/slick/slick-theme.css', '1.0', 'all');

	// dequeue woo responsive. We'll do it ourselves.
	wp_dequeue_style('woocommerce-smallscreen');

	// remove some gutenberg stuff
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wc-block-vendors-style');
	wp_dequeue_style('wc-block-style');
}

add_action( 'admin_enqueue_scripts', 'bbh_enqueue_admin_scripts', 99999 );
function bbh_enqueue_admin_scripts() {
    wp_enqueue_script( 'bbh_lottie', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js' );

}
