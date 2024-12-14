<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

use WCBoost\Packages\Manager as Packages_Manager;

/**
 * Plugin main class
 */
final class Plugin {

	/**
	 * Plugin properties
	 *
	 * @since 1.0.14
	 *
	 * @var array
	 */
	private $props = [];

	/**
	 * Query instance.
	 *
	 * @var \WCBoost\Wishlist\Query
	 */
	public $query;

	/**
	 * Packages manager
	 *
	 * @var \WCBoost\Packages\Manager
	 */
	protected $packages_manager;

	/**
	 * The single instance of the class.
	 *
	 * @var \WCBoost\Wishlist\Plugin
	 */
	protected static $_instance = null;

	/**
	 * Main instance. Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @static
	 * @return \WCBoost\Wishlist\Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'wcboost-wishlist' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'wcboost-wishlist' ), '1.0.0' );
	}

	/**
	 * Magic method to load in-accessible properties on demand
	 *
	 * @since 1.0.13
	 *
	 * @param  string $prop
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		switch ( $prop ) {
			case 'version':
				if ( empty( $this->props['version'] ) ) {
					$plugin = get_plugin_data( WCBOOST_WISHLIST_FILE );
					$this->props['version'] = $plugin['Version'];
				}
				return  $this->props['version'];
				break;

			case 'packages':
				return $this->packages_manager;
				break;
		}
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_packages();
		$this->includes();
		$this->init();
		$this->init_hooks();
	}

	/**
	 * Plugin URL getter.
	 *
	 * @return string
	 */
	public function plugin_url( $path = '/' ) {
		return untrailingslashit( plugins_url( $path, WCBOOST_WISHLIST_FILE ) );

	}

	/**
	 * Plugin path getter.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( WCBOOST_WISHLIST_FILE ) );
	}

	/**
	 * Plugin base name
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return defined( 'WCBOOST_WISHLIST_PRO' ) ? WCBOOST_WISHLIST_PRO : WCBOOST_WISHLIST_FREE;
	}

	/**
	 * Load files
	 *
	 * @return void
	 */
	protected function includes() {
		include_once __DIR__ . '/helper.php';
		include_once __DIR__ . '/install.php';
		include_once __DIR__ . '/session.php';
		include_once __DIR__ . '/query.php';
		include_once __DIR__ . '/action-scheduler.php';
		include_once __DIR__ . '/form-handler.php';
		include_once __DIR__ . '/ajax-handler.php';
		include_once __DIR__ . '/frontend.php';
		include_once __DIR__ . '/shortcodes.php';
		include_once __DIR__ . '/compatibility.php';
		include_once __DIR__ . '/wishlist.php';
		include_once __DIR__ . '/wishlist-item.php';
		include_once __DIR__ . '/data-stores/wishlist.php';
		include_once __DIR__ . '/data-stores/wishlist-item.php';
		include_once __DIR__ . '/customizer/customizer.php';
		include_once __DIR__ . '/widgets/wishlist.php';
		include_once __DIR__ . '/integrations/manager.php';

		if ( is_admin() ) {
			include_once __DIR__ . '/admin/templates-notice.php';
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	protected function init() {
		$this->query = new Query();

		Install::init();
		Action_Scheduler::init();
		Shortcodes::init();
		Form_Handler::init();
		Ajax_Handler::init();

		Customize\Customizer::instance();
		Frontend::instance();
		Session::instance();
		Integrations\Manager::instance();

		if ( is_admin() ) {
			new Admin\Templates_Notice();
		}
	}

	/**
	 * Core hooks to run the plugin
	 */
	protected function init_hooks() {
		add_action( 'init', [ $this, 'load_translation' ] );

		add_filter( 'woocommerce_data_stores', [ $this, 'register_data_stores' ] );
		add_filter( 'woocommerce_get_wishlist_page_id', [ $this, 'wishlist_page_id' ] );

		add_filter( 'woocommerce_get_settings_pages', [ $this, 'setting_page' ] );

		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Load textdomain.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'wcboost-wishlist', false, dirname( plugin_basename( WCBOOST_WISHLIST_FILE ) ) . '/languages/' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 *
	 * @deprecated 1.1.0
	 */
	public function define_tables() {
		_deprecated_function( __METHOD__, '1.1.0', 'WCBoost\Wishlist\Install::define_tables' );

		Install::define_tables();
	}

	/**
	 * Register custom plugin Data Stores classes
	 *
	 * @param array $data_stores
	 * @return array
	 */
	public function register_data_stores( $data_stores ) {
		$data_stores['wcboost_wishlist']      = '\WCBoost\Wishlist\DataStore\Wishlist';
		$data_stores['wcboost_wishlist_item'] = '\WCBoost\Wishlist\DataStore\Wishlist_Item';

		return $data_stores;
	}

	/**
	 * Get the wishlist page id
	 *
	 * @return int
	 */
	public function wishlist_page_id() {
		$page_id = get_option( 'wcboost_wishlist_page_id' );

		return absint( $page_id );
	}

	/**
	 * Add new setting page to WooCommerce > Settings
	 *
	 * @param array $pages
	 * @return array
	 */
	public function setting_page( $pages ) {
		include_once 'admin/settings.php';

		$pages[] = new Settings();

		return $pages;
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	public function register_widgets() {
		register_widget( '\WCBoost\Wishlist\Widget\Wishlist' );
	}

	/**
	 * Load packages
	 *
	 * @since 1.0.13
	 *
	 * @return void
	 */
	protected function load_packages() {
		if ( ! class_exists( 'WCBoost\Packages\Manager' ) ) {
			include_once $this->plugin_path() . '/packages/manager.php';
		}

		$this->packages_manager = new Packages_Manager( $this->plugin_path() . '/packages' );

		if ( is_admin() ) {
			$this->packages_manager->load_package( 'templates-status' );

			$templates_status = Packages_Manager::package( 'templates-status' );
			$templates_status->add_templates_path( 'WCBoost - Wishlist', $this->plugin_path() . '/templates/' );
		}
	}
}
