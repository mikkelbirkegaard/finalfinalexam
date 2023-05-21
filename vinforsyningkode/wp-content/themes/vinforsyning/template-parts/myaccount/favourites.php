<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
	'<h1><u>%s</u></h1>',
	__('Mine favoritter', 'bbh')
);


global $userFavourites;
if(count($userFavourites) > 0){
	echo do_shortcode('[products ids="'.implode(', ',$userFavourites).'" columns="4"]');
} else{
	echo sprintf(
		'<h4>%s</h4><a class="bbh-btn btn back-to-shop" href="%s">%s</a>',
		__('Du har ikke tilføjet nogen produktet til dine favoritter endnu.', 'bbh'),
		get_permalink( woocommerce_get_page_id( 'shop' ) ),
		__('Gå til shoppen', 'bbh')
	);
}
