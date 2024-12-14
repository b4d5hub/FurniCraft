<?php
namespace WCBoost\ProductsCompare;

use WCBoost\ProductsCompare\Analytics\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcodes class
 */
class Shortcodes {

	/**
	 * Initialize shortcodes
	 */
	public static function init() {
		add_shortcode( 'wcboost_compare', [ __CLASS__, 'compare_page' ] );
		add_shortcode( 'wcboost_compare_button', [ __CLASS__, 'compare_button' ] );
		add_shortcode( 'wcboost_compare_similars', [ __CLASS__, 'similar_products' ] );
	}

	/**
	 * Compare page shortcode
	 *
	 * @param  array $atts
	 * @return string
	 */
	public static function compare_page( $atts ) {
		$atts = shortcode_atts(
			[
				'product_ids' => empty( $_GET['compare_products'] ) ? '' : trim( $_GET['compare_products'] ),
				'fields'      => '',
			],
			$atts,
			'wcboost_compare'
		);

		$product_ids = ! empty( $atts['product_ids'] ) ? array_map( 'intval', explode( ',', $atts['product_ids'] ) ) : [];
		$product_ids = array_filter( $product_ids );
		$list        = empty( $product_ids ) ? Plugin::instance()->list : new Compare_List( $product_ids );
		$fields      = array_map( 'trim', explode( ',', $atts['fields'] ) );
		$fields      = array_map( 'strtolower', $fields );
		$args        = [
			'compare_list'   => $list,
			'compare_fields' => array_filter( $fields ),
			'return_url'     => apply_filters( 'wcboost_products_compare_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
		];

		$args = apply_filters( 'wcboost_products_compare_template_args', $args, $list );
		$html = wc_get_template_html( 'compare/compare.php', $args, '', Plugin::instance()->plugin_path() . '/templates/' );

		return '<div class="woocommerce wcboost-products-compare">' . $html . '</div>';
	}

	/**
	 * Compare button shortcode
	 *
	 * @param  array $atts
	 * @return string
	 */
	public static function compare_button( $atts ) {
		$atts = shortcode_atts(
			[
				'product_id' => '',
				'class'      => '',
			],
			$atts,
			'wcboost_compare_button'
		);

		$atts['product_id'] = $atts['product_id'] ? $atts['product_id'] : ( ! empty( $GLOBALS['product'] ) ? $GLOBALS['product']->get_id() : 0 );

		if ( ! $atts['product_id'] ) {
			return '';
		}

		/** @var \WC_Product | \WC_Product_Variable $_product */
		$_product = wc_get_product( $atts['product_id'] );

		if ( ! $_product ) {
			return '';
		}

		$list = Plugin::instance()->list;

		if ( $list && $list->has_item( $_product ) && 'hide' == get_option( 'wcboost_products_compare_exists_item_button_behaviour' ) ) {
			return '';
		}

		$args = Frontend::instance()->get_button_template_args( $_product );

		if ( ! empty( $atts['class'] ) ) {
			$args['class'] .= ' ' . $atts['class'];
		}

		$html = wc_get_template_html( 'loop/add-to-compare.php', $args, '', Plugin::instance()->plugin_path() . '/templates/' );

		return apply_filters( 'wcboost_products_compare_shortcode_button_html', $html, $atts );
	}

	/**
	 * Shortcode of similar products which are calculated from comparison data.
	 *
	 * @param  array $atts
	 *
	 * @return string
	 */
	public static function similar_products( $atts ) {
		$atts = shortcode_atts(
			[
				'product_id' => '',
				'limit'      => 12,
				'columns'    => 4,
			],
			$atts,
			'wcboost_compare_similars'
		);

		$product_id  = intval( $atts['product_id'] );
		$similar_ids = Data::get_similar_product_ids( $product_id );

		if ( empty( $similar_ids ) ) {
			return '';
		}

		$shortcode = new \WC_Shortcode_Products( [
			'ids'     => implode( ',', $similar_ids ),
			'orderby' => 'post__in',
			'limit'   => $atts['limit'],
			'columns' => $atts['columns'],
		], 'wcboost_compare_similars' );

		return $shortcode->get_content();
	}
}
