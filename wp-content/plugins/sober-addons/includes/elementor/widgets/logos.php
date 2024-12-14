<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Logos widget
 */
class Logos extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-logos';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Logos', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-logo';
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
		return [ 'logos', 'images', 'sober' ];
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
			'section_logos',
			[ 'label' => __( 'Logos', 'sober' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'source',
			[
				'label' => __( 'Source', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => __( 'Image', 'sober' ),
					'external' => __( 'External Image', 'sober' ),
				],
				'default' => 'image',
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'source' => 'image',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'condition' => [
					'source' => 'image',
				],
			]
		);

		$repeater->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'source' => 'external',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'sober' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'logos',
			[
				'label'   => __( 'Logo Images', 'sober' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
			]
		);

		$gallery_columns = range( 1, 10 );
		$gallery_columns = array_combine( $gallery_columns, $gallery_columns );

		$this->add_control(
			'columns',
			[
				'label' => __( 'Maximum Columns', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 4,
				'options' => $gallery_columns,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logos_style',
			[
				'label' => __( 'Logos', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'logos_spacing',
			[
				'label' => __( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'show_label' => false,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .sober-logos__logo' => 'padding: calc({{SIZE}}{{UNIT}} / 2)',
					'{{WRAPPER}} .sober-logos' => 'margin: calc(-{{SIZE}}{{UNIT}} / 2)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'logo_border',
				'selector' => '{{WRAPPER}} .sober-logos__logo-inner',
				'separator' => 'before',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '2',
							'right' => '2',
							'bottom' => '2',
							'left' => '2',
							'isLinked' => true,
						],
					],
					'color' => [
						'default' => '#f1f2f4',
					],
				],
			]
		);

		$this->add_control(
			'logo_border_radius',
			[
				'label' => __( 'Border Radius', 'sober' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sober-logos__logo-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'sober-logos', 'sober-logos--columns-' . $settings['columns'] ] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php foreach ( $settings['logos'] as $logo ) : ?>
				<figure class="sober-logos__logo">
					<?php
					$logo_key = 'logo_' . $logo['_id'];
					$image_html = '';

					$this->add_render_attribute( $logo_key, 'class', [ 'sober-logos__logo-inner' ] );

					if ( ! empty( $logo['link']['url'] ) ) {
						$this->add_link_attributes( $logo_key, $logo['link'] );
					}

					if ( 'image' == $logo['source'] ) {
						$image_html = Group_Control_Image_Size::get_attachment_image_html( $logo, 'thumbnail', 'image' );
					} else {
						$image_html = sprintf( '<img src="%s" alt="">', esc_url( $logo['image_url'] ) );
					}

					if ( $image_html ) {
						if ( ! empty( $logo['link']['url'] ) ) {
							$image_html = '<a ' . $this->get_render_attribute_string( $logo_key ) . '>' . $image_html . '</a>';
						} else {
							$image_html = '<span ' . $this->get_render_attribute_string( $logo_key ) . '>' . $image_html . '</span>';
						}
					}

					echo $image_html;
					?>
				</figure>
			<?php endforeach; ?>
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
		<div class="sober-logos sober-logos--columns-{{ settings.columns }}">
			<# _.each( settings.logos, function( logo ) { #>
				<#
				var logoKey = logo['_id'];
				var imageHtml = '';

				view.addRenderAttribute( logoKey, 'class', [ 'sober-logos__logo-inner' ] );

				if ( logo.link.url ) {
					view.addRenderAttribute( logoKey, 'href', logo.link.url );
				}

				if ( 'image' === logo.source && logo.image.url ) {
					var image = {
						id: logo.image.id,
						url: logo.image.url,
						size: logo.thumbnail_size,
						dimension: logo.thumbnail_custom_dimension,
						model: view.getEditModel()
					};

					var image_url = elementor.imagesManager.getImageUrl( image );

					imageHtml = '<img src="' + image_url + '" />';
				} else if ( 'external' === logo.source && logo.image_url ) {
					imageHtml = '<img src="' + logo.image_url + '">';
				}

				if ( imageHtml ) {
					if ( logo.link.url ) {
						imageHtml = '<a ' + view.getRenderAttributeString( logoKey ) + '>' + imageHtml + '</a>';
					} else {
						imageHtml = '<span ' + view.getRenderAttributeString( logoKey ) + '>' + imageHtml + '</span>';
					}
				}
				#>
				<figure class="sober-logos__logo">{{{ imageHtml }}}</figure>
			<# } ); #>
		</div>
		<?php
	}
}
