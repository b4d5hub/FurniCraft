<?php
/**
 * Compatible with other plugins/themes
 */
namespace WCBoost\ProductsCompare;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 */
class Compatibility {

	/**
	 * The single instance of the class
	 *
	 * @var WCBoost\ProductsCompare\Compatibility
	 */
	protected static $_instance = null;

	/**
	 * Main instance
	 *
	 * @return WCBoost\ProductsCompare\Compatibility
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'check_compatible_hooks' ] );
	}

	/**
	 * Check compatibility with other plugins/themes and add hooks
	 */
	public function check_compatible_hooks() {
		// WCBoost Wishlist plugin. No check because we use filter.
		add_filter( 'wcboost_wishlist_page_id_option_exclude', [ $this, 'exclude_compare_page_from_wishlist_option' ] );
	}

	/**
	 * Add the compage page id to the exclude option of wishlist page ids.
	 *
	 * @param  array $page_ids Excluded page ids.
	 * @return array
	 */
	public function exclude_compare_page_from_wishlist_option( $page_ids ) {
		$compare_page_id = wc_get_page_id( 'compare' );

		if ( $compare_page_id ) {
			$page_ids[] = $compare_page_id;
		}

		return $page_ids;
	}
}

Compatibility::instance();
