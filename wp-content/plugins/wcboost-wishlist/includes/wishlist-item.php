<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Wishlist Item Data
 */
class Wishlist_Item extends \WC_Data {
	/**
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = [
		'item_id'      => '',
		'status'       => 'publish', // publish, trash
		'item_key'     => '',
		'product_id'   => 0,
		'variation_id' => 0,
		'quantity'     => 1,
		'wishlist_id'  => 0,
		'date_added'   => '',
		'date_expires' => '',
	];

	/**
	 * The original product
	 *
	 * @var \WC_Product
	 */
	protected $product;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'wcboost_wishlist_item';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'wishlists';

	/**
	 * Wishlist item constructor. Loads wishlist item data.
	 *
	 * @param mixed $data Wishlist item data, object, ID or token.
	 */
	public function __construct( $data = '' ) {
		parent::__construct( $data );

		$this->data_store = \WC_Data_Store::load( 'wcboost_wishlist_item' );

		// If we already have a wishlist object, read it again.
		if ( $data instanceof self ) {
			$this->set_id( absint( $data->get_item_id() ) );
			$this->read_object_from_database();
			return;
		}

		// Set a product object.
		if ( $data instanceof \WC_Product ) {
			if ( $data->get_type() == 'variation' ) {
				$this->set_product_id( $data->get_parent_id() );
				$this->set_variation_id( $data->get_id() );
			} else {
				$this->set_product_id( $data->get_id() );
			}

			$item_key = $this->data_store->generate_item_key( $this );
			$this->set_item_key( $item_key );

			return;
		}

		// Set the data manually.
		if ( is_array( $data ) ) {
			$this->read_manual_data( $data );
			return;
		}

		// Try to load wishlist item using ID.
		if ( is_int( $data ) && $data ) {
			$this->set_id( $data );
			$this->set_item_id( $data );
		} else {
			$this->set_object_read( true );
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 */
	protected function read_object_from_database() {
		if ( $this->get_item_id() <= 0 ) {
			return;
		}

		try {
			$this->data_store->read( $this );
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Error reading wishlist item: ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Developers can programmatically return items.
	 *
	 * @param array $data Array of item properties.
	 */
	protected function read_manual_data( $data ) {
		$this->set_props( $data );

		if ( ! empty( $data['item_id'] ) ) {
			$this->set_id( $data['item_id'] );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}

		$item_key = $this->data_store->generate_item_key( $this );

		if ( $item_key ) {
			$this->set_item_key( $item_key );
		}
	}

	/**
	 * Set item id
	 *
	 * @param int $id
	 */
	public function set_item_id( $id ) {
		$this->set_prop( 'item_id', absint( $id ) );
	}

	/**
	 * Set item key
	 *
	 * @param int $id
	 */
	public function set_item_key( $key ) {
		$this->set_prop( 'item_key', $key );
	}

	/**
	 * Set product id
	 *
	 * @param int $id
	 */
	public function set_product_id( $id ) {
		$this->set_prop( 'product_id', absint( $id ) );
	}

	/**
	 * Set variation id
	 *
	 * @param int $id
	 */
	public function set_variation_id( $id ) {
		$this->set_prop( 'variation_id', absint( $id ) );
	}

	/**
	 * Set item status
	 *
	 * @param string $status The item status: publish, trash
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', in_array( $status, [ 'publish', 'trash' ] ) ? $status : 'publish' );
	}

	/**
	 * Set quantity
	 *
	 * @param int $quantity
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', max( intval( $quantity ), 0 ) );
	}

	/**
	 * Set wishlist id
	 *
	 * @param int $id
	 */
	public function set_wishlist_id( $id ) {
		$this->set_prop( 'wishlist_id', absint( $id ) );
	}

	/**
	 * Set added date
	 *
	 * @param string|int $date
	 */
	public function set_date_added( $date ) {
		$this->set_date_prop( 'date_added', $date );
	}

	/**
	 * Set added expires
	 *
	 * @param string|int $date
	 */
	public function set_date_expires( $date ) {
		$this->set_date_prop( 'date_expires', $date );
	}

	/**
	 * Get item id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_item_id( $context = 'view' ) {
		return (int) $this->get_prop( 'item_id', $context );
	}

	/**
	 * Get item status
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get item key
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_item_key( $context = 'view' ) {
		return $this->get_prop( 'item_key', $context );
	}

	/**
	 * Get product id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_product_id( $context = 'view' ) {
		return absint( $this->get_prop( 'product_id', $context ) );
	}

	/**
	 * Get variation id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_variation_id( $context = 'view' ) {
		return absint( $this->get_prop( 'variation_id', $context ) );
	}

	/**
	 * Get quantity
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_quantity( $context = 'view' ) {
		return (int) $this->get_prop( 'quantity', $context );
	}

	/**
	 * Get wishlist id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_wishlist_id( $context = 'view' ) {
		return (int) $this->get_prop( 'wishlist_id', $context );
	}

	/**
	 * Get added date
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL
	 */
	public function get_date_added( $context = 'view' ) {
		return $this->get_prop( 'date_added', $context );
	}

	/**
	 * Get expries date
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL
	 */
	public function get_date_expires( $context = 'view' ) {
		return $this->get_prop( 'date_expires', $context );
	}

	/**
	 * Return product object
	 *
	 * @return \WC_Product || \WC_Product_Variation
	 */
	public function get_product() {
		if ( empty( $this->product ) ) {
			$product_id = $this->get_product_id();

			if ( wc_string_to_bool( get_option( 'wcboost_wishlist_allow_adding_variations' ) ) && $this->get_variation_id() ) {
				$product_id = $this->get_variation_id();
			}

			$product = wc_get_product( $product_id );

			if ( $product ) {
				$this->product = $product;
			} else {
				return false;
			}
		}

		return $this->product;
	}

	/**
	 * Move item to trash.
	 * Change the status to "trash" and set the "date_expires" to the next day.
	 *
	 * @return bool
	 */
	public function trash() {
		if ( $this->data_store ) {
			do_action( 'wcboost_wishlist_item_move_to_trash', $this );

			$this->set_status( 'trash' );
			$this->set_date_expires( strtotime( '+1 hour' ) );
			$this->save();

			do_action( 'wcboost_wishlist_item_moved_to_trash', $this );

			return true;
		}

		return false;
	}

	/**
	 * Restore item from trash.
	 * Change the status to "publish" and reset the "date_expires".
	 *
	 * @return bool
	 */
	public function restore() {
		if ( $this->data_store ) {
			do_action( 'wcboost_wishlist_item_restore', $this );

			$this->set_status( 'publish' );
			$this->set_date_expires( '' );
			$this->save();

			do_action( 'wcboost_wishlist_item_restored', $this );

			return true;
		}

		return false;
	}

	/**
	 * Add to wishlist URL
	 *
	 * @return string
	 */
	public function get_add_url() {
		$url = remove_query_arg(
			'added-to-wishlist',
			add_query_arg(
				[ 'add-to-wishlist' => $this->get_product()->get_id() ],
				is_feed() || is_404() ? $this->get_product()->get_permalink() : ''
			)
		);

		return apply_filters( 'wcboost_wishlist_add_to_wishlist_url', $url, $this );
	}

	/**
	 * Remove from wishlist URL
	 *
	 * @return string
	 */
	public function get_remove_url() {
		$url = remove_query_arg(
			'removed-wishlist-item',
			add_query_arg(
				[
					'remove-wishlist-item' => $this->get_item_key(),
					'_wpnonce' => wp_create_nonce( 'wcboost-wishlist-remove-item' )
				],
				is_feed() || is_404() ? $this->get_product()->get_permalink() : ''
			)
		);

		return apply_filters( 'wcboost_wishlist_remove_from_wishlist_url', $url, $this );
	}

	/**
	 * Restore item URL
	 *
	 * @return string
	 */
	public function get_restore_url() {
		$referer = is_feed() || is_404() ? $this->get_product()->get_permalink() : '';
		$url     = add_query_arg( [
			'undo-wishlist-item' => $this->get_item_key(),
			'_wpnonce' => wp_create_nonce( 'wcboost-wishlist-restore-item' )
		], $referer );

		return apply_filters( 'wcboost_wishlist_remove_from_wishlist_url', $url, $this );
	}
}
