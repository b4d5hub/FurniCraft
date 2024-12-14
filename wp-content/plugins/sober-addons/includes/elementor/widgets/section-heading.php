<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Button widget
 */
class Section_Heading extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-section-heading';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Section Heading', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-heading';
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
		return [ 'heading', 'title', 'sober' ];
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
			'section_title',
			[ 'label' => __( 'Heading', 'sober' ) ]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'sober' ),
				'default' => __( 'Add Your Heading Text Here', 'sober' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'sober' ),
					'small' => __( 'Small', 'sober' ),
					'medium' => __( 'Medium', 'sober' ),
					'large' => __( 'Large', 'sober' ),
				],
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => __( 'HTML Tag', 'sober' ),
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
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'sober' ),
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
					'justify' => [
						'title' => __( 'Justified', 'sober' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'more_button',
			[
				'label' => __( 'Read More', 'sober' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'more_button_link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'condition' => [
					'more_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_text',
			[
				'label' => __( 'Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'View more', 'sober' ),
				'condition' => [
					'more_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_arrow',
			[
				'label' => __( 'Hover Arrow', 'sober' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'more_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Heading', 'sober' ),
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
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-section-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .sober-section-heading',
			]
		);

		$this->add_control(
			'more_button_heading',
			[
				'label' => __( 'More Button', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'more_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_color',
			[
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-section-heading__more' => 'color: {{VALUE}};',
				],
				'condition' => [
					'more_button' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'more_button_typography',
				'selector' => '{{WRAPPER}} .sober-section-heading__more',
				'condition' => [
					'more_button' => 'yes',
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

		$this->add_render_attribute( 'wrapper', 'class', [
			'sober-section-heading',
			'sober-section-heading--' . $settings['size'],
		] );

		if ( ! empty( $settings['align'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-section-header--align-' . $settings['align'] );
		}

		$this->add_render_attribute( 'title', 'class', 'sober-section-heading__title' );
		$this->add_inline_editing_attributes( 'title', 'none' );

		$title = sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( 'title' ), $settings['title'] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'title', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'title', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'title', 'rel', 'nofollow' );
			}

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'title' ), $settings['title'] );
		}

		$button = '';
		if ( 'yes' == $settings['more_button'] && $settings['more_button_text'] ) {
			$button_text = esc_html( $settings['more_button_text'] );
			$this->add_render_attribute( 'button', 'class', ['sober-section-heading__more'] );

			if ( $settings['more_button_link']['url'] ) {
				$this->add_render_attribute( 'button', 'href', $settings['more_button_link']['url'] );

				if ( $settings['more_button_link']['is_external'] ) {
					$this->add_render_attribute( 'button', 'target', '_blank' );
				}

				if ( $settings['more_button_link']['nofollow'] ) {
					$this->add_render_attribute( 'button', 'rel', 'nofollow' );
				}
			}

			if ( $settings['more_button_arrow'] ) {
				$button_text .= '<svg viewBox="0 0 20 20" width="20" height="20"><use xlink:href="#right-arrow"></use></svg>';
			}

			$button = sprintf( '<a %s>%s</a>', $this->get_render_attribute_string( 'button' ), $button_text );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s%4$s</%1$s>', $settings['header_size'], $this->get_render_attribute_string( 'wrapper' ), $title, $button );

		echo $title_html;
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
			'sober-section-heading',
			'sober-section-heading--' + settings.size,
			'sober-section-header--align-' + settings.align
		] );

		view.addRenderAttribute( 'title', 'class', 'sober-section-heading__title' );
		view.addInlineEditingAttributes( 'title' );

		var title = '<span ' + view.getRenderAttributeString( 'title' ) + '>' + settings.title + '</span>';

		if ( '' !== settings.link.url ) {
			view.addRenderAttribute( 'title', 'href', settings.link.url );
			title = '<a ' + view.getRenderAttributeString( 'title' ) + '>' + settings.title + '</a>';
		}

		var button = '';

		if ( 'yes' === settings.more_button && settings.more_button_text ) {
			var button_text = settings.more_button_text;

			view.addRenderAttribute( 'button', 'class', 'sober-section-heading__more' );

			if ( settings.more_button_link.url ) {
				view.addRenderAttribute( 'button', 'href', settings.more_button_link.url );
			}

			if ( 'yes' === settings.more_button_arrow ) {
				button_text += '<svg viewBox="0 0 20 20" width="20" height="20"><use xlink:href="#right-arrow"></use></svg>';
			}

			button = '<a ' + view.getRenderAttributeString( 'button' ) + '>' + button_text + '</a>';
		}

		var title_html = '<' + settings.header_size  + ' ' + view.getRenderAttributeString( 'wrapper' ) + '>' + title + button + '</' + settings.header_size + '>';

		print( title_html );
		#>
		<?php
	}
}
