<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Subsribe Box widget
 */
class Subscribe_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-subscribe-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Subscribe Box', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-mailchimp';
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
		return [ 'newsletter', 'subscribe', 'mailchimp', 'sober' ];
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
			'section_subscribe_box',
			[ 'label' => __( 'Subscribe Box', 'sober' ) ]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sober' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Join our list', 'sober' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Signup to be the first to hear about exclusive deals', 'sober' )
			]
		);

		$forms = $this->get_form_options();
		$form_ids = array_keys( $forms );

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => current( $form_ids ),
				'options' => $forms,
			]
		);

		$this->add_control(
			'form_style',
			[
				'label' => __( 'Style', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'default' => __( 'Default', 'sober' ),
					'inline' => __( 'Inline', 'sober' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'sober' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .sober-subscribe-box__title',
			]
		);

		$this->add_control(
			'desc_heading',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .sober-subscribe-box__desc',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form_style',
			[
				'label' => __( 'Form', 'sober' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_style' => 'default',
				],
			]
		);

		$this->add_control(
			'fields_heading',
			[
				'label' => __( 'Fields', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .sober-subscribe-box__form input[type="text"], {{WRAPPER}} .sober-subscribe-box__form input[type="email"], {{WRAPPER}} .sober-subscribe-box__form textarea, {{WRAPPER}} .sober-subscribe-box__form select',
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label' => __( 'Button', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_button_color' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'sober' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__form button, {{WRAPPER}} .sober-subscribe-box__form input[type="submit"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Button Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__form button, {{WRAPPER}} .sober-subscribe-box__form input[type="submit"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'sober' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__form button, {{WRAPPER}} .sober-subscribe-box__form input[type="submit"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => __( 'Button Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-subscribe-box__form button, {{WRAPPER}} .sober-subscribe-box__form input[type="submit"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get list of MailChimp forms
	 *
	 * @return array
	 */
	protected function get_form_options() {
		$forms = get_posts( array( 'post_type' => 'mc4wp-form', 'numberposts' => -1 ));
		$options = [];

		foreach( $forms as $form ) {
			$options[$form->ID] = $form->post_title . " - ID: $form->ID";
		}

		return $options;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo \Sober_Shortcodes::subscribe_box( [
			'title'      => $settings['title'],
			'form_id'    => $settings['form_id'],
			'form_style' => $settings['form_style'],
			'el_class'   => 'sober-subscribe-box--elementor',
		], $settings['description'] );
	}
}
