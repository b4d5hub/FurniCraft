<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_stc', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_stc', 10, 2 );

// Insert a new pending commission.
add_action( 'studiocart_order_created', 'slicewp_insert_pending_commission_stc' );

// Update the status of the commission to "unpaid", thus marking it as complete
add_action( 'sc_order_complete', 'slicewp_accept_pending_commission_stc', 15, 2 );

// Update the status of the commission to "rejected" when the originating order is refunded
add_action( 'sc_order_refunded', 'slicewp_reject_commission_on_refund_stc', 10, 2 );

// Update the status of the commission to "rejected" when the originating order is deleted
add_action( 'trash_sc_order', 'slicewp_reject_commission_on_delete_stc', 10, 2 );

// Add the commission settings in product page
add_filter( 'sc_product_setting_tabs', 'slicewp_add_commission_settings_tab_stc' );
add_filter( 'sc_product_setting_tab_commission_settings_fields', 'slicewp_add_commission_settings_fields_stc' );

// Saves the commissions settings in product meta
add_action( 'save_post_sc_product', 'slicewp_save_product_commission_settings_stc', 10, 2 );

// Add the commission settings in category page
add_action( 'sc_product_cat_add_form_fields', 'slicewp_add_category_commision_settings_stc', 10 );
add_action( 'sc_product_cat_edit_form_fields', 'slicewp_edit_category_commision_settings_stc', 10, 1 );

// Save the product category commission settings
add_action( 'create_sc_product_cat', 'slicewp_save_category_commission_settings_stc', 10 );
add_action( 'edited_sc_product_cat', 'slicewp_save_category_commission_settings_stc', 10 );

// Add the reference amount in the commission data
add_filter( 'slicewp_pre_insert_commission_data', 'slicewp_add_commission_data_reference_amount_stc' );
add_filter( 'slicewp_pre_update_commission_data', 'slicewp_add_commission_data_reference_amount_stc' );

/**
 * Adds the edit screen link to the reference column value from the commissions list table
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_stc( $output, $item ) {

	if ( empty( $item['reference'] ) )
		return $output;

	if ( empty( $item['origin'] ) || $item['origin'] != 'stc' )
		return $output;

	// Get the order
	$order = sc_get_order( $item['reference'] );

	// Create link to order only if the order exists
	if ( ! empty( $order ) )
		$output = '<a href="' . esc_url( add_query_arg( array( 'post' => $item['reference'], 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . $item['reference'] . '</a>';

	return $output;

}


/**
 * Inserts a new pending commission when a new order is registered.
 * 
 * @param ScrtOrder $order
 * 
 */
