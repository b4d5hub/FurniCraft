<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Upsell files
 *
 */
function slicewp_include_files_upsell() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if ( file_exists( $dir_path . 'class-integration-upsell.php' ) )
		include $dir_path . 'class-integration-upsell.php';

	// Include hooks functions
	if ( slicewp_is_integration_active( 'upsell' ) && slicewp_is_integration_plugin_active( 'upsell' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-upsell.php' ) )
			include $dir_path . 'functions-hooks-integration-upsell.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_upsell' );


/**
 * Register the class that handles Upsell related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_upsell( $integrations ) {

	$integrations['upsell'] = 'SliceWP_Integration_Upsell';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_upsell', 120 );


/**
 * Verifies if Upsell is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_upsell( $is_active = false ) {

	if ( class_exists( '\Upsell\Plugin' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_upsell', 'slicewp_is_integration_plugin_active_upsell' );
