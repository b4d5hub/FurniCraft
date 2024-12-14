<?php
/**
 * Includes Clarity custom hooks available for use by other plugins.
 *
 * @package Clarity
 * @since 0.10.0
 */

add_filter( 'clrt_integrate_with_clarity', 'clrt_integrate_with_clarity_handler' );

/**
 * Integrates the user's WordPress site with Clarity and invites the site users to the created project.
 *
 * @since 0.10.0
 *
 * @param string $referrer This value is sent from the external plugin calling the hook.
 * @return bool|WP_Error True on success or 'WP_Error' in case of failure.
 */
function clrt_integrate_with_clarity_handler( $referrer ) {
	// Ensure the current user has admin privileges.
	if ( ! current_user_can( 'install_plugins' ) ) {
		return new WP_Error( 'invalid_user_role', 'This hook requires admin privileges' );
	}

	$is_integrated = get_option( 'clarity_project_id' );

	if ( $is_integrated ) {
		return true;
	}

	$project_id   = clrt_get_clarity_wordpress_site_id();
	$url          = get_site_url();
	$site_name    = wp_parse_url( $url, PHP_URL_HOST );
	$users        = clrt_get_mapped_users();
	$integrations = clrt_get_integrations();

	$body = wp_json_encode(
		array(
			'projectId'    => $project_id,
			'url'          => $url,
			'siteName'     => $site_name,
			'users'        => $users,
			'integrations' => $integrations,
		)
	);

	$api_url = "https://clarity.azure-api.net/provision?referrer=$referrer";

	$args = array(
		'body'    => $body,
		'headers' => array(
			'Content-Type' => 'application/json',
		),
	);

	$result = wp_remote_post( $api_url, $args );

	$error_codes = array( 400, 403, 429, 500 );
	if ( is_wp_error( $result ) || in_array( $result['response']['code'], $error_codes, true ) ) {
		return new WP_Error( 'request_error', 'An error occurred when sending the request' );
	} else {
		update_option( 'clarity_project_id', $result['body'] );
		return true;
	}
}

/**
 * Gets the WordPress site ID from the options table or creates a new one if it doesn't exist.
 *
 * @since 0.10.0
 *
 * @return string The WordPress site ID.
 */
function clrt_get_clarity_wordpress_site_id() {
	$id = get_option( 'clarity_wordpress_site_id' );

	if ( ! $id ) {
		$id = wp_generate_uuid4();
		update_option( 'clarity_wordpress_site_id', $id );
	}

	return $id;
}

/**
 * Extracts the WordPress site users and maps them to the format required by the Clarity api.
 *
 * @since 0.10.0
 *
 * @return array[] An array of mapped users data.
 */
function clrt_get_mapped_users() {
	define( 'MAX_NUMBER_OF_USERS', 10 ); // Max number allowed by the Clarity API.

	$requesting_user = wp_get_current_user();

	$requesting_user_id = array( $requesting_user->ID );

	$users[] = clrt_map_user( $requesting_user, true );

	if ( is_multisite() ) {
		$super_admins = get_users(
			array(
				'exclude'    => $requesting_user_id,
				'capability' => 'manage_network',
			)
		);
		$super_admins = array_map( 'clrt_map_user', $super_admins );

		$users = array_merge( $users, $super_admins );
	} else {
		$admins = get_users(
			array(
				'exclude' => $requesting_user_id,
				'role'    => 'administrator',
			)
		);
		$admins = array_map( 'clrt_map_user', $admins );

		$editors = get_users(
			array(
				'exclude' => $requesting_user_id,
				'role'    => 'editor',
			)
		);
		$editors = array_map( 'clrt_map_user', $editors );

		$users = array_merge( $users, $admins, $editors );
	}

	$users = array_slice( $users, 0, MAX_NUMBER_OF_USERS );

	return $users;
}

/**
 * Maps a WordPress user to a Clarity user.
 *
 * @since 0.10.0
 *
 * @param WP_User $user The WordPress user object.
 * @param bool    $is_requesting_user Indicates whether the user is the one calling the integration hook.
 * @return array An associative array containing the mapped user data.
 */
function clrt_map_user( $user, $is_requesting_user = false ) {
	return array(
		'email'    => $user->user_email,
		'role'     => clrt_map_user_role( $user, $is_requesting_user ),
		'provider' => 'codeInvite',
	);
}

/**
 * Maps a WordPress user role to a Clarity role.
 *
 * @since 0.10.0
 *
 * @param WP_User $user The WordPress user object.
 * @param bool    $is_requesting_user Indicates whether the user is the one calling the integration hook.
 * @return string The mapped user role.
 */
function clrt_map_user_role( $user, $is_requesting_user ) {
	if ( $is_requesting_user ) {
		return 'requestingAdmin';
	}

	return in_array( 'editor', $user->roles, true ) ? 'member' : 'admin';
}

/**
 * Returns a list of integrations that contains only WordPress integration data.
 *
 * @since 0.10.0
 *
 * @return array[] An array containing WordPress integration data.
 */
function clrt_get_integrations() {
	return array(
		array(
			'id'              => clrt_get_clarity_wordpress_site_id(),
			'integrationType' => 'Wordpress',
		),
	);
}
