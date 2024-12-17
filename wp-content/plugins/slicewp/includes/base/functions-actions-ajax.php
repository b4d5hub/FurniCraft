<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AJAX callback that returns an array of data from WP_User objects
 *
 */
function slicewp_action_ajax_get_users() {

	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_user_search' ) ) {
		wp_die(0);
	}

	if ( ! current_user_can( 'list_users' ) ) {
		wp_die( 0 );
	}

	$affiliates   = ( ! empty( $_REQUEST['affiliates'] ) ? $_REQUEST['affiliates'] : '' );
	$user_role    = ( ! empty( $_REQUEST['user_role'] ) ? $_REQUEST['user_role'] : '' );
	$return_value = ( ! empty( $_REQUEST['return_value'] ) ? $_REQUEST['return_value'] : 'user_id' );
	$users 		  = array();
	$return 	  = array();

	// Prepare users arguments.
	$args = array(
		'number' 		 => -1,
		'role'			 => $user_role,
		'search_columns' => array( 'user_login', 'user_email', 'display_name' ),
		'search' 		 => ( ! empty( $_REQUEST['term'] ) ? '*' . trim( $_REQUEST['term'] ) . '*' : '' )
	);

	// Get all users matching the search terms.
	$users = get_users( $args );

	// Save the user ID => affiliate ID pairs.
	$affiliates_ids = array();

	// Filter out non-affiliate users. We only need affiliates here.
	if ( $affiliates == 'include' ) {

		foreach ( $users as $key => $user ) {

			$affiliate = slicewp_get_affiliate_by_user_id( $user->ID );

			if ( ! is_null( $affiliate ) ) {

				$affiliates_ids[$user->ID] = $affiliate->get( 'id' );
				continue;

			}

			unset( $users[$key] );

		}

	}

	// Filter out affiliate users. We only need non-affiliates here.
	if ( $affiliates == 'exclude' ) {

		foreach ( $users as $key => $user ) {

			$affiliate = slicewp_get_affiliate_by_user_id( $user->ID );

			if ( is_null( $affiliate ) ) {
				continue;
			}

			unset( $users[$key] );

		}

	}

	// Reset keys.
	$users = array_values( $users );

	// Filter the results before returning.
	foreach ( $users as $user ) {

		$display_name = $user->first_name . ' ' . $user->last_name;
		$display_name = ( ! empty( trim( $display_name ) ) ? $display_name : $user->display_name );

		$return[] = array(
			'label' => $display_name . ' (' . $user->user_email . ')',
			'value' => ( ( $return_value == 'user_id' || empty( $affiliates_ids ) ) ? $user->ID : ( $affiliates_ids[$user->ID] ) )
		);

	}

	// Return the options.
	wp_send_json( $return );

}
add_action( 'wp_ajax_slicewp_action_ajax_get_users', 'slicewp_action_ajax_get_users' );


/**
 * Attempts to register a website with our server.
 *
 */
