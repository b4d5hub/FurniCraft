<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles the rewrite rules.
 *
 */
Class SliceWP_Rewrite_Rules {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
		add_filter( 'redirect_canonical', array( $this, 'prevent_homepage_redirects' ), 0, 2 );

	}


	/** 
	 * Adds the affiliate keyword endpoint in the URL.
	 * 
	 */
	function rewrite_rules() {

		// Get the affiliate keyword.
		$keyword = slicewp_get_setting( 'affiliate_keyword' );

		// Get the taxonomies.
		$taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'objects' );

		// Add rewrite rules for taxonomies.
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) {

			if ( is_array( $taxonomy->rewrite ) && ! empty( $taxonomy->rewrite['slug'] ) ) {

				add_rewrite_rule( $taxonomy->rewrite['slug'] . '\/(.+?)\/' . $keyword . '(/(.*))?/?$', 'index.php?' . $taxonomy_slug . '=$matches[1]&' . $keyword . '=$matches[3]', 'top' );
				
			}
			
		}

		// Rewrite the endpoint.
		add_rewrite_endpoint( $keyword, EP_ROOT | EP_PERMALINK | EP_PAGES | EP_CATEGORIES | EP_TAGS | EP_SEARCH | EP_ALL_ARCHIVES, false );

	}


	/**
	 * Flush rewrite rules if requested.
	 * 
	 */
	function maybe_flush_rewrite_rules() {

		// Check if we have to flush rewrite rules.
		if ( get_option( 'slicewp_flush_rewrite_rules') ) {

			// Flush rewrite rules.
			flush_rewrite_rules();

			delete_option( 'slicewp_flush_rewrite_rules' );

		}

	}


	/**
	 * Prevents homepage redirects in case the requested url contains the friendly affiliate endpoint.
	 * 
	 * @param string $redirect_url
	 * @param string $requested_url
	 * 
	 * @return string
	 * 
	 */
	function prevent_homepage_redirects( $redirect_url, $requested_url ) {

		// Check if we are on the homepage.
		if ( ! is_front_page() ) {
			return $redirect_url;
		}

		// Get the affiliate keyword.
		$keyword = slicewp_get_setting( 'affiliate_keyword' );

		// Check if the requested url contains the affiliate keyword.
		if ( strpos( $requested_url, '/' . $keyword ) !== false ) {

			return $requested_url;

		}

		return $redirect_url;

	}

}

new SliceWP_Rewrite_Rules();