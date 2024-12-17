<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the SureCart files.
 *
 */
function slicewp_include_files_surecart() {

	// Get legend dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class.
	if ( file_exists( $dir_path . 'class-integration-surecart.php' ) ) {
        include $dir_path . 'class-integration-surecart.php';
    }

	// Include hooks functions.
	if ( slicewp_is_integration_active( 'surecart' ) && slicewp_is_integration_plugin_active( 'surecart' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-surecart.php' ) ) {
            include $dir_path . 'functions-hooks-integration-surecart.php';
        }
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_surecart' );


/**
 * Register the class that handles SureCart related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_surecart( $integrations ) {

	$integrations['surecart'] = 'SliceWP_Integration_SureCart';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_surecart', 120 );


/**
 * Verifies if SureCart is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_surecart( $is_active = false ) {

	if ( class_exists( 'SureCart' ) ) {
        $is_active = true;
    }

	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_surecart', 'slicewp_is_integration_plugin_active_surecart' );
