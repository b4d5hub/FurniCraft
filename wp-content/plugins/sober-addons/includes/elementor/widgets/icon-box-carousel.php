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
 * Icon Box Carousel widget
 */
class Icon_Box_Carousel extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-icon-box-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Icon Box Carousel', 'sober' );
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
		return [ 'icon box carousel', 'icon box', 'icon', 'box', 'carousel', 'sober' ];
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
			'section_icon_boxes',
			[ 'label' => __( 'Icon Boxes', 'sober' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'sober' ),
				'placeholder' => __( 'Enter your title', 'sober' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
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

		$repeater->add_control(
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

		// Style settings.
		$repeater->add_control(
			'heading_style_section',
			[
				'label' => __( 'STYLE SETTINGS', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'heading_icon_style',
			[
				'label' => __( 'Icon', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_type' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
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

		$repeater->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}.sober-icon-box.icon-style-circle .box-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.sober-icon-box.icon-style-round .box-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'icon_border_color',
			[
				'label' => __( 'Border Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e4e6eb',
				'condition' => [
					'icon_view' => 'circle',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.sober-icon-box.icon-style-circle .sober-icon-box__icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'icon_background_color',
			[
				'label' => __( 'Background Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#23232c',
				'condition' => [
					'icon_view' => 'round',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.sober-icon-box.icon-style-round .sober-icon-box__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$repeater->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__title' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__title',
			]
		);

		$repeater->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'heading_description_style',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__content' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .sober-icon-box__content',
			]
		);
		// End style settings.

		$this->add_control(
			'boxes',
			[
				'label'   => __( 'Icon Boxes', 'sober' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-star',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-rocket',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-cloud',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-coffee',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-feather',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-fish',
							'library' => 'fa-solid',
						],
						'title' => __( 'Icon Box', 'sober' ),
						'description' => _x( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Icon box default description', 'sober' ),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel',
			[ 'label' => __( 'Carousel', 'sober' ) ]
		);

		$this->add_responsive_control(
			'items_to_show',
			[
				'label'   => __( 'Boxes to Show', 'sober' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				],
				'desktop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
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

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Carousel', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'boxes_gap',
			[
				'label' => __( 'Boxes Gap', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-icon-box' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2 );',
				],
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'sober-icon-box-carousel' ] );

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php foreach ( $settings['boxes'] as $box ) : ?>
				<?php
				$box_key = 'box_' . $box['_id'];
				$icon_key = 'icon_' . $box['_id'];
				$title_key = 'title_' . $box['_id'];
				$description_key = 'description_' . $box['_id'];

				$this->add_render_attribute( $box_key, 'class', [
					'sober-icon-box',
					'sober-icon-box-carousel__box',
					'icon-type-' . $box['icon_type'],
					'icon-style-' . $box['icon_view'],
					'elementor-repeater-item-' . $box['_id'],
				] );
				$this->add_render_attribute( $icon_key, 'class', ['sober-icon-box__icon', 'box-icon'] );
				$this->add_render_attribute( $title_key, 'class', ['sober-icon-box__title', 'box-title'] );
				$this->add_render_attribute( $description_key, 'class', ['sober-icon-box__content', 'box-content'] );
				?>
				<div <?php echo $this->get_render_attribute_string( $box_key ); ?>>
					<div <?php echo $this->get_render_attribute_string( $icon_key ); ?>>
						<?php
						if ( 'image' == $box['icon_type'] ) {
							echo $box['icon_image'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $box['title'] ), esc_url( $box['icon_image']['url'] ) ) : '';
						} elseif ( 'external' == $box['icon_type'] ) {
							echo $box['icon_url'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $box['title'] ), esc_url( $box['icon_url'] ) ) : '';
						} else {
							Icons_Manager::render_icon( $box['icon'], [ 'aria-hidden' => 'true' ] );
						}
						?>
					</div>
					<<?php echo $box['title_tag'] ?> <?php echo $this->get_render_attribute_string( $title_key ); ?>><?php echo esc_html( $box['title'] ) ?></<?php echo $box['title_tag'] ?>>
					<div <?php echo $this->get_render_attribute_string( $description_key ); ?>><?php echo wp_kses_post( $box['description'] ) ?></div>
				</div>
			<?php endforeach; ?>
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
		<div class="sober-icon-box-carousel">
			<# _.each( settings.boxes, function( box ) { #>
				<#
				var box_key = 'box_' + box['_id'],
					icon_key = 'icon_' + box['_id'],
					title_key = 'title_' + box['_id'],
					description_key = 'description_' + box['_id'];

				var icon = '';

				if ( 'image' === box.icon_type ) {
					icon = box.icon_image ? '<img src="' + box.icon_image.url + '">' : '';
				} else if ( 'external' === box.icon_type ) {
					icon = box.icon_url ? '<img src="' + box.icon_url + '">' : '';
				} else {
					icon = elementor.helpers.renderIcon( view, box.icon, { 'aria-hidden': true }, 'i' , 'object' );
					icon = icon && icon.rendered ? icon.value : '';
				}

				view.addRenderAttribute( box_key, 'class', [
					'sober-icon-box',
					'sober-icon-box-carousel__box',
					'icon-type-' + box.icon_type,
					'icon-style-' + box.icon_view,
					'elementor-repeater-item-' + box['_id'],
				] );

				view.addRenderAttribute( icon_key, 'class', ['sober-icon-box__icon', 'box-icon'] );
				view.addRenderAttribute( title_key, 'class', ['sober-icon-box__title', 'box-title'] );
				view.addRenderAttribute( description_key, 'class', ['sober-icon-box__content', 'box-content'] );
				#>
				<div {{{ view.getRenderAttributeString( box_key ) }}}>
					<div {{{ view.getRenderAttributeString( icon_key ) }}}>{{{ icon }}}</div>
					<{{{ box.title_tag }}} {{{ view.getRenderAttributeString( title_key ) }}}>{{ box.title }}</{{{ box.title_tag }}}>
					<div {{{ view.getRenderAttributeString( description_key ) }}}>{{{ box.description }}}</div>
				</div>
			<# } ); #>
		</div>
		<?php
	}
}
