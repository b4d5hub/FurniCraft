<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link.
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_surecart', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_surecart', 10, 2 );

// Insert a new pending commission.
add_action( 'surecart/checkout_confirmed', 'slicewp_insert_pending_commission_surecart', 99 );

// Update the status of the commission to "unpaid", thus marking it as complete.
add_action( 'surecart/order_paid', 'slicewp_accept_pending_commission_surecart', 99 );

// Update the status of the commission to "rejected" when the originating order is refunded.
add_action( 'surecart/refund_created', 'slicewp_reject_commission_on_refund_surecart', 99 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_surecart( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'surecart' ) {
		return $output;
	}

    $order = \SureCart\Models\Order::find( $item['reference'] );

    // Create link to order only if the order exists.
    if ( ! empty( $order->id ) ) {

        $url = esc_url( \SureCart::getUrl()->edit( 'order', $order->id ) );

        if ( ! empty( $url ) ) {
            $output = '<a href="' . esc_url( $url ) . '">#' . $order->number . '</a>';
        }

    }

	return $output;

}


/**
 * Inserts a new pending commission when a checkout is processed.
 *
 * @param \SureCart\Models\Checkout $checkout
 *
 */
function slicewp_insert_pending_commission_surecart( $checkout ) {

    // Get and check to see if referrer exists.
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

    $checkout = \SureCart\Models\Checkout::with( [ 'initial_order', 'order', 'product', 'customer' ] )->find( $checkout->id );

    if ( empty( $checkout->order->id ) ) {
        return;
    }

    $order = $checkout->order;

    /**
	 * Filters the referrer affiliate ID for SureCart.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int    $affiliate_id
	 * @param string $order->id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_surecart', $affiliate_id, $order->id );

	if ( empty( $affiliate_id ) ) {
		return;
	}

    // Verify if the affiliate is valid.
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'SureCart: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

    // Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'reference' => $order->id, 'origin' => 'surecart' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'SureCart: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

    // Check to see if the affiliate made the purchase.
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get( 'user_id' ) ) {

			slicewp_add_log( 'SureCart: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}
		
		if ( slicewp_affiliate_has_email( $affiliate_id, $purchase->customer->email ) ) {

			slicewp_add_log( 'SureCart: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}


    // Process the customer.
	$customer_args = array(
		'email'   	   => $checkout->customer->email,
		'user_id' 	   => ( ! empty( $checkout->metadata->wp_created_by ) ? absint( $checkout->metadata->wp_created_by ) : get_current_user_id() ),
		'first_name'   => $checkout->customer->first_name,
		'last_name'    => $checkout->customer->last_name,
		'affiliate_id' => $affiliate_id
	);

	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
        slicewp_add_log( sprintf( 'SureCart: Customer #%s has been successfully processed.', $customer_id ) );
    } else {
        slicewp_add_log( 'SureCart: Customer could not be processed due to an unexpected error.' );
    }


	if ( \SureCart\Support\Currency::isZeroDecimal( $checkout->currency ) ) {
        $order_amount = $checkout->amount_due;
    } else {
        $order_amount = round( $checkout->amount_due / 100, 2 );
    }

    // Calculate commission amount.
    $args = array(
        'origin'	   => 'surecart',
        'type' 		   => 'sale',
        'affiliate_id' => $affiliate_id,
        'customer_id'  => $customer_id
    );

	$commission_amount = slicewp_calculate_commission_amount( slicewp_maybe_convert_amount( $order_amount, strtoupper( $checkout->currency ), slicewp_get_setting( 'active_currency', 'USD' ) ), $args );

    // Check that the commission amount is not zero.
	if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

		slicewp_add_log( 'SureCart: Commission was not inserted because the commission amount is zero. Order: ' . sanitize_text_field( $order->id ) );
		return;

	}

    // Prepare commission data.
	$commission_data = array(
		'affiliate_id'		=> absint( $affiliate_id ),
		'visit_id'			=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'		=> slicewp_mysql_gmdate(),
		'date_modified'		=> slicewp_mysql_gmdate(),
		'type'				=> 'sale',
		'status'			=> 'pending',
		'reference'			=> sanitize_text_field( $order->id ),
		'reference_amount'	=> slicewp_sanitize_amount( $order_amount ),
		'customer_id'		=> absint( $customer_id ),
		'origin'			=> 'surecart',
		'amount'			=> slicewp_sanitize_amount( $commission_amount ),
		'currency'			=> slicewp_get_setting( 'active_currency', 'USD' )
	);

    // Insert the commission.
	$commission_id = slicewp_insert_commission( $commission_data );

	if ( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ) {

			slicewp_update_visit( $visit_id, array( 'date_modified' => slicewp_mysql_gmdate(), 'commission_id' => $commission_id ) );
			
		}
		
		slicewp_add_log( sprintf( 'SureCart: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'SureCart: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to an order to "unpaid", thus marking it as complete.
 *
 * @param \SureCart\Models\Order $order
 *
 */
function slicewp_accept_pending_commission_surecart( $order ) {

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order->id, 'origin' => 'surecart', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
        return;
    }

	foreach ( $commissions as $commission ) {

		// Return if the commission has already been paid.
		if ( $commission->get('status') == 'paid' ) {
            continue;
        }

		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'unpaid'
		);

		// Update the commission.
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );

		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'SureCart: Pending commission #%s successfully marked as completed.', $commission->get( 'id' ) ) );

		} else {

			slicewp_add_log( sprintf( 'SureCart: Pending commission #%s could not be completed due to an unexpected error.' ), $commission->get( 'id' ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating order is refunded.
 *
 * @param \SureCart\Models\Refund $refund
 *
 */
function slicewp_reject_commission_on_refund_surecart( $refund ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) ) {
		return;
	}

	$refund = \SureCart\Models\Refund::with( [ 'charge', 'charge.checkout', 'checkout.order' ] )->find( $refund->id );

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $refund->charge->checkout->order->id, 'origin' => 'surecart', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'SureCart: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
			continue;
	
		}
	
		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'rejected'
		);
	
		// Update the commission.
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );
	
		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'SureCart: Commission #%s successfully marked as rejected, after order #%s was refunded.', $commission->get( 'id' ), ( $refund->charge->checkout->order->number . ' (' . $refund->charge->checkout->order->id . ')' ) ) );

		} else {

			slicewp_add_log( sprintf( 'SureCart: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}