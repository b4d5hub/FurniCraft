<?php
/**
 * Lost password form confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/lost-password-form-confirmation.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

slicewp_user_notices()->register_notice( 'lost_password_confirmation_email_sent', '<p>' . __( 'Password reset email has been sent.', 'slicewp' ) . '</p>', 'updated' );
slicewp_user_notices()->output_notice( 'lost_password_confirmation_email_sent' );

?>

<p><?php echo esc_html( apply_filters( 'slicewp_lost_password_confirmation_message', __( 'A password reset email has been sent to the email address you provided. Please note that it may take several minutes for the email to show up in your inbox.', 'slicewp' ) ) ); ?></p>