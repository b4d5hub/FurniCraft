<?php
/**
 * Plugin Name: WCBoost - Variation Swatches
 * Description: Transform boring dropdown variants into attractive and intuitive swatches, improving user experience and simplifying product selection.
 * Plugin URI: https://wcboost.com/plugin/woocommerce-variation-swatches/
 * Author: WCBoost
 * Version: 1.1.1
 * Author URI: https://wcboost.com/
 *
 * Text Domain: wcboost-variation-swatches
 * Domain Path: /languages/
 *
 * Requires PHP: 7.0
 * Requires at least: 4.5
 * Tested up to: 6.7
 * WC requires at least: 3.0.0
 * WC tested up to: 9.3.3
 * License: GPLv3 or later
 *
 * @package WCBoost\VariationSwatches
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCBOOST_VARIATION_SWATCHES_FREE', plugin_basename( __FILE__ ) );

if ( ! defined( 'WCBOOST_VARIATION_SWATCHES_FILE' ) ) {
	define( 'WCBOOST_VARIATION_SWATCHES_FILE', __FILE__ );
}

if ( ! class_exists( '\WCBoost\VariationSwatches\Plugin' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';
}

// Declare compatibility with WooCommerce features.
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if ( ! function_exists( 'wcboost_variation_swatches' ) ) {
	/**
	 * Load and init plugin's instance
	 */
	function wcboost_variation_swatches() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		return \WCBoost\VariationSwatches\Plugin::instance();
	}

	add_action( 'woocommerce_loaded', 'wcboost_variation_swatches' );
}

if ( ! function_exists( 'wcboost_variation_swatches_installation_check' ) ) {
	/**
	 * Check condtions for plugin installation and perform additional actions
	 *
	 * @since 1.0.15
	 *
	 * @return void
	 */
	function wcboost_variation_swatches_installation_check() {
		if ( ! class_exists( '\WCBoost\VariationSwatches\Compatibility' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/compatibility.php';
		}

		// Check if the Pro version is installed, then deactivate the free version.
		if ( defined( 'WCBOOST_VARIATION_SWATCHES_PRO' ) && defined( 'WCBOOST_VARIATION_SWATCHES_FREE' ) ) {
			\WCBoost\VariationSwatches\Compatibility::deactivate_free_version();

			add_action( 'admin_notices', '\WCBoost\VariationSwatches\Compatibility::free_version_deactive_notice' );
		}

		// Check if WooCommerce is installed.
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', '\WCBoost\VariationSwatches\Compatibility::woocommerce_missing_notice' );
		}
	}

	add_action( 'plugins_loaded', 'wcboost_variation_swatches_installation_check' );
}

/**
 * Backup all custom attributes by resettig the type to "select".
 *
 * @todo remove in 2.0.0
 */
function wcboost_variation_swatches_deactivate( $network_deactivating ) {
	_deprecated_function( __FUNCTION__, '1.0.15' );

	// Early return if WooCommerce is not activated.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $wpdb;

	$blog_ids         = [1];
	$original_blog_id = 1;
	$network          = false;

	if ( is_multisite() && $network_deactivating ) {
		$blog_ids         = get_sites( [ 'fields' => 'ids', 'number' => -1 ] );
		$original_blog_id = get_current_blog_id();
		$network          = true;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/admin/backup.php';

	foreach ( $blog_ids as $blog_id ) {
		if ( $network ) {
			switch_to_blog( $blog_id );
		}

		\WCBoost\VariationSwatches\Admin\Backup::backup();

		delete_option( 'wcboost_variation_swatches_ignore_restore' );
	}

	if ( $network ) {
		switch_to_blog( $original_blog_id );
	}
}
