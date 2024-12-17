<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the User area.
 *
 */
function slicewp_include_files_user_shortcodes() {

	// Get affiliate admin dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include actions.
	if ( file_exists( $dir_path . 'functions-actions-shortcodes.php' ) ) {
		include $dir_path . 'functions-actions-shortcodes.php';
	}

	if ( file_exists( $dir_path . 'functions-shortcodes.php' ) ) {
		include $dir_path . 'functions-shortcodes.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_user_shortcodes' );


/**
 * Grabs the password reset key and user ID and sets them in a cookie.
 *
 */
function slicewp_template_redirect_catch_password_reset_key() {

	if ( empty( $_GET['key'] ) || empty( $_GET['id'] ) ) {
		return;
	}

	if ( ! is_singular() ) {
		return;
	}

	$page_id_reset_password = absint( slicewp_get_setting( 'page_affiliate_reset_password', 0 ) );

	if ( empty( $page_id_reset_password ) ) {
		return;
	}

	if ( get_the_ID() != $page_id_reset_password ) {
		return;
	}

	$user_id = absint( $_GET['id'] );

	// If the user is logged in, make sure the reset password key is for them.
	$logged_in_user_id = get_current_user_id();

	if ( $logged_in_user_id && $logged_in_user_id !== $user_id ) {

		slicewp_user_notices()->register_notice( 'reset_password_key_not_matching', '<p>' . __( 'This password reset key is for a different user account. Please log out and try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_key_not_matching' );

		return;

	}


	// Set the reset password cookie.
	$reset_password_cookie = 'wp-resetpass-' . COOKIEHASH;
	$reset_password_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

	setcookie( $reset_password_cookie, sprintf( '%d:%s', $user_id, wp_unslash( $_GET['key'] ) ), 0, $reset_password_path, COOKIE_DOMAIN, is_ssl(), true );

	// Redirect to reset password form.
	wp_safe_redirect( add_query_arg( array( 'show-reset-form' => 'true' ), get_permalink( $page_id_reset_password ) ) );
	exit;

}
add_action( 'template_redirect', 'slicewp_template_redirect_catch_password_reset_key' );


/**
 * Registers the password reset confirmation message.
 *
 */
function slicewp_reset_password_confirmation_message() {

	if ( empty( $_GET['password-reset'] ) ) {
		return;
	}

	slicewp_user_notices()->register_notice( 'reset_password_confirmation', '<p>' . __( 'Your password has been reset successfully.', 'slicewp' ) . '</p>', 'updated' );
    slicewp_user_notices()->display_notice( 'reset_password_confirmation' );

}
add_action( 'slicewp_user_notices', 'slicewp_reset_password_confirmation_message', 9 );


/**
 * Block affiliate registrations that are using email addresses found in the blocklist.
 * 
 * @param bool
 * 
 * @return bool
 * 
 */
function slicewp_allow_affiliate_registration_verify_spam_blocklist( $allow ) {

	// Get blocklist.
	$blocklist = array_unique( array_filter( array_map( 'trim', preg_split( "/\r\n|\n|\r/", slicewp_get_setting( 'affiliate_registration_blocklist', '' ) ) ) ) );

	// Bail if blocklist is empty.
	if ( empty( $blocklist ) ) {
		return $allow;
	}

	// Get the email address being used.
	if ( ! empty( $_POST['user_email'] ) ) {
		
		$user_email = trim( $_POST['user_email'] );

	} elseif ( is_user_logged_in() ) {

		$user 		= get_userdata( get_current_user_id() );
		$user_email = $user->user_email;

	} else {
		$user_email = '';
	}

	// Bail if we don't have an email address for the user.
	if ( empty( $user_email ) ) {
		return $allow;
	}

	// Go through each blocklist item and check against the email address.
	foreach ( $blocklist as $item ) {

		if ( $item == $user_email ) {
			$allow = false;
			continue;
		}

		if ( substr_compare( $user_email, '@' . $item, -strlen( '@' . $item ) ) === 0 ) {
			$allow = false;
			continue;
		}

		if ( strpos( $item, '^' ) === 0 && strpos( $user_email, substr( $item, 1 ) ) === 0 ) {
			$allow = false;
			continue;
		}

		if ( strpos( $item, '*' ) === 0 && strpos( $user_email, substr( $item, 1 ) ) !== false ) {
			$allow = false;
			continue;
		}

	}

	// Register the notification.
	if ( ! $allow ) {

		slicewp_user_notices()->register_notice( 'spam_blocked', '<p>' . __( "Action could not be completed.", 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'spam_blocked' );

	}

	return $allow;

}
add_filter( 'slicewp_allow_affiliate_registration', 'slicewp_allow_affiliate_registration_verify_spam_blocklist', 50 );