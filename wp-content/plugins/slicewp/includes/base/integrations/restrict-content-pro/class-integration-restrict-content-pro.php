<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The class that defines the Restrict Content Pro integration
 *
 */
Class SliceWP_Integration_Restrict_Content_Pro extends SliceWP_Integration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		/**
		 * Set the name of the integration
		 *
		 */
		$this->name = 'Restrict Content Pro';

		/**
		 * Set the supports values
		 *
		 */
		$supports = array(
			'commission_types' => array( 'subscription' )
		);

		/**
		 * Filter the supports array
		 *
		 * @param array $supports
		 *
		 */
		$this->supports = apply_filters( 'slicewp_integration_supports_rcp', $supports );

	}

}