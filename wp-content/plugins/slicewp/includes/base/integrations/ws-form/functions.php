<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the WS Form files.
 *
 */
function slicewp_include_files_wsf() {

	// Get legend dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class.
	if ( file_exists( $dir_path . 'class-integration-ws-form.php' ) ) {
        include $dir_path . 'class-integration-ws-form.php';
    }
    
	// Include hooks functions
	if ( slicewp_is_integration_active( 'wsf' ) && slicewp_is_integration_plugin_active( 'wsf' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-ws-form.php' ) ) {
            include $dir_path . 'functions-hooks-integration-ws-form.php';
        }
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_wsf' );


/**
 * Register the class that handles WS Form related actions.
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_wsf( $integrations ) {

	$integrations['wsf'] = 'SliceWP_Integration_WS_Form';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_wsf', 110 );


/**
 * Verifies if WS Form is active.
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_wsf( $is_active = false ) {

	if ( defined( 'WS_FORM_NAME' ) ) {
        $is_active = true;
    }
	
	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_wsf', 'slicewp_is_integration_plugin_active_wsf' );