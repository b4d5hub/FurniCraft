<?php
/**
 * Reset password form.
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/reset-password-form.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<form id="slicewp-affiliate-reset-password-form" class="slicewp-form" action="" method="POST">

	<!-- Notices -->
	<?php do_action( 'slicewp_user_notices' ); ?>

	<p><?php echo apply_filters( 'slicewp_reset_password_message', __( 'Please enter a new password below.', 'slicewp' ) ); ?></p>

	<!-- New Password -->
	<div class="slicewp-field-wrapper" data-type="password">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-new-password"><?php echo __( 'New Password', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
		</div>
		
		<input id="slicewp-new-password" required name="new_password" type="password" value="" autocomplete="new-password" />

	</div>

	<!-- New Password Confirm -->
	<div class="slicewp-field-wrapper" data-type="password">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-new-password-confirm"><?php echo __( 'Re-enter new password', 'slicewp' ); ?> <span class="slicewp-field-required-marker">*</span></label>
		</div>
		
		<input id="slicewp-new-password-confirm" required name="new_password_confirm" type="password" value="" autocomplete="new-password" />

	</div>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_user_id" value="<?php echo absint( $args['user_id'] ); ?>" />

	<!-- Action and nonce -->
	<input type="hidden" name="slicewp_action" value="reset_user_password" />
	<?php wp_nonce_field( 'slicewp_reset_user_password', 'slicewp_token', false ); ?>

	<!-- Submit -->
	<button type="submit" class="slicewp-button-primary"><?php echo __( 'Save new password', 'slicewp' ); ?></button>

</form>