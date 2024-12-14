<?php
namespace WCBoost\ProductsCompare;

/**
 * Helper class
 */
class Helper {

	/**
	 * Check if is the compare page.
	 *
	 * @return bool
	 */
	public static function is_compare_page() {
		$page_id = wc_get_page_id( 'compare' );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}

	/**
	 * Get SVG icon
	 *
	 * @param  string $icon Icon name
	 * @param  int    $size
	 * @return string
	 */
	public static function get_icon( $icon, $size = 24 ) {
		$svg = '';

		switch ( $icon ) {
			case 'spinner':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48C304 74.51 282.5 96 256 96C229.5 96 208 74.51 208 48C208 21.49 229.5 0 256 0C282.5 0 304 21.49 304 48zM304 464C304 490.5 282.5 512 256 512C229.5 512 208 490.5 208 464C208 437.5 229.5 416 256 416C282.5 416 304 437.5 304 464zM0 256C0 229.5 21.49 208 48 208C74.51 208 96 229.5 96 256C96 282.5 74.51 304 48 304C21.49 304 0 282.5 0 256zM512 256C512 282.5 490.5 304 464 304C437.5 304 416 282.5 416 256C416 229.5 437.5 208 464 208C490.5 208 512 229.5 512 256zM74.98 437C56.23 418.3 56.23 387.9 74.98 369.1C93.73 350.4 124.1 350.4 142.9 369.1C161.6 387.9 161.6 418.3 142.9 437C124.1 455.8 93.73 455.8 74.98 437V437zM142.9 142.9C124.1 161.6 93.73 161.6 74.98 142.9C56.24 124.1 56.24 93.73 74.98 74.98C93.73 56.23 124.1 56.23 142.9 74.98C161.6 93.73 161.6 124.1 142.9 142.9zM369.1 369.1C387.9 350.4 418.3 350.4 437 369.1C455.8 387.9 455.8 418.3 437 437C418.3 455.8 387.9 455.8 369.1 437C350.4 418.3 350.4 387.9 369.1 369.1V369.1z"/></svg>';
				break;

			case 'close':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg>';
				break;

			case 'plus':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>';
				break;

			case 'check':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>';
				break;

			case 'code-compare':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M320 488c0 9.5-5.6 18.1-14.2 21.9s-18.8 2.3-25.8-4.1l-80-72c-5.1-4.6-7.9-11-7.9-17.8s2.9-13.3 7.9-17.8l80-72c7-6.3 17.2-7.9 25.8-4.1s14.2 12.4 14.2 21.9v40h16c35.3 0 64-28.7 64-64V153.3C371.7 141 352 112.8 352 80c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V320c0 70.7-57.3 128-128 128H320v40zM456 80c0-13.3-10.7-24-24-24s-24 10.7-24 24s10.7 24 24 24s24-10.7 24-24zM192 24c0-9.5 5.6-18.1 14.2-21.9s18.8-2.3 25.8 4.1l80 72c5.1 4.6 7.9 11 7.9 17.8s-2.9 13.3-7.9 17.8l-80 72c-7 6.3-17.2 7.9-25.8 4.1s-14.2-12.4-14.2-21.9V128H176c-35.3 0-64 28.7-64 64V358.7c28.3 12.3 48 40.5 48 73.3c0 44.2-35.8 80-80 80s-80-35.8-80-80c0-32.8 19.7-61 48-73.3V192c0-70.7 57.3-128 128-128h16V24zM56 432c0 13.3 10.7 24 24 24s24-10.7 24-24s-10.7-24-24-24s-24 10.7-24 24z"/></svg>';
				break;

			case 'arrows':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M422.6 278.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L434.7 176H64c-17.7 0-32-14.3-32-32s14.3-32 32-32H434.7L377.4 54.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l112 112c12.5 12.5 12.5 32.8 0 45.3l-112 112zm-269.3 224l-112-112c-12.5-12.5-12.5-32.8 0-45.3l112-112c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3L141.3 336H512c17.7 0 32 14.3 32 32s-14.3 32-32 32H141.3l57.4 57.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0z"/></svg>';
				break;

			case 'square':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 32C419.3 32 448 60.65 448 96V416C448 451.3 419.3 480 384 480H64C28.65 480 0 451.3 0 416V96C0 60.65 28.65 32 64 32H384zM384 80H64C55.16 80 48 87.16 48 96V416C48 424.8 55.16 432 64 432H384C392.8 432 400 424.8 400 416V96C400 87.16 392.8 80 384 80z"/></svg>';
				break;

			case 'chevron-up':
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 105.4c12.5-12.5 32.8-12.5 45.3 0l192 192c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L256 173.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l192-192z"/></svg>';
				break;
		}

		if ( $svg ) {
			$svg = str_replace( '<svg', '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" role="image"', $svg );
		}

		return apply_filters( 'wcboost_products_compare_svg_icon', $svg, $icon );
	}

