<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Preview the Payout.
 *
 */
function slicewp_admin_action_send_to_preview_payout() {

	if ( $_POST['date_range'] == 'custom_range' ) {

		// Verify for Date Fields.
		if ( empty( $_POST['date_min'] ) || empty( $_POST['date_max'] ) ) {

			slicewp_admin_notices()->register_notice( 'payouts_date_empty', '<p>' . __( 'Please fill in the Start Date and End Date fields.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payouts_date_empty' );
	
			return;
	
		}

		// Verify for End Date to be greater than Start Date.
		if( $_POST['date_min'] > $_POST['date_max'] ) {

			slicewp_admin_notices()->register_notice( 'payouts_date_inversed', '<p>' . __( 'Please fill in an End Date greater than Start Date.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payouts_date_inversed' );

			return;

		}

	}

	// Verify for Payment Minimum Amount.
	if ( ! isset( $_POST['payments_minimum_amount'] ) || ! is_numeric( slicewp_sanitize_amount( $_POST['payments_minimum_amount'] ) ) || $_POST['payments_minimum_amount'] < 0 ) {

		slicewp_admin_notices()->register_notice( 'payments_minimum_amount_error', '<p>' . __( 'Please fill in a Payments Minimum Amount equal or greater than 0.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payments_minimum_amount_error' );

		return;

	}

	$payments_preview = slicewp_generate_payout_payments_preview( $_POST );

	// Check that we have payments
	if ( empty( $payments_preview ) ) {

		slicewp_admin_notices()->register_notice( 'no_payment_generated', '<p>' . __( 'No affiliate payments could be generated for the selected payout details.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_payment_generated' );
		
		return;

	}

	$query_args = _slicewp_array_wp_kses_post( array_intersect_key( $_POST, array_flip( array( 'page', 'subpage', 'date_range', 'date_up_to', 'date_min', 'date_max', 'include_grace_period', 'payments_minimum_amount', 'included_affiliates', 'selected_affiliates' ) ) ) );

	wp_redirect( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_send_to_preview_payout', 'slicewp_admin_action_send_to_preview_payout', 50 );


/**
 * Create the Payout.
 *
 */
function slicewp_admin_action_create_payout() {

	// Verify for nonce.
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_create_payout' ) ) {
		return;
	}

	$payments_preview = slicewp_generate_payout_payments_preview( $_GET );

	// Bail if no potential payments were generated.
	if ( empty( $payments_preview ) ) {

		slicewp_admin_notices()->register_notice( 'no_commissions_found', '<p>' . __( 'No commissions found.', 'slicewp' ) . '</p>', 'notice-warning' );
		slicewp_admin_notices()->display_notice( 'no_commissions_found' );
		
		return;

	}

	// Prepare the payout data and insert the payout.
	$payout_data = array(
		'date_created'  	 => slicewp_mysql_gmdate(),
		'date_modified' 	 => slicewp_mysql_gmdate(),
		'originator_user_id' => get_current_user_id(),
		'amount'			 => slicewp_sanitize_amount( array_sum( array_column( $payments_preview, 'amount' ) ) )
	);

	$payout_id = slicewp_insert_payout( $payout_data );

	// If the payout could not be inserted show a message to the user.
	if ( ! $payout_id ) {

		slicewp_admin_notices()->register_notice( 'payout_insert_false', '<p>' . __( 'Something went wrong. Could not add the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_insert_false' );

		return;

	}

	// Sort payments by amount.
	array_multisort( array_column( $payments_preview, 'amount' ), SORT_ASC, $payments_preview );

	// Handle each payment.
	foreach ( $payments_preview as $_payment_data ) {

		// Prepare the payment data.
		$payment_data = array(
			'affiliate_id'	 	 => absint( $_payment_data['affiliate_id'] ),
			'amount'		 	 => slicewp_sanitize_amount( $_payment_data['amount'] ),
			'currency'		 	 => sanitize_text_field( $_payment_data['currency'] ),
			'payout_method'	 	 => slicewp_get_affiliate_payout_method( absint( $_payment_data['affiliate_id'] ) ),
			'date_created'   	 => slicewp_mysql_gmdate(),
			'date_modified'  	 => slicewp_mysql_gmdate(),
			'status'		 	 => 'unpaid',
			'commission_ids' 	 => sanitize_text_field( implode( ',', $_payment_data['commission_ids'] ) ),
			'payout_id'		 	 => $payout_id,
			'originator_user_id' => get_current_user_id()
		);
		
		$payment_id = slicewp_insert_payment( $payment_data );

		// Add the payment ID to each commission.
		if ( $payment_id ) {

			foreach ( $_payment_data['commission_ids'] as $commission_id ) {

				slicewp_update_commission( $commission_id, array( 'date_modified' => slicewp_mysql_gmdate(), 'payment_id' => $payment_id ) );
	
			}

		}

	}

	// Redirect to the generated payout page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'view-payout', 'payout_id' => $payout_id, 'slicewp_message' => 'payout_insert_success', 'payments_count' => count( $payments_preview ) ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_create_payout', 'slicewp_admin_action_create_payout', 50 );


/**
 * Generates the Payment CSV.
 *
 */
function slicewp_admin_action_generate_payouts_csv() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_generate_payouts_csv' ) ) {
		return;
	}

	// Verify for Payout ID.
	if ( empty( $_GET['payout_id'] ) ) {
		return;
	}

	// Verify for Payout existence.
	$payout = slicewp_get_payout( absint( $_GET['payout_id'] ) );
	
	if ( is_null( $payout ) ) {
		return;
	}

	// Get the payments contained in the payout.
	$payments_args = array(
		'number'	=> -1,
		'payout_id'	=> $payout->get( 'id' ),
	);

	$payments = slicewp_get_payments( $payments_args );
	
	if ( empty( $payments ) ) {
		return;
	}

	/**
	 * Prepare and filter the mass pay CSV file columns.
	 * 
	 * @param array
	 * 
	 */
	$csv_columns = apply_filters( 'slicewp_mass_pay_csv_cols', array(
		'id' 			 => 'ID',
		'affiliate_name' => 'Name',
		'payment_email'  => 'Email',
		'amount' 		 => 'Amount',
		'currency' 		 => 'Currency'
	));
	
	// Prepare the CSV data.
	$data = array();

	foreach ( $payments as $key => $payment ) {

		$affiliate = slicewp_get_affiliate( $payment->get('affiliate_id') );

		$data[$key] = apply_filters( 'slicewp_mas_pay_csv_item_data', array(
			'id' 			 => $payment->get('id'),
			'affiliate_name' => slicewp_get_affiliate_name( $affiliate ),
			'payment_email'  => $affiliate->get( 'payment_email' ),
			'amount' 		 => $payment->get( 'amount' ),
			'currency' 		 => $payment->get( 'currency' ),
		), $payment );

	}

	/**
	 * Filter the CSV file data.
	 * 
	 * @param array $data
	 * 
	 */
	$data = apply_filters( 'slicewp_mass_pay_csv_data', $data );

	// Sort data based on the columns.
	foreach ( $data as $key => $item ) {

		$_item = array();

		foreach ( array_keys( $csv_columns ) as $column_key ) {

			$_item[$column_key] = ( isset( $item[$column_key] ) ? $item[$column_key] : '' );

		}

		$data[$key] = $_item;

	}

	$filename = 'slicewp-payout-' . absint( $payout->get( 'id' ) ) . '-' . str_replace( ' ', '-', get_date_from_gmt( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' ) ) . '.csv';
	
	slicewp_generate_csv( array_values( $csv_columns ), array_map( 'array_values', $data ), $filename );

}
add_action( 'slicewp_admin_action_generate_payouts_csv', 'slicewp_admin_action_generate_payouts_csv', 50 );


/**
 * Handles the single payment action.
 * 
 */
function slicewp_admin_action_do_single_payment() {

	// Verify action.
	if ( ! slicewp_verify_request_action( 'do_single_payment' ) ) {
		return;
	}

	if ( empty( $_REQUEST['payout_method'] ) ) {
		return;
	}

	if ( empty( $_REQUEST['payment_id'] ) ) {
		return;
	}

	$payout_method = sanitize_text_field( $_REQUEST['payout_method'] );
	$payment 	   = slicewp_get_payment( absint( $_REQUEST['payment_id'] ) );

	// Bail if payment doesn't exist.
	if ( is_null( $payment ) ) {
		return;
	}

	// Bail if payment is paid.
	if ( 'paid' == $payment->get( 'status' ) ) {
		return;
	}

	/**
	 * Action hook for each payout method to hook into and handle the payment.
	 *
	 * @param int $payment_id
	 *
	 */
	do_action( 'slicewp_do_single_payment_' . $payout_method, $payment->get( 'id' ) );

}
add_action( 'slicewp_admin_action_do_single_payment', 'slicewp_admin_action_do_single_payment' );


/**
 * Handles the bulk payments action, which should pay all payments of a payout.
 *
 */
function slicewp_admin_action_do_bulk_payments() {

	// Verify action.
	if ( ! slicewp_verify_request_action( 'do_bulk_payments' ) ) {
		return;
	}

	if ( empty( $_REQUEST['payout_method'] ) ) {
		return;
	}

	if ( empty( $_REQUEST['payout_id'] ) ) {
		return;
	}

	$payout_method = sanitize_text_field( $_REQUEST['payout_method'] );
	$payout 	   = slicewp_get_payout( absint( $_REQUEST['payout_id'] ) );

	if ( is_null( $payout ) ) {
		return;
	}

	// Check if all payments are paid
	$payments_all  = slicewp_get_payments( array( 'payout_id' => $payout->get( 'id' ), 'payout_method' => $payout_method ), true );
	$payments_paid = slicewp_get_payments( array( 'payout_id' => $payout->get( 'id' ), 'payout_method' => $payout_method, 'status' => 'paid' ), true );

	if ( $payments_all == $payments_paid ) {

		slicewp_add_log( 'Bulk payments was not processed. All payout payments are marked as paid.' );
		return;

	}

	/**
	 * Action hook for each payout method to hook into and handle the payments.
	 *
	 * @param int $payout_id
	 *
	 */
	do_action( 'slicewp_do_bulk_payments_' . $payout_method, $payout->get( 'id' ) );

}
add_action( 'slicewp_admin_action_do_bulk_payments', 'slicewp_admin_action_do_bulk_payments' );


/**
 * Deletes a payout and the contained payments
 *
 */
function slicewp_admin_action_delete_payout() {

	// Verify for nonce
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_payout' ) ) {
		return;
	}

	// Verify for Payout ID
	if ( empty( $_GET['payout_id'] ) ) {
		return;
	}

	$payout_id = absint( $_GET['payout_id'] );
	$payout	   = slicewp_get_payout( $payout_id );

	if ( is_null( $payout ) ) {
		return;
	}

	// Check if current user is the one that generated the payout.
	if ( $payout->get( 'originator_user_id' ) != get_current_user_id() ) {

		slicewp_admin_notices()->register_notice( 'payout_delete_different_admin', '<p>' . __( 'You are not allowed to delete this payout because it was generated by another administrator.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_delete_different_admin' );

		return;

	}

	// Get payments
	$payments = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $payout_id, 'status' => 'paid' ) );

	// Return early if any payments are paid
	if ( ! empty ( $payments ) ) {

		slicewp_admin_notices()->register_notice( 'payout_payments_paid', '<p>' . __( 'The payout was not deleted because it contains paid payments.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_payments_paid' );

		return;

	}

	// Delete the payments
	$payments = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $payout_id ) );

	foreach ( $payments as $payment ) {

		$deleted = slicewp_delete_payment( $payment->get('id') );

		if ( ! $deleted ) {

			slicewp_add_log( sprintf( 'Payout #%s was not deleted because the contained payment #%s could not be deleted.', $payout_id, $payment->get('id') ) );

			slicewp_admin_notices()->register_notice( 'payment_delete_false', '<p>' . sprintf( __( 'Payout #%s was not deleted because the contained payment #%s could not be deleted.', 'slicewp') , $payment_id, $payment->get('id') ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'payment_delete_false' );
	
			return;

		}

		// Delete the payment's metadata
		$payment_meta = slicewp_get_payment_meta( $payment->get('id') );

		if ( ! empty( $payment_meta ) ) {

			foreach ( $payment_meta as $key => $value ) {

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
		
	}


	// Delete the payout
	$deleted = slicewp_delete_payout( $payout_id );
	
	if ( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'payout_delete_false', '<p>' . __( 'Something went wrong. Could not delete the payout. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'payout_delete_false' );
	
		return;

	}

	// Delete the payout's metadata
	$payout_meta = slicewp_get_payout_meta( $payout_id );

	if ( ! empty( $payout_meta ) ) {

		foreach( $payout_meta as $key => $value ) {

			slicewp_delete_payout_meta( $payment->get('id'), $key );

		}

	}
	
	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'slicewp_message' => 'payout_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_payout', 'slicewp_admin_action_delete_payout', 50 );