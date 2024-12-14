<?php
/**
 * Display attribute swatches
 */
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Helper;
use WCBoost\VariationSwatches\Admin\Term_Meta;

class Swatches {
	/**
	 * The single instance of the class
	 *
	 * @var WCBoost\VariationSwatches\Swatches
	 */
	protected static $_instance = null;

	/**
	 * Main instance
	 *
	 * @return WCBoost\VariationSwatches\Swatches
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'swatches_html' ], 100, 2 );
	}

	/**
	 * Register scripts
	 *
	 * @since 1.0.17
	 *
	 * @return void
	 */
	public function register_scripts() {
		$version = Plugin::instance()->version;
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'wcboost-variation-swatches', plugins_url( 'assets/css/frontend.css', WCBOOST_VARIATION_SWATCHES_FILE ), [], $version );
		wp_register_script( 'wcboost-variation-swatches', plugins_url( 'assets/js/frontend' . $suffix . '.js', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'jquery' ], $version, true );
	}

	/**
	 * Enqueue scripts and stylesheets
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wcboost-variation-swatches' );

		$inline_css = $this->inline_style();

		if ( ! empty( $inline_css ) ) {
			wp_add_inline_style( 'wcboost-variation-swatches', $this->inline_style() );
		}

		wp_enqueue_script( 'wcboost-variation-swatches' );

		$params = apply_filters( 'wcboost_variation_swatches_js_params', [
			'show_selected_label' => wc_string_to_bool( Helper::get_settings( 'show_selected_label' ) ),
		] );

		if ( ! empty( $params ) ) {
			wp_localize_script( 'wcboost-variation-swatches', 'wcboost_variation_swatches_params', $params );
		}

		do_action( 'wcboost_variation_swatches_enqueue_scripts' );
	}

	/**
	 * Inline style for variation swatches. Generated from the settings.
	 *
	 * @return string The CSS code
	 */
	public function inline_style() {
		$size = Helper::get_settings( 'size' );

		$css = ':root { --wcboost-swatches-item-width: ' . absint( $size['width'] ) . 'px; --wcboost-swatches-item-height: ' . absint( $size['height'] ) . 'px; }';

		return apply_filters( 'wcboost_variation_swatches_css', $css );
	}

	/**
	 * Filter function to add swatches bellow the default selector
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string
	 */
	public function swatches_html( $html, $args ) {
		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$name      = $args['name'] ? $args['name'] : wc_variation_attribute_name( $attribute );

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		if ( empty( $options ) ) {
			return $html;
		}

		// Get per-product swatches settings.
		$swatches_args = $this->get_swatches_args( $product->get_id(), $attribute );
		$swatches_args = wp_parse_args( $args, $swatches_args );

		if ( ! Helper::is_swatches_type( $swatches_args['swatches_type'] ) ) {
			return $html;
		}

		// Let's render the swatches html.
		$swatches_html = '';

		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				[
					'fields' => 'all',
					'slug'   => $options,
				]
			);

			foreach ( $terms as $term ) {
				$swatches_html .= $this->get_term_swatches( $term, $swatches_args );
			}
		} else {
			foreach ( $options as $option ) {
				$swatches_html .= $this->get_term_swatches( $option, $swatches_args );
			}
		}

		if ( ! empty( $swatches_html ) ) {
			$classes       = [
				'wcboost-variation-swatches',
				'wcboost-variation-swatches--' . $swatches_args['swatches_type'],
				'wcboost-variation-swatches--' . $swatches_args['swatches_shape']
			];

			if ( $swatches_args['swatches_tooltip'] ) {
				$classes[] = 'wcboost-variation-swatches--has-tooltip';
			}

			$swatches_html = '<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="' . esc_attr( $name ) . '" role="group">' . $swatches_html . '</ul>';
			$html          = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $html . $swatches_html . '</div>';
		}

		return apply_filters( 'wcboost_variation_swatches_html', $html, $args );
	}

	/**
	 * Get HTML of a single attribute term swatches
	 *
	 * @param object|string $term
	 * @param array $args
	 * @return string
	 */
	public function get_term_swatches( $term, $args ) {
		$type  = $args['swatches_type'];
		$value = is_object( $term ) ? $term->slug : $term;
		$name  = is_object( $term ) ? $term->name : $term;
		$name  = apply_filters( 'woocommerce_variation_option_name', $name, ( is_object( $term ) ? $term : null ), $args['attribute'], $args['product'] );
		$size  = ! empty( $args['swatches_size'] ) ? sprintf( '--wcboost-swatches-item-width: %1$dpx; --wcboost-swatches-item-height: %2$dpx;', absint( $args['swatches_size']['width'] ), absint( $args['swatches_size']['height'] ) ) : '';
		$html  = '';

		if ( is_object( $term ) ) {
			$selected = sanitize_title( $args['selected'] ) == $value;
		} else {
			// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
			$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? $args['selected'] == sanitize_title( $value ) : $args['selected'] == $value;
		}

		$data = $this->get_attribute_swatches_data( $term, $args );

		$class = [
			'wcboost-variation-swatches__item',
			'wcboost-variation-swatches__item-' . $value,
		];

		if ( $selected ) {
			$class[] = 'selected';
		}

		if ( ! empty( $args['swatches_class'] ) ) {
			$class[] = $args['swatches_class'];
		}

		switch ( $type ) {
			case 'color':
				$color = '--wcboost-swatches-item-color:' . $data['value'];
				$html  = sprintf(
					'<li class="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button" aria-pressed="false">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( implode( ' ', $class ) ),
					esc_attr( $size . $color ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_html( $name )
				);
				break;

			case 'image':
				$html = sprintf(
					'<li class="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button" aria-pressed="false">
						<img src="%s" alt="%s">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( implode( ' ', $class ) ),
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_url( $data['image_src'] ),
					esc_attr( ! empty( $data['image_alt'] ) ? $data['image_alt'] : $name ),
					esc_html( $name )
				);
				break;

			case 'label':
				$html = sprintf(
					'<li class="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button" aria-pressed="false">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( implode( ' ', $class ) ),
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_html( $data['value'] ? $data['value'] : $name )
				);
				break;

			case 'button':
				$html = sprintf(
					'<li class="%s" style="%s" aria-label="%s" data-value="%s" tabindex="0" role="button" aria-pressed="false">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( implode( ' ', $class ) ),
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_html( $name )
				);
				break;
		}

		return apply_filters( 'wcboost_variation_swatches_' . $type . '_html', $html, $args, $data, $term );
	}

	/**
	 * Get attribute swatches args
	 *
	 * @param int $product_id   Product ID
	 * @param string $attribute Attribute name
	 *
	 * @return array
	 */
	public function get_swatches_args( $product_id, $attribute ) {
		$swatches_meta = Helper::get_swatches_meta( $product_id );
		$attribute_key = sanitize_title( $attribute );

		if ( ! empty( $swatches_meta[ $attribute_key ] ) ) {
			$swatches_args = [
				'swatches_type'       => $swatches_meta[ $attribute_key ]['type'],
				'swatches_shape'      => $swatches_meta[ $attribute_key ]['shape'],
				'swatches_size'       => 'custom' == $swatches_meta[ $attribute_key ]['size'] ? $swatches_meta[ $attribute_key ]['custom_size'] : '',
				'swatches_attributes' => $swatches_meta[ $attribute_key ]['swatches'],
			];

			if ( Helper::is_default( $swatches_args['swatches_type'] ) ) {
				$swatches_args['swatches_type'] = taxonomy_exists( $attribute ) ? Helper::get_attribute_taxonomy( $attribute )->attribute_type : 'select';
				$swatches_args['swatches_attributes'] = [];

				// Auto convert dropdowns to buttons.
				if ( 'select' == $swatches_args['swatches_type'] && wc_string_to_bool( Helper::get_settings( 'auto_button' ) ) ) {
					$swatches_args['swatches_type'] = 'button';
				}
			} else {
				$swatches_args['swatches_edited'] = true;
			}

			if ( Helper::is_default( $swatches_args['swatches_shape'] ) ) {
				$swatches_args['swatches_shape'] = Helper::get_settings( 'shape' );
			}
		} else {
			$swatches_args = [
				'swatches_type'       => taxonomy_exists( $attribute ) ? Helper::get_attribute_taxonomy( $attribute )->attribute_type : 'select',
				'swatches_shape'      => Helper::get_settings( 'shape' ),
				'swatches_size'       => '',
				'swatches_attributes' => [],
			];

			// Auto convert dropdowns to buttons.
			if ( 'select' == $swatches_args['swatches_type'] && wc_string_to_bool( Helper::get_settings( 'auto_button' ) ) ) {
				$swatches_args['swatches_type'] = 'button';
			}
		}

		$swatches_args['swatches_tooltip']    = wc_string_to_bool( Helper::get_settings( 'tooltip' ) );
		$swatches_args['swatches_image_size'] = $swatches_args['swatches_size'] ? $swatches_args['swatches_size'] : Helper::get_settings( 'size' );

		return apply_filters( 'wcboost_variation_swatches_item_args', $swatches_args, $attribute, $product_id, );
	}

	/**
	 * Get attribute swatches data
	 *
	 * @param object|string  $term Term object or name (with custom attributes).
	 * @param array  $args Swatches args.
	 *
	 * @return array {
	 *     @type string $type The swatches type.
	 *     @type string $value The swatches value.
	 *     @type string $image_src The swatches image src.
	 * }
	 */
	public function get_attribute_swatches_data( $term, $args ) {
		$type = isset( $args['swatches_type'] ) ? $args['swatches_type'] : 'select';
		$data = [
			'type'  => $type,
			'value' => '',
		];

		if ( ! Helper::is_swatches_type( $type ) ) {
			return $data;
		}

		$key = is_object( $term ) ? $term->term_id : sanitize_title( $term );

		if ( isset( $args['swatches_attributes'][ $key ] ) && isset( $args['swatches_attributes'][ $key ][ $type ] ) ) {
			$value = $args['swatches_attributes'][ $key ][ $type ];
		} else {
			$value = is_object( $term ) ? Term_Meta::instance()->get_meta( $term->term_id, $type ) : '';
		}

		$data['value'] = $value;

		if ( 'image' == $type ) {
			if ( ! $value ) {
				$image_src = wc_placeholder_img_src( 'thumbnail' );
			} else {
				$dimension = ! empty( $args['swatches_image_size'] ) ? array_values( $args['swatches_image_size'] ) : 'thumbnail';
				$image     = Helper::get_image( $value, $dimension, false );
				$image_src = $image ? $image[0] : wc_placeholder_img_src( 'thumbnail' );
			}

			$data['image_src'] = $image_src;

			if ( ! empty( $args['product'] ) ) {
				$product = $args['product'];
				$name    = is_object( $term ) ? $term->name : $term;

				$data['image_alt'] = $product->get_title() . ' - ' . $name;
			}
		}

		return apply_filters( 'wcboost_variation_swatches_attribute_swatches_data', $data, $term, $args );
	}
}
