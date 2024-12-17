<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_gpd', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_gpd', 10, 2 );

// Insert a new pending commission
add_action( 'getpaid_new_invoice', 'slicewp_insert_pending_commission_gpd', 10 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'getpaid_invoice_status_publish', 'slicewp_accept_pending_commission_gpd', 10, 2 );

// Update the status of the commission to "rejected" when the originating purchase is refunded
add_action( 'wpinv_post_refund_invoice', 'slicewp_reject_commission_on_refund_gpd', 10, 2 );

// Update the status of the commission to "rejected" when the originating purchase is deleted
add_action( 'getpaid_delete_invoice', 'slicewp_reject_commission_on_delete_gpd', 10 );

// Update the status of the commission to "rejected" when the originating order is cancelled or failed payment
add_action( 'getpaid_invoice_status_wpi-failed', 'slicewp_reject_commission_on_order_fail_gpd', 10, 2 );
add_action( 'getpaid_invoice_status_wpi-cancelled', 'slicewp_reject_commission_on_order_fail_gpd', 10, 2 );

// Update the status of the commission to "pending" when the originating order is moved from failed to any other status
add_action( 'getpaid_invoice_status_wpi-pending', 'slicewp_approve_rejected_commission_gpd', 10, 3 );
add_action( 'getpaid_invoice_status_wpi-processing', 'slicewp_approve_rejected_commission_gpd', 10, 3 );
add_action( 'getpaid_invoice_status_wpi-onhold', 'slicewp_approve_rejected_commission_gpd', 10, 3 );

// Add commission settings in the wpi_item pages
add_filter( 'add_meta_boxes', 'slicewp_add_commission_settings_metabox_gpd', 10, 2 );

// Saves the commissions settings in wpi_item meta
add_action( 'getpaid_item_metabox_save', 'slicewp_save_product_commission_settings_gpd', 10, 2 );

// Add the reference amount in the commission data
add_filter( 'slicewp_pre_insert_commission_data', 'slicewp_add_commission_data_reference_amount_gpd' );
add_filter( 'slicewp_pre_update_commission_data', 'slicewp_add_commission_data_reference_amount_gpd' );

