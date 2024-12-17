<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Update the "list_table_items_per_page" user preferences value.
 *
 */
function slicewp_user_action_update_user_preferences_list_table_items_per_page() {

    // Verify for nonce.
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_user_preferences_list_table_items_per_page' ) ) {
        return;
    }

    if ( empty( $_POST['list_table_items_per_page'] ) || empty( absint( $_POST['list_table_items_per_page'] ) ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        return;
    }

    $items_per_page   = absint( $_POST['list_table_items_per_page'] );
    $per_page_options = slicewp_get_list_table_items_per_page_options();

    if ( ! in_array( $items_per_page, $per_page_options ) ) {
        $items_per_page = 10;
    }

    // Update the "list_table_items_per_page" user preference.
    $user_preferences = get_user_meta( get_current_user_id(), 'slicewp_user_preferences', true );
    $user_preferences = ( ! empty( $user_preferences ) && is_array( $user_preferences ) ? $user_preferences : array() );

    $user_preferences['list_table_items_per_page'] = $items_per_page;

    update_user_meta( get_current_user_id(), 'slicewp_user_preferences', $user_preferences );

    // Remove the "page_number" query arguments.
    $removable_query_args = array();

    foreach ( $_GET as $key => $value ) {

        if ( strpos( $key, 'page_number' ) === 0 ) {
            $removable_query_args[] = $key;
        }

    }
    
	// Redirect back to the page.
    wp_redirect( remove_query_arg( $removable_query_args, add_query_arg( $_GET ) ) );
    exit;

}
add_action( 'slicewp_user_action_update_user_preferences_list_table_items_per_page', 'slicewp_user_action_update_user_preferences_list_table_items_per_page', 50 );