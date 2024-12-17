<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the removal of a commission from the payment.
 *
 */
function slicewp_admin_action_remove_commission() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_remove_commission' ) ) {
		return;
	}

  	// Verify for Payment ID.
	if ( empty( $_GET['payment_id'] ) ) {
		return;
	}
    
	// Verify for Commission ID.
	if ( empty( $_GET['commission_id'] ) ) {
		return;
	}

    $payment_id = absint( $_GET['payment_id'] );
    $remove_id  = absint( $_GET['commission_id'] );

    // Get the payment.
    $payment = slicewp_get_payment( $payment_id );

    if ( is_null( $payment ) ) {
		return;
	}

	// Get the commission.
	$commission = slicewp_get_commission( $remove_id );

	if ( is_null( $commission ) ) {
		return;
	}

	// Remove the payment's ID from the commission
	$commission_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'payment_id' => 0
	);

	$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );

	// Return if the commission could not be updated.
	if ( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_update_false', '<p>' . __( 'Something went wrong. Could not remove the commission from the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_update_false' );

		return;

	}

	// Calculate the payment's new amount.
    $payment_amount  = $payment->get( 'amount' ) - $commission->get( 'amount' );

    // Prepare payment data to be updated.
    $payment_data = array(
        'date_modified'  => slicewp_mysql_gmdate(),
        'amount'         => slicewp_sanitize_amount( $payment_amount )
    );


	/**
	 * Backwards compatibility for the payment's "commission_ids" attribute.
	 * 
	 * The "commission_ids" attribute is considered deprecated and should not be used. However, for now,
	 * we continue maintaining it for compatbility reasons.
	 * 
	 */
	$commission_ids = array_map( 'trim', explode( ',', $payment->get( 'commission_ids' ) ) );

	// Check if commission is part of the payment's "commission_ids" attribute.
	$found_key = array_search( $remove_id, $commission_ids );

	// If it is, remove it.
	if ( false !== $found_key ) {

		unset( $commission_ids[$found_key] );

		$payment_data['commission_ids'] = implode( ',', array_values( $commission_ids ) );

	}


    // Update the payment.
	$updated = slicewp_update_payment( $payment_id, $payment_data );

	if ( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_update_false', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_update_false' );

		return;

	}

	// Redirect to the current page.
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'review-payment', 'payment_id' => $payment_id, 'slicewp_message' => 'commission_remove_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_remove_commission', 'slicewp_admin_action_remove_commission', 50 );


/**
 * Validates and handles the updating of a payment in the database.
 *
 */
