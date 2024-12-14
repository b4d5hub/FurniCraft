<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Message box widget
 */
class Message_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-message-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Message Box', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-alert';
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
		return [ 'message box', 'message', 'alert', 'notice', 'sober' ];
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
			'section_message',
			[ 'label' => __( 'Message Box', 'sober' ) ]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'success' => __( 'Success', 'sober' ),
					'info'    => __( 'Information', 'sober' ),
					'danger'  => __( 'Error', 'sober' ),
					'warning' => __( 'Warning', 'sober' ),
				],
				'default' => 'success',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your message content', 'sober' ),
				'default' => __( 'I am the message content. Click the edit button to change this text.', 'sober' ),
			]
		);

		$this->add_control(
			'closeable',
			[
				'label'        => __( 'Close Button', 'sober' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'sober' ),
				'label_off'    => __( 'Off', 'sober' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			[
				'label' => __( 'Message Box', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background',
			[
				'label' => __( 'Background Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-message-box' => 'background-color: {{VALUE}};',
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
			'content_color',
			[
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-message-box' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .sober-message-box .box-content',
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

		$this->add_render_attribute( 'wrapper', 'class', 'sober-message-box sober-message-box--elementor ' . $settings['type'] );

		if ( $settings['closeable'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'closeable' );
		}

		$this->add_render_attribute( 'content', 'class', 'box-content' );
		$this->add_inline_editing_attributes( 'content' );

		$icon = str_replace( array( 'info', 'danger' ), array( 'information', 'error' ), $settings['type'] );
		$close = $settings['closeable'] ? '<a class="close" href="#"><svg viewBox="0 0 14 14"><use xlink:href="#close-delete-small"></use></svg></a>' : '';
		?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<svg viewBox="0 0 20 20" class="message-icon"><use xlink:href="#<?php echo esc_attr( $icon ) ?>"></use></svg>
			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>><?php echo $settings['content'] ?></div>
			<?php echo $close; ?>
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
		view.addRenderAttribute( 'wrapper', 'class', [ 'sober-message-box', settings.type, 'sober-message-box--elementor' ] );

		if ( 'yes' === settings.closeable ) {
			view.addRenderAttribute( 'wrapper', 'class', [ 'closeable' ] );
		}

		var icon = settings.type;

		if ( 'info' === icon ) {
			icon = 'information';
		} else if ( 'danger' === icon ) {
			icon = 'error';
		}

		view.addRenderAttribute( 'content', 'class', [ 'box-content' ] );

		view.addInlineEditingAttributes( 'content' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<svg viewBox="0 0 20 20" class="message-icon"><use xlink:href="#{{ icon }}"></use></svg>
			<div {{{ view.getRenderAttributeString( 'content' ) }}}>{{{ settings.content }}}</div>
			<# if ( 'yes' === settings.closeable ) { #>
				<a class="close" href="#"><svg viewBox="0 0 14 14"><use xlink:href="#close-delete-small"></use></svg></a>
			<# } #>
		</div>

		<?php
	}
}
