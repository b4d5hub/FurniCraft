<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_upsell', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_upsell', 10, 2 );

// Insert a new pending commission
add_action( 'upsell_order_entity_created', 'slicewp_insert_pending_commission_upsell', 10 );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'upsell_order_status_completed', 'slicewp_accept_pending_commission_upsell', 10 );

// Update the status of the commission to "rejected" when the originating purchase is refunded
add_action( 'upsell_order_status_refunded', 'slicewp_reject_commission_on_refund_upsell', 10 );

// Update the status of the commission to "rejected" when the originating purchase is deleted
add_action( 'upsell_order_entity_trashed', 'slicewp_reject_commission_on_delete_upsell', 10 );
add_action( 'upsell_order_status_cancelled', 'slicewp_reject_commission_on_delete_upsell', 10 );

// Add the commission settings in product page
add_filter( 'upsell_product_field_group', 'slicewp_add_product_field_group_commission_settings_upsell', 10, 2);

/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_upsell( $output, $item ) {

	if ( empty( $item['reference'] ) )
		return $output;

	if ( empty( $item['origin'] ) || $item['origin'] != 'upsell' )
		return $output;

    // Get the order
	$order = new Upsell\Entities\Order( $item['reference'] );

	// Create link to order only if the order exists
    if ( ! empty( $order->getId() ) )
		$output = '<a href="' . esc_url( add_query_arg( array( 'post' => $item['reference'], 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . $item['reference'] . '</a>';

	return $output;

}


/**
 * Inserts a new pending commission when a new pending order is registered
 *
 * @param Upsell\Entities\Order $order
 *
 */
function slicewp_insert_pending_commission_upsell( $order ) {

	// Get and check to see if referrer exists
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for Upsell.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $order_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_upsell', $affiliate_id, $order->getId() );

	if ( empty( $affiliate_id ) ) {
		return;
	}

	// Verify if the affiliate is valid
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'UPSELL: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'reference' => $order->getId(), 'origin' => 'upsell' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'UPSELL: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'UPSELL: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}
		
		if ( slicewp_affiliate_has_email( $affiliate_id, $order->getAttribute('customer_email') ) ) {

			slicewp_add_log( 'UPSELL: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}


	// Process the customer
	$user_id =  get_current_user_id();

	$customer_args = array(
		'email'   	   => $order->getAttribute('customer_email'),
		'user_id' 	   => ! empty( $user_id ) ? $user_id : 0,
		'first_name'   => $order->getAttribute('customer_first_name'),
		'last_name'    => $order->getAttribute('customer_last_name'),
		'affiliate_id' => $affiliate_id
	);

	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
		slicewp_add_log( sprintf( 'UPSELL: Customer #%s has been successfully processed.', $customer_id ) );
	} else {
		slicewp_add_log( 'UPSELL: Customer could not be processed due to an unexpected error.' );
	}

	// Calculate the commission amount for each item in the cart
	if ( ! slicewp_is_commission_basis_per_order() ) {

		$commission_amount = 0;

		foreach ( $order->getItems() as $cart_item ) {

            // Verify if commissions are disabled for this product
            if ( get_post_meta( $cart_item['id'], 'slicewp_disable_commissions', true ) ) {
				continue;
			}

			$amount = $cart_item['total'];

			// Exclude tax
			if ( slicewp_get_setting( 'exclude_tax', false ) ) {
				$amount -= $cart_item['tax_total'];
			}

			// Calculate commission amount
			$args = array(
				'origin'	   => 'upsell',
				'type' 		   => ! empty( $cart_item['options']['plan'] ) ? 'subscription' : 'sale',
				'affiliate_id' => $affiliate_id,
				'product_id'   => $cart_item['id'],
				'customer_id'  => $customer_id
			);

			$commission_amount += slicewp_calculate_commission_amount( $amount, $args );

            // Save the order commission types for future use
            $order_commission_types[] = $args['type'];

		}

	// Calculate the commission amount for the entire order
	} else {

		$args = array(
			'origin'	   => 'upsell',
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

        slicewp_add_log( 'UPSELL: Commission was not inserted because the commission amount is zero. Order: ' . absint( $order->getId() ) );
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
		'reference'			=> $order->getId(),
		'reference_amount'	=> slicewp_sanitize_amount( $order->getTotal() ),
		'customer_id'		=> $customer_id,
		'origin'			=> 'upsell',
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
		
		slicewp_add_log( sprintf( 'UPSELL: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'UPSELL: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a order to "unpaid", thus marking it as complete.
 *
 * @param Upsell\Entities\Order $order
 *
 */
function slicewp_accept_pending_commission_upsell( $order ) {

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order->getId(), 'origin' => 'upsell', 'order' => 'ASC' ) );

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

			slicewp_add_log( sprintf( 'UPSELL: Pending commission #%s successfully marked as completed.', $commission->get( 'id' ) ) );

		} else {

			slicewp_add_log( sprintf( 'UPSELL: Pending commission #%s could not be completed due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is refunded.
 *
 * @param Upsell\Entities\Order $order
 *
 */
function slicewp_reject_commission_on_refund_upsell( $order ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order->getId(), 'origin' => 'upsell', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s successfully marked as rejected, after order #%s was refunded.', $commission->get( 'id' ), $order->getId() ) );

		} else {

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is deleted
 *
 * @param Upsell\Entities\Order $order
 *
 */
function slicewp_reject_commission_on_delete_upsell( $order ) {

	// Check to see if a commission for this order has been registered
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order->getId(), 'origin' => 'upsell', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s successfully marked as rejected, after order #%s was deleted.', $commission->get( 'id' ), $order->getId() ) );

		} else {

			slicewp_add_log( sprintf( 'UPSELL: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Adds the commissions settings fields in the product page
 * 
 * @param array $fieldGroup
 * @param int $id
 * 
 */
function slicewp_add_product_field_group_commission_settings_upsell( $fieldGroup, $id ) {

	if ( isset( $fieldGroup['fields'] ) ) {

		// Add the Commission Settings tab in the product settings
		$fieldGroup['fields'] = array_merge(
			$fieldGroup['fields'],
			[
				array(
					'key' => 'slicewp_commission_settings_tab',
					'label' => __( 'Commission Settings', 'slicewp' ),
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				)
			]
		);

		/**
		 * Filter to add fields before the core ones
		 * 
		 */
		$fieldGroup = apply_filters( 'slicewp_upsell_product_field_group_commission_settings_top', $fieldGroup, $id );

		// Add the disable commissions checkbox
		$fieldGroup['fields'] = array_merge(
			$fieldGroup['fields'],
			[
				array(
					'key' => 'slicewp_disable_commissions',
					'label' => __( 'Disable commissions', 'slicewp' ),
					'name' => 'slicewp_disable_commissions',
					'type' => 'true_false',
					'instructions' => __( 'When enabled, affiliate commissions will not be generated for this product.', 'slicewp' ),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
				)
			]
		);

		/**
		 * Filter to add fields after the core ones
		 * 
		 */
		$fieldGroup = apply_filters( 'slicewp_upsell_product_field_group_commission_settings_bottom', $fieldGroup, $id );

	}

	return $fieldGroup;

}
