<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<form id="slicewp-affiliate-login-form" class="slicewp-form" action="" method="POST">

	<!-- Notices -->
	<?php do_action( 'slicewp_user_notices' ); ?>

	<!-- Login -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-login"><?php echo __( 'Username / Email', 'slicewp' ); ?> *</label>
		</div>
		
		<div class="slicewp-field-inner">

			<input id="slicewp-user-login" name="login" type="text" value="<?php echo ( ! empty( $_POST['login'] ) ? esc_attr( $_POST['login'] ) : '' ); ?>" />

		</div>

	</div>

	<!-- Password -->
	<div class="slicewp-field-wrapper">

		<div class="slicewp-field-label-wrapper">
			<label for="slicewp-user-password"><?php echo __( 'Password', 'slicewp' ); ?> *</label>
		</div>
		
		<div class="slicewp-field-inner">

			<div class="slicewp-field-input-password">

				<input id="slicewp-user-password" name="password" type="password" value="" />
				
				<a href="#" class="slicewp-show-hide-password">
					<?php echo slicewp_get_svg( 'outline-eye' ); ?>
					<?php echo slicewp_get_svg( 'outline-eye-off' ); ?>
				</a>

			</div>

		</div>

	</div>

	<?php
	
		/**
		 * Add extra HTML to the bottom of the form.
		 * 
		 */
		do_action( 'slicewp_form_affiliate_login' );

	?>

	<!-- Action and nonce -->
	<input type="hidden" name="slicewp_action" value="login_affiliate" />
	<?php wp_nonce_field( 'slicewp_login_affiliate', 'slicewp_token', false ); ?>

	<!-- Redirect URL -->
	<input type="hidden" name="redirect_url" value="<?php echo ( ! empty($atts['redirect_url']) ? esc_url( $atts['redirect_url'] ) : '' ); ?>" />

	<!-- Submit -->
	<button type="submit" class="slicewp-button-primary"><?php echo __( 'Login', 'slicewp' ); ?></button>

	<!-- Lost Password -->
	<?php if ( ! empty( slicewp_get_setting( 'page_affiliate_reset_password' ) ) ): ?>
		<div class="slicewp-lost-password">
			<a href="<?php echo get_permalink( absint( slicewp_get_setting( 'page_affiliate_reset_password' ) ) ); ?>"><?php echo __( 'Lost your password?', 'slicewp' ); ?></a>
		</div>
	<?php endif; ?>
	
</form>