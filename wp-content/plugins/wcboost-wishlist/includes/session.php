<?php
/**
 * Wishlist Session handler
 *
 * @since 1.1.2
 */
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

use WCBoost\Packages\Utilities\Singleton_Trait;

/**
 * Wishlist Session and Cookie Handler Class
 */
final class Session {
	use Singleton_Trait;

	const SESSION_NAME = 'wcboost_wishlist_session';
	const HASH_COOKIE = 'wcboost_wishlist_hash';

	/**
	 * Class constructor
	 */
	public function __construct() {
		// Cookie events.
		add_action( 'wcboost_wishlist_add_item', [ $this, 'maybe_set_hash_cookies' ] );
		add_action( 'wcboost_wishlist_removed_item', [ $this, 'maybe_set_hash_cookies' ] );
		add_action( 'wp', [ $this, 'maybe_set_hash_cookies' ], 99 );
		add_action( 'shutdown', [ $this, 'maybe_set_hash_cookies' ], 0 );

		// Merge wishlist after login.
		add_action( 'wp_login', [ $this, 'set_merge_guest_wishlist_flag' ], 10, 2 );
		add_action( 'wp', [ $this, 'merge_guest_wishlist_notice' ] );
	}

	/**
	 * Will set cookies if needed and when possible.
	 *
	 * @since 1.1.2
	 *
	 * @return void
	 */
	public function maybe_set_hash_cookies() {
		if ( headers_sent() || ! did_action( 'wp_loaded' ) ) {
			return;
		}

		$this->set_hash_cookies();
	}

	/**
	 * Set wishlist hash cookie
	 *
	 * @since 1.1.2
	 *
	 * @param  bool $set Should the cookie be set or unset.
	 *
	 * @return void
	 */
	private function set_hash_cookies( $set = true )  {
		if ( $set ) {
			$wishlist = Helper::get_wishlist();
			$hash     = $wishlist->get_hash();

			wc_setcookie( static::HASH_COOKIE, $hash );
			$_COOKIE[ static::HASH_COOKIE ] = $hash;
		} else {
			wc_setcookie( static::HASH_COOKIE, '', time() - HOUR_IN_SECONDS );
			unset( $_COOKIE[ static::HASH_COOKIE ] );
		}
	}

