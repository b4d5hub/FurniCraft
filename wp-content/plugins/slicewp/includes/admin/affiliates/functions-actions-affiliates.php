<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Validates and handles the adding of the new affiliate in the database
 *
 */
function slicewp_admin_action_add_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_add_affiliate' ) )
		return;

	// Verify for user ID
	if( empty( $_POST['user_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_user_id_missing', '<p>' . __( 'Please select the user you wish to add as an affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_user_id_missing' );

		return;

	}


	// Get all affiliate fields and build their objects
    $affiliate_fields = array();

    foreach ( slicewp_get_affiliate_fields() as $affiliate_field ) {

        $field = slicewp_create_form_field_object( $affiliate_field );

        if ( is_null( $field ) )
            continue;

        $affiliate_fields[] = $field;

    }

    // Filter out from validation WP_User specific fields if user is logged-in
    foreach ( $affiliate_fields as $key => $field ) {

        if( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm' ) ) )
            unset( $affiliate_fields[$key] );

    }

    // Reset array values and keys
    $affiliate_fields = array_values( $affiliate_fields );


    // Validate fields
    foreach ( $affiliate_fields as $field ) {

        $field->validate( $_POST );

    }


    // For smooth user experience break the execution now and return any errors.
    if ( slicewp_form_errors()->has_errors() ) {

    	$error_fields_labels = array();
    	$error_string 		 = '';

    	$affiliate_fields_names = array_column( array_map( function( $field ) { return $field->to_array(); }, $affiliate_fields ), 'name' );
    	$form_errors_codes		= slicewp_form_errors()->get_error_codes();

    	if ( array_intersect( $form_errors_codes, $affiliate_fields_names ) ) {

    		foreach ( $affiliate_fields as $field ) {

	    		if( in_array( $field->get('name'), slicewp_form_errors()->get_error_codes() ) ) {

	    			$errors = slicewp_form_errors()->get_error_messages( $field->get('name') );

	    			$error_string .= '<p><strong>' . $field->get( 'label' ) . ':</strong></p>';

	    			foreach ( $errors as $error ) {

	    				$error_string .= '<p>' . $error . '</p>';

	    			}

	    		}

	    	}

	        slicewp_admin_notices()->register_notice( 'empty_fields_error', '<p>' . __( 'Some information is missing or is invalid. Please review the following fields:', 'slicewp' ) . '</p>' . $error_string, 'error' );
	        slicewp_admin_notices()->display_notice( 'empty_fields_error' );

    	} else {

    		foreach ( slicewp_form_errors()->get_error_codes() as $error_code ) {

    			slicewp_admin_notices()->register_notice( $error_code, wpautop( slicewp_form_errors()->get_error_message( $error_code ) ), 'error' );
		        slicewp_admin_notices()->display_notice( $error_code );

    		}

    	}

        return;

    }


	// Verify for affiliate status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_missing', '<p>' . __( 'Please select the status of your new affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_missing' );

		return;

	}

	$statuses = slicewp_get_affiliate_available_statuses();

	// Verify if the affiliate status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_invalid' );

		return;

	}

	// Verify if payout method exists.
	if ( ! empty( $_POST['payout_method'] ) ) {

		$payout_methods = slicewp_get_payout_methods();

		if ( ! in_array( sanitize_text_field( $_POST['payout_method'] ), array_keys( $payout_methods ) ) ) {

			slicewp_admin_notices()->register_notice( 'affiliate_payout_method_invalid', '<p>' . __( 'The selected payout method is not registered.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'affiliate_payout_method_invalid' );

			return;
			
		}

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare affiliate data to be inserted
	$affiliate_data = array(
		'user_id' 		=> absint( $_POST['user_id'] ),
		'payment_email'	=> ( ! empty( $_POST['payment_email'] ) ? sanitize_text_field( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' ),
		'date_created'  => slicewp_mysql_gmdate(),
		'date_modified' => slicewp_mysql_gmdate(),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Insert affiliate into the database
	$affiliate_id = slicewp_insert_affiliate( $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $affiliate_id ) {

		slicewp_admin_notices()->register_notice( 'affiliate_insert_false', '<p>' . __( 'Something went wrong. Could not add the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_insert_false' );

		return;

	}

	// Update payout method.
	if ( isset( $_POST['payout_method'] ) ) {

		slicewp_update_affiliate_meta( $affiliate_id, 'payout_method', ( ! empty( $_POST['payout_method'] ) ) ? sanitize_text_field( $_POST['payout_method'] ) : '' );
		
	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_insert_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_add_affiliate', 'slicewp_admin_action_add_affiliate', 50 );


/**
 * Validates and handles the updating of an affiliate in the database
 *
 */
function slicewp_admin_action_update_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_affiliate' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_POST['affiliate_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_id_missing', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_id_missing' );

		return;

	}


	// Get all affiliate fields and build their objects
    $affiliate_fields = array();

    foreach ( slicewp_get_affiliate_fields() as $affiliate_field ) {

        $field = slicewp_create_form_field_object( $affiliate_field );

        if ( is_null( $field ) )
            continue;

        $affiliate_fields[] = $field;

    }

    // Filter out from validation WP_User specific fields if user is logged-in
    foreach ( $affiliate_fields as $key => $field ) {

        if( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm' ) ) )
            unset( $affiliate_fields[$key] );

    }

    // Reset array values and keys
    $affiliate_fields = array_values( $affiliate_fields );


    // Validate fields
    foreach ( $affiliate_fields as $field ) {

        $field->validate( $_POST );

    }


    // For smooth user experience break the execution now and return any errors.
    if ( slicewp_form_errors()->has_errors() ) {

    	$error_fields_labels = array();
    	$error_string 		 = '';

    	$affiliate_fields_names = array_column( array_map( function( $field ) { return $field->to_array(); }, $affiliate_fields ), 'name' );
    	$form_errors_codes		= slicewp_form_errors()->get_error_codes();

    	if ( array_intersect( $form_errors_codes, $affiliate_fields_names ) ) {

    		foreach ( $affiliate_fields as $field ) {

	    		if( in_array( $field->get('name'), slicewp_form_errors()->get_error_codes() ) ) {

	    			$errors = slicewp_form_errors()->get_error_messages( $field->get('name') );

	    			$error_string .= '<p><strong>' . $field->get( 'label' ) . ':</strong></p>';

	    			foreach ( $errors as $error ) {

	    				$error_string .= '<p>' . $error . '</p>';

	    			}

	    		}

	    	}

	        slicewp_admin_notices()->register_notice( 'empty_fields_error', '<p>' . __( 'Some information is missing or is invalid. Please review the following fields:', 'slicewp' ) . '</p>' . $error_string, 'error' );
	        slicewp_admin_notices()->display_notice( 'empty_fields_error' );

    	} else {

    		foreach ( slicewp_form_errors()->get_error_codes() as $error_code ) {

    			slicewp_admin_notices()->register_notice( $error_code, wpautop( slicewp_form_errors()->get_error_message( $error_code ) ), 'error' );
		        slicewp_admin_notices()->display_notice( $error_code );

    		}

    	}

        return;

    }


	// Verify for affiliate's existance
	$affiliate_id = absint( $_POST['affiliate_id'] );
	$affiliate 	  = slicewp_get_affiliate( $affiliate_id );

	if( is_null( $affiliate ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_not_exists', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_not_exists' );

		return;

	}

	// Verify for affiliate status
	if( empty( $_POST['status'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_missing', '<p>' . __( 'Please select the status of your new affiliate.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_missing' );

		return;

	}

	$statuses = slicewp_get_affiliate_available_statuses();

	// Verify if the affiliate status is valid
	if( ! in_array( $_POST['status'], array_keys( $statuses ) ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_status_invalid', '<p>' . __( 'The selected status in not allowed.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_status_invalid' );

		return;

	}

	// Verify if payout method exists.
	if ( ! empty( $_POST['payout_method'] ) ) {

		$payout_methods = slicewp_get_payout_methods();

		if ( ! in_array( sanitize_text_field( $_POST['payout_method'] ), array_keys( $payout_methods ) ) ) {

			slicewp_admin_notices()->register_notice( 'affiliate_payout_method_invalid', '<p>' . __( 'The selected payout method is not registered.', 'slicewp' ) . '</p>', 'error' );
			slicewp_admin_notices()->display_notice( 'affiliate_payout_method_invalid' );

			return;
			
		}

	}

	$_POST = stripslashes_deep( $_POST );

	// Prepare affiliate data to be updated
	$affiliate_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'payment_email'	=> ( ! empty( $_POST['payment_email'] ) ? sanitize_text_field( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' ),
		'status'		=> sanitize_text_field( $_POST['status'] )
	);

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $updated ) {

		slicewp_admin_notices()->register_notice( 'affiliate_update_false', '<p>' . __( 'Something went wrong. Could not update the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_update_false' );

		return;

	}

	// Update payout method.
	if ( isset( $_POST['payout_method'] ) ) {

		slicewp_update_affiliate_meta( $affiliate_id, 'payout_method', ( ! empty( $_POST['payout_method'] ) ) ? sanitize_text_field( $_POST['payout_method'] ) : '' );

	}

	// Redirect to the edit page of the affiliate with a success message
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_update_success', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_update_affiliate', 'slicewp_admin_action_update_affiliate', 50 );


/**
 * Validates and handles the deleting of an affiliate from the database.
 *
 */
function slicewp_admin_action_delete_affiliate() {

	// Verify for nonce.
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_delete_affiliate' ) )
		return;

	// Verify for affiliate ID.
	if ( empty( $_GET['affiliate_id'] ) )
		return;

	// Delete the affiliate.
	$deleted = slicewp_delete_affiliate( absint( $_GET['affiliate_id'] ) );

	if ( ! $deleted ) {

		slicewp_admin_notices()->register_notice( 'affiliate_delete_false', '<p>' . __( 'Something went wrong. Could not delete the affiliate. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_delete_false' );

		return;

	}

	// Redirect to the current page
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_message' => 'affiliate_delete_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_delete_affiliate', 'slicewp_admin_action_delete_affiliate', 50 );


/**
 * Validates and handles the review process of an affiliate
 *
 */
function slicewp_admin_action_review_affiliate() {

	// Verify for nonce
	if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_review_affiliate' ) )
		return;

	// Verify for affiliate ID
	if( empty( $_POST['affiliate_id'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_id_missing', '<p>' . __( 'Something went wrong. Could not update the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_id_missing' );

		return;

	}

	// Verify for affiliate's existance
	$affiliate_id = absint( $_POST['affiliate_id'] );
	$affiliate 	  = slicewp_get_affiliate( $affiliate_id );

	if( is_null( $affiliate ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_not_exists', '<p>' . __( 'Something went wrong. Could not update the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_not_exists' );

		return;

	}

	// Verify if Reject Reason is filled
    if ( isset( $_POST['slicewp_reject_affiliate'] ) && isset( $_POST['send_email_notification'] ) && empty( $_POST['affiliate_reject_reason'] ) ) {

        slicewp_admin_notices()->register_notice( 'affiliate_reject_reason', '<p>' . __( 'Please fill in the Reject Reason field.', 'slicewp' ) . '</p>', 'error' );
        slicewp_admin_notices()->display_notice( 'affiliate_reject_reason' );

        return;

	}
	
	// Prepare affiliate data to be updated
	$_POST = stripslashes_deep( $_POST );

	if ( isset( $_POST['slicewp_approve_affiliate'] ) ) {

		$affiliate_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'active'
		);

	} else {

		$affiliate_data = array(
			'date_modified' => slicewp_mysql_gmdate(),
			'status'		=> 'rejected'
		);

	}

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

	// If the affiliate could not be inserted show a message to the user
	if( ! $updated && isset( $_POST['slicewp_approve_affiliate'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_review_approve_false', '<p>' . __( 'Something went wrong. Could not Approve the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_review_approve_false' );

		return;

	}

	if( ! $updated && isset( $_POST['slicewp_reject_affiliate'] ) ) {

		slicewp_admin_notices()->register_notice( 'affiliate_review_reject_false', '<p>' . __( 'Something went wrong. Could not Reject the affiliate application. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_admin_notices()->display_notice( 'affiliate_review_reject_false' );

		return;

	}

	// Redirect to the edit page of the affiliate with a success message
	if( isset( $_POST['slicewp_approve_affiliate'] ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_review_approve_success' ), admin_url( 'admin.php' ) ) );
		exit;
	
	} else {

		wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_id' => $affiliate_id, 'slicewp_message' => 'affiliate_review_reject_success' ), admin_url( 'admin.php' ) ) );
		exit;
	
	}
	
}
add_action( 'slicewp_admin_action_review_affiliate', 'slicewp_admin_action_review_affiliate', 50 );


/**
 * Validates and handles the bulk deleting of affiliates from the database.
 * 
 */
function slicewp_admin_action_bulk_action_affiliates_delete() {

	if ( empty( $_REQUEST['action'] ) || $_REQUEST['action'] != 'delete' ) {
		return;
	}

	if ( empty( $_REQUEST['slicewp_token'] ) || ! wp_verify_nonce( $_REQUEST['slicewp_token'], 'slicewp_bulk_action_affiliates' ) ) {
		return;
	}

	if ( empty( $_REQUEST['affiliate_ids'] ) ) {
		return;
	}

	$affiliate_ids = array_map( 'absint', $_REQUEST['affiliate_ids'] );
	$deleted_ids  	= 0;

	foreach ( $affiliate_ids as $affiliate_id ) {

		// Delete affiliate.
		$deleted = slicewp_delete_affiliate( $affiliate_id );

		if ( $deleted ) {
			$deleted_ids++;
		}

	}

	// Redirect to the current page.
	wp_redirect( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_message' => 'bulk_action_affiliates_delete_success', 'updated' => absint( $deleted_ids ) ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'admin_init', 'slicewp_admin_action_bulk_action_affiliates_delete' );