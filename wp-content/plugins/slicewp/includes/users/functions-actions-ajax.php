<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AJAX handler for refreshing the data of the affiliate dashboard when applying the filters the affiliate select in their account.
 * 
 */
function slicewp_action_ajax_apply_affiliate_dashboard_filters() {

	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_affiliate_account' ) ) {
		wp_send_json_error();
	}

    if ( empty( $_REQUEST['affiliate_id'] ) ) {
        wp_send_json_error();
    }

    if ( absint( $_REQUEST['affiliate_id'] ) != slicewp_get_current_affiliate_id() && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error();
    }

    $date_range = ( ! empty( $_REQUEST['dashboard-filter-date-range'] ) ? sanitize_text_field( $_REQUEST['dashboard-filter-date-range'] ) : 'past_30_days' );

    $args = array(
        'date_range' => $date_range
    );

    if ( $date_range == 'custom' ) {

        if ( ! empty( $_REQUEST['dashboard-filter-date-start'] ) ) {
            $args['date_start'] = sanitize_text_field( $_REQUEST['dashboard-filter-date-start'] );
        }

        if ( ! empty( $_REQUEST['dashboard-filter-date-end'] ) ) {
            $args['date_end'] = sanitize_text_field( $_REQUEST['dashboard-filter-date-end'] );
        }

    }

    $data = slicewp_build_affiliate_dashboard_data( absint( $_REQUEST['affiliate_id'] ), $args );

    if ( is_null( $data ) ) {

        wp_send_json_error();

    } else {

        wp_send_json_success( $data );

    }

}
add_action( 'wp_ajax_slicewp_action_ajax_apply_affiliate_dashboard_filters', 'slicewp_action_ajax_apply_affiliate_dashboard_filters' );