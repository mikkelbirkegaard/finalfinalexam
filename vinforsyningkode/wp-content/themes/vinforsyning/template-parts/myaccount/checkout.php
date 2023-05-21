<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
	'<h1><u>%s</u></h1>',
	__('Checkout', 'bbh')
);
echo do_shortcode('[woocommerce_checkout]');
?>
<div class="">
	<?php
$rows = get_sub_field('holliday_pick', 'theme_indicative_price_settings');

if( have_rows('holliday_pick', 'theme_indicative_price_settings') ) {
	?> <span id="holidays_dates" style="opacity: 0;"> <?php
    while( have_rows('holliday_pick', 'theme_indicative_price_settings') ) {
        the_row();
        ?>

		<?php

		$holiday_dates = the_sub_field('holliday_dates');
		//echo $holiday_dates;
		echo $holiday_dates . ',';

		//print_r (explode(" ",$holiday_dates));

		?>

		<?php
    }
	?></span><?php
}
?>
</div>



<?php
