<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Control_Media;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Category Banner widget
 */
class Category_Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-category-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Category Banner', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-featured-image';
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
		return [ 'category banner', 'banner', 'sober' ];
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
			'section_category_banner',
			[ 'label' => __( 'Category Banner', 'sober' ) ]
		);

		$this->add_control(
			'image',
			[
				'label'   => __( 'Image', 'sober' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => ['url' => Utils::get_placeholder_image_src()],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sober' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Category Name', 'sober' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Category description', 'sober' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Shop Now', 'sober' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'         => __( 'Link', 'sober' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'sober' ),
				'show_external' => true,
				'default'       => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				],
			]
		);

		$this->add_control(
			'image_position',
			[
				'label' => __( 'Image Position', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'default' => 'left',
				'options' => [
					'left'         => __( 'Left', 'sober' ),
					'right'        => __( 'Right', 'sober' ),
					'top'          => __( 'Top', 'sober' ),
					'bottom'       => __( 'Bottom', 'sober' ),
					'top-left'     => __( 'Top Left', 'sober' ),
					'top-right'    => __( 'Top Right', 'sober' ),
					'bottom-left'  => __( 'Bottom Left', 'sober' ),
					'bottom-right' => __( 'Bottom Right', 'sober' ),
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label' => __( 'Content Position', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top-left',
				'options' => [
					'top-left'     => __( 'Top Left', 'sober' ),
					'top-right'    => __( 'Top Right', 'sober' ),
					'middle-left'  => __( 'Middle Left', 'sober' ),
					'middle-right' => __( 'Middle Right', 'sober' ),
					'bottom-left'  => __( 'Bottom Left', 'sober' ),
					'bottom-right' => __( 'Bottom Right', 'sober' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category_banner',
			[
				'label' => __( 'Category Banner', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-category-banner .banner-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '60',
						],
					],
					'font_weight' => [
						'default' => '500',
					],
				],
				'selector' => '{{WRAPPER}} .sober-category-banner .banner-title',
			]
		);

		$this->add_control(
			'desc_style_heading',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-category-banner .banner-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '13',
						],
					],
					'font_weight' => [
						'default' => '400',
					],
				],
				'selector' => '{{WRAPPER}} .sober-category-banner .banner-text',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .sober-category-banner__button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sober-category-banner__button:after' => 'background-color: {{VALUE}};',
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
			'sober-category-banner',
			'sober-category-banner--elementor',
			'sober-category-banner--content-' . $settings['content_position'],
			'sober-category-banner--image-' . $settings['image_position'],
			'text-position-' . $settings['content_position'],
			'image-' . $settings['image_position'],
		] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		$this->add_render_attribute( 'image_holder', 'class', ['sober-category-banner__image', 'banner-image'] );

		if ( ! empty( $settings['image']['url'] ) ) {
			$this->add_render_attribute( 'image_holder', 'style', 'background-image: url(' . $settings['image']['url'] . ')' );

			$this->add_render_attribute( 'image', 'src', $settings['image']['url'] );
			$this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $settings['image'] ) );
		} else {
			$this->add_render_attribute( 'image_holder', 'style', 'background-image: url(' . Utils::get_placeholder_image_src() . ')' );
			$this->add_render_attribute( 'image', 'src', Utils::get_placeholder_image_src() );
		}

		$this->add_render_attribute( 'title', 'class', ['sober-category-banner__title', 'banner-title'] );
		$this->add_inline_editing_attributes( 'title', 'none' );

		$this->add_render_attribute( 'desc', 'class', ['sober-category-banner__desc', 'banner-text'] );
		$this->add_inline_editing_attributes( 'desc', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div class="sober-category-banner__inner banner-inner">
				<a <?php echo $this->get_render_attribute_string( 'link' ) ?> <?php echo $this->get_render_attribute_string( 'image_holder' ) ?>>
					<img <?php echo $this->get_render_attribute_string( 'image' ) ?>>
				</a>
				<div class="sober-category-banner__content banner-content">
					<h2 <?php echo $this->get_render_attribute_string( 'title' ) ?>><?php echo wp_kses_post( $settings['title'] ); ?></h2>
					<div <?php echo $this->get_render_attribute_string( 'desc' ) ?>><?php echo wp_kses_post( $settings['desc'] ); ?></div>
					<?php if ( ! empty( $settings['button_text'] ) ) : ?>
						<a <?php echo $this->get_render_attribute_string( 'link' ) ?> class="sober-category-banner__button sober-button button-light line-hover active">
							<?php echo esc_html( $settings['button_text'] ) ?>
						</a>
					<?php endif; ?>
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
			'sober-category-banner',
			'sober-category-banner--elementor',
			'sober-category-banner--content-' + settings.content_position,
			'sober-category-banner--image-' + settings.image_position,
			'text-position-' + settings.content_position,
			'image-' + settings.image_position,
		] );

		if ( settings.link.url ) {
			view.addRenderAttribute( 'link', 'href', settings.link.url );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'link', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'link', 'rel', 'nofollow' );
			}
		}

		view.addRenderAttribute( 'image_holder', 'class', ['sober-category-banner__image', 'banner-image'] );

		if ( settings.image.url ) {
			view.addRenderAttribute( 'image_holder', 'style', 'background-image: url(' + settings.image.url + ')' );
		}

		view.addRenderAttribute( 'title', 'class', ['sober-category-banner__title', 'banner-title'] );
		view.addInlineEditingAttributes( 'title', 'none' );

		view.addRenderAttribute( 'desc', 'class', ['sober-category-banner__desc', 'banner-text'] );
		view.addInlineEditingAttributes( 'desc', 'basic' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="sober-category-banner__inner banner-inner">
				<a {{{ view.getRenderAttributeString( 'link' ) }}} {{{ view.getRenderAttributeString( 'image_holder' ) }}}>
					<# if ( settings.image.url ) { #>
						<img src="{{ settings.image.url }}">
					<# } #>
				</a>
				<div class="sober-category-banner__content banner-content">
					<h2 {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</h2>
					<div {{{ view.getRenderAttributeString( 'desc' ) }}}>{{{ settings.desc }}}</div>
					<# if ( settings.button_text ) { #>
						<a {{{ view.getRenderAttributeString( 'link' ) }}} class="sober-category-banner__button sober-button button-light line-hover active">{{{ settings.button_text }}}</a>
					<# } #>
				</div>
			</div>
		</div>
		<?php
	}
}
