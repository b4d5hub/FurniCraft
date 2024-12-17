<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Dashboard extends SliceWP_Submenu_Page {

	/**
	 * Callback for the HTML output for the Dashboard page
	 *
	 */
	public function output() {

		include 'views/view-dashboard.php';

	}

}