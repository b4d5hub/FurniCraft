<?php
namespace WCBoost\ProductsCompare;

/**
 * Plugin main class
 */
final class Plugin {

	/**
	 * Plugin properties
	 *
	 * @var array
	 */
	private $props = [];

	/**
	 * The product list to compare
	 *
	 * @var Compare_List
	 */
	public $list;

	/**
	 * The single instance of the class.
	 *
	 * @var \WCBoost\ProductsCompare\Plugin
	 */
	protected static $_instance = null;

	/**
	 * Main instance. Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @static
	 * @return \WCBoost\ProductsCompare\Plugin
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
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'wcboost-products-compare' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'wcboost-products-compare' ), '1.0.0' );
	}

	/**
	 * Magic method to load in-accessible properties on demand
	 *
	 * @since 1.0.5
	 *
	 * @param  string $prop
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		switch ( $prop ) {
			case 'version':
				if ( empty( $this->props['version'] ) ) {
					$plugin = get_plugin_data( WCBOOST_PRODUCTS_COMPARE_FILE );
					$this->props['version'] = $plugin['Version'];
				}
				return $this->props['version'];
				break;
		}
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();
		$this->init();
	}

	/**
	 * Plugin URL getter.
	 *
	 * @return string
	 */
	public function plugin_url( $path = '/' ) {
		return untrailingslashit( plugins_url( $path, WCBOOST_PRODUCTS_COMPARE_FILE ) );

	}

	/**
	 * Plugin path getter.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( WCBOOST_PRODUCTS_COMPARE_FILE ) );
	}

	/**
	 * Plugin base name
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( WCBOOST_PRODUCTS_COMPARE_FILE );
	}

	/**
	 * Load files
	 *
	 * @return void
	 */
	protected function includes() {
		include_once __DIR__ . '/admin/settings.php';
		include_once __DIR__ . '/admin/notices.php';
		include_once __DIR__ . '/helper.php';
		include_once __DIR__ . '/form-handler.php';
		include_once __DIR__ . '/ajax-handler.php';
		include_once __DIR__ . '/compare-list.php';
		include_once __DIR__ . '/frontend.php';
		include_once __DIR__ . '/shortcodes.php';
		include_once __DIR__ . '/compatibility.php';
		include_once __DIR__ . '/customizer.php';
		include_once __DIR__ . '/analytics/data.php';
		include_once __DIR__ . '/analytics/tracker.php';
		include_once __DIR__ . '/widgets/products-compare.php';
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	protected function init() {
		$this->initialize_list();
		$this->init_hooks();

		Install::init();
		Form_Handler::init();
		Ajax_Handler::init();
		Shortcodes::init();

		Frontend::instance();

		Analytics\Tracker::instance();
	}

	/**
	 * Core hooks to run the plugin
	 */
	protected function init_hooks() {
		add_action( 'init', [ $this, 'load_translation' ] );
		// add_action( 'init', [ $this, 'initialize_list' ] );

		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

		add_filter( 'woocommerce_get_compare_page_id', [ $this, 'compare_page_id' ] );

		add_action( 'wp_login', [ $this, 'user_logged_in' ], 10, 2 );
	}

	/**
	 * Load textdomain.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'wcboost-products-compare', false, dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/' );
	}

	/**
	 * Initialize the list of compare products
	 *
	 * @param bool $force Force initialization
	 *
	 * @return void
	 */
	public function initialize_list( $force = false ) {
		if ( ! $this->list || $force ) {
			$this->list = new Compare_List();

			$this->list->init();
		}
	}

	/**
	 * Empty product list.
	 * Initialize a new list of compare products.
	 *
	 * @param  bool $reset_db
	 * @return void
	 */
	public function empty_list( $reset_db = false ) {
		$this->list->empty( $reset_db );

		if ( $reset_db ) {
			$this->initialize_list( true );
		}
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	public function register_widgets() {
		register_widget( '\WCBoost\ProductsCompare\Widget\Products_Compare_Widget' );
	}

	/**
	 * Get the compare page id
	 *
	 * @return int
	 */
	public function compare_page_id() {
		$page_id = get_option( 'wcboost_products_compare_page_id' );
		$page_id = apply_filters( 'wpml_object_id', $page_id, 'page', false, null );

		return $page_id;
	}

	/**
	 * Update logic triggered on login.
	 *
	 * @since 1.0.6
	 * @param string $user_login User login.
	 * @param object $user       User.
	 */
	public function user_logged_in( $user_login, $user ) {
		update_user_meta( $user->ID, '_wcboost_products_compare_load_after_login', 1 );
	}
}