/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_gpd( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'gpd' ) {
		return $output;
	}

	// Get the invoice.
	$invoice = wpinv_get_invoice( $item['reference'] );

	if ( empty( $invoice ) ) {
		return $output;
	}

	// Create link to invoice only if the invoice exists.
	if ( ! empty( $invoice->get_id() ) ) {
		$output = '<a href="' . esc_url( add_query_arg( array( 'post' => $item['reference'], 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . $item['reference'] . '</a>';
	}

	return $output;

}


/**
 * Inserts a new pending commission when a new invoice is registered
 *
 * @param WPInv_Invoice $invoice
 *
 */
function slicewp_insert_pending_commission_gpd( $invoice ) {

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for GetPaid.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $invoice_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_gpd', $affiliate_id, $invoice->get_id() );

	if ( empty( $affiliate_id ) ) {
		return;
	}

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'GPD: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if the invoice is a renewal or not
	if ( $invoice->is_renewal() ) {

		if ( ! class_exists( 'SliceWP_Recurring_Commissions' ) ) {

			slicewp_add_log( 'GPD: Pending commission was not created because the invoice is a renewal.' );
		
		}
		
		return;

	}

	// Check to see if a commission for this invoice has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $invoice->get_id(), 'origin' => 'gpd' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'GPD: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get( 'user_id' ) ) {

			slicewp_add_log( 'GPD: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}
		
		if ( slicewp_affiliate_has_email( $affiliate_id, $invoice->get_email() ) ) {

			slicewp_add_log( 'GPD: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}


	// Get customer email and user id from invoice
	$customer_args = array(
		'email'   	   => $invoice->get_email(),
		'user_id' 	   => $invoice->get_user_id(),
		'first_name'   => $invoice->get_first_name(),
		'last_name'    => $invoice->get_last_name(),
		'affiliate_id' => $affiliate_id
	);

	// Process the customer
	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
		slicewp_add_log( sprintf( 'GPD: Customer #%s has been successfully processed.', $customer_id ) );
	} else {
		slicewp_add_log( 'GPD: Customer could not be processed due to an unexpected error.' );
	}


	// Get all invoice items
	$invoice_items = $invoice->get_items();

	if ( ! is_array( $invoice_items ) ) {

		slicewp_add_log( 'GPD: Pending commission was not created because the invoice details were not valid.' );
		return;

	}


	// Calculate the commission amount for each item in the invoice
	if ( ! slicewp_is_commission_basis_per_order() ) {

		$commission_amount = 0;

		foreach ( $invoice_items as $invoice_item ) {

			// Verify if commissions are disabled for this item
			if ( get_post_meta( $invoice_item->get_id(), 'slicewp_disable_commissions', true ) ) {
				continue;
			}

			// Get the item amount after discounts and taxes
			$amount = slicewp_process_item_amount_gpd( $invoice_item, $invoice );

			// Calculate commission amount
			$args = array(
				'origin'	   => 'gpd',
				'type' 		   => $invoice_item->is_recurring() ? 'subscription' : 'sale',
				'affiliate_id' => $affiliate_id,
				'product_id'   => $invoice_item->get_id(),
				'customer_id'  => $customer_id
			);

			$commission_amount += slicewp_calculate_commission_amount( slicewp_maybe_convert_amount( $amount, $invoice->get_currency( 'edit' ), slicewp_get_setting( 'active_currency', 'USD' ) ), $args );

			// Save the order commission types for future use
			$order_commission_types[] = $args['type'];

		}

	// Calculate the commission amount for the entire order
	} else {

		$args = array(
			'origin'	   => 'gpd',
			'type' 		   => 'sale',
			'affiliate_id' => $affiliate_id,
			'customer_id'  => $customer_id
		);

		$commission_amount = slicewp_calculate_commission_amount( 0, $args );
		
		// Save the order commission types for future use
		$order_commission_types[] = $args['type'];

	}

	// Check that the commission amount is not zero
	if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

		slicewp_add_log( 'GPD: Commission was not inserted because the commission amount is zero. Invoice: ' . absint( $invoice->get_id() ) );
		return;

	}
	
	// Remove duplicated order commission types
	$order_commission_types = array_unique( $order_commission_types );

	// Prepare commission data
	$commission_data = array(
		'affiliate_id'		=> $affiliate_id,
		'visit_id'			=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'		=> slicewp_mysql_gmdate(),
		'date_modified'		=> slicewp_mysql_gmdate(),
		'type'				=> sizeof( $order_commission_types ) == 1 ? $order_commission_types[0] : 'sale',
		'status'			=> 'pending',
		'reference'			=> $invoice->get_id(),
		'reference_amount'	=> slicewp_sanitize_amount( $invoice->get_total() ),
		'customer_id'		=> $customer_id,
		'origin'			=> 'gpd',
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
		
		slicewp_add_log( sprintf( 'GPD: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'GPD: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a invoice to "unpaid", thus marking it as complete.
 *
 * @param WPInv_Invoice $invoice
 * @param array 		$status_transition
 *
 */
function slicewp_accept_pending_commission_gpd( $invoice, $status_transition ) {

	// Check to see if a commission for this invoice has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $invoice->get_id(), 'origin' => 'gpd', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		// Return if the commission has already been paid.
		if ( $commission->get( 'status' ) == 'paid' ) {
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

			slicewp_add_log( sprintf( 'GPD: Pending commission #%s successfully marked as completed.', $commission->get( 'id' ) ) );

		} else {

			slicewp_add_log( sprintf( 'GPD: Pending commission #%s could not be completed due to an unexpected error.', $commission->get( 'id' ) ) );

		}
		
	}

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is refunded.
 *
 * @param WPInv_Invoice $invoice
 * @param int    		$invoice_id
 *
 */
function slicewp_reject_commission_on_refund_gpd( $invoice, $invoice_id ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) ) {
		return;
	}

	// Check to see if a commission for this invoice has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $invoice_id, 'origin' => 'gpd', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'GPD: Commission #%s successfully marked as rejected, after invoice #%s was refunded.', $commission->get( 'id' ), $invoice_id ) );

		} else {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is deleted.
 *
 * @param WPInv_Invoice $invoice
 *
 */
function slicewp_reject_commission_on_delete_gpd( $invoice ) {

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $invoice->get_id(), 'origin' => 'gpd', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
			continue;
	
		}
	
		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'rejected'
		);
	
		// Update the commission.
		$updated = slicewp_update_commission( $commission->get('id'), $commission_data );
	
		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'GPD: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get('id'), $invoice->get_id() ) );

		} else {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected due to an unexpected error.', $commission->get('id') ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating order is cancelled or failed payment.
 *
 * @param WPInv_Invoice $invoice
 * @param array 		$status_transition
 *
 */
function slicewp_reject_commission_on_order_fail_gpd( $invoice, $status_transition ) {

	if ( ! in_array( $status_transition['to'], array( 'wpi-failed', 'wpi-cancelled' ) ) ) {
		return;
	}

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $invoice->get_id(), 'origin' => 'gpd', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'GPD: Commission #%s successfully marked as rejected, after order #%s failed or was cancelled.', $commission->get( 'id' ), $invoice->get_id() ) );

		} else {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "pending" when the originating order is moved from failed to any other status.
 *
 * @param WPInv_Invoice $invoice
 * @param string 		$status_from
 * @param string 		$status_to
 *
 */
function slicewp_approve_rejected_commission_gpd( $invoice, $status_transition ) {

	if ( in_array( $status_transition['to'], array( 'wpi-failed', 'wpi-cancelled', 'wpi-refunded', 'publish' ) ) ) {
		return;
	}

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $invoice->get_id(), 'origin' => 'gpd', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) != 'rejected' ) {
			continue;
		}

		// Prepare commission data.
		$commission_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status' 		=> 'pending'
		);

		// Update the commission.
		$updated = slicewp_update_commission( $commission->get( 'id' ), $commission_data );

		if ( false !== $updated ) {

			slicewp_add_log( sprintf( 'GPD: Commission #%s successfully marked as pending, after order #%s was updated from %s to %s.', $commission->get('id'), $invoice->get_id(), $status_transition['from'], $status_transition['to'] ) );

		} else {

			slicewp_add_log( sprintf( 'GPD: Commission #%s could not be marked as pending due to an unexpected error.', $commission->get( 'id' ) ) );

		}
			
	}

}


