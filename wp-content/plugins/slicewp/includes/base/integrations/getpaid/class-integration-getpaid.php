<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the GetPaid integration
 *
 */
Class SliceWP_Integration_GetPaid extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'GetPaid';

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
		$this->supports = apply_filters( 'slicewp_integration_supports_gpd', $supports );

	}

}