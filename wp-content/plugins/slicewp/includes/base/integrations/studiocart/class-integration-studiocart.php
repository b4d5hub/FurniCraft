<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the Studiocart integration
 *
 */
Class SliceWP_Integration_Studiocart extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'Studiocart';

		/**
		 * Set the supports values
		 *
		 */
		$supports = array(
			'commission_types' => array( 'sale', 'subscription' )
		);

		/**
		 * Filter the supports array
		 *
		 * @param array $supports
		 *
		 */
		$this->supports = apply_filters( 'slicewp_integration_supports_stc', $supports );

	}

}