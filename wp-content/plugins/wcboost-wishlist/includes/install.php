<?php
/**
 * Install plugin
 */
namespace WCBoost\Wishlist;

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
		add_action( 'switch_blog', [ __CLASS__, 'define_tables' ], 0 );
		add_action( 'init', [ __CLASS__, 'check_version' ], 5 );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );
		add_action( 'admin_notices', [ __CLASS__, 'deactivate_notice' ] );
	}

	/**
	 * Check the plugin version and run the installer
	 *
	 * @return void
	 */
	public static function check_version() {
		if ( version_compare( get_option( 'wcboost_wishlist_version' ), Plugin::instance()->version, '<' ) ) {
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

		if ( 'yes' === get_transient( 'wcboost_wishlist_installing' ) ) {
			return;
		}

		set_transient( 'wcboost_wishlist_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::define_tables();
		self::create_tables();
		self::maybe_create_pages();
		self::update_version();
		flush_rewrite_rules();

		delete_transient( 'wcboost_wishlist_installing' );

		do_action( 'wcboost_wishlist_installed' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 *
	 * @since 1.1.0
	 */
	public static function define_tables() {
		global $wpdb;

		// Wishlist table.
		if ( ! isset( $wpdb->wishlists ) ) {
			$wpdb->wishlists = $wpdb->prefix . 'wcboost_wishlists';
			$wpdb->tables[] = 'wcboost_wishlists';
		}

		// Wishlist items table.
		if ( ! isset( $wpdb->wishlist_items ) ) {
			$wpdb->wishlist_items = $wpdb->prefix . 'wcboost_wishlist_items';
			$wpdb->tables[] = 'wcboost_wishlist_items';
		}
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * @return void
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	/**
	 * Get table schema.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
		CREATE TABLE {$wpdb->wishlists} (
			wishlist_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			wishlist_title text NULL,
			wishlist_slug VARCHAR(200) NULL,
			wishlist_token VARCHAR(64) NOT NULL,
			description longtext NULL,
			menu_order INT(11) NOT NULL,
			status varchar(200) NOT NULL DEFAULT 'private',
			user_id BIGINT UNSIGNED NOT NULL,
			session_id VARCHAR(200) NULL,
			date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			date_expires datetime NULL DEFAULT NULL,
			is_default tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY  (wishlist_id),
			KEY user_id (user_id),
			UNIQUE KEY wishlist_token (wishlist_token)
		) $collate;
		CREATE TABLE {$wpdb->wishlist_items} (
			item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			status varchar(200) NOT NULL DEFAULT 'publish',
			product_id BIGINT UNSIGNED NOT NULL,
			variation_id BIGINT UNSIGNED NOT NULL DEFAULT '0',
			quantity INT(11) NOT NULL,
			wishlist_id BIGINT UNSIGNED NOT NULL,
			date_added datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			date_expires datetime NULL DEFAULT NULL,
			PRIMARY KEY  (item_id),
			KEY product_id (product_id),
			KEY wishlist_id (wishlist_id)
		) $collate;
		";

		return $tables;
	}

	/**
	 * Create pages on installation.
	 */
	public static function maybe_create_pages() {
		if ( empty( get_option( 'wcboost_wishlist_version' ) ) ) {
			self::create_pages();
		}
	}

	/**
	 * Create pages that the plugin relies on
	 *
	 * @return void
	 */
	public static function create_pages() {
		if ( ! function_exists( 'wc_create_page' ) && defined( 'WC_PLUGIN_FILE' ) ) {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/wc-admin-functions.php';
		}

		wc_create_page(
			esc_sql( _x( 'wishlist', 'Page slug', 'wcboost-wishlist' ) ),
			'wcboost_wishlist_page_id',
			_x( 'Wishlist', 'Page title', 'wcboost-wishlist' ),
			'<!-- wp:shortcode -->[wcboost_wishlist]<!-- /wp:shortcode -->'
		);
	}

	/**
	 * Update plugin version to current
	 */
	public static function update_version() {
		update_option( 'wcboost_wishlist_version', Plugin::instance()->version );
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
		if ( plugin_basename( WCBOOST_WISHLIST_FILE ) !== $file ) {
			return $links;
		}

		$row_meta = [
			'docs'    => '<a href="https://wcboost.com/docs-category/wcboost-wishlists/?utm_source=docs-link&utm_campaign=wp-dash&utm_medium=plugin-meta" aria-label="' . esc_attr__( 'View wishlist documentation', 'wcboost-wishlist' ) . '">' . esc_html__( 'Docs', 'wcboost-wishlist' ) . '</a>',
			'support' => '<a href="https://wordpress.org/support/plugin/wcboost-wishlist/" aria-label="' . esc_attr__( 'Visit community forums', 'wcboost-wishlist' ) . '">' . esc_html__( 'Community support', 'wcboost-wishlist' ) . '</a>',
		];

		return array_merge( $links, $row_meta );
	}

	/**
	 * The admin notice that inform the free version of plugin has been automatically deactivated.
	 *
	 * @since 1.0.11
	 *
	 * @return void
	 */
	public static function deactivate_notice() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$auto_deactivated = get_transient( 'wcboost_wishlist_auto_deactivate_free_version' );

		if ( ! $auto_deactivated ) {
			return;
		}
		?>
		<div class="notice is-dismissible">
			<p><?php esc_html_e( 'WCBoost - Wishlist (Free) has been automatically deactivated as you now have the Pro version installed.', 'wcboost-wishlist' ); ?></p>
		</div>
		<?php
		delete_transient( 'wcboost_wishlist_auto_deactivate_free_version' );
	}
}
