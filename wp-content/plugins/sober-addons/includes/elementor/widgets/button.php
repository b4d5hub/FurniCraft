<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Button widget
 */
class Button extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-button';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Button', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-button';
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
		return [ 'button', 'sober' ];
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
			'section_button',
			[ 'label' => __( 'Button', 'sober' ) ]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Click here', 'sober' ),
				'placeholder' => __( 'Button text', 'sober' ),
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
				'label' => __( 'Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __( 'Normal', 'sober' ),
					'outline' => __( 'Outline', 'sober' ),
					'light' => __( 'Light', 'sober' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => __( 'Small', 'sober' ),
					'normal' => __( 'Medium', 'sober' ),
					'large' => __( 'Large', 'sober' ),
				],
				'condition' => [
					'button_type!' => 'light',
				],
			]
		);

		$this->add_control(
			'width',
			[
				'label' => __( 'Width', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => __( 'Auto', 'sober' ),
					'min' => __( 'Min Width', 'sober' ),
				],
				'condition' => [
					'button_type!' => 'light',
				],
			]
		);

		$this->add_responsive_control(
			'min_width',
			[
				'label' => __( 'Min Width', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'show_label' => false,
				'size_units' => ['px', '%'],
				'condition' => [
					'button_type!' => 'light',
					'width' => 'min',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-button--type-normal, {{WRAPPER}} .sober-button--type-outline' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'left'    => [
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
						'title' => __( 'Justified', 'sober' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'sober-button-wrapper--align-',
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __( 'Button ID', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'sober' ),
				'label_block' => false,
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'sober' ),
				'separator' => 'before',

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'sober' ),
					'dark' => __( 'Dark', 'sober' ),
					'white' => __( 'White', 'sober' ),
					'custom' => __( 'Custom', 'sober' ),
				],
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
			'custom_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'color_scheme' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-button--type-normal' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sober-button--type-outline' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sober-button--type-outline:hover' => 'color: #fff; background-color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .sober-button--type-light' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'custom_text_color',
			[
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'color_scheme' => 'custom',
					'button_type' => 'normal',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-button--type-normal' => 'color: {{VALUE}};',
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
			'custom_hover_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'color_scheme' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-button--type-normal:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sober-button--type-outline:hover' => 'color: #fff; background-color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .sober-button--type-light:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'custom_hover_text_color',
			[
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'color_scheme' => 'custom',
					'button_type' => 'normal',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-button--type-normal:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'button', 'class', [
			'sober-button',
			'sober-button--elementor',
			'sober-button--type-' . $settings['button_type'],
			'button-type-' . $settings['button_type'],
		] );
		$this->add_render_attribute( 'button', 'role', 'button' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );
			$this->add_render_attribute( 'button', 'class', 'sober-button--link' );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}

		if ( 'light' == $settings['button_type'] ) {
			$this->add_render_attribute( 'button', 'class', 'button-light line-hover' );
		} else {
			$this->add_render_attribute( 'button', 'class', ['button', 'sober-button--size-' . $settings['size'], 'button-' . $settings['size'], $settings['size']] );
		}

		if ( ! empty( $settings['color_scheme'] ) ) {
			$this->add_render_attribute( 'button', 'class', ['sober-button--color-' . $settings['color_scheme'], 'button-color-' . $settings['color_scheme']] );
		}

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		$this->add_render_attribute( 'text', 'class', ['sober-button__text'] );
		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo esc_html( $settings['text'] ) ?></span>
		</a>
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
		view.addRenderAttribute( 'button', 'class', [
			'sober-button',
			'sober-button--elementor',
			'sober-button--type-' + settings.button_type,
			'button-type-' + settings.button_type,
		] );
		view.addRenderAttribute( 'button', 'role', 'button' );

		if ( settings.link.url ) {
			view.addRenderAttribute( 'button', 'class', 'sober-button--link' );
			view.addRenderAttribute( 'button', 'href', settings.link.url );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'button', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'button', 'rel', 'nofollow' );
			}
		}

		if ( 'light' === settings.button_type ) {
			view.addRenderAttribute( 'button', 'class', 'button-light line-hover' );
		} else {
			view.addRenderAttribute( 'button', 'class', ['button', 'sober-button--size-' + settings.size, 'button-' + settings.size, settings.size] );
		}

		if ( settings.color_scheme ) {
			view.addRenderAttribute( 'button', 'class', ['sober-button--color-' + settings.color_scheme, 'button-color-' + settings.color_scheme] );
		}

		if ( settings.button_css_id ) {
			view.addRenderAttribute( 'button', 'id', settings.addRenderAttribute );
		}

		view.addRenderAttribute( 'text', 'class', ['sober-button__text'] );
		view.addInlineEditingAttributes( 'text', 'none' );
		#>
		<a {{{ view.getRenderAttributeString( 'button' ) }}}>
			<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
		</a>
		<?php
	}
}
