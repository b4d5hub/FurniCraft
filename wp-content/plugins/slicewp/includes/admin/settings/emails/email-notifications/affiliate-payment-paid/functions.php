<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register the email notification sent to affiliates when a payment is paid.
 *
 * @param array $email_notifications
 *
 * @return array
 *
 */
function slicewp_email_notification_affiliate_payment_paid( $email_notifications = array() ) {

	// Prepare notification data.
	$notification = array(
		'name'			=> __( 'Payment Paid', 'slicewp' ),
		'description'	=> __( 'The affiliate will receive an email when a due payment is paid to them.', 'slicewp' ),
		'recipient'		=> 'affiliate',
		'merge_tags'  	=> array(),
	);

	// Add merge tags.
	$merge_tags = new SliceWP_Merge_Tags();

	foreach ( $merge_tags->get_tags() as $tag_slug => $tag_data ) {

		if ( empty( $tag_data['category'] ) || in_array( $tag_data['category'], array( 'affiliate', 'payment', 'general' ) ) ) {
			$notification['merge_tags'][] = $tag_slug;
		}

	}

    // Register notification.
    $email_notifications['affiliate_payment_paid'] = $notification;

	return $email_notifications;

}
add_filter( 'slicewp_available_email_notification', 'slicewp_email_notification_affiliate_payment_paid', 55 );


/**
 * Send an email notification to the affiliate when a payment is paid.
 *
 * @param int	$payment_id
 * @param array	$payment_data
 *
 */
function slicewp_send_email_notification_affiliate_payment_paid( $payment_id = 0, $payment_data = array() ) {

	// Verify received arguments not to be empty.
	if ( empty( $payment_id ) ) {
		return;
	}

	if ( empty( $payment_data['status'] ) ) {
		return;
	}

	// Check that the payment is "paid".
	if ( $payment_data['status'] != 'paid' ) {
		return;
	}

	// Verify if Email Notification sending is Enabled.
	$notification_settings = slicewp_get_email_notification_settings( 'affiliate_payment_paid' );

	if ( empty( $notification_settings['enabled'] ) ) {
		return;
	}

	// Verify if the Email Notification Subject and Content are filled in.
	if ( empty( $notification_settings['subject'] ) || empty( $notification_settings['content'] ) ) {
		return;
	}

    // Check if this email notification has already been sent.
    $sent_email_notifications = array_values( array_filter( (array)slicewp_get_payment_meta( $payment_id, '_sent_email_notifications', true ) ) );

    if ( in_array( 'affiliate_payment_paid', $sent_email_notifications ) ) {
        return;
    }

    // Get the payment.
	$payment = slicewp_get_payment( $payment_id );
	
	// Get the affiliate email address.
	$affiliate = slicewp_get_affiliate( absint( $payment->get( 'affiliate_id' ) ) );
	$user      = get_user_by( 'id', $affiliate->get('user_id') );

	if ( empty( $user->user_email ) ) {
		return;
	}

	// Prepare the email content.
	$email_subject = ( ! empty( $notification_settings['subject'] ) ? sanitize_text_field( $notification_settings['subject'] ) : '' );
	$email_content = ( ! empty( $notification_settings['content'] ) ? $notification_settings['content'] : '' );

	// Replace the tags with data.
	$merge_tags = new SliceWP_Merge_Tags();
	$merge_tags->set_data( 'affiliate', $affiliate );
	$merge_tags->set_data( 'payment', $payment );

	$email_subject = $merge_tags->replace_tags( $email_subject );
	$email_content = $merge_tags->replace_tags( $email_content );

	slicewp_wp_email( $user->user_email, $email_subject, $email_content );

    // Mark email notification as sent.
    $sent_email_notifications[] = 'affiliate_payment_paid';

    slicewp_update_payment_meta( $payment_id, '_sent_email_notifications', $sent_email_notifications );

}
add_action( 'slicewp_update_payment', 'slicewp_send_email_notification_affiliate_payment_paid', 20, 2 );