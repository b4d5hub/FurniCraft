<?php
/**
 * Customize WooCommerce templates
 *
 * @package Sober
 */

/**
 * Class for all WooCommerce template modification
 *
 * @version 1.0
 */
class Sober_WooCommerce {
	/**
	 * The single instance of the class
	 *
	 * @var Sober_WooCommerce
	 */
	protected static $instance = null;

	/**
	 * Number of days to keep set a product as a new one
	 * @var int
	 */
	protected $new_duration;

	/**
	 * Main instance
	 *
	 * @return Sober_WooCommerce
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construction function
	 */
	public function __construct() {
		// Check if Woocomerce plugin is actived
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		add_image_size( 'sober_wc_gallery_thumbnail', 160, 200, true );

		$this->new_duration = intval( sober_get_option( 'product_newness' ) );

		$this->parse_query();
		$this->hooks();
	}

	/**
	 * Parse request to change the shop columns and products per page
	 */
	public function parse_query() {
		if ( isset( $_GET['shop_columns'] ) && in_array( intval( $_GET['shop_columns'] ), array( 4, 5, 6 ) ) ) {
			$days = apply_filters( 'sober_product_columns_cookie_expire', 6 * 60 * 24 * 30 );
			wc_setcookie( 'product_columns', intval( $_GET['shop_columns'] ), $days );
			WC()->session->set( 'product_columns', intval( $_GET['shop_columns'] ) );
		}
	}

	/**
	 * Hooks to WooCommerce actions, filters
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'pre_get_posts', array( $this, 'get_products_by_group' ) );

		// Set as is_filtered with the theme's filter.
		add_filter( 'woocommerce_is_filtered', array( $this, 'is_filtered' ) );

		// Changes gallery image size prop.
		add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size' ) );

		// Need an early hook to ajaxify update mini shop cart
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );

		// Update wishlist fragment
		add_filter( 'add_to_wishlist_fragments', array( $this, 'add_to_wishlist_fragments' ) );
		add_filter( 'wcboost_wishlist_add_to_wishlist_fragments', array( $this, 'wcboost_wishlist_fragments' ) );

		// Disable redirect to product page while having only one search result
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

		// WooCommerce Styles
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'wc_styles' ) );

		// Change message content
		add_filter( 'wc_add_to_cart_message_html', array( $this, 'add_to_cart_message' ) );

		// Add Bootstrap classes
		add_filter( 'post_class', array( $this, 'product_class' ), 50, 3 );
		add_filter( 'product_cat_class', array( $this, 'product_cat_class' ), 50 );

		// Change shop columns
		add_filter( 'loop_shop_columns', array( $this, 'shop_columns' ), 20 );
		add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ), 20 );

		// Add badges
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_ribbons' ), 15 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'product_ribbons' ), 7 );

		// Wrap product thumbnail
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'open_loop_thumbnail_wrapper' ), 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'close_loop_thumbnail_wrapper' ), 30 );

		// Change product link position
		if ( 'slider' == sober_get_option( 'products_item_style' ) ) {
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_loop_thumbnail_images' ), 7 );
		} else {
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_loop_link_open' ), 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 20 );
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_product_loop_buttons' ), 25 );

		// Adds hovered thumbnail to loop product
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_product_loop_hover_thumbnail' ) );

		// Add link to product title in shop loop
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'show_product_loop_title' ) );

		// Remove stars in shop loop
		if ( sober_get_option( 'shop_hide_stars' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}

		// Add icon to the add-to-cart
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_to_cart_catalog_button' ), 10, 3 );

		// Change next and prev icon
		add_filter( 'woocommerce_pagination_args', array( $this, 'pagination_args' ) );

		// Add toolbars for shop page
		add_filter( 'woocommerce_show_page_title', '__return_false' );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'shop_toolbar' ) );

		// Remove breadcrumb, use theme's instead
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb_args' ) );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		if ( sober_get_option( 'product_hide_outstock_price' ) ) {
			add_filter( 'woocommerce_get_price_html', array( $this, 'price_html' ), 10, 2 );
		}

		/**
		 * Single products hooks
		 */
		$product_style = sober_get_option( 'single_product_style' );

		if ( is_singular( 'product' ) ) {
			add_action( 'woocommerce_before_main_content', array( $this, 'product_breadcrumb' ), 20 );
		}

