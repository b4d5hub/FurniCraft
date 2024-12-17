<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Processes the "integrations" step of the setup wizard.
 * 
 */
function slicewp_action_ajax_process_setup_wizard_step_integrations() {

    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_setup_wizard' ) ) {
		wp_die( 0 );
	}

    if ( ! empty( $_POST['form_data']['integrations'] ) ) {

		$_POST['form_data']['integrations'] = (array)$_POST['form_data']['integrations'];
        $_POST['form_data']['integrations'] = array_map( 'sanitize_text_field', $_POST['form_data']['integrations'] );

		$settings 	  = slicewp_get_option( 'settings', array() );
		$integrations = array();

		foreach ( slicewp()->integrations as $integration_slug => $integration ) {

			if ( in_array( $integration_slug, $_POST['form_data']['integrations'] ) ) {
                $integrations[] = $integration_slug;
            }

		}

		$settings['active_integrations'] = $integrations;

		slicewp_update_option( 'settings', $settings );

	}

    // Update the current step to the next one.
    slicewp_update_option( 'setup_wizard_current_step', 'setup' );

    wp_send_json_success();

}
add_action( 'wp_ajax_slicewp_action_ajax_process_setup_wizard_step_integrations', 'slicewp_action_ajax_process_setup_wizard_step_integrations' );


/**
 * Processes the "setup" step of the setup wizard.
 * 
 */
function slicewp_action_ajax_process_setup_wizard_step_setup() {

    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_setup_wizard' ) ) {
		wp_die( 0 );
	}

    // Get general settings.
	$settings = slicewp_get_option( 'settings', array() );

	// Set commission types.
	$commission_types = slicewp_get_available_commission_types();

	foreach ( $commission_types as $type => $details ) {

		if ( isset( $_POST['form_data']['commission_rate_' . $type] ) ) {
            $settings['commission_rate_' . $type] = sanitize_text_field( $_POST['form_data']['commission_rate_' . $type] );
        }

		if ( isset( $_POST['form_data']['commission_rate_type_' . $type] ) ) {
            $settings['commission_rate_type_' . $type] = sanitize_text_field( $_POST['form_data']['commission_rate_type_' . $type] );
        }

	}

	// Set currency.
	if ( isset( $_POST['form_data']['active_currency'] ) ) {

		// Set active currency.
		$settings['active_currency'] = sanitize_text_field( $_POST['form_data']['active_currency'] );

		// Set currency separators and symbol position.
		$thousands_separators = slicewp_get_currencies( 'thousands_separator' );
		$decimal_separators   = slicewp_get_currencies( 'decimal_separator' );
		$symbol_position 	  = slicewp_get_currencies( 'symbol_position' );

		$settings['currency_thousands_separator'] = ( ! empty( $thousands_separators[$settings['active_currency']] ) ? $thousands_separators[$settings['active_currency']] : ',' );
		$settings['currency_decimal_separator']   = ( ! empty( $decimal_separators[$settings['active_currency']] ) ? $decimal_separators[$settings['active_currency']] : '.' );
		$settings['currency_symbol_position']     = ( ! empty( $symbol_position[$settings['active_currency']] ) ? $symbol_position[$settings['active_currency']] : 'before' );

	}
	
	// Set cookie duration.
	if ( isset( $_POST['form_data']['cookie_duration'] ) ) {
        $settings['cookie_duration'] = absint( $_POST['form_data']['cookie_duration'] );
    }

	// Update general settings.
	slicewp_update_option( 'settings', $settings );

    // Update the current step to the next one.
    slicewp_update_option( 'setup_wizard_current_step', 'pages' );

    wp_send_json_success();

}
add_action( 'wp_ajax_slicewp_action_ajax_process_setup_wizard_step_setup', 'slicewp_action_ajax_process_setup_wizard_step_setup' );


/**
 * Processes the "pages" step of the setup wizard.
 * 
 */
