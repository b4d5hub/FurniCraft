<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the "skip_setup_wizard" admin action.
 * 
 */
function slicewp_admin_action_skip_setup_wizard() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_skip_setup_wizard' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	update_option( 'slicewp_setup_wizard_hidden', 1 );
	delete_option( 'slicewp_setup_wizard_current_step' );

	wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_skip_setup_wizard', 'slicewp_admin_action_skip_setup_wizard' );


/**
 * Handles the "finish_setup_wizard" admin action.
 * 
 */
function slicewp_admin_action_finish_setup_wizard() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_finish_setup_wizard' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	update_option( 'slicewp_setup_wizard_hidden', 1 );
	delete_option( 'slicewp_setup_wizard_current_step' );

	wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_finish_setup_wizard', 'slicewp_admin_action_finish_setup_wizard' );


/**
 * Handles the "show_setup_wizard" admin action.
 * 
 */
function slicewp_admin_action_show_setup_wizard() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_show_setup_wizard' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	update_option( 'slicewp_setup_wizard_hidden', '' );
	delete_option( 'slicewp_setup_wizard_current_step' );

	wp_redirect( add_query_arg( array( 'page' => 'slicewp-setup' ), remove_query_arg( array( 'slicewp_action', 'slicewp_token' ) ) ) );
	exit;

}
add_action( 'slicewp_admin_action_show_setup_wizard', 'slicewp_admin_action_show_setup_wizard' );