function slicewp_admin_action_review_payment() {
    
	// Verify for nonce.
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_review_payment' ) ) {
		return;
	}

    // Verify for Payment ID
	if ( empty( $_POST['payment_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'payment_id_missing', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_id_missing' );

		return;

    }
    
	// Verify for payment's existance
	$payment_id = absint( $_POST['payment_id'] );
	$payment 	= slicewp_get_payment( $payment_id );

	if ( is_null( $payment ) ) {

		slicewp_admin_notices()->register_notice( 'payment_not_exists', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_not_exists' );

		return;

	}

    // Verify for payment status
	if ( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'payment_status_missing', '<p>' . __( 'Please select the status of the payment.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_status_missing' );

		return;

    }
    
    $statuses = slicewp_get_payment_available_statuses();
    
	// Verify if the payment status is valid
	if ( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'payment_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_status_invalid' );

		return;

    }

	// Verify for payment status
	if ( empty( $_POST['payout_method'] ) ) {

		slicewp_admin_notices()->register_notice( 'payout_method_missing', '<p>' . __( 'Please select a payout method for the payment.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_method_missing' );

		return;

    }

	$payout_methods = slicewp_get_payout_methods();

	if ( ! in_array( $_POST['payout_method'], array_keys( $payout_methods ) ) ) {

		slicewp_admin_notices()->register_notice( 'payout_method_invalid', '<p>' . __( 'The selected payout method in not valid.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_method_invalid' );

		return;

	}
    
    $_POST = stripslashes_deep( $_POST );

    // Prepare payment data to be updated.
	$payment_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'payout_method' => sanitize_text_field( $_POST['payout_method'] ),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Update payment into the database.
	$updated = slicewp_update_payment( $payment_id, $payment_data );

	// If the payment could not be updated show a message to the user.
	if ( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_update_false', '<p>' . __( 'Something went wrong. Could not update the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_update_false' );

		return;

	}

	// Update commissions status only if the payment's new status is different.
	if ( $payment->get( 'status' ) != $_POST['status'] ) {

		// Get the commissions from the updated payment.
		$commission_ids = slicewp_get_commissions( array( 'number' => -1, 'fields' => 'id', 'payment_id' => $payment->get( 'id' ) ) );

		// Prepare the data for the commission update.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> ( $_POST['status'] == 'paid' ? 'paid'  : 'unpaid' ),
		);
		
		// Change the status of the commissions.
		foreach ( $commission_ids as $commission_id ) {

			$updated = slicewp_update_commission( $commission_id, $commission_data );

			if ( ! $updated ) {

				slicewp_admin_notices()->register_notice( 'commission_update_false', '<p>' . __( 'Something went wrong. Could not update the commission. Please try again.', 'slicewp' ) . '</p>', 'error' );
				slicewp_admin_notices()->display_notice( 'commission_update_false' );
		
				return;
		
			}
		
		}

	}

	// Redirect to the review page of the payment with a success message.
	wp_redirect( add_query_arg( array_merge( $_GET, array( 'slicewp_message' => 'payment_update_success', 'updated' => '1' ) ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_review_payment', 'slicewp_admin_action_review_payment', 50 );


/**
 * Deletes the payment.
 *
 */
function slicewp_admin_action_delete_payment() {

	// Verify for nonce
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_payment' ) ) {
		return;
	}

	// Verify for payment ID
	if ( empty( $_GET['payment_id'] ) ) {
		return;
	}
	
	// Verify for payment's existance
	$payment_id = absint( $_GET['payment_id'] );
	$payment 	= slicewp_get_payment( $payment_id );

	if ( is_null( $payment ) ) {
		return;
	}

	// Delete the payment
	$deleted = slicewp_delete_payment( $payment_id );

	if ( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'payment_delete_false', '<p>' . __( 'Something went wrong. Could not delete the payment. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_delete_false' );

		return;

	}

	// Delete the payment's metadata
	$payment_meta = slicewp_get_payment_meta( $payment->get('id') );

	if ( ! empty( $payment_meta ) ) {

		foreach( $payment_meta as $key => $value ) {

			slicewp_delete_payment_meta( $payment->get('id'), $key );

		}

	}

	// Remove the payment's ID from each commission that has it attached.
	$commission_ids = slicewp_get_commissions( array( 'fields' => 'id', 'number' => -1, 'payment_id' => absint( $payment->get( 'id' ) ) ) );

	if ( ! empty( $commission_ids ) ) {

		foreach ( $commission_ids as $commission_id ) {
			
			slicewp_update_commission( $commission_id, array( 'payment_id' => 0 ) );

		}

	}

	// Substract the deleted Payment amount from the Payout.
	if ( ! empty( $payment->get( 'payout_id' ) ) ) {

		$payout_id = $payment->get( 'payout_id' );
		$payout    = slicewp_get_payout( $payout_id );

		if ( empty( $payout ) ) {

			slicewp_admin_notices()->register_notice( 'payout_update_false', '<p>' . __( 'Something went wrong. Could not update the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payout_update_false' );

			return;

		}

		$payout_args = array(
			'amount' => slicewp_sanitize_amount( $payout->get( 'amount' ) - $payment->get( 'amount' ) )
		);

		$updated = slicewp_update_payout( $payout_id, $payout_args );

		if ( ! $updated ) {

			slicewp_admin_notices()->register_notice( 'payout_update_false', '<p>' . __( 'Something went wrong. Could not update the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payout_update_false' );

			return;

		}
		
	}

	// Redirect to the current page
	wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token', 'payment_id' ), add_query_arg( array( 'slicewp_message' => 'payment_delete_success' ) ) ) );
	exit;
	
}
add_action( 'slicewp_admin_action_delete_payment', 'slicewp_admin_action_delete_payment', 50 );


/**
 * Marks a payment as paid.
 * 
 */
function slicewp_admin_action_mark_payment_as_paid() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_mark_payment_as_paid' ) ) {
		return;
	}

	// Verify for payment ID.
	if ( empty( $_GET['payment_id'] ) ) {
		return;
	}
	
	// Verify for payment's existance.
	$payment_id = absint( $_GET['payment_id'] );
	$payment 	= slicewp_get_payment( $payment_id );

	if ( is_null( $payment ) ) {
		return;
	}

	// Check that the payment isn't already paid.
	if ( 'paid' == $payment->get( 'status' ) ) {
		return;
	}

	// Prepare payment data.
	$payment_data = array(
		'status' 		=> 'paid',
		'date_modified' => slicewp_mysql_gmdate()
	);

	// Update payment to paid.
	$updated = slicewp_update_payment( $payment->get( 'id' ), $payment_data );

	if ( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'payment_mark_as_paid_false', '<p>' . __( 'Something went wrong. Could not mark the payment as paid. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payment_mark_as_paid_false' );

		return;

	}

	// Update all commissions to paid.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'payment_id' => $payment->get( 'id' ) ) );

	foreach ( $commissions as $commission ) {

		if ( 'paid' == $commission->get( 'status' ) ) {
			continue;
		}

		$commission_data = array(
			'status' 		=> 'paid',
			'date_modified' => slicewp_mysql_gmdate()
		);

		slicewp_update_commission( $commission->get( 'id' ), $commission_data );

	}

	// Redirect to the payout/payments page.
	wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token' ), add_query_arg( array( 'slicewp_message' => 'payment_mark_as_paid_success', 'updated' => '1' ) ) ) );
	exit;

}
add_action( 'slicewp_admin_action_mark_payment_as_paid', 'slicewp_admin_action_mark_payment_as_paid', 50 );