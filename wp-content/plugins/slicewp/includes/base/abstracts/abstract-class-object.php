<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all object
 *
 */
abstract class SliceWP_Base_Object {


	/**
	 * Constructor.
	 *
	 */
	public function __construct( $object ) {
		
		foreach ( get_object_vars( $object ) as $key => $value ) {

			if ( ! property_exists( $this, $key ) ) {
				continue;
			}

			$this->$key = $value;

		}

	}


	/**
	 * Getter.
	 *
	 * @param string $property
	 *
	 */
	public function get( $property ) {

		if ( method_exists( $this, 'get_' . $property ) ) {

			return $this->{'get_' . $property}();

		} else {

			return ( property_exists( $this, $property ) ? $this->$property : null );

		}

	}


	/**
	 * Setter.
	 *
	 * @param string $property
	 * @param string $value
	 *
	 */
	public function set( $property, $value ) {

		if ( method_exists( $this, 'set_' . $property ) ) {

			$this->{'set_' . $property}( $value );

		} else {

			$this->$property = $value;

		}

	}


	/**
	 * Returns the object attributes and their values as an array.
	 *
	 */
	public function to_array() {

		return get_object_vars( $this );

	}

}