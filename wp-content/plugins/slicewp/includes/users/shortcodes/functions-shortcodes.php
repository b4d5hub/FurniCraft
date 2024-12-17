<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Affiliate registration
add_shortcode( 'slicewp_affiliate_registration', 'slicewp_shortcode_affiliate_registration' );

// Affiliate login
add_shortcode( 'slicewp_affiliate_login', 'slicewp_shortcode_affiliate_login' );

// Affiliate account
add_shortcode( 'slicewp_affiliate_account', 'slicewp_shortcode_affiliate_account' );

// Affiliate reset password
add_shortcode( 'slicewp_affiliate_reset_password', 'slicewp_shortcode_affiliate_reset_password' );

// Affiliate ID
add_shortcode( 'slicewp_affiliate_id', 'slicewp_shortcode_affiliate_id' );

// Affiliate referral URL
add_shortcode( 'slicewp_affiliate_url', 'slicewp_shortcode_affiliate_url' );

// Creative
add_shortcode( 'slicewp_creative', 'slicewp_shortcode_creative' );


/**
 * Generates the shortcode for affiliate registration.
 *
 */
function slicewp_shortcode_affiliate_registration( $atts ) {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    // Enqueue scripts and styles.
    wp_enqueue_script( 'slicewp-script' );
    wp_styles()->do_items( 'slicewp-style' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'redirect_url' => ''
    ), $atts );

    // Verify if affiliate registration is allowed
    $register_affiliate = slicewp_get_setting( 'allow_affiliate_registration' );
    
    if ( empty( $register_affiliate ) && current_user_can( 'administrator' ) ) {
        return '<p>' . __( "You see this message because you're an administrator of this website. The affiliate registrations are disabled!", 'slicewp' ) . '</p>' . '<p>' . sprintf( __( "You can enable public affiliate registration from the %sSliceWP settings page%s.", 'slicewp' ), '<a target="_blank" href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-settings' ), admin_url( 'admin.php' ) ) ) . '">', '</a>' ) . '</p>';
    }

    if ( empty( $register_affiliate ) ) {
        return '<p>' . __( 'Affiliate registration is currently disabled. Please contact the site owner for more details.', 'slicewp' ) . '</p>';
    }

    // Verify if the Affiliate Account Page is set in Settings
    if ( empty( $atts['redirect_url'] ) ) {

        $page_id = slicewp_get_setting( 'page_affiliate_account' , 0 );
        
        if ( ! empty( $page_id ) ) {

            $atts['redirect_url'] = get_permalink( $page_id );
        
        }

    }
    
    // Verify if the user is logged in after registration with success and show a notification
    if ( is_user_logged_in() && ! empty( $_GET['success'] ) ) {

        slicewp_user_notices()->register_notice( 'user_registered_success', '<p>' . __( 'Your account was registered successfully!', 'slicewp' ) . '</p>', 'updated' );

        return slicewp_user_notices()->output_notice( 'user_registered_success', true );

    }

    // Verify if the user is logged in
    if ( is_user_logged_in() ) {

        $user = wp_get_current_user();
        $affiliate = slicewp_get_affiliate_by_user_id( $user->ID );

    }

    // Show the registration form
    if ( empty( $affiliate ) ) {

        // Include the register template
        $dir_path = plugin_dir_path( __FILE__ );

        ob_start();

        if ( file_exists( $dir_path . 'templates/template-register.php' ) ) {
            include $dir_path . 'templates/template-register.php';
        }

        $return = ob_get_contents();

        ob_end_clean();

        // Show the registration form.
        return $return;

    } else {

        $affiliate_account_page_id = absint( slicewp_get_setting( 'page_affiliate_account' , 0 ) );

        slicewp_user_notices()->register_notice( 'user_already_registered', '<p>' . __( 'You are already registered!', 'slicewp' ) . ( ! empty( $affiliate_account_page_id ) ? ' ' . sprintf( __( 'View your account %shere%s.', 'slicewp' ), '<a href="' . esc_url( get_permalink( $affiliate_account_page_id ) ) . '">', '</a>' ) : '' ) . '</p>', 'warning' );

        return slicewp_user_notices()->output_notice( 'user_already_registered', true );
        
    }

}


/**
 * Generates the shortcode for affiliate login
 *
 */
