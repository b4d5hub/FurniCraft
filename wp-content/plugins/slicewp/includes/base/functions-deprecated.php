<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns the commision types that are available, based on what integration is available
 *
 * @deprecated 1.0.7 - No longer used in core and not recommended for external usage.
 * 					   Replaced by slicewp_get_available_commission_types().
 *					   Slated for removal in version 2.0.0
 *
 * @return array
 *
 */
function slicewp_get_active_commission_types() {

	return slicewp_get_available_commission_types();

}


/**
 * Returns an array with SliceWP_Commission objects from the database
 * 
 * @deprecated 1.0.58 - No longer used in core and not recommended for external usage.
 * 					    Replaced by slicewp_get_commissions() function,
 * 						by providing the "fields" argument with the name of the column.
 * 						For example: slicewp_get_commissions( array( 'fields' => $column_name ) )
 *					    Slated for removal in version 2.0.0
 *
 * @param string $column
 * @param array  $args
 * @param bool   $count
 *
 * @return array
 *
 */
function slicewp_get_commissions_column( $column, $args = array(), $count = false ) {

	$column = slicewp()->db['commissions']->get_commissions_column( $column, $args, $count );
	
	/**
	 * Add a filter hook just before returning
	 * 
	 * @deprecated 1.0.58
	 *
	 * @param array $commissions
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_commissions_column', $column, $args, $count );

}


/**
 * Verifies if the given action is currently in process.
 * 
 * @deprecated 1.0.63 - No longer used in core and not recommended for external usage.
 * 						Replaced by slicewp_verify_request_action() function
 *
 * @param string $action
 *
 * @return bool
 *
 */
function slicewp_doing_admin_action( $action ) {

	if ( empty( $_REQUEST['slicewp_action'] ) ) {
		return false;
	}

	if ( $_REQUEST['slicewp_action'] != $action ) {
		return false;
	}

	if ( empty( $_REQUEST['slicewp_token'] ) ) {
		return false;
	}

	if ( ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_' . $action ) ) {
		return false;
	}

	return true;

}


/**
 * Determines whether or not there are SliceWP add-ons on the server.
 * 
 * @deprecated 1.0.86 - No longer used in core and not recommended for external usage.
 *
 * @return bool
 *
 */
function slicewp_add_ons_exist() {

	$plugins = get_plugins();

	foreach ( $plugins as $plugin_slug => $plugin_details ) {

		if ( 0 === strpos( $plugin_slug, 'slicewp-add-on' ) ) {
			return true;
		}

	}

	return false;

}