function slicewp_insert_pending_commission_stc( $order ) {

	// Get and check to see if referrer exists.
	$affiliate_id = slicewp_get_referrer_affiliate_id();
	$visit_id	  = slicewp_get_referrer_visit_id();

	/**
	 * Filters the referrer affiliate ID for Studiocart.
	 * This is mainly used by add-ons for different functionality.
	 *
	 * @param int $affiliate_id
	 * @param int $order_id
	 *
	 */
	$affiliate_id = apply_filters( 'slicewp_referrer_affiliate_id_stc', $affiliate_id, $order->id );

	if ( empty( $affiliate_id ) ) {
		return;
	}

	// Verify if the affiliate is valid.
	if ( ! slicewp_is_affiliate_valid( $affiliate_id ) ) {

		slicewp_add_log( 'STC: Pending commission was not created because the affiliate is not valid.' );
		return;

	}

	// Check to see if the order is a renewal or not.
	if ( ! empty( $order->renewal ) ) {

		slicewp_add_log( 'STC: Pending commission was not created because the order is a renewal.' );
		return;

	}

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'reference' => $order->id, 'origin' => 'stc' ) );

	if ( ! empty( $commissions ) ) {

		slicewp_add_log( 'STC: Pending commission was not created because another commission for the reference and origin already exists.' );
		return;

	}

	// Check to see if the affiliate made the purchase.
	if ( empty( slicewp_get_setting( 'affiliate_own_commissions' ) ) ) {

		$affiliate = slicewp_get_affiliate( $affiliate_id );

		if ( is_user_logged_in() && get_current_user_id() == $affiliate->get('user_id') ) {

			slicewp_add_log( 'STC: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}
		
		if ( slicewp_affiliate_has_email( $affiliate_id, $order_info['email'] ) ) {

			slicewp_add_log( 'STC: Pending commission was not created because the customer is also the affiliate.' );
			return;

		}

	}


	// Process the customer.
	$customer_args = array(
		'email'   	 => $order->email,
		'user_id' 	 => $order->user_account,
		'first_name' => $order->first_name,
		'last_name'  => $order->last_name
	);

	$customer_id = slicewp_process_customer( $customer_args );

	if ( $customer_id ) {
		slicewp_add_log( sprintf( 'STC: Customer #%s has been successfully processed.', $customer_id ) );
	} else {
		slicewp_add_log( 'STC: Customer could not be processed due to an unexpected error.' );
	}


	// Check if we have a one time or a recurring payment.
	$commission_type = ( $order->plan->type == 'recurring' ? 'subscription' : 'sale' );

	// Calculate the commission amount for the ordered product.
	if ( ! slicewp_is_commission_basis_per_order() ) {

		// Get the product categories.
		$categories = get_the_terms( $order->product_id, 'sc_product_cat' );

		// Verify if commissions are disabled for this product category.
		if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) ) {
			return;
		}

		// Verify if commissions are disabled for this product.
		if ( get_post_meta( $order->product_id, 'slicewp-disable-commissions', true ) ) {
			return;
		}

		$amount = $order->amount;

		// Exclude tax.
		if ( slicewp_get_setting( 'exclude_tax', false ) ) {
			$amount = $amount - $order->tax_amount;
		}

		// Calculate commission amount.
		$args = array(
			'origin'	   => 'stc',
			'type' 		   => $commission_type,
			'affiliate_id' => $affiliate_id,
			'product_id'   => $order->product_id,
			'customer_id'  => $customer_id
		);

		$commission_amount = slicewp_calculate_commission_amount( slicewp_maybe_convert_amount( $amount, $order->currency, slicewp_get_setting( 'active_currency', 'USD' ) ), $args );

	// Calculate the commission amount for the entire order.
	} else {

		$args = array(
			'origin'	   => 'stc',
			'type' 		   => $commission_type,
			'affiliate_id' => $affiliate_id,
			'customer_id'  => $customer_id
		);

		$commission_amount = slicewp_calculate_commission_amount( 0, $args );

	}

	// Check that the commission amount is not zero.
	if ( ( $commission_amount == 0 ) && empty( slicewp_get_setting( 'zero_amount_commissions' ) ) ) {

		slicewp_add_log( 'STC: Commission was not inserted because the commission amount is zero. Order: ' . absint( $order->id ) );
		return;

	}


	// Prepare commission data
	$commission_data = array(
		'affiliate_id'		=> $affiliate_id,
		'visit_id'			=> ( ! is_null( $visit_id ) ? $visit_id : 0 ),
		'date_created'		=> slicewp_mysql_gmdate(),
		'date_modified'		=> slicewp_mysql_gmdate(),
		'type'				=> $commission_type,
		'status'			=> 'pending',
		'reference'			=> absint( $order->id ),
		'reference_amount'	=> slicewp_sanitize_amount( $order->amount ),
		'customer_id'		=> $customer_id,
		'origin'			=> 'stc',
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
		
		slicewp_add_log( sprintf( 'STC: Pending commission #%s has been successfully inserted.', $commission_id ) );
		
	} else {

		slicewp_add_log( 'STC: Pending commission could not be inserted due to an unexpected error.' );
		
	}

}


/**
 * Updates the status of the commission attached to a order to "unpaid", thus marking it as complete.
 *
 * @param string $status
 * @param array  $order_info
 *
 */