function slicewp_shortcode_affiliate_login( $atts = '' ) {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    // Enqueue scripts and styles.
    wp_enqueue_script( 'slicewp-script' );
    wp_styles()->do_items( 'slicewp-style' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'redirect_url' => ''
    ), $atts );


    // Verify if the user was logged in with success message and display a notification
    if ( is_user_logged_in() && ! empty( $_GET['success'] ) ) {

        slicewp_user_notices()->register_notice( 'user_login_success', '<p>' . __( 'You are now logged in!', 'slicewp' ) . '</p>', 'updated' );
        
        return slicewp_user_notices()->output_notice( 'user_login_success', true );

    }

    // Verify if the user is already logged in
    if ( is_user_logged_in() ) {

        slicewp_user_notices()->register_notice( 'user_already_logged_in', '<p>' . __( 'You are already logged in!', 'slicewp' ) . ' ' . sprintf( __( '%1$sClick here if you wish to logout.%2$s', 'slicewp' ), '<a href="' . esc_url( wp_logout_url( slicewp_get_current_page_url() ) ) . '">', '</a>' ) . '</p>', 'warning' );

        return slicewp_user_notices()->output_notice( 'user_already_logged_in', true );

    }

    // Verify if the Affiliate Account Page is set in Settings
    if ( empty( $atts['redirect_url'] ) ) {

        $page_id = slicewp_get_setting( 'page_affiliate_account' , 0 );
        
        if ( ! empty( $page_id ) ) {

            $atts['redirect_url'] = get_permalink( $page_id );
        
        }

    }

    // Include the login template
    $dir_path = plugin_dir_path( __FILE__ );

    ob_start();

    if ( file_exists( $dir_path . 'templates/template-login.php' ) ) {

        include $dir_path . 'templates/template-login.php';

    }
   
    $return = ob_get_contents();

    ob_end_clean();

    // Show the login form
    return $return;

}


/**
 * Generates the shortcode for affiliate account.
 *
 */
function slicewp_shortcode_affiliate_account( $atts ) {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }
    
    /**
     * If the user isn't logged in, display the login form instead of the account.
     *
     */
    if ( ! is_user_logged_in() ) {

        return slicewp_shortcode_affiliate_login();

    }

    // Prepare the default attributes.
    $atts = shortcode_atts( array(
        'menu_position' => 'top'
    ), $atts );

    // Enqueue scripts and styles.
    wp_enqueue_script( 'slicewp-luxon' );
    wp_enqueue_script( 'slicewp-chart-js' );
    wp_enqueue_script( 'slicewp-chart-js-plugin-crosshair' );
    wp_enqueue_script( 'slicewp-chart-js-adapter-luxon' );
    wp_enqueue_script( 'slicewp-script' );
    wp_print_styles( 'slicewp-style' );

    // Verify if the user is affiliate.
    $affiliate = slicewp_get_affiliate_by_user_id( get_current_user_id() );

    // Include the login template.
    $dir_path = plugin_dir_path( __FILE__ );

    // Check if the Affiliate Registration is allowed.
    $register_affiliate = slicewp_get_setting( 'allow_affiliate_registration' );

    if ( empty( $affiliate ) && empty( $register_affiliate ) ) {

        slicewp_user_notices()->register_notice( 'user_not_affiliate', '<p>' . __( 'Your account does not have affiliate privileges!', 'slicewp' ) . '</p>', 'warning' );
        
        return slicewp_user_notices()->output_notice( 'user_not_affiliate', true );

    }

    ob_start();
    
    // Show the registration form if the user is registered but it's not affiliate.
    if ( empty( $affiliate ) ) {

        slicewp_user_notices()->register_notice( 'user_not_affiliate_warning', '<p>' . __( 'You are not enrolled in our affiliate program. Please fill out the form below to apply.', 'slicewp' ) . '</p>', 'warning' );
        slicewp_user_notices()->display_notice( 'user_not_affiliate_warning' );

        if ( file_exists( $dir_path . 'templates/template-register.php' ) ) {
            include $dir_path . 'templates/template-register.php';
        }

    } else {

        // Check the affiliate status and show the appropiate message
        if ( $affiliate->get('status') == 'pending' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_pending', '<p>' . __( 'Your affiliate account is currently being reviewed.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_pending' );
           
        }

        if ( $affiliate->get('status') == 'rejected' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_rejected', '<p>' . __( 'Your affiliate application has been rejected.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_rejected' );

        }

        if ( $affiliate->get('status') == 'inactive' ) {

            slicewp_user_notices()->register_notice( 'affiliate_account_inactive', '<p>' . __( 'Your affiliate account is not active.', 'slicewp' ) . '</p>', 'warning' );
            slicewp_user_notices()->output_notice( 'affiliate_account_inactive' );

        }

        if ( $affiliate->get('status') == 'active' ) {

            // Show the settings updated success message.
            if ( ! empty( $_GET['slicewp-updated'] ) ) {

                slicewp_user_notices()->register_notice( 'affiliate_settings_saved', '<p>' . __( 'Settings saved!', 'slicewp' ) . '</p>', 'updated' );
                slicewp_user_notices()->display_notice( 'affiliate_settings_saved' );

            }

            // Include the affiliate account template.
            slicewp_get_template_part( 'affiliate-area/affiliate-account', null, $atts );
        
        }

    }
    
    $return = ob_get_contents();

    ob_end_clean();

    return $return;

}


