<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner grid widget with 5 banners
 */
class Banner_Grid_5 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-banner-grid-5';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Banner Grid 5', 'sober' );
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
		return [ 'banner grid 5', 'banner grid', 'banner', 'grid', 'sober' ];
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
			'prefix' => 'banner1',
			'title'  => __( 'Banner 1', 'sober' ),
		] );

		$this->register_banner_controls( [
			'prefix' => 'banner2',
			'title'  => __( 'Banner 2', 'sober' ),
		] );

		$this->register_banner_controls( [
			'prefix'         => 'banner3',
			'title'          => __( 'Banner 3', 'sober' ),
			'double_buttons' => true,
		] );

		$this->register_banner_controls( [
			'prefix' => 'banner4',
			'title'  => __( 'Banner 4', 'sober' ),
		] );

		$this->register_banner_controls( [
			'prefix' => 'banner5',
			'title'  => __( 'Banner 5', 'sober' ),
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
			'prefix'         => 'banner',
			'title'          => __( 'Banner', 'sober' ),
			'double_buttons' => false,
		] );

		$this->start_controls_section(
			'section_' . $args['prefix'],
			[
				'label' => $args['title'],
			]
		);

		$this->add_control(
			$args['prefix'] . '_image_heading',
			[
				'label' => __( 'Image', 'sober' ),
				'type' => Controls_Manager::HEADING,
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
			$args['prefix'] . '_button_heading',
			[
				'label' => __( 'Button', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$args['prefix'] . '_button_text',
			[
				'label' => __( 'Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'sober' ),
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

		if ( $args['double_buttons'] ) {
			$this->add_control(
				$args['prefix'] . '_second_button_heading',
				[
					'label' => __( 'Second Button', 'sober' ),
					'type' => Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				$args['prefix'] . '_second_button_text',
				[
					'label' => __( 'Text', 'sober' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Button Text', 'sober' ),
				]
			);

			$this->add_control(
				$args['prefix'] . '_second_link',
				[
					'label' => __( 'Link', 'sober' ),
					'type' => Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'sober' ),
					'default' => [
						'url' => '#',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="sober-banner-grid-5 sober-banner-grid-5--elementor">
			<div class="sober-banner-grid-5__wrapper banners-wrap">
				<div class="sober-banner-grid-5__column-1 banners banners-column-1">
					<?php
					$this->render_banner( 1, $settings );
					$this->render_banner( 2, $settings );
					?>
				</div>
				<div class="sober-banner-grid-5__column-2 banners banners-column-2">
					<?php
					$this->render_banner( 3, $settings );
					?>
				</div>
				<div class="sober-banner-grid-5__column-3 banners banners-column-3">
					<?php
					$this->render_banner( 4, $settings );
					$this->render_banner( 5, $settings );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single banner
	 *
	 * @param int $index
	 * @param array $settings
	 * @return void
	 */
	protected function render_banner( $index, $settings ) {
		$prefix      = 'banner' . $index;
		$wrapper_key = $prefix . '_wrapper';
		$image_key   = $prefix . '_image';
		$button_key  = $prefix . '_button';
		$button2_key = $prefix . '_second_button';
		$link_key    = $prefix . '_link';
		$link2_key   = $prefix . '_second_link';
		$button      = '';
		$button2     = '';

		$this->add_render_attribute( $wrapper_key, 'class', [
			'sober-banner-grid-5__banner',
			'sober-banner-grid-5__banner-' . $index,
			'sober-banner-grid__banner',
			'sober-banner-image',
			'sober-banner-image-' . $index,
		] );

		if ( ! empty( $settings[$button_key . '_text'] ) ) {
			$this->add_render_attribute( $button_key, 'class', ['sober-banner-image__button', 'sober-banner-image-' . $index . '__button'] );
			$this->add_render_attribute( $button_key, 'href', $settings[$link_key]['url'] );

			if ( $settings[$link_key]['is_external'] ) {
				$this->add_render_attribute( $button_key, 'target', '_blank' );
			}

			if ( $settings[$link_key]['nofollow'] ) {
				$this->add_render_attribute( $button_key, 'rel', 'nofollow' );
			}

			$button = sprintf( '<a %s>%s</a>', $this->get_render_attribute_string( $button_key ), esc_html( $settings[$button_key . '_text'] ) );
		}

		if ( ! empty( $settings[$button2_key . '_text'] ) ) {
			$this->add_render_attribute( $button2_key, 'class', ['sober-banner-image__button', 'sober-banner-image__button-2', 'sober-banner-image-' . $index . '__button', 'sober-banner-image-' . $index . '__button-2'] );
			$this->add_render_attribute( $button2_key, 'href', $settings[$link2_key]['url'] );

			if ( $settings[$link2_key]['is_external'] ) {
				$this->add_render_attribute( $button2_key, 'target', '_blank' );
			}

			if ( $settings[$link2_key]['nofollow'] ) {
				$this->add_render_attribute( $button2_key, 'rel', 'nofollow' );
			}

			$button2 = sprintf( '<a %s>%s</a>', $this->get_render_attribute_string( $button2_key ), esc_html( $settings[$button2_key . '_text'] ) );
		}

		$this->add_render_attribute( $image_key, 'class', [
			'sober-banner-image__image',
			'sober-banner-image-' . $index . '__image',
		] );

		if ( ! empty( $settings[$image_key]['id'] ) ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $settings[$image_key]['id'], 'image', [
				'image_size' => 'custom',
				'image_custom_dimension' => $this->get_image_size( $index ),
			] );

			if ( $image_src ) {
				$this->add_render_attribute( $image_key, 'src', $image_src );
				$this->add_render_attribute( $wrapper_key, 'style','background-image: url(' . $image_src . ')' );
			}

			$this->add_render_attribute( $image_key, 'alt', Control_Media::get_image_alt( $settings[$image_key] ) );
		} elseif ( ! empty( $settings[$image_key]['url'] ) ) {
			$this->add_render_attribute( $image_key, 'src', $settings[$image_key]['url'] );
			$this->add_render_attribute( $wrapper_key, 'style','background-image: url(' . $settings[$image_key]['url'] . ')' );
		} else {
			$size = $this->get_image_size( $index );
			$image_src = 'https://via.placeholder.com/' . $size['width'] . 'x' . $size['height'];
			$this->add_render_attribute( $image_key, 'src', $image_src );
			$this->add_render_attribute( $wrapper_key, 'style','background-image: url(' . $image_src . ')' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( $wrapper_key ); ?>>
			<img <?php echo $this->get_render_attribute_string( $image_key ); ?>>

			<div class="sober-banner-image__buttons sober-banner-image-<?php echo $index; ?>__buttons">
				<?php echo $button; ?>
				<?php echo $button2; ?>
			</div>
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
				'width' => 520,
				'height' => 400,
			],
			2 => [
				'width' => 520,
				'height' => 500,
			],
			3 => [
				'width' => 750,
				'height' => 920,
			],
			4 => [
				'width' => 520,
				'height' => 500,
			],
			5 => [
				'width' => 520,
				'height' => 400,
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
					width: 520,
					height: 400
				},
				2: {
					width: 520,
					height: 500
				},
				3: {
					width: 750,
					height: 920
				},
				4: {
					width: 520,
					height: 500
				},
				5: {
					width: 520,
					height: 400
				},
			};
		var columOpenTag = '',
			columnCloseTag = '';
		#>
		<div class="sober-banner-grid-5 sober-banner-grid-5--elementor">
			<div class="sober-banner-grid-5__wrapper banners-wrap">
			<# for ( var i = 1; i <= 5; i ++ ) {
				switch ( i ) {
					case 1:
						columOpenTag = '<div class="sober-banner-grid-5__column-1 banners banners-column-1">';
						columnCloseTag = '';
						break;
					case 3:
						columOpenTag = '<div class="sober-banner-grid-5__column-2 banners banners-column-2">';
						columnCloseTag = '</div>';
						break;
					case 4:
						columOpenTag = '<div class="sober-banner-grid-5__column-3 banners banners-column-3">';
						columnCloseTag = '';
						break;
					case 2:
					case 5:
						columOpenTag = '';
						columnCloseTag = '</div>';
						break;
				}

				var prefix      = 'banner' + i,
					wrapper_key = prefix + '_wrapper',
					image_key   = prefix + '_image',
					button_key  = prefix + '_button',
					link_key    = prefix + '_link',
					button2_key = prefix + '_second_button',
					link2_key   = prefix + '_second_link';

				view.addRenderAttribute( wrapper_key, 'class', [
					'sober-banner-grid-5__banner',
					'sober-banner-grid-5__banner-' + i,
					'sober-banner-grid__banner',
					'sober-banner-image',
					'sober-banner-image-' + i
				] );

				if ( settings[button_key + '_text'] ) {
					view.addRenderAttribute( button_key, 'class', ['sober-banner-image__button', 'sober-banner-image-' + i + '__button'] );
					view.addRenderAttribute( button_key, 'href', settings[link_key]['url'] );

					if ( settings[link_key]['is_external'] ) {
						view.addRenderAttribute( button_key, 'target', '_blank' );
					}

					if ( settings[link_key]['nofollow'] ) {
						view.addRenderAttribute( button_key, 'rel', 'nofollow' );
					}
				}

				if ( settings[button2_key + '_text'] ) {
					view.addRenderAttribute( button2_key, 'class', [
						'sober-banner-image__button',
						'sober-banner-image__button-2',
						'sober-banner-image-' + i + '__button',
						'sober-banner-image-' + i + '__button-2'
					] );
					view.addRenderAttribute( button2_key, 'href', settings[link2_key]['url'] );

					if ( settings[link2_key]['is_external'] ) {
						view.addRenderAttribute( button2_key, 'target', '_blank' );
					}

					if ( settings[link2_key]['nofollow'] ) {
						view.addRenderAttribute( button2_key, 'rel', 'nofollow' );
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
						view.addRenderAttribute( image_key, 'style', 'background-image: url(' + image_src + ')' );
					}
				} else if ( settings[image_key]['url'] ) {
					view.addRenderAttribute( image_key, 'src', settings[image_key]['url'] );
					view.addRenderAttribute( image_key, 'style', 'background-image: url(' + settings[image_key]['url'] + ')' );
				} else {
					var image_src = 'https://via.placeholder.com/' + sizes[i]['width'] + 'x' + sizes[i]['height'];
					view.addRenderAttribute( image_key, 'src', image_src );
					view.addRenderAttribute( image_key, 'style', 'background-image: url(' + image_src + ')' );
				}
			#>
			{{{ columOpenTag }}}
				<div {{{ view.getRenderAttributeString( wrapper_key ) }}}>
					<img {{{ view.getRenderAttributeString( image_key ) }}}>

					<div class="sober-banner-image__buttons sober-banner-image-{{{ i }}}__buttons">
						<# if ( settings[button_key + '_text'] ) { #>
							<a {{{ view.getRenderAttributeString( button_key ) }}}>{{ settings[button_key + '_text'] }}</a>
						<# } #>
						<# if ( settings[button2_key + '_text'] ) { #>
							<a {{{ view.getRenderAttributeString( button2_key ) }}}>{{ settings[button2_key + '_text'] }}</a>
						<# } #>
					</div>
				</div>
			{{{ columnCloseTag }}}
			<# } #>
			</div>
		</div>
		<?php
	}
}
