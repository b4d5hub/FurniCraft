<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Registers new affiliate account.
 *
 */
function slicewp_user_action_register_affiliate() {

    // Verify if affiliate registration is allowed.
    $allow_affiliate_registration = (bool)slicewp_get_setting( 'allow_affiliate_registration', false );

    /**
     * Filter the allow affiliate registration.
     * 
     * @param $allow_affiliate_registration
     * 
     */
    $allow_affiliate_registration = apply_filters( 'slicewp_allow_affiliate_registration', $allow_affiliate_registration );
    
    if ( empty( $allow_affiliate_registration ) ) {
        return;
    }
        
    // Verify for nonce.
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_register_affiliate' ) ) {
        return;
    }

    // Verify if the affiliate is already registered.
    if ( slicewp_is_user_affiliate() ) {
        return;
    }

    // Verify the honeypot.
    if ( empty( $_POST['slicewp_hnp'] ) || $_POST['slicewp_hnp'] != 't7i5s2g1d8n4h9y6xpv0' ) {
        return;
    }

    // Get all affiliate fields and build their objects
    $affiliate_fields = array();

    foreach ( slicewp_get_affiliate_fields() as $affiliate_field ) {

        $field = slicewp_create_form_field_object( $affiliate_field );

        if ( is_null( $field ) ) {
            continue;
        }

        $affiliate_fields[] = $field;

    }

    // Filter out from validation fields that aren't set for the affiliate registration form.
    foreach ( $affiliate_fields as $key => $field ) {

        $output_conditionals = $field->get('output_conditionals');

        if ( empty( $output_conditionals['form'] ) || ! in_array( 'affiliate_registration', $output_conditionals['form'] ) ) {
            unset( $affiliate_fields[$key] );
        }

    }

    // Filter out from validation WP_User specific fields.
    if ( is_user_logged_in() ) {

        $user = get_userdata( get_current_user_id() );

        foreach ( $affiliate_fields as $key => $field ) {

            if ( in_array( $field->get( 'name' ), array( 'user_login', 'user_email', 'password', 'password_confirm' ) ) ) {
                unset( $affiliate_fields[$key] );
            }

            if ( in_array( $field->get( 'name' ), array( 'first_name', 'last_name' ) ) && ! empty( $user->{$field->get( 'name' )} ) ) {
                unset( $affiliate_fields[$key] );
            }

        }

    }

    // Reset array values and keys.
    $affiliate_fields = array_values( $affiliate_fields );


    // Validate fields.
    foreach ( $affiliate_fields as $field ) {

        $field->validate( $_POST );

    }

    // For smooth user experience break the execution now and return any errors.
    if ( slicewp_form_errors()->has_errors() ) {

        slicewp_user_notices()->register_notice( 'empty_fields_error', '<p>' . __( 'Some information is missing or is invalid. Please review the registration form.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'empty_fields_error' );

        return;

    }


    // Continue with certain verifications needed for new users.
    if ( ! is_user_logged_in() ) {

        // Verify that passwords match.
        if ( in_array( 'password_confirm', array_column( array_map( 'slicewp_object_to_array', $affiliate_fields ), 'name' ) ) && $_POST['password'] != $_POST['password_confirm'] ) {

            slicewp_user_notices()->register_notice( 'password_confirm_error', '<p>' . __( 'The typed passwords do not match.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'password_confirm_error' );

            return;

        }

        // Verify if the Username is available.
        if ( in_array( 'user_login', array_column( array_map( 'slicewp_object_to_array', $affiliate_fields ), 'name' ) ) && username_exists( $_POST['user_login'] ) ) {

            slicewp_user_notices()->register_notice( 'user_login_exists_error', '<p>' . sprintf( __( 'The username <strong>%s</strong> is already registered.', 'slicewp' ) , esc_attr( $_POST['user_login'] ) ) . '</p>' . '<p>' . __( 'If you have a user account on our website, please log in first, and then proceed to register for the affiliate program.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'user_login_exists_error' );

            return;

        }

        // Verify if the email address is already used.
        if ( email_exists( $_POST['user_email'] ) ) {

            slicewp_user_notices()->register_notice( 'user_email_exists_error', '<p>' . sprintf( __( 'The email address <strong>%s</strong> is already registered.', 'slicewp' ), esc_attr( $_POST['user_email'] ) ) . '</p>' . '<p>' . __( 'If you have a user account on our website, please log in first, and then proceed to register for the affiliate program.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'user_email_exists_error' );

            return;

        }

    }

    // Verify for Terms and Conditions.
    $page_terms_conditions = slicewp_get_setting( 'page_terms_conditions' );

    if ( ! empty( $page_terms_conditions ) ) {

        if ( empty( $_POST['terms_conditions'] ) ) {

            slicewp_user_notices()->register_notice( 'terms_empty_error', '<p>' . __( 'You must agree with our Terms and Conditions.', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'terms_empty_error' );
        
            return;
    
        }

    }

    // Verify for reCAPTCHA
    $recaptcha = slicewp_get_setting( 'enable_recaptcha' );

    if ( ! empty( $recaptcha ) && ! slicewp_is_recaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'recaptcha_invalid_error', '<p>' . __( 'Please complete the reCAPTCHA.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'recaptcha_invalid_error' );
    
        return;

    }

    // Verify for Turnstile.
    $turnstile = slicewp_get_setting( 'enable_turnstile' );

    if ( ! empty( $turnstile ) && ! slicewp_is_turnstile_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'turnstile_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'turnstile_invalid_error' );
    
        return;

    }

    // Verify for hCaptcha.
    $hcaptcha = slicewp_get_setting( 'enable_hcaptcha' );

    if ( ! empty( $hcaptcha ) && ! slicewp_is_hcaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'hcaptcha_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'hcaptcha_invalid_error' );
    
        return;

    }


    if ( ! is_user_logged_in() ) {

        // Prepare user_login
        $user_login = ( in_array( 'user_login', array_column( array_map( 'slicewp_object_to_array', $affiliate_fields ), 'name' ) ) && ! empty( $_POST['user_login'] ) ? sanitize_user( $_POST['user_login'] ) : sanitize_email( $_POST['user_email'] ) );

        // Prepare user data to be inserted in db
        $userdata = array(
            'user_login'    => $user_login,
            'user_pass'     => trim( $_POST['password'] ),
            'user_email'    => sanitize_email( $_POST['user_email'] ),
            'first_name'    => ( ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '' ),
            'last_name'     => ( ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '' )
        );

        // Insert user data
        $user_id = wp_insert_user( $userdata );
        
        // Verify if user was inserted successfully
        if ( is_wp_error( $user_id ) ) {

            slicewp_user_notices()->register_notice( 'user_insert_error', '<p>' . __( 'User could not be created! Please try again later!', 'slicewp' ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'user_insert_error' );

            return;

        }

    } else {

        $user_id = get_current_user_id();
        $user    = get_userdata( $user_id );

        if ( empty( $user->first_name ) || empty( $user->last_name ) ) {

            wp_update_user( array(
                'ID'         => $user_id,
                'first_name' => ( ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : $user->first_name ),
                'last_name'  => ( ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : $user->last_name )
            ));

        }
    
    }

    // Verify the status to be used for the affiliate
    $affiliate_register_status_active = slicewp_get_setting( 'affiliate_register_status_active' );

    // Prepare affiliate data to be inserted in db
    $affiliate_data = array(
        'user_id' 		=> absint( $user_id ),
        'date_created'  => slicewp_mysql_gmdate(),
        'date_modified' => slicewp_mysql_gmdate(),
        'payment_email' => ( ! empty( $_POST['payment_email'] ) ? sanitize_email( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' ),
        'status'		=> ( ( $affiliate_register_status_active == 1 ) ? 'active' : 'pending' )
    );

    // Insert affiliate in db
    $affiliate_id = slicewp_insert_affiliate( $affiliate_data );
    
    // Verify if affiliate was inserted succesfully
    if ( empty( $affiliate_id ) ) {

        slicewp_user_notices()->register_notice( 'affiliate_insert_error', '<p>' . __( 'Affiliate account could not be created!', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'affiliate_insert_error' );

        return;
    
    }


    // Log in the user if everything is okay by this point.
    if ( isset( $user_login ) && ! is_user_logged_in() ) {

        // Prepare the credentials for login
        $credentials = array(
            'user_login'    => $user_login,
            'user_password' => trim( $_POST['password'] )
        );
        
        // Login the user.
        $user = wp_signon( $credentials, '' );
    
        // Set the current user.
        if ( ! is_wp_error( $user ) ) {
    
            wp_set_current_user( $user_id );
            
        }

    }


    /**
     * Executes right after the user and affiliate have been added to the database.
     *
     * @param int $affiliate_id
     *
     */
    do_action( 'slicewp_register_affiliate', $affiliate_id );
    

    // Redirect to the Affiliate Account Page
    if ( ! empty( $_POST['redirect_url'] ) ) {

        wp_redirect( $_POST['redirect_url'] );
        exit;
    
    }

	// Redirect to the Register Page with success message
    wp_redirect( add_query_arg( array( 'success' => 1 ) ) );
    exit;

}
add_action( 'slicewp_user_action_register_affiliate', 'slicewp_user_action_register_affiliate', 50 );


/**
 * Login for the affiliate
 *
 */
function slicewp_user_action_login_affiliate() {

    // Verify for nonce.
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_login_affiliate' ) ) {
        return;
    }

    // Verify for Login.
	if ( empty( $_POST['login'] ) ) {

        slicewp_user_notices()->register_notice( 'login_empty_error', '<p>' . __( 'Please fill in your Username / Email address.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'login_empty_error' );
    
        return;
    
    }

    // Verify for Password.
    if ( empty( $_POST['password'] ) ) {

        slicewp_user_notices()->register_notice( 'password_empty_error', '<p>' . __( 'Please fill in your Password.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'password_empty_error' );

        return;

    }

    // Verify for reCAPTCHA.
    $recaptcha = slicewp_get_setting( 'enable_recaptcha' );

    if ( ! empty( $recaptcha ) && ! slicewp_is_recaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'recaptcha_invalid_error', '<p>' . __( 'Please complete the reCAPTCHA.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'recaptcha_invalid_error' );
    
        return;

    }

    // Verify for Turnstile.
    $turnstile = slicewp_get_setting( 'enable_turnstile' );

    if ( ! empty( $turnstile ) && ! slicewp_is_turnstile_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'turnstile_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'turnstile_invalid_error' );
    
        return;

    }

    // Verify for hCaptcha.
    $hcaptcha = slicewp_get_setting( 'enable_hcaptcha' );

    if ( ! empty( $hcaptcha ) && ! slicewp_is_hcaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'hcaptcha_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'hcaptcha_invalid_error' );
    
        return;

    }

    // Verify if the field contains an email address.
    if ( is_email( $_POST['login'] ) ) {

        if ( ! email_exists( $_POST['login'] ) ) {

            slicewp_user_notices()->register_notice( 'email_not_registered_error', '<p>' . sprintf( __( 'Unable to login. Please try again.', 'slicewp' ), $_POST['login'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'email_not_registered_error' );

            return;

        }

    } else {
        
        if ( ! username_exists( $_POST['login'] ) ) {

            slicewp_user_notices()->register_notice( 'username_not_registered_error', '<p>' . sprintf( __( 'Unable to login. Please try again.', 'slicewp' ), $_POST['login'] ) . '</p>', 'error' );
            slicewp_user_notices()->display_notice( 'username_not_registered_error' );

            return;

        }

    }


    // Prepare the credentials for login.
    $credentials = array(
        'user_login'    => is_email( $_POST['login'] ) ? sanitize_email( $_POST['login'] ) : sanitize_user( $_POST['login'] ),
        'user_password' => trim( $_POST['password'] )
    );
    
    // Login the user.
    $user = wp_signon( $credentials, '' );

    if ( is_wp_error( $user ) ){
 
        slicewp_user_notices()->register_notice( 'affiliate_login_error', '<p>' . __( 'Unable to login. Please try again.' , 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'affiliate_login_error' );

        return;

    }
    
    // Redirect to the Affiliate Account Page.
    if ( ! empty( $_POST['redirect_url'] ) ) {

        wp_redirect( $_POST['redirect_url'] );
        exit;
    
    }
    
	// Redirect to the Login Page with success message.
    wp_redirect( add_query_arg( array( 'success' => 1 ) ) );
    exit;

}
add_action( 'slicewp_user_action_login_affiliate', 'slicewp_user_action_login_affiliate', 50 );


/**
 * Update the affiliate settings.
 *
 */
function slicewp_user_action_update_affiliate_settings() {

    // Verify for nonce
	if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_update_affiliate_settings' ) ) {
        return;
    }

    // Get all affiliate fields and build their objects
    $affiliate_fields = array();

    foreach ( slicewp_get_affiliate_fields() as $affiliate_field ) {

        $field = slicewp_create_form_field_object( $affiliate_field );

        if ( is_null( $field ) ) {
            continue;
        }

        $affiliate_fields[] = $field;

    }

    // Filter out from validation fields that aren't set for the affiliate registration form
    foreach ( $affiliate_fields as $key => $field ) {

        $output_conditionals = $field->get('output_conditionals');

        if ( empty( $output_conditionals['form'] ) || ! in_array( 'affiliate_account', $output_conditionals['form'] ) ) {
            unset( $affiliate_fields[$key] );
        }

    }

    // Filter out from validation WP_User specific fields
    foreach ( $affiliate_fields as $key => $field ) {

        if ( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm' ) ) ) {
            unset( $affiliate_fields[$key] );
        }

    }

    // Reset array values and keys
    $affiliate_fields = array_values( $affiliate_fields );


    // Validate fields
    foreach ( $affiliate_fields as $field ) {

        $field->validate( $_POST );

    }

    // Break the execution now and return any errors
    if ( slicewp_form_errors()->has_errors() ) {

        slicewp_user_notices()->register_notice( 'empty_fields_error', '<p>' . __( 'Some information is missing or is invalid. Please review the form.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'empty_fields_error' );

        return;

    }


    // Get the affiliate id
    $affiliate    = slicewp_get_affiliate_by_user_id( get_current_user_id() );
    $affiliate_id = $affiliate->get('id');

	// Prepare affiliate data to be updated
	$affiliate_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
        'payment_email'	=> ( ! empty( $_POST['payment_email'] ) ? sanitize_text_field( $_POST['payment_email'] ) : '' ),
        'website'       => ( ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : '' )
	);

	// Update affiliate into the database
	$updated = slicewp_update_affiliate( $affiliate_id, $affiliate_data );

    // If the affiliate could not be updated show a message to the user
	if ( ! $updated ) {

		slicewp_user_notices()->register_notice( 'affiliate_update_false', '<p>' . __( 'Something went wrong. Could not update the settings. Please try again.', 'slicewp' ) . '</p>', 'error' );
		slicewp_user_notices()->display_notice( 'affiliate_update_false' );

		return;

    }

    // Redirect to the Affiliate Account Page with success message
    wp_redirect( add_query_arg( array( 'slicewp-updated' => 1 ) ) );
    exit;
    
}
add_action( 'slicewp_user_action_update_affiliate_settings', 'slicewp_user_action_update_affiliate_settings', 50 );


/**
 * Sends a reset password link.
 *
 */
function slicewp_user_action_send_reset_password_email() {

    // Verify for nonce
    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_send_reset_password_email' ) ) {
        return;
    }

    if ( empty( $_POST['email'] ) ) {
        return;
    }

    // Verify for reCAPTCHA.
    $recaptcha = slicewp_get_setting( 'enable_recaptcha' );

    if ( ! empty( $recaptcha ) && ! slicewp_is_recaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'recaptcha_invalid_error', '<p>' . __( 'Please complete the reCAPTCHA.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'recaptcha_invalid_error' );
    
        return;

    }

    // Verify for Turnstile.
    $turnstile = slicewp_get_setting( 'enable_turnstile' );

    if ( ! empty( $turnstile ) && ! slicewp_is_turnstile_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'turnstile_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'turnstile_invalid_error' );
    
        return;

    }

    // Verify for hCaptcha.
    $hcaptcha = slicewp_get_setting( 'enable_hcaptcha' );

    if ( ! empty( $hcaptcha ) && ! slicewp_is_hcaptcha_valid( $_POST ) ) {

        slicewp_user_notices()->register_notice( 'hcaptcha_invalid_error', '<p>' . __( 'Could not validate your request, please try again.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'hcaptcha_invalid_error' );
    
        return;

    }

    // Verify for Login
    if ( ! is_email( $_POST['email'] ) ) {

        slicewp_user_notices()->register_notice( 'reset_password_email_invalid', '<p>' . __( 'The provided email address is invalid.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_email_invalid' );
    
        return;
    
    }

    // Verify for affiliate account
    $email_address = sanitize_email( $_POST['email'] );
    $affiliate     = slicewp_get_affiliate_by_user_email( sanitize_email( $_POST['email'] ) );

    if ( is_null( $affiliate ) ) {

        slicewp_user_notices()->register_notice( 'reset_password_affiliate_null', '<p>' . __( 'There is no affiliate account with the provided email address.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_affiliate_null' );
    
        return;

    }

    // Get user
    $user = get_userdata( $affiliate->get( 'user_id' ) );

    // Verify if WP allows for a user password reset
    $allow = apply_filters( 'allow_password_reset', true, $user->ID );

    if ( ! $allow ) {

        slicewp_user_notices()->register_notice( 'reset_password_not_allowed', '<p>' . __( 'Password reset is not allowed for this user.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_not_allowed' );

        return;

    } elseif ( is_wp_error( $allow ) ) {

        slicewp_user_notices()->register_notice( 'reset_password_not_allowed', '<p>' . $allow->get_error_message() . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_not_allowed' );

        return;

    }

    // Get password reset key.
    $key = get_password_reset_key( $user );

    // Send password reset email.
    $email_subject  = __( 'Password Reset Request', 'slicewp' );

    $email_content  = '<p>' . sprintf( __( 'Hi %s,', 'slicewp' ), esc_html( $user->get( 'first_name' ) ) ) . '</p>';
    $email_content .= '<p>' . sprintf( __( 'Someone has requested a new password for the account on %s', 'slicewp' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ) . '</p>';
    $email_content .= '<p>' . __( "If you didn't make this request, just ignore this email. If you'd like to proceed:", 'slicewp' ) . '</p>';
    $email_content .= '<a class="link" href="' . esc_url( add_query_arg( array( 'key' => $key, 'id' => $user->ID ), get_permalink( absint( slicewp_get_setting( 'page_affiliate_reset_password', 0 ) ) ) ) ) . '">' . __( 'Click here to reset your password', 'slicewp' ) . '</a>';

    slicewp_wp_email( $email_address, $email_subject, $email_content );

    // Redirect to confirmation page
    wp_redirect( add_query_arg( array( 'reset-link-sent' => 'true' ) ) );
    exit;

}
add_action( 'slicewp_user_action_send_reset_password_email', 'slicewp_user_action_send_reset_password_email' );


/**
 * Resets the user's password.
 *
 */
function slicewp_user_action_reset_user_password() {

    // Verify for nonce.
    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_reset_user_password' ) ) {
        return;
    }

    if ( empty( $_POST['new_password'] ) || empty( $_POST['new_password_confirm'] ) ) {
        return;
    }

    if ( empty( $_POST['reset_key'] ) || empty( $_POST['reset_user_id'] ) ) {
        return;
    }

    $user = get_userdata( absint( $_POST['reset_user_id'] ) );

    if ( false === $user ) {
        return;
    }

    // Check for password reset key.
    $user = check_password_reset_key( wp_unslash( $_POST['reset_key'] ), $user->user_login );

    if ( is_wp_error( $user ) ) {

        slicewp_user_notices()->register_notice( 'reset_password_key_invalid', '<p>' . __( 'This key is invalid or has already been used. Please reset your password again if needed.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'reset_password_key_invalid' );

        return;

    }

    // Check for passwords to match
    if ( $_POST['new_password'] !== $_POST['new_password_confirm'] ) {

        slicewp_user_notices()->register_notice( 'password_confirm_error', '<p>' . __( 'The provided passwords do not match.', 'slicewp' ) . '</p>', 'error' );
        slicewp_user_notices()->display_notice( 'password_confirm_error' );

        return;

    }


    /**
     * Fires before the user's password is reset.
     *
     * @param WP_User $user
     * @param string  $new_pass
     *
     */
    do_action( 'password_reset', $user, $_POST['new_password'] );

    // If all is good, change the password
    wp_set_password( $_POST['new_password'], $user->ID );

    // Add log
    slicewp_add_log( sprintf( 'Password changed for user: %s', $user->user_login ) );

    // Delete the password reset cookie
    $reset_password_cookie = 'wp-resetpass-' . COOKIEHASH;
    $reset_password_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

    setcookie( $reset_password_cookie, ' ', time() - YEAR_IN_SECONDS, $reset_password_path, COOKIE_DOMAIN, is_ssl(), true );

    // Redirect to account page
    wp_redirect( add_query_arg( array( 'password-reset' => 'true' ), get_permalink( absint( slicewp_get_setting( 'page_affiliate_account' ) ) ) ) );
    exit;

}
add_action( 'slicewp_user_action_reset_user_password', 'slicewp_user_action_reset_user_password' );