/**
 * Adds the commissions settings metabox
 * 
 * @param string  $post_type
 * @param WP_Post $post
 * 
 */
function slicewp_add_commission_settings_metabox_gpd( $post_type, $post ) {

	// Check that post type is 'wpi_item'
	if ( $post_type != 'wpi_item' ) {
		return;
	}

	// Add the meta box
	add_meta_box( 'slicewp_metabox_commission_settings_gpd', __( 'Commission settings', 'slicewp' ),  'slicewp_add_product_commission_settings_gpd', $post_type, 'advanced', 'default' );

}


/**
 * Adds the product commission settings fields in GPD add/edit invoice page
 * 
 * 
 */
function slicewp_add_product_commission_settings_gpd() {

	global $post;

	// Get the disable commissions value
	$disable_commissions = get_post_meta( $post->ID, 'slicewp_disable_commissions', true );

	?>

	<div id="slicewp_product_settings" class="bsui slicewp-options-groups-wrapper" style="padding-top: 10px;">

		<?php

			/**
			 * Hook to add option groups before the core one
			 * 
			 */
			do_action( 'slicewp_gpd_metabox_commission_settings_top' );

		?>

		<div class="slicewp-options-group">

			<?php
				
				/**
				 * Hook to add settings before the core ones
				 * 
				 */
				do_action( 'slicewp_gpd_metabox_commission_settings_core_top' );

			?>

			<div class="slicewp-option-field-wrapper form-group row">

                <label for="slicewp-disable-commissions" class="col-sm-3 col-form-label">
                    <?php echo __( 'Disable Commissions', 'slicewp' );?>
                </label>
				
				<div class="col-sm-8">
					<?php
						echo aui()->input(
							array(
								'id'			=> 'slicewp-disable-commissions',
								'name'			=> 'slicewp_disable_commissions',
								'class'			=> 'slicewp-option-field-disable-commissions',
								'type'			=> 'checkbox',
								'label'			=> __( 'Disable commissions for this item', 'slicewp' ),
								'label_class'	=> 'col-form-label',
								'value'			=> '1',
								'checked'		=> $disable_commissions,
								'no_wrap'		=> true
							)
						);
					?>
				</div>

			</div>

			<?php

				/**
				 * Hook to add settings after the core ones
				 * 
				 */
				do_action( 'slicewp_gpd_metabox_commission_settings_core_bottom' );
			?>

		</div>

		<?php

			/**
			 * Hook to add option groups after the core one
			 * 
			 */
			do_action( 'slicewp_gpd_metabox_commission_settings_bottom' );
		
		?>

	</div>

	<?php

	// Add nonce field
	wp_nonce_field( 'slicewp_save_meta', 'slicewp_token', false );

}


/**
 * Saves the product commission settings into the item meta
 * 
 * @param int		 $post_id
 * @param WPInv_Item $item
 * 
 */
function slicewp_save_product_commission_settings_gpd( $post_id, $item ) {

	// Verify for nonce
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_meta' ) ) {
		return $post_id;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return $post_id;
	}

	// Update the disable commissions settings
	if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

		update_post_meta( $post_id, 'slicewp_disable_commissions', 1 );

	} else {

		delete_post_meta( $post_id, 'slicewp_disable_commissions' );

	}

}


/**
 * Adds the reference amount in the commission data.
 * 
 * @param array $commission_data
 * 
 * @return array
 * 
 */
function slicewp_add_commission_data_reference_amount_gpd( $commission_data ) {

	if ( ! ( doing_action( 'slicewp_admin_action_add_commission' ) || doing_action( 'slicewp_admin_action_update_commission' ) ) ) {
		return $commission_data;
	}

	// Check if the origin is GetPaid.
	if ( 'gpd' != $commission_data['origin'] ) {
		return $commission_data;
	}

	// Check if we have a reference.
	if ( empty( $commission_data['reference'] ) ) {
		return $commission_data;
	}

	// Get the invoice.
	$invoice = wpinv_get_invoice( $commission_data['reference'] );

	if ( empty( $invoice ) ) {
		return $commission_data;
	}

	// Save the reference amount.
	$commission_data['reference_amount'] = slicewp_sanitize_amount( $invoice->get_total() );

	// Return the updated commission data
	return $commission_data;

}