/**
 * Generates the shortcode for affiliate reset password.
 *
 */
function slicewp_shortcode_affiliate_reset_password() {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    // Enqueue scripts and styles.
    wp_styles()->do_items( 'slicewp-style' );

    ob_start();

    // Lost password form
    if ( empty( $_GET['reset-link-sent'] ) && empty( $_GET['show-reset-form'] ) ) {

        slicewp_get_template_part( 'affiliate-area/lost-password-form' );
        
    }

    // Lost password confirmation
    if ( ! empty( $_GET['reset-link-sent'] ) ) {

        slicewp_get_template_part( 'affiliate-area/lost-password-form-confirmation' );

    }

    // Reset password form
    if ( ! empty( $_GET['show-reset-form'] ) ) {

        if ( isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' ) ) {

            list( $user_id, $key ) = array_map( 'sanitize_text_field', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );

            // Check password reset key
            $user = get_userdata( absint( $user_id ) );
            $user = check_password_reset_key( $key, ( $user ? $user->user_login : '' ) );

            if ( ! is_wp_error( $user ) ) {

                slicewp_get_template_part( 'affiliate-area/reset-password-form', null, array( 'key' => $key, 'user_id' => $user_id ) );

            }

        }

    }

    return ob_get_clean();

}


/**
 * Generates the shortcode for creative
 *
 */
function slicewp_shortcode_creative( $atts ) {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    // Enqueue scripts and styles.
    wp_enqueue_script( 'slicewp-script' );
    wp_styles()->do_items( 'slicewp-style' );
    
    // Prepare the default attributes
    $atts = shortcode_atts( array(
        'id' => ''
    ), $atts );

    if ( empty( $atts ) ) {
        return;
    }

    // Verify if the user is logged in
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Verify if the user is affiliate
    if ( ! slicewp_is_user_affiliate() ) {
        return;
    }

    $creative = slicewp_get_creative( absint( $atts['id'] ) );
    
    // Verify if the creative exists
    if ( empty( $creative ) ) {
        return;
    }

    // Verify if the creative is active
    if ( $creative->get('status') == 'inactive' ) {
        return;
    }

    // Include the creative template
    $dir_path = plugin_dir_path( __FILE__ );

    ob_start();
    
    if ( file_exists( $dir_path . 'templates/template-creative.php' ) ) {

        include $dir_path . 'templates/template-creative.php';

    }
    
    // Show the Creative
    return ob_get_clean();

}


/**
 * Returns the currently logged-in affiliate's ID.
 * 
 */
function slicewp_shortcode_affiliate_id() {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $affiliate_id = slicewp_get_current_affiliate_id();

    return ( ! empty( $affiliate_id ) ? $affiliate_id : '' );

}


/**
 * Returns the affiliate URL.
 *
 */
function slicewp_shortcode_affiliate_url( $atts ) {

    if ( is_admin() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    // Prepare the default attributes.
    $atts = shortcode_atts( array(
        'affiliate_id' => slicewp_get_current_affiliate_id(),
        'url'          => ''
    ), $atts );

    // Get the affiliate's URL.
    $affiliate_url = slicewp_get_affiliate_url( absint( $atts['affiliate_id'] ), $atts['url'] );

    return ( ! is_null( $affiliate_url ) ? $affiliate_url : '' );

}