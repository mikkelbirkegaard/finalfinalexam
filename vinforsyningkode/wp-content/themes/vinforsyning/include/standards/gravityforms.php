<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'gform_after_save_form', 'leaven_always_enable_honeypot', 10, 2 );
/**
 * Always enable Gravity Forms' anti-spam honeypot.
 * @param $form_meta
 * @param $is_new
 */
function leaven_always_enable_honeypot( $form_meta, $is_new ) {
    if ( ! $is_new )
        return;
	if(! class_exists('GFAPI'))
		return;

    $form_meta['enableHoneypot'] = true;
    GFAPI::update_form( $form_meta );
}
