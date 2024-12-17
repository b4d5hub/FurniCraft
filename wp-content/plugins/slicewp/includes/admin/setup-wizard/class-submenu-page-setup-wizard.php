<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SliceWP_Submenu_Page_Setup_Wizard extends SliceWP_Submenu_Page {

	/**
	 * All setup wizard steps.
	 *
	 * @access private
	 * @var    array
	 *
	 */
	private $steps;

	/**
	 * The current step the user is viewing.
	 *
	 * @access private
	 * @var    string
	 *
	 */
	private $current_step;

	/**
	 * The numerical index for the current step.
	 *
	 * @access private
	 * @var    int
	 *
	 */
	private $current_step_index;


	/**
	 * Helper init method that runs on parent __construct
	 *
	 */
	protected function init() {

		$this->steps 			  = $this->get_steps();
		$this->current_step 	  = ( ! empty( $_GET['current_step'] ) ? sanitize_text_field( $_GET['current_step'] ) : ( ! empty( slicewp_get_option( 'setup_wizard_current_step', false ) ) ? slicewp_get_option( 'setup_wizard_current_step', false ) : key( $this->steps ) ) );
		$this->current_step_index = array_search( $this->current_step, array_keys( $this->steps ) );

	}


	/**
	 * Returns an array with all setup wizard steps.
	 *
	 * @return array
	 *
	 */
	protected function get_steps() {

		$steps = array(
			'integrations' => __( 'Integrations', 'slicewp' ),
			'setup' 	   => __( 'Program Basics', 'slicewp' ),
			'pages' 	   => __( 'Affiliate Pages', 'slicewp' ),
			'emails' 	   => __( 'Emails', 'slicewp' ),
			'finished' 	   => __( 'Ready!', 'slicewp' )
		);

		return $steps;

	}


	/**
	 * Callback for the HTML output for the page.
	 *
	 */
	public function output() {

		include_once 'views/view-setup-wizard.php';

	}

}