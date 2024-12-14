<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

class Ajax_Handler {
	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$frontend_events = [
			'add_to_wishlist',
			'remove_wishlist_item',
			'get_wishlist_fragments',
		];

		foreach ( $frontend_events as $event ) {
			add_action( 'wc_ajax_' . $event, [ __CLASS__, $event ] );
		}
	}

	/**
	 * AJAX add to wishlist
	 *
	 * @since 1.0.0
	 */
	public static function add_to_wishlist() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error();
			exit;
		}

		// Stop if not allowing guests to creating wishlishs.
		if ( ! wc_string_to_bool( get_option( 'wcboost_wishlist_enable_guest_wishlist', 'yes' ) ) && ! is_user_logged_in() ) {
			$data = [];

			if ( 'redirect_to_account_page' == get_option( 'wcboost_wishlist_guest_behaviour', 'message' ) ) {
				$data['redirect_url'] = wc_get_page_permalink( 'myaccount' );
			} else {
				$message = get_option( 'wcboost_wishlist_guest_message', __( 'You need to login to add products to your wishlist', 'wcboost-wishlist' ) );

				if ( $message ) {
					ob_start();
					wc_print_notice( $message, 'notice' );
					$data['message'] = ob_get_clean();
				}
			}

			wp_send_json_error( $data );
			exit;
		}

		$product_id     = apply_filters( 'wcboost_wishlist_add_to_wishlist_product_id', absint( $_POST['product_id'] ) );
		$quantity       = empty( $_POST['quantity'] ) ? 1 : absint( $_POST['quantity'] );
		$product        = wc_get_product( $product_id );
		$product_status = get_post_status( $product_id );

		if ( ! $product || 'publish' != $product_status ) {
			wp_send_json_error();
			exit;
		}

		if ( $product->is_type( 'variation' ) && ! wc_string_to_bool( get_option( 'wcboost_wishlist_allow_adding_variations' ) ) ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		$wishlist_id = ! empty( $_REQUEST['wishlist'] ) ? absint( wp_unslash( $_REQUEST['wishlist'] ) ) : 0;
		$wishlist    = Helper::get_wishlist( $wishlist_id );
		$item        = new Wishlist_Item( $product );

		// Insert the wishlist to db if it is a temporary one, so we have the wishlist_id.
		if ( ! $wishlist->get_id() ) {
			$wishlist->save();
		}

		if ( ! $wishlist->can_edit() ) {
			wp_send_json_error();
			exit;
		}

		$item->set_quantity( $quantity );
		$was_added = $wishlist->add_item( $item );

		if ( $was_added && ! is_wp_error( $was_added ) ) {
			if ( wc_string_to_bool( get_option( 'wcboost_wishlist_redirect_after_add' ) ) ) {
				/* translators: %s: product name */
				$message = sprintf( esc_html__( '%s has been added to your wishlist', 'wcboost-wishlist' ), '&ldquo;' . $product->get_title() . '&rdquo;' );

				wc_add_notice( $message );
			}

			wp_send_json_success( [
				'fragments'      => self::get_refreshed_fragments(),
				'wishlist_hash'  => $wishlist->get_hash(),
				'wishlist_url'   => $wishlist->get_public_url(),
				'wishlist_items' => self::get_wishlist_items( $wishlist_id ),
				'remove_url'     => $item->get_remove_url(),
				'product_id'     => $product_id,
			] );
		} else {
			wp_send_json_error();
		}
		// phpcs:enable
	}

	/**
	 * AJAX remove wishlist item
	 */
	public static function remove_wishlist_item() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$item_key    = wc_clean( isset( $_POST['item_key'] ) ? wp_unslash( $_POST['item_key'] ) : '' );
		$wishlist_id = isset( $_POST['wishlist_id'] ) ? absint( $_POST['wishlist_id'] ) : 0;
		$wishlist    = Helper::get_wishlist( $wishlist_id );
		$item        = $wishlist->get_item( $item_key );
		$was_removed = $wishlist->remove_item( $item_key );
		// phpcs:enable

		if ( $was_removed && ! is_wp_error( $was_removed ) ) {
			wp_send_json_success( [
				'fragments'      => self::get_refreshed_fragments(),
				'wishlist_hash'  => $wishlist->get_hash(),
				'wishlist_url'   => $wishlist->get_public_url(),
				'wishlist_items' => self::get_wishlist_items( $wishlist_id ),
				'restore_url'    => $item->get_restore_url(),
				'add_url'        => $item->get_add_url(),
				'product_id'     => $item->get_product()->get_id(),
			] );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * AJAX get wishlist fragments
	 *
	 * @since 1.0.0
	 */
	public static function get_wishlist_fragments() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product_ids = ! empty( $_POST['product_ids'] ) ? array_map( 'absint', $_POST['product_ids'] ) : [];
		$fragments   = self::get_refreshed_fragments();

		if ( ! empty( $product_ids ) ) {
			foreach ( $product_ids as $id ) {
				if ( $id ) {
					$button = do_shortcode( '[wcboost_wishlist_button product_id="' . $id . '"]' );
					$fragments['.wcboost-wishlist-button[data-product_id="' . $id . '"]'] = $button;
				}
			}
		}

		$wishlist = Helper::get_wishlist();

		wp_send_json_success( [
			'fragments'      => $fragments,
			'wishlist_hash'  => $wishlist->get_hash(),
			'wishlist_items' => self::get_wishlist_items(),
		] );
	}

	/**
	 * Get a refreshed wishlist fragment
	 *
	 * @since 1.0.0
	 */
	public static function get_refreshed_fragments() {
		ob_start();
		Helper::widget_content();
		$widget_content = ob_get_clean();

		$data = [
			'.wcboost-wishlist-widget .wcboost-wishlist-widget-content' => $widget_content,
		];

		return apply_filters( 'wcboost_wishlist_add_to_wishlist_fragments', $data );
	}

	/**
	 * Get product ids of the wishlist
	 *
	 * @param  string|bool $wishlist_id
	 *
	 * @return void
	 */
	public static function get_wishlist_items( $wishlist_id = false ) {
		$wishlist = Helper::get_wishlist( $wishlist_id );
		$items    = $wishlist->get_items();
		$data     = [];

		foreach ( $items as $item ) {
			$id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			$data[ $id ] = [
				'quantity'   => $item->get_quantity(),
				'remove_url' => $item->get_remove_url(),
			];
		}

		return $data;
	}
}
