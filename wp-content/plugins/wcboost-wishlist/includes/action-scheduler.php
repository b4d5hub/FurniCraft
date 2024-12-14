<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

class Action_Scheduler {

	/**
	 * Init actions scheduler
	 */
	public static function init() {
		add_action( 'admin_init', [ __CLASS__, 'schedule' ] );

		add_action( 'wcboost_wishlist_cleanup', [ __CLASS__, 'delete_expired_lists' ] );
	}

	/**
	 * Schedule actions
	 */
	public static function schedule() {
		if ( ! WC()->queue()->get_next( 'wcboost_wishlist_cleanup' ) ) {
			WC()->queue()->schedule_recurring( strtotime( 'midnight tonight' ), DAY_IN_SECONDS, 'wcboost_wishlist_cleanup' );
		}
	}

	/**
	 * Delete expired wishlists and items
	 */
	public static function delete_expired_lists() {
		self::remove_expired_items();
		self::remove_expired_wishlists();
	}

	/**
	 * Remove expired items which have status as "trash"
	 */
	public static function remove_expired_items() {
		try {
			\WC_Data_Store::load( 'wcboost_wishlist_item' )->delete_expired();
		} catch ( \Exception $e ) {
			wc_caught_exception( $e, __METHOD__ );
		}
	}

	/**
	 * Remove expired wishlists which have status as "trash" or were created by guest users
	 */
	public static function remove_expired_wishlists() {
		try {
			\WC_Data_Store::load( 'wcboost_wishlist' )->delete_expired();
		} catch ( \Exception $e ) {
			wc_caught_exception( $e, __METHOD__ );
		}
	}

}
