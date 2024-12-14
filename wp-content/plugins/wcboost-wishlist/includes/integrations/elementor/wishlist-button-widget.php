<?php
namespace WCBoost\Wishlist\Integrations\Elementor;

defined( 'ABSPATH' ) || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use \WCBoost\Wishlist\Shortcodes;

class Wishlist_Button_Widget extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve wishlist button widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wcboost_wishlist_button';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve wishlist button widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Wishlist Button', 'wcboost-wishlist' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve wishlist button widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-heart wcboost-elementor-widget-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the wishlist button widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wcboost' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the wishlist button widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'wishlist', 'favourite', 'like', 'button', 'wcboost' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Wishlist button', 'wcboost-wishlist' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'product_id',
			[
				'label' => __( 'Product ID', 'wcboost-wishlist' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to use the current product.', 'wcboost-wishlist' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wishlist_button_style',
			[
				'label' => esc_html__( 'Wishlist button', 'wcboost-wishlist' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .wcboost-wishlist-button',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render wishlist button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Temporary product ID to preview the button in the editor.
		$product_id = ! empty( $settings['product_id'] ) ? $settings['product_id'] : ( ! empty( $GLOBALS['product'] ) ? $GLOBALS['product']->get_id() : 0 );

		if ( ( ! $product_id || ! wc_get_product( $product_id ) ) && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$products   = get_posts( 'fields=ids&posts_per_page=1&post_type=product' );
			$product_id = current( $products );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Shortcodes::button( [ 'product_id' => absint( $product_id ) ] );
	}
}