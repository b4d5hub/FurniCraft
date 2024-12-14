<?php
/**
 * Install plugin
 */
namespace WCBoost\ProductsCompare;

defined( 'ABSPATH' ) || exit;

/**
 * Installation class
 */
class Install {

	/**
	 * Init hooks
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'check_version' ], 5 );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );
	}

	/**
	 * Check the plugin version and run the installer
	 *
	 * @return void
	 */
	public static function check_version() {
		if ( version_compare( get_option( 'wcboost_products_compare_version' ), Plugin::instance()->version, '<' ) ) {
			self::install();
		}
	}

	/**
	 * Install plugin
	 *
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		self::maybe_create_pages();
		self::update_version();

		do_action( 'wcboost_products_compare_installed' );
	}

	/**
	 * Create pages on installation.
	 */
	public static function maybe_create_pages() {
		if ( empty( get_option( 'wcboost_products_compare_version' ) ) ) {
			self::create_pages();
		}
	}

	/**
	 * Create pages that the plugin relies on
	 *
	 * @return void
	 */
	private static function create_pages() {
		if ( ! function_exists( 'wc_create_page' ) && defined( 'WC_PLUGIN_FILE' ) ) {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/wc-admin-functions.php';
		}

		wc_create_page(
			esc_sql( _x( 'compare', 'Page slug', 'wcboost-products-compare' ) ),
			'wcboost_products_compare_page_id',
			_x( 'Compare', 'Page title', 'wcboost-products-compare' ),
			'<!-- wp:shortcode -->[wcboost_compare]<!-- /wp:shortcode -->'
		);
	}

	/**
	 * Update plugin version to current
	 */
	public static function update_version() {
		update_option( 'wcboost_products_compare_version', Plugin::instance()->version );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 *
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( Plugin::instance()->plugin_basename() !== $file ) {
			return $links;
		}

		$row_meta = [
			'docs'    => '<a href="https://docs.wcboost.com/plugin/woocommerce-products-compare/?utm_source=docs-link&utm_campaign=wp-dash&utm_medium=plugin-meta" aria-label="' . esc_attr__( 'View documentation', 'wcboost-products-compare' ) . '">' . esc_html__( 'Docs', 'wcboost-products-compare' ) . '</a>',
			'support' => '<a href="https://wordpress.org/support/plugin/wcboost-products-compare/" aria-label="' . esc_attr__( 'Visit community forums', 'wcboost-products-compare' ) . '">' . esc_html__( 'Community support', 'wcboost-products-compare' ) . '</a>',
		];

		return array_merge( $links, $row_meta );
	}
}
