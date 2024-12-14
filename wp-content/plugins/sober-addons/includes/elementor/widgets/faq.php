<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * FAQ widget
 */
class FAQ extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-faq';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] FAQ', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-help-o';
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
		return [ 'faq', 'question', 'help', 'sober' ];
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
			'section_faq',
			[ 'label' => __( 'FAQ', 'sober' ) ]
		);

		$this->add_control(
			'question',
			[
				'label'       => __( 'Question', 'sober' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter the question here', 'sober' ),
				'default'     => __( 'Question content goes here', 'sober' ),
			]
		);

		$this->add_control(
			'answer',
			[
				'label'     => __( 'Answer', 'sober' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => __( 'Answer content goes here, click edit button to change this text.', 'sober' ),
			]
		);

		$this->add_control(
			'state',
			[
				'label'        => __( 'Default State', 'sober' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Open', 'sober' ),
				'label_off'    => __( 'Close', 'sober' ),
				'return_value' => 'open',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_question',
			[
				'label' => __( 'Question', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'question_typography',
				'selector' => '{{WRAPPER}} .sober-faq__question-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_answer',
			[
				'label' => __( 'Answer', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'answer_typography',
				'selector' => '{{WRAPPER}} .sober-faq__answer-content',
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

		$this->add_render_attribute( 'wrapper', 'class', ['sober-faq', 'sober-faq--elementor'] );

		if ( 'open' === $settings['state'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'open' );
		}

		$this->add_render_attribute( 'question', 'class', ['question-title', 'sober-faq__question-title'] );
		$this->add_inline_editing_attributes( 'question', 'none' );

		$this->add_render_attribute( 'answer', 'class', ['sober-faq__answer-content'] );
		$this->add_inline_editing_attributes( 'answer', 'advanced' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="sober-faq__question question">
				<span class="sober-faq__question-label question-label"><?php esc_html_e( 'Question', 'sober' ) ?></span>
				<span class="sober-faq__question-icon question-icon"><span class="toggle-icon"></span></span>
				<span <?php echo $this->get_render_attribute_string( 'question' ); ?>><?php echo esc_html( $settings['question'] ) ?></span>
			</div>
			<div class="sober-faq__answer answer">
				<span class="sober-faq__answer-label answer-label"><?php esc_html_e( 'Answer', 'sober' ) ?></span>
				<div <?php echo $this->get_render_attribute_string( 'answer' ); ?>><?php echo wp_kses_post( $settings['answer'] ) ?></div>
			</div>
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
		view.addRenderAttribute( 'wrapper', 'class', [
			'sober-faq',
			'sober-faq--elementor',
		] );

		if ( 'open' === settings.state ) {
			view.addRenderAttribute( 'wrapper', 'class', 'open' );
		}

		view.addRenderAttribute( 'question', 'class', ['question-title', 'sober-faq__question-title'] );
		view.addInlineEditingAttributes( 'question', 'none' );

		view.addRenderAttribute( 'answer', 'class', ['sober-faq__answer-content'] );
		view.addInlineEditingAttributes( 'answer', 'advanced' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="sober-faq__question question">
				<span class="sober-faq__question-label question-label"><?php esc_html_e( 'Question', 'sober' ) ?></span>
				<span class="sober-faq__question-icon question-icon"><span class="toggle-icon"></span></span>
				<span {{{ view.getRenderAttributeString( 'question' ) }}}>{{{ settings.question }}}</span>
			</div>
			<div class="sober-faq__answer answer">
				<span class="sober-faq__answer-label answer-label"><?php esc_html_e( 'Answer', 'sober' ) ?></span>
				<div {{{ view.getRenderAttributeString( 'answer' ) }}}>{{{ settings.answer }}}</div>
			</div>
		</div>
		<?php
	}
}