		// Adds breadcrumb and product navigation on top of product
		if ( 'style-4' != $product_style && 'style-5' != $product_style ) {
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'product_toolbar' ), 5 );
		}

		// Wrap images and summary into a div
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'open_product_summary' ), 5 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'close_product_summary' ), 5 );

		// Remove thumbnails of product style 1 & 4
		if ( in_array( $product_style, array( 'style-1', 'style-4', 'style-6' ) ) && 'thumbnails' != sober_get_option( 'mobile_product_gallery_display' ) ) {
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		}

		// Add a class of product video to gallery.
		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'product_gallery_classes' ) );

		// Reorder stars
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );

		// Reorder description
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );

		// Reorder price
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );

		// Add to wishlist button
		remove_action( 'woocommerce_after_add_to_cart_button', array( 'Soo_Wishlist_Frontend', 'single_product_button' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_product_wishlist_button' ) );
		add_filter( 'woocommerce_reset_variations_link', array( $this, 'reset_variations_link' ) );
		add_filter( 'wcboost_wishlist_item_remove_link', array( $this, 'wishlist_item_remove_link' ) );
		add_filter( 'wcboost_wishlist_svg_icon', array( $this, 'wishlist_heart_icon' ), 10, 2 );

		// Product share
		add_action( 'woocommerce_share', array( $this, 'share' ) );

		// Product extra content
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_extra_content' ), 200 );

		// Change product tabs title
		add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ), 50 );

		// Remove tab heading
		add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
		add_filter( 'woocommerce_product_description_heading', '__return_false' );

		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
		add_filter( 'woocommerce_upsell_display_args', array( $this, 'upsell_products_args' ) );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'dropdown_variation_options_args' ) );

		// Ajax add to cart on single product pages.
		add_action( 'wc_ajax_sober_ajax_add_to_cart', array( $this, 'ajax_add_to_cart' ) );
		add_action( 'wc_ajax_nopriv_sober_ajax_add_to_cart', array( $this, 'ajax_add_to_cart' ) );

		// Sticky add to cart.
		if ( sober_get_option( 'product_sticky_addtocart' ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'sticky_add_to_cart' ), 99 );
		}

		if ( 'style-1' == $product_style ) {
			// Wrap product summary for sticky description
			add_action( 'woocommerce_single_product_summary', array( $this, 'open_sticky_description' ), 0 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'close_sticky_description' ), 1000 );
		} elseif ( 'style-3' == $product_style ) {
			if ( 'upsell_products' == sober_get_option( 'product_side_products' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			} elseif ( 'related_products' == sober_get_option( 'product_side_products' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			}
			add_action( 'woocommerce_after_single_product', array( $this, 'side_products' ) );
		} elseif ( 'style-5' == $product_style ) {
			add_filter( 'sober_site_content_container_class', array( $this, 'product_site_content_container_wrapped' ) );

			remove_action( 'woocommerce_before_single_product_summary', array( $this, 'product_ribbons' ), 7 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_ribbons' ), 2 );

			remove_action( 'woocommerce_share', array( $this, 'share' ) );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_meta_share' ), 5 );

			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 15 );
			add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
		} elseif ( 'style-6' == $product_style ) {
			add_filter( 'sober_site_content_container_class', array( $this, 'product_site_content_container_wrapped' ) );

			// Wrap product summary for sticky description
			add_action( 'woocommerce_single_product_summary', array( $this, 'open_sticky_description' ), 0 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'close_sticky_description' ), 1000 );

			remove_action( 'woocommerce_before_single_product_summary', array( $this, 'product_ribbons' ), 7 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_ribbons' ), 2 );

			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 15 );
			add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
		}

		/**
		 * Cart page
		 */
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sell_columns' ) );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );

		// Add billing title
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'billing_title' ) );

		// Edit name fields.
		add_filter( 'woocommerce_form_field_args', array( $this, 'form_field_args' ), 10, 2 );

		/**
		 * Cart widget
		 */
		remove_action( 'woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal' );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'widget_shopping_cart_button_checkout' ), 10 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'widget_shopping_cart_button_view_cart' ), 20 );

		/**
		 * My Account
		 */
		add_filter( 'woocommerce_my_account_my_address_description', '__return_empty_string' );

		/**
		 * Quick view
		 */
		add_action( 'wp_ajax_nopriv_sober_get_product_quickview', array( $this, 'quick_view' ) );
		add_action( 'wp_ajax_sober_get_product_quickview', array( $this, 'quick_view' ) );
		add_action( 'wc_ajax_product_quick_view', array( $this, 'quick_view' ) );

		add_action( 'sober_woocommerce_before_product_quickview_summary', array( $this, 'product_ribbons' ), 5 );
		add_action( 'sober_woocommerce_before_product_quickview_summary', 'woocommerce_show_product_images' );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_title' );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_rating', 30 );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_price', 40 );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_add_to_cart', 50 );
		add_action( 'sober_woocommerce_product_quickview_summary', 'woocommerce_template_single_meta', 60 );
		if ( sober_get_option( 'product_quickview_detail_link' ) ) {
			add_action( 'sober_woocommerce_product_quickview_summary', array( $this, 'quickview_detail_link' ), 70 );
		}

		// Support Product Bundles plugins.
		if ( class_exists( 'WC_Bundles' ) ) {
			$this->support_wc_bundles_plugin();
		}

		if ( class_exists( '\WCBoost\ProductsCompare\Plugin' ) ) {
			add_action( 'wp', array( $this, 'support_compare_plugin' ), 20 );
		}
	}

	/**
	 * Remove default woocommerce styles
	 *
	 * @since  1.0
	 *
	 * @param  array $styles
	 *
	 * @return array
	 */
	public function wc_styles( $styles ) {
		// unset( $styles['woocommerce-general'] );
		unset( $styles['woocommerce-layout'] );
		unset( $styles['woocommerce-smallscreen'] );

		return $styles;
	}

	/**
	 * Change the main query to get products by group
	 *
	 * @param object $query
	 */
	public function get_products_by_group( $query ) {
		if ( is_admin() || empty( $_GET['product_group'] ) || ! is_woocommerce() || ! $query->is_main_query() ) {
			return;
		}

		switch ( $_GET['product_group'] ) {
			case 'featured':
				if( version_compare( WC()->version, '3.0.0', '<' ) ) {
					$meta_query = WC()->query->get_meta_query();
					$meta_query[] = array(
						'key'   => '_featured',
						'value' => 'yes',
					);
					$query->set( 'meta_query', $meta_query );
				} else {
					$tax_query = WC()->query->get_tax_query();
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
				}
				break;

			case 'sale':
				$query->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
				break;

			case 'new':
				$query->set( 'post__in', array_merge( array( 0 ), sober_get_new_product_ids() ) );
				break;

			case 'best_sellers':
				$query->set( 'meta_key', 'total_sales' );
				$query->set( 'order', 'DESC' );
				$query->set( 'orderby', 'meta_value_num' );
				break;
		}
	}

	/**
	 * Change gallery image size.
	 *
	 * @return string
	 */
	public function gallery_image_size() {
		return 'woocommerce_single';
	}

	/**
	 * Move the button to the end of message
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public function add_to_cart_message( $message ) {
		if ( preg_match( '/(<a\b[^>]*>(.*?)<\/a>)/i', $message, $matches ) ) {
			$message = preg_replace( '/<a\b[^>]*>(.*?)<\/a>/i', '', $message );
			$message .= $matches[0];
		}

		return $message;
	}

	/**
	 * Ajaxify update cart viewer
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function add_to_cart_fragments( $fragments ) {
		$fragments['span.cart-counter'] = '<span class="count cart-counter">' . WC()->cart->get_cart_contents_count() . '</span>';

		return $fragments;
	}

	/**
	 * Ajaxify update wishlist viewer
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function add_to_wishlist_fragments( $fragments ) {
		// Always use WCBoost wishlist if it is available.
		if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
			return $fragments;
		}

		if ( function_exists( 'Soo_Wishlist' ) ) {
			$fragments['span.wishlist-counter'] = '<span class="count wishlist-counter">' . soow_count_products() . '</span>';
			$fragments['.sober-modal div.soo-wishlist'] = do_shortcode( '[soo_wishlist]' );
		}

		return $fragments;
	}

	/**
	 * Add more fragments to WCBoost wishlists
	 *
	 * @param  array $fragments
	 *
	 * @return array
	 */
	public function wcboost_wishlist_fragments( $fragments ) {
		if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
			$fragments['span.wishlist-counter'] = '<span class="count wishlist-counter">' . \WCBoost\Wishlist\Helper::get_wishlist()->count_items() . '</span>';
			ob_start();
			\WCBoost\Wishlist\Helper::widget_content( array(
				'show_stock'    => true,
				'show_purchase' => true,
			) );

			$fragments['.sober-modal .wcboost-wishlist-widget-content'] = ob_get_clean();
		}

		return $fragments;
	}

	/**
	 * Change the shop columns
	 *
	 * @since  1.0.0
	 *
	 * @param  int $columns The default columns
	 *
	 * @return int
	 */
	public function shop_columns( $columns ) {
		if ( is_woocommerce() ) {
			$columns = ( isset( WC()->session ) && ! is_null( WC()->session->get( 'product_columns' ) ) ) ? intval( WC()->session->get( 'product_columns' ) ) : intval( sober_get_option( 'product_columns' ) );
		}

		return $columns;
	}

	/**
	 * Change number of products per page
	 *
	 * @param int $limit
	 *
	 * @return int
	 */
	public function products_per_page( $limit ) {
		$limit = intval( sober_get_option( 'products_per_page' ) );

		if ( ! empty( WC()->session ) && ! is_null( WC()->session->get( 'product_columns' ) ) ) {
			$columns = WC()->session->get( 'product_columns' );
			$rows    = intval( $limit / $columns );
			$rows    = $rows ? $rows: $rows + 1;
			$limit   = $rows * $columns;
		}

		return $limit;
	}

	/**
	 * Change next and previous icon of pagination nav
	 *
	 * @since  1.0
	 */
	public function pagination_args( $args ) {
		$args['prev_text'] = sober_svg_icon( 'icon=left-arrow&echo=0' );
		$args['next_text'] = sober_svg_icon( 'icon=right-arrow&echo=0' );

		if ( sober_get_option( 'shop_nav_type' ) != 'links' ) {
			$loading           = '<span class="loading-icon"><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span><span class="bubble"><span class="dot"></span></span></span>';
			$args['prev_text'] = '';
			$args['next_text'] = '<span class="button-text">' . esc_html__( 'Load More', 'sober' ) . '</span>' . $loading;
		}

		return $args;
	}

	/**
	 * Add an icon to the add to cart link on catalog pages
	 *
	 * @param string $link
	 * @param object $product
	 * @param array $args
	 *
	 * @return string
	 */
	public function add_to_cart_catalog_button( $link, $product, $args ) {
		if ( sober_get_option( 'product_quickview' ) && sober_get_option( 'product_quickview_behavior' ) == 'buy_button' && 'quickview' != sober_get_option( 'products_item_style' ) ) {
			$icon = 'quickview-eye';
			$url  = $product->get_permalink();
			$args['class'] = str_replace( 'ajax_add_to_cart', '', $args['class'] );
		} else {
			$icon = 'basket-addtocart';
			$url  = $product->add_to_cart_url();
		}

		$icon = sober_svg_icon( array( 'icon' => $icon, 'echo' => false ) );

		$link = sprintf(
			'<a href="%s" data-quantity="%s" class="%s sober-loop-atc-button" %s>
				%s
				<span class="screen-reader-text">%s</span>
			</a>',
			esc_url( $url ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			$icon,
			esc_html( $product->add_to_cart_text() )
		);

		return $link;
	}

	/**
	 * Display a tool bar on top of product archive
	 *
	 * @since 1.0
	 */
	public function shop_toolbar() {
		if ( ! sober_get_option( 'shop_toolbar' ) ) {
			return;
		}

		global $woocommerce_loop;

		if ( isset( $woocommerce_loop['is_shortcode'] ) && $woocommerce_loop['is_shortcode'] ) {
			return;
		}

		$columns = ! is_null( WC()->session->get( 'product_columns' ) ) ? intval( WC()->session->get( 'product_columns' ) ) : intval( sober_get_option( 'product_columns' ) );
		$toggle  = sober_get_option( 'products_toggle' );
		$sort    = sober_get_option( 'products_sorting' );
		$display = ( sober_get_option( 'products_toggle_taxonomy_keep' ) && is_product_taxonomy() ) || is_shop();

		if ( $toggle ) {
			$type = sober_get_option( 'products_toggle_type' );
			$tabs = array();

			$tabs[] = sprintf(
				'<li data-filter="*" class="line-hover active"><a href="%s">%s</a></li>',
				esc_url( remove_query_arg( array( 'product_group' ) ) ),
				esc_html__( 'All Products', 'sober' )
			);

			if ( 'tag' == $type && $display ) {
				$tags = trim( sober_get_option( 'products_toggle_tags' ) );
				$tags = explode( ',', $tags );

				if ( $tags ) {
					foreach ( $tags as $tag ) {
						$tag = get_term_by( 'name', $tag, 'product_tag' );

						if ( $tag ) {
							$tabs[] = sprintf(
								'<li data-filter=".product_tag-%s" class="line-hover"><a href="%s">%s</a></li>',
								esc_attr( $tag->slug ),
								esc_url( get_term_link( $tag ) ),
								esc_html( $tag->name )
							);
						}
					}
				}
			} elseif ( 'category' == $type && $display ) {
				$categories = array();
				$amount = sober_get_option( 'products_toggle_category_amount' );
				$args = array(
					'taxonomy' => 'product_cat',
					'orderby'  => 'count',
					'order'    => 'DESC',
					'parent'   => 0,
				);

				if ( is_numeric( $amount ) ) {
					$args['number'] = $amount;
					$args['parent'] = '';
					$categories = get_terms( $args );
				} elseif ( ! empty( $amount ) ) {
					$names = explode( ',', $amount );

					if ( $names ) {
						foreach ( $names as $name ) {
							$cat = get_term_by( 'name', $name, 'product_cat' );

							if ( $cat ) {
								$categories[] = $cat;
							}
						}
					}
				} else {
					$categories = get_terms( $args );
				}

				if ( $categories && ! is_wp_error( $categories ) ) {
					foreach ( $categories as $category ) {
						$tabs[] = sprintf(
							'<li data-filter=".product_cat-%s" class="line-hover"><a href="%s">%s</a></li>',
							esc_attr( $category->slug ),
							esc_url( get_term_link( $category ) ),
							esc_html( $category->name )
						);
					}
				}
			} elseif ( 'group' == $type || ! $display ) {
				$groups   = (array) sober_get_option( 'products_toggle_groups' );
				$type     = 'group';
				$base_url = '';

				if ( is_shop() ) {
					$base_url = wc_get_page_permalink( 'shop' );
				} elseif ( is_product_taxonomy() ) {
					$term = get_queried_object();
					$base_url = get_term_link( $term );
				}

				if ( ! empty( $_GET['product_group'] ) ) {
					$tabs = array();
					$tabs[] = sprintf(
						'<li data-filter="*" class="line-hover"><a href="%s">%s</a></li>',
						$base_url ? esc_url( $base_url ) : esc_url( remove_query_arg( 'product_group' ) ),
						esc_html__( 'All Products', 'sober' )
					);
				}

				if ( in_array( 'featured', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".featured" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'featured' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'featured' ), $base_url ) ),
						esc_html__( 'Hot Products', 'sober' )
					);
				}

				if ( in_array( 'new', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".new" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'new' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'new' ), $base_url ) ),
						esc_html__( 'New Products', 'sober' )
					);
				}

				if ( in_array( 'sale', $groups ) ) {
					$tabs[] = sprintf(
						'<li data-filter=".sale" class="line-hover %s"><a href="%s">%s</a></li>',
						isset( $_GET['product_group'] ) && 'sale' == $_GET['product_group'] ? 'active' : '',
						esc_url( add_query_arg( array( 'product_group' => 'sale' ), $base_url ) ),
						esc_html__( 'Sale Products', 'sober' )
					);
				}
			}
		}
		?>

		<div class="shop-toolbar">
			<div class="row">
				<div class="col-sm-9 col-md-7 hidden-xs nav-filter">
					<?php if ( $toggle ) : ?>
						<ul class="shop-toolbar__tabs products-filter filter-by-<?php echo esc_attr( $type ) ?> clearfix">
							<?php echo implode( "\n", $tabs ) ?>
						</ul>
					<?php else : ?>
						<?php
						if ( $sort ) {
							woocommerce_catalog_ordering();
						}
						woocommerce_result_count();
						?>
					<?php endif; ?>

					<?php do_action( 'sober_shop_toolbar_left' ); ?>
				</div>

				<div class="col-xs-12 col-sm-3 col-md-5 controls">
					<ul class="toolbar-control">
						<?php if ( $toggle ) : ?>
							<li class="data totals">
								<?php
								if ( $sort ) {
									woocommerce_catalog_ordering();
								} else {
									woocommerce_result_count();
								}
								?>
							</li>
						<?php endif; ?>
						<li class="data product-size">
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 6 ) ) ) ?>" class="small-size <?php echo 6 == $columns ? 'active' : '' ?>" rel="nofollow">
								<?php sober_svg_icon( 'icon=small-view-size&size=15' ); ?>
							</a>
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 5 ) ) ) ?>" class="medium-size <?php echo 5 == $columns ? 'active' : '' ?>" rel="nofollow">
								<?php sober_svg_icon( 'icon=medium-view-size&size=15' ); ?>
							</a>
							<a href="<?php echo esc_url( add_query_arg( array( 'shop_columns' => 4 ) ) ) ?>" class="large-size <?php echo 4 == $columns ? 'active' : '' ?>" rel="nofollow">
								<?php sober_svg_icon( 'icon=large-view-size&size=15' ); ?>
							</a>
						</li>
						<?php if ( is_active_sidebar( 'shop-filter' ) && sober_get_option( 'products_filter' ) ) : ?>
							<li class="data filter">
								<a href="#" class="toggle-filter">
									<?php sober_svg_icon( 'icon=filter' ); ?>
									<?php esc_html_e( 'Filter', 'sober' ) ?>
								</a>

								<div class="filter-widgets woocommerce">
									<button class="close">
										<?php sober_svg_icon( 'icon=close-delete' ); ?>
										<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
									</button>
									<?php dynamic_sidebar( 'shop-filter' ) ?>
								</div>
							</li>
						<?php endif; ?>
					</ul>

					<?php do_action( 'sober_shop_toolbar_right' ); ?>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Filter function for breadcrumb args
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function breadcrumb_args( $args ) {
		$args['delimiter']   = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>';

		return $args;
	}

	/**
	 * Display product breadcrumb
	 */
	public function product_breadcrumb() {
		?>

		<div class="product-breadcrumb">
			<nav class="product-navigation post-navigation">
				<?php previous_post_link( '%link', sober_svg_icon( 'icon=left-arrow&echo=0' ) . '<span class="screen-reader-text">%title</span>' ) ?>
				<?php next_post_link( '%link', sober_svg_icon( 'icon=right-arrow&echo=0' ) . '<span class="screen-reader-text">%title</span>' ) ?>
			</nav>
			<?php woocommerce_breadcrumb() ?>
		</div>

		<?php
	}

	/**
	 * Add Bootstrap's column classes for product
	 *
	 * @since 1.0
	 *
	 * @param array  $classes
	 * @param string $class
	 * @param string $post_id
	 *
	 * @return array
	 */
	public function product_class( $classes, $class = '', $post_id = '' ) {
		global $woocommerce_loop, $product;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! sober_is_elementor_editor_mode() ) {
			return $classes;
		}

		// Add classes for products in archive page only
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ) ) ) {
			return $classes;
		}

		// Add classes for product
		if ( $this->new_badge() ) {
			$classes[] = 'new';
		}

		if ( is_single( $post_id ) && 'style-5' ==  sober_get_option( 'single_product_style' ) ) {
			if ( get_post_meta( $post_id, 'background_color', true ) ) {
				$classes[] = 'background-set';
			}

			if ( get_post_meta( $post_id, 'background_color_for_all_images', true ) ) {
				$classes[] = 'background-set-all';
			}
		}

		$classes[] = 'layout-' . sober_get_option( 'single_product_style' );

		if ( ! is_single( $post_id ) ) {
			$classes[] = 'col-md-4 col-sm-4 col-xs-6';

			if ( isset( $woocommerce_loop['columns'] ) && $woocommerce_loop['columns'] == 5 ) {
				$classes[] = 'col-lg-1-5';
			} elseif ( isset( $woocommerce_loop['columns'] ) ) {
				$classes[] = 'col-lg-' . ( 12 / $woocommerce_loop['columns'] );
			}

			$gallery_image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
			if ( ! empty( $gallery_image_ids ) ) {
				$classes[] = 'product-has-gallery';
			}

			// Adds a class of product style in grid
			$classes[] = 'product-style-' . sober_get_option( 'products_item_style' );
		}

		return $classes;
	}

	/**
	 * Add class for product category item
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function product_cat_class( $classes ) {
		global $woocommerce_loop, $product;

		$classes[] = 'col-md-4 col-sm-4 col-xs-6';

		if ( ! isset( $woocommerce_loop['columns'] ) ) {
			$classes[] = 'col-lg-1-5';

			return $classes;
		}

		if ( $woocommerce_loop['columns'] == 5 ) {
			$classes[] = 'col-lg-1-5';
		} else {
			$classes[] = 'col-lg-' . ( 12 / $woocommerce_loop['columns'] );
		}

		return $classes;
	}

	/**
	 * Sale badge
	 *
	 * @return string
	 */
	public function sale_badge() {
		global $product;

		if ( ! $product->is_on_sale() || 'grouped' == $product->get_type() ) {
			return '';
		}

		$format     = sober_get_option( 'product_badge_sale_format' );
		$text       = sober_get_option( 'product_badge_sale_text' );
		$percentage = 0;

		if ( 'percent' === $format || false !== strpos( $text, '{%}' ) ) {
			if ( $product->get_type() == 'variable' ) {
				$available_variations = $product->get_available_variations();
				$max_percentage       = 0;
				$total_variations     = count( $available_variations );

				for ( $i = 0; $i < $total_variations; $i++ ) {
					$variation_id        = $available_variations[ $i ]['variation_id'];
					$variable_product    = new WC_Product_Variation( $variation_id );
					$regular_price       = $variable_product->get_regular_price();
					$sales_price         = $variable_product->get_sale_price();
					$variable_percentage = $regular_price && $sales_price ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ) : 0;

					if ( $variable_percentage > $max_percentage ) {
						$max_percentage = $variable_percentage;
					}
				}

				$percentage = $max_percentage ? $max_percentage : $percentage;
			} elseif ( $product->get_regular_price() != 0 ) {
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			}
		}

		if ( 'percent' == $format ) {
			$off    = sober_get_option( 'product_onsale_off' ) ? ' ' . esc_html__( 'OFF', 'sober' ) : '';
			$output = '<span class="onsale woocommerce-badge ribbon">' . $percentage . '%' . $off . '</span>';
		} else {
			$text = str_replace( '{%}', $percentage . '%', $text );
			$output = '<span class="onsale woocommerce-badge ribbon">' . esc_html( $text ) . '</span>';
		}

		return $output;
	}

	/**
	 * Featured badge
	 *
	 * @return string
	 */
	public function featured_badge() {
		global $product;

		if ( ! $product->is_featured() ) {
			return '';
		}

		$text = sober_get_option( 'product_badge_featured_text' );
		$text = false !== $text ? $text : esc_html__( 'Hot', 'sober' );

		return '<span class="featured woocommerce-badge ribbon">' . $text . '</span>';
	}

	/**
	 * Sold out badge
	 *
	 * @return string
	 */
	public function sold_out_badge() {
		global $product;

		if ( $product->is_in_stock() ) {
			return '';
		}

		// Double check if this is a variable product.
		$in_stock = false;

		if ( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();

			foreach ( $variations as $variation ) {
				if( $variation['is_in_stock'] ) {
					$in_stock = true;
					break;
				}
			}
		}

		if ( $in_stock ) {
			return '';
		}

		$text = sober_get_option( 'product_badge_soldout_text' );
		$text = false !== $text ? $text : esc_html__( 'Sold Out', 'sober' );

		return '<span class="sold-out woocommerce-badge ribbon">' . $text . '</span>';
	}

	/**
	 * New badge
	 *
	 * @return string
	 */
	public function new_badge() {
		global $product;

		$is_new = get_post_meta( $product->get_id(), '_is_new', true );

		if ( ! $is_new && $this->new_duration ) {
			$is_new = ( ( time() - ( 60 * 60 * 24 * $this->new_duration ) ) < strtotime( get_the_time( 'Y-m-d' ) ) );
		}

		if ( $is_new ) {
			$text = sober_get_option( 'product_badge_new_text' );
			$text = false !== $text ? $text : esc_html__( 'New', 'sober' );

			return '<span class="newness woocommerce-badge ribbon">' . $text . '</span>';
		}

		return '';
	}

	/**
	 * Display badge for new product or featured product
	 *
	 * @since 1.0
	 */
	public function product_ribbons() {
		if ( ! sober_get_option( 'product_badges' ) ) {
			return;
		}

		global $product;

		$output = array();

		if ( sober_get_option( 'product_badge_sale' ) ) {
			$sale_badge = $this->sale_badge();

			if ( $sale_badge ) {
				$output['onsale'] = $sale_badge;
			}
		}

		if ( sober_get_option( 'product_badge_featured' ) ) {
			$featured_badge = $this->featured_badge();

			if ( $featured_badge ) {
				$output['featured'] = $featured_badge;
			}
		}

		if ( sober_get_option( 'product_badge_new' ) ) {
			$new_badge = $this->new_badge();

			if ( $new_badge ) {
				$output['newness'] = $new_badge;
			}
		}

		if ( sober_get_option( 'product_sold_out_ribbon' ) ) {
			$sold_out_badge = $this->sold_out_badge();

			if ( $sold_out_badge ) {
				$output['soldout'] = $sold_out_badge;
			}
		}

		$output = apply_filters( 'sober_product_ribbons', $output, $product );
		ksort( $output );

		if ( $output ) {
			printf( '<span class="ribbons woocommerce-badges">%s</span>', implode( '', $output ) );
		}
	}

	/**
	 * Open product thumbnail wrapper
	 */
	public function open_loop_thumbnail_wrapper() {
		echo '<div class="product-header">';
	}

	/**
	 * Close product thumbnail wrapper
	 */
	public function close_loop_thumbnail_wrapper() {
		echo '</div>';
	}

	/**
	 * Loop thumbnail images
	 */
	public function product_loop_thumbnail_images() {
		global $product;

		$image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

		if ( $image_ids ) {
			echo '<div class="product-images__slider owl-carousel owl-theme">';
		}

		woocommerce_template_loop_product_link_open();
		woocommerce_template_loop_product_thumbnail();
		woocommerce_template_loop_product_link_close();

		foreach ( $image_ids as $image_id ) {
			$image_size = has_image_size( 'woocommerce_thumbnail' ) ? 'woocommerce_thumbnail' : 'shop_catalog';
			$src = wp_get_attachment_image_src( $image_id, $image_size );

			if ( ! $src ) {
				continue;
			}

			woocommerce_template_loop_product_link_open();

			printf(
				'<img data-src="%s" width="%s" height="%s" class="owl-lazy" alt="%s">',
				esc_url( $src[0] ),
				esc_attr( $src[1] ),
				esc_attr( $src[2] ),
				esc_attr( $product->get_title() )
			);

			woocommerce_template_loop_product_link_close();
		}

		if ( $image_ids ) {
			echo '</div>';
		}
	}

	/**
	 * Open product link on the shop page
	 * Adds more attributes zooming
	 */
	public function product_loop_link_open() {
		if ( 'zoom' == sober_get_option( 'products_item_style' ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( $image ) {
				echo '<a href="' . get_the_permalink() . '" class="woocommerce-LoopProduct-link product-thumbnail-zoom" data-zoom_image="' . $image[0] . '">';
			} else {
				woocommerce_template_loop_product_link_open();
			}
		} else {
			woocommerce_template_loop_product_link_open();
		}
	}

	/**
	 * Add billing title
	 */
	public function billing_title() {
		if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

			<h3><?php esc_html_e( 'Billing &amp; Shipping', 'sober' ); ?></h3>

		<?php else : ?>

			<h3><?php esc_html_e( 'Billing Details', 'sober' ); ?></h3>

		<?php endif;
	}

	/**
	 * Edit form fields.
	 *
	 * @param array  $args Form field argurments.
	 * @param string $key  Field name
	 *
	 * @return array
	 */
	public function form_field_args( $args, $key ) {
		switch ( $key ) {
			case 'billing_first_name':
			case 'shipping_first_name':
			case 'account_first_name':
				$args['placeholder'] = $args['label'];
				$args['label']       = esc_html__( 'Name', 'sober' );
				break;

			case 'billing_last_name':
			case 'shipping_last_name':
			case 'account_last_name':
				$args['placeholder'] = $args['label'];
				break;

			case 'account_password':
				$args['label'] = esc_html__( 'Account password', 'sober' );
				break;
		}

		return $args;
	}

	/**
	 * Add hover image for a product on catalog page
	 */
	public function show_product_loop_hover_thumbnail() {
		global $product;

		if ( ! sober_get_option( 'product_hover_thumbnail' ) ) {
			return;
		}

		if ( in_array( sober_get_option( 'products_item_style' ), array( 'slider', 'zoom' ) ) ) {
			return;
		}

		$image_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

		if ( empty( $image_ids ) ) {
			return;
		}

		$image_size = has_image_size( 'woocommerce_thumbnail' ) ? 'woocommerce_thumbnail' : 'shop_catalog';

		echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-shop_catalog size-shop_catalog product-hover-image' ) );
	}

	/**
	 * Show product buttons inside the .product-header div
	 * This contains add_to_cart and wishlist buttons
	 */
	public function show_product_loop_buttons() {
		$style              = sober_get_option( 'products_item_style' );
		$quickview          = sober_get_option( 'product_quickview' );
		$quickview_behavior = sober_get_option( 'product_quickview_behavior' );
		?>

		<?php if ( 'default' != $style ) : ?>

			<div class="buttons-icon">
				<?php
				if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
					\WCBoost\Wishlist\Frontend::instance()->loop_add_to_wishlist_button();
				} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
					echo do_shortcode( '[add_to_wishlist]' );
				} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
					echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				}

				// Quick-view icon
				if ( 'quickview' != $style && $quickview && $quickview_behavior == 'view_button' ) {
					printf(
						'<a href="%s" class="quick_view_button button" data-product_id="%s">' . sober_svg_icon( 'icon=quickview-eye&echo=0' ) . '</a>',
						esc_url( get_permalink() ),
						esc_attr( get_the_ID() )
					);
				}

				if ( class_exists( '\WCBoost\ProductsCompare\Frontend' ) ) {
					\WCBoost\ProductsCompare\Frontend::instance()->loop_add_to_compare_button();
				}
				?>
			</div>

		<?php endif; ?>

		<?php if ( in_array( $style, array( 'default', 'quickview', 'addtocart' ) ) ) : ?>

			<div class="buttons">
				<?php
				woocommerce_template_loop_add_to_cart();

				if ( 'default' == $style ) {
					if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
						\WCBoost\Wishlist\Frontend::instance()->loop_add_to_wishlist_button();
					} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
						echo do_shortcode( '[add_to_wishlist]' );
					} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
						echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
					}
				}

				if (
					( 'default' == $style && $quickview && $quickview_behavior == 'view_button' )
					|| ( 'quickview' == $style && $quickview )
				) {
					printf(
						'<a href="%s" class="quick_view_button button" data-product_id="%s">' . sober_svg_icon( 'icon=quickview-eye&echo=0' ) . '</a>',
						esc_url( get_permalink() ),
						esc_attr( get_the_ID() )
					);
				}

				if ( 'default' == $style && class_exists( '\WCBoost\ProductsCompare\Frontend' ) ) {
					\WCBoost\ProductsCompare\Frontend::instance()->loop_add_to_compare_button();
				}
				?>
			</div>

		<?php endif; ?>

		<?php
	}

	/**
	 * Print new product title shop page with link inside
	 */
	public function show_product_loop_title() {
		?>

		<h3 class="woocommerce-loop-product__title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3>

		<?php
	}

	/**
	 * Hide the price for out-of-stock products
	 *
	 * @param string $price
	 * @param object $product
	 *
	 * @return string
	 */
	public function price_html( $price, $product ) {
		if ( ! $product->is_in_stock() ) {
			$price = esc_html__( 'Sold Out', 'sober' );
		}

		return $price;
	}

	/**
	 * Display product toolbar on single product page
	 */
	public function product_toolbar() {
		if ( ! sober_get_option( 'product_toolbar' ) ) {
			return;
		}
		?>

		<div class="product-toolbar">
			<?php
			if ( sober_get_option( 'product_navigation' ) ) {
				$nav_args = array(
					'screen_reader_text' => esc_html__( 'Product navigation', 'sober' ),
					'prev_text'          => sober_svg_icon( 'icon=left-arrow&echo=0' ) . '<span class="screen-reader-text">%title</span>',
					'next_text'          => '<span class="screen-reader-text">%title</span>' . sober_svg_icon( 'icon=right-arrow&echo=0' ),
				);

				if ( sober_get_option( 'product_navigation_same_cat' ) ) {
					$nav_args['in_same_term'] = true;
					$nav_args['taxonomy'] = 'product_cat';
				}

				the_post_navigation( $nav_args );
			}

			$yoast = get_option( 'wpseo_internallinks' );

			if ( function_exists( 'yoast_breadcrumb' ) && $yoast && $yoast['breadcrumbs-enable'] ) {
				yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
			} else {
				woocommerce_breadcrumb();
			}
			?>
		</div>

		<?php
	}

	/**
	 * Open product summary div
	 */
	public function open_product_summary() {
		echo '<div class="product-summary clearfix">';
	}

	/**
	 * Close product summary div
	 */
	public function close_product_summary() {
		echo '</div>';
	}

	/**
	 * Open sticky product summary container
	 */
	public function open_sticky_description() {
		echo '<div class="sticky-summary">';
	}

	/**
	 * Close sticky product summary container
	 */
	public function close_sticky_description() {
		echo '</div>';
	}

	/**
	 * Add wishlist button again
	 */
	public function single_product_wishlist_button() {
		global $product;

		// Button was added to variable products manually.
		if ( $product->is_type( 'variable' ) || $product->is_type( 'external' ) || $product->is_type( 'bundle' ) ) {
			return;
		}

		$this->add_to_wishlist_button();
	}

	/**
	 * Display the add-to-wishlist button for single product
	 *
	 * @return void
	 */
	public function add_to_wishlist_button() {
		if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
			\WCBoost\Wishlist\Frontend::instance()->single_add_to_wishlist_button();
		} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
			echo do_shortcode( '[add_to_wishlist]' );
		}
	}

	/**
	 * Change the icon of the link to remove wishlist items.
	 *
	 * @param  string $link
	 * @return string
	 */
	public function wishlist_item_remove_link( $link ) {
		$link = str_replace( "&times;", sober_svg_icon( 'icon=close-delete-small&size=12&echo=0' ), $link );
		return $link;
	}

	/**
	 * Wishlist hear icon
	 *
	 * @param  string $svg
	 * @param  string $icon
	 * @return string
	 */
	public function wishlist_heart_icon( $svg, $icon ) {
		if ( 'heart' == $icon ) {
			$svg = sober_svg_icon( 'icon=heart-wishlist-like&echo=0' );
		} elseif ( 'heart-filled' == $icon ) {
			$svg = sober_svg_icon( 'icon=heart-wishlist-liked&echo=0' );
		}

		return $svg;
	}

	/**
	 * Wrap reset variations link with a div container
	 *
	 * @param $link
	 *
	 * @return string
	 */
	public function reset_variations_link( $link ) {
		return '<div class="variations-reset">' . $link . '</div>';
	}

	/**
	 * Product share
	 * Share on Facebook, Twitter, Pinterest
	 */
	public function share() {
		$socials = sober_get_option( 'product_share' );

		if ( empty( $socials ) ) {
			return;
		}
		?>

		<div class="product-share">
			<span class="screen-reader-text"><?php esc_html_e( 'Share','sober' ) ?></span>

			<?php if ( in_array( 'facebook', $socials ) ) : ?>

				<a href="<?php echo esc_url( add_query_arg( array( 'u' => rawurlencode( get_permalink() ) ), 'https://www.facebook.com/sharer/sharer.php' ) ) ?>" target="_blank" class="facebook-share-link">
					<i class="fa fa-facebook"></i><?php esc_html_e( 'Facebook', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'twitter', $socials ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array(
					'url'  => rawurlencode( get_permalink() ),
					'text' => rawurlencode( get_the_title() ),
				), 'https://twitter.com/intent/tweet' ) ) ?>" target="_blank" class="twitter-share-link">
					<i class="fa fa-twitter"></i><?php esc_html_e( 'Twitter', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'pinterest', $socials ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array(
					'url'         => rawurlencode( get_permalink() ),
					'media'       => get_the_post_thumbnail_url( null, 'full' ),
					'description' => rawurlencode( get_the_title() ),
				), 'http://pinterest.com/pin/create/button' ) ) ?>" target="_blank" class="pinterest-share-link">
					<i class="fa fa-pinterest-p"></i><?php esc_html_e( 'Pinterest', 'sober' ) ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( 'email', $socials ) ) : ?>
				<a href="mailto:?subject=<?php esc_attr_e( 'I wanted you to see this', 'sober' ) ?>&amp;body=<?php echo esc_attr_e( 'Check out this site', 'sober' ) . ' ' . rawurlencode( get_permalink() ) ?>" target="_blank" class="email-share-link">
					<i class="fa fa-envelope-o"></i><?php esc_html_e( 'Email', 'sober' ) ?>
				</a>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Add extra content at bottom of product's short description
	 */
	public function product_extra_content() {
		$content = sober_get_option( 'product_extra_content' );

		if ( empty( $content ) ) {
			return;
		}

		printf( '<div class="product-extra-content">%s</div>', do_shortcode( wp_kses_post( $content ) ) );
	}

	/**
	 * Change product tab titles
	 * Add <span> to the counter beside "Review" tab
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_tabs( $tabs ) {
		foreach ( $tabs as &$tab ) {
			$tab['title'] = str_replace( array( '(', ')' ), array(
				'<span class="counter">',
				'</span>',
			), $tab['title'] );
		}

		return $tabs;
	}

	/**
	 * Change related & up-sell products columns
	 *
	 * @param int $columns
	 *
	 * @return int
	 */
	public function related_products_columns( $columns ) {
		global $woocommerce_loop;

		if ( 'style-3' == sober_get_option( 'single_product_style' ) ) {
			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'up-sells' ) {
				$columns = 2;
			} else {
				$columns = 4;
			}
		} elseif ( 'style-5' == sober_get_option( 'single_product_style' ) ) {
			$columns = 4;
		} else {
			$columns = 5;
		}

		return $columns;
	}

	/**
	 * Change related products args
	 * It contains 'posts_per_page' and 'columns'
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function related_products_args( $args ) {
		global $woocommerce_loop;

		$product_style = sober_get_option( 'single_product_style' );

		if ( 'style-3' == $product_style ) {
			$args['posts_per_page'] = 4;

			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'related' ) {
				$args['columns'] = 2;
			} else {
				$args['columns'] = 4;
			}
		} elseif ( 'style-4' == $product_style || 'style-5' == $product_style || 'style-6' == $product_style ) {
			$args['posts_per_page'] = 4;
			$args['columns']        = 4;
		} else {
			$args['posts_per_page'] = 5;
			$args['columns']        = 5;
		}

		if ( sober_get_option( 'product_related_carousel' ) ) {
			$args['posts_per_page'] = intval( sober_get_option( 'product_related_carousel_items' ) );
		}

		return $args;
	}

	/**
	 * Change upsell products args
	 * It contains 'posts_per_page' and 'columns'
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function upsell_products_args( $args ) {
		global $woocommerce_loop;

		$product_style = sober_get_option( 'single_product_style' );

		if ( 'style-3' == $product_style ) {
			$args['posts_per_page'] = 20;

			if ( isset( $woocommerce_loop['name'] ) && $woocommerce_loop['name'] == 'up-sells' ) {
				$args['columns'] = 2;
			} else {
				$args['columns'] = 4;
			}
		} elseif ( 'style-4' == $product_style || 'style-5' == $product_style || 'style-6' == $product_style ) {
			$args['posts_per_page'] = 4;
			$args['columns']        = 4;
		} else {
			$args['posts_per_page'] = 5;
			$args['columns']        = 5;
		}

		if ( 'style-3' != $product_style && sober_get_option( 'product_upsells_carousel' ) ) {
			$args['posts_per_page'] = 24;
		}

		return $args;
	}

	/**
	 * Change cross sell product columns
	 *
	 * @return int
	 */
	public function cross_sell_columns() {
		return 4;
	}

	/**
	 * Add a label to quantity input on the cart page
	 *
	 * @param  string $product_quantity Quantity input HTML
	 * @param  string $cart_item_key
	 * @param  array  $cart_item
	 * @return string
	 */
	public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product->is_sold_individually() ) {
			$product_quantity = '<span class="quantity--individual">' . _x( '1', 'Product quantity', 'sober' ) . '</span>';
		}

		$product_quantity = '<span class="quantity-label">' . esc_html__( 'Qty:', 'sober' ) . '</span>' . $product_quantity;

		return $product_quantity;
	}

	/**
	 * Change the default option none text
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function dropdown_variation_options_args( $args ) {
		$args['show_option_none'] = esc_html__( 'Select', 'sober' );

		return $args;
	}

	/**
	 * Output the proceed to checkout button.
	 */
	public function widget_shopping_cart_button_checkout() {
		?>

		<p>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout wc-forward">
				<span class="subtotal"><?php echo WC()->cart->get_cart_subtotal() ?></span>
				<span><?php esc_html_e( 'Checkout', 'sober' ); ?></span>
			</a>
		</p>

		<?php
	}

	/**
	 * Output the view cart button.
	 */
	public function widget_shopping_cart_button_view_cart() {
		?>

		<p>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="line-hover active wc-forward">
				<?php esc_html_e( 'View Cart', 'sober' ); ?>
			</a>
		</p>

		<?php
	}

	/**
	 * Change content container class
	 *
	 * @param string $class
	 * @return string
	 */
	public function product_site_content_container_wrapped( $class ) {
		if ( is_singular( 'product' ) ) {
			$class = 'container';
		}

		return $class;
	}

	/**
	 * Display the product meta and social share
	 */
	public function product_meta_share() {
		?>

		<div class="woocommerce-product-meta-share">
			<?php woocommerce_template_single_meta(); ?>
			<?php $this->share(); ?>
		</div>

		<?php
	}

	/**
	 * Product quick view template.
	 *
	 * @return string
	 */
	public function quick_view() {
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'sober' ) );
			exit;
		}

		$post_object = get_post( $_POST['product_id'] );
		if ( ! $post_object || ! in_array( $post_object->post_type, array( 'product', 'product_variation' ) ) ) {
			wp_send_json_error( esc_html__( 'Invalid product.', 'sober' ) );
			exit;
		}

		ob_start();
		wc_get_template( 'content-product-quickview.php', array(
			'post_object' => $post_object
		) );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * Display the product link inside the quickview modal
	 */
	public function quickview_detail_link() {
		$text = apply_filters( 'sober_quickview_product_link_text', esc_html__( 'View full product details', 'sober' ) );

		if ( ! $text ) {
			return;
		}
		?>
		<div class="view-product-link sober-product-quickview__view-product-link">
			<a href="<?php the_permalink(); ?>" class="view-details"><?php echo esc_html( $text ); ?></a>
		</div>
		<?php
	}

	/**
	 * Display side products on prduct page v3.
	 */
	public static function side_products() {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return;
		}

		global $product;

		$limit = sober_get_option( 'product_side_products_limit' );
		$type  = sober_get_option( 'product_side_products' );

		if ( 'upsell_products' == $type ) {
			$query        = new stdClass();
			$query->posts = $product->get_upsell_ids();
		} elseif ( 'related_products' == $type ) {
			$query            = new stdClass();
			$related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $limit, $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
			$related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );
			$query->posts     = $related_products;
		} else {
			$atts  = array(
				'per_page'     => intval( $limit ),
				'category'     => '',
				'cat_operator' => 'IN',
			);

			switch ( $type ) {
				case 'sale_products':
				case 'top_rated_products':
					$atts = array_merge( array(
						'orderby' => 'title',
						'order'   => 'ASC',
					), $atts );
					break;

				case 'recent_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
					), $atts );
					break;

				case 'featured_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
						'orderby' => 'rand',
					), $atts );
					$atts['visibility'] = 'featured';
					break;
			}

			$args  = new WC_Shortcode_Products( $atts, $type );
			$args  = $args->get_query_args();
			$args['post__not_in'] = array( $product->get_id() );
			$query = new WP_Query( $args );
		}

		echo '<div class="side-products">';
		echo '	<h2>' . esc_html( sober_get_option( 'product_side_products_title' ) ) . '</h2>';
		echo '	<ul class="products ' . esc_attr( str_replace( '_', '-', $type ) ) . '">';

		foreach ( $query->posts as $product_id ) {
			$product_id  = is_numeric( $product_id ) ? $product_id : $product_id->get_id();
			$post_object = get_post( $product_id );

			setup_postdata( $GLOBALS['post'] =& $post_object );
			wc_get_template_part( 'content', 'product' );
		}

		wp_reset_postdata();

		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Add classes to product gallery.
	 */
	public function product_gallery_classes( $classes ) {
		if ( sober_product_has_video() ) {
			$classes[] = 'woocommerce-product-gallery--has-video';
		}

		return $classes;
	}

	/**
	 * Init hooks to support plugin WC Bundles by SomewhereWarm, the official plugin.
	 *
	 * @return void
	 */
	protected function support_wc_bundles_plugin() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_wc_bundles_scripts' ), 999 );
		add_action( 'woocommerce_bundles_add_to_cart_button', array( $this, 'add_to_wishlist_button' ), 20 );
	}

	/**
	 * Load stylesheets and scripts of WC Bundles plugins on the frontend to support theme's quick-view.
	 *
	 * @return void
	 */
	public function load_wc_bundles_scripts() {
		wp_enqueue_style( 'wc-bundle-css' );
		wp_enqueue_script( 'wc-add-to-cart-bundle' );
	}

	/**
	 * Support WCBoost Products Compare plugin.
	 *
	 * @return void
	 */
	public function support_compare_plugin() {
		$plugin_frontend = \WCBoost\ProductsCompare\Frontend::instance();

		remove_action( 'woocommerce_after_shop_loop_item', array( $plugin_frontend, 'loop_add_to_compare_button' ), 15 );

		add_filter( 'wcboost_products_compare_svg_icon', array( $this, 'compare_svg_icon' ), 10, 2 );
	}

	/**
	 * Change SVG icon for  WCBoost Products Compare components.
	 *
	 * @param  string $svg
	 * @param  string $icon
	 *
	 * @return string
	 */
	public function compare_svg_icon( $svg, $icon ) {
		if ( 'close' == $icon ) {
			$svg = sober_svg_icon( 'icon=close-delete&echo=0' );
		}

		return $svg;
	}

	/**
	 * Ajax add to cart handler on single product pages.
	 *
	 * @return void
	 */
	public function ajax_add_to_cart() {
		// Disable redirecting.
		remove_all_filters( 'woocommerce_add_to_cart_redirect' );
		add_filter( 'pre_option_woocommerce_cart_redirect_after_add', '__return_null' );

		// Correct the 'add-to-cart' param.
		if ( isset( $_REQUEST['sober-add-to-cart'] ) ) {
			$_REQUEST['add-to-cart'] = $_REQUEST['sober-add-to-cart'];
			unset( $_REQUEST['sober-add-to-cart'] );
		}

		WC_Form_Handler::add_to_cart_action();

		// Support Facebook Pixel tracking.
		if ( class_exists( 'WC_Facebookcommerce_Pixel' ) ) {
			$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : $_REQUEST['add-to-cart'];
			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		}

		// Send the ajax response.
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ajax_add_to_cart_notices' ) );
		WC_AJAX::get_refreshed_fragments();
	}

	/**
	 * Add notices to ajax add to cart fragments on single product pages.
	 *
	 * @param array $fragments
	 * @return array
	 */
	public function ajax_add_to_cart_notices( $fragments ) {
		$fragments['notices_html'] = wc_print_notices( true );

		return $fragments;
	}

	/**
	 * Filter function to correct the is_filtered function with filters of the theme.
	 */
	public function is_filtered( $is_filtered ) {
		if ( isset( $_GET['filter'] ) ) {
			$is_filtered = true;
		}

		return $is_filtered;
	}

	/**
	 * Sticky cart form
	 */
	public static function sticky_add_to_cart() {
		global $product;

		if ( 'bundle' === $product->get_type() ) {
			return;
		}

		$position = sober_get_option( 'product_sticky_addtocart' );
		?>
		<div class="sticky-cart-form sticky-cart-form--<?php echo esc_attr( $position ) ?>" data-position="<?php echo esc_attr( $position ) ?>" aria-hidden="true">
			<div class="sober-container sticky-cart-form__container">
				<div class="sticky-cart-form__product-summary">
					<?php
					$post_thumbnail_id = $product->get_image_id();
					$thumbnail_size    = apply_filters( 'sober_sticky_add_to_cart_image_size', 'gallery_thumbnail' );

					if ( $post_thumbnail_id ) {
						$thumbnail_src     = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
						$alt_text          = trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
					} else {
						$thumbnail_src     = wc_placeholder_img_src( $thumbnail_size );
						$alt_text          = esc_html__( 'Awaiting product image', 'woocommerce' );
					}

					printf( '<div class="sticky-cart-form__product-image"><img src="%s" alt="%s" data-o_src="%s"></div>', esc_url( $thumbnail_src[0] ), esc_attr( $alt_text ), esc_url( $thumbnail_src[0] ) );
					the_title( '<p class="sticky-cart-form__product-title">', '</p>' );
					woocommerce_template_single_price();
					?>
				</div>

				<?php woocommerce_template_single_add_to_cart(); ?>

				<button type="button" class="button sticky-cart-form__mobile-button" data-product_type="<?php echo esc_attr( $product->get_type() ) ?>">
					<?php echo esc_html( $product->add_to_cart_text() ); ?>
				</button>
			</div>
		</div>
		<?php
	}
}

/**
 * Change the gallery thumbnail size.
 *
 * @param array $size Image size.
 * @return array
 */
function sober_woocommerce_gallery_thumbnail_size( $size ) {
	$size['width'] = 80;
	$size['height'] = 100;
	$size['crop'] = 1;

	return $size;
}

add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'sober_woocommerce_gallery_thumbnail_size' );
