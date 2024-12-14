<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Chart widget
 */
class Chart extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-circle-chart';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Circle Chart', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter-circle';
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
		return [ 'progress', 'chart', 'sober' ];
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
			'section_circle_chart',
			[ 'label' => __( 'Circle Chart', 'sober' ) ]
		);

		$this->add_control(
			'value',
			[
				'label' => __( 'Value', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'size',
			[
				'label'     => __( 'Circle Size', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 2000,
				'step'      => 1,
				'default'   => 200,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'thickness',
			[
				'label'     => __( 'Circle Thickness', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 8,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'label_source',
			[
				'label' => __( 'Label', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => __( 'Percentage', 'sober' ),
					'custom' => __( 'Custom', 'sober' ),
				],
				'default' => 'auto',
			]
		);

		$this->add_control(
			'label',
			[
				'label' => __( 'Label', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'show_label' => false,
				'condition' => [
					'label_source' => 'custom',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_circle',
			[
				'label' => __( 'Circle', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'empty_color',
			[
				'label' => __( 'Circle Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0)',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Fill Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6dcff6',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => __( 'Label', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .sober-chart .text',
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

		echo \Sober_Shortcodes::chart( [
			'value'         => $settings['value']['size'],
			'size'          => $settings['size'],
			'thickness'     => $settings['thickness'],
			'label_source'  => $settings['label_source'],
			'label'         => $settings['label'],
			'color'         => $settings['color'],
			'el_class'      => 'sober-chart--elementor',
		] );
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', [
			'sober-chart',
			'sober-chart-' + settings.value.size,
			'sober-chart--elementor',
		] );

		view.addRenderAttribute( 'wrapper', 'data-value', [ settings.value.size/100 ] );
		view.addRenderAttribute( 'wrapper', 'data-size', [ settings.size ] );
		view.addRenderAttribute( 'wrapper', 'data-thickness', [ settings.thickness ] );
		view.addRenderAttribute( 'wrapper', 'data-fill', [ '{"color": "' + settings.color + '"}' ] );

		view.addRenderAttribute( 'text', 'class', [ 'text' ] );
		view.addRenderAttribute( 'text', 'style', 'color: ' + settings.color );

		var label = ('custom' === settings.label_source) ? settings.label : '<span class="unit">' + settings.value.unit + '</span>' + settings.value.size.toString();
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ label }}}</div>
		</div>
		<?php
	}
}
