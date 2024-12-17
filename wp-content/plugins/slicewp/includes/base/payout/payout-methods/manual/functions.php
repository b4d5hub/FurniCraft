<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register "Manual" payout method.
 
 * The manual payout method is registered very late and should always be included for everything to work nicely.
 *
 * @param array $payout_methods
 *
 * @return array
 *
 */
function slicewp_register_payout_method_manual( $payout_methods ) {

	if ( ! is_array( $payout_methods ) ) {
		$payout_methods = array();
	}

	$payout_methods = array_reverse( $payout_methods );

	$payout_methods['manual'] = array(
		'label'    => __( 'Manual', 'slicewp' ),
		'supports' => array( 'single_payment', 'bulk_payments', 'payout_request_invoice' ),
		'messages' => array(
			'payout_action_confirmation_bulk_payments' => __( 'This will mark all unpaid and failed payments for this payout as paid. All commissions associated with these payments will also be marked as paid. Are you sure you want to continue?', 'slicewp' )
		)
	);

	return array_reverse( $payout_methods );

}
add_filter( 'slicewp_register_payout_methods', 'slicewp_register_payout_method_manual', 999 );


/**
 * Checks to see whether within a payout the "manual" payout method can performs bulk payments.
 * 
 * @param bool 	 $can_do
 * @param int 	 $payout_id
 * @param string $payout_method
 * 
 * @return bool
 * 
 */
function slicewp_can_do_bulk_payments_payout_method_manual( $can_do, $payout_id, $payout_method ) {

	if ( 'manual' != $payout_method ) {
		return $can_do;
	}

	$payments = slicewp_get_payments( array( 'payout_id' => $payout_id, 'payout_method' => $payout_method, 'status' => 'unpaid' ), true );

	if ( ! empty( $payments ) ) {
		$can_do = true;
	}

	return $can_do;

}
add_filter( 'slicewp_can_do_bulk_payments', 'slicewp_can_do_bulk_payments_payout_method_manual', 10, 3 );


/**
 * Handles "manual" bulk payments.
 *
 * @param int $payout_id
 *
 */
function slicewp_do_bulk_payments_manual( $payout_id ) {

	$payments = slicewp_get_payments( array( 'payout_id' => $payout_id, 'payout_method' => 'manual', 'status' => 'unpaid' ) );

	// Go through each payment and mark it as paid.
	foreach ( $payments as $payment ) {

		// Update payment.
		$payment_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'paid'
		);

		$updated = slicewp_update_payment( $payment->get( 'id' ), $payment_data );

		// If the payment wasn't updated, go to next payment
		if ( ! $updated ) {
			continue;
		}

		// If the payment was updated, update each of the generated commissions.
		$commission_ids = slicewp_get_commissions( array( 'number' => -1, 'payment_id' => $payment->get( 'id' ), 'fields' => 'id' ) );
		$commission_ids = array_map( 'absint', $commission_ids );

		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'paid'
		);

		foreach ( $commission_ids as $commission_id ) {

			$updated = slicewp_update_commission( $commission_id, $commission_data );
		
		}

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-payouts', 'subpage' => 'view-payout', 'payout_id' => absint( $payout_id ), 'slicewp_message' => 'payout_bulk_payments_manual_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_do_bulk_payments_manual', 'slicewp_do_bulk_payments_manual', 10, 2 );


/**
 * Adds extra HTML after the "Payout Method" field found in the do bulk payments admin form.
 * 
 * @param int $payout_id
 * 
 */
function slicewp_form_do_bulk_payments_payout_method_bottom_manual( $payout_id ) {

	if ( empty( $payout_id ) ) {
		return;
	}

	$payments = slicewp_get_payments( array( 'number' => -1, 'payout_id' => $payout_id, 'payout_method' => 'manual', 'status' => 'unpaid', 'fields' => 'amount' ) );

	if ( empty( $payments ) ) {
		return;
	}

	?>

		<div class="slicewp-select2-option-selection-description" data-option="manual">
			<p><?php echo sprintf( _n( 'There is %d unpaid manual payment in this payout, totaling %s.', 'There are %d unpaid manual payments in this payout, totaling %s.', count( $payments ), 'slicewp' ), count( $payments ), slicewp_format_amount( array_sum( $payments ), slicewp_get_setting( 'active_currency', 'USD' ) ) ); ?></p>
			<p><?php echo __( 'By clicking the button below, you can mark these payments as paid.', 'slicewp' ) ?></p>
		</div>

	<?php

}
add_action( 'slicewp_form_do_bulk_payments_payout_method_bottom', 'slicewp_form_do_bulk_payments_payout_method_bottom_manual' );