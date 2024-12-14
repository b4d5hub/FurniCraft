<?php
namespace WCBoost\ProductsCompare\Analytics;

use WCBoost\ProductsCompare\Analytics\Data;

/**
 * Monitor comparison data
 */
class Tracker {

	/**
	 * The single instance of the class.
	 * @var Tracker
	 */
	protected static $_instance = null;

	/**
	 * Main instance.
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @static
	 * @return Tracker
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'add_to_cart_url' ] );
		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_link' ] );

		add_action( 'wcboost_products_compare_product_added', [ $this, 'track_add_to_compare' ], 10, 2 );
		add_action( 'wcboost_products_compare_product_removed', [ $this, 'track_remove_from_compare' ], 10, 2 );
		add_action( 'woocommerce_add_to_cart', [ $this, 'track_add_to_cart' ], 10, 2 );
	}

	/**
	 * Add the source param to the add_to_cart URL.
	 *
	 * @param  string $url
	 *
	 * @return string
	 */
	public function add_to_cart_url( $url ) {
		if ( wc_get_loop_prop( 'is_compare' ) ) {
			$url = add_query_arg( 'wcboost_source', 'compare', $url );
		}

		return $url;
	}

	/**
	 * Add the source attribute to the add_to_cart link.
	 *
	 * @param  string $link
	 *
	 * @return string
	 */
	public function add_to_cart_link( $link ) {
		if ( wc_get_loop_prop( 'is_compare' ) ) {
			$link = str_replace( '<a ', '<a data-wcboost_source="compare" ', $link );
		}

		return $link;
	}

	/**
	 * Track the product that is added to the comparison list.
	 *
	 * @param  int $product_id
	 * @param  Compare_List $list
	 *
	 * @return void
	 */
	public function track_add_to_compare( $product_id, $list ) {
		if ( ! $this->can_track() ) {
			return;
		}

		// Track comparison counts.
		$this->update_product_compare_count( $product_id, 1 );

		$this->update_product_compare_time( $product_id, time() );

		$this->update_user_compare_data( [
			'last_compare' => time(),
			'products'     => [
				[
					'id'      => $product_id,
					'count'   => 1,
					'updated' => time(),
				]
			],
		] );

		// Update compared data.
		$items = $list->get_items();
		$key   = array_search( $product_id, $items );
		unset( $items[ $key ] );

		// - Update compared data for existing products.
		foreach ( $items as $item ) {
			$this->update_product_compare_data(
				$item,
				[
					'products' => [
						[
							'id'      => $product_id,
							'count'   => 1,
							'updated' => time(),
						],
					],
				]
			);
		}

		// - Update compared data for the current product.
		$item_ids   = array_values( $items );
		$items_data = array_map( function( $item_id ) {
			return [
				'id'      => $item_id,
				'count'   => 1,
				'updated' => time(),
			];
		}, $item_ids );

		$this->update_product_compare_data(
			$product_id,
			[
				'products' => $items_data,
			]
		);
	}

	/**
	 * Track the product that has been removed from the list
	 *
	 * @param  int $product_id
	 * @param  Compare_List $list
	 *
	 * @return void
	 */
	public function track_remove_from_compare( $product_id, $list ) {
		if ( ! $this->can_track() ) {
			return;
		}

		// Track comparison counts.
		$this->update_product_compare_count( $product_id, -1 );

		// Track user's selected products.
		$this->update_user_compare_data( [
			'products' => [
				[
					'id'    => $product_id,
					'count' => -1,
				]
			],
		] );

		// Update compared data.
		$items = $list->get_items();

		// - Update simlar items for existing products.
		foreach ( $items as $item ) {
			$this->update_product_compare_data(
				$item,
				[
					'products' => [
						[
							'id'    => $product_id,
							'count' => -1,
						],
					]
				]
			);
		}

		// - Update compared data for the current product.
		$item_ids   = array_values( $items );
		$items_data = array_map( function( $item_id ) {
			return [
				'id'    => $item_id,
				'count' => -1,
			];
		}, $item_ids );

		$this->update_product_compare_data(
			$product_id,
			[
				'products' => $items_data
			]
		);
	}

	/**
	 * Track data of the selected products.
	 *
	 * @param  string $cart_item_key
	 * @param  int    $product_id
	 *
	 * @return void
	 */
	public function track_add_to_cart( $cart_item_key, $product_id ) {
		if ( ! $this->can_track() ) {
			return;
		}

		$source = ! empty( $_REQUEST['wcboost_source'] ) ? $_REQUEST['wcboost_source'] : '';

		if ( 'compare' != $source ) {
			return;
		}

		$this->update_add_to_cart_count( $product_id );

		$this->update_user_compare_data( [
			'last_addtocart' => time(),
			'products'       => [
				[
					'id'        => $product_id,
					'addtocart' => 1,
				]
			],
		] );

		$items = \WCBoost\ProductsCompare\Plugin::instance()->list->get_items();
		$key   = array_search( $product_id, $items );
		unset( $items[ $key ] );

		$item_ids   = array_values( $items );
		$items_data = array_map( function( $item_id ) {
			return [
				'id'   => $item_id,
				'lose' => 1,
			];
		}, $item_ids );

		$this->update_product_compare_data( $product_id, [
			'products' => $items_data,
		] );
	}

