<?php
/**
 * Button 2 grid builder shortcode element.
 *
 * @var WPBakeryShortCode_Vc_Button2 $this
 * @var array $atts
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$wrapper_css_class = 'vc_button-2-wrapper';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_btn';
// parse link.

$class .= ( '' !== $color ) ? ( ' vc_btn_' . $color . ' vc_btn-' . $color ) : '';
$class .= ( '' !== $size ) ? ( ' vc_btn_' . $size . ' vc_btn-' . $size ) : '';
$class .= ( '' !== $style ) ? ' vc_btn_' . $style : '';

$css = isset( $css ) ? $css : '';
$class_to_filter = $class;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$link = 'class="' . esc_attr( $css_class ) . '"';
$target = '';
$rel = '';
if ( isset( $atts['link'] ) ) {
	$css_class .= ' vc_gitem-link';
	if ( 'custom' === $atts['link'] && ! empty( $atts['url'] ) ) {
		$vc_link = vc_build_link( $atts['url'] );
		if ( strlen( $vc_link['target'] ) ) {
			$target = ' target="' . esc_attr( $vc_link['target'] ) . '"';
		}
		if ( strlen( $vc_link['rel'] ) ) {
			$rel = ' rel="' . esc_attr( $vc_link['rel'] ) . '"';
		}
		$link = 'href="' . esc_url( $vc_link['url'] ) . '" class="' . esc_attr( $css_class ) . '"';
	} elseif ( 'post_link' === $atts['link'] ) {
		$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
		$link = 'href="{{ post_link_url }}" class="' . esc_attr( $css_class ) . '"' . $target;
	} elseif ( 'image' === $atts['link'] ) {
		$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
		$link = '{{ post_image_url_href }} class="' . esc_attr( $css_class ) . '"' . $target;
	} elseif ( 'image_lightbox' === $atts['link'] ) {
		$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
		$link = '{{ post_image_url_attr_prettyphoto:' . esc_attr( $css_class ) . ' }}' . $target;
	} elseif ( 'image_full' === $atts['link'] ) {
		$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
		$link = '{{ post_full_image_url_href }} class="' . esc_attr( $css_class ) . '"' . $target;
	} elseif ( 'image_full_lightbox' === $atts['link'] ) {
		$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
		$link = '{{ post_full_image_url_attr_prettyphoto:' . esc_attr( $css_class ) . ' }}' . $target;
	}
}

$link = apply_filters( 'vc_gitem_post_data_get_link_link', 'a ' . $link, $atts, $css_class ) . apply_filters( 'vc_gitem_post_data_get_link_target', $target, $atts ) . apply_filters( 'vc_gitem_post_data_get_link_rel', $rel, $atts );

if ( $align ) {
	$wrapper_css_class .= ' vc_button-2-align-' . $align;
}

$output = '<div class="' . esc_attr( $wrapper_css_class ) . '">';
$output .= '<' . $link . $target . $rel . '>' . $title . '</a>';
$output .= '</div>';

return $output;
