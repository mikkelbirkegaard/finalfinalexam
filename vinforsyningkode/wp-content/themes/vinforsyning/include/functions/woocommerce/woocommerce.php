<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/*=============================================
  = This file includes all the WooCommerce logic =
===============================================*/

if (file_exists(dirname(__FILE__) . '/signup.php')) {
	include(dirname(__FILE__) . '/signup.php');
}

if (file_exists(dirname(__FILE__) . '/custom_pricing.php')) {
	include(dirname(__FILE__) . '/custom_pricing.php');
}

if (file_exists(dirname(__FILE__) . '/helper-functions.php')) {
	include(dirname(__FILE__) . '/helper-functions.php');
}

if (file_exists(dirname(__FILE__) . '/taxonomies.php')) {
	include(dirname(__FILE__) . '/taxonomies.php');
}

if (file_exists(dirname(__FILE__) . '/my_account.php')) {
	include(dirname(__FILE__) . '/my_account.php');
}

if (file_exists(dirname(__FILE__) . '/archive.php')) {
	include(dirname(__FILE__) . '/archive.php');
}

if (file_exists(dirname(__FILE__) . '/single.php')) {
	include(dirname(__FILE__) . '/single.php');
}

if (file_exists(dirname(__FILE__) . '/admin.php')) {
	include(dirname(__FILE__) . '/admin.php');
}


if (file_exists(dirname(__FILE__) . '/filter.php')) {
	include(dirname(__FILE__) . '/filter.php');
}

if (file_exists(dirname(__FILE__) . '/rest_api.php')) {
	include(dirname(__FILE__) . '/rest_api.php');
}


if (file_exists(dirname(__FILE__) . '/cart.php')) {
	include(dirname(__FILE__) . '/cart.php');
}


if (file_exists(dirname(__FILE__) . '/checkout.php')) {
	include(dirname(__FILE__) . '/checkout.php');
}

if (file_exists(dirname(__FILE__) . '/customer_info.php')) {
	include(dirname(__FILE__) . '/customer_info.php');
}