	/**
	 * Update the product compare count.
	 * This counter is increased once the product is added to a compare list,
	 * and is decreased when the product is removed.
	 *
	 * @param  int $product_id
	 * @param  int $amount
	 *
	 * @return void
	 */
	protected function update_product_compare_count( $product_id, $amount = 1 ) {
		$amount = intval( $amount );
		$count  = Data::get_product_compare_count( $product_id );
		$count  = max( 0, $count + $amount );

		// Update avg counter.
		update_post_meta( $product_id, Data::PRODUCT_COMPARE_COUNT, $count );

		if ( $amount < 0 ) {
			$removed = Data::get_product_removal_count( $product_id );
			$removed = max( 0, $removed + $amount );

			// Update removal counter.
			update_post_meta( $product_id, Data::PRODUCT_COMPARE_REMOVAL, $count );
		} else {
			$addition = Data::get_product_addition_count( $product_id );
			$addition = $addition + $amount;

			update_post_meta( $product_id, Data::PRODUCT_COMPARE_ADDITION, $addition );
		}
	}

	/**
	 * Update the last time a product was added to a compare list
	 *
	 * @param  int $product_id
	 * @param  int $time
	 *
	 * @return void
	 */
	protected function update_product_compare_time( $product_id, $time = 0 ) {
		$time = intval( $time );
		$time = $time ? $time : time();

		update_post_meta( $product_id, Data::PRODUCT_COMPARE_TIME, $time );
	}

	/**
	 * Update compared data for a given product.
	 * This data includes an array of products that have been compared with the given products.
	 *
	 * @param  int   $product_id The ID of main product to update compared data
	 * @param  array $items      The list of related products to update for main product
	 *
	 * @return void
	 */
	protected function update_product_compare_data( $product_id, $data ) {
		$product_data = Data::get_product_compare_data( $product_id );
		$product_data = $this->parse_data( $product_data, $data );

		update_post_meta( $product_id, Data::PRODUCT_COMPARE_DATA, $product_data );
	}

	/**
	 * Update user meta data
	 *
	 * @param  array $data
	 * @param  int   $user_id
	 *
	 * @return void
	 */
	protected function update_user_compare_data( $data, $user_id = 0 ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$user_data = Data::get_user_compare_data( $user_id );
		$user_data = $this->parse_data( $user_data, $data );

		update_user_meta( $user_id, Data::USER_COMPARE_DATA . get_current_blog_id(), $user_data );
	}

	/**
	 * Update counter of times a product was added to the shopping cart.
	 *
	 * @param  int $product_id
	 *
	 * @return void
	 */
	protected function update_add_to_cart_count( $product_id ) {
		$count = Data::get_product_add_to_cart_count( $product_id );

		update_post_meta( $product_id, Data::PRODUCT_CART_ADDITION, $count + 1 );
	}

	/**
	 * Parse data to update
	 *
	 * @param  array $current
	 * @param  array $update
	 *
	 * @return array
	 */
	private function parse_data( $current, $update ) {
		if ( ! empty( $update['products'] ) ) {
			$products = ! empty( $current['products'] ) ? $current['products'] : [];

			if ( empty( $products ) ) {
				$products = $update['products'];
			} else {
				foreach ( $update['products'] as $item_data ) {
					$index = array_search( $item_data['id'], array_column( $products, 'id' ) );

					if ( false !== $index ) {
						$products[ $index ] = $this->parse_product_data( $products[ $index ], $item_data );;
					} else {
						$products[] = $this->parse_product_data( [], $item_data );;
					}
				}
			}

			$current['products'] = $products;
			unset( $update['products'] );
		}

		if ( ! empty( $update ) ) {
			$current = array_merge( $current, $update );
		}

		return $current;
	}

	/**
	 * Parse tracking data for a single item
	 *
	 * @param  array $current
	 * @param  array $update
	 *
	 * @return array
	 */
	private function parse_product_data( $current, $update ) {
		if ( empty( $current ) ) {
			return $update;
		}

		// Compare counter.
		if ( isset( $update['count'] ) ) {
			$current_count = isset( $current['count'] ) ? intval( $current['count'] ) : 0;
			$current['count'] = max( 0, $current_count + intval( $update['count'] ) );
			unset( $update['count'] );
		}

		// Last update.
		if ( ! empty( $update['updated'] ) ) {
			$current['updated'] = $update['updated'];
			unset( $update['updated'] );
		}

		// Counter of how many times a product was added to the cart from the compare list.
		// Used for user tracking.
		if ( ! empty( $update['addtocart'] ) ) {
			$current_count = isset( $current['addtocart'] ) ? intval( $current['addtocart'] ) : 0;
			$current['addtocart'] = max( 0, $current_count + intval( $update['addtocart'] ) );
			unset( $update['addtocart'] );
		}

		// Counter of how many times a product was losed with the active product.
		// Used for product tracking.
		if ( ! empty( $update['lose'] ) ) {
			$current_count = isset( $current['lose'] ) ? intval( $current['lose'] ) : 0;
			$current['lose'] = max( 0, $current_count + intval( $update['lose'] ) );
			unset( $update['lose'] );
		}

		// Other properties.
		if ( ! empty( $update ) ) {
			$current = array_merge( $current, $update );
		}

		return $current;
	}

	/**
	 * Determine if it is possible to perform a tracking event
	 *
	 * @return bool
	 */
	public function can_track() {
		if ( current_user_can( 'administrator' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determine if the tracking option is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return wc_string_to_bool( get_option( 'wcboost_products_compare_tracking', 'yes' ) );
	}
}
