<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Icon_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-icon-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Icon Box', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
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
		return [ 'icon box', 'icon', 'box', 'sober' ];
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
			'section_icon',
			[ 'label' => __( 'Icon Box', 'sober' ) ]
		);

		$this->add_control(
			'icon_type',
			[
				'label' => __( 'Icon Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', 'sober' ),
					'image' => __( 'Image', 'sober' ),
					'external' => __( 'External Image', 'sober' ),
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'sober' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label' => __( 'Choose Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_url',
			[
				'label' => __( 'Icon URL', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'icon_type' => 'external',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'sober' ),
				'placeholder' => __( 'Enter your title', 'sober' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label' => '',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'sober' ),
				'placeholder' => __( 'Enter your description', 'sober' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title Tag', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'condition' => [
					'icon_type' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_type' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_view',
			[
				'label' => __( 'Icon Style', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'normal' => __( 'Normal', 'sober' ),
					'circle' => __( 'Circle', 'sober' ),
					'round' => __( 'Rounded', 'sober' ),
				],
				'default' => 'normal',
			]
		);

		$this->add_responsive_control(
			'icon_shape_size',
			[
				'label' => __( 'Shape Size', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 110,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 300,
					],
				],
				'condition' => [
					'icon_view' => ['circle', 'round'],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box.icon-style-circle .box-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sober-icon-box.icon-style-round .box-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color',
			[
				'label' => __( 'Border Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e4e6eb',
				'condition' => [
					'icon_view' => 'circle',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box.icon-style-circle .sober-icon-box__icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_background_color',
			[
				'label' => __( 'Background Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#23232c',
				'condition' => [
					'icon_view' => 'round',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box.icon-style-round .sober-icon-box__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_bottom_space',
			[
				'label' => __( 'Spacing', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'heading_title',
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
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .sober-icon-box__title',
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => __( 'Spacing', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .sober-icon-box__content',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'sober-icon-box',
			'icon-type-' . $settings['icon_type'],
			'icon-style-' . $settings['icon_view'],
		] );
		$this->add_render_attribute( 'icon', 'class', ['sober-icon-box__icon', 'box-icon'] );
		$this->add_render_attribute( 'title', 'class', ['sober-icon-box__title', 'box-title'] );
		$this->add_render_attribute( 'description', 'class', ['sober-icon-box__content', 'box-content'] );

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
				<?php
				if ( 'image' == $settings['icon_type'] ) {
					echo $settings['icon_image'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $settings['title'] ), esc_url( $settings['icon_image']['url'] ) ) : '';
				} elseif ( 'external' == $settings['icon_type'] ) {
					echo $settings['icon_url'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $settings['title'] ), esc_url( $settings['icon_url'] ) ) : '';
				} else {
					Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
				}
				?>
			</div>
			<?php if ( $settings['title'] ) : ?>
				<<?php echo $settings['title_tag'] ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo esc_html( $settings['title'] ) ?></<?php echo $settings['title_tag'] ?>>
			<?php endif; ?>
			<?php if ( $settings['description'] ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render icon box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		var icon = '';

		if ( 'image' === settings.icon_type ) {
			icon = settings.icon_image ? '<img src="' + settings.icon_image.url + '">' : '';
		} else if ( 'external' === settings.icon_type ) {
			icon = settings.icon_url ? '<img src="' + settings.icon_url + '">' : '';
		} else {
			icon = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' );
			icon = icon && icon.rendered ? icon.value : '';
		}

		view.addRenderAttribute( 'wrapper', 'class', [
			'sober-icon-box',
			'icon-type-' + settings.icon_type,
			'icon-style-' + settings.icon_view,
		] );
		view.addRenderAttribute( 'icon', 'class', ['sober-icon-box__icon', 'box-icon'] );
		view.addRenderAttribute( 'title', 'class', ['sober-icon-box__title', 'box-title'] );
		view.addRenderAttribute( 'description', 'class', ['sober-icon-box__content', 'box-content'] );

		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'description', 'basic' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'icon' ) }}}>{{{ icon }}}</div>
			<# if ( settings.title ) { #>
				<{{{ settings.title_tag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</{{{ settings.title_tag }}}>
			<# } #>
			<# if ( settings.description ) { #>
				<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
			<# } #>
		</div>
		<?php
	}
}
