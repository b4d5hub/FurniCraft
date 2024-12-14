<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Image slider widget
 */
class Image_Slider extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-image-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Image Slider', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-slider-push';
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
		return [ 'image slider', 'image carousel', 'image', 'carousel', 'slider', 'sober' ];
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
			'section_image_slider',
			[ 'label' => __( 'Image Slider', 'sober' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Image', 'sober' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => ['url' => Utils::get_placeholder_image_src()],
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'   => __( 'Text', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Slide title', 'sober' ),
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'   => __( 'Button Text', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Discover', 'sober' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'   => __( 'Button URL', 'sober' ),
				'type'    => Controls_Manager::URL,
				'default' => ['url' => __( 'https://your-link.com', 'sober' )],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'   => __( 'Slides', 'sober' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'text' => __( 'Slide title 1', 'sober' ),
						'button' => __( 'Discover Collection', 'sober' ),
						'link' => ['url' => '#'],
					],
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'text' => __( 'Slide title 2', 'sober' ),
						'button' => __( 'Discover Collection', 'sober' ),
						'link' => ['url' => '#'],
					],
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'text' => __( 'Slide title 3', 'sober' ),
						'button' => __( 'Discover Collection', 'sober' ),
						'link' => ['url' => '#'],
					],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_addtional_options',
			[ 'label' => __( 'Additional Options', 'sober' ) ]
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
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'sober' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'frontend_available' => true,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'sober' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode',
			[
				'label' => __( 'Free Mode', 'sober' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'On', 'sober' ),
				'label_off' => __( 'Off', 'sober' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image_slider',
			[
				'label' => __( 'Image Slider', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-image-slider, {{WRAPPER}} .owl-nav button' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Text Typography', 'sober' ),
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .sober-image-slider__item-text',
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

		$this->add_render_attribute( 'wrapper', 'class', ['sober-image-slider'] );

		if ( $settings['free_mode'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-image-slider--free-mode' );
		}

		$list = [];
		foreach ( $settings['slides'] as $index => $slide ) {
			$link_key = 'link_' . $index;

			if ( $slide['link']['url'] ) {
				$this->add_render_attribute( $link_key, 'href', esc_url( $slide['link']['url'] ) );

				if ( $slide['link']['is_external'] ) {
					$this->add_render_attribute( $link_key, 'target', '_blank' );
				}

				if ( $slide['link']['nofollow'] ) {
					$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
				}
			}

			// $image = wp_get_attachment_image_src( $slide['image']['i'], 'full' );
			// $image = $image ? $image[0] : Utils::get_placeholder_image_src();
			$image = $slide['image']['url'] ? $slide['image']['url'] : Utils::get_placeholder_image_src();
			$image_html = sprintf( '<img alt="%s" src="%s" class="skip-lazy">',
				esc_attr( $slide['text'] ),
				esc_url( $image )
			);

			$text = $slide['text'] ? '<h3 class="sober-image-slider__item-text">' . esc_html( $slide['text'] ) . '</h3>' : '';
			$button = $slide['button_text'] ? '<span class="sober-image-slider__item-button">' . esc_html( $slide['button_text'] ) . '</span>' : '';

			$list[] = '<div class="sober-image-slider__item"><a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . $text . $button . '</a></div>';
		}

		if ( ! $list ) {
			return;
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php echo implode( '', $list ); ?>
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
		view.addRenderAttribute( 'wrapper', 'class', [ 'sober-image-slider' ] );

		if ( settings.free_mode ) {
			view.addRenderAttribute( 'wrapper', 'class', [ 'sober-image-slider--free-mode' ] );
		}
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# _.each( settings.slides, function( slide, index ) { #>
				<#
				var link_key = 'link_' + index.toString();
				if ( slide.link.url ) {
					view.addRenderAttribute( link_key, 'href', slide.link.url );

					if ( slide.link.is_external ) {
						view.addRenderAttribute( link_key, 'target', '_blank' )
					}

					if ( slide.link.nofollow ) {
						view.addRenderAttribute( link_key, 'rel', 'nofollow' )
					}
				}
				#>
				<div class="sober-image-slider__item">
					<a {{{ view.getRenderAttributeString( link_key ) }}}>
						<img src="{{ slide.image.url }}">
						<# if ( slide.text ) { #>
							<h3 class="sober-image-slider__item-text">{{{ slide.text }}}</h3>
						<# } #>
						<# if ( slide.button_text ) { #>
							<span class="sober-image-slider__item-button">{{{ slide.button_text }}}</span>
						<# } #>
					</a>
				</div>
			<# } ); #>
		</div>
		<?php
	}
}
