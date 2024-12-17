<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Studiocart files
 *
 */
function slicewp_include_files_stc() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if ( file_exists( $dir_path . 'class-integration-studiocart.php' ) )
		include $dir_path . 'class-integration-studiocart.php';

	// Include hooks functions
	if ( slicewp_is_integration_active( 'stc' ) && slicewp_is_integration_plugin_active( 'stc' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-studiocart.php' ) )
			include $dir_path . 'functions-hooks-integration-studiocart.php';
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_stc' );


/**
 * Register the class that handles Studiocart related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_stc( $integrations ) {

	$integrations['stc'] = 'SliceWP_Integration_Studiocart';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_stc', 70 );


/**
 * Verifies if Studiocart is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_stc( $is_active = false ) {

	if ( defined( 'NCS_CART_VERSION' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_stc', 'slicewp_is_integration_plugin_active_stc' );