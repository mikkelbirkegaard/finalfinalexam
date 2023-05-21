<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/*=============================================
          = Lazybg og lazysrc =
===============================================*/
add_filter('wp_calculate_image_srcset', 'bbh_filter_image_srcset', 10, 2);
function bbh_filter_image_srcset($sources, $size_array){
  	$minSize = 768;
	$firstKey = array_keys($sources);
	if($firstKey < $minSize){
		if(isset($sources[150])){
			unset($sources[150]);
		}
		return $sources;
	}
  	foreach ($sources as $width => $value) {
    	if($width < $minSize){
      		unset($sources[$width]);
    	}
  	}
  	return $sources;
}

function lazyBg($id, $echo = true){
	if(!$id){
		return false;
	}
	$srcset = wp_get_attachment_image_srcset($id);
	if(!$srcset && $src = wp_get_attachment_image_src($id, 'large')) {
		$srcset = $src[0];
	}
	$html = "data-bgset=\"{$srcset}\" data-sizes=\"auto\"";
	if($echo){
		echo $html;
		return true;
	} else{
		return $html;
	}
}

function lazySrc($id, $echo = true){
	if(!$id){
		return false;
	}
	$srcset = wp_get_attachment_image_srcset($id);
	if(!$srcset && $src = wp_get_attachment_image_src($id, 'large')) {
		$srcset = $src[0];
	}
	$html = "data-srcset=\"{$srcset}\" data-sizes='auto'";
	if($echo){
		echo $html;
		return true;
	} else{
		return $html;
	}
}




/*=============================================
          = Gravity forms field =
===============================================*/
/**
 * Populate ACF select field options with Gravity Forms forms
 */
function acf_populate_gf_forms_ids( $field ) {
  if ( class_exists( 'GFFormsModel' ) ) {
    $choices = [];
    foreach ( \GFFormsModel::get_forms() as $form ) {
      $choices[ $form->id ] = $form->title;
    }
    $field['choices'] = $choices;
  }
  return $field;
}
add_filter( 'acf/load_field/name=gravityforms', 'acf_populate_gf_forms_ids' );

/*=============================================
          = Nav menu field =
===============================================*/
function acf_populate_nav_menu_field( $field ) {
  if ( class_exists( 'GFFormsModel' ) ) {
    $choices = [];

	$menus = wp_get_nav_menus();
	$choices['0'] = __('Ingen menu', 'bbh');
    foreach ( $menus as $menu ) {
      	$choices[ $menu->term_id ] = $menu->name;
    }
    $field['choices'] = $choices;
  }
  return $field;
}
add_filter( 'acf/load_field/name=nav_menu', 'acf_populate_nav_menu_field' );


/*=============================================
                = Color field =
===============================================*/

add_filter('acf/load_field/name=theme_color', 'bbh_the_color_field_content');
function bbh_the_color_field_content( $field ){

    $field['type'] = 'select';
    $field['choices'] = array();

	$field['choices']['white'] = 'Hvid';
	$field['choices']['black'] = 'Sort';
    $field['choices']['gray'] = 'Grå';
    $field['choices']['sand'] = 'Sand';
    $field['choices']['brown'] = 'Brun';
	$field['choices']['brown-dark'] = 'Mørk Brun';


    return $field;
}




/*=============================================
  = Acf custom location rule- Only top level terms =
===============================================*/


add_filter('acf/location/rule_types', 'acf_location_rules_types');
function acf_location_rules_types( $choices ) {
  $choices[ 'Forms' ][ 'taxonomy_term_parent' ] = 'Taxonomy term parent only';
  return $choices;
}

add_filter('acf/location/rule_values/taxonomy_term_parent', 'acf_location_rules_values_taxonomy_term_parent');
function acf_location_rules_values_taxonomy_term_parent( $choices ) {

  $choices['true'] = 'True';

  return $choices;
}

