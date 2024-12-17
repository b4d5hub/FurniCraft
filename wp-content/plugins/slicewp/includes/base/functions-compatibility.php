<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Popup Maker's admin script collides with our own scripts and breaks our admin pages.
 * 
 * This script deregisters Popup Maker's script from SliceWP's admin pages.
 * 
 */
function slicewp_compatibility_deregister_popup_maker_admin_script() {
	
	if ( empty( $_GET['page'] ) ) {
		return;
	}
	
	if ( false === strpos( $_GET['page'], 'slicewp' ) ) {
		return;
	}
	
	wp_deregister_script( 'pum-admin-general' );
	
}
add_action( 'admin_enqueue_scripts', 'slicewp_compatibility_deregister_popup_maker_admin_script', 100 );


/**
 * Wordfence adds the reCAPTCHA checks for all login attempts.
 * 
 * If the attempt comes from a SliceWP login form we'll block the check if our own reCAPTCHA feature is enabled.
 * 
 */
function slicewp_compatibility_disable_recaptcha_wordfence( $required ) {
	
	if ( empty( $_POST['slicewp_token'] ) ) {
		return $required;
	}
	
	if ( ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) ) {
		return $required;
	}

	if ( empty( slicewp_get_setting( 'enable_recaptcha' ) ) 
		&& empty( slicewp_get_setting( 'enable_turnstile' ) )
		&& empty( slicewp_get_setting( 'enable_hcaptcha' ) )
	) {
		return $required;
	}

	return false;
	
}
add_filter( 'wordfence_ls_require_captcha', 'slicewp_compatibility_disable_recaptcha_wordfence' );


/**
 * Advanced Google reCAPTCHA adds the reCAPTCHA checks for all login attempts.
 * 
 * If the attempt comes from a SliceWP login form we'll block the check if our own reCAPTCHA feature is enabled.
 * 
 */
function slicewp_compatibility_disable_advanced_google_recaptcha_check() {
	
	if ( empty( $_POST['slicewp_token'] ) ) {
		return;
	}
	
	if ( ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) ) {
		return;
	}

	if ( empty( slicewp_get_setting( 'enable_recaptcha' ) ) 
		&& empty( slicewp_get_setting( 'enable_turnstile' ) )
		&& empty( slicewp_get_setting( 'enable_hcaptcha' ) )
	) {
		return;
	}

	// Old versions of this plugin.
	remove_filter( 'wp_authenticate_user', 'advanced_google_recaptcha_process_login_form', 10, 2 );

	// Current version of this plugin.
	remove_filter( 'authenticate', array('WPCaptcha_Functions', 'wp_authenticate_username_password' ), 9999, 3 );
	add_filter( 'authenticate', 'wp_authenticate_username_password', 9999, 3 );
	
}
add_action( 'init', 'slicewp_compatibility_disable_advanced_google_recaptcha_check', 5 );


/**
 * reCaptcha by BestWebSoft adds the reCAPTCHA checks for all login attempts.
 * 
 * If the attempt comes from a SliceWP login form we'll block the check if our own reCAPTCHA feature is enabled.
 * 
 */
function slicewp_compatibility_disable_gglcptch_recaptcha_check() {
	
	if ( ! isset( $_POST['slicewp_token'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) ) {
		return;
	}

	if ( empty( slicewp_get_setting( 'enable_recaptcha' ) ) 
		&& empty( slicewp_get_setting( 'enable_turnstile' ) )
		&& empty( slicewp_get_setting( 'enable_hcaptcha' ) )
	) {
		return;
	}
	
	remove_action( 'authenticate', 'gglcptch_login_check', 21 );
	
}
add_action( 'slicewp_user_action_login_affiliate', 'slicewp_compatibility_disable_gglcptch_recaptcha_check', 20 );


/**
 * Simple Cloudflare Turnstile adds spam protection for all login attempts.
 * 
 * If the attempt comes from a SliceWP login form we'll block the check if our own reCAPTCHA feature is enabled.
 * 
 */
function slicewp_compatibility_disable_simple_cloudflare_turnstile() {
	
	if ( ! isset( $_POST['slicewp_token'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) ) {
		return;
	}

	if ( empty( slicewp_get_setting( 'enable_recaptcha' ) ) 
		&& empty( slicewp_get_setting( 'enable_turnstile' ) )
		&& empty( slicewp_get_setting( 'enable_hcaptcha' ) )
	) {
		return;
	}
	
	remove_action( 'authenticate', 'cfturnstile_wp_login_check', 21, 1 );
	
}
add_action( 'slicewp_user_action_login_affiliate', 'slicewp_compatibility_disable_simple_cloudflare_turnstile', 20 );