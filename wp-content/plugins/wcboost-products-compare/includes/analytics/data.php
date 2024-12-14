<?php
namespace WCBoost\ProductsCompare\Analytics;

/**
 * Data class for analytics
 */
class Data {

	const PRODUCT_COMPARE_COUNT = 'wcboost_compare_count';

	const PRODUCT_COMPARE_ADDITION = 'wcboost_compare_addition';

	const PRODUCT_COMPARE_REMOVAL = 'wcboost_compare_removal';

	const PRODUCT_COMPARE_TIME = 'wcboost_compare_time';

	const PRODUCT_COMPARE_DATA = 'wcboost_compare_data';

	const PRODUCT_CART_ADDITION = 'wcboost_compare_addtocart';

	const USER_COMPARE_DATA = 'wcboost_compare_data_';

	/**
	 * Get the compare count
	 *
	 * @param  int $product_id
	 *
	 * @return int The comparision count
	 */
	public static function get_product_compare_count( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return 0;
		}

		return intval( get_post_meta( $product_id, static::PRODUCT_COMPARE_COUNT, true ) );
	}

	/**
	 * Get number of times a product has been added to the compare list
	 *
	 * @param  int  $product_id
	 *
	 * @return int
	 */
	public static function get_product_addition_count( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return 0;
		}

		return intval( get_post_meta( $product_id, static::PRODUCT_COMPARE_ADDITION, true ) );
	}

	/**
	 * Get the number of times a product has been removed from the compare list
	 *
	 * @param  int  $product_id
	 *
	 * @return int
	 */
	public static function get_product_removal_count( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return 0;
		}

		return intval( get_post_meta( $product_id, static::PRODUCT_COMPARE_REMOVAL, true ) );
	}

	/**
	 * Get compared data of a product
	 *
	 * @param  int $product_id
	 *
	 * @return array
	 */
	public static function get_product_compare_data( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return [];
		}

		$compare_data = get_post_meta( $product_id, static::PRODUCT_COMPARE_DATA, true );
		$compare_data = $compare_data ? array_filter( $compare_data ) : [];

		return $compare_data;
	}

	/**
	 * Get user data for comparison
	 *
	 * @param  int  $user_id
	 *
	 * @return array
	 */
	public static function get_user_compare_data( $user_id = 0 ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		$default = [
			'last_compare'   => 0,
			'last_addtocart' => 0,
			'products'       => [],
		];

		if ( ! $user_id ) {
			return $default;
		}

		$data = (array) get_user_meta( $user_id, static::USER_COMPARE_DATA . get_current_blog_id(), true );
		$data = $data ? array_filter( $data ) : [];
		$data = wp_parse_args( $data, $default );

		return $data;
	}

	/**
	 * Get counter of times a product was added to cart from the compare list.
	 *
	 * @param  int  $product_id
	 *
	 * @return int
	 */
	public static function get_product_add_to_cart_count( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return 0;
		}

		return intval( get_post_meta( $product_id, static::PRODUCT_CART_ADDITION, true ) );
	}

	/**
	 * Get IDs of similar products for a given product
	 *
	 * @param  int  $product_id
	 *
	 * @return array
	 */
	public static function get_similar_product_ids( $product_id = 0 ) {
		$product_id = $product_id ? $product_id : get_the_ID();

		if ( ! $product_id ) {
			return [];
		}

		$compare_data = self::get_product_compare_data( $product_id );

		if ( empty( $compare_data ) || empty( $compare_data['products'] ) ) {
			return [];
		}

		// Sort compared products by count.
		usort( $compare_data['products'], function( $a, $b ) {
			return $b['count'] - $a['count'];
		} );

		$ids = array_column( $compare_data['products'], 'id' );

		return $ids;
	}
}
