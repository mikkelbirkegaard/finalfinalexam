<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*=============================================
          = Remove short description =
===============================================*/

function bbh_remove_product_short_description() {
	remove_meta_box( 'postexcerpt', 'product', 'normal');
}
add_action('add_meta_boxes', 'bbh_remove_product_short_description', 999);



/*=============================================
          = save country and cl as meta key =
===============================================*/
/*
	Country and CL are saved as product attributes (taxonomy).
	To order them numerically or alphabetically we save their values as post_meta on update product.
 */
add_action( 'woocommerce_update_product', 'mp_sync_on_product_save', 10, 1 );
function mp_sync_on_product_save( $product_id ) {
 	$country = wp_get_post_terms(
		$product_id,
		'district',
		array(
			'number' => 1,
			'parent' => 0,
			'fields' => 'names'
	 	)
 	);

	if($country && count($country) > 0){
		update_post_meta($product_id, '_country_name',$country[0]);
	} else{
		update_post_meta($product_id, '_country_name','');
	}

	// get and cast cl size as float before saving
	$_product = wc_get_product($product_id);
	$clSize = $_product->get_attribute('pa_size');
	$clValue = 0;
	if($clSize){
		$clSize = explode(', ', $clSize);

		$string = preg_replace('/[a-zA-Z]/m', '', $clSize[0]);

		if($string){
			$string = trim($string);
			$string = floatval($string);
			$clValue = $string;
		}
	}
	update_post_meta($product_id, '_cl_size', $clValue);

}

//add_action( 'save_post', 'bbh_save_product_custom_meta', 10, 3);
function bbh_save_product_custom_meta($post_id, $post, $update) {
    $post_type = get_post_type($post_id);
    // If this isn't a 'product' post, don't update it.
    if ($post_type != 'product')
        return;

    if (!empty($_POST['attribute_names']) && !empty($_POST['attribute_values'])) {
        $attribute_names = $_POST['attribute_names'];
        $attribute_values = $_POST['attribute_values'];
        foreach ($attribute_names as $key => $attribute_name) {
            switch ($attribute_name) {
                //for color (string)
                case 'pa_size':
                    //it may have multiple color (eg. black, brown, maroon, white) but we'll take only the first color.
                    if (!empty($attribute_values[$key][0])) {
                        update_post_meta($post_id, 'pa_size', $attribute_values[$key][0]);
                    }
                    break;
                default:
                    break;
            }
        }
    }
}
