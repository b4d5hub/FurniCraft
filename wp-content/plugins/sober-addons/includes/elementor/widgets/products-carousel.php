<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Products carousel widget
 */
class Products_Carousel extends Products_Grid {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-products-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Products Carousel', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['sober'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'products carousel', 'products', 'carousel', 'woocommerce', 'sober' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_products',
			[ 'label' => __( 'Products', 'sober' ) ]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_product_types(),
				'default' => 'recent',
			]
		);

		$this->add_control(
			'category',
			[
				'label' => __( 'Category', 'sober' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_product_categories(),
				'default' => '',
				'multiple' => true,
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => __( 'Tags', 'sober' ),
				'description' => __( 'Product tags, separate by commas', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_product_orderby(),
				'default' => 'menu_order',
				'condition' => [
					'type' => ['featured', 'sale']
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => __( 'Ascending', 'sober' ),
					'DESC' => __( 'Descending', 'sober' ),
				],
				'default' => 'ASC',
				'condition' => [
					'type' => ['featured', 'sale'],
					'orderby!' => ['', 'rand'],
				],
			]
		);

		$this->add_control(
			'limit',
			[
				'label'     => __( 'Number Of Products', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 12,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel',
			[ 'label' => __( 'Carousel', 'sober' ) ]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'   => __( 'Products to Show', 'sober' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				],
				'desktop_default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'sober' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'sober' ),
				'label_off'    => __( 'No', 'sober' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'        => __( 'Autoplay Speed (ms)', 'sober' ),
				'type'         => Controls_Manager::NUMBER,
				'default'      => 5000,
				'condition'    => [
					'autoplay' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => __( 'Infinite Loop', 'sober' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'sober' ),
				'label_off'    => __( 'No', 'sober' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = [
			'type'              => $settings['type'],
			'per_page'          => $settings['limit'],
			'columns'           => $settings['columns'],
			'category'          => is_array( $settings['category'] ) ? implode( ',', $settings['category'] ): $settings['category'],
			'tag'               => $settings['tag'],
			'orderby'           => $settings['orderby'],
			'order'             => $settings['order'],
			'autoplay'          => $settings['autoplay'] ? intval( $settings['autoplay_speed'] ):             '',
			'loop'              => $settings['loop'],
			'auto_responsive'   => false,
			'responsive_mobile' => $settings['columns_mobile'],
			'responsive_tablet' => $settings['columns_tablet'],
			'el_class'          => 'sober-product-carousel--elementor',
		];

		echo \Sober_Shortcodes::product_carousel( $atts );
	}
}