function slicewp_action_ajax_register_website() {

	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_settings' ) ) {
		wp_die(0);
	}

	if ( empty( $_POST['license_key'] ) ) {
		wp_die(0);
	}

	$license_key = sanitize_text_field( $_POST['license_key'] );
	$website_url = get_site_url();

	// Call the API link
	$response = wp_remote_get( add_query_arg( array( 'edde_api_action' => 'register_website', 'license_key' => $license_key, 'url' => $website_url ), 'https://slicewp.com/' ), array( 'timeout' => 30, 'sslverify' => false, 'headers' => array( 'Cache-Control' => 'no-cache' ) ) );

	// If the connection isn't successfull, return
	if ( is_wp_error( $response ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be registered. WP Error: ' . $response->get_error_message() );

		if ( strpos( $response->get_error_message(), 'cURL error' ) !== false ) {
			wp_send_json( array( 'success' => false, 'data' => array( 'message' => '<p>' . $response->get_error_message() . '</p><p>' . '<a href="https://slicewp.com/docs/license-key-issues/#curl-error" target="_blank">' . __( 'Click here to learn how to fix this error', 'slicewp' ) . '</a>' . '</p>' ) ) );
		} else {
			wp_send_json( array( 'success' => false, 'data' => array( 'message' => '<p>' . __( 'Something went wrong. Could not register the website.', 'slicewp' ) . '</p><p>' . sprintf( __( 'Error: %s', 'slicewp' ), $response->get_error_message() ) . '</p>'  ) ) );
		}

	}

	// Get the response's body
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// If the website could not be registered, return the error
	if ( ! empty( $body['error'] ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be registered. API return error: ' . $body['error'] );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => slicewp_get_api_action_response_error( 'register_website', $body['error'] ) ) ) );

	}

	// Log the success
	slicewp_add_log( 'System: Website was successfully registered.' );

	// Save the license key
	update_option( 'slicewp_license_key', $license_key );

	// Save license key data
	update_option( 'slicewp_license_key_data', $body );

	// Set the website as registered
	update_option( 'slicewp_website_registered', true );

	// Return with a success message
	wp_send_json( array( 'success' => true, 'data' => array( 'message' => __( 'Your website has been successfully registered.', 'slicewp' ) ) ) );

}
add_action( 'wp_ajax_slicewp_action_ajax_register_website', 'slicewp_action_ajax_register_website' );


/**
 * Attempts to deregister a website from our server
 *
 */
function slicewp_action_ajax_deregister_website() {

	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_settings' ) ) {
		wp_die(0);
	}

	if ( empty( $_POST['license_key'] ) ) {
		wp_die(0);
	}

	$license_key = sanitize_text_field( $_POST['license_key'] );
	$website_url = get_site_url();

	// Call the API link
	$response = wp_remote_get( add_query_arg( array( 'edde_api_action' => 'deregister_website', 'license_key' => $license_key, 'url' => $website_url ), 'https://slicewp.com/' ), array( 'timeout' => 30, 'sslverify' => false, 'headers' => array( 'Cache-Control' => 'no-cache' ) ) );

	// If the connection isn't successfull, return
	if ( is_wp_error( $response ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be deregistered. WP Error: ' . $response->get_error_message() );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => __( 'Something went wrong. Could not activate the website. Please try again.', 'slicewp' ) ) ) );

	}

	// Get the response's body
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// If the website could not be registered, return the error
	if ( ! empty( $body['error'] ) ) {

		// Log the error
		slicewp_add_log( 'System: Website could not be deregistered. API return error: ' . $body['error'] );

		wp_send_json( array( 'success' => false, 'data' => array( 'message' => slicewp_get_api_action_response_error( 'deregister_website', $body['error'] ) ) ) );

	}

	// Log the success
	slicewp_add_log( 'System: Website was successfully deregistered.' );

	// Save the license key
	delete_option( 'slicewp_license_key' );

	// Save license key data
	delete_option( 'slicewp_license_key_data' );

	// Set the website as registered
	delete_option( 'slicewp_website_registered' );

	// Return with a success message
	wp_send_json( array( 'success' => true, 'data' => array( 'message' => __( 'Your website has been successfully deregistered.', 'slicewp' ) ) ) );

}
add_action( 'wp_ajax_slicewp_action_ajax_deregister_website', 'slicewp_action_ajax_deregister_website' );


/**
 * AJAX callback that returns an array of data from WP_Posts objects.
 *
 */
function slicewp_action_ajax_get_posts() {
	
	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_post_search' ) ) {
		wp_die( 0 );
	}
	
	if ( ! current_user_can( 'administrator' ) ) {
		wp_die( 0 );
	}
	
	if ( empty( $_REQUEST['query_args'] ) ) {
		wp_die( 0 );
	}
	
	// Prepare arguments for WP Query.
	$query_args = ( ! empty( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : array() );
	$query_args['search_title'] = ( ! empty( $_REQUEST['term'] ) ? trim( $_REQUEST['term'] ) : '' );

	// Get the posts.
	add_filter( 'posts_where', 'slicewp_add_posts_title_search_wp_query', 10, 2 );
	$wp_query = new WP_Query( $query_args );
	remove_filter( 'posts_where', 'slicewp_add_posts_title_search_wp_query', 10, 2 );

	$return = array();

	// Check if there are any posts.
	if ( $wp_query->have_posts() ) {

		// Parse all the posts.
		foreach ( $wp_query->posts as $post ) {

			$return[] = array(
				'id'    => $post->ID,
				'title' => $post->post_title
			);

		}

	}

	echo json_encode( $return );
	wp_die();

}
add_action( 'wp_ajax_slicewp_action_ajax_get_posts', 'slicewp_action_ajax_get_posts' );


/**
 * Activates an add-on.
 * 
 */
function slicewp_action_ajax_activate_add_on() {

	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_activate_deactivate_add_on' ) ) {
		wp_die( 0 );
	}
	
	if ( empty( $_REQUEST['add_on'] ) ) {
		wp_die( 0 );
	}

	$add_on_slug = sanitize_text_field( $_REQUEST['add_on'] );

	// Bail early if the add-on is not registered.
	if ( empty( slicewp()->add_ons[$add_on_slug] ) ) {

		wp_send_json_error();

	}

	// If the add-on is already active, return with success.
	if ( slicewp()->add_ons[$add_on_slug]->is_active() ) {

		wp_send_json_success();

	}

	// Activate add-on and return success/error.
	if ( slicewp()->add_ons[$add_on_slug]->activate() ) {

		wp_send_json_success();

	} else {

		wp_send_json_error();

	}

}
add_action( 'wp_ajax_slicewp_action_ajax_activate_add_on', 'slicewp_action_ajax_activate_add_on' );


/**
 * Deactivates an add-on.
 * 
 */
function slicewp_action_ajax_deactivate_add_on() {

	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_activate_deactivate_add_on' ) ) {
		wp_die( 0 );
	}
	
	if ( empty( $_REQUEST['add_on'] ) ) {
		wp_die( 0 );
	}

	$add_on_slug = sanitize_text_field( $_REQUEST['add_on'] );

	// Bail early if the add-on is not registered.
	if ( empty( slicewp()->add_ons[$add_on_slug] ) ) {

		wp_send_json_error();

	}

	// If the add-on is already inactive, return with success.
	if ( ! slicewp()->add_ons[$add_on_slug]->is_active() ) {

		wp_send_json_success();

	}

	// Deactivate add-on and return success/error.
	if ( slicewp()->add_ons[$add_on_slug]->deactivate() ) {

		wp_send_json_success();

	} else {

		wp_send_json_error();

	}

}
add_action( 'wp_ajax_slicewp_action_ajax_deactivate_add_on', 'slicewp_action_ajax_deactivate_add_on' );