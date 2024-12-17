<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Payout admin area
 *
 */
function slicewp_include_files_admin_payout() {

	// Get creative admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-payouts.php' ) )
		include $dir_path . 'class-submenu-page-payouts.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-payouts.php' ) )
		include $dir_path . 'functions-actions-payouts.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-payments.php' ) )
		include $dir_path . 'functions-actions-payments.php';

	// Include payout payments preview list table
	if( file_exists( $dir_path . 'class-list-table-payout-payments-preview.php' ) )
		include $dir_path . 'class-list-table-payout-payments-preview.php';

	// Include payouts list table
	if( file_exists( $dir_path . 'class-list-table-payouts.php' ) )
		include $dir_path . 'class-list-table-payouts.php';

	// Include payout payments list table
	if( file_exists( $dir_path . 'class-list-table-payout-payments.php' ) )
		include $dir_path . 'class-list-table-payout-payments.php';

	// Include payments list table
	if( file_exists( $dir_path . 'class-list-table-payments.php' ) )
		include $dir_path . 'class-list-table-payments.php';

	// Include payments commissions list table
	if( file_exists( $dir_path . 'class-list-table-payment-commissions.php' ) )
		include $dir_path . 'class-list-table-payment-commissions.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_payout' );


/**
 * Register the Payouts admin submenu page
 *
 */
