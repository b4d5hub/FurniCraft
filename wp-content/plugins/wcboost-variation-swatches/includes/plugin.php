<?php
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 */
final class Plugin {

	/**
	 * Plugin properties
	 *
	 * @since 1.0.17
	 *
	 * @var array
	 */
	private $props = [];

	/**
	 * Options mapping object.
	 *
	 * @var \WCBoost\VariationSwatches\Mapping
	 */
	public $mapping = null;

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var Plugin
	 */
	protected static $_instance = null;

	/**
	 * Magic method to load in-accessible properties on demand
	 *
	 * @since 1.0.17
	 *
	 * @param  string $prop
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		switch ( $prop ) {
			case 'version':
				if ( ! isset( $this->props['version'] ) ) {
					$plugin = get_plugin_data( WCBOOST_VARIATION_SWATCHES_FILE );
					$this->props['version'] = $plugin['Version'];
				}
				return $this->props['version'];
				break;
		}
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->load_files();
		$this->init_hooks();
		$this->init();
		$this->set_mapping();

		do_action( 'wcboost_variation_swatches_init' );
	}

	/**
	 * Load files
	 */
	public function load_files() {
		require_once dirname( __FILE__ ) . '/mapping.php';
		require_once dirname( __FILE__ ) . '/helper.php';
		require_once dirname( __FILE__ ) . '/swatches.php';
		require_once dirname( __FILE__ ) . '/compatibility.php';

		require_once dirname( __FILE__ ) . '/admin/backup.php';
		require_once dirname( __FILE__ ) . '/admin/settings.php';
		require_once dirname( __FILE__ ) . '/admin/term-meta.php';
		require_once dirname( __FILE__ ) . '/admin/product-data.php';

		require_once dirname( __FILE__ ) . '/customizer/customizer.php';
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'load_textdomain' ] );

		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . plugin_basename( WCBOOST_VARIATION_SWATCHES_FILE ), [ $this, 'add_action_links' ] );
			add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wcboost-variation-swatches', false, dirname( plugin_basename( WCBOOST_VARIATION_SWATCHES_FILE ) ) . '/languages/' );
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.15
	 *
	 * @return void
	 */
	protected function init() {
		Swatches::instance();
		Compatibility::instance();
	}

	/**
	 * Set the mapping object
	 */
	public function set_mapping() {
		$this->mapping = new Mapping();
	}

	/**
	 * Get the mapping object
	 *
	 * @return WCBoost\VariationSwatches\Mapping
	 */
	public function get_mapping() {
		return $this->mapping;
	}

	/**
	 * Add action links
	 *
	 * @since 1.0.15
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		if ( ! defined( 'WCBOOST_VARIATION_SWATCHES_PRO' ) ) {
			$actions = [
				'wcboost-go-pro' => '<a href="https://wcboost.com/plugin/woocommerce-variation-swatches/?utm_source=wp-plugin&utm_medium=wp-dash&utm_campaign=plugin-action-links" target="_blank" style="font-weight:bold;color:green;">' . esc_html__( 'Go Pro', 'wcboost-variation-swatches' ) . '</a>',
			];
			$links = array_merge( $links, $actions );
		}

		return $links;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @since 1.0.17
	 *
	 * @param array $links Plugin Row Meta.
	 * @param string $file  Plugin Base file.
	 *
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( WCBOOST_VARIATION_SWATCHES_FILE ) !== $file ) {
			return $links;
		}

		$row_meta = [
			'docs'    => '<a href="https://wcboost.com/docs-category/wcboost-variation-swatches/?utm_source=docs-link&utm_campaign=wp-dash&utm_medium=plugin-meta" aria-label="' . esc_attr__( 'View plugin documentation', 'wcboost-variation-swatches' ) . '" target="_blank">' . esc_html__( 'Docs', 'wcboost-variation-swatches' ) . '</a>',
			'support' => '<a href="https://wordpress.org/support/plugin/wcboost-variation-swatches/" aria-label="' . esc_attr__( 'Visit community support forums', 'wcboost-variation-swatches' ) . '" target="_blank">' . esc_html__( 'Community support', 'wcboost-variation-swatches' ) . '</a>',
		];

		return array_merge( $links, $row_meta );
	}
}
