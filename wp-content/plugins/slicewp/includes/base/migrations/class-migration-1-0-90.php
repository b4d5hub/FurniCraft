<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_90
 *
 * Migration that runs when updating to version 1.0.90
 *
 */
class SliceWP_Migration_1_0_90 extends SliceWP_Abstract_Migration {


	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		
		$this->id          = 'slicewp-update-1-0-90';
		$this->notice_type = 'none';

		parent::__construct();

	}


	/**
	 * Actually run the migration.
	 *
	 */
	public function migrate() {

        $setup_wizard_visited = get_option( 'slicewp_setup_wizard_visited' );

		if ( ! empty( $setup_wizard_visited ) ) {

            update_option( 'slicewp_setup_wizard_hidden', 1 );
            
        }

        return true;

	}

}