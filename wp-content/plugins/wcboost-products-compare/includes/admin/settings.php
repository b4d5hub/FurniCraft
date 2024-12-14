<?php
namespace WCBoost\ProductsCompare\Admin;

/**
 * Products compare settings
 */
class Settings {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_products_general_settings', [ $this, 'register_settings' ], 20 );
	}

	/**
	 * Register settings to the section General of Products tab.
	 *
	 * @param  array $settings
	 * @return array
	 */
	public function register_settings( $settings ) {
		$exclude_pages = apply_filters( 'wcboost_products_compare_page_id_option_exclude', [
			wc_get_page_id( 'checkout' ),
			wc_get_page_id( 'myaccount' ),
			wc_get_page_id( 'cart' ),
		] );

		$compare_settings = [
			[
				'title' => __( 'Products Compare', 'wcboost-products-compare' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'wcboost_products_compare_options',
			],
			[
				'name'     => __( 'Compare page', 'wcboost-products-compare' ),
				'desc_tip' => __( 'Page content: [wcboost_compare]', 'wcboost-products-compare' ),
				'type'     => 'single_select_page_with_search',
				'id'       => 'wcboost_products_compare_page_id',
				'default'  => '',
				'class'    => 'wc-page-search',
				'css'      => 'min-width:300px;',
				'autoload' => false,
				'args'     => [
					'exclude' => $exclude_pages,
				],
			],
			[
				'name'    => __( 'Added to compare behaviour', 'wcboost-products-compare' ),
				'type'    => 'radio',
				'id'      => 'wcboost_products_compare_added_behavior',
				'default' => '',
				'options' => [
					''         => __( 'No additional action', 'wcboost-products-compare' ),
					'redirect' => __( 'Redirect to the compare page', 'wcboost-products-compare' ),
					'popup'    => __( 'Open the compare popup', 'wcboost-products-compare' ),
				],
			],
			[
				'name'    => __( 'Existing products behaviour', 'wcboost-products-compare' ),
				'desc'    => __( 'Select how the button work with products that are already in the compare list', 'wcboost-products-compare' ),
				'type'    => 'select',
				'id'      => 'wcboost_products_compare_exists_item_button_behaviour',
				'default' => 'remove',
				'options' => [
					'remove' => __( 'Remove from the compare list', 'wcboost-products-compare' ),
					'view'   => __( 'View the compare page', 'wcboost-products-compare' ),
					'popup'  => __( 'Open the compare popup', 'wcboost-products-compare' ),
					'hide'   => __( 'Hide the button', 'wcboost-products-compare' ),
				],
			],
			[
				'name'    => __( 'AJAX Loading', 'wcboost-products-compare' ),
				'desc'    => __( 'Load the list and buttons via AJAX to bypass the cache', 'wcboost-products-compare' ),
				'type'    => 'checkbox',
				'id'      => 'wcboost_products_compare_ajax_bypass_cache',
				'default' => 'no',
			],
			[
				'name'    => __( 'Comparision data tracking', 'wcboost-products-compare' ),
				'desc'    => __( 'Monitor user comparison statistics.', 'wcboost-products-compare' )
							. '<p class="description">' . __( 'This data gives insights into product performance, user interests, and is used to calculate similar products. Enabling this option may add additional meta data to products.', 'wcboost-products-compare' ) . '</p>',
				'type'    => 'checkbox',
				'id'      => 'wcboost_products_compare_tracking',
				'default' => 'yes',
			],
			[
				'type' => 'sectionend',
				'id'   => 'wcboost_products_compare_options',
			]
		];

		$settings = array_merge( $settings, $compare_settings );

		return $settings;
	}
}

new Settings();
