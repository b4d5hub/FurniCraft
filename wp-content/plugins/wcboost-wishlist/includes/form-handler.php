<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

class Form_Handler {
	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_loaded', [ __CLASS__, 'add_item_action' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'remove_item_action' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'restore_item_action' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'update_wishlist' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'delete_wishlist' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'merge_guest_wishlist' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'ignore_merge_guest_wishlist' ], 20 );

		// Auto removal.
		switch ( get_option( 'wcboost_wishlist_auto_remove' ) ) {
			case 'on_addtocart':
				add_action( 'woocommerce_add_to_cart', [ __CLASS__, 'auto_remove_item_on_add_to_cart' ], 10, 4 );
				break;

			case 'on_checkout':
				add_action( 'woocommerce_new_order_item', [ __CLASS__, 'auto_remove_item_on_checkout' ], 10, 2 );
				break;
		}
	}

	/**
	 * Add to wishlist action.
	 *
	 * @param string|bool $url The URL to redirect to.
	 */
	public static function add_item_action( $url = false ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['add-to-wishlist'] ) || ! is_numeric( wp_unslash( $_REQUEST['add-to-wishlist'] ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		// Stop if not allowing guests to creating wishlishs.
		if ( ! wc_string_to_bool( get_option( 'wcboost_wishlist_enable_guest_wishlist', 'yes' ) ) && ! is_user_logged_in() ) {
			if ( 'redirect_to_account_page' == get_option( 'wcboost_wishlist_guest_behaviour', 'message' ) ) {
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			} else {
				$message = get_option( 'wcboost_wishlist_guest_message', __( 'You need to login to add products to your wishlist', 'wcboost-wishlist' ) );

				if ( $message ) {
					wc_add_notice( $message, 'notice' );
				}

				return;
			}
		}

		wc_nocache_headers();

		$product_id     = apply_filters( 'wcboost_wishlist_add_to_wishlist_product_id', absint( $_REQUEST['add-to-wishlist'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$adding_product = wc_get_product( $product_id );

		if ( ! $adding_product ) {
			return;
		}

		if ( ! wc_string_to_bool( get_option( 'wcboost_wishlist_allow_adding_variations' ) ) && $adding_product->is_type( 'variation' ) ) {
			$adding_product = wc_get_product( $adding_product->get_parent_id() );
		}

		$wishlist_id = ! empty( $_REQUEST['wishlist'] ) ? absint( wp_unslash( $_REQUEST['wishlist'] ) ) : 0;
		$wishlist    = Helper::get_wishlist( $wishlist_id );
		$item        = new Wishlist_Item( $adding_product );

		// Insert the wishlist to db if it is a temporary one, so we have the wishlist_id.
		if ( ! $wishlist->get_id() ) {
			$wishlist->save();
		}

		if ( ! $wishlist->can_edit() ) {
			return;
		}

		if ( ! empty( $_REQUEST['quantity'] ) ) {
			$item->set_quantity( absint( $_REQUEST['quantity'] ) );
		}

		$was_added = $wishlist->add_item( $item );

		if ( $was_added && ! is_wp_error( $was_added ) ) {
			/* translators: %s: product name */
			$message = sprintf( esc_html__( '%s has been added to your wishlist', 'wcboost-wishlist' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );

			if ( wc_string_to_bool( get_option( 'wcboost_wishlist_redirect_after_add' ) ) ) {
				$return_to = apply_filters( 'wcboost_wishlist_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
				$button    = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a>', esc_url( $return_to ), esc_html__( 'Continue shopping', 'wcboost-wishlist' ) );
			} else {
				$return_to = $wishlist_id ? add_query_arg( [ 'wishlist_id' => $wishlist_id ], wc_get_page_permalink( 'wishlist' ) ) : wc_get_page_permalink( 'wishlist' );
				$button    = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a>', esc_url( $return_to ), esc_html__( 'View wishlist', 'wcboost-wishlist' ) );
			}

			wc_add_notice( $button . $message );

			// Redirect.
			$url = apply_filters( 'wcboost_wishlist_add_to_wishlist_redirect', $url, $adding_product );

			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			} elseif ( wc_string_to_bool( get_option( 'wcboost_wishlist_redirect_after_add' ) ) ) {
				wp_safe_redirect( wc_get_page_permalink( 'wishlist' ) );
				exit;
			}
		} else {
			$code = is_wp_error( $was_added ) ? $was_added->get_error_code() : false;

			switch ( $code ) {
				case 'item_exists':
					/* translators: %s: product name */
					$message = sprintf( esc_html__( '%s already exists in your wishlist', 'wcboost-wishlist' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );
					break;

				case 'no_permission':
					/* translators: %s: product name */
					$message = sprintf( esc_html__( '%s cannot be added to the wishlist', 'wcboost-wishlist' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );
					break;

				default:
					/* translators: %s: product name */
					$message = sprintf( esc_html__( 'Failed to add %s to your wishlist', 'wcboost-wishlist' ), '&ldquo;' . $adding_product->get_title() . '&rdquo;' );
					break;
			}

			wc_add_notice( $message, 'error' );
		}
		// phpcs:enable
	}

	/**
	 * Remove an item from the current wishlist.
	 * This action only removes items from the active wishlist.
	 */
	public static function remove_item_action() {
		if ( empty( $_GET['remove-wishlist-item'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wcboost-wishlist-remove-item' ) ) {
			return;
		}

		wc_nocache_headers();

		$wishlist    = Helper::get_wishlist();
		$item_key    = sanitize_text_field( wp_unslash( $_GET['remove-wishlist-item'] ) );
		$item        = $wishlist->get_item( $item_key );
		$was_removed = $wishlist->remove_item( $item_key );

		if ( $was_removed && ! is_wp_error( $was_removed ) ) {
			$wishlist_url    = wc_get_page_permalink( 'wishlist' );
			/* translators: %s: product name */
			$removed_notice  = sprintf( __( '%s is removed from the wishlist.', 'wcboost-wishlist' ), '&ldquo;' . $item->get_product()->get_title() . '&rdquo;' );
			$removed_notice .= ' <a href="' . esc_url( $item->get_restore_url() ) . '" class="restore-item">' . esc_html__( 'Undo?', 'wcboost-wishlist' ) . '</a>';

			wc_add_notice( $removed_notice, 'success' );

			$referer = wp_get_referer() ? remove_query_arg( [ 'remove-wishlist-item', 'add-to-wishlist', 'added-to-wishlist', 'undo-wishlist-item', '_wpnonce' ], add_query_arg( 'removed-wishlist-item', '1', wp_get_referer() ) ) : $wishlist_url;
			wp_safe_redirect( $referer );
			exit;
		} else {
			$code = is_wp_error( $was_removed ) ? $was_removed->get_error_code() : false;

			switch ( $code ) {
				case 'not_exists':
					$message = $was_removed->get_error_message();
					break;

				case 'no_permission':
					$message = esc_html__( 'You are not allowed to remove this item', 'wcboost-wishlist' );
					break;

				default:
					$message = esc_html__( 'Failed to remove the wishlist item', 'wcboost-wishlist' );
					break;
			}

			wc_add_notice( $message, 'error' );
		}

	}

	/**
	 * Restore a wishlist item that has just been removed
	 */
	public static function restore_item_action() {
		if ( empty( $_GET['undo-wishlist-item'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wcboost-wishlist-restore-item' ) ) {
			return;
		}

		wc_nocache_headers();

		$wishlist     = Helper::get_wishlist();
		$item_key     = sanitize_text_field( wp_unslash( $_GET['undo-wishlist-item'] ) );
		$was_restored = $wishlist->restore_item( $item_key );

		if ( $was_restored && ! is_wp_error( $was_restored ) ) {
			$referer = wp_get_referer() ? remove_query_arg( [ 'undo-wishlist-item', 'removed-wishlist-item', '_wpnonce' ], wp_get_referer() ) : wc_get_page_permalink( 'wishlist' );
			wp_safe_redirect( $referer );
			exit;
		} else {
			$code = is_wp_error( $was_restored ) ? $was_restored->get_error_code() : false;

			switch ( $code ) {
				case 'not_exists':
					$message = $was_restored->get_error_message();
					break;

				case 'no_permission':
					$message = esc_html__( 'You are not allowed to restore this item', 'wcboost-wishlist' );
					break;

				default:
					$message = esc_html__( 'Failed to restore the wishlist item', 'wcboost-wishlist' );
					break;
			}

			wc_add_notice( $message, 'error' );
		}
	}

	/**
	 * Update wishlist action
	 */
	public static function update_wishlist() {
		if ( ( isset( $_POST['action'] ) && 'update_wishlist' != $_POST['action'] ) || ! isset( $_POST['update_wishlist'] ) ) {
			return;
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wcboost-wishlist-update' ) ) {
			return;
		}

		wc_nocache_headers();

		$wishlist_id = isset( $_POST['wishlist_id'] ) ? absint( $_POST['wishlist_id'] ) : 0;

		if ( ! $wishlist_id ) {
			return;
		}

		$wishlist = Helper::get_wishlist( $wishlist_id );

		if ( ! $wishlist->get_id() || ! $wishlist->can_edit() ) {
			return;
		}

		// Update details.
		if ( ! empty( $_POST['wishlist_title'] ) ) {
			$title = sanitize_text_field( wp_unslash( $_POST['wishlist_title'] ) );

			if ( $wishlist->get_wishlist_title() != $title ) {
				$wishlist->set_wishlist_title( $title );
				$wishlist->set_wishlist_slug( sanitize_title_with_dashes( $title ) );
			}
		}

		if ( ! empty( $_POST['wishlist_description'] ) ) {
			$wishlist->set_description( sanitize_textarea_field( wp_unslash( $_POST['wishlist_description'] ) ) );
		}

		if ( ! empty( $_POST['wishlist_privacy'] ) ) {
			$wishlist->set_status( sanitize_text_field( wp_unslash( $_POST['wishlist_privacy'] ) ) );
		}

		$wishlist->save();

		// Update items.
		$items = isset( $_POST['wishlist_item'] ) && is_array( $_POST['wishlist_item'] ) ? wp_unslash( $_POST['wishlist_item'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		foreach ( $items as $item_key => $item_data ) {
			$item = $wishlist->get_item( $item_key );

			if ( ! $item || ! $item->get_id() ) {
				continue;
			}

			// Update quantity.
			if ( isset( $item_data['qty'] ) ) {
				$qty = absint( $item_data['qty'] );

				if ( $qty ) {
					$item->set_quantity( $qty );
				} else {
					$item->trash();
				}
			}

			$item->save();
		}

		wc_add_notice( __( 'Wishlist updated', 'wcboost-wishlist' ) );

		$referer = wp_get_referer() ? remove_query_arg( [ 'undo-wishlist-item', 'removed-wishlist-item', '_wpnonce' ], wp_get_referer() ) : wc_get_page_permalink( 'wishlist' );
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Delete wishlist action
	 */
	public static function delete_wishlist() {
		if ( ( isset( $_POST['action'] ) && 'delete_wishlist' != $_POST['action'] ) || ! isset( $_POST['delete_wishlist'] ) ) {
			return;
		}

		if ( empty( $_POST['wishlist_id'] ) || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wcboost-wishlist-delete' ) ) {
			return;
		}

		wc_nocache_headers();

		$wishlist_id = absint( $_POST['wishlist_id'] );
		$wishlist    = Helper::get_wishlist( $wishlist_id );

		if ( ! $wishlist->can_edit() ) {
			return;
		}

		if ( $wishlist->is_default() ) {
			wc_add_notice( __( 'You cannot delete the default wishlist', 'wcboost-wishlist' ), 'error' );
		} else {
			$wishlist->trash();
			wc_add_notice( __( 'Wishlist deleted', 'wcboost-wishlist' ) );
		}

		$referer = wc_get_page_permalink( 'shop' );
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Merge guest wishlist action
	 *
	 * @since 1.1.4
	 */
	public static function merge_guest_wishlist() {
		if ( ! isset( $_GET['action'] ) || 'merge_guest_wishlist' != $_GET['action'] ) {
			return;
		}

		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wcboost-wishlist-merge' ) ) {
			return;
		}

		wc_nocache_headers();

		$wishlist          = Helper::get_wishlist();
		$guest_wishlist_id = $wishlist->get_data_store()->get_wishlist_id_by_session( Session::get_session_id() );
		$guest_wishlist    = $guest_wishlist_id ? Helper::get_wishlist( $guest_wishlist_id ) : null;

		if ( $guest_wishlist && ! $guest_wishlist->is_empty() ) {
			$merged_item_count = $wishlist->merge( $guest_wishlist );

			if ( $merged_item_count ) {
				/* translators: %d: number of items */
				$message = _n( '%d item has been added to your wishlist', '%d items have been added to your wishlist', $merged_item_count, 'wcboost-wishlist' );
				wc_add_notice( sprintf( $message, $merged_item_count ) );
				$wishlist->save();
			} else {
				wc_add_notice( __( 'All items in the temporary wishlist have been added to your wishlist', 'wcboost-wishlist' ), 'notice' );
			}

			if ( \wc_string_to_bool( get_option( 'wcboost_wishlist_clear_guest_wishlist_after_merge', 'yes' ) ) ) {
				$guest_wishlist->empty();
			}

			Session::delete_merge_guest_wishlist_flag();
		}

		if ( wp_get_referer() ) {
			$referer = remove_query_arg( [ 'undo-wishlist-item', 'removed-wishlist-item', '_wpnonce' ], wp_get_referer() );
		} else {
			$referer = wc_get_page_permalink( 'wishlist' );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Ignore merge guest wishlist action
	 *
	 * @since 1.1.4
	 */
	public static function ignore_merge_guest_wishlist() {
		if ( ! isset( $_GET['action'] ) || 'ignore_merge_guest_wishlist' != $_GET['action'] ) {
			return;
		}

		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wcboost-wishlist-merge-ignore' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		Session::delete_merge_guest_wishlist_flag();

		$referer = wp_get_referer() ? remove_query_arg( [ 'action', '_wpnonce' ], wp_get_referer() ) : wc_get_page_permalink( 'wishlist' );
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Remove a product from the wishlist automatically once it is added to the cart.
	 *
	 * @param string $cart_item_key
	 * @param int $product_id
	 * @param int $quantity
	 * @param int $variation_id
	 */
	public static function auto_remove_item_on_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id ) {
		$removing_product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );

		self::auto_remove_item( $removing_product );
	}

	/**
	 * Remove a product from the wishlist automatically once an order has been created
	 *
	 * @param int $order_item_id
	 * @param \WC_Order_Item $order_item
	 */
	public static function auto_remove_item_on_checkout( $order_item_id, $order_item ) {
		if ( ! is_a( $order_item, '\WC_Order_Item_Product' ) ) {
			return;
		}

		/** @var WC_Order_Item_Product $order_item */
		$removing_product = $order_item->get_product();
		self::auto_remove_item( $removing_product );
	}

	/**
	 * Auto remove item an item from the wishlist
	 *
	 * @param \WC_Product $product
	 */
	protected static function auto_remove_item( $product ) {
		$removing_item = new Wishlist_Item( $product );
		$wishlist_id   = isset( $_REQUEST['wishlist_id'] ) ? absint( $_REQUEST['wishlist_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $wishlist_id ) {
			Helper::get_wishlist( $wishlist_id )->remove_item( $removing_item->get_item_key() );
		} else {
			$wishlists = Plugin::instance()->query->get_user_wishlists();

			foreach ( $wishlists as $wishlist ) {
				$wishlist->remove_item( $removing_item->get_item_key() );
			}
		}
	}
}
