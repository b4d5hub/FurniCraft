<?php
namespace WCBoost\Wishlist\DataStore;
use WCBoost\Wishlist\Session;

defined( 'ABSPATH' ) || exit;

/**
 * Wishlist Data Store
 */
class Wishlist {

	/**
	 * Is reading data from the database.
	 *
	 * @var bool
	 */
	private $is_reading = false;

	/**
	 * Method to create a new wishlist in the database
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 */
	public function create( &$wishlist ) {
		global $wpdb;

		if ( ! $wishlist->get_wishlist_title( 'edit' ) ) {
			$default_title = get_option( 'wcboost_wishlist_title_default' );
			$default_title = $default_title ? $default_title : __( 'My Wishlist', 'wcboost-wishlist' );

			$wishlist->set_wishlist_title( $default_title );
		}

		if ( ! $wishlist->get_wishlist_slug( 'edit' ) ) {
			$slug = sanitize_title_with_dashes( $wishlist->get_wishlist_title() );
			$wishlist->set_wishlist_slug( $slug );
		}

		if ( ! $wishlist->get_wishlist_token( 'edit' ) ) {
			$wishlist->set_wishlist_token( $this->generate_token() );
		}

		if ( is_user_logged_in() ) {
			$user_id = $wishlist->get_user_id( 'edit' );
			$current_user_id = get_current_user_id();

			if ( ! $user_id || ( $current_user_id != $user_id && ! current_user_can( 'manage_options' ) ) ) {
				$wishlist->set_user_id( $current_user_id );
			}
		} else {
			$wishlist->set_session_id( $this->generate_session_id() );
		}

		if ( ! $wishlist->get_date_created( 'edit' ) ) {
			$wishlist->set_date_created( time() );
		}

		if ( ! is_user_logged_in() ) {
			$wishlist->set_date_expires( strtotime( '+30 days' ) );
		}

		$data = [
			'wishlist_title' => $wishlist->get_wishlist_title(),
			'wishlist_slug'  => $wishlist->get_wishlist_slug(),
			'wishlist_token' => $wishlist->get_wishlist_token(),
			'description'    => $wishlist->get_description(),
			'menu_order'     => $wishlist->get_menu_order(),
			'status'         => $wishlist->get_status(),
			'user_id'        => $wishlist->get_user_id(),
			'session_id'     => $wishlist->get_session_id(),
			'date_created'   => $wishlist->get_date_created()->format( 'Y-m-d H:i:s' ),
			'date_expires'   => $wishlist->get_date_expires(),
			'is_default'     => $wishlist->get_is_default(),
		];

		if ( $wishlist->get_date_expires() ) {
			$data['date_expires'] = $wishlist->get_date_expires()->format( 'Y-m-d H:i:s' );
		}

		$format = [
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%d',
		];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . 'wcboost_wishlists',
			$data,
			$format
		);

