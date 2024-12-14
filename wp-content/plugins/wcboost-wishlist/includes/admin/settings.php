<?php
namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\WC_Settings_Page' ) ) {
	include_once WC_ABSPATH . 'includes/admin/settings/class-wc-settings-page.php';
}

class Settings extends \WC_Settings_Page {

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->id = 'wcboost_wishlist';
		$this->label = __( 'Wishlist', 'wcboost-wishlist' );

		// Add settings of endpoints to the Advanced tab.
		add_filter( 'woocommerce_settings_pages', [ $this, 'get_endpoint_settings' ] );

		parent::__construct();
	}

	/**
	 * Get settings for the default section.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$exclude_pages = apply_filters( 'wcboost_wishlist_page_id_option_exclude', [
			wc_get_page_id( 'checkout' ),
			wc_get_page_id( 'myaccount' ),
			wc_get_page_id( 'cart' ),
		] );

		$settings = [
			// General section.
			[
				'type'  => 'title',
				'title' => __( 'General', 'wcboost-wishlist' ),
				'desc'  => __( 'This section controls how the wishlist work.', 'wcboost-wishlist' ),
				'id'    => 'wcboost_wishlist_general_section',
			],
			[
				'name'    => __( 'Guest wishlists', 'wcboost-wishlist' ),
				'desc'    => __( 'Allow guests to create wishlists', 'wcboost-wishlist' ),
				'type'    => 'checkbox',
				'id'      => 'wcboost_wishlist_enable_guest_wishlist',
				'default' => 'yes',
			],
			[
				'name'    => __( 'Guest behaviour', 'wcboost-wishlist' ),
				'id'      => 'wcboost_wishlist_guest_behaviour',
				'default' => 'redirect_to_account_page',
				'type'    => 'select',
				'options' => [
					'redirect_to_account_page' => __( 'Redirect to My Account page', 'wcboost-wishlist' ),
					'show_message'             => __( 'Display a message', 'wcboost-wishlist' ),
				],
			],
			[
				'name'    => __( 'Guest message', 'wcboost-wishlist' ),
				'id'      => 'wcboost_wishlist_guest_message',
				'default' => __( 'You need to login to add products to your wishlist', 'wcboost-wishlist' ),
				'type'    => 'textarea',
				'css'     => 'min-width: 50%; height: 75px;',
			],
			[
				'name'          => __( 'Merge guest wishlist', 'wcboost-wishlist' ),
				'desc'          => __( 'Allow merging guest wishlist after a user login', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_merge_guest_wishlist',
				'default'       => 'yes',
				'checkboxgroup' => 'start',
			],
			[
				'desc'          => __( 'Automatically empty guest wishlist after merging', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_clear_guest_wishlist_after_merge',
				'default'       => 'yes',
				'checkboxgroup' => 'end',
			],
			[
				'name'    => __( 'Add variations', 'wcboost-wishlist' ),
				'desc'    => __( 'Allow adding variations to the wishlist', 'wcboost-wishlist' ),
				'type'    => 'checkbox',
				'id'      => 'wcboost_wishlist_allow_adding_variations',
				'default' => 'no',
			],
			[
				'name'    => __( 'AJAX loading', 'wcboost-wishlist' ),
				'desc'    => __( 'Loads wishlist items via AJAX to bypass the cache', 'wcboost-wishlist' ),
				'type'    => 'checkbox',
				'id'      => 'wcboost_wishlist_ajax_bypass_cache',
				'default' => 'no',
			],
			[
				'name'    => __( 'Auto removal', 'wcboost-wishlist' ),
				'desc'    => __( 'Automatically delete products from the wishlist', 'wcboost-wishlist' ),
				'type'    => 'select',
				'id'      => 'wcboost_wishlist_auto_remove',
				'default' => '',
				'options' => [
					''             => __( 'Never', 'wcboost-wishlist' ),
					'on_addtocart' => __( 'On added to cart', 'wcboost-wishlist' ),
					'on_checkout'  => __( 'On checkout', 'wcboost-wishlist' ),
				],
			],
			[
				'type' => 'sectionend',
				'id'   => 'wcboost_wishlist_general_section',
			],
			  // Button section.
			[
				'type'  => 'title',
				'title' => __( 'Wishlist button', 'wcboost-wishlist' ),
				  /* translators: %s: URL to the Customizer section */
				'desc' => wp_kses( sprintf( __( 'This section controls how the wishlist button is displayed and worked. Some visual settings can be configured in the <a href="%s" target="_blank">Customizer</a>.', 'wcboost-wishlist' ), esc_url( admin_url( 'customize.php?autofocus[section]=wcboost_wishlist_button' ) ) ), [ 'a' => [ 'href' => true, 'target' => true ] ] ),
				'id'   => 'wcboost_wishlist_button_section',
			],
			[
				'name'          => __( 'Add to wishlist behaviour', 'wcboost-wishlist' ),
				'desc'          => __( 'Redirect to the wishlist page after successful addition', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_redirect_after_add',
				'default'       => 'no',
				'checkboxgroup' => 'start',
			],
			[
				'desc'          => __( 'Enable AJAX add to wishlist', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_enable_ajax_add_to_wishlist',
				'default'       => 'yes',
				'checkboxgroup' => 'end',
			],
			[
				'name'    => __( 'Button display for exists items', 'wcboost-wishlist' ),
				'desc'    => __( 'Choose how to display the button with products that are already in the wishlist.', 'wcboost-wishlist' ),
				'id'      => 'wcboost_wishlist_exists_item_button_behaviour',
				'default' => 'view_wishlist',
				'type'    => 'select',
				'options' => [
					'hide'          => __( 'Hide button', 'wcboost-wishlist' ),
					'view_wishlist' => __( 'View wishlist', 'wcboost-wishlist' ),
					'remove'        => __( 'Remove from wishlist', 'wcboost-wishlist' ),
				],
			],
			'single_button_position' => [
				'name'     => __( 'Button position', 'wcboost-wishlist' ),
				'desc'     => __( 'Choose where to show "Add to wishlist" button on the product page.', 'wcboost-wishlist' ),
				'desc_tip' => __( 'Manually use the button shortcode: [wcboost_wishlist_button]', 'wcboost-wishlist' ),
				'id'       => 'wcboost_wishlist_single_button_position',
				'default'  => wc_get_theme_support( 'wishlist::single_button_position', 'after_add_to_cart' ),
				'type'     => 'select',
				'options'  => [
					'before_add_to_cart' => __( 'Before "Add to cart" button', 'wcboost-wishlist' ),
					'after_add_to_cart'  => __( 'After "Add to cart" button', 'wcboost-wishlist' ),
					'after_title'        => __( 'After product name', 'wcboost-wishlist' ),
					'after_excerpt'      => __( 'After product short description', 'wcboost-wishlist' ),
					'manual'             => __( 'Manually use shortcode', 'wcboost-wishlist' ),
				],
			],
			'loop_button_position' => [
				'name'     => __( 'Button in loop', 'wcboost-wishlist' ),
				'desc'     => __( 'Choose where to show "Add to wishlist" button on the product catalog pages.', 'wcboost-wishlist' ),
				'desc_tip' => __( 'Manually use the button shortcode: [wcboost_wishlist_button]', 'wcboost-wishlist' ),
				'id'       => 'wcboost_wishlist_loop_button_position',
				'default'  => wc_get_theme_support( 'wishlist::loop_button_position', '' ),
				'type'     => 'select',
				'options'  => [
					''                   => __( 'Hide the button', 'wcboost-wishlist' ),
					'before_add_to_cart' => __( 'Before "Add to cart" button', 'wcboost-wishlist' ),
					'after_add_to_cart'  => __( 'After "Add to cart" button', 'wcboost-wishlist' ),
					'manual'             => __( 'Manually use shortcode', 'wcboost-wishlist' ),
				],
			],
			[
				'type' => 'sectionend',
				'id'   => 'wcboost_wishlist_button_section',
			],
			// Wishlist page.
			[
				'type'  => 'title',
				'title' => __( 'Wishlist Page', 'wcboost-wishlist' ),
				/* translators: %s: URL to the Customizer section */
				'desc' => wp_kses( sprintf( __( 'This section controls how the wishlist page is displayed. Some visual settings can be configured in the <a href="%s" target="_blank">Customizer</a>.', 'wcboost-wishlist' ), esc_url( admin_url( 'customize.php?autofocus[section]=wcboost_wishlist_page' ) ) ), [ 'a' => [ 'href' => true, 'target' => true ] ] ),
				'id'   => 'wcboost_wishlist_page_section',
			],
			[
				'name'     => __( 'Page', 'wcboost-wishlist' ),
				'desc_tip' => __( 'Page content: [wcboost_wishlist]', 'wcboost-wishlist' ),
				'type'     => 'single_select_page_with_search',
				'id'       => 'wcboost_wishlist_page_id',
				'default'  => '',
				'class'    => 'wc-page-search',
				'css'      => 'min-width:300px;',
				'autoload' => false,
				'args'     => [
					// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
					'exclude' => $exclude_pages,
				],
			],
			[
				'desc'    => __( 'The default wishlist title', 'wcboost-wishlist' ),
				'type'    => 'text',
				'id'      => 'wcboost_wishlist_title_default',
				'default' => esc_html__( 'My Wishlist', 'wcboost-wishlist' ),
			],
			[
				'name'          => __( 'Table columns', 'wcboost-wishlist' ),
				'desc'          => __( 'Product price', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_table_columns[price]',
				'checkboxgroup' => 'start',
				'default'       => 'yes',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Stock status', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_table_columns[stock]',
				'checkboxgroup' => '',
				'default'       => 'yes',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Quantity', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_table_columns[quantity]',
				'checkboxgroup' => '',
				'default'       => 'no',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Added date', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_table_columns[date]',
				'checkboxgroup' => '',
				'default'       => 'no',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Add to cart', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_table_columns[purchase]',
				'checkboxgroup' => 'end',
				'default'       => 'yes',
				'autoload'      => false,
			],
			[
				'name'     => __( 'Social share', 'wcboost-wishlist' ),
				'desc'     => __( 'Show sharing buttons at the bottom of wishlist', 'wcboost-wishlist' ),
				'desc_tip' => __( 'These buttons will be displayed on public and shareable wishlists only', 'wcboost-wishlist' ),
				'type'     => 'checkbox',
				'id'       => 'wcboost_wishlist_share',
				'default'  => 'yes',
			],
			[
				'name'          => __( 'Social media', 'wcboost-wishlist' ),
				'desc'          => __( 'Facebook', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[facebook]',
				'default'       => 'yes',
				'checkboxgroup' => 'start',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Twitter', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[twitter]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Linkedin', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[linkedin]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Tumble', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[tumblr]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Reddit', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[reddit]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'StumbleUpon', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[stumbleupon]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Telegram', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[telegram]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Whatsapp', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[whatsapp]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Pocket', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[pocket]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Digg', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[digg]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'VK', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[vk]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Email', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[email]',
				'default'       => 'yes',
				'checkboxgroup' => '',
				'autoload'      => false,
			],
			[
				'desc'          => __( 'Copy link', 'wcboost-wishlist' ),
				'type'          => 'checkbox',
				'id'            => 'wcboost_wishlist_share_socials[link]',
				'default'       => 'yes',
				'checkboxgroup' => 'end',
				'autoload'      => false,
			],
			[
				'type' => 'sectionend',
				'id'   => 'wcboost_wishlist_page_section',
			],
		];

		if ( 'theme' === wc_get_theme_support( 'wishlist::single_button_position' ) ) {
			unset( $settings['single_button_position'] );
		}

		if ( 'theme' === wc_get_theme_support( 'wishlist::loop_button_position' ) ) {
			unset( $settings['loop_button_position'] );
		}

		return apply_filters( 'wcboost_wishlist_settings', $settings );
	}

	/**
	 * Add endpoint settings to the Settings > Advanced > Page setup.
	 *
	 * @param array $settings
	 * @return array
	 */
	public function get_endpoint_settings( $settings ) {
		$endpoint_settings = [
			[
				'type'  => 'title',
				'title' => __( 'Wishlist Endpoints', 'wcboost-wishlist' ),
				'desc'  => __( 'Endpoints are appended to the page URL to handle specific actions on the wishlist page. They should be unique.', 'wcboost-wishlist' ),
				'id'    => 'wcboost_wishlist_enpoints_section',
			],
			[
				'title'    => __( 'Edit wishlist', 'wcboost-wishlist' ),
				'desc'     => __( 'Endpoint for the "Wishlist &rarr; Edit wishlist" page.', 'wcboost-wishlist' ),
				'id'       => 'wcboost_wishlist_edit_endpoint',
				'type'     => 'text',
				'default'  => 'edit-wishlist',
				'desc_tip' => true,
			],
			[
				'type' => 'sectionend',
				'id'   => 'wcboost_wishlist_enpoints_section',
			],
		];

		return array_merge( $settings, $endpoint_settings );
	}
}
