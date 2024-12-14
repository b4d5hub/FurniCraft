<?php
/**
 * Uninstall plugin
 */
namespace WCBoost\Wishlist;

// If uninstall not called from WordPress exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function uninstall() {
	global $wpdb;

	// Define table names.
	$tables = [
		$wpdb->prefix . 'wcboost_wishlists',
		$wpdb->prefix . 'wcboost_wishlists_items'
	];

	// Plugin options to remove.
	$options = [
		'wcboost_wishlist_version',
		'wcboost_wishlist_db_version'
	];

	// Delete options.
	foreach ( $options as $option ) {
		delete_option( $option );
	}

	// Remove tables.
	foreach ( $tables as $table ) {
		// phpcs:ignore
		$wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS `%s`", $table ) );
	}

	// Clear any cached data.
	wp_cache_flush();
}

// Check if is multi-site.
if ( ! is_multisite() ) {
	uninstall();
} else {
	global $wpdb;

	// phpcs:ignore
	$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	$original_blog_id = get_current_blog_id();

	try {
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			uninstall();
		}
	} catch ( \Exception $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WCBoost Wishlist uninstall error: ' . $e->getMessage() );
		}
	} finally {
		// Ensure we switch back to the original blog even if there's an error.
		switch_to_blog( $original_blog_id );
	}
}
