<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link.
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_rcp', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_rcp', 10, 2 );

// Inserts a new pending commission when the registration form is processed
add_action( 'rcp_form_processing', 'slicewp_insert_pending_commission_rcp', 10, 8 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'rcp_update_payment_status_complete', 'slicewp_accept_pending_commission_rcp', 10, 1 );

// Update the status of the commission to "rejected" when the originating payment is failed or abandoned
add_action( 'rcp_update_payment_status_failed', 'slicewp_reject_commission_on_fail_rcp', 10, 1 );
add_action( 'rcp_update_payment_status_abandoned', 'slicewp_reject_commission_on_fail_rcp', 10, 1 );

// Update the status of the commission to "rejected" when the originating payment is refunded
add_action( 'rcp_update_payment_status_refunded', 'slicewp_reject_commission_on_refund_rcp', 10, 1 );

// Update the status of the commission to "rejected" when the originating payment is deleted
add_action( 'rcp_delete_payment', 'slicewp_reject_commission_on_delete_rcp', 10, 1 );

// Add the commission settings in add/edit membership level pages
add_action( 'rcp_add_subscription_form', 'slicewp_add_product_commission_settings_membership_rcp' );
add_action( 'rcp_edit_subscription_form', 'slicewp_add_product_commission_settings_membership_rcp' );

// Save the commission settings for membership levels
add_action( 'rcp_add_subscription', 'slicewp_save_product_commission_settings_rcp', 10, 2 );
add_action( 'rcp_edit_subscription_level', 'slicewp_save_product_commission_settings_rcp', 10, 2 );

// Add the reference amount in the commission data
add_filter( 'slicewp_pre_insert_commission_data', 'slicewp_add_commission_data_reference_amount_rcp' );
add_filter( 'slicewp_pre_update_commission_data', 'slicewp_add_commission_data_reference_amount_rcp' );


/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_rcp( $output, $item ) {

	if ( empty( $item['reference'] ) )
		return $output;

	if ( empty( $item['origin'] ) || $item['origin'] != 'rcp' )
		return $output;

	global $rcp_payments_db;

	if ( is_null( $rcp_payments_db ) )
		return $output;

	// Get the payment.
	$payment = $rcp_payments_db->get_payment( absint( $item['reference'] ) );

	// Create link to payment only if the payment exists
	if ( ! empty( $payment->id ) )
		$output = '<a href="' . esc_url( add_query_arg( array( 'page' => 'rcp-payments', 'payment_id' => absint( $item['reference'] ), 'view' => 'edit-payment' ), admin_url( 'admin.php' ) ) ) . '">' . $item['reference'] . '</a>';

	return $output;

}


/**
 * Inserts a new pending commission when the registration form is processed
 *
 * @param array                $_post_data          Posted data.
 * @param int                  $user_id             ID of the user registering.
 * @param float                $price               Price of the membership.
 * @param int                  $payment_id          ID of the pending payment associated with this registration.
 * @param RCP_Customer         $customer            Customer object.
 * @param int                  $membership_id       ID of the new pending membership.
 * @param RCP_Membership|false $previous_membership Previous membership object, or false if none.
 * @param string               $registration_type   Type of registration: 'new', 'renewal', or 'upgrade'.
 *
 */
