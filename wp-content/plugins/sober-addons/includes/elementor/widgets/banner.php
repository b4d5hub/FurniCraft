<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Breakpoints\Manager as BreakpointsManager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner widget
 */
class Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Banner Image', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-rollover';
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
		return [ 'banner', 'image', 'sober' ];
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
			'section_banner_image',
			[ 'label' => __( 'Image', 'sober' ) ]
		);

		$this->add_control(
			'image_source',
			[
				'label' => __( 'Image Source', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media',
				'options' => [
					'media'    => __( 'Media Library', 'sober' ),
					'external' => __( 'External Image', 'sober' ),
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src()
				],
				'condition' =>  [
					'image_source' => 'media',
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' =>  [
					'image_source' => 'external',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'condition' =>  [
					'image_source' => 'media',
				],
			]
		);

		$this->add_control(
			'image_hover',
			[
				'label' => __( 'Hover Effect', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => [
					'none'     => __( 'None', 'sober' ),
					'zoom'     => __( 'Zoom In', 'sober' ),
					'box'      => __( 'Overlay Box', 'sober' ),
					'zoom_box' => __( 'Zoom in & Overlay Box', 'sober' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_banner_content',
			[ 'label' => __( 'Content', 'sober' ) ]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Banner subtitle', 'sober' ),
				'placeholder' => __( 'Subtitle', 'sober' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Banner Title', 'sober' ),
				'placeholder' => __( 'Title', 'sober' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Banner description', 'sober' ),
				'placeholder' => __( 'Description', 'sober' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_banner_button',
			[ 'label' => __( 'Buttons', 'sober' ) ]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'sober' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'sober' ),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => __( 'Button Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'normal' => __( 'Normal', 'sober' ),
					'outline' => __( 'Outline', 'sober' ),
					'light' => __( 'Light', 'sober' ),
				],
			]
		);

		$this->add_control(
			'second_button',
			[
				'label' => __( 'Second Button', 'sober' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'sober' ),
				'label_off' => __( 'OFF', 'sober' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'second_button_text',
			[
				'label' => __( 'Button Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'sober' ),
				'condition' => [
					'second_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'second_link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'sober' ),
				'default' => [
					'url' => '#',
				],
				'condition' => [
					'second_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'second_button_type',
			[
				'label' => __( 'Button Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outline',
				'options' => [
					'normal' => __( 'Normal', 'sober' ),
					'outline' => __( 'Outline', 'sober' ),
					'light' => __( 'Light', 'sober' ),
				],
				'condition' => [
					'second_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'buttons_visible',
			[
				'label' => __( 'Buttons Visibility', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'default' => 'always',
				'options' => [
					'always' => __( 'Always', 'sober' ),
					'fadein' => __( 'Fadein on hover', 'sober' ),
					'fadeup' => __( 'Fadein-Up on hover', 'sober' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => __( 'Width & Position', 'sober' ),
			]
		);

		$this->add_control(
			'width_section_heading',
			[
				'label' => __( 'Content Width', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => __( 'Width', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => _x( 'Default', 'Banner image content width', 'sober' ),
					'custom' => _x( 'Custom', 'Banner image content width', 'sober' ),
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => 'width: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_custom_width',
			[
				'label' => __( 'Custom Width', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'content_width' => 'custom',
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'position_section_heading',
			[
				'label' => __( 'Content Position', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_position_x',
			[
				'label' => __( 'Horizontal Orientation', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'desktop_default' => 'left',
				'tablet_default' => 'left',
				'mobile_default' => 'left',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sober' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'sober' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => 'left: 0; right: auto;',
					'center' => 'left: 50%; right: auto',
					'right' => 'left: auto; right: 0;',
				],
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'content_position_y',
			[
				'label' => __( 'Vertical Orientation', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'desktop_default' => 'top',
				'tablet_default' => 'top',
				'mobile_default' => 'top',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'sober' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'sober' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'sober' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top' => 'top: 0; bottom: auto;',
					'middle' => 'top: 50%; bottom: auto',
					'bottom' => 'top: auto; bottom: 0;',
				],
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'content_position',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'absoulte',
				'desktop_default' => 'absoulte',
				'tablet_default' => 'absoulte',
				'mobile_default' => 'absoulte',
				'condition' => [
					'content_position_x!' => 'center',
					'content_position_y!' => 'middle',
				],
				'device_args' => $this->get_responsive_device_args( [
					'condition' => [
						'content_position_x!' => 'center',
						'content_position_y!' => 'middle',
					],
					'selectors' => [
						'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}}; transform: none;',
					],
				] ),
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_position_center_x',
			[
				'type' => Controls_Manager::HIDDEN,
				'desktop_default' => 'absoulte',
				'tablet_default' => 'absoulte',
				'mobile_default' => 'absoulte',
				'condition' => [
					'content_position_x' => 'center',
					'content_position_y!' => 'middle',
				],
				'device_args' => $this->get_responsive_device_args( [
					'condition' => [
						'content_position_x' => 'center',
						'content_position_y!' => 'middle',
					],
					'selectors' => [
						'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}}; transform: translate(-50%,0)',
					],
				] ),
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}}; transform: translate(-50%,0)',
				],
			]
		);

		$this->add_responsive_control(
			'content_position_center_y',
			[
				'type' => Controls_Manager::HIDDEN,
				'desktop_default' => 'absoulte',
				'tablet_default' => 'absoulte',
				'mobile_default' => 'absoulte',
				'condition' => [
					'content_position_x!' => 'center',
					'content_position_y' => 'middle',
				],
				'device_args' => $this->get_responsive_device_args( [
					'condition' => [
						'content_position_x!' => 'center',
						'content_position_y' => 'middle',
					],
					'selectors' => [
						'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}}; transform: translate(0,-50%)',
					],
				] ),
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => '{{content_position_x.VALUE}}; {{content_position_y.VALUE}}; transform: translate(0,-50%)',
				],
			]
		);

		$this->add_responsive_control(
			'content_position_center',
			[
				'type' => Controls_Manager::HIDDEN,
				'desktop_default' => 'center',
				'tablet_default' => 'center',
				'mobile_default' => 'center',
				'condition' => [
					'content_position_x' => 'center',
					'content_position_y' => 'middle',
				],
				'device_args' => $this->get_responsive_device_args( [
					'condition' => [
						'content_position_x' => [ 'center' ],
						'content_position_y' => [ 'middle' ],
					],
				] ),
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => 'top: 50%; left: 50%; bottom: auto; transform: translate(-50%,-50%)',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'sober' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 40,
					'right' => 40,
					'bottom' => 40,
					'left' => 40,
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Text Align', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'desktop_default' => '',
				'tablet_default' => '',
				'mobile_default' => '',
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sober' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'sober' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'sober' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtitle_style_section_heading',
			[
				'label' => __( 'Subtitle', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .sober-banner-image__subtitle',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '12',
						],
					],
					'font_weight' => [
						'default' => '600',
					],
					'text_transform' => [
						'default' => 'uppercase',
					]
				],
			]
		);

		$this->add_responsive_control(
			'subtitle_space',
			[
				'label' => __( 'Bottom Space', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'title_style_section_heading',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .sober-banner-image__title',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '30',
						],
					],
					'font_weight' => [
						'default' => '300',
					],
				],
			]
		);

		$this->add_responsive_control(
			'title_space',
			[
				'label' => __( 'Bottom Space', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'description_style_section_heading',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .sober-banner-image__description',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '12',
						],
					],
					'font_weight' => [
						'default' => '400',
					],
				],
			]
		);

		$this->add_responsive_control(
			'description_space',
			[
				'label' => __( 'Bottom Space', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Buttons', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'sober' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Button Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main.sober-banner-image__button--normal' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => 'normal',
				],
			]
		);

		$this->add_control(
			'second_button_text_color',
			[
				'label' => __( 'Second Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--second' => 'color: {{VALUE}};',
				],
				'condition' => [
					'second_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'second_button_background_color',
			[
				'label' => __( 'Second Button Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--second.sober-banner-image__button--normal' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'second_button' => 'yes',
					'second_button_type' => 'normal',
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
			'button_text_hover_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __( 'Button Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main.sober-banner-image__button--normal:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sober-banner-image__button--main.sober-banner-image__button--outline:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => ['normal', 'outline'],
				],
			]
		);

		$this->add_control(
			'second_button_text_hover_color',
			[
				'label' => __( 'Second Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--second:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'second_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'second_button_background_hover_color',
			[
				'label' => __( 'Second Button Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--second.sober-banner-image__button--normal:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sober-banner-image__button--second.sober-banner-image__button--outline:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'second_button' => 'yes',
					'second_button_type' => ['normal', 'outline'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_style_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Button Typography', 'sober' ),
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .sober-banner-image__button--main',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '12',
						],
					],
					'font_weight' => [
						'default' => '600',
					],
					'text_transform' => [
						'default' => 'uppercase',
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Second Button Typography', 'sober' ),
				'name' => 'second_button_typography',
				'selector' => '{{WRAPPER}} .sober-banner-image__button--second',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '12',
						],
					],
					'font_weight' => [
						'default' => '600',
					],
					'text_transform' => [
						'default' => 'uppercase',
					],
				],
				'condition' => [
					'second_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_padding_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Button Padding', 'sober' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 0,
					'right' => 30,
					'bottom' => 0,
					'left' => 30,
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main.sober-banner-image__button--outline, {{WRAPPER}} .sober-banner-image__button--main.sober-banner-image__button--normal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_type' => ['normal', 'outline'],
				],
			]
		);

		$this->add_responsive_control(
			'second_button_padding',
			[
				'label' => __( 'Second Button Padding', 'sober' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 0,
					'right' => 30,
					'bottom' => 0,
					'left' => 30,
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--second.sober-banner-image__button--outline, {{WRAPPER}} .sober-banner-image__button--second.sober-banner-image__button--normal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'second_button' => 'yes',
					'second_button_type' => ['normal', 'outline'],
				],
			]
		);

		$this->add_responsive_control(
			'buttons_space',
			[
				'label' => __( 'Buttons Space', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-image__button--main + .sober-banner-image__button--second' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'second_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get Responsive Device Args
	 *
	 * Receives an array of device args, and duplicates it for each active breakpoint.
	 * Returns an array of device args.
	 *
	 * @param array $args arguments to duplicate per breakpoint
	 * @param array $devices_to_exclude
	 *
	 * @return array responsive device args
	 */
	protected function get_responsive_device_args( array $args, array $devices_to_exclude = [] ) {
		$device_args = [];
		$breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();

		foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
			// If the device is not excluded, add it to the device args array.
			if ( ! in_array( $breakpoint_key, $devices_to_exclude, true ) ) {
				$parsed_device_args = $this->parse_device_args_placeholders( $args, $breakpoint_key );

				$device_args[ $breakpoint_key ] = $parsed_device_args;
			}
		}

		return $device_args;
	}

	/**
	 * Parse Device Args Placeholders
	 *
	 * Receives an array of args. Iterates over the args, and replaces the {{DEVICE}} placeholder, if exists, with the
	 * passed breakpoint key.
	 *
	 * @param array $args
	 * @param string $breakpoint_key
	 * @return array parsed device args
	 */
	private function parse_device_args_placeholders( array $args, $breakpoint_key ) {
		$parsed_args = [];

		foreach ( $args as $arg_key => $arg_value ) {
			$arg_key = str_replace( '{{DEVICE}}', $breakpoint_key, $arg_key );

			if ( is_array( $arg_value ) ) {
				$arg_value = $this->parse_device_args_placeholders( $arg_value, $breakpoint_key );
			}

			if ( is_string( $arg_value ) ) {
				$arg_value = str_replace( '{{DEVICE}}', $breakpoint_key, $arg_value );
			}

			$parsed_args[ $arg_key ] = $arg_value;
		}

		return $parsed_args;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'sober-banner-image',
			'sober-banner--elementor',
			'sober-banner-image--hover-' . $settings['image_hover'],
			'sober-banner-image--button-visible-' . $settings['buttons_visible'],

		] );

		if ( ! empty( $settings['content_position_x'] ) && ! empty( $settings['content_position_y'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-banner-image--content-' . $settings['content_position_x'] . '-' . $settings['content_position_y'] );
		}

		if ( ! empty( $settings['content_position_x_tablet'] ) && ! empty( $settings['content_position_y_tablet'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-banner-image--content-sm-' . $settings['content_position_x_tablet'] . '-' . $settings['content_position_y_tablet'] );
		}

		if ( ! empty( $settings['content_position_x_mobile'] ) && ! empty( $settings['content_position_y_mobile'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-banner-image--content-xs-' . $settings['content_position_x_mobile'] . '-' . $settings['content_position_y_mobile'] );
		}

		if ( 'fadeup' == $settings['buttons_visible'] && 'top' == $settings['content_position_y'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-banner-image--content-keep-top' );
		}

		$this->add_render_attribute( 'content', 'class', [ 'sober-banner-image__content' ] );

		$this->add_render_attribute( 'subtitle', 'class', [ 'sober-banner-image__subtitle' ] );
		$this->add_inline_editing_attributes( 'subtitle', 'none' );

		$this->add_render_attribute( 'title', 'class', [ 'sober-banner-image__title' ] );
		$this->add_inline_editing_attributes( 'title' );

		$this->add_render_attribute( 'description', 'class', [ 'sober-banner-image__description' ] );
		$this->add_inline_editing_attributes( 'description' );

		$button = $button2 = '';

		if ( 'yes' == $settings['second_button'] && ! empty( $settings['second_button_text'] ) ) {
			$this->add_render_attribute( 'button', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--main', 'sober-banner-image__button--' . $settings['button_type'] ] );

			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );

				if ( $settings['link']['is_external'] ) {
					$this->add_render_attribute( 'button', 'target', '_blank' );
				}

				if ( $settings['link']['nofollow'] ) {
					$this->add_render_attribute( 'button', 'rel', 'nofollow' );
				}
			}

			if ( ! empty( $settings['button_text'] ) ) {
				$button = '<a ' . $this->get_render_attribute_string( 'button' ) . '>' . esc_html( $settings['button_text'] ) . '</a>';
			}

			$this->add_render_attribute( 'button2', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--second', 'sober-banner-image__button--' . $settings['second_button_type'] ] );

			if ( ! empty( $settings['second_link']['url'] ) ) {
				$this->add_render_attribute( 'button2', 'href', $settings['second_link']['url'] );

				if ( $settings['second_link']['is_external'] ) {
					$this->add_render_attribute( 'button2', 'target', '_blank' );
				}

				if ( $settings['second_link']['nofollow'] ) {
					$this->add_render_attribute( 'button2', 'rel', 'nofollow' );
				}
			}

			if ( ! empty( $settings['second_button_text'] ) ) {
				$button2 = '<a ' . $this->get_render_attribute_string( 'button2' ) . '>' . esc_html( $settings['second_button_text'] ) . '</a>';
			}

			$wrapper_open = '<div class="sober-banner-image__wrapper">';
			$wrapper_close = '</div>';
		} else {
			$this->add_render_attribute( 'button', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--main', 'sober-banner-image__button--' . $settings['button_type'] ] );

			if ( ! empty( $settings['button_text'] ) ) {
				$button = '<span ' . $this->get_render_attribute_string( 'button' ) . '>' . esc_html( $settings['button_text'] ) . '</span>';
			}

			$this->add_render_attribute( 'link', 'class', 'sober-banner-image__link' );

			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );

				if ( $settings['link']['is_external'] ) {
					$this->add_render_attribute( 'link', 'target', '_blank' );
				}

				if ( $settings['link']['nofollow'] ) {
					$this->add_render_attribute( 'link', 'rel', 'nofollow' );
				}
			}

			$wrapper_open = '<a ' . $this->get_render_attribute_string( 'link' ) . '>';
			$wrapper_close = '</a>';
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php echo $wrapper_open; ?>
				<?php
				if ( 'external' == $settings['image_source'] ) {
					printf( '<img src="%s" alt="%s">', esc_url( $settings['image_url'] ), esc_attr( $settings['title'] ) );
				} else {
					echo Group_Control_Image_Size::get_attachment_image_html( $settings );
				}
				?>

				<div <?php echo $this->get_render_attribute_string( 'content' ) ?>>
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'subtitle' ) ?>><?php echo esc_html( $settings['subtitle'] ) ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'title' ) ?>><?php echo wp_kses_post( $settings['title'] ) ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'description' ) ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
					<?php endif; ?>

					<div class="sober-banner-image__buttons">
						<?php echo $button; ?>
						<?php echo $button2; ?>
					</div>
				</div>
			<?php echo $wrapper_close; ?>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		var image_url = '<?php echo Utils::get_placeholder_image_src(); ?>';

		if ( 'external' === settings.image_source ) {
			image_url = settings.image_url ? settings.image_url : image_url;
		} else if ( settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};

			image_url = elementor.imagesManager.getImageUrl( image );
		}

		view.addRenderAttribute( 'wrapper', 'class', [
			'sober-banner-image',
			'sober-banner--elementor',
			'sober-banner-image--hover-' + settings.image_hover,
			'sober-banner-image--button-visible-' + settings.buttons_visible,
			'sober-banner-image--content-' + settings.content_position_x + '-' + settings.content_position_y,
			'sober-banner-image--content-sm-' + settings.content_position_x_tablet + '-' + settings.content_position_y_tablet,
			'sober-banner-image--content-xs-' + settings.content_position_x_mobile + '-' + settings.content_position_y_mobile,
		] );

		if ( 'fadeup' === settings.buttons_visible && 'top' === settings.content_position_y ) {
			view.addRenderAttribute( 'wrapper', 'class', 'sober-banner-image--content-keep-top' );
		}

		view.addRenderAttribute( 'content', 'class', [ 'sober-banner-image__content' ] );

		view.addRenderAttribute( 'subtitle', 'class', [ 'sober-banner-image__subtitle' ] );
		view.addInlineEditingAttributes( 'subtitle', 'none' );

		view.addRenderAttribute( 'title', 'class', [ 'sober-banner-image__title' ] );
		view.addInlineEditingAttributes( 'title' );

		view.addRenderAttribute( 'description', 'class', [ 'sober-banner-image__description' ] );
		view.addInlineEditingAttributes( 'description' );

		var buttonHTML = '';
		var button2HTML = '';

		if ( 'yes' === settings.second_button && settings.second_button_text ) {
			view.addRenderAttribute( 'button', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--main', 'sober-banner-image__button--' + settings.button_type ] );

			if ( settings.link.url ) {
				view.addRenderAttribute( 'button', 'href', settings.link.url );

				if ( settings.link.is_external ) {
					view.addRenderAttribute( 'button', 'target', '_blank' );
				}

				if ( settings.link.nofollow ) {
					view.addRenderAttribute( 'button', 'rel', 'nofollow' );
				}
			}

			if ( settings.button_text ) {
				buttonHTML = '<a ' + view.getRenderAttributeString( 'button' ) + '>' + settings.button_text + '</a>';
			}

			view.addRenderAttribute( 'button2', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--second', 'sober-banner-image__button--' + settings.second_button_type ] );

			if ( settings.second_link.url ) {
				view.addRenderAttribute( 'button2', 'href', settings.second_link.url );

				if ( settings.second_link.is_external ) {
					view.addRenderAttribute( 'button2', 'target', '_blank' );
				}

				if ( settings.second_link.nofollow ) {
					view.addRenderAttribute( 'button2', 'rel', 'nofollow' );
				}
			}

			if ( settings.second_button_text ) {
				button2HTML = '<a ' + view.getRenderAttributeString( 'button2' ) + '>' + settings.second_button_text + '</a>';
			}

			var wrapperOpenHTML = '<div class="sober-banner-image__wrapper">';
			var wrapperCloseHTML = '</div>';
		} else {
			view.addRenderAttribute( 'button', 'class', [ 'sober-banner-image__button', 'sober-banner-image__button--main', 'sober-banner-image__button--' + settings.button_type ] );

			if ( settings.button_text ) {
				buttonHTML = '<span ' + view.getRenderAttributeString( 'button' ) + '>' + settings.button_text + '</span>';
			}

			view.addRenderAttribute( 'link', 'class', 'sober-banner-image__link' );

			if ( settings.link.url ) {
				view.addRenderAttribute( 'link', 'href', settings.link.url );

				if ( settings.link.is_external ) {
					view.addRenderAttribute( 'link', 'target', '_blank' );
				}

				if ( settings.link.nofollow ) {
					view.addRenderAttribute( 'link', 'rel', 'nofollow' );
				}
			}

			var wrapperOpenHTML = '<a ' + view.getRenderAttributeString( 'link' ) + '>';
			var wrapperCloseHTML = '</a>';
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			{{{ wrapperOpenHTML }}}
				<img src="{{ image_url }}">

				<div {{{ view.getRenderAttributeString( 'content' ) }}}>
					<# if ( settings.subtitle ) { #>
						<div {{{ view.getRenderAttributeString( 'subtitle' ) }}}>{{ settings.subtitle }}</div>
					<# } #>

					<# if ( settings.title ) { #>
						<div {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</div>
					<# } #>

					<# if ( settings.description ) { #>
						<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
					<# } #>

					<div class="sober-banner-image__buttons">
						{{{ buttonHTML }}}
						{{{ button2HTML }}}
					</div>
				</div>
			{{{ wrapperCloseHTML }}}
		</div>
		<?php
	}
}
