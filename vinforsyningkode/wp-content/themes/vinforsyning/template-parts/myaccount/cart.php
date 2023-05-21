<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


echo sprintf(
	'<h1><u>%s</u></h1>',
	__('Min indkøbskurv', 'bbh')
);
echo do_shortcode('[woocommerce_cart]');
