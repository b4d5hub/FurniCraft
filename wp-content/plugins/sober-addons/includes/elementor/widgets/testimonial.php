<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Testimonial widget
 */
class Testimonial extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-testimonial';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Testimonial', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial';
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
		return [ 'testimonial', 'sober' ];
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
			'section_tesimonial',
			[ 'label' => __( 'Testimonial', 'sober' ) ]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'This is the testimonial content.', 'sober' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Author Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => SOBER_ADDONS_URL . '/assets/images/man-placeholder.png',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name',
			[
				'label' => __( 'Author Name', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Andrew Sober', 'sober' ),
			]
		);

		$this->add_control(
			'job',
			[
				'label' => __( 'Job Title/Company', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Company', 'sober' ),
			]
		);

		$this->add_control(
			'image_position',
			[
				'label' => __( 'Image Position', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'default' => 'top',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __( 'Top', 'sober' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'sober' ),
						'icon' => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => __( 'Image Size', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 160,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-testimonial .author-photo' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sober-testimonial.testimonial-align-left .testimonial-entry' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sober-testimonial.testimonial-align-right .testimonial-entry' => 'margin-right: {{SIZE}}{{UNIT}};',
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
			'heading_content',
			[
				'label' => __( 'Content', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-testimonial .testimonial-content' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .sober-testimonial .testimonial-content',
			]
		);

		$this->add_control(
			'heading_author',
			[
				'label' => __( 'Author Info', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'author_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-testimonial .testimonial-author' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'selector' => '{{WRAPPER}} .sober-testimonial .testimonial-author',
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

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$image_size = 'large';
		} else {
			$image_size = ( 150 >= $settings['image_size']['size'] ) ? 'thumbnail' : 'medium';
			$image_size = ( 300 <= $settings['image_size']['size'] ) ? 'large' : $image_size;
		}

		$image = wp_get_attachment_image_src( $settings['image']['id'], $image_size );
		$image = $image ? '<img src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( $settings['name'] ) . '">' : '';

		if ( ! $image && ! empty( $settings['image']['url'] ) ) {
			$image = '<img src="' . esc_url( $settings['image']['url'] ) . '" alt="' . esc_attr( $settings['name'] ) . '">';
		}

		$author_info = array(
			'<span class="sober-testimonial__auhtor-name name">' . esc_html( $settings['name'] ) . '</span>',
			'<span class="sober-testimonial__author-company company">' . esc_html( $settings['job'] ) . '</span>',
		);

		$this->add_render_attribute( 'wrapper', 'class', [
			'sober-testimonial',
			'sober-testimonial--elementor',
			'testimonial-align-' . $settings['image_position']
		] );

		$this->add_render_attribute( 'content', 'class', [ 'testimonial-content', 'sober-testimonial__content' ] );
		$this->add_inline_editing_attributes( 'content' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="author-photo sober-testimonial__photo"><?php echo $image ?></div>
			<div class="testimonial-entry sober-testimonial__entry">
				<div <?php echo $this->get_render_attribute_string( 'content' ); ?>><?php echo wp_kses_post( $settings['content'] ); ?></div>
				<div class="testimonial-author sober-testimonial__author">
					<?php echo implode( ', ', $author_info ) ?>
				</div>
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
			'sober-testimonial',
			'sober-testimonial--elementor',
			'testimonial-align-' + settings.image_position
		] );

		view.addRenderAttribute( 'content', 'class', [ 'testimonial-content', 'sober-testimonial__content' ] );
		view.addInlineEditingAttributes( 'content' );

		var comma = settings.name && settings.job ? ', ' : '';
		var image_url = settings.image.url;

		if ( settings.image.id ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: 'large',
				model: view.getEditModel()
			};

			image_url = elementor.imagesManager.getImageUrl( image );
		}
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="author-photo sober-testimonial__photo">
				<img src="{{ image_url }}">
			</div>
			<div class="testimonial-entry sober-testimonial__entry">
				<div {{{ view.getRenderAttributeString( 'content' ) }}}>{{{ settings.content }}}</div>
				<div class="testimonial-author sober-testimonial__author">
					<# if ( settings.name ) { #>
						<span class="sober-testimonial__auhtor-name name">{{ settings.name }}</span>
					<# } #>
					{{{ comma }}}
					<# if ( settings.job ) { #>
						<span class="sober-testimonial__auhtor-company company">{{ settings.job }}</span>
					<# } #>
				</div>
			</div>
		</div>

		<?php
	}
}