add_filter('acf/location/rule_match/taxonomy_term_parent', 'acf_location_rules_match_taxonomy_term_parent', 10, 3);
function acf_location_rules_match_taxonomy_term_parent( $match, $rule, $options ) {
  	// Apply for taxonomies and only to single term edit screen
  	if ( ! isset( $_GET[ 'tag_ID' ] ) ) {
    	return false;
  	}

	// Get the term and ensure it's valid
	$term = get_term( $_GET[ 'tag_ID' ],$_GET[ 'taxonomy' ] );
	if ( ! is_a( $term, 'WP_Term' ) ) {
		return false;
	}

  	// Apply for those that have parent only
  	if($rule['operator'] == '==' ){
	  	return !$term->parent;
  	} elseif($rule['operator'] == '!='){
		return $term->parent;
	}
  	return $match;
}



/*=============================================
          = Video functions  =
===============================================*/
function get_embed_video_src($fieldObj){
	if(!$fieldObj){
		return false;
	}
	// Use preg_match to find iframe src.
	$match = preg_match('/src="(.+?)"/', $fieldObj, $matches);
	if(!$match){
		return false;
	}
	$src = $matches[1];
	// Add extra parameters to src and replcae HTML.
	$src = preg_replace('/www\.youtube\.com/', 'www.youtube-nocookie.com', $src);
	$params = array(
	    'controls'  => 0,
	    'hd'        => 1,
	    'autohide'  => 1,
		'autoplay' => 1,
		'rel' => 0
	);
	$new_src = add_query_arg($params, $src);

	return $new_src;

}


function get_video_player($file = false, $thumbId = 0){
	ob_start();
	if ($file): ?>
		<div class="video-container lazyload" <?php lazyBg($thumbId); ?>>
			<video class="lazyload" playsinline data-src="<?php echo $file['url'] ?>" data-type="<?php echo  $file['mime_type'] ?>"></video>
			<span class="play-btn class-video"></span>
			<span class="mute-btn"></span>
		</div>
	<?php endif;
	return ob_get_clean();
}


/*=============================================
          = Icomoon select2 with icons =
===============================================*/
function icoomon_field_contents( $field ){

        $field['type'] = 'select';
        $field['choices'] = array();
		$field['choices'] ['icon-vinflasker'] = 'Vinflaske';
		$field['choices'] ['icon-plant'] = 'Plante';
		$field['choices'] ['icon-wineglass'] = 'Vinglas';
		$field['choices']['icon-b2b'] = 'B2B';
		$field['choices']['icon-baeredygtighed'] = 'Bæredygtighed';
		$field['choices']['icon-cirkel'] = 'Cirkel';
		//$field['choices']['icon-dinner'] = 'Bæredygtighed 2';
		$field['choices']['icon-eksperter'] = 'Stjerner';
		$field['choices']['icon-eksport'] = 'Eksport';
		$field['choices']['icon-flaske'] = 'Flaske';
		$field['choices']['icon-stjerne'] = 'Stjerne (tom)';
		$field['choices']['icon-fyldt-stjerne'] = 'Stjerne';
		$field['choices']['icon-galleri'] = 'Galleri';
		$field['choices']['icon-hjaelpe-hele-vejen'] = 'Vej';
		$field['choices']['icon-kontodetaljer'] = 'Dokumenter';
		$field['choices']['icon-kurv'] = 'Kurv';
		$field['choices']['icon-leveringsadresse'] = 'Addresse';
		$field['choices']['icon-links'] = 'Ekstern link';
		$field['choices']['icon-log-ud'] = 'Log ud';
		$field['choices']['icon-lup'] = 'Lup';
		$field['choices']['icon-min-konto'] = 'Profil';
		$field['choices']['icon-noegle'] = 'Nøgle';
		$field['choices']['icon-oekonomi'] = 'Økonomi';
		$field['choices']['icon-pil-hoejre'] = 'Pil Højre';
		$field['choices']['icon-pil-op'] = 'Pil op';
		$field['choices']['icon-play'] = 'Play';
		$field['choices']['icon-pose'] = 'Pose';
		$field['choices']['icon-produktkatalog'] = 'Produkt katalog';
		$field['choices']['icon-skraeddersyet'] = 'Menukort';
		$field['choices']['icon-smagning'] = 'Menukort 2';
		$field['choices']['icon-skraldespand'] = 'Skraldespand';
		$field['choices']['icon-sortiment'] = 'Sortiment';
		$field['choices']['icon-statistik'] = 'Statistik';
		$field['choices']['icon-vinglas'] = 'Vinglas';
		$field['choices']['icon-vinpakker'] = 'Vinpakke';
		$field['choices']['icon-vinrejser'] = 'Kontakt';

		// Piktogrammer
		$field['choices']['icon-madpiktogram_Dessert'] = 'Dessert';
		$field['choices']['icon-madpiktogram_Fisk'] = 'Fisk';
		$field['choices']['icon-madpiktogram_Fuglevildt'] = 'Fuglevildt';
		$field['choices']['icon-madpiktogram_Gris'] = 'Gris';
		$field['choices']['icon-madpiktogram_Gryderet'] = 'Gryderet';
		$field['choices']['icon-madpiktogram_Is'] = 'Is';
		$field['choices']['icon-madpiktogram_Kaffe'] = 'Kaffe';
		$field['choices']['icon-madpiktogram_Kalv'] = 'Kalv';
		$field['choices']['icon-madpiktogram_Kylling'] = 'Kylling';
		$field['choices']['icon-madpiktogram_Lam'] = 'Lam';
		$field['choices']['icon-madpiktogram_Okse'] = 'Okse';
		$field['choices']['icon-madpiktogram_Orientalsk'] = 'Orientalsk';
		$field['choices']['icon-madpiktogram_Pasta'] = 'Pasta';
		$field['choices']['icon-madpiktogram_Pizza'] = 'Pizza';
		$field['choices']['icon-madpiktogram_Ravildt'] = 'Råvildt';
		$field['choices']['icon-madpiktogram_Salat'] = 'Salat';
		$field['choices']['icon-madpiktogram_Skaldyr'] = 'Skaldyr';
		$field['choices']['icon-madpiktogram_Trte'] = 'Tærte';

        // Lottie
        $field['choices']['lottie-baeredygtighed'] = 'Lottie: Bæredygtighed';
        $field['choices']['lottie-eksperter'] = 'Lottie: Eksperter';
        $field['choices']['lottie-sortiment'] = 'Lottie: Sortiment';


        $field['wrapper']['class'] = 'icomoon-select-element';
        $field['ui'] = true;

    return $field;
}