		if ( $result ) {
			$wishlist->set_wishlist_id( $wpdb->insert_id );
			$wishlist->set_id( $wpdb->insert_id );
			$wishlist->apply_changes();

			if ( $wishlist->get_is_default( 'edit' ) ) {
				$this->reset_previous_default_wishlist( $wishlist );
			}

			do_action( 'wcboost_wishlist_inserted', $wishlist );
		}
	}

	/**
	 * Update the wishlist in the database
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 */
	public function update( &$wishlist ) {
		global $wpdb;

		$changes = $wishlist->get_changes();

		if ( array_intersect( ['wishlist_title', 'wishlist_slug', 'description', 'menu_order', 'status', 'is_default'], array_keys( $changes ) ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update(
				$wpdb->prefix . 'wcboost_wishlists',
				[
					'wishlist_title' => $wishlist->get_wishlist_title( 'edit' ),
					'wishlist_slug'  => $wishlist->get_wishlist_slug( 'edit' ),
					'description'    => $wishlist->get_description( 'edit' ),
					'menu_order'     => $wishlist->get_menu_order( 'edit' ),
					'status'         => $wishlist->get_status( 'edit' ),
					'is_default'     => $wishlist->get_is_default( 'edit' ),
				],
				[
					'wishlist_id' => $wishlist->get_wishlist_id( 'edit' )
				]
			);

			// Reset the previous default wishlist.
			if ( $wishlist->get_is_default( 'edit' ) ) {
				$this->reset_previous_default_wishlist( $wishlist );
			}
		}

		$wishlist->apply_changes();
		$this->clear_cache( $wishlist );

		do_action( 'wcboost_wishlist_updated', $wishlist );
	}

	/**
	 * Remove a wishlist from the database
	 *
	 * @since 1.0.0
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist Wishlist object
	 * @param array    $args Array of args to pass to the delete method.
	 */
	public function delete( &$wishlist, $args = [] ) {
		if ( ! $wishlist->get_wishlist_id() ) {
			return;
		}

		// Do not allow deleting the default list.
		if ( $wishlist->is_default() ) {
			return;
		}

		global $wpdb;

		do_action( 'wcboost_wishlist_delete', $wishlist );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $wpdb->prefix . 'wcboost_wishlists', [ 'wishlist_id' => $wishlist->get_wishlist_id() ] );

		do_action( 'wcboost_wishlist_deleted', $wishlist );

		$wishlist->apply_changes();
		$this->clear_cache( $wishlist );
	}

	/**
	 * Read a wishlist item from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist Wishlist object.
	 *
	 * @throws Exception If invalid wishlist
	 */
	public function read( &$wishlist ) {
		global $wpdb;

		$id    = $wishlist->get_wishlist_id();
		$token = $wishlist->get_wishlist_token();

		if ( ! $id && ! $token ) {
			throw new \Exception( esc_html__( 'Invalid wishlist.', 'wcboost-wishlist' ) );
		}

		// Get from cache if available.
		if ( $id ) {
			$data = wp_cache_get( 'wcboost-wishlist-' . $id, 'wishlists' );
		} else {
			$data = wp_cache_get( 'wcboost-wishlist-' . $token, 'wishlists' );
		}

		$this->set_is_reading( true );

		if ( false === $data ) {
			if ( $id ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcboost_wishlists WHERE wishlist_id = %d LIMIT 1;", $id ) );
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcboost_wishlists WHERE wishlist_token = %s LIMIT 1;", $token ) );
			}

			if ( ! $data ) {
				throw new \Exception( esc_html__( 'Invalid wishlist.', 'wcboost-wishlist' ) );
			}

			if ( $id ) {
				wp_cache_set( 'wcboost-wishlist-' . $id, $data, 'wishlists' );
			} else {
				wp_cache_set( 'wcboost-wishlist-' . $token, $data, 'wishlists' );
			}
		}

		$wishlist->set_props( [
			'wishlist_id'    => $data->wishlist_id,
			'wishlist_title' => $data->wishlist_title,
			'wishlist_slug'  => $data->wishlist_slug,
			'wishlist_token' => $data->wishlist_token,
			'description'    => $data->description,
			'menu_order'     => $data->menu_order,
			'status'         => $data->status,
			'user_id'        => $data->user_id,
			'session_id'     => $data->session_id,
			'date_created'   => $data->date_created,
			'date_expires'   => $data->date_expires,
			'is_default'     => $data->is_default,
		] );

		$wishlist->set_id( $data->wishlist_id );
		$wishlist->set_object_read();
		$this->set_is_reading( false );
	}

	/**
	 * Get wishlist items from the database.
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist
	 * @throws Exception If invalid wishlist
	 */
	public function read_items( &$wishlist ) {
		global $wpdb;

		$wishlist_id = $wishlist->get_wishlist_id();

		if ( ! $wishlist_id ) {
			throw new \Exception( esc_html__( 'Invalid wishlist.', 'wcboost-wishlist' ) );
		}

		$this->set_is_reading( true );

		$items = wp_cache_get( 'wcboost-wishlist-items-' . $wishlist_id, 'wishlists' );

		if ( false === $items ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$items = $wpdb->get_col( $wpdb->prepare( "SELECT item_id FROM {$wpdb->prefix}wcboost_wishlist_items WHERE wishlist_id = %d;", [ $wishlist_id ] ) );

			if ( ! empty( $items ) ) {
				wp_cache_set( 'wcboost-wishlist-items-' . $wishlist_id, $items, 'wishlists' );
			}
		}

		foreach ( $items as $item_id ) {
			$item = new \WCBoost\Wishlist\Wishlist_Item( absint( $item_id ) );

			if ( $item->get_status() == 'trash' ) {
				$wishlist->add_item_to_trash( $item );
			} else {
				$wishlist->add_item( $item, [ 'silence' => true ] );
			}
		}

		$this->set_is_reading( false );
	}

	/**
	 * Get ID of the default wishlist
	 *
	 * @return int
	 */
	public function get_default_wishlist_id() {
		global $wpdb;

		$default_wishlist_id = 0;

		if ( is_user_logged_in() ) {
			$wishlist_id = wp_cache_get( 'wcboost-wishlist-default-' . get_current_user_id(), 'wishlists' );

			if ( false === $wishlist_id ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wishlist_id = $wpdb->get_var( $wpdb->prepare( "SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE user_id = %d AND status != 'trash' AND is_default = 1 LIMIT 1;", [ get_current_user_id() ] ) );
				$default_wishlist_id = absint( $wishlist_id );

				wp_cache_set( 'wcboost-wishlist-default-' . get_current_user_id(), $default_wishlist_id, 'wishlists' );
			}
		} else {
			$session_id = Session::get_session_id();

			if ( $session_id ) {
				$wishlist_id = wp_cache_get( 'wcboost-wishlist-default-' . $session_id, 'wishlists' );

				if ( false === $wishlist_id ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wishlist_id = $wpdb->get_var( $wpdb->prepare( "SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE session_id = %s AND status != 'trash' LIMIT 1;", [ $session_id ] ) );
					$default_wishlist_id = absint( $wishlist_id );

					wp_cache_set( 'wcboost-wishlist-default-' . $session_id, $default_wishlist_id, 'wishlists' );
				}
			}
		}

		return $default_wishlist_id;
	}

	/**
	 * Get user wishlist IDs
	 *
	 * @return array
	 */
	public function get_wishlist_ids() {
		global $wpdb;

		if ( ! is_user_logged_in() ) {
			return $this->get_guest_wishlist_ids();
		}

		$ids = wp_cache_get( 'wcboost-user-wishlists-' . get_current_user_id(), 'wishlists' );

		if ( false === $ids ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$ids = $wpdb->get_col( $wpdb->prepare( "SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE user_id = %d AND status != 'trash';", [ get_current_user_id() ] ) );
			$ids = array_map( 'absint', $ids );

			wp_cache_set( 'wcboost-user-wishlists-' . get_current_user_id(), $ids, 'wishlists' );
		}

		return $ids;
	}

	/**
	 * Get guest wishlist IDs
	 *
	 * @since 1.1.4
	 *
	 * @return array
	 */
	public function get_guest_wishlist_ids() {
		global $wpdb;

		$session_id = Session::get_session_id();

		if ( ! $session_id ) {
			return [];
		}

		$ids = wp_cache_get( 'wcboost-user-wishlists-' . $session_id, 'wishlists' );

		if ( false === $ids ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$ids = $wpdb->get_col( $wpdb->prepare( "SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE session_id = %s LIMIT 1;", [ $session_id ] ) );
			$ids = array_map( 'absint', $ids );

			wp_cache_set( 'wcboost-user-wishlists-' . $session_id, $ids, 'wishlists' );
		}

		return $ids;
	}

	/**
	 * Get wishlist ID by session ID
	 *
	 * @since 1.1.4
	 *
	 * @param string $session_id
	 * @return int
	 */
	public function get_wishlist_id_by_session( $session_id ) {
		global $wpdb;

		if ( ! $session_id ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wishlist_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE session_id = %s LIMIT 1;",
				[ $session_id ]
			)
		);

		return absint( $wishlist_id );
	}

	/**
	 * Generate unique token for wishlist
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function generate_token() {
		global $wpdb;

		$length = absint( apply_filters( 'wcboost_wishlist_token_length', 16 ) );
		$length = $length / 2;

		do {
			// Modified from "wc_rand_hash"
			if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
				$hash = sha1( wp_rand() );
			} else {
				$hash = bin2hex( openssl_random_pseudo_bytes( $length ) ); // @codingStandardsIgnoreLine
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wcboost_wishlists WHERE wishlist_token = %s;", $hash ) );
		} while ( $count );

		return $hash;
	}

	/**
	 * Generate an unique user session.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function generate_session_id() {
		if ( is_user_logged_in() ) {
			return false;
		}

		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$hasher     = new \PasswordHash( 8, false );
		$session_id = md5( $hasher->get_random_bytes( 32 ) );

		return $session_id;
	}

	/**
	 * Reset previous default wishlist.
	 * When a new default wishlist is created/updated, previous default wishlist will be reset.
	 *
	 * @since 1.0.0
	 */
	public function reset_previous_default_wishlist( $wishlist ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( $wpdb->prepare(
			"UPDATE {$wpdb->prefix}wcboost_wishlists SET is_default = 0 WHERE wishlist_id != %d AND user_id = %d AND is_default = 1;",
			[ $wishlist->get_wishlist_id( 'edit' ), $wishlist->get_user_id( 'edit' ) ]
		) );
	}

	/**
	 * Deleted expired items which are in trash box
	 */
	public function delete_expired() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcboost_wishlist_items WHERE wishlist_id IN ( SELECT wishlist_id FROM {$wpdb->prefix}wcboost_wishlists WHERE (status = 'trash' OR user_id = 0) AND date_expires < CURDATE() );" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcboost_wishlists WHERE (status = 'trash' OR user_id = 0) AND date_expires < CURDATE();" );
		// phpcs:enable
	}

	/**
	 * Clear cache.
	 *
	 * @since 1.0.0
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist Wishlist object
	 */
	public function clear_cache( &$wishlist ) {
		if ( $wishlist->get_wishlist_id() ) {
			wp_cache_delete( 'wcboost-wishlist-' . $wishlist->get_wishlist_id(), 'wishlists' );
			wp_cache_delete( 'wcboost-wishlist-items-' . $wishlist->get_wishlist_id(), 'wishlists' );
		} elseif ( $wishlist->get_wishlist_token() ) {
			wp_cache_delete( 'wcboost-wishlist-' . $wishlist->get_wishlist_token(), 'wishlists' );
		}

		$user_id    = $wishlist->get_user_id( 'edit' );
		$session_id = $wishlist->get_session_id( 'edit' );

		if ( $user_id ) {
			wp_cache_delete( 'wcboost-wishlist-default-' . $user_id, 'wishlists' );
			wp_cache_delete( 'wcboost-user-wishlists-' . $user_id, 'wishlists' );
		} else {
			wp_cache_delete( 'wcboost-wishlist-default-' . $session_id, 'wishlists' );
			wp_cache_delete( 'wcboost-user-wishlists-' . $session_id, 'wishlists' );
		}
	}

	/**
	 * Set the status to determine if is reading data.
	 * This method is called when initialization is on running.
	 *
	 * @since 1.1.1
	 *
	 * @param  bool $reading
	 *
	 * @return void
	 */
	public function set_is_reading( $reading = false ) {
		$this->is_reading = wc_string_to_bool( $reading );
	}

	/**
	 * Get the reading_db status
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 */
	public function is_reading() {
		return $this->is_reading;
	}
}