	/**
	 * Get compare icon for the button
	 *
	 * @param  bool $added
	 * @param  int  $size
	 * @return string
	 */
	public static function get_compare_icon( $added = false, $size = 24 ) {
		$icon = get_option( 'wcboost_products_compare_button_icon', false );

		if ( false === $icon ) {
			$icon = wc_get_theme_support( 'products_compare::button_icon', 'arrows' );
		}

		if ( ! $icon ) {
			return '';
		}

		$icon = $added ? 'check' : $icon;
		$svg  = self::get_icon( $icon, $size );
		$svg  = $svg ? $svg : ( $added ? self::get_icon( 'check', $size ) : self::get_icon( 'arrows', $size ) );

		return apply_filters( 'wcboost_products_compare_button_icon', $svg, $icon );
	}

	/**
	 * Get button text
	 *
	 * @param string $type
	 * @return string
	 */
	public static function get_button_text( $type = 'add' ) {
		$type = in_array( $type, [ 'add', 'remove', 'view' ] ) ? $type : 'add';
		$button_text = wp_parse_args( get_option( 'wcboost_products_compare_button_text', [] ), [
			'add'    => __( 'Compare', 'wcboost-products-compare' ),
			'remove' => __( 'Remove compare', 'wcboost-products-compare' ),
			'view'   => __( 'Browse compare', 'wcboost-products-compare' ),
		] );

		$text = array_key_exists( $type, $button_text ) ? $button_text[ $type ] : $button_text['add'];

		return apply_filters( 'wcboost_products_compare_button_' . $type . '_text', $text );
	}

	/**
	 * Get the URL of adding action
	 *
	 * @param  \WC_Product | int $product
	 * @return string
	 */
	public static function get_add_url( $product = false ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
		$product = $product ? $product : $GLOBALS['product'];
		$referer = is_feed() || is_404() ? $product->get_permalink() : '';
		$url     = add_query_arg( [ 'add_to_compare' => $product->get_id() ], $referer );

		return apply_filters( 'wcboost_products_compare_add_to_compare_url', $url, $product );
	}

	/**
	 * Get the URL of removing a product from the compare list
	 *
	 * @param \WC_Product $product
	 * @return string
	 */
	public static function get_remove_url( $product = false ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
		$product = $product ? $product : $GLOBALS['product'];
		$referer = is_feed() || is_404() ? $product->get_permalink() : '';
		$params  = [
			'remove_compare_item' => self::generate_item_key( $product ),
			'_wpnonce' => wp_create_nonce( 'wcboost-products-compare-remove-item' ),
		];

		if ( empty( $referer ) && self::is_compare_page() ) {
			if ( wc_get_page_id( 'compare' ) ) {
				$referer = wc_get_page_permalink( 'compare' );
			}

			if ( isset( $_REQUEST['popup'] ) ) {
				$params['_wp_http_referer'] = wc_get_page_permalink( 'compare' );
			}
		}

		$url = add_query_arg( $params, $referer );

		return apply_filters( 'wcboost_products_compare_remove_from_compare_url', $url, $product );
	}

	/**
	 * Get the URL of clearing the list
	 *
	 * @param  string $list_id
	 * @return string
	 */
	public static function get_clear_url( $list_id = false ) {
		$list_id = $list_id ? $list_id : Plugin::instance()->list->get_id();
		$referer = is_feed() || is_404() ? wc_get_page_permalink( 'compare' ) : '';
		$params  = [
			'clear_compare_list' => $list_id,
			'_wpnonce'           => wp_create_nonce( 'wcboost-products-compare-clear-list' ),
		];

		if ( empty( $referer ) && self::is_compare_page() ) {
			if ( wc_get_page_id( 'compare' ) ) {
				$referer = wc_get_page_permalink( 'compare' );
			}

			if ( isset( $_REQUEST['popup'] ) ) {
				$params['_wp_http_referer'] = wc_get_page_permalink( 'compare' );
			}
		}

		$url = add_query_arg( $params, $referer );

		return apply_filters( 'wcboost_products_compare_clear_url', $url );
	}

	/**
	 * Generate the unique key for the adding product.
	 *
	 * @param  int | \WC_Product $product_id
	 * @return string
	 */
	public static function generate_item_key( $product ) {
		$product_id = is_a( $product, 'WC_Product' ) ? $product->get_id() : $product;

		return apply_filters( 'wcboost_products_compare_item_key', md5( $product_id ) );
	}

	/**
	 * Display the compare field content of a product
	 *
	 * @param  string $field
	 * @param  \WC_Product $product
	 * @param  string $key
	 *
	 * @return void
	 */
	public static function compare_field( $field, $product, $key = '' ) {
		_deprecated_function( __METHOD__, '1.0.4', __NAMESPACE__ . '\Frontend::field_content' );

		Frontend::field_content( $field, $product, [ 'key' => $key ] );
	}

	/**
	 * Compare widget content
	 *
	 * @return void
	 */
	public static function widget_content( $args = [] ) {

		$args = wp_parse_args( $args, [
			'list_class'    => '',
			'show_rating'   => wc_review_ratings_enabled(),
			'compare_items' => Plugin::instance()->list->get_items(),
		] );

		echo '<div class="wcboost-products-compare-widget-content">';
		wc_get_template( 'compare/compare-widget.php', $args, '', Plugin::instance()->plugin_path() . '/templates/' );
		echo '</div>';
	}
}
