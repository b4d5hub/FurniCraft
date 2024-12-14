<?php
/**
 * Handle ajax requests
 */

/**
 * Authenticate a user, confirming the login credentials are valid.
 */
function sober_login_authenticate() {
	// check_ajax_referer( 'woocommerce-login', 'security' );

	$creds = array(
		'user_login'    => trim( wp_unslash( $_POST['username'] ) ),
		'user_password' => $_POST['password'],
		'remember'      => ! empty( $_POST['rememberme'] ),
	);

	// Apply WooCommerce filters
	if ( class_exists( 'WooCommerce' ) ) {
		$validation_error = new WP_Error();
		$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

		if ( $validation_error->get_error_code() ) {
			wp_send_json_error( $validation_error->get_error_message() );
		}

		if ( empty( $creds['user_login'] ) ) {
			wp_send_json_error( esc_html__( 'Username is required.', 'sober' ) );
		}

		// On multisite, ensure user exists on current site, if not add them before allowing login.
		if ( is_multisite() ) {
			$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

			if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
				add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
			}
		}

		$creds = apply_filters( 'woocommerce_login_credentials', $creds );
	}

	$user = wp_authenticate( $creds['user_login'], $creds['user_password'] );

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( $user->get_error_message() );
	} else {
		wp_send_json_success();
	}
}

add_action( 'wp_ajax_nopriv_sober_login_authenticate', 'sober_login_authenticate' );

/**
 * Add 'monthly' cron interval
 *
 * @param  array $schedules
 * @return array
 */
function sober_add_cron_interval( $schedules ) {
	$schedules['monthly'] = array(
		'interval' => 2505600, // 29 days.
		'display'  => esc_html__( 'Monthly', 'sober' ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'sober_add_cron_interval' );

/**
 * Schedule events
 */
function sober_cron_events() {
	if ( ! wp_next_scheduled( 'sober_monthly_tasks' ) ) {
		wp_schedule_event( time(), 'monthly', 'sober_monthly_tasks' );
	}
}

add_action( 'sober_monthly_tasks', 'sober_refresh_instagram_access_token' );
add_action( 'wp', 'sober_cron_events' );
