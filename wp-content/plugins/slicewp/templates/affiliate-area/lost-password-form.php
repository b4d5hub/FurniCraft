<?php
/**
 * Lost password form.
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/lost-password-form.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<form id="slicewp-affiliate-lost-password-form" class="slicewp-form" action="" method="POST">

	<!-- Notices -->
	<?php do_action( 'slicewp_user_notices' ); ?>

	<p><?php echo apply_filters( 'slicewp_lost_password_message', __( 'Lost your password? Please enter your email address. You will receive a link to create a new password via email.', 'slicewp' ) ); ?></p>

	<!-- Email -->
	<div class="slicewp-field-wrapper" data-type="email">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-email"><?php echo __( 'Email', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
		</div>
		
		<input id="slicewp-user-email" required name="email" type="email" value="<?php echo ( ! empty( $_POST['email'] ) ? esc_attr( $_POST['email'] ) : '' ); ?>" />

	</div>

	<?php
	
		/**
		 * Add extra HTML to the bottom of the form.
		 * 
		 */
		do_action( 'slicewp_form_affiliate_lost_password' );

	?>

	<!-- Action and nonce -->
	<input type="hidden" name="slicewp_action" value="send_reset_password_email" />
	<?php wp_nonce_field( 'slicewp_send_reset_password_email', 'slicewp_token', false ); ?>

	<!-- Submit -->
	<button type="submit" class="slicewp-button-primary"><?php echo __( 'Reset password', 'slicewp' ); ?></button>

</form>