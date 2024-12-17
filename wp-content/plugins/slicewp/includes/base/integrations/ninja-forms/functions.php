<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Ninja Forms files
 *
 */
function slicewp_include_files_nfo() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if ( file_exists( $dir_path . 'class-integration-ninja-forms.php' ) )
		include $dir_path . 'class-integration-ninja-forms.php';

	// Include hooks functions
	if ( slicewp_is_integration_active( 'nfo' ) && slicewp_is_integration_plugin_active( 'nfo' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-ninja-forms.php' ) )
			include $dir_path . 'functions-hooks-integration-ninja-forms.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_nfo' );


/**
 * Register the class that handles Ninja Forms related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_nfo( $integrations ) {

	$integrations['nfo'] = 'SliceWP_Integration_Ninja_Forms';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_nfo', 110 );


/**
 * Verifies if Ninja Forms is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_nfo( $is_active = false ) {

	if ( class_exists( 'Ninja_Forms' ) )
		$is_active = true;
	
	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_nfo', 'slicewp_is_integration_plugin_active_nfo' );