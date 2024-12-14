<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Wishlist Query class
 */
class Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	private $query_vars = [];

	/**
	 * The wishlist instance.
	 *
	 * @var \WCBoost\Wishlist\Wishlist
	 */
	public $wishlist;

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_wishlist_page_rewrite_rules' ] );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		}

		$this->query_vars = [
			'wishlist_token' => 'wishlist_token',
			'edit-wishlist'  => get_option( 'wcboost_wishlist_edit_endpoint', 'edit-wishlist' ),
		];
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Add rewrite rules for wishlist page.
	 */
	public function add_wishlist_page_rewrite_rules() {
		// Use get_option to ensure always get the page of the default language (if multilingual is enabled).
		$wishlist_page_id = get_option( 'wcboost_wishlist_page_id' );

		if ( empty( $wishlist_page_id ) ) {
			return;
		}

		$wishlist_page      = get_post( $wishlist_page_id );
		$wishlist_page_slug = $wishlist_page ? urldecode( $wishlist_page->post_name ) : false;

		if ( empty( $wishlist_page_slug ) ) {
			return;
		}

		$this->add_rewrite_rules( $wishlist_page_slug );
	}

	/**
	 * Add rewrite rules for wishlists
	 *
	 * @param  string $base Page slug
	 * @return void
	 */
	public function add_rewrite_rules( $base ) {
		if ( empty( $base ) || ! is_string( $base ) ) {
			return;
		}

		$query_vars = $this->get_query_vars();

		// Does not support the 'wishlist_token' query var.
		if ( isset( $query_vars['wishlist_token'] ) ) {
			unset( $query_vars['wishlist_token'] );
		}

		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( empty( $var ) ) {
				continue;
			}

			add_rewrite_rule( '^' . $base . '/' . $var . '(/(.*))?/?$', 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]&' . $key . '=$matches[2]', 'top' );
		}

		add_rewrite_rule( '^' . $base . '(/(.*))?/page/([0-9]{1,})/?$', 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]&paged=$matches[3]', 'top' );
		add_rewrite_rule( '^' . $base . '(/(.*))?/?$', 'index.php?pagename=' . $base . '&wishlist_token=$matches[2]', 'top' );
	}

	/**
	 * Add public query vars for wishlist page.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Get the default wishlist of the current user
	 */
	protected function read_default_wishlist() {
		$default_wishlist_id = \WC_Data_Store::load( 'wcboost_wishlist' )->get_default_wishlist_id();

		if ( $default_wishlist_id ) {
			$this->wishlist = new Wishlist( $default_wishlist_id );
		}

		if ( empty( $this->wishlist ) ) {
			$this->wishlist = new Wishlist();
			$this->wishlist->set_is_default( true );
		}
	}

	/**
	 * Get wishlist instance.
	 * If no wishlist ID is passed, the default wishlist will be returned.
	 *
	 * @param int|string $wishlist_id Wishlist id or token
	 * @return \WCBoost\Wishlist\Wishlist
	 */
	public function get_wishlist( $wishlist_id = 0 ) {
		// Ensure the default wishlist is always exists.
		if ( empty( $this->wishlist ) ) {
			$this->read_default_wishlist();
		}

		if ( ! $wishlist_id ) {
			return $this->wishlist;
		}

		if ( $this->wishlist->get_wishlist_id() == $wishlist_id ) {
			return $this->wishlist;
		}

		return new Wishlist( $wishlist_id );
	}

	/**
	 * Get all wishlits of current user
	 *
	 * @return array
	 */
	public function get_user_wishlists() {
		$wishlist_ids = \WC_Data_Store::load( 'wcboost_wishlist' )->get_wishlist_ids();
		$wishlists    = [];

		while ( count( $wishlist_ids ) ) {
			$id          = array_pop( $wishlist_ids );
			$wishlists[] = new Wishlist( $id );
		}

		return $wishlists;
	}

	/**
	 * Get the wishlish endpoint URL
	 *
	 * @param string $endpoint
	 * @param string $value
	 *
	 * @return string
	 */
	public function get_endpoint_url( $endpoint, $value = '' ) {
		$wishlist_url = wc_get_page_permalink( 'wishlist' );
		$query_vars = $this->get_query_vars();
		$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $wishlist_url, '?' ) ) {
				$query_string = '?' . wp_parse_url( $wishlist_url, PHP_URL_QUERY );
				$wishlist_url = current( explode( '?', $wishlist_url ) );
			} else {
				$query_string = '';
			}

			$url = trailingslashit( $wishlist_url );

			if ( $value ) {
				$url .= 'wishlist-token' == $endpoint || 'wishlist_token' == $endpoint ? user_trailingslashit( $value ) : trailingslashit( $endpoint ) . user_trailingslashit( $value );
			} else {
				$url .= user_trailingslashit( $endpoint );
			}

			$url .= $query_string;
		} else {
			$url = add_query_arg( $endpoint, $value, $wishlist_url );
		}

		return apply_filters( 'wcboost_wishlist_get_endpoint_url', $url, $endpoint, $value, $this );
	}
}