function slicewp_register_submenu_page_payouts( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['payouts'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Payouts',
		'data' 		 => array(
			'page_title' => __( 'Payouts', 'slicewp' ),
			'menu_title' => __( 'Payouts', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_payouts', 'manage_options' ),
			'menu_slug'  => 'slicewp-payouts'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_payouts', 35 );


/**
 * Localizes the payout methods custom messages before the plugin's admin script
 *
 * These messages are used for admin interaction purposes
 *
 */
function slicewp_enqueue_admin_scripts_payout_methods_messages() {

	$payout_methods = slicewp_get_payout_methods();
	$messages 		= array();

	foreach( $payout_methods as $payout_method_slug => $payout_method ) {

		if( ! empty( $payout_method['messages'] ) )
			$messages[$payout_method_slug] = $payout_method['messages'];

	}

	wp_localize_script( 'slicewp-script', 'slicewp_payout_methods_messages', $messages );
	
}
add_action( 'slicewp_enqueue_admin_scripts', 'slicewp_enqueue_admin_scripts_payout_methods_messages' );


/**
 * Generates a csv with the provided data
 *
 */
function slicewp_generate_csv( $header, $data, $filename = 'data.csv' ) {

	header( "Content-Type: text/csv; charset=utf-8" );
	header( "Content-Disposition: attachment; filename=" . $filename );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, $header );

	foreach ( $data as $row ) {

		unset( $csv_line );

		foreach ( $header as $key => $value ) {
			
			if ( isset( $row[$key] ) ) {

		 		$csv_line[] = $row[$key];

			}
		}

		fputcsv( $output, $csv_line );

	}
	
	die();

}


/**
 * Generates and returns an array with eligible payments for payout based on the given args.
 * 
 * Attention: This is an experimental function that is not considered complete.
 * 			  It's considered private and it should not be used outside of the plugin's core.
 * 
 * @access private
 * 
 * @param array $args
 * 
 * @return array
 * 
 */
function slicewp_generate_payout_payments_preview( $args ) {

	$payments = array();

	// Set up to date.
	if ( ! empty( $args['date_range'] ) && $args['date_range'] == 'up_to' ) {

		$date_min = '';
		$date_max = new DateTime( ( ! empty( $args['date_up_to'] ) ? sanitize_text_field( $args['date_up_to'] ) : date( 'Y-m-d' ) ) . ' 23:59:59' );

	}

	// Set custom date range arguments.
	if ( ! empty( $args['date_range'] ) && $args['date_range'] == 'custom_range' ) {

		$date_min = ( ! empty( $args['date_min'] ) ? new DateTime( sanitize_text_field( $args['date_min'] ) . ' 00:00:00' ) : '' );
		$date_max = ( ! empty( $args['date_max'] ) ? new DateTime( sanitize_text_field( $args['date_max'] ) . ' 23:59:59') : '' );
		
	}

	// Set affiliates.
	if ( ! empty( $args['included_affiliates'] ) && $args['included_affiliates'] == 'selected' ) {

		if ( ! empty( $args['selected_affiliates'] ) && is_array( $args['selected_affiliates'] ) ) {

			$selected_affiliate_ids = array_values( array_filter( array_map( 'absint', $args['selected_affiliates'] ) ) );

		}

	}

	// Take into account the grace period.
	$grace_period = slicewp_get_setting( 'commissions_grace_period', 0 );

	if ( ! empty( $date_max ) && ! empty( $grace_period ) && empty( $args['include_grace_period'] ) ) {

		$date_grace_end = new DateTime( date( 'Y-m-d' ) . ' 23:59:59');
		$date_grace_end = $date_grace_end->modify( '-' . absint( $grace_period ) . ' day' );

		if ( $date_max > $date_grace_end ) {

			$date_max = $date_grace_end;

		}

	}

	// Prepare the arguments to read the commissions.
	$commission_args = array(
		'fields'	   => 'affiliate_id',
		'number'	   => -1,
		'status'	   => 'unpaid',
		'date_min' 	   => ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
		'date_max' 	   => ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
		'affiliate_id' => ( ! empty( $selected_affiliate_ids ) ? $selected_affiliate_ids : '' ),
		'payment_id'   => 0
	);

	// Get the affiliate ids that generated the commissions.
	$affiliate_ids = slicewp_get_commissions( $commission_args );

	// Bail if we don't have any data for the queried commissions.
	if ( empty( $affiliate_ids ) ) {
		return $payments;
	}

	// Keep only the unique affiliate_ids.
	$affiliate_ids = array_unique( $affiliate_ids );
	$affiliate_ids = array_map( 'absint', $affiliate_ids );
	$affiliate_ids = array_values( $affiliate_ids );

	// Get the Payments Minimum Amount setting.
	$minimum_payment_amount = slicewp_sanitize_amount( isset( $args['payments_minimum_amount'] ) ? esc_attr( $args['payments_minimum_amount'] ) : 0 );

	// Get the currency setting.
	$currency = slicewp_get_setting( 'active_currency', 'USD' );

	// Get the commissions of each affiliate.
	foreach ( $affiliate_ids as $i => $affiliate_id ) {

		// Make sure affiliate exists.
		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_null( $affiliate ) ) {
			continue;
		}

		$commission_args['fields'] 		 = '';
		$commission_args['affiliate_id'] = $affiliate_id;

		$commissions = slicewp_get_commissions( $commission_args );

		$payment_amount = 0;
		$commission_ids = array();

		// Save the Payment amount
		foreach ( $commissions as $j => $commission ) {

			$commission_amount = (float)$commission->get( 'amount' );

			// Don't take into account zero values.
			if ( empty( $commission_amount ) ) {
				continue;
			}

			$payment_amount    += $commission_amount;
			$commission_ids[$j] = $commission->get( 'id' );
			
		}

		// Skip the payment if the amount is zero.
		if ( empty( $payment_amount ) ) {
			continue;
		}

		// Skip the payment if is less than the Payments Minimum Amount setting
		if ( $payment_amount < $minimum_payment_amount ) {
			continue;
		}

		// Prepare the Payout data
		$payment_data = array(
			'affiliate_id'		=> $affiliate_id,
			'amount'			=> $payment_amount,
			'currency'			=> $currency,
			'commission_ids'	=> $commission_ids
		);

		// Save the Payment data.
		$payments[] = $payment_data;

	}

	return $payments;

}