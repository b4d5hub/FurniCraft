<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Countdown widget
 */
class Countdown extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-countdown';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Countdown', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
		return [ 'countdown', 'clock', 'sober' ];
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
			'section_countdown',
			[ 'label' => __( 'Countdown', 'sober' ) ]
		);

		$this->add_control(
			'date',
			[
				'label' => __( 'Date', 'sober' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date( 'Y/m/d', strtotime( '+5 days' ) ),
				'picker_options' => [
					'dateFormat' => 'Y/m/d H:i:S'
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
			'text_align',
			[
				'label' => __( 'Text Alignment', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
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
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .sober-countdown' => 'text-align: {{VALUE}};',
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

		echo \Sober_Shortcodes::countdown( [
			'date'       => $settings['date'],
			'text_align' => '',
			'el_class'   => 'sober-countdown--elementor',
		] );
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<div class="sober-countdown sober-countdown--elementor">
			<div class="timers" data-date="{{ settings.date }}">
				<div class="timer-day box"><span class="time day"></span><span class="title"><?php _e( 'Days', 'sober' ) ?></span></div>
				<div class="timer-hour box"><span class="time hour"></span><span class="title"><?php _e( 'Hours', 'sober' ) ?></span></div>
				<div class="timer-min box"><span class="time min"></span><span class="title"><?php _e( 'Mins', 'sober' ) ?></span></div>
				<div class="timer-secs box"><span class="time secs"></span><span class="title"><?php _e( 'Sec', 'sober' ) ?></span></div>
			</div>
		</div>
		<?php
	}
}
