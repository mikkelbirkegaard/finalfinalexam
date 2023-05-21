<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;



// Check function exists.
if( function_exists('acf_add_options_page') ) {

    // Add parent.
    $parent = acf_add_options_page(array(
		'page_title'  => __('Tema Indstillinger', 'bbh'),
		'menu_title'  => __('Tema Indstillinger', 'bbh'),
		'menu_slug'   => 'theme-general-settings',
		'capability'  => 'edit_posts',
		'position'    => '3',
		'redirect'    => true,
		'post_id'     => 'options',
		'icon_url'    => 'dashicons-layout',
    ));

    // Add header page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Header Indstillinger', 'bbh'),
        'menu_title'  => __('Header', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_header_settings'
    ));

	// Add footer page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Footer Indstillinger', 'bbh'),
        'menu_title'  => __('Footer', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_footer_settings'
    ));

	// Add blog page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Blog', 'bbh'),
        'menu_title'  => __('Blog', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_blog_settings'
    ));

	// Add login popup page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Login popup', 'bbh'),
        'menu_title'  => __('Login', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_login_settings'
    ));

	// Add shop page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Shop', 'bbh'),
        'menu_title'  => __('Shop', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_shop_settings'
    ));

	// Add shop page.
    $child = acf_add_options_page(array(
        'page_title'  => __('Min Konto', 'bbh'),
        'menu_title'  => __('Min Konto', 'bbh'),
        'parent_slug' => $parent['menu_slug'],
		'post_id' => 'theme_account_settings'
    ));

	// Add parent.
    $parent = acf_add_options_page(array(
		'page_title'  => __('Vejledende udsalgspris', 'bbh'),
		'menu_title'  => __('Vejledende udsalgspris', 'bbh'),
		'menu_slug'   => 'theme-indicative-price-settings',
		'capability'  => 'edit_posts',
		'position'    => '4',
		'redirect'    => true,
		'post_id'     => 'theme_indicative_price_settings',
		'icon_url'    => 'dashicons-money',
    ));

}
