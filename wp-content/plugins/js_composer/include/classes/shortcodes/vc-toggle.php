<?php
/**
 * Class that handles specific [vc_toggle] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_toggle.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Toggle
 */
class WPBakeryShortCode_Vc_Toggle extends WPBakeryShortCode {
	/**
	 * Override default title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function outputTitle( $title ) {
		return '';
	}

	/**
	 * Get element heading.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getHeading( $atts ) {
		if ( isset( $atts['use_custom_heading'] ) && 'true' === $atts['use_custom_heading'] ) {
			$custom_heading = wpbakery()->getShortCode( 'vc_custom_heading' );

			$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, 'custom_' );
			$data['text'] = $atts['title'];

			return $custom_heading->render( array_filter( $data ) );
		} else {
			return '<h4>' . esc_html( $atts['title'] ) . '</h4>';
		}
	}
}
