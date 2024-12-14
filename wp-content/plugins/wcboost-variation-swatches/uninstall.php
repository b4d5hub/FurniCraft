<?php
/**
 * Uninstall plugin
 */

// If uninstall not called from WordPress then exit
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

// Restore swatches attributes to standard select type.
$table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';

if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) == $table ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$update = "UPDATE `$table` SET `attribute_type` = 'select' WHERE `attribute_type` != 'text'";
	$wpdb->query( $update ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
}

// Remove options.
delete_option( 'wcboost_variation_swatches_ignore_restore' );
delete_site_option( 'wcboost_variation_swatches_ignore_restore' );
