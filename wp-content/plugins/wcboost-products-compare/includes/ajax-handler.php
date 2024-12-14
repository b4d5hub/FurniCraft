<?php
namespace WCBoost\ProductsCompare;

use MailPoet\AdminPages\Pages\Help;

class Ajax_Handler {

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$frontend_events = [
			'add_to_compare',
			'remove_compare_item',
			'get_compare_fragments',
		];

		foreach ( $frontend_events as $event ) {
			add_action( 'wc_ajax_' . $event, [ __CLASS__, $event ] );
		}
	}

	/**
	 * AJAX add to compare
	 *
	 * @since 1.0.0
	 */
	public static function add_to_compare() {
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id     = absint( $_POST['product_id'] );
		$product        = wc_get_product( $product_id );
		$product_status = get_post_status( $product_id );

		if ( ! $product || 'publish' != $product_status ) {
			wp_send_json_error();
			exit;
		}

		$added = Plugin::instance()->list->add_item( $product_id );

		if ( $added ) {
			if ( 'redirect' == get_option( 'wcboost_products_compare_added_behavior' ) ) {
				/* translators: %s: product name */
				$message = sprintf( esc_html__( '%s has been added to the compare list', 'wcboost-products-compare' ), '&ldquo;' . $product->get_title() . '&rdquo;' );

				wc_add_notice( $message );
			}

			wp_send_json_success( [
				'fragments'     => self::get_refreshed_fragments(),
				'remove_url'    => Helper::get_remove_url( $product ),
				'compare_url'   => wc_get_page_permalink( 'compare' ),
				'count'         => Plugin::instance()->list->count_items(),
				'compare_items' => self::get_compare_items(),
				'compare_hash'  => Plugin::instance()->list->get_hash(),
			] );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * AJAX remove compare item
	 */
	public static function remove_compare_item() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$item_key = isset( $_POST['item_key'] ) ? wc_clean( wp_unslash( $_POST['item_key'] ) ) : '';
		$product_id = Plugin::instance()->list->remove_item( $item_key );

		if ( false !== $product_id ) {
			wp_send_json_success( [
				'fragments'     => self::get_refreshed_fragments(),
				'compare_items' => self::get_compare_items(),
				'compare_hash'  => Plugin::instance()->list->get_hash(),
				'add_url'       => Helper::get_add_url( $product_id ),
			] );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * AJAX get fragments for products compare.
	 *
	 * @since 1.0.0
	 */
	public static function get_compare_fragments() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$fragments = self::get_refreshed_fragments();

		if ( ! empty( $_POST['product_button_ids'] ) ) {
			$product_ids = array_map( 'absint', $_POST['product_button_ids'] );

			foreach ( $product_ids as $id ) {
				if ( $id ) {
					$button = do_shortcode( '[wcboost_compare_button product_id="' . $id . '"]' );
					$fragments['.wcboost-products-compare-button[data-product_id="' . $id . '"]'] = $button;
				}
			}
		}

		wp_send_json_success( [
			'fragments'     => $fragments,
			'compare_items' => self::get_compare_items(),
			'compare_hash'  => Plugin::instance()->list->get_hash(),
		] );
	}

	/**
	 * Get a refreshed compare fragments
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_refreshed_fragments() {
		ob_start();
		Helper::widget_content();
		$widget_content = ob_get_clean();

		$data = [
			'div.wcboost-products-compare-widget-content' => $widget_content,
		];

		return apply_filters( 'wcboost_products_compare_add_to_compare_fragments', $data );
	}

	/**
	 * Get contents of the compare list
	 *
	 * @since 1.0.6
	 *
	 * @return void
	 */
	protected static function get_compare_items() {
		$items = Plugin::instance()->list->get_items();
		$data  = [];

		foreach ( $items as $key => $product_id ) {
			$data[ $product_id ] = [ 'remove_url' => Helper::get_remove_url( $product_id ) ];
		}

		return $data;
	}
}
