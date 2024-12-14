<?php
/**
 * Compatible with other plugins/themes
 */
namespace WCBoost\Wishlist;

use WCBoost\Packages\Utilities\Singleton_Trait;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 */
class Compatibility {
	use Singleton_Trait;

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
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			// Add more rewrite rules for the wishlist page in other languages.
			$this->add_translated_rewrite_rules();

			// Translate product ids. 'wcboost_wishlist_item' is the object type.
			$item_object_type = 'wcboost_wishlist_item';
			add_filter( "woocommerce_{$item_object_type}_get_product_id", [ $this, 'translate_product_id' ] );
			add_filter( "woocommerce_{$item_object_type}_get_variation_id", [ $this, 'translate_variation_id' ] );

			// Always use id of default product in adding actions.
			add_filter( 'wcboost_wishlist_add_to_wishlist_product_id', [ $this, 'translate_product_id_to_default' ] );

			// Always use the same item key for removing actions.
			add_filter( 'wcboost_wishlist_item_key', [ $this, 'translate_item_key_to_default' ], 10, 3 );

			// Translate the wishlist page ID.
			add_filter( 'option_wcboost_wishlist_page_id', [ $this, 'translate_option_wishlist_page_id' ] );
		}
	}

	/**
	 * Add rewrite rules for translated wishlist pages.
	 *
	 * @return void
	 */
	protected function add_translated_rewrite_rules() {
		// Get the default wishlist page id.
		$wishlist_page_id = get_option( 'wcboost_wishlist_page_id' );

		if ( empty( $wishlist_page_id ) ) {
			return;
		}

		$trid = apply_filters( 'wpml_element_trid', null, $wishlist_page_id, 'post_page' );

		if ( ! $trid ) {
			return;
		}

		$translations = apply_filters( 'wpml_get_element_translations', NULL, $trid, 'post_page' );
		$default_lang = apply_filters( 'wpml_default_language', NULL );

		foreach ( $translations as $translate_page_obj ) {
			// No need to add rewrite rule for default language.
			if ( $default_lang == $translate_page_obj->language_code ) {
				continue;
			}

			$page_slug = get_post_field( 'post_name', $translate_page_obj->element_id );

			if ( empty( $page_slug ) ) {
				continue;
			}

			Plugin::instance()->query->add_rewrite_rules( $page_slug );
		}
	}

	/**
	 * Translate product id if a wishlist item
	 *
	 * @param  int $product_id
	 * @return int
	 */
	public function translate_product_id( $product_id ) {
		return apply_filters( 'wpml_object_id', $product_id, 'product' );
	}

	/**
	 * Translate variation id if a wishlist item
	 *
	 * @param  int $variation_id
	 * @return int
	 */
	public function translate_variation_id( $variation_id ) {
		return apply_filters( 'wpml_object_id', $variation_id, 'product_variation' );
	}

	/**
	 * Treanslate product id to the default language.
	 *
	 * @param  int $product_id
	 * @return int
	 */
	public function translate_product_id_to_default( $product_id ) {
		$default_lang = apply_filters( 'wpml_default_language', NULL );

		return apply_filters( 'wpml_object_id', $product_id, 'product', false, $default_lang );
	}

	/**
	 * Tranlslate item key to the default language.
	 *
	 * @param  string $key
	 * @param  int $product_id
	 * @param  int $variation_id
	 * @return string
	 */
	public function translate_item_key_to_default( $key, $product_id, $variation_id ) {
		$default_lang = apply_filters( 'wpml_default_language', NULL );
		$current_lang = apply_filters( 'wpml_current_language', NULL );

		if ( $default_lang == $current_lang ) {
			return $key;
		}

		$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, $default_lang );

		if ( $variation_id ) {
			$variation_id = apply_filters( 'wpml_object_id', $variation_id, 'product_variation', false, $default_lang );
		}

		return md5( implode( '_', [ $product_id, $variation_id ] ) );
	}

	/**
	 * Translate the option of wishlist page id
	 *
	 * @param  int $page_id
	 *
	 * @return int
	 */
	public function translate_option_wishlist_page_id( $page_id ) {
		return apply_filters( 'wpml_object_id', $page_id, 'page' );
	}
}

Compatibility::instance();
