<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_mepr', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_mepr', 10, 2 );

// Insert a new pending commission
add_action( 'mepr-txn-status-pending', 'slicewp_insert_pending_commission_mepr', 10, 1 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'mepr-txn-status-complete', 'slicewp_accept_pending_commission_mepr', 10, 1 );
add_action( 'mepr-txn-status-confirmed', 'slicewp_accept_pending_commission_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is failed
add_action( 'mepr-txn-status-failed', 'slicewp_reject_commission_on_fail_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is refunded
add_action( 'mepr-txn-status-refunded', 'slicewp_reject_commission_on_refund_mepr', 10, 1 );

// Update the status of the commission to "rejected" when the originating transaction is deleted
add_action( 'mepr_pre_delete_transaction', 'slicewp_reject_commission_on_delete_mepr', 10, 1 );

// Add the commission settings in download page
add_action( 'add_meta_boxes', 'slicewp_add_commission_settings_metabox_mepr', 10, 2 );

// Save the affiliate id in the product meta
add_action( 'save_post_memberpressproduct', 'slicewp_save_product_commission_settings_mepr', 10, 2 );

// Adds custom CSS to the admin area for this integration
add_action( 'admin_head', 'slicewp_admin_custom_css_mepr' );

// Add the reference amount in the commission data
add_filter( 'slicewp_pre_insert_commission_data', 'slicewp_add_commission_data_reference_amount_mepr' );
add_filter( 'slicewp_pre_update_commission_data', 'slicewp_add_commission_data_reference_amount_mepr' );


/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_mepr( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'mepr' ) {
		return $output;
	}

    // Get the transaction
    $transaction = new MeprTransaction( $item['reference'] );

    // Create link to payment only if the payment exists
    if ( ! empty( $transaction->id ) ) {
		$output = '<a href="' . esc_url( add_query_arg( array( 'page' => 'memberpress-trans', 'action' => 'edit', 'id' => $item['reference'] ), admin_url( 'admin.php' ) ) ) . '">' . $item['reference'] . '</a>';
	}
    
    return $output;
    
}


/**
 * Inserts a new pending commission when a new transaction is registered
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_insert_pending_commission_mepr( $transaction ) {

    // Verify if commissions are disabled for the purchased product.
    if ( get_post_meta( $transaction->product_id, 'slicewp_disable_commissions', true ) ) {
		return;
	}

    // Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for MemberPress.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $transaction->id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_mepr', $affiliate_id, $transaction->id );

	if ( empty( $affiliate_id ) ) {

		slicewp_add_log( 'MEPR: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if a commission for this transaction has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $transaction->id, 'origin' => 'mepr' ) );

	if ( ! empty( $commissions ) ) {
		return;
	}

	// Check to see if the affiliate made the purchase
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'MEPR: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

		// Get the user
		$user = get_userdata( $transaction->user_id );

		// Check to see if the affiliate made the purchase, as we don't want this
		if ( slicewp_affiliate_has_email( $affiliate_id, $user->user_email ) ) {

			slicewp_add_log( 'MEPR: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}

	// Get general options.
	$mepr_options = MeprOptions::fetch();

	// Get customer email and user id from transaction
	$user = get_userdata( $transaction->user_id );

	// Process the customer
	$customer_args = array(
		'email'   	   => $user->get( 'user_email' ),
		'user_id' 	   => $transaction->user_id,
		'first_name'   => $user->get( 'first_name' ),
		'last_name'    => $user->get( 'last_name' ),
		'affiliate_id' => $affiliate_id
	);

	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
		slicewp_add_log( sprintf( 'MEPR: Customer #%s has been successfully processed.', $customer_id ) );
	} else {
		slicewp_add_log( 'MEPR: Customer could not be processed due to an unexpected error.' );
	}


    // Get the order amount. Exclude tax.
    if ( slicewp_get_setting( 'exclude_tax', false ) ) {
		$amount = $transaction->amount;
	} else {
		$amount = $transaction->total;
	}

	// If the transaction has a subscription that is in trial, set the amount of the trial.
	if ( $transaction->subscription() && $transaction->subscription()->trial ) {

		if ( slicewp_get_setting( 'exclude_tax', false ) ) {
			$amount = $transaction->subscription()->trial_amount;
		} else {
			$amount = $transaction->subscription()->trial_total;
		}

	}

    // Calculate the commission amount for the entire transaction
    $args = array(
        'origin'	   => 'mepr',
        'type' 		   => 'subscription',
        'affiliate_id' => $affiliate_id,
        'product_id'   => $transaction->product_id,
		'customer_id'  => $customer_id
    );

	$commission_amount = slicewp_calculate_commission_amount( slicewp_maybe_convert_amount( $amount, $mepr_options->currency_code, slicewp_get_setting( 'active_currency', 'USD' ) ), $args );

    // Check that the commission amount is not zero.
    if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

        slicewp_add_log( 'MEPR: Commission was not inserted because the commission amount is zero. Transaction: ' . absint( $transaction->id ) );
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
		'reference'			=> $transaction->id,
		'reference_amount'	=> slicewp_sanitize_amount( $transaction->total ),
		'customer_id'		=> $customer_id,
		'origin'			=> 'mepr',
		'amount'			=> slicewp_sanitize_amount( $commission_amount ),
		'currency'			=> slicewp_get_setting( 'active_currency', 'USD' )
	);

	// Insert the commission
	$commission_id = slicewp_insert_commission( $commission_data );

	if( ! empty( $commission_id ) ) {

		// Update the visit with the newly inserted commission_id
		if ( ! is_null( $visit_id ) ) {

			slicewp_update_visit( $visit_id, array( 'date_modified' => slicewp_mysql_gmdate(), 'commission_id' => $commission_id ) );
			
		}
		
		slicewp_add_log( sprintf( 'MEPR: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'MEPR: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a transaction to "unpaid", thus marking it as complete.
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_accept_pending_commission_mepr( $transaction ) {

	// Check to see if a commission for this transaction has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $transaction->id, 'origin' => 'mepr', 'order' => 'ASC' ) );

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

			slicewp_add_log( sprintf( 'MEPR: Pending commission #%s successfully marked as completed.', $commission->get( 'id' ) ) );

		} else {

			slicewp_add_log( sprintf( 'MEPR: Pending commission #%s could not be completed due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is failed.
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_fail_mepr( $transaction ) {

	if ( empty( $transaction->id ) ) {
		return;
	}

	// Check to see if a commission for this transaction has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $transaction->id, 'origin' => 'mepr', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after transaction #%s failed.', $commission->get( 'id' ), $transaction->id ) );

		} else {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is refunded.
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_refund_mepr( $transaction ) {

	if ( empty( $transaction->id ) ) {
		return;
	}

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) ) {
		return;
	}

	// Check to see if a commission for this transaction has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $transaction->id, 'origin' => 'mepr', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after transaction #%s was refunded.', $commission->get( 'id' ), $transaction->id ) );

		} else {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating transaction is deleted.
 *
 * @param MeprTransaction $transaction
 *
 */
function slicewp_reject_commission_on_delete_mepr( $transaction ) {

	if ( empty( $transaction->id ) ) {
		return;
	}

	// Check to see if a commission for this payment has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $transaction->id, 'origin' => 'mepr', 'order' => 'ASC' ) );

	if ( empty( $commissions ) ) {
		return;
	}

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'MEPR: Commission #%s successfully marked as rejected, after payment #%s was deleted.', $commission->get( 'id' ), $transaction->id ) );

		} else {

			slicewp_add_log( sprintf( 'MEPR: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Adds the commissions settings metabox
 * 
 * @param string $post_type
 * @param WP_Post $post
 * 
 */
function slicewp_add_commission_settings_metabox_mepr( $post_type, $post ) {
	
    // Check that post type is 'memberpressproduct'
    if ( $post_type != 'memberpressproduct' ) {
		return;
	}

    // Add the meta box
    add_meta_box( 'slicewp_metabox_commission_settings_mepr', __( 'Subscription Commission Settings', 'slicewp' ), 'slicewp_add_product_commission_settings_mepr', $post_type, 'advanced', 'high' );

}


/**
 * Adds the product commission settings fields in MemberPress add/edit subscription page
 * 
 */
function slicewp_add_product_commission_settings_mepr() {

    global $post;

    // Get the disable commissions value
    $disable_commissions = get_post_meta( $post->ID, 'slicewp_disable_commissions', true );

?>

    <div id="slicewp_product_settings" class="slicewp-options-groups-wrapper">

        <?php

            /**
             * Hook to add option groups before the core one
             * 
             */
            do_action( 'slicewp_mepr_metabox_commission_settings_top' );

        ?>

        <div class="slicewp-options-group">

            <?php
                
                /**
                 * Hook to add settings before the core ones
                 * 
                 */
                do_action( 'slicewp_mepr_metabox_commission_settings_core_top' );

            ?>

            <div class="slicewp-option-field-wrapper" style="margin-top: 0;">
            	<label for="slicewp-disable-commissions"><?php echo __( 'Disable Commissions', 'slicewp' ); ?></label>
                <label for="slicewp-disable-commissions">
                    <input type="checkbox" class="slicewp-option-field-disable-commissions" name="slicewp_disable_commissions" id="slicewp-disable-commissions" value="1"<?php checked( $disable_commissions, true ); ?> />
                    <?php echo __( 'Disable commissions for this membership.', 'slicewp' ); ?>
                </label>
            </div>

            <?php

                /**
                 * Hook to add settings after the core ones
                 * 
                 */
                do_action( 'slicewp_mepr_metabox_commission_settings_core_bottom' );
            ?>

        </div>

        <?php

            /**
             * Hook to add option groups after the core one
             * 
             */
            do_action( 'slicewp_mepr_metabox_commission_settings_bottom' );
        
        ?>

    </div>

<?php

    // Add nonce field
    wp_nonce_field( 'slicewp_save_meta', 'slicewp_token', false );

}


/**
 * Saves the product commission settings into the product meta
 * 
 * @param int $post_id
 * @param WP_Post $post
 * 
 */
function slicewp_save_product_commission_settings_mepr( $post_id, $post ) {

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
 * Adds custom CSS to the admin area for this integration
 *
 */
function slicewp_admin_custom_css_mepr() {

	if ( 'memberpressproduct' !== get_post_type() ) {
		return;
	}
	
	?>

		<style>
			#slicewp_product_settings { margin: 20px 0 8px 0; }
			.slicewp-option-field-wrapper { margin-left: 200px; min-height: 30px; margin-top: 25px; }
			.slicewp-option-field-wrapper > label:first-of-type { display: inline-block; float: left; max-width: 195px; width: 100%; margin-left: -200px; }
			.slicewp-option-field-wrapper > label:first-of-type p { margin: 0; }
			.slicewp-option-field-wrapper > label:first-of-type p strong { font-weight: normal; }
		</style>

	<?php

}


/**
 * Adds the reference amount in the commission data
 * 
 * @param array $commission_data
 * 
 * @return array
 * 
 */
function slicewp_add_commission_data_reference_amount_mepr( $commission_data ) {

	if ( ! ( doing_action( 'slicewp_admin_action_add_commission' ) || doing_action( 'slicewp_admin_action_update_commission' ) ) ) {
		return $commission_data;
	}

	// Check if the origin is Memberpress
	if ( 'mepr' != $commission_data['origin'] ) {
		return $commission_data;
	}

	// Check if we have a reference
	if ( empty( $commission_data['reference'] ) ) {
		return $commission_data;
	}

	// Get the transaction
	$transaction = new MeprTransaction( $commission_data['reference'] );

	if ( empty( $transaction ) || empty( $transaction->total ) ) {
		return $commission_data;
	}

	// Save the reference amount
	$commission_data['reference_amount'] = slicewp_sanitize_amount( $transaction->total );

	// Return the updated commission data
	return $commission_data;

}