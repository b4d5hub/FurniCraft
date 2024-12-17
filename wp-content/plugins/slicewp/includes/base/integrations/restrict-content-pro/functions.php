<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Restrict Content Pro files
 *
 */
function slicewp_include_files_rcp() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if( file_exists( $dir_path . 'class-integration-restrict-content-pro.php' ) )
		include $dir_path . 'class-integration-restrict-content-pro.php';

	// Include hooks functions
	if( slicewp_is_integration_active( 'rcp' ) && slicewp_is_integration_plugin_active( 'rcp' ) ) {

		if( file_exists( $dir_path . 'functions-hooks-integration-restrict-content-pro.php' ) )
			include $dir_path . 'functions-hooks-integration-restrict-content-pro.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_rcp' );


/**
 * Register the Restrict Content Pro class
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_rcp( $integrations ) {

	$integrations['rcp'] = 'SliceWP_Integration_Restrict_Content_Pro';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_rcp', 70 );


/**
 * Verifies if Restrict Content Pro is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_rcp( $is_active = false ) {

	if( defined( 'RCP_PLUGIN_VERSION' ) )
		$is_active = true;

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_rcp', 'slicewp_is_integration_plugin_active_rcp' );