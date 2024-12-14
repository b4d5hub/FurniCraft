<?php
/**
 * The template for displaying [vc_wp_pages] shortcode output of 'WP Pages' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_btn.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var $atts
 * @var $title
 * @var $sortby
 * @var $exclude
 * @var $el_class
 * @var $el_id
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Wp_Pages $this
 */
$title = $sortby = $exclude = $el_class = $el_id = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class = $this->getExtraClass( $el_class );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div ' . implode( ' ', $wrapper_attributes ) . ' class="vc_wp_pages wpb_content_element' . esc_attr( $el_class ) . '">';
$type = 'WP_Widget_Pages';
$args = array();
global $wp_widget_factory;
// to avoid unwanted warnings let's check before using widget.
if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
	ob_start();
	the_widget( $type, $atts, $args );
	$output .= ob_get_clean();

	$output .= '</div>';

	return $output;
}
