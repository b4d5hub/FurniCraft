<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_73
 *
 * Migration that runs when updating to version 1.0.72
 *
 */
class SliceWP_Migration_1_0_73 extends SliceWP_Abstract_Migration {

	
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		
		$this->id          = 'slicewp-update-1-0-73';
		$this->notice_type = 'none';

		parent::__construct();

	}


	/**
	 * Actually run the migration.
	 *
	 */
	public function migrate() {

		// Signal that we have to flush rewrite rules
		update_option( 'slicewp_flush_rewrite_rules', '1' );
		
		return true;

	}

}