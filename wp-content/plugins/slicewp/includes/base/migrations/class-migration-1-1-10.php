<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_1_10
 *
 * Migration that runs when updating to version 1.1.10
 *
 */
class SliceWP_Migration_1_1_10 extends SliceWP_Abstract_Migration {


	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		
		$this->id          = 'slicewp-update-1-1-10';
		$this->notice_type = 'none';

		parent::__construct();

	}


	/**
	 * Actually run the migration.
	 *
	 */
	public function migrate() {

        // Get settings.
        $settings = slicewp_get_option( 'settings', array() );

        if ( empty( $settings ) || empty( $settings['email_notifications'] ) || ! is_array( $settings['email_notifications'] ) || isset( $settings['email_notifications']['affiliate_payment_paid'] ) ) {
            return true;
        }

        // Set the default settings for the affiliate payment paid email notification.
        $settings['email_notifications']['affiliate_payment_paid'] = array(
            'enabled' => '',
            'subject' => __( "Affiliate Payment Paid", 'slicewp' ),
            'content' => __( 'Hey {{affiliate_first_name}},', 'slicewp' ) . "\n\n" . __( "We've processed your affiliate earnings and sent out your payment.", 'slicewp' ) . "\n\n" . __( 'Payment amount: {{payment_amount}}' ) . "\n\n" . __( 'Payment method: {{payment_payout_method}}' ) . "\n\n" . __( 'Payment ID: {{payment_id}}' )
        );

        slicewp_update_option( 'settings', $settings );

        return true;

	}

}