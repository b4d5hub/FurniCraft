<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Outputs the enabled captcha widgets.
 * 
 */
function slicewp_output_captcha() {

    // reCAPTCHA
	$recaptcha = slicewp_get_setting( 'enable_recaptcha' );

	if ( ! empty( $recaptcha ) ):

        ?>

		<div class="slicewp-field-wrapper slicewp-field-wrapper-recaptcha">

			<?php wp_enqueue_script( 'slicewp-recaptcha-async-defer', 'https://www.google.com/recaptcha/api.js' ); ?>
			<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( slicewp_get_setting( 'recaptcha_site_key' ) ); ?>"></div>
			<input type="hidden" name="g-recaptcha-remoteip" value="<?php echo esc_attr( slicewp_get_user_ip_address() ); ?>" />

		</div>

        <?php

	endif;

	// Turnstile
	$turnstile = slicewp_get_setting( 'enable_turnstile' );

	if ( ! empty( $turnstile ) ):

        ?>

		<div class="slicewp-field-wrapper slicewp-field-wrapper-turnstile">

			<?php wp_enqueue_script( 'slicewp-turnstile-async-defer', 'https://challenges.cloudflare.com/turnstile/v0/api.js' ); ?>
			<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( slicewp_get_setting( 'turnstile_site_key' ) ); ?>" data-theme="light"></div>

		</div>

        <?php

	endif;

	// hCaptcha
	$hcaptcha = slicewp_get_setting( 'enable_hcaptcha' );

	if ( ! empty( $hcaptcha ) ):

        ?>

		<div class="slicewp-field-wrapper slicewp-field-wrapper-hcaptcha">

			<?php wp_enqueue_script( 'slicewp-hcaptcha-async-defer', 'https://js.hcaptcha.com/1/api.js' ); ?>
			<div class="h-captcha" data-sitekey="<?php echo esc_attr( slicewp_get_setting( 'hcaptcha_site_key' ) ); ?>" data-theme="light"></div>

		</div>

        <?php

	endif;

}
add_action( 'slicewp_form_affiliate_registration', 'slicewp_output_captcha' );
add_action( 'slicewp_form_affiliate_login', 'slicewp_output_captcha' );
add_action( 'slicewp_form_affiliate_lost_password', 'slicewp_output_captcha' );


/**
 * Verifies if the provided reCAPTCHA response is valid.
 *
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_is_recaptcha_valid( $data ) {

	$site_key   = slicewp_get_setting( 'recaptcha_site_key' );
	$secret_key = slicewp_get_setting( 'recaptcha_secret_key' );

	if ( empty( $site_key ) || empty( $secret_key ) ) {
		return false;
	}

	if ( empty( $data['g-recaptcha-response'] ) || empty( $data['g-recaptcha-remoteip'] ) ) {
		return false;
	}

	// Send post to verify the response with Google
	$response = wp_safe_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
		'timeout' => 30,
		'body'    => array(
			'secret'   => $secret_key,
			'response' => $data['g-recaptcha-response'],
			'remoteip' => $data['g-recaptcha-remoteip']
		)
	));

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! isset( $body['success'] ) || ! $body['success'] ) {
		return false;
	}

	return true;

}


/**
 * Verifies if the provided Turnstile response is valid.
 *
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_is_turnstile_valid( $data ) {

	$site_key   = slicewp_get_setting( 'turnstile_site_key' );
	$secret_key = slicewp_get_setting( 'turnstile_secret_key' );

	if ( empty( $site_key ) || empty( $secret_key ) ) {
		return false;
	}

	if ( empty( $data['cf-turnstile-response'] ) ) {
		return false;
	}

	// Send post to verify the response with CloudFlare.
	$response = wp_safe_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
		'timeout' => 30,
		'body'    => array(
			'secret'   => $secret_key,
			'response' => $data['cf-turnstile-response']
		)
	));

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! isset( $body['success'] ) || ! $body['success'] ) {
		return false;
	}

	return true;

}


/**
 * Verifies if the provided hCaptcha response is valid.
 *
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_is_hcaptcha_valid( $data ) {

	$site_key   = slicewp_get_setting( 'hcaptcha_site_key' );
	$secret_key = slicewp_get_setting( 'hcaptcha_secret_key' );

	if ( empty( $site_key ) || empty( $secret_key ) ) {
		return false;
	}

	if ( empty( $data['h-captcha-response'] ) ) {
		return false;
	}

	// Send post to verify the response with CloudFlare.
	$response = wp_safe_remote_post( 'https://hcaptcha.com/siteverify', array(
		'timeout' => 30,
		'body'    => array(
			'secret'   => $secret_key,
			'response' => $data['h-captcha-response']
		)
	));

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! isset( $body['success'] ) || ! $body['success'] ) {
		return false;
	}

	return true;

}