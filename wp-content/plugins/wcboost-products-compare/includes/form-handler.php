<?php
namespace WCBoost\ProductsCompare;

use Exception;

/**
 * Form handler class
 */
class Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_loaded', [ __CLASS__, 'add_item_action' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'remove_item_action' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'clear_list_action' ], 20 );
	}

	/**
	 * Add to compare list action.
	 *
	 * @param string|bool $url The URL to redirect to.
	 *
	 * @return void
	 */
	public static function add_item_action( $url = false ) {
		if ( ! isset( $_REQUEST['add_to_compare'] ) || ! is_numeric( wp_unslash( $_REQUEST['add_to_compare'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		wc_nocache_headers();

		$product_id     = absint( wp_unslash( $_REQUEST['add_to_compare'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$adding_product = wc_get_product( $product_id );

		if ( ! $adding_product ) {
			return;
		}

		$added = Plugin::instance()->list->add_item( $product_id );

		if ( $added ) {
			/* translators: %s: product name */
			$message = sprintf( esc_html__( '%s has been added to the compare list', 'wcboost-products-compare' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );

			if ( 'redirect' == get_option( 'wcboost_products_compare_added_behavior' ) ) {
				$return_to = apply_filters( 'wcboost_products_compare_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
				$button    = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a>', esc_url( $return_to ), esc_html__( 'Continue shopping', 'wcboost-products-compare' ) );
			} else {
				$return_to = wc_get_page_permalink( 'compare' );
				$button    = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a>', esc_url( $return_to ), esc_html__( 'Compare', 'wcboost-products-compare' ) );
			}

			wc_add_notice( $button . $message );

			$url = apply_filters( 'wcboost_products_compares_add_to_list_redirect', $url, $adding_product );

			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			} elseif ( 'redirect' == get_option( 'wcboost_products_compare_added_behavior' ) ) {
				wp_safe_redirect( wc_get_page_permalink( 'compare' ) );
				exit;
			}
		} else {
			/* translators: %s: product name */
			$message = sprintf( esc_html__( '%s was added to the compare list', 'wcboost-products-compare' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );
			wc_add_notice( $message );
		}
	}

	/**
	 * Remove an item from the current compare list.
	 *
	 * @return void
	 */
	public static function remove_item_action() {
		if ( empty( $_GET['remove_compare_item'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wcboost-products-compare-remove-item' ) ) {
			return;
		}

		wc_nocache_headers();

		$item_key    = sanitize_text_field( wp_unslash( $_GET['remove_compare_item'] ) );
		$was_removed = Plugin::instance()->list->remove_item( $item_key );

		if ( $was_removed ) {
			$removing_product = wc_get_product( $was_removed );

			/* translators: %s: product name */
			$removed_notice  = sprintf( __( '%s is removed from the compare list.', 'wcboost-products-compare' ), '&ldquo;' . $removing_product->get_title() . '&rdquo;' );

			wc_add_notice( $removed_notice, 'success' );

			$referer = wp_get_referer() ? remove_query_arg( [ 'remove_compare_item', 'add_to_compare', '_wpnonce' ], add_query_arg( 'removed-compare-item', '1', wp_get_referer() ) ) : add_query_arg( 'removed-compare-item', '1', wc_get_page_permalink( 'compare' ) );
			wp_safe_redirect( $referer );
			exit;
		} else {
			$message = esc_html__( 'Failed to remove the product from the compare list', 'wcboost-products-compare' );
			wc_add_notice( $message, 'error' );
		}

	}

	/**
	 * Clear the whole list
	 *
	 * @return void
	 */
	public static function clear_list_action() {
		if ( empty( $_GET['clear_compare_list'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wcboost-products-compare-clear-list' ) ) {
			return;
		}

		wc_nocache_headers();

		$list_id = wp_unslash( $_GET['clear_compare_list'] );

		if ( $list_id != Plugin::instance()->list->get_id() ) {
			return;
		}

		Plugin::instance()->empty_list( true );

		wc_add_notice( __( 'The compare list is emptied', 'wcboost-products-compare' ), 'success' );

		$referer = wp_get_referer() ? remove_query_arg( [ 'remove_compare_item', 'add_to_compare', 'clear_compare_list', '_wpnonce' ], add_query_arg( 'compare_list_emptied', '1', wp_get_referer() ) ) : add_query_arg( 'compare_list_emptied', '1', wc_get_page_permalink( 'compare' ) );
		wp_safe_redirect( $referer );
		exit;
	}
}
