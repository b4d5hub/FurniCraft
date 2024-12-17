<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles registration and loading of batch processors
 *
 */
class SliceWP_Batch_Processor_Manager {

	/**
	 * Loads a batch processor.
	 *
	 * @return null|object
	 *
	 */
	public function load_processor( $processor_slug, $args = array() ) {

		/**
		 * Hook to register batch processors class handlers
		 * The array element should be 'class_slug' => 'class_name'
		 *
		 * @param array
		 *
		 */
		$processors = apply_filters( 'slicewp_register_batch_processor', array() );

		if ( empty( $processors[$processor_slug] ) )
			return null;

		return new $processors[$processor_slug]( $args );

	}

}