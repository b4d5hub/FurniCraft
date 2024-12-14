<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner widget
 */
class Banner_Simple extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-banner-simple';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Banner Simple', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-rollover';
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
		return [ 'banner', 'image', 'sober' ];
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
			'section_banner_image',
			[ 'label' => __( 'Banner', 'sober' ) ]
		);

		$this->add_control(
			'image_source',
			[
				'label' => __( 'Image Source', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media',
				'options' => [
					'media'    => __( 'Media Library', 'sober' ),
					'external' => __( 'External Image', 'sober' ),
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src()
				],
				'condition' => [
					'image_source' => 'media',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'condition' => [
					'image_source' => 'media',
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'image_source' => 'external',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Banner Title', 'sober' ),
				'placeholder' => __( 'Title', 'sober' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'p' => 'p',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Banner Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'sober' ),
				'default' => [
					'url' => '#',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Read More', 'sober' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Banner', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'desktop_default' => 'center',
				'tablet_default' => '',
				'mobile_default' => '',
				'separator' => 'before',
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
						'title' => __( 'Justify', 'sober' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-banner-simple' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .sober-banner-simple__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Title Typography', 'sober' ),
				'selector' => '{{WRAPPER}} .sober-banner-simple__text',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
					'font_weight' => [
						'default' => '300',
					],
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'sober-banner-simple', 'sober-banner-simple--elementor' ] );

		if ( ! empty( $settings['button_text'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'sober-banner-simple--has-button' );
		}

		if ( 'external' == $settings['image_source'] ) {
			$image = sprintf( '<img src="%s" alt="%s">', esc_url( $settings['image_url'] ), esc_attr( $settings['title'] ) );
		} else {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<a <?php echo $this->get_render_attribute_string( 'link' ) ?>>
				<?php echo $image; ?>
			</a>

			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo $settings['title_size']; ?> class="sober-banner-simple__text">
					<a <?php echo $this->get_render_attribute_string( 'link' ) ?>><?php echo $settings['title'] ?></a>
				</<?php echo $settings['title_size']; ?>>
			<?php endif; ?>

			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<p class="sober-banner-simple__button">
					<a <?php echo $this->get_render_attribute_string( 'link' ) ?>><?php echo esc_html( $settings['button_text'] ) ?></a>
				</p>
			<?php endif; ?>
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
		var image_url = '<?php echo Utils::get_placeholder_image_src(); ?>';

		if ( 'external' === settings.image_source ) {
			image_url = settings.image_url ? settings.image_url : image_url;
		} else if ( settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};

			image_url = elementor.imagesManager.getImageUrl( image );
		}

		view.addRenderAttribute( 'wrapper', 'class', [
			'sober-banner-simple',
			'sober-banner-simple--elementor'
		] );

		if ( settings.button_text ) {
			view.addRenderAttribute( 'wrapper', 'class', 'sober-banner-simple--has-button' );
		}

		if ( settings.link.url ) {
			view.addRenderAttribute( 'link', 'href', settings.link.url );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'link', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'link', 'rel', 'nofollow' );
			}
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<a {{{ view.getRenderAttributeString( 'link' ) }}}>
				<img src="{{ image_url }}">
			</a>
			<# if ( settings.title ) { #>
				<{{{ settings.title_size }}} class="sober-banner-simple__text">
					<a {{{ view.getRenderAttributeString( 'link' ) }}}>{{{ settings.title }}}</a>
				</{{{ settings.title_size }}}>
			<# } #>
			<# if ( settings.button_text ) { #>
				<p class="sober-banner-simple__button">
					<a {{{ view.getRenderAttributeString( 'link' ) }}}>{{ settings.button_text }}</a>
				</p>
			<# } #>
		</div>
		<?php
	}
}
