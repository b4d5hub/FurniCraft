<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product banner widget
 */
class Product_Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-product';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Product Banner', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-bag-light';
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
		return [ 'product banner', 'product', 'banner', 'sober' ];
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
			'section_product_banner',
			[ 'label' => __( 'Product Banner', 'sober' ) ]
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
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_source' => 'media',
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'External Image', 'sober' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => Utils::get_placeholder_image_src(),
				'condition' => [
					'image_source' => 'external',
				],
			]
		);

		$this->add_control(
			'product_name',
			[
				'label' => __( 'Product Name', 'sober' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Product Name', 'sober' ),
			]
		);

		$this->add_control(
			'product_desc',
			[
				'label' => __( 'Product Description', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Product description', 'sober' ),
			]
		);

		$this->add_control(
			'product_price',
			[
				'label' => __( 'Product Price', 'sober' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 100,
				'min' => 0,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link',
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
			'button_text',
			[
				'label' => __( 'Button Text', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Add To Cart', 'sober' ),
				'default' => __( 'Add To Cart', 'sober' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_product_banner',
			[
				'label' => __( 'Product Banner', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'white',
				'options' => [
					'dark' => __( 'Dark', 'sober' ),
					'white' => __( 'White', 'sober' ),
					'custom' => __( 'Custom', 'sober' ),
				],
			]
		);

		$this->add_control(
			'custom_color',
			[
				'label' => __( 'Custom Color', 'sober' ),
				'show_label' => false,
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'color_scheme' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-product' => 'color: {{VALUE}};',
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
			'sober-product-banner',
			'sober-product',
			'sober-product--elementor',
			'sober-product-banner--color-' . $settings['color_scheme'],
		] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );
			$this->add_render_attribute( 'link', 'class', ['sober-product-banner__link', 'overlink'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		$this->add_render_attribute( 'image_holder', 'class', ['sober-product-banner__image', 'product-image'] );

		if ( 'external' == $settings['image_source'] && ! empty( $settings['image_url'] ) ) {
			$this->add_render_attribute( 'image_holder', 'style', 'background-image: url(' . $settings['image_url'] . ')' );
			$this->add_render_attribute( 'image', 'src', $settings['image_url'] );
			$this->add_render_attribute( 'image', 'alt', $settings['product_name'] );
		} elseif ( ! empty( $settings['image']['url'] ) ) {
			$this->add_render_attribute( 'image_holder', 'style', 'background-image: url(' . $settings['image']['url'] . ')' );

			$this->add_render_attribute( 'image', 'src', $settings['image']['url'] );
			$this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $settings['image'] ) );
		}

		$this->add_render_attribute( 'product_name', 'class', ['sober-product-banner__product-name', 'product-title'] );
		$this->add_inline_editing_attributes( 'product_name', 'none' );

		$this->add_render_attribute( 'product_desc', 'class', ['sober-product-banner__product-desc', 'product-desc'] );
		$this->add_inline_editing_attributes( 'product_desc', 'none' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div  <?php echo $this->get_render_attribute_string( 'image_holder' ) ?>>
				<?php
				if ( ! empty( $settings['image']['url'] ) ) {
					echo '<img ' . $this->get_render_attribute_string( 'image' ) . '/>';
				}
				?>
			</div>
			<div class="product-info sober-product-banner__info">
				<h3 <?php echo $this->get_render_attribute_string( 'product_name' ) ?>><?php echo esc_html( $settings['product_name'] ) ?></h3>
				<?php if ( ! empty( $settings['product_desc'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'product_desc' ) ?>><?php echo wp_kses_post( $settings['product_desc'] ) ?></div>
				<?php endif; ?>
				<div class="sober-product-banner__product-price product-price">
					<span class="price"><?php echo wc_price( floatval( $settings['product_price'] ) ) ?></span>
					<span class="button"><?php echo esc_html( $settings['button_text'] ) ?></span>
				</div>
			</div>
			<a <?php echo $this->get_render_attribute_string( 'link' ) ?>><?php esc_html_e( 'View Product', 'sober' ) ?></a>
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
			'sober-product-banner',
			'sober-product',
			'sober-product--elementor',
			'sober-product-banner--color-' + settings.color_scheme,
		] );

		if ( settings.link.url ) {
			view.addRenderAttribute( 'link', 'href', settings.link.url );
			view.addRenderAttribute( 'link', 'class', ['sober-product-banner__link', 'overlink'] );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'link', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'link', 'rel', 'nofollow' );
			}
		}

		view.addRenderAttribute( 'image_holder', 'class', ['sober-product-banner__image', 'product-image'] );

		if ( 'external' === settings.image_source && settings.image_url ) {
			view.addRenderAttribute( 'image_holder', 'style', 'background-image: url(' + settings.image_url + ')' );
			view.addRenderAttribute( 'image', 'src', settings.image_url );
		} else if ( settings.image.url ) {
			view.addRenderAttribute( 'image_holder', 'style', 'background-image: url(' + settings.image.url + ')' );
			view.addRenderAttribute( 'image', 'src', settings.image.url );
		}

		view.addRenderAttribute( 'product_name', 'class', ['sober-product-banner__product-name', 'product-title'] );
		view.addInlineEditingAttributes( 'product_name', 'none' );

		view.addRenderAttribute( 'product_desc', 'class', ['sober-product-banner__product-desc', 'product-desc'] );
		view.addInlineEditingAttributes( 'product_desc', 'none' );

		var currency = {
			decimals     : <?php echo wc_get_price_decimals() ?>,
			symbol       : '<?php echo get_woocommerce_currency_symbol() ?>',
			decimal_sep  : '<?php echo esc_attr( wc_get_price_decimal_separator() ) ?>',
			thousand_sep : '<?php echo esc_attr( wc_get_price_thousand_separator() ) ?>',
			format       : '<?php echo esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ) ?>',
		};

		var regex = '\\d(?=(\\d{3})+' + (currency.decimals > 0 ? '\\D' : '$') + ')';
		var priceValue = settings.product_price ? settings.product_price.toFixed( currency.decimals ).toString().replace( new RegExp( regex, 'g' ), '$&' + currency.thousand_sep ) : '';
		var price = currency.format.replace( '%s', currency.symbol ).replace( '%v', priceValue );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'image_holder' ) }}}>
				<# if ( settings.image.url ) { #>
					<img {{{ view.getRenderAttributeString( 'image' ) }}}>
				<# } #>
			</div>
			<div class="product-info sober-product-banner__info">
				<h3 {{{ view.getRenderAttributeString( 'product_name' ) }}}>{{{ settings.product_name }}}</h3>
				<# if ( settings.product_desc ) { #>
					<div {{{ view.getRenderAttributeString( 'product_desc' ) }}}>{{{ settings.product_desc }}}</div>
				<# } #>
				<div class="sober-product-banner__product-price product-price">
					<span class="price">{{{ price }}}</span>
					<span class="button">{{{ settings.button_text }}}</span>
				</div>
			</div>
			<a {{{ view.getRenderAttributeString( 'link' ) }}}><?php esc_html_e( 'View Product', 'sober' ) ?></a>
		</div>
		<?php
	}
}
