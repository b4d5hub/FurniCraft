<?php
/**
 * Plugin Name: Sober Addons
 * Plugin URI: http://uix.store/
 * Description: A collection of extra elements, custom post types, widgets for the Sober theme.
 * Author: UIX Themes
 * Author URI: http://uix.store
 * Version: 1.6.21
 * Text Domain: sober
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sober_Addons
 */
final class Sober_Addons {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init();
	}

	/**
	 * Defines constants
	 */
	public function define_constants() {
		define( 'SOBER_ADDONS_VER', '1.6.21' );
		define( 'SOBER_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'SOBER_ADDONS_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Load files
	 */
	public function includes() {
		include_once( SOBER_ADDONS_DIR . 'includes/functions.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/woocommerce.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/update.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/import.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/user.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/portfolio.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/class-sober-vc.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-shortcodes.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-banner.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/shortcodes/class-sober-banner-grid.php' );
	}

	/**
	 * Initialize
	 */
	public function init() {
		// add_action( 'admin_notices', array( $this, 'check_dependencies' ) );

		load_plugin_textdomain( 'sober', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'vc_before_init', 'vc_set_as_theme' );
		add_action( 'vc_after_init', array( 'Sober_Addons_VC', 'init' ), 50 );
		add_action( 'init', array( 'Sober_Shortcodes', 'init' ), 50 );
		// add_action( 'init', array( $this, 'update' ) );

		add_action( 'init', array( 'Sober_Addons_Portfolio', 'init' ) );

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_action( 'plugins_loaded', array( $this, 'init_elementor' ) );
	}

	/**
	 * Init Elementor support
	 */
	public function init_elementor() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, '2.0.0', '>=' ) ) {
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		include_once( SOBER_ADDONS_DIR . 'includes/elementor/elementor.php' );
	}

	/**
	 * Check plugin dependencies
	 * Check if Visual Composer plugin is installed
	 */
	public function check_dependencies() {
		if ( ! defined( 'WPB_VC_VERSION' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
			$plugin_data = get_plugin_data( __FILE__ );

			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				sprintf(
					__( '<strong>%s</strong> requires <strong><a href="http://bit.ly/wpbakery-page-builder" target="_blank">WPBakery Page Builder</a></strong> or <strong><a href="https://wordpress.org/plugins/elementor/" target="_blank">Elementor Page Builder</a></strong> plugin to be installed and activated on your site.', 'sober' ),
					$plugin_data['Name']
				)
			);
		}
	}

	/**
	 * Register widgets
	 */
	public function widgets_init() {
		$theme = wp_get_theme();
		$template = $theme->get( 'Template' );

		if ( $template ) {
			$theme = wp_get_theme( $template );
		}

		// Don't load widget files with old verison of Sober.
		if ( ! version_compare( $theme->get( 'Version' ), '2.1.1', '>=' ) ) {
			return;
		}

		// Load widdget files
		include_once( SOBER_ADDONS_DIR . 'includes/widgets/socials.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/widgets/popular-posts.php' );
		include_once( SOBER_ADDONS_DIR . 'includes/widgets/instagram.php' );

		register_widget( 'Sober_Social_Links_Widget' );
		register_widget( 'Sober_Popular_Posts_Widget' );
		register_widget( 'Sober_Instagram_Widget' );

		if ( class_exists( 'WooCommerce' ) ) {
			include_once( SOBER_ADDONS_DIR . 'includes/widgets/products-filter.php' );
			register_widget( 'Sober_Products_Filter_Widget' );
		}
	}

	/**
	 * Check for update
	 */
	public function update() {
		// set auto-update params
		$plugin_current_version = SOBER_ADDONS_VER;
		$plugin_remote_path     = 'https://update.uix.store';
		$plugin_slug            = plugin_basename( __FILE__ );
		$license_user           = '';
		$license_key            = '';

		new Sober_Addons_AutoUpdate( $plugin_current_version, $plugin_remote_path, $plugin_slug );
	}
}

new Sober_Addons();