function slicewp_accept_pending_commission_stc( $status, $order_info ) {

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order_info['ID'], 'origin' => 'stc', 'order' => 'ASC' ) );

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

			slicewp_add_log( sprintf( 'STC: Pending commission #%s successfully marked as completed.', $commission->get( 'id' ) ) );

		} else {

			slicewp_add_log( sprintf( 'STC: Pending commission #%s could not be completed due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}

/**
 * Update the status of the commission to "rejected" when the originating purchase is refunded.
 *
 * @param string $status
 * @param array  $order_info
 *
 */
function slicewp_reject_commission_on_refund_stc( $status, $order_info ) {

	if ( ! slicewp_get_setting( 'reject_commissions_on_refund', false ) )
		return;

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $order_info['ID'], 'origin' => 'stc', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'STC: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'STC: Commission #%s successfully marked as rejected, after order #%s was refunded.', $commission->get( 'id' ), $order_info['ID'] ) );

		} else {
			
			slicewp_add_log( sprintf( 'STC: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

		}

	}

}


/**
 * Update the status of the commission to "rejected" when the originating purchase is deleted.
 *
 * @param int $post_id
 * @param WP_Post $post
 *
 */
function slicewp_reject_commission_on_delete_stc( $post_id, $post ) {

	// Check to see if a commission for this order has been registered.
	$commissions = slicewp_get_commissions( array( 'number' => -1, 'reference' => $post_id, 'origin' => 'stc', 'order' => 'ASC' ) );

	if ( empty( $commissions ) )
		return;

	foreach ( $commissions as $commission ) {

		if ( $commission->get( 'status' ) == 'paid' ) {

			slicewp_add_log( sprintf( 'STC: Commission #%s could not be rejected because it was already paid.', $commission->get( 'id' ) ) );
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

			slicewp_add_log( sprintf( 'STC: Commission #%s successfully marked as rejected, after order #%s was deleted.', $commission->get( 'id' ), $post_id ) );

		} else {

			slicewp_add_log( sprintf( 'STC: Commission #%s could not be rejected due to an unexpected error.', $commission->get( 'id' ) ) );

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
function slicewp_add_commission_settings_tab_stc( $tabs ) {

	$tabs['commission_settings'] = __( 'Commission Settings', 'slicewp' );

	return $tabs;

}

/**
 * Adds the product commission settings fields in Studiocart add/edit product page
 * 
 * 
 */
function slicewp_add_commission_settings_fields_stc( $fields ) {

	global $post;
	
	$fields['slicewp_product_settings_div'] = array(
		'type'	=> 'html',
		'value' => '<div id="slicewp_product_settings" class="slicewp-options-groups-wrapper">'
	);

	/**
	 * Hook to add option groups before the core one
	 * 
	 */
	$fields = apply_filters( 'slicewp_stc_metabox_commission_settings_top', $fields );

	$fields['slicewp_options_group_div'] = array(
		'type'	=> 'html',
		'value' => '<div class="slicewp-options-group">'
	);


	// Get the product categories
	$categories = get_the_terms( $post->ID, 'sc_product_cat' );

	if ( ! empty( $categories[0]->term_id ) && get_term_meta( $categories[0]->term_id, 'slicewp_disable_commissions', true ) ) {

		$fields['slicewp_pcr_disabled'] = array(
			'type'	=> 'html',
			'value' => '<p class="slicewp-product-commissions-disabled">' . __( 'The product commission rate settings are not available because the commissions for this product category are disabled.', 'slicewp' ) . '</p>'
		);

	} else {


		/**
		 * Filter to add settings before the core ones
		 * 
		 */
		$fields = apply_filters( 'slicewp_stc_metabox_commission_settings_core_top', $fields );

		$fields['slicewp_option_field_wrapper_disable_commissions_div'] = array(
			'type'	=> 'html',
			'value' => '<div class="slicewp-option-field-wrapper">'
		);
		
		$fields['slicewp_disable_commissions'] = array(
			'class' 		=> 'slicewp-option-field-disable-commissions',
			'id' 			=> 'slicewp-disable-commissions',
			'label'			=> __( 'Disable commissions' ,'slicewp'),
			'type'			=> 'checkbox',
			'value'			=> '', // will be populated by Studiocart, if removed a notice will be shown
		);

		$fields['slicewp_option_field_wrapper_disable_commissions_div_end'] = array(
			'type'	=> 'html',
			'value' => '</div>'
		);

		/**
		 * Filter to add settings after the core ones
		 * 
		 */
		$fields = apply_filters( 'slicewp_stc_metabox_commission_settings_core_bottom', $fields );

	}

	// Close the options group div
	$fields['slicewp_options_group_div_end'] = array(
		'type'	=> 'html',
		'value' => '</div>'
	);

	/**
	 * Hook to add option groups after the core one
	 * 
	 */
	$fields = apply_filters( 'slicewp_stc_metabox_commission_settings_bottom', $fields );

	$fields['slicewp_product_settings_div_end'] = array(
		'type'	=> 'html',
		'value' => '</div>'
	);

    // Add nonce field
	$fields['slicewp_product_settings_nonce_field'] = array(
		'type'	=> 'html',
		'value' => wp_nonce_field( 'slicewp_save_meta', 'slicewp_token', false, false )
	);

	return $fields;

}


/**
 * Saves the product commission settings into the product meta
 * 
 * @param int $post_id
 * @param WP_Post $post
 * 
 */
function slicewp_save_product_commission_settings_stc( $post_id, $post ) {

	// Verify for nonce
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_save_meta' ) )
		return $post_id;
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return $post_id;

	// Update the disable commissions settings
	if ( ! empty( $_POST['slicewp-disable-commissions'] ) ) {

		update_post_meta( $post_id, 'slicewp-disable-commissions', 1 );

	} else {

		delete_post_meta( $post_id, 'slicewp-disable-commissions' );

	}

}


/**
 * Adds the Category Rate and Type fields in the add new product category page
 * 
 */
function slicewp_add_category_commision_settings_stc() {

	/**
	 * Hook to add fields before the core ones
	 * 
	 */
	do_action( 'slicewp_stc_add_category_form_fields_top' );
	
	?>

	<div class="slicewp-option-field-wrapper form-field">

		<label for="slicewp-disable-commissions">
			<input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions"/><?php echo __( 'Disable Commissions', 'slicewp' ); ?>
		</label>
		<p><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>

	</div>

	<?php

	/**
	 * Hook to add fields after the core ones
	 * 
	 */
	do_action( 'slicewp_stc_add_category_form_fields_bottom' );

}


/**
 * Adds the disable commissions checkbox in the edit product category page
 * 
 * @param WP_Term $category
 * 
 */
function slicewp_edit_category_commision_settings_stc( $category ) {

	// Get the product category commission settings
	$current_category_disable_commissions = get_term_meta( $category->term_id, 'slicewp_disable_commissions', true );

	/**
	 * Hook to add fields before the core ones
	 * 
	 */
	do_action( 'slicewp_stc_edit_category_form_fields_top', $category );
	
?>

	<tr class="slicewp-option-field-wrapper form-field">
		<th scope="row">
			<label for="slicewp-disable-commissions"><?php echo __( 'Disable Commissions', 'slicewp' ); ?></label>
		</th>
		<td>
			<input type="checkbox" class="slicewp-option-field-disable-commissions checkbox" name="slicewp_disable_commissions" id="slicewp-disable-commissions" <?php checked( $current_category_disable_commissions, true ); ?>/>
			<p class="description"><?php echo __( 'When checked, commissions will not be generated for this product category.', 'slicewp' ); ?></p>
		</td>
	</tr>

<?php

	/**
	 * Hook to add fields after the core ones
	 * 
	 */
	do_action( 'slicewp_stc_edit_category_form_fields_bottom', $category );

}


/**
 * Saves the product category commission settings into the category meta
 * 
 * @param int $category_id
 * 
 */
function slicewp_save_category_commission_settings_stc( $category_id ) {

	// Update the disable commissions settings
	if ( ! empty( $_POST['slicewp_disable_commissions'] ) ) {

		update_term_meta( $category_id, 'slicewp_disable_commissions', 1 );
	
	} else {

		delete_term_meta( $category_id, 'slicewp_disable_commissions' );
	
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
function slicewp_add_commission_data_reference_amount_stc( $commission_data ) {

	if ( ! ( doing_action( 'slicewp_admin_action_add_commission' ) || doing_action( 'slicewp_admin_action_update_commission' ) ) )
		return $commission_data;

	// Check if the origin is Studiocart
	if ( 'stc' != $commission_data['origin'] )
		return $commission_data;

	// Check if we have a reference
	if ( empty( $commission_data['reference'] ) )
		return $commission_data;

	// Get the order
	$order = sc_get_order( $commission_data['reference'] );

	if ( empty( $order ) )
		return $commission_data;

	// Save the reference amount
	$commission_data['reference_amount'] = slicewp_sanitize_amount( $order['order_amount'] );

	// Return the updated commission data
	return $commission_data;

}