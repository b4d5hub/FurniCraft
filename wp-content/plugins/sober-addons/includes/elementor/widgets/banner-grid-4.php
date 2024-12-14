<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner grid widget with 4 banners
 */
class Banner_Grid_4 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-banner-grid-4';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Banner Grid 4', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-inner-section';
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
		return [ 'banner grid 4', 'banner grid', 'banner', 'grid', 'sober' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_banner_controls( [
			'prefix'            => 'banner1',
			'title'             => __( 'Banner 1', 'sober' ),
			'content_position'  => 'center',
			'subtitle_selector' => '.sober-banner-image-1__subtitle',
			'title_selector'    => '.sober-banner-image-1__title',
			'desc_selector'     => '.sober-banner-image-1__description',
			'button_selector'   => '.sober-banner-image-1__button',
			'title_options'     => [
				'font_size' => [
					'default' => [
						'unit' => 'px',
						'size' => '60',
					],
				],
				'font_weight' => [
					'default' => '300',
				],
				'line_height' => [
					'default' => [
						'unit' => 'em',
						'size' => '1.3333',
					],
				]
			],
		] );

		$this->register_banner_controls( [
			'prefix'            => 'banner2',
			'title'             => __( 'Banner 2', 'sober' ),
			'content_position'  => 'center',
			'subtitle_selector' => '.sober-banner-image-2__subtitle',
			'title_selector'    => '.sober-banner-image-2__title',
			'desc_selector'     => '.sober-banner-image-2__description',
			'button_selector'   => '.sober-banner-image-2__button',
			'title_options'     => [
				'font_size' => [
					'default' => [
						'unit' => 'px',
						'size' => '30',
					],
				],
				'font_weight' => [
					'default' => '300',
				],
				'line_height' => [
					'default' => [
						'unit' => 'em',
						'size' => '1.3333',
					],
				]
			],
		] );

		$this->register_banner_controls( [
			'prefix'            => 'banner3',
			'title'             => __( 'Banner 3', 'sober' ),
			'content_position'  => 'center',
			'subtitle_selector' => '.sober-banner-image-3__subtitle',
			'title_selector'    => '.sober-banner-image-3__title',
			'desc_selector'     => '.sober-banner-image-3__description',
			'button_selector'   => '.sober-banner-image-3__button',
			'title_options'     => [
				'font_size' => [
					'default' => [
						'unit' => 'px',
						'size' => '30',
					],
				],
				'font_weight' => [
					'default' => '300',
				],
				'line_height' => [
					'default' => [
						'unit' => 'em',
						'size' => '1.3333',
					],
				]
			],
		] );

		$this->register_banner_controls( [
			'prefix'            => 'banner4',
			'title'             => __( 'Banner 4', 'sober' ),
			'content_position'  => 'left',
			'subtitle_selector' => '.sober-banner-image-4__subtitle',
			'title_selector'    => '.sober-banner-image-4__title',
			'desc_selector'     => '.sober-banner-image-4__description',
			'button_selector'   => '.sober-banner-image-4__button',
			'title_options'     => [
				'font_size' => [
					'default' => [
						'unit' => 'px',
						'size' => '35',
					],
				],
				'font_weight' => [
					'default' => '300',
				],
				'line_height' => [
					'default' => [
						'unit' => 'em',
						'size' => '1.3333',
					],
				]
			],
		] );
	}

	/**
	 * Register settings for a single banner
	 *
	 * @param array $args
	 * @return void
	 */
	protected function register_banner_controls( $args = [] ) {
		$args = wp_parse_args( $args, [
			'prefix'            => 'banner',
			'title'             => __( 'Banner', 'sober' ),
			'content_position'  => 'center',
			'subtitle_selector' => '.sober-banner__subtitle',
			'title_selector'    => '.sober-banner__title',
			'desc_selector'     => '.sober-banner__desc',
			'subtitle_options'  => [],
			'title_options'     => [],
			'desc_options'      => [],
		] );

		$this->start_controls_section(
			'section_' . $args['prefix'],
			[
				'label' => $args['title'],
			]
		);

		$this->add_control(
			$args['prefix'] . '_image',
			[
				'label'   => __( 'Image', 'sober' ),
				'type'    => Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			$args['prefix'] . '_image_hover',
			[
				'label' => __( 'Hover Effect', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'zoom_box',
				'options' => [
					'none'     => __( 'None', 'sober' ),
					'zoom'     => __( 'Zoom In', 'sober' ),
					'box'      => __( 'Overlay Box', 'sober' ),
					'zoom_box' => __( 'Zoom in & Overlay Box', 'sober' ),
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_subtitle',
			[
				'label' => __( 'Subtitle', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Banner subtitle', 'sober' ),
				'placeholder' => __( 'Subtitle', 'sober' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$args['prefix'] . '_title',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Banner Title', 'sober' ),
				'placeholder' => __( 'Title', 'sober' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			$args['prefix'] . '_description',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Banner description', 'sober' ),
				'placeholder' => __( 'Description', 'sober' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_text',
			[
				'label' => __( 'Button Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'sober' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			$args['prefix'] . '_link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'sober' ),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_type',
			[
				'label' => __( 'Button Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'normal' => __( 'Normal', 'sober' ),
					'outline' => __( 'Outline', 'sober' ),
					'light' => __( 'Light', 'sober' ),
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_visible',
			[
				'label' => __( 'Button Visibility', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'default' => 'fadein',
				'options' => [
					'always' => __( 'Always', 'sober' ),
					'fadein' => __( 'Fadein on hover', 'sober' ),
					'fadeup' => __( 'Fadein-Up on hover', 'sober' ),
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_content_position',
			[
				'label' => __( 'Content Position', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => $args['content_position'],
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sober' ),
						'icon' => 'eicon-h-align-center',
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
			'section_style_' . $args['prefix'],
			[
				'label' => $args['title'],
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			$args['prefix'] . '_subtitle_style_section',
			[
				'label' => __( 'Subtitle', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			$args['prefix'] . '_subtitle_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $args['subtitle_selector'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $args['prefix'] . '_subtitle_typography',
				'selector' => '{{WRAPPER}} ' . $args['subtitle_selector'],
				'fields_options' => $args['subtitle_options']
			]
		);

		$this->add_control(
			$args['prefix'] . '_title_style_section',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$args['prefix'] . '_title_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $args['title_selector'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $args['prefix'] . '_title_typography',
				'selector' => '{{WRAPPER}} ' . $args['title_selector'],
				'fields_options' => $args['title_options']
			]
		);

		$this->add_control(
			$args['prefix'] . '_desc_style_section',
			[
				'label' => __( 'Description', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$args['prefix'] . '_desc_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $args['desc_selector'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $args['prefix'] . '_desc_typography',
				'selector' => '{{WRAPPER}} ' . $args['desc_selector'],
				'fields_options' => $args['desc_options']
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_style_section',
			[
				'label' => __( 'Button', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( $args['prefix'] . '_button_style_tabs' );

		$this->start_controls_tab(
			$args['prefix'] . '_tab_button_normal',
			[
				'label' => __( 'Normal', 'sober' ),
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_text_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} '. $args['button_selector'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_background_color',
			[
				'label' => __( 'Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $args['button_selector'] . '.sober-banner-image__button--normal' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$args['prefix'] . '_button_type' => 'normal',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$args['prefix'] . '_tab_button_hover',
			[
				'label' => __( 'Hover', 'sober' ),
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_text_hover_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ' . $args['button_selector'] . ':hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_background_hover_color',
			[
				'label' => __( 'Background', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $args['button_selector'] . '.sober-banner-image__button--normal:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $args['button_selector'] . '.sober-banner-image__button--outline:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
				'condition' => [
					$args['prefix'] . '_button_type' => ['normal', 'outline'],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

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
			'sober-banner-grid-4',
			'sober-banner-grid-4--elementor',
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<?php
				$prefix       = 'banner' . $i;
				$wrapper_key  = $prefix . '_wrapper';
				$link_key     = $prefix . '_link';
				$image_key    = $prefix . '_image';
				$content_key  = $prefix . '_content';
				$subtitle_key = $prefix . '_subtitle';
				$title_key    = $prefix . '_title';
				$desc_key     = $prefix . '_description';
				$button_key   = $prefix . '_button';

				$this->add_render_attribute( $wrapper_key, 'class', [
					'sober-banner-grid-4__banner',
					'sober-banner-grid-4__banner-' . $i,
					'sober-banner-grid__banner',
					'sober-banner-image',
					'sober-banner-image-' . $i,
					'sober-banner-image--hover-' . $settings[$prefix . '_image_hover'],
					'sober-banner-image--button-visible-' . $settings[$prefix . '_button_visible'],
					'sober-banner-grid-4__banner--content-' . $settings[$prefix . '_content_position'],
					'sober-banner-grid__banner--content-' . $settings[$prefix . '_content_position'],
				] );

				$this->add_render_attribute( $link_key, 'class', ['sober-banner-image__link', 'sober-banner-image-' . $i . '__link'] );

				if ( ! empty( $settings[$link_key]['url'] ) ) {
					$this->add_render_attribute( $link_key, 'href', $settings[$link_key]['url'] );

					if ( $settings[$link_key]['is_external'] ) {
						$this->add_render_attribute( $link_key, 'target', '_blank' );
					}

					if ( $settings[$link_key]['nofollow'] ) {
						$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
					}
				}

				$this->add_render_attribute( $image_key, 'class', [
					'sober-banner-image__image',
					'sober-banner-image-' . $i . '__image',
				] );

				if ( ! empty( $settings[$image_key]['id'] ) ) {
					$image_src = Group_Control_Image_Size::get_attachment_image_src( $settings[$image_key]['id'], 'image', [
						'image_size' => 'custom',
						'image_custom_dimension' => $this->get_image_size( $i ),
					] );

					if ( $image_src ) {
						$this->add_render_attribute( $image_key, 'src', $image_src );
					}

					$this->add_render_attribute( $image_key, 'alt', Control_Media::get_image_alt( $settings[$image_key] ) );
				} elseif ( ! empty( $settings[$image_key]['url'] ) ) {
					$this->add_render_attribute( $image_key, 'src', $settings[$image_key]['url'] );
				} else {
					$size = $this->get_image_size( $i );
					$this->add_render_attribute( $image_key, 'src', 'https://via.placeholder.com/' . $size['width'] . 'x' . $size['height'] );
				}

				$this->add_render_attribute( $content_key, 'class', [
					'sober-banner-image__content',
					'sober-banner-image-' . $i . '__content',
				] );

				$this->add_render_attribute( $subtitle_key, 'class', [ 'sober-banner-image__subtitle', 'sober-banner-image-' . $i . '__subtitle' ] );
				$this->add_inline_editing_attributes( $subtitle_key, 'none' );

				$this->add_render_attribute( $title_key, 'class', [ 'sober-banner-image__title', 'sober-banner-image-' . $i . '__title' ] );
				$this->add_inline_editing_attributes( $title_key, 'none' );

				$this->add_render_attribute( $desc_key, 'class', [ 'sober-banner-image__description', 'sober-banner-image-' . $i . '__description' ] );
				$this->add_inline_editing_attributes( $desc_key );

				$this->add_render_attribute( $button_key, 'class', [ 'sober-banner-image__button', 'sober-banner-image-' . $i . '__button', 'sober-banner-image__button--' . $settings[ $button_key . '_type' ] ] );
				?>
				<div <?php echo $this->get_render_attribute_string( $wrapper_key ); ?>>
					<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
						<img <?php echo $this->get_render_attribute_string( $image_key ); ?>>
						<div <?php echo $this->get_render_attribute_string( $content_key ); ?>>
							<?php if ( ! empty( $settings[$subtitle_key] ) ) : ?>
								<div <?php echo $this->get_render_attribute_string( $subtitle_key ) ?>><?php echo esc_html( $settings[$subtitle_key] ) ?></div>
							<?php endif; ?>

							<?php if ( ! empty( $settings[$title_key] ) ) : ?>
								<div <?php echo $this->get_render_attribute_string( $title_key ) ?>><?php echo wp_kses_post( $settings[$title_key] ) ?></div>
							<?php endif; ?>

							<?php if ( ! empty( $settings[$desc_key] ) ) : ?>
								<div <?php echo $this->get_render_attribute_string( $desc_key ) ?>><?php echo wp_kses_post( $settings[$desc_key] ) ?></div>
							<?php endif; ?>

							<?php if ( ! empty( $settings[$button_key . '_text'] ) ) : ?>
								<div class="sober-banner-image__buttons sober-banner-image-<?php echo $i; ?>__buttons">
									<span <?php echo $this->get_render_attribute_string( $button_key ) ?>><?php echo esc_html( $settings[$button_key . '_text'] ) ?></span>
								</div>
							<?php endif; ?>
						</div>
					</a>
				</div>
			<?php endfor; ?>
		</div>
		<?php
	}

	/**
	 * Get the right image size for each image.
	 *
	 * @param int $index The image index.
	 * @return array
	 */
	protected function get_image_size( $index ) {
		$sizes = [
			1 => [
				'width' => 920,
				'height' => 820,
			],
			2 => [
				'width' => 460,
				'height' => 410,
			],
			3 => [
				'width' => 460,
				'height' => 410,
			],
			4 => [
				'width' => 920,
				'height' => 410,
			],
		];

		if ( isset( $sizes[ $index ] ) ) {
			return $sizes[ $index ];
		}

		return 'full';
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		var sizes = {
				1: {
					width: 920,
					height: 820
				},
				2: {
					width: 460,
					height: 410
				},
				3: {
					width: 460,
					height: 410
				},
				4: {
					width: 920,
					height: 410
				}
			};
		#>
		<div class="sober-banner-grid-4 sober-banner-grid-4--elementor">
			<# for (var i = 1; i <= 4; i ++ ) {
				var prefix       = 'banner' + i,
					wrapper_key  = prefix + '_wrapper',
					link_key     = prefix + '_link',
					image_key    = prefix + '_image',
					content_key  = prefix + '_content',
					subtitle_key = prefix + '_subtitle',
					title_key    = prefix + '_title',
					desc_key     = prefix + '_description',
					button_key   = prefix + '_button';

				view.addRenderAttribute( wrapper_key, 'class', [
					'sober-banner-grid-4__banner',
					'sober-banner-grid-4__banner-' + i,
					'sober-banner-image',
					'sober-banner-image-' + i,
					'sober-banner-image--hover-' + settings[prefix + '_image_hover'],
					'sober-banner-image--button-visible-' + settings[prefix + '_button_visible'],
					'sober-banner-grid-4__banner--content-' + settings[prefix + '_content_position'],
				] );

				view.addRenderAttribute( link_key, 'class', ['sober-banner-image__link', 'sober-banner-image-' + i + '__link'] );

				if ( settings[link_key]['url'] ) {
					view.addRenderAttribute( link_key, 'href', settings[link_key]['url'] );

					if ( settings[link_key]['is_external'] ) {
						view.addRenderAttribute( link_key, 'target', '_blank' );
					}

					if ( settings[link_key]['nofollow'] ) {
						view.addRenderAttribute( link_key, 'rel', 'nofollow' );
					}
				}

				view.addRenderAttribute( image_key, 'class', [
					'sober-banner-image__image',
					'sober-banner-image-' + i + '__image',
				] );

				if ( settings[image_key]['id'] ) {
					var image = {
						id: settings[image_key]['id'],
						url: settings[image_key]['url'],
						size: 'custom',
						dimension: sizes[i],
						model: view.getEditModel()
					};

					var image_src = elementor.imagesManager.getImageUrl( image );

					if ( image_src ) {
						view.addRenderAttribute( image_key, 'src', image_src );
					}
				} else if ( settings[image_key]['url'] ) {
					view.addRenderAttribute( image_key, 'src', settings[image_key]['url'] );
				} else {
					view.addRenderAttribute( image_key, 'src', 'https://via.placeholder.com/' + sizes[i]['width'] + 'x' + sizes[i]['height'] );
				}

				view.addRenderAttribute( content_key, 'class', [
					'sober-banner-image__content',
					'sober-banner-image-' + i + '__content',
				] );

				view.addRenderAttribute( subtitle_key, 'class', [ 'sober-banner-image__subtitle', 'sober-banner-image-' + i + '__subtitle' ] );
				view.addInlineEditingAttributes( subtitle_key, 'none' );

				view.addRenderAttribute( title_key, 'class', [ 'sober-banner-image__title', 'sober-banner-image-' + i + '__title' ] );
				view.addInlineEditingAttributes( title_key, 'none' );

				view.addRenderAttribute( desc_key, 'class', [ 'sober-banner-image__description', 'sober-banner-image-' + i + '__description' ] );
				view.addInlineEditingAttributes( desc_key );

				view.addRenderAttribute( button_key, 'class', [ 'sober-banner-image__button', 'sober-banner-image-' + i + '__button', 'sober-banner-image__button--' + settings[ button_key + '_type' ] ] );
			#>
				<div {{{ view.getRenderAttributeString( wrapper_key ) }}}>
					<a {{{ view.getRenderAttributeString( link_key ) }}}>
						<img {{{ view.getRenderAttributeString( image_key ) }}}>
						<div {{{ view.getRenderAttributeString( content_key ) }}}>
							<# if ( settings[subtitle_key] ) { #>
								<div {{{ view.getRenderAttributeString( subtitle_key ) }}}>{{ settings[subtitle_key] }}</div>
							<# } #>

							<# if ( settings[title_key] ) { #>
								<div {{{ view.getRenderAttributeString( title_key ) }}}>{{{ settings[title_key] }}}</div>
							<# } #>

							<# if ( settings[desc_key] ) { #>
								<div {{{ view.getRenderAttributeString( desc_key ) }}}>{{{ settings[desc_key] }}}</div>
							<# } #>

							<# if ( settings[button_key + '_text'] ) { #>
								<div class="sober-banner-image__buttons sober-banner-image-{{{ i }}}__buttons">
									<span {{{ view.getRenderAttributeString( button_key ) }}}>{{ settings[button_key + '_text'] }}</span>
								</div>
							<# } #>
						</div>
					</a>
				</div>
			<# } #>
		</div>
		<?php
	}
}
