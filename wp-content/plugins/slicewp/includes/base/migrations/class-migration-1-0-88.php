<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_88
 *
 * Migration that runs when updating to version 1.0.88
 *
 */
class SliceWP_Migration_1_0_88 extends SliceWP_Abstract_Migration {


	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		
		$this->id          = 'slicewp-update-1-0-88';
		$this->notice_type = 'none';

		parent::__construct();

	}


	/**
	 * Actually run the migration.
	 *
	 */
	public function migrate() {

		$payouts = slicewp_get_payouts( array( 'number' => -1 ) );

        foreach ( $payouts as $payout ) {

            if ( ! empty( $payout->get( 'originator_user_id' ) ) ) {
                continue;
            }

            $payout_data = array(
                'date_modified' 	 => slicewp_mysql_gmdate(),
		        'originator_user_id' => absint( $payout->get( 'admin_id' ) ),
            );

            slicewp_update_payout( $payout->get( 'id' ), $payout_data );

        }

        return true;

	}

}