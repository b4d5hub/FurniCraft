<?php
namespace WCBoost\ProductsCompare;

/**
 * Compare products list
 */
class Compare_List {

	const SESSION_KEY = 'wcboost_products_compare_list';

	/**
	 * The list id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The list of product ids
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Class constructor
	 */
	public function __construct( $product_ids = [] ) {
		if ( ! empty( $product_ids ) ) {
			$this->load_products_manually( $product_ids );
		} else {
			$this->id = wc_rand_hash();

			if ( ! did_action( 'wp_loaded' ) ) {
				add_action( 'wp_loaded', [ $this, 'load_products_from_session' ] );
			} else {
				$this->load_products_from_session();
			}
		}
	}

	/**
	 * Init hooks.
	 * This method is called individually after initialization of the main list.
	 *
	 * @since 1.0.6
	 *
	 * @return void
	 */
	public function init() {
		// Should be called with user-defined list only.
		if ( ! $this->get_id() ) {
			return;
		}

		// Persistent compare list stored to usermeta.
		add_action( 'wcboost_products_compare_product_added', [ $this, 'update_persistent_list' ] );
		add_action( 'wcboost_products_compare_product_removed', [ $this, 'update_persistent_list' ] );
		add_action( 'wcboost_products_compare_list_emptied', [ $this, 'delete_persistent_list' ] );

		// Cookie events.
		add_action( 'wcboost_products_compare_product_added', [ $this, 'maybe_set_cookies' ] );
		add_action( 'wcboost_products_compare_product_removed', [ $this, 'maybe_set_cookies' ] );
		add_action( 'wcboost_products_compare_list_emptied', [ $this, 'maybe_set_cookies' ] );
		add_action( 'wp', [ $this, 'maybe_set_cookies' ], 99 );
		add_action( 'shutdown', [ $this, 'maybe_set_cookies' ], 0 );
	}

	/**
	 * Get product list from WC()->session
	 *
	 * @return void
	 */
	public function load_products_from_session() {
		if ( ! WC()->session ) {
			return;
		}

		$data = WC()->session->get( self::SESSION_KEY, null );
		$update_session = false;
		$merge_list = (bool) get_user_meta( get_current_user_id(), '_wcboost_products_compare_load_after_login', true );

		if ( null === $data || $merge_list ) {
			$saved          = $this->get_saved_list();
			$data           = $data ? $data : [ 'id' => '', 'items' => [] ];
			$data['id']     = empty( $data['items'] ) ? $saved['id'] : $data['id'];
			$data['items']  = array_merge( $saved['items'], $data['items'] );
			$update_session = true;

			delete_user_meta( get_current_user_id(), '_wcboost_products_compare_load_after_login' );
		}

		foreach ( $data['items'] as $product_id ) {
			$key = Helper::generate_item_key( $product_id );
			$this->items[ $key ] = $product_id;
		}

		if ( ! empty( $data['id'] ) ) {
			$this->id = $data['id'];
		}

		if ( $update_session ) {
			$this->update();

			if ( $merge_list ) {
				$this->update_persistent_list();
			}
		}
	}

	/**
	 * Set the list data manually.
	 * Manully add products to the list.
	 *
	 * @param  array $product_ids
	 * @return void
	 */
	protected function load_products_manually( $product_ids ) {
		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				continue;
			}