function slicewp_action_ajax_process_setup_wizard_step_pages() {

    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_setup_wizard' ) ) {
		wp_die( 0 );
	}

    // Set the pages that can be created.
	$pages = array(
		'affiliate_account'  => array(
			'post_title'   => __( 'Affiliate Account', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_account]'
		),
		'affiliate_register' => array(
			'post_title'   => __( 'Affiliate Registration', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_registration]'
		),
		'affiliate_reset_password' => array(
			'post_title'   => __( 'Affiliate Reset Password', 'slicewp' ),
			'post_content' => '[slicewp_affiliate_reset_password]'
		)
	);

	global $wpdb;

	// Save the page ids
	$page_ids = array();

	foreach ( $pages as $page_slug => $page_data ) {

		// Continue if the admin did not select the page.
		if ( empty( $_POST['form_data']['page_' . $page_slug] ) ) {
            continue;
        }

		// Try to check if the page already exists.
		$shortcode = str_replace( array( '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ), '', $page_data['post_content'] );
		$page_id   = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1", '%' . $shortcode . '%' ) );

		// If page exists save the ID for later
		if ( ! is_null( $page_id ) ) {

			$page_ids[$page_slug] = absint( $page_id );

		// If the page doesn't exist, insert it
		} else {

			$page_array = array_merge( $page_data, array(
				'post_type'   => 'page',
				'post_status' => 'publish'
			));

			$page_ids[$page_slug] = wp_insert_post( $page_array );

		}

	}

	// If the affiliate register page exists, save it in the general settings.
	if ( ! empty( $_POST['form_data']['page_affiliate_register'] ) && ! empty( $page_ids['affiliate_register'] ) ) {

		$settings = slicewp_get_option( 'settings', array() );

		$settings['page_affiliate_register'] = $page_ids['affiliate_register'];

		slicewp_update_option( 'settings', $settings );

	}

	// If the affiliate account page exists, save it in the general settings.
	if ( ! empty( $_POST['form_data']['page_affiliate_account'] ) && ! empty( $page_ids['affiliate_account'] ) ) {

		$settings = slicewp_get_option( 'settings', array() );

		$settings['page_affiliate_account'] = $page_ids['affiliate_account'];

		slicewp_update_option( 'settings', $settings );

	}

	// If the reset password page exists, save it in the general settings.
	if ( ! empty( $_POST['form_data']['page_affiliate_reset_password'] ) && ! empty( $page_ids['affiliate_reset_password'] ) ) {

		$settings = slicewp_get_option( 'settings', array() );

		$settings['page_affiliate_reset_password'] = $page_ids['affiliate_reset_password'];

		slicewp_update_option( 'settings', $settings );

	}

    // Update the current step to the next one.
    slicewp_update_option( 'setup_wizard_current_step', 'emails' );
    
    wp_send_json_success();

}
add_action( 'wp_ajax_slicewp_action_ajax_process_setup_wizard_step_pages', 'slicewp_action_ajax_process_setup_wizard_step_pages' );


/**
 * Processes the "emails" step of the setup wizard.
 * 
 */
function slicewp_action_ajax_process_setup_wizard_step_emails() {

    if ( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_setup_wizard' ) ) {
		wp_die( 0 );
	}

    // Get general settings.
	$settings = slicewp_get_option( 'settings', array() );
	
	$email_notifications = slicewp_get_available_email_notifications();

	foreach ( $email_notifications as $email_notification_slug => $email_notification ) {

		if ( ! empty( $email_notification['sending'] ) && $email_notification['sending'] == 'manual' ) {
            continue;
        }

		if ( ! empty( $_POST['form_data'][$email_notification_slug] ) ) {

            $settings['email_notifications'][$email_notification_slug]['enabled'] = 1;

        } else {

            $settings['email_notifications'][$email_notification_slug]['enabled'] = '';

        }

	}

	// Update general settings.
	slicewp_update_option( 'settings', $settings );

    // Update the current step to the next one.
    slicewp_update_option( 'setup_wizard_current_step', 'finished' );
    
    wp_send_json_success();

}
add_action( 'wp_ajax_slicewp_action_ajax_process_setup_wizard_step_emails', 'slicewp_action_ajax_process_setup_wizard_step_emails' );