<?php
namespace WCBoost\ProductsCompare;

defined( 'ABSPATH' ) || exit;

/**
 * Customizer settings class
 */
class Customizer {

	const SECTION_ID = 'wcboost_products_compare';

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'customize_register', [ $this, 'register' ], 20 );
	}

	/**
	 * Add settings to the customizer.
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @return void
	 */
	public function register( $wp_customize ) {
		$this->register_section( $wp_customize );
		$this->register_settings( $wp_customize );
	}

	/**
	 * Register the section inside the Customizer.
	 *
	 * @param  \WP_Customize_Manager $wp_customize
	 * @return void
	 */
	private function register_section( $wp_customize ) {
		if ( ! $wp_customize->get_panel( 'woocommerce' ) ) {
			$this->maybe_register_panel( $wp_customize );
			$panel = 'wcboost';
		} else {
			$panel = 'woocommerce';
		}

		$wp_customize->add_section( self::SECTION_ID, [
			'title'    => __( 'Products Compare', 'wcboost-products-compare' ),
			'priority' => 40,
			'panel'    => $panel,
		] );
	}

	/**
	 * Register settings in the Customizer.
	 *
	 * @param  \WP_Customize_Manager $wp_customize
	 * @return void
	 */
	private function register_settings( $wp_customize ) {
		$wp_customize->add_setting(
			'wcboost_products_compare_button_icon',
			[
				'default'    => wc_get_theme_support( 'products_compare::button_icon', 'arrows' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_button_icon',
			[
				'label'       => __( 'Button icon', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'select',
				'choices'     => [
					''             => __( 'No icon', 'wcboost-products-compare' ),
					'arrows'       => __( 'Arrows', 'wcboost-products-compare' ),
					'plus'         => __( 'Plus', 'wcboost-products-compare' ),
					'square'       => __( 'Checkbox', 'wcboost-products-compare' ),
					'code-compare' => __( 'Code compare', 'wcboost-products-compare' ),
				],
			]
		);

		$wp_customize->add_setting(
			'wcboost_products_compare_button_text[add]',
			[
				'default'    => __( 'Compare', 'wcboost-products-compare' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_setting(
			'wcboost_products_compare_button_text[remove]',
			[
				'default'    => __( 'Remove compare', 'wcboost-products-compare' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_setting(
			'wcboost_products_compare_button_text[view]',
			[
				'default'    => __( 'Browse compare', 'wcboost-products-compare' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_button_text[add]',
			[
				'label'       => __( 'Button text', 'wcboost-products-compare' ),
				'description' => __( 'Button add', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'text',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_button_text[remove]',
			[
				'description' => __( 'Button remove', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'text',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_button_text[view]',
			[
				'description' => __( 'Button view', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'text',
			]
		);

		$wp_customize->add_setting(
			'wcboost_products_compare_bar',
			[
				'default'    => '',
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_bar',
			[
				'label'       => __( 'Compare Bar', 'wcboost-products-compare' ),
				'description' => __( 'Display a bar of comparing products', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'select',
				'choices'     => [
					''       => __( 'Disable', 'wcboost-products-compare' ),
					'bottom' => __( 'Display at bottom', 'wcboost-products-compare' ),
				],
			]
		);

		$wp_customize->add_setting(
			'wcboost_products_compare_bar_button_behavior',
			[
				'default'    => 'page',
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_products_compare_bar_button_behavior',
			[
				'label'       => __( 'Bar button behavior', 'wcboost-products-compare' ),
				'section'     => self::SECTION_ID,
				'type'        => 'radio',
				'choices'     => [
					'page'  => __( 'Open compare page', 'wcboost-products-compare' ),
					'popup' => __( 'Open the compage popup', 'wcboost-products-compare' ),
				],
			]
		);
	}

	/**
	 * Check and register the WCBoost panel if WooCommerce panel doesn't exist.
	 *
	 * @param  \WP_Customize_Manager $wp_customize
	 * @return void
	 */
	private function maybe_register_panel( $wp_customize ) {
		if ( ! $wp_customize->get_panel( 'wcboost' ) ) {
			$wp_customize->add_panel( 'wcboost', [
				'priority'   => 200,
				'capability' => 'manage_woocommerce',
				'title'      => __( 'WCBoost', 'wcboost' ),
			] );
		}
	}
}

new Customizer();