			$key = Helper::generate_item_key( $product_id );
			$this->items[ $key ] = $product_id;
		}
	}

	/**
	 * Get the list id
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the list items.
	 *
	 * @return array
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Count the number of items
	 *
	 * @return int
	 */
	public function count_items() {
		return count( $this->items );
	}

	/**
	 * Add a new product to the list and update the session
	 *
	 * @param  int | WC_Product $product
	 * @return int | bool TRUE if successful, FALSE otherwise
	 */
	public function add_item( $product ) {
		$product_id = is_a( $product, 'WC_Product' ) ? $product->get_id() : $product;
		$key        = Helper::generate_item_key( $product_id );

		if ( ! $this->has_item( $product ) ) {
			$this->items[ $key ] = $product_id;

			// Update the session.
			$this->update();

			do_action( 'wcboost_products_compare_product_added', $product_id, $this );

			return true;
		}

		return false;
	}

	/**
	 * Remove a product from the list.
	 *
	 * @param string $key
	 *
	 * @return int|bool The removed product ID if successful, FALSE otherwise.
	 */
	public function remove_item( $key ) {
		if ( array_key_exists( $key, $this->items ) ) {
			$product_id = $this->items[ $key ];
			unset( $this->items[ $key ] );

			$this->update();

			do_action( 'wcboost_products_compare_product_removed', $product_id, $this );

			return $product_id;
		}

		return false;
	}

	/**
	 * Empty the list.
	 * Also reset the ID to create a new list.
	 *
	 * @param  bool $reset_db Reset data in the database
	 * @return void
	 */
	public function empty( $reset_db = false ) {
		$this->items = [];

		if ( $reset_db ) {
			$this->delete();
		}

		do_action( 'wcboost_products_compare_list_emptied', $reset_db, $this );
	}

	/**
	 * Check if a product exist in the list
	 *
	 * @param  int | \WC_Product $product
	 * @return bool
	 */
	public function has_item( $product ) {
		$product_id = is_a( $product, 'WC_Product' ) ? $product->get_id() : $product;

		return in_array( $product_id, $this->items );
	}

	/**
	 * Check if the list is empty
	 *
	 * @return bool
	 */
	public function is_empty() {
		return $this->count_items() ? false : true;
	}

	/**
	 * Get the hash based on list contents.
	 *
	 * @since 1.0.6
	 *
	 * @return string
	 */
	public function get_hash() {
		$hash = $this->get_id() ? md5( wp_json_encode( $this->get_items() ) . $this->count_items() ) : '';

		return apply_filters( 'wcboost_products_compare_hash', $hash, $this );
	}

	/**
	 * Get the list contents for session
	 *
	 * @since 1.0.6
	 * @return array
	 */
	public function get_list_for_session() {
		return [
			'id'    => $this->id,
			'items' => array_values( $this->items ),
		];
	}

	/**
	 * Update the session.
	 * Just update the product ids to the session.
	 *
	 * @return void
	 */
	private function update() {
		// Initialize the customer session if it is not already initialized.
		if ( WC()->session && ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		if ( $this->id ) {
			WC()->session->set(
				self::SESSION_KEY,
				$this->get_list_for_session()
			);
		}
	}

	/**
	 * Delete the list data from the database.
	 *
	 * @return void
	 */
	private function delete() {
		// Initialize the customer session if it is not already initialized.
		if ( WC()->session && WC()->session->has_session() ) {
			WC()->session->set( self::SESSION_KEY, null );
		}
	}

	/**
	 * Get the persistent list from the database.
	 *
	 * @since  1.0.6
	 * @return array
	 */
	private function get_saved_list() {
		$saved = [
			'id'    => '',
			'items' => [],
		];

		if ( apply_filters( 'wcboost_products_compare_persistent_enabled', true ) ) {
			$saved_list = get_user_meta( get_current_user_id(), '_wcboost_products_compare_' . get_current_blog_id(), true );

			if ( isset( $saved_list['items'] ) ) {
				$saved['items'] = array_filter( (array) $saved_list['items'] );
			}

			if ( isset( $saved_list['id'] ) ) {
				$saved['id'] = $saved_list['id'];
			}
		}

		return $saved;
	}

	/**
	 * Update persistent list
	 *
	 * @since 1.0.6
	 * @return void
	 */
	public function update_persistent_list() {
		if ( $this->get_id() && get_current_user_id() && apply_filters( 'wcboost_products_compare_persistent_enabled', true ) ) {
			update_user_meta(
				get_current_user_id(),
				'_wcboost_products_compare_' . get_current_blog_id(),
				$this->get_list_for_session()
			);
		}
	}

	/**
	 * Delete the persistent list permanently
	 *
	 * @since 1.0.6
	 * @return void
	 */
	public function delete_persistent_list() {
		if ( $this->get_id() && get_current_user_id() && apply_filters( 'wcboost_products_compare_persistent_enabled', true ) ) {
			delete_user_meta( get_current_user_id(), '_wcboost_products_compare_' . get_current_blog_id() );
		}
	}

	/**
	 * Will set cookies if needed and when possible.
	 *
	 * Headers are only updated if headers have not yet been sent.
	 *
	 * @since 1.0.6
	 * @return void
	 */
	public function maybe_set_cookies() {
		if ( headers_sent() || ! did_action( 'wp_loaded' ) ) {
			return;
		}

		if ( ! $this->is_empty() ) {
			$this->set_cookies( true );
		} else {
			$this->set_cookies( false );
		}
	}

	/**
	 * Set the comparison cookie
	 *
	 * @since 1.0.6
	 *
	 * @param  bool $set Should the cookie be set or unset.
	 *
	 * @return void
	 */
	private function set_cookies( $set = true )  {
		if ( $set ) {
			wc_setcookie( 'wcboost_compare_hash', $this->get_hash() );
		} else {
			wc_setcookie( 'wcboost_compare_hash', '', time() - HOUR_IN_SECONDS );
			unset( $_COOKIE['wcboost_compare_hash'] );
		}
	}
}
