<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Wishlist Data
 */
class Wishlist extends \WC_Data {

	/**
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = [
		'wishlist_id'    => '',
		'wishlist_title' => '',
		'wishlist_slug'  => '',
		'wishlist_token' => '',
		'description'    => '',
		'menu_order'     => 0,
		'status'         => 'shared',
		'user_id'        => 0,
		'session_id'     => '',
		'date_created'   => '',
		'date_expires'   => '',
		'is_default'     => false,
	];

	/**
	 * Wishlist items
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Wishlist items that will be removed
	 *
	 * @var array
	 */
	protected $removing_items = [];

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'wcboost_wishlist';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'wishlists';

	/**
	 * Wishlist constructor. Loads wishlist data.
	 *
	 * @param mixed $data Wishlist data, object, ID or token.
	 */
	public function __construct( $data = '' ) {
		parent::__construct( $data );

		$this->data_store = \WC_Data_Store::load( 'wcboost_wishlist' );

		// If we already have a wishlist object, read it again.
		if ( $data instanceof self ) {
			$this->set_id( absint( $data->get_wishlist_id() ) );
			$this->read_object_from_database();
			return;
		}

		// Set the data manually.
		if ( is_array( $data ) ) {
			$this->read_manual_data( $data );
			return;
		}

		// Try to load wishlist using ID or token.
		if ( is_int( $data ) && $data ) {
			$this->set_id( $data );
			$this->set_wishlist_id( $data );
		} elseif ( ! empty( $data ) && is_string( $data ) ) {
			$this->set_wishlist_token( $data );
		} else {
			$this->read_new_data();
			$this->set_object_read( true );
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 */
	protected function read_object_from_database() {
		if ( $this->get_wishlist_id() <= 0 && empty( $this->get_wishlist_token() ) ) {
			return;
		}

		try {
			$this->data_store->read( $this );
			$this->data_store->read_items( $this );
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Error reading wishlist: ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Developers can programmatically return wishlists.
	 *
	 * @param array $data Array of wishlist properties.
	 */
	protected function read_manual_data( $data ) {
		if ( ! empty( $data['expiry_date'] ) && empty( $data['date_expires'] ) ) {
			$data['date_expires'] = $data['expiry_date'];
		}

		$this->set_props( $data );
		$this->set_id( 0 );

		if ( $this->get_wishlist_id() > 0 ) {
			$this->data_store->read_items( $this );
		}
	}

	/**
	 * Set the data for new wishlist
	 */
	protected function read_new_data() {
		$this->set_user_id( get_current_user_id() );
		$this->set_date_created( time() );

		if ( ! is_user_logged_in() ) {
			$this->set_date_expires( strtotime( '+30 days' ) );
			$this->set_session_id( $this->data_store->generate_session_id() );
		}
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
	 * Set wishlist title
	 *
	 * @param string $title
	 */
	public function set_wishlist_title( $title ) {
		$this->set_prop( 'wishlist_title', $title );
	}

	/**
	 * Set wishlist slug
	 *
	 * @param string $slug
	 */
	public function set_wishlist_slug( $slug ) {
		$this->set_prop( 'wishlist_slug', $slug );
	}

	/**
	 * Set wishlist token
	 *
	 * @param string $token
	 */
	public function set_wishlist_token( $token ) {
		$this->set_prop( 'wishlist_token', (string) $token );
	}

	/**
	 * Set wishlist description
	 *
	 * @param string $description
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', $description );
	}

	/**
	 * Set menu order
	 *
	 * @param int $token
	 */
	public function set_menu_order( $order ) {
		$this->set_prop( 'menu_order', absint( $order ) );
	}

	/**
	 * Set wishlist status
	 *
	 * @param string $status The wishlist status "shared", "private", "publish" or "trash".
	 */
	public function set_status( $status ) {
		if ( in_array( $status, [ 'shared', 'private', 'publish', 'trash' ] ) ) {
			$this->set_prop( 'status', (string) $status );
		}
	}

	/**
	 * Set wishlist user id
	 *
	 * @param int $user_id The ID of user who created the wishlist
	 */
	public function set_user_id( $user_id ) {
		$this->set_prop( 'user_id', absint( $user_id ) );
	}

	/**
	 * Set wishlist user id
	 *
	 * @param string $session_id
	 */
	public function set_session_id( $session_id ) {
		$this->set_prop( 'session_id', $session_id );
	}

	/**
	 * Set created date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set expiration date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 */
	public function set_date_expires( $date ) {
		$this->set_date_prop( 'date_expires', $date );
	}

	/**
	 * Set wishlist title
	 *
	 * @param bool $title
	 */
	public function set_is_default( $is_default ) {
		$this->set_prop( 'is_default', (bool) $is_default );
	}

	/**
	 * Get wishlist title
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_wishlist_id( $context = 'view' ) {
		return intval( $this->get_prop( 'wishlist_id', $context ) );
	}

	/**
	 * Get wishlist title
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_wishlist_title( $context = 'view' ) {
		return $this->get_prop( 'wishlist_title', $context );
	}

	/**
	 * Get wishlist slug
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_wishlist_slug( $context = 'view' ) {
		return $this->get_prop( 'wishlist_slug', $context );
	}

	/**
	 * Get wishlist token
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_wishlist_token( $context = 'view' ) {
		return trim( $this->get_prop( 'wishlist_token', $context ) );
	}

	/**
	 * Get wishlist description
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Get menu order
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_menu_order( $context = 'view' ) {
		return intval( $this->get_prop( 'menu_order', $context ) );
	}

	/**
	 * Get wishlist status
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get user id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {
		return intval( $this->get_prop( 'user_id', $context ) );
	}

	/**
	 * Get session id
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_session_id( $context = 'view' ) {
		return $this->get_prop( 'session_id', $context );
	}

	/**
	 * Get created date
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get expire date
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL
	 */
	public function get_date_expires( $context = 'view' ) {
		return $this->get_prop( 'date_expires', $context );
	}

	/**
	 * Get value of the prop is_default
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return bool
	 */
	public function get_is_default( $context = 'view' ) {
		return \wc_string_to_bool( $this->get_prop( 'is_default', $context ) );
	}

	/**
	 * Check if current wishlist is default
	 *
	 * @return bool
	 */
	public function is_default() {
		return $this->get_is_default();
	}

	/**
	 * Move the wishlist to trash by updating the status and set the expires date.
	 *
	 * @return bool
	 */
	public function trash() {
		if ( $this->data_store ) {
			do_action( 'wcboost_wishlist_move_to_trash', $this );

			$this->set_status( 'trash' );
			$this->set_date_expires( strtotime( '+30 days' ) );
			$this->save();

			do_action( 'wcboost_wishlist_moved_to_trash', $this );

			return true;
		}

		return false;
	}

	/**
	 * Restore trashed wishlist
	 *
	 * @return bool
	 */
	public function restore() {
		if ( $this->data_store && 'trash' == $this->get_status() ) {
			do_action( 'wcboost_wishlist_restore', $this );

			$this->set_status( 'private' );
			$this->set_date_expires( '' );
			$this->save();

			do_action( 'wcboost_wishlist_restored', $this );

			return true;
		}

		return false;
	}

	/**
	 * Add a new item to the wishlist
	 *
	 * @param Wishlist_Item $item Item to be added
	 *
	 * @return bool|WP_Error Returns TRUE on success, FALSE on failure. WP_Error on invalid.
	 */
	public function add_item( $item ) {
		if ( ! $item instanceof Wishlist_Item || ! $item->get_product_id() ) {
			return false;
		}

		$product = $item->get_product();

		if ( ! $product || ! $product->exists() || ( 'publish' !== $product->get_status() && ! current_user_can( 'edit_post', $product->get_id() ) ) ) {
			$item->trash();

			if ( ! $product || ! $product->exists() ) {
				$message = esc_html__( 'A product has been removed from your wishlist because it does not exist anymore.', 'wcboost-wishlist' );
			} else {
				/* translators: %s product name */
				$message = sprintf( esc_html__( 'The product "%s" has been removed from your wishlist because it can no longer be purchased.', 'wcboost-wishlist' ), $product->get_title() );
			}

			wc_add_notice( $message, 'error' );
			return false;
		}

		if ( $this->has_item( $item ) ) {
			return new \WP_Error( 'item_exists', esc_html__( 'This item already exists', 'wcboost-wishlist' ) );
		}

		// Update the item data.
		$item->set_wishlist_id( $this->get_wishlist_id() );

		if ( ! $item->get_id() ) {
			$item->set_date_added( time() );
		}

		$this->items[ $item->get_item_key() ] = $item;

		// Save to database.
		if ( ! $item->get_id() ) {
			$item->save();
			$this->save();
		}

		if ( ! $this->data_store->is_reading() ) {
			do_action( 'wcboost_wishlist_add_item', $item );
		}

		return true;
	}

	/**
	 * Remove a wishlist item
	 *
	 * @param string $item_key The item key or item object
	 * @return bool|WP_Error Returns TRUE on success, WP_Error on invalid.
	 */
	public function remove_item( $item_key ) {
		if ( ! $this->can_edit() ) {
			return new \WP_Error( 'no_permission', esc_html__( 'You are not allowed to edit the wishlist', 'wcboost-wishlist' ) );
		}

		if ( ! $this->has_item( $item_key ) ) {
			return new \WP_Error( 'not_exists', esc_html__( 'Invalid wishlist item', 'wcboost-wishlist' ) );
		}

		$item = $this->get_item( $item_key );

		do_action( 'wcboost_wishlist_remove_item', $item );

		if ( $item->trash() ) {
			$this->add_item_to_trash( $item );
			$this->save();

			do_action( 'wcboost_wishlist_removed_item', $item );

			return true;
		}

		return false;
	}

	/**
	 * Restore an item
	 *
	 * @param string|Wishlist_Item $item
	 * @return bool|WP_Error Returns TRUE on success, WP_Error on invalid.
	 */
	public function restore_item( $item ) {
		if ( ! $this->can_edit() ) {
			return new \WP_Error( 'no_permission', esc_html__( 'You are not allowed to edit the wishlist', 'wcboost-wishlist' ) );
		}

		$item_key = is_string( $item ) ? $item : $item->get_item_key();

		if ( ! array_key_exists( $item_key, $this->removing_items ) ) {
			return new \WP_Error( 'not_exists', esc_html__( 'Invalid wishlist item', 'wcboost-wishlist' ) );
		}

		$item = $this->removing_items[ $item_key ];

		do_action( 'wcboost_wishlist_restore_item', $item );

		if ( $item->restore() ) {
			$this->items[ $item_key ] = $item;
			$this->remove_item_from_trash( $item );
			$this->save();

			do_action( 'wcboost_wishlist_restored_item', $item );

			return true;
		}

		return false;
	}

	/**
	 * Check if an item exists in the wishlist
	 *
	 * @param Wishlist_Item|string $item Item object or item key
	 * @return bool
	 */
	public function has_item( $item ) {
		if ( ! is_string( $item ) && ! is_a( $item, '\WCBoost\Wishlist\Wishlist_Item' ) ) {
			return false;
		}

		$item_key = is_string( $item ) ? $item : $item->get_item_key();

		return array_key_exists( $item_key, $this->items );
	}

	/**
	 * Get item object
	 *
	 * @param string $item_key Item key
	 * @return Wishlist_Item|bool
	 */
	public function get_item( $item_key ) {
		if ( ! $this->has_item( $item_key ) ) {
			return false;
		}

		return $this->items[ $item_key ];
	}

	/**
	 * Get the list of items
	 *
	 * @return Wishlist_Item[]
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Count items in the wishlist
	 *
	 * @return int
	 */
	public function count_items() {
		return count( $this->items );
	}

	/**
	 * Test if the wishlist is empty
	 *
	 * @return bool
	 */
	public function is_empty() {
		return $this->count_items() > 0 ? false : true;
	}

	/**
	 * Check if current user can edit the wishlist
	 *
	 * @return bool
	 */
	public function can_edit() {
		if ( ! $this->get_id() ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			return ( $user_id && $user_id === $this->get_user_id() );
		}

		if ( $this->get_session_id() ) {
			return ( Session::get_session_id() === $this->get_session_id() );
		}

		return false;
	}

	/**
	 * Check if the wishlist is public
	 *
	 * @return bool
	 */
	public function is_public() {
		return $this->get_status() === 'publish';
	}

	/**
	 * Check if the wishlist is shareable
	 *
	 * @return bool
	 */
	public function is_shareable() {
		return in_array( $this->get_status(), ['publish', 'shared'] );
	}

	/**
	 * Save should create or update based on object existence.
	 * Also set the session id for guests.
	 */
	public function save() {
		parent::save();

		if ( ! is_user_logged_in() ) {
			Session::set_session_id( $this->get_session_id() );
		}
	}

	/**
	 * Save wishlist items
	 *
	 * @return int
	 */
	public function save_items() {
		if ( ! $this->data_store ) {
			return $this->get_id();
		}

		foreach ( $this->items as $item ) {
			$item->save();
		}

		return $this->get_id();
	}

	/**
	 * Add an item to trash
	 *
	 * @param Wishlist_Item $item
	 * @return bool
	 */
	public function add_item_to_trash( $item ) {
		if ( ! $item instanceof Wishlist_Item ) {
			return false;
		}

		// Ensure the item status is correct.
		$item->set_status( 'trash' );
		$this->removing_items[ $item->get_item_key() ] = $item;

		if ( $this->has_item( $item ) ) {
			unset( $this->items[ $item->get_item_key() ] );
			$this->save();
		}

		return true;
	}

	/**
	 * Remove an item from the trash
	 *
	 * @param Wishlist_Item $item
	 * @return bool
	 */
	public function remove_item_from_trash( $item ) {
		if ( ! $item instanceof Wishlist_Item ) {
			return false;
		}

		unset( $this->removing_items[ $item->get_item_key() ] );

		return true;
	}

	/**
	 * Empty the wishlist
	 *
	 * @since 1.1.4
	 *
	 * @return void
	 */
	public function empty() {
		foreach ( $this->items as $item ) {
			$item->trash();
			$this->add_item_to_trash( $item );
		}

		$this->items = [];
		$this->save();

		do_action( 'wcboost_wishlist_emptied', $this );
	}

	/**
	 * Check if the wishlist has a product
	 *
	 * @since 1.1.4
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function has_product( $product_id ) {
		foreach ( $this->items as $item ) {
			if ( $item->get_product_id() === $product_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the public URL of the wishlist
	 *
	 * @return string
	 */
	public function get_public_url() {
		$url = Plugin::instance()->query->get_endpoint_url( 'wishlist-token', $this->get_wishlist_token() );

		return apply_filters( 'wcboost_wishlist_public_url', $url, $this );
	}

	/**
	 * Get the edit URL of the wishlist
	 *
	 * @return string
	 */
	public function get_edit_url() {
		$url = Plugin::instance()->query->get_endpoint_url( 'edit-wishlist', $this->get_wishlist_token() );

		return apply_filters( 'wcboost_wishlist_edit_url', $url, $this );
	}

	/**
	 * Get unique hash key for the wishlist.
	 * For all temporary wishlists, the Key is the same.
	 *
	 * @since 1.1.1
	 * @since 1.1.2 Always generate the hash key. A fixed key is generated for temporary wishlists.
	 *
	 * @return string
	 */
	public function get_hash_key() {
		$hash_key = '';

		if ( $this->get_id() ) {
			$hash_key = md5( get_current_blog_id() . '_' . $this->get_id() . '_' . $this->get_wishlist_token() );
		} else {
			// Generate a fixed hash for temporary wishlists.
			$hash_key = md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() );
		}

		return $hash_key;
	}

	/**
	 * Get hash content for the wishlist.
	 * For all temporary wishlists, the content is the same because all params are the same,
	 * which are: { wishlist_token: '', items: [], count: 0 }
	 *
	 * @since 1.1.1
	 * @since 1.1.2 Always generate hash content
	 *
	 * @return string
	 */
	public function get_hash_content() {
		$hash = md5( $this->get_wishlist_token() . wp_json_encode( $this->get_items() ) . $this->count_items() );

		return apply_filters( 'wcboost_wishlist_hash', $hash, $this );
	}

	/**
	 * Get hash for the wishlist.
	 * Generated by combining the hash_key and hash_content.
	 *
	 * @since 1.1.1
	 *
	 * @return string Wishlist hash in format HASH_KEY::HASH_CONTENTS
	 */
	public function get_hash() {
		return $this->get_hash_key() . '::' . $this->get_hash_content();
	}

	/**
	 * Merge items from another wishlist
	 *
	 * @since 1.1.4
	 *
	 * @param Wishlist $wishlist
	 *
	 * @return int Number of items merged
	 */
	public function merge( $wishlist ) {
		$this->data_store->set_is_reading( true );

		$merged_count = 0;

		foreach ( $wishlist->get_items() as $item ) {
			$adding_product = $item->get_product();
			$merging_item   = new Wishlist_Item( $adding_product );

			$merged = $this->add_item( $merging_item );

			if ( $merged && ! is_wp_error( $merged ) ) {
				$merged_count++;
			}
		}

		$this->data_store->set_is_reading( false );

		return $merged_count;
	}
}
