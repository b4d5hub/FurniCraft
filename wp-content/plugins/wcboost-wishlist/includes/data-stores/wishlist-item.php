<?php
namespace WCBoost\Wishlist\DataStore;

defined( 'ABSPATH' ) || exit;

/**
 * Wishlist Item Data Store
 */
class Wishlist_Item {

	/**
	 * Method to create a new wishlist item in the database
	 *
	 * @global wpdb $wpdb
	 * @param \WCBoost\Wishlist\Wishlist_Item $item
	 */
	public function create( &$item ) {
		global $wpdb;

		if ( ! $item->get_product_id() || ! $item->get_wishlist_id() ) {
			return;
		}

		$product = wc_get_product( $item->get_product_id() );

		if ( ! $product ) {
			return;
		}

		if ( ! $item->get_date_added() ) {
			$item->set_date_added( time() );
		}

		$data = [
			'status'       => $item->get_status(),
			'product_id'   => $item->get_product_id(),
			'variation_id' => $item->get_variation_id(),
			'quantity'     => $item->get_quantity(),
			'wishlist_id'  => $item->get_wishlist_id(),
			'date_added'   => $item->get_date_added()->format( 'Y-m-d H:i:s' ),
			'date_expires' => $item->get_date_expires(),
		];

		if ( $item->get_date_expires() ) {
			$data['date_expires'] = $item->get_date_expires()->format( 'Y-m-d H:i:s' );
		}

		$format = [
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
		];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . 'wcboost_wishlist_items',
			$data,
			$format
		);

		if ( $result ) {
			$item->set_item_id( $wpdb->insert_id );
			$item->set_id( $wpdb->insert_id );
			$item->set_item_key( $this->generate_item_key( $item ) );
			$item->apply_changes();

			do_action( 'wcboost_wishlist_item_inserted', $item );
		}
	}

	/**
	 * Update the wishlist item into the database
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb
	 * @param \WCBoost\Wishlist\Wishlist_Item $item
	 */
	public function update( &$item ) {
		global $wpdb;

		$changes = $item->get_changes();

		if ( array_intersect( ['status', 'quantity', 'wishlist_id', 'date_expires'], array_keys( $changes ) ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update(
				$wpdb->prefix . 'wcboost_wishlist_items',
				[
					'status'       => $item->get_status( 'edit' ),
					'quantity'     => $item->get_quantity( 'edit' ),
					'wishlist_id'  => $item->get_wishlist_id( 'edit' ),
					'date_expires' => $item->get_date_expires() ? $item->get_date_expires()->format( 'Y-m-d H:i:s' ) : '',
				],
				[
					'item_id' => $item->get_item_id( 'edit' )
				]
			);
		}

		$item->apply_changes();
		$this->clear_cache( $item );

		do_action( 'wcboost_wishlist_item_updated', $item );
	}

	/**
	 * Remove a wishlist from the database
	 *
	 * @since 1.0.0
	 *
	 * @param \WCBoost\Wishlist\Wishlist_Item $item Wishlist object
	 * @param array    $args Array of args to pass to the delete method.
	 */
	public function delete( &$item, $args = [] ) {
		if ( ! $item->get_item_id() ) {
			return;
		}

		global $wpdb;

		do_action( 'wcboost_wishlist_item_delete', $item );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $wpdb->prefix . 'wcboost_wishlist_items', [ 'item_id' => $item->get_item_id() ], [ '%d' ] );

		do_action( 'wcboost_wishlist_item_deleted', $item );

		$item->apply_changes();
		$this->clear_cache( $item );
	}

	/**
	 * Read a order item from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param \WCBoost\Wishlist\Wishlist_Item $item Wishlist object.
	 *
	 * @throws Exception If invalid order item.
	 */
	public function read( &$item ) {
		global $wpdb;

		$item_id = $item->get_item_id();

		if ( ! $item_id ) {
			throw new \Exception( esc_html__( 'Invalid item.', 'wcboost-wishlist' ) );
		}

		// Get from cache if available.
		$data = wp_cache_get( 'wcboost-wishlist-item-' . $item_id, 'wishlists' );

		if ( false === $data ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcboost_wishlist_items WHERE item_id = %d LIMIT 1;", $item_id ) );
		}

		if ( ! $data ) {
			throw new \Exception( esc_html__( 'No item found with given id.', 'wcboost-wishlist' ) );
		}

		wp_cache_set( 'wcboost-wishlist-item-' . $item_id, $data, 'wishlists' );

		$item->set_props( [
			'item_id'      => $data->item_id,
			'status'       => $data->status,
			'product_id'   => $data->product_id,
			'variation_id' => $data->variation_id,
			'quantity'     => $data->quantity,
			'wishlist_id'  => $data->wishlist_id,
			'date_added'   => $data->date_added,
			'date_expires' => $data->date_expires,
		] );

		$item_key = $this->generate_item_key( $item );

		if ( $item_key ) {
			$item->set_item_key( $item_key );
		}

		$item->set_id( $data->item_id );
		$item->set_object_read();
	}

	/**
	 * Deleted expired items which are in trash box
	 */
	public function delete_expired() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcboost_wishlist_items WHERE status = 'trash' AND date_expires < CURDATE();" );
	}

	/**
	 * Generate the item key tokens
	 *
	 * @param \WCBoost\Wishlist\Wishlist_Item $item
	 * @return string|bool
	 */
	public function generate_item_key( $item ) {
		$product_id   = $item->get_product_id();
		$variation_id = $item->get_variation_id();

		if ( ! $product_id ) {
			return false;
		}

		$id_parts = [ $product_id, $variation_id ];

		return apply_filters( 'wcboost_wishlist_item_key', md5( implode( '_', $id_parts ) ), $product_id, $variation_id );
	}

	/**
	 * Clear cache.
	 *
	 * @since 1.0.0
	 *
	 * @param Wishlist $wishlist Wishlist object
	 */
	public function clear_cache( &$item ) {
		if ( $item->get_item_id() ) {
			wp_cache_delete( 'wcboost-wishlist-item-' . $item->get_item_id(), 'wishlists' );
		}
	}
}
