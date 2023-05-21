<?php
register_meta('post', 'bbh_last_updated', [
		'object_subtype'	=> 'product',
        'show_in_rest'		=> true,
        'single'		=> true,
]);

// function bbh_cron_schedule($schedules){
//     if(!isset($schedules["1hour"])){
//         $schedules["1hour"] = array(
//             'interval' => 60*60,
//             'display' => __('Once every hour'));
//     }
//
//     return $schedules;
// }
// add_filter('cron_schedules','bbh_cron_schedule');
//
// if (!wp_next_scheduled('bbh_product_update')) {
// 	wp_schedule_event( time(), '1hour', 'bbh_product_update' );
// }
//add_action ( 'bbh_product_update', 'bbh_update_products' );

function bbh_update_products(){
	$date = date("Y/m/d H:i:s");
	// args to fetch all products
	$args = array(
	    'post_type' => 'product',
	    'posts_per_page' => -1,
		'post__in' => array(92379)
	);
	// create a custom query
	$products = new WP_Query( $args );
	// if products were returned...
	if ( $products->have_posts() ):
	    // loop over them....
	    while ( $products->have_posts() ): $products->the_post();
			$product_id = get_the_ID();
			if ($product_id == 92379) {
				$rows = get_field('reservations', get_the_ID());

				if ($rows && is_string($rows)) {
					$repeater_lines = intval($rows);
					$reservations = [];
				for ($i = 0; $i <= $rows-1; $i++) {
					$user_id = get_field('reservations_'.$i.'_user_id', get_the_ID());
					$product_id = get_field('reservations_'.$i.'_product_id', get_the_ID());
					$quantity = get_field('reservations_'.$i.'_quantity', get_the_ID());
					$line = [
						'bbh_user_id' => $user_id,
				        'bbh_product_id' => $product_id,
				        'bbh_quantity' => $quantity
					];
					$reservations[] = $line;
					//if( current_user_can( 'administrator' ) ){ d($quantity); };

				}
				//if( current_user_can( 'administrator' ) ){ d($line); };
				//update_post_meta(get_the_ID(), 'bbh_last_updated', $line);
				//if( current_user_can( 'administrator' ) ){ d($json); };
				// if( current_user_can( 'administrator' ) ){ d($formatted); };
				$arg = array(
			    'ID'            => get_the_ID(),
				'meta_input' => array(
			    'bbh_last_updated' => $date,
			   	)
				);
				wp_update_post( $arg );
				//update_field( 'bbh_test_repeater', $reservations, get_the_ID());

				}
				$rows = [
				  [
				    'bbh_user_id' => 'Something Great',
				    'bbh_product_id' => 'Great.org',
				    'bbh_quantity' => 1,
				  ],
				  [
				    'bbh_user_id' => 'Even Better',
				    'bbh_product_id' => 'Better.org',
				    'bbh_quantity' => 2,
				  ],
				  [
				    'bbh_user_id' => 'Better Still',
				    'bbh_product_id' => 'BetterStill.com',
				    'bbh_quantity' => 3,
				  ],
				];
				//update_field( 'bbh_test_repeater', $rows, get_the_ID());

			}
	    endwhile;
	endif;
	}
	//bbh_update_products();
 ?>
