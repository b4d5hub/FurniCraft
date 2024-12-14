<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

class Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		add_shortcode( 'wcboost_wishlist', [ __CLASS__, 'wishlist' ] );
		add_shortcode( 'wcboost_wishlist_button', [ __CLASS__, 'button' ] );
	}

	/**
	 * Wishlist shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function wishlist( $atts ) {
		$atts = shortcode_atts(
			[
				'token' => get_query_var( 'wishlist_token' ),
			],
			$atts,
			'wcboost_wishlist'
		);

		$wishlist = Helper::get_wishlist( $atts['token'] );
		$template = self::get_wishlist_template( $wishlist );
		$args     = [
			'wishlist'   => $wishlist,
			'return_url' => apply_filters( 'wcboost_wishlist_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
		];

		$args = apply_filters( 'wcboost_wishlist_template_args', $args, $wishlist );
		$html = wc_get_template_html( $template, $args, '', Plugin::instance()->plugin_path() . '/templates/' );

		return '<div class="woocommerce wocommerce-wishlist wcboost-wishlist">' . $html . '</div>';
	}

	/**
	 * Add to wishlist button shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function button( $atts ) {
		$atts = shortcode_atts(
			[
				'product_id' => '',
				'quantity'   => 1,
				'wishlist'   => '',
				'class'      => '',
			],
			$atts,
			'wcboost_wishlist_button'
		);

		$product_id = $atts['product_id'] ? $atts['product_id'] : ( ! empty( $GLOBALS['product'] ) ? $GLOBALS['product']->get_id() : 0 );

		if ( ! $product_id ) {
			return '';
		}

		/** @var \WC_Product || \WC_Product_Variable $_product */
		$_product = wc_get_product( $product_id );

		if ( ! $_product ) {
			return '';
		}

		$wishlist = Helper::get_wishlist( $atts['wishlist'] );
		$item     = new Wishlist_Item( $_product );

		if ( $wishlist->has_item( $item ) && 'hide' == get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) ) {
			return '';
		}

		$args = Frontend::instance()->get_button_template_args( $wishlist, $item );

		$args['quantity'] = $atts['quantity'];

		if ( ! empty( $atts['class'] ) ) {
			$args['class'] .= ' ' . $atts['class'];
		}

		$html = wc_get_template_html( 'loop/add-to-wishlist.php', $args, '', Plugin::instance()->plugin_path() . '/templates/' );

		return apply_filters( 'wcboost_wishlist_shortcode_button_html', $html, $wishlist, $item, $atts );
	}

	/**
	 * Get the wishlist template.
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @return string
	 */
	public static function get_wishlist_template( $wishlist ) {
		if ( $wishlist->can_edit() ) {
			if ( get_query_var( 'edit-wishlist' ) ) {
				$template = 'wishlist/form-edit-wishlist.php';
			} else {
				$template = $wishlist->count_items() ? 'wishlist/wishlist.php' : 'wishlist/wishlist-empty.php';
			}
		} elseif ( $wishlist->is_shareable() ) {
			$template = $wishlist->count_items() ? 'wishlist/wishlist.php' : 'wishlist/wishlist-empty.php';
		} else {
			$template = 'wishlist/wishlist-none.php';
		}

		return $template;
	}
}
