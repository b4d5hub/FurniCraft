<?php
/**
 * Plugin Name: WCBoost - Wishlist
 * Description: Our WooCommerce Wishlist plugin enables customers to create personalized collections of products that they like but aren't ready to purchase immediately. Enhance the shopping experience by saving products for further consideration, making decisions easier than ever.
 * Plugin URI: https://wcboost.com/plugin/woocommerce-wishlist/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Author: WCBoost
 * Version: 1.1.5
 * Author URI: https://wcboost.com/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 *
 * Text Domain: wcboost-wishlist
 * Domain Path: /languages/
 *
 * Requires PHP: 7.0
 * Requires at least: 4.5
 * Tested up to: 6.7
 * WC requires at least: 3.0.0
 * WC tested up to: 9.3
 * License: GPLv3 or later
 *
 * @package WCBoost
 * @category Wishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCBOOST_WISHLIST_FREE', plugin_basename( __FILE__ ) );

if ( ! defined( 'WCBOOST_WISHLIST_FILE' ) ) {
	define( 'WCBOOST_WISHLIST_FILE', __FILE__ );
}

if ( ! class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';
}

// Declare compatibility with WooCommerce features.
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

// Auto-deactivate the free version.
if ( ! function_exists( 'wcboost_wishlist_installation_check' ) ) {
	/**
	 * Check condtions for plugin installation and perform additional actions
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	function wcboost_wishlist_installation_check() {
		if ( defined( 'WCBOOST_WISHLIST_PRO' ) && defined( 'WCBOOST_WISHLIST_FREE' ) ) {
			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			deactivate_plugins( WCBOOST_WISHLIST_FREE );

			set_transient( 'wcboost_wishlist_auto_deactivate_free_version', time(), DAY_IN_SECONDS );
		}
	}

	add_action( 'plugins_loaded', 'wcboost_wishlist_installation_check' );
}

if ( ! function_exists( 'wcboost_wishlist' ) ) {

	/**
	 * Load and init plugin's instance
	 */
	function wcboost_wishlist() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		return \WCBoost\Wishlist\Plugin::instance();
	}
}

add_action( 'woocommerce_loaded', 'wcboost_wishlist' );

if ( ! function_exists( 'wcboost_wishlist_activate' ) ) {

	/**
	 * Install plugin on activation
	 */
	function wcboost_wishlist_activate() {
		// Install the plugin.
		if ( class_exists( 'WooCommerce' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/install.php';

			\WCBoost\Wishlist\Install::install();
		}
	}
}

register_activation_hook( __FILE__,  'wcboost_wishlist_activate' );
