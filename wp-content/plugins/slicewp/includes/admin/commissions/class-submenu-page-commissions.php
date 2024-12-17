<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Commissions extends SliceWP_Submenu_Page {

	/**
	 * Helper init method that runs on parent __construct
	 *
	 */
	protected function init() {

		add_action( 'admin_init', array( $this, 'register_admin_notices' ), 10 );

	}


	/**
	 * Callback method to register admin notices that are sent via URL parameters
	 *
	 */
	public function register_admin_notices() {

		if ( empty( $_GET['slicewp_message'] ) ) {
			return;
		}

		// Commission insert success.
		slicewp_admin_notices()->register_notice( 'commission_insert_success', '<p>' . __( 'Commission added successfully.', 'slicewp' ) . '</p>' );

		// Commission updated successfully.
		slicewp_admin_notices()->register_notice( 'commission_update_success', '<p>' . __( 'Commission updated successfully.', 'slicewp' ) . '</p>' );

		// Commission updated fail.
		slicewp_admin_notices()->register_notice( 'commission_update_fail', '<p>' . __( 'Something went wrong. Could not update the commission.', 'slicewp' ) . '</p>', 'error' );

		// Commission delete success.
		slicewp_admin_notices()->register_notice( 'commission_delete_success', '<p>' . __( 'Commission deleted successfully.', 'slicewp' ) . '</p>' );

		// Commission mark as paid success.
		slicewp_admin_notices()->register_notice( 'commission_mark_as_paid_success', '<p>' . __( 'Commission successfully marked as paid.', 'slicewp' ) . '</p>' );

		// Commission approved success.
		slicewp_admin_notices()->register_notice( 'commission_approved_success', '<p>' . __( 'Commission successfully approved.', 'slicewp' ) . '</p>' );

		// Commission rejected success.
		slicewp_admin_notices()->register_notice( 'commission_rejected_success', '<p>' . __( 'Commission successfully rejected.', 'slicewp' ) . '</p>' );

		// Commission bulk delete success.
		if ( ! empty( $_GET['updated'] ) ) {

			slicewp_admin_notices()->register_notice( 'bulk_action_commissions_delete_success', '<p>' . sprintf( __( '%d commission(s) deleted successfully.', 'slicewp' ), absint( $_GET['updated'] ) ) . '</p>' );

		}

	}


	/**
	 * Callback for the HTML output for the Commission page
	 *
	 */
	public function output() {

		if ( empty( $this->current_subpage ) ) {

			include 'views/view-commissions.php';

		} else {

			if ( $this->current_subpage == 'add-commission' ) {
				include 'views/view-add-commission.php';
			}

			if ( $this->current_subpage == 'edit-commission' ) {
				include 'views/view-edit-commission.php';
			}

		}

	}

}