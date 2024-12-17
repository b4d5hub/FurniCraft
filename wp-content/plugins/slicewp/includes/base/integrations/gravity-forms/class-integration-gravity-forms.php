<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the Gravity Forms integration
 *
 */
Class SliceWP_Integration_Gravity_Forms extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'Gravity Forms';

		/**
		 * Set the supports values
		 *
		 */
		$supports = array();

		/**
		 * Filter the supports array
		 *
		 * @param array $supports
		 *
		 */
		$this->supports = apply_filters( 'slicewp_integration_supports_gfo', $supports );

	}

}