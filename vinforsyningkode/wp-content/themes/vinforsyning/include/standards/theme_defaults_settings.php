<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*=============================================
          = Theme settings =
===============================================*/
function bbh_get_theme_settings(){
	if( !file_exists(get_stylesheet_directory() . '/assets/json/wp-standard-settings.json') ){
		return false;
	}

	$file = file_get_contents(get_stylesheet_directory() . '/assets/json/wp-standard-settings.json');
	$file = json_decode($file, true);
	if(!$file || is_null($file)){
		return false;
	}

	return $file;
}

function bbh_run_activate_plugin( $plugin ) {
    $current = get_option( 'active_plugins' );
    $plugin = plugin_basename( trim( $plugin ) );

    if ( !in_array( $plugin, $current ) ) {
        $current[] = $plugin;
        sort( $current );
        do_action( 'activate_plugin', trim( $plugin ) );
        update_option( 'active_plugins', $current );
        do_action( 'activate_' . trim( $plugin ) );
        do_action( 'activated_plugin', trim( $plugin) );
    }

    return null;
}

add_action('after_setup_theme', 'bbh_run_theme_setup');
function bbh_run_theme_setup(){
	$hasRun = get_option('bbh_ran_theme_setup', false);
	if(!!$hasRun){
		return false;
	}


	$settings = bbh_get_theme_settings();
	if( isset($settings['modules']) ){
		// activate plugin before activating modules
		bbh_run_activate_plugin('gp-premium/gp-premium.php');

		foreach ( ( array ) $settings['modules'] as $key => $val ) {
			update_option( $val, 'activated' );
		}
	}

	if( isset( $settings['mods']) ){
		foreach ( ( array ) $settings['mods'] as $key => $val ) {
			set_theme_mod( $key, $val );
		}
	}

	if( isset($settings['options']) ){
		foreach ( ( array ) $settings['options'] as $key => $val ) {
			update_option( $key, $val );
		}
	}

	if( isset( $settings['wp'] ) ){
		foreach( ( array ) $settings['wp'] as $key => $val ) {
			update_option( $key, $val );
		}
	}

	// Delete existing dynamic CSS cache
	delete_option( 'generate_dynamic_css_output' );
	delete_option( 'generate_dynamic_css_cached_version' );

	// update option so we know we have run the setup
	update_option('bbh_ran_theme_setup', true);

}