function slicewp_insert_pending_commission_rcp( $_post_data, $user_id, $price, $payment_id, $customer, $membership_id, $previous_membership, $registration_type ) {

	// Check if the transaction is for a new subscription
	if ( $registration_type != 'new' ) {
		return;
	}

	global $rcp_payments_db;

	// Get the payment
	$payment = $rcp_payments_db->get_payment( $payment_id );

	/**
	 * Verify if commissions are disabled for the purchased membership level
	 *
	 */
	if ( rcp_get_membership_level_meta( $payment->object_id, 'slicewp_disable_commissions', true ) ) {
		return;
	}

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for Restrict Content Pro.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $payment_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_rcp', $affiliate_id, $payment_id );

	if ( empty( $affiliate_id ) ) {
		return;
	}

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'RCP: Pending commission was not created because the affiliate is not valid.' );
		return;
		
	}

	// Check to see if a commission for this payment has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $payment_id, 'origin' => 'rcp' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'RCP: Commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'RCP: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

		if ( ! empty( $_post_data['rcp_user_email'] ) && slicewp_affiliate_has_email( $affiliate_id, sanitize_email( $_post_data['rcp_user_email'] ) ) ) {

			slicewp_add_log( 'RCP: Commission was not created because the customer is also the affiliate.' );
			return;

		}
		
	}


	// Get user attached to the payment
	$user = get_userdata( $user_id );

	// Process the customer
	$customer_args = array(
		'email'   	 => $user->get( 'user_email' ),
		'user_id' 	 => $user_id,
		'first_name' => $user->get( 'first_name' ),
		'last_name'  => $user->get( 'last_name' )
	);

	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
		slicewp_add_log( sprintf( 'RCP: Customer #%s has been successfully processed.', $customer_id ) );
	} else {
		slicewp_add_log( 'RCP: Customer could not be processed due to an unexpected error.' );
	}

	
	// Get the order amount
	$amount = $payment->amount;

	// Calculate the commission amount for the entire payment
	$args = array(
		'origin'	   => 'rcp',
		'type' 		   => 'subscription',
		'affiliate_id' => $affiliate_id,
		'product_id'   => $payment->object_id,
		'customer_id'  => $customer_id
	);

	$commission_amount = slicewp_calculate_commission_amount( slicewp_maybe_convert_amount( $amount, rcp_get_currency(), slicewp_get_setting( 'active_currency', 'USD' ) ), $args );

	// Check that the commission amount is not zero
	if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

		slicewp_add_log( 'RCP: Commission was not inserted because the commission amount is zero. Payment: ' . absint( $payment_id ) );
		return;

	}

	// Prepare commission data
	$commission_data = array(
		'affiliate_id'		=> $affiliate_id,
		'visit_id'			=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'		=> slicewp_mysql_gmdate(),
		'date_modified'		=> slicewp_mysql_gmdate(),
		'type'				=> 'subscription',
		'status'			=> 'pending',
		'reference'			=> $payment_id,
		'reference_amount'	=> slicewp_sanitize_amount( $payment->amount ),
		'customer_id'		=> $customer_id,
		'origin'			=> 'rcp',
		'amount'			=> slicewp_sanitize_amount( $commission_amount ),
		'currency'			=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if ( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ) {

			slicewp_update_visit( $visit_id, array( 'date_modified' => slicewp_mysql_gmdate(), 'commission_id' => $commission_id ) );
			
		}
		
		slicewp_add_log( sprintf( 'RCP: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'RCP: Pending commission could not be inserted due to an unexpected error.' );

	}

}


/**
 * Updates the status of the commission attached to a payment to "unpaid", thus marking it as complete.
 *
 * @param int $payment_id
 *
 */
function slicewp_accept_pending_commission_rcp( $payment_id ) {

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $payment_id, 'origin' => 'rcp', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		// Return if the commission has already been paid.
		if ( $commission->get( 'status' ) == 'paid' )
			continue;

		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'unpaid'
		);

		// Update the commission.
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );

		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'RCP: Pending commission #%s successfully marked as completed.', $commission->get('id') ) );

		} else {

			slicewp_add_log( sprintf( 'RCP: Pending commission #%s could not be completed due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}

/**
 * Update the status of the commission to "rejected" when the originating payment is failed.
 *
 * @param int $payment_id
 *
 */
function slicewp_reject_commission_on_fail_rcp( $payment_id ) {

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $payment_id, 'origin' => 'rcp', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'RCP: Commission #%s successfully marked as rejected, after payment #%s failed.', $commission->get( 'id' ), $payment_id ) );

		} else {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating payment is refunded.
 *
 * @param int $payment_id
 *
 */
function slicewp_reject_commission_on_refund_rcp( $payment_id ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $payment_id, 'origin' => 'rcp', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
			continue;
	
		}
	
		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'rejected'
		);
	
		// Update the commission
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );
	
		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'RCP: Commission #%s successfully marked as rejected, after payment #%s was refunded.', $commission->get( 'id' ), $payment_id ) );

		} else {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating payment is deleted.
 *
 * @param int $payment_id
 *
 */
function slicewp_reject_commission_on_delete_rcp( $payment_id ) {

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $payment_id, 'origin' => 'rcp', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
			continue;
	
		}
	
		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'rejected'
		);
	
		// Update the commission
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );
	
		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'RCP: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get( 'id' ), $payment_id ) );

		} else {

			slicewp_add_log( sprintf( 'RCP: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Adds the product commission settings fields in Restrict Content Pro add membership page
 *
 */
function slicewp_add_product_commission_settings_membership_rcp() {

	$level_id 			 = ( ! empty( $_GET['edit_subscription'] ) ? absint( $_GET['edit_subscription'] ) : 0 );
	$disable_commissions = rcp_get_membership_level_meta( $level_id, 'slicewp_disable_commissions', true );

?>

	<tr id="slicewp_product_settings" class="slicewp-options-groups-wrapper" >
		<td colspan="2" style="padding: 0px;">

			<h2><?php echo __( 'Commission Settings', 'slicewp' ) ?></h2>

				<?php

					/**
					 * Hook to add option groups before the core one
					 * 
					 */
					do_action( 'slicewp_rcp_commission_settings_top' );

				?>

				<table class="slicewp-options-group form-table">
					<tbody>

						<?php

							/**
							 * Hook to add settings before the core ones
							 * 
							 */
							do_action( 'slicewp_rcp_commission_settings_core_top' );

						?>

						<tr class="slicewp-option-field-wrapper form-field">
							<th scope="row" valign="top">
								<label for="slicewp-disable-commissions"><?php echo __( 'Disable commissions', 'slicewp' ); ?></label>
							</th>
							<td>
								<input type="checkbox" class="slicewp-option-field-disable-commissions" name="slicewp_disable_commissions" id="slicewp-disable-commissions" value="1" <?php checked( $disable_commissions, true ); ?> />
								<p class="description"><?php echo __( 'Disable commissions for this membership level.', 'slicewp' ); ?></p>
							</td>
						</tr>

						<?php

							/**
							 * Hook to add settings after the core ones
							 * 
							 */
							do_action( 'slicewp_rcp_commission_settings_core_bottom' );

						?>

					</tbody>
				</table>

				<?php

					/**
					 * Hook to add option groups after the core one
					 * 
					 */
					do_action( 'slicewp_rcp_commission_settings_bottom' );
				
				?>

		</td>
	</tr>

<?php

	// Add nonce field
	wp_nonce_field( 'slicewp_save_membership', 'slicewp_token', false );

}


/**
 * Saves the commission settings into RCP membership meta
 * 
 * @param int	$level_id
 * @param array $args
 * 
 */
function slicewp_save_product_commission_settings_rcp( $level_id, $args ) {

    // Verify for nonce
    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_membership' ) )
        return;

    // Update the disable commissions settings
    if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

        rcp_update_membership_level_meta( $level_id, 'slicewp_disable_commissions', 1 );

    } else {

        rcp_delete_membership_level_meta( $level_id, 'slicewp_disable_commissions' );

    }

}


/**
 * Adds the reference amount in the commission data
 * 
 * @param array $commission_data
 * 
 * @return array
 * 
 */
function slicewp_add_commission_data_reference_amount_rcp( $commission_data ) {

	if ( ! ( doing_action( 'slicewp_admin_action_add_commission' ) || doing_action( 'slicewp_admin_action_update_commission' ) ) )
		return $commission_data;

	// Check if the origin is Restrict Content Pro
	if ( 'rcp' != $commission_data['origin'] )
		return $commission_data;

	// Check if we have a reference
	if ( empty( $commission_data['reference'] ) )
		return $commission_data;

	global $rcp_payments_db;

	if ( is_null( $rcp_payments_db ) )
		return $commission_data;

	// Get the payment
	$payment = $rcp_payments_db->get_payment( $commission_data['reference'] );

	if ( empty( $payment ) )
		return $commission_data;

	// Save the reference amount
	$commission_data['reference_amount'] = slicewp_sanitize_amount( $payment->amount );

	// Return the updated commission data
	return $commission_data;

}