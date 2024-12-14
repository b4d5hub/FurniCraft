<?php
/**
 * WPML compatibility functions
 */

class Sober_WPML {
	/**
	 * The single instance of the class
	 *
	 * @var Sober_WPML
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wpml_pb_shortcode_encode', array( $this, 'shortcode_encode_urlencoded_json' ), 10, 3 );
		add_filter( 'wpml_pb_shortcode_decode', array( $this, 'shortcode_decode_urlencoded_json' ), 10, 3 );
	}

	/**
	 * Encode the param_groups type of js_composer
	 *
	 * @param string $string
	 * @param string $encoding
	 * @param array $original_string
	 * @return string
	 */
	public function shortcode_encode_urlencoded_json( $string, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$output = array();

			foreach ( $original_string as $combined_key => $value ) {
				$parts = explode( '_', $combined_key );
				$i     = array_pop( $parts );
				$key   = implode( '_', $parts );

				$output[ $i ][ $key ] = $value;
			}

			$string = urlencode( json_encode( $output ) );
		}

		return $string;
	}

	/**
	 * Decode urleconded string of param_groups type of js_composer
	 *
	 * @param string $string
	 * @param string $encoding
	 * @param string $original_string
	 * @return string
	 */
	public function shortcode_decode_urlencoded_json( $string, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$rows   = json_decode( urldecode( $original_string ), true );
			$string = array();
			$atts   = array( 'label', 'value', 'image', 'title', 'button_text', 'url' );

			foreach ( (array) $rows as $i => $row ) {
				foreach ( $row as $key => $value ) {
					if ( in_array( $key, $atts ) ) {
						$string[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => true );
					} else {
						$string[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => false );
					}
				}
			}
		}

		return $string;
	}

}

Sober_WPML::instance();
