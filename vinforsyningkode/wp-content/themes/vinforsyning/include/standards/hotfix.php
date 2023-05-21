<?php

/*-------------- Fix WP SCSS double refresh -------------*/
// if(function_exists('wp_scss_needs_compiling')){
// 	add_action('init', 'bbh_fix_scss_double_refresh');
// 	function bbh_fix_scss_double_refresh(){
// 		if(function_exists('wp_scss_needs_compiling')){
// 			wp_scss_needs_compiling();
// 			remove_action('wp_head', 'wp_scss_needs_compiling');
// 		}
// 	}
// }


/*-------------- Allow SVG uploads -------------*/
function bodhi_svgs_upload_mimes( $mimes = array() ) {

	global $bodhi_svgs_options;

	if ( empty( $bodhi_svgs_options['restrict'] ) || current_user_can( 'administrator' ) ) {

		// allow SVG file upload
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;

	} else {

		return $mimes;

	}

}
add_filter( 'upload_mimes', 'bodhi_svgs_upload_mimes', 99 );

/**
 * Check Mime Types
 */
function bodhi_svgs_upload_check( $checked, $file, $filename, $mimes ) {

	if ( ! $checked['type'] ) {

		$check_filetype		= wp_check_filetype( $filename, $mimes );
		$ext				= $check_filetype['ext'];
		$type				= $check_filetype['type'];
		$proper_filename	= $filename;

		if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
			$ext = $type = false;
		}

		$checked = compact( 'ext','type','proper_filename' );
	}

	return $checked;

}
add_filter( 'wp_check_filetype_and_ext', 'bodhi_svgs_upload_check', 10, 4 );