	/**
	 * Get guest wishlist session ID.
	 *
	 * @since 1.1.2
	 *
	 * @return string
	 */
	public static function get_session_id() {
		if ( empty( $_COOKIE[ self::SESSION_NAME ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( $_COOKIE[ self::SESSION_NAME ] ) );
	}

	/**
	 * Set session id for guests.
	 * Store the session ID in cookie for 30 days. It can be changed via a hook.
	 *
	 * @since 1.1.2
	 *
	 * @param string $session_id
	 */
	public static function set_session_id( $session_id ) {
		$expire = time() + absint( apply_filters( 'wcboost_wishlist_session_expire', MONTH_IN_SECONDS ) );
		wc_setcookie( self::SESSION_NAME, $session_id, $expire );
		$_COOKIE[ self::SESSION_NAME ] = $session_id;
	}

	/**
	 * Clear session ID in the cookie
	 *
	 * @since 1.1.2
	 */
	public static function clear_session_id() {
		wc_setcookie( self::SESSION_NAME, '', time() - HOUR_IN_SECONDS );
		unset( $_COOKIE[ self::SESSION_NAME ] );
	}

	/**
	 * Set the flag to merge wishlist after login.
	 *
	 * @since 1.1.4
	 *
	 * @param string $user_login
	 * @param \WP_User $user
	 */
	public function set_merge_guest_wishlist_flag( $user_login, $user ) {
		if ( ! static::get_session_id() ) {
			return;
		}

		if ( ! \wc_string_to_bool( get_option( 'wcboost_wishlist_enable_guest_wishlist', 'yes' ) ) ) {
			return;
		}

		update_user_meta( $user->ID, '_wcboost_wishlist_merge_after_login', true );
	}

	/**
	 * Delete merge wishlist flag
	 *
	 * @since 1.1.4
	 */
	public static function delete_merge_guest_wishlist_flag() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		delete_user_meta( get_current_user_id(), '_wcboost_wishlist_merge_after_login' );
	}

	/**
	 * Add notice to merge guest wishlist.
	 *
	 * @since 1.1.4
	 */
	public function merge_guest_wishlist_notice() {
		if ( ! is_user_logged_in() || ! Helper::is_wishlist() ) {
			return;
		}

		if ( ! get_user_meta( get_current_user_id(), '_wcboost_wishlist_merge_after_login', true ) ) {
			return;
		}

		// Reset the flag if: no guest wishlist, or merging guest wishlist is disabled.
		if (
			! static::get_session_id() ||
			! \wc_string_to_bool( get_option( 'wcboost_wishlist_enable_guest_wishlist', 'yes' ) ) ||
			! \wc_string_to_bool( get_option( 'wcboost_wishlist_merge_guest_wishlist', 'yes' ) )
		) {
			static::delete_merge_guest_wishlist_flag();
			return;
		}

		// Delete the flag if the guest wishlist is not found or empty.
		$wishlist          = Helper::get_wishlist();
		$guest_wishlist_id = $wishlist->get_data_store()->get_wishlist_id_by_session( static::get_session_id() );
		$guest_wishlist    = $guest_wishlist_id ? Helper::get_wishlist( $guest_wishlist_id ) : null;
		$mergeable         = false;

		if ( $guest_wishlist && ! $guest_wishlist->is_empty() && 'trash' != $guest_wishlist->get_status() ) {
			// Find if there is any new product that is not in the current wishlist.
			foreach ( $guest_wishlist->get_items() as $item ) {
				if ( ! $wishlist->has_product( $item->get_product_id() ) ) {
					$mergeable = true;
					break;
				}
			}
		}

		$mergeable = \wc_string_to_bool( apply_filters( 'wcboost_wishlist_merge_guest_wishlist', $mergeable, $guest_wishlist ) );

		if ( ! $mergeable ) {
			static::delete_merge_guest_wishlist_flag();
			return;
		}

		$message = sprintf( 'We noticed some items in your guest wishlist. Would you like to add them to this wishlist?', 'wcboost-wishlist' );

		$links   = array();
		$links[] = sprintf(
			/* translators: %1$s: URL to ignore merge guest wishlist, %2$s: aria-label for the button, %3$s: text for the button */
			'<a href="%1$s" class="button wcboost-wishlist-merge-button wcboost-wishlist-merge-no" aria-label="%2$s">%3$s</a>',
			esc_url( add_query_arg( [
				'action'   => 'ignore_merge_guest_wishlist',
				'_wpnonce' => wp_create_nonce( 'wcboost-wishlist-merge-ignore' ),
			] ) ),
			esc_attr__( 'No, keep separate and dismiss this notice', 'wcboost-wishlist' ),
			esc_html__( 'No', 'wcboost-wishlist' )
		);
		$links[] = sprintf(
			/* translators: %1$s: URL to merge guest wishlist, %2$s: aria-label for the button, %3$s: text for the button */
			'<a href="%1$s" class="button wcboost-wishlist-merge-button wcboost-wishlist-merge-yes" aria-label="%2$s">%3$s</a>',
			esc_url( add_query_arg( [
				'action'   => 'merge_guest_wishlist',
				'_wpnonce' => wp_create_nonce( 'wcboost-wishlist-merge' ),
			] ) ),
			esc_attr__( 'Yes, merge with the guest wishlist', 'wcboost-wishlist' ),
			esc_html__( 'Yes', 'wcboost-wishlist' )
		);

		$links = apply_filters( 'wcboost_wishlist_merge_guest_wishlist_links', $links );

		$message .= implode( ' ', $links );

		if ( ! wc_has_notice( $message, 'notice' ) ) {
			wc_add_notice( $message, 'notice' );
		}
	}
}