add_filter('acf/load_field/name=icomoon', 'icoomon_field_contents');

// enqueue admin js
add_action( 'acf/input/admin_enqueue_scripts', 'bbh_icomoon_select2_scripts' );
function bbh_icomoon_select2_scripts(){
    wp_enqueue_script('bbh_icomoon', get_stylesheet_directory_uri() . '/assets/js/icomoon_admin.js', array( 'jquery', 'select2' ) );
    wp_enqueue_style('vinforsyning_icomoon', 'https://i.icomoon.io/public/e9bd5dc1f6/stjyskVinforsyningtest/style.css', '1.0', 'all');
    $translation_array = array( 'templateUrl' => get_stylesheet_directory_uri() );
    //after wp_enqueue_script
    wp_localize_script( 'bbh_icomoon', 'object_name', $translation_array );
}



/*=============================================
= Experimental version of updating content with acf flexible values =
===============================================*/

function get_article_excerpt($field = 'flexible_sections', $id = null, $length = 55) {
	$postContent = false;
	// check if the flexible content field has rows of data
	if( have_rows($field, $id) ):
		ob_start();
		?>

		<div class="flexible-field-wrapper">
	        <?php // loop through the rows of data
	        while ( have_rows($field, $id) ) : the_row();

				// save layout name as var
	            $slug = get_row_layout();
				if($slug != 'article_text_content'){
					continue;
				}
				// check if layout exist in import folder
				if( file_exists( get_theme_file_path("/include/flexible-content/import/{$slug}.php") ) ) {
	        		include( get_theme_file_path("/include/flexible-content/import/{$slug}.php") );
	        	}

	        endwhile; // END while have_rows() ?>
		</div> <?php // END div.flexible-field-wrapper
		$postContent = ob_get_clean();
		?>
	<?php else :

	endif;
	$length = $length ?: apply_filters( 'excerpt_length', 55 );
    $postContent = preg_replace('/<h1[^>]*>([\s\S]*?)<\/h1[^>]*>/', '', $postContent);
	$postContent = wp_trim_words(strip_tags($postContent), $length);
	$postContent = apply_filters( 'get_the_excerpt', strip_tags($postContent) );
	return apply_filters( 'the_excerpt', $postContent );
};
