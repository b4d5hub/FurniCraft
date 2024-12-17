<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the form fields
 *
 */
function slicewp_include_files_form_fields() {

	// Get dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include field classes
	if( file_exists( $dir_path . 'abstract-class-form-field.php' ) )
		include $dir_path . 'abstract-class-form-field.php';

	// Include "heading" field class
	if( file_exists( $dir_path . 'class-form-field-heading.php' ) )
		include $dir_path . 'class-form-field-heading.php';

	// Include "text" field class
	if( file_exists( $dir_path . 'class-form-field-text.php' ) )
		include $dir_path . 'class-form-field-text.php';

	// Include "email" field class
	if( file_exists( $dir_path . 'class-form-field-email.php' ) )
		include $dir_path . 'class-form-field-email.php';

	// Include "password" field class
	if( file_exists( $dir_path . 'class-form-field-password.php' ) )
		include $dir_path . 'class-form-field-password.php';

	// Include "url" field class
	if( file_exists( $dir_path . 'class-form-field-url.php' ) )
		include $dir_path . 'class-form-field-url.php';

	// Include "textarea" field class
	if( file_exists( $dir_path . 'class-form-field-textarea.php' ) )
		include $dir_path . 'class-form-field-textarea.php';

	// Include "select" field class
	if( file_exists( $dir_path . 'class-form-field-select.php' ) )
		include $dir_path . 'class-form-field-select.php';

	// Include "radio" field class
	if( file_exists( $dir_path . 'class-form-field-radio.php' ) )
		include $dir_path . 'class-form-field-radio.php';

	// Include "checkbox" field class
	if( file_exists( $dir_path . 'class-form-field-checkbox.php' ) )
		include $dir_path . 'class-form-field-checkbox.php';

	// Include "file" field class
	if( file_exists( $dir_path . 'class-form-field-file.php' ) )
		include $dir_path . 'class-form-field-file.php';

	// Include "country" field class
	if( file_exists( $dir_path . 'class-form-field-country.php' ) )
		include $dir_path . 'class-form-field-country.php';

	// Include "state" field class
	if( file_exists( $dir_path . 'class-form-field-state.php' ) )
		include $dir_path . 'class-form-field-state.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_form_fields' );


/**
 * Returns all registered form field types.
 *
 * @return array
 *
 */
function slicewp_get_form_field_types() {

	/**
	 * Filter to add extra form field types.
	 *
	 * @param array
	 *
	 */
	$field_types = apply_filters( 'slicewp_register_form_field_types', array() );

	return $field_types;

}


/**
 * Returns all registered affiliate fields.
 *
 * @return array
 *
 */
function slicewp_get_affiliate_fields() {

	$fields = apply_filters( 'slicewp_register_affiliate_fields', array() );

	return $fields;

}


/**
 * Registers the default affiliate fields data
 *
 * @param array $fields
 *
 * @return array
 *
 */
function slicewp_register_default_affiliate_fields( $fields ) {

	$fields[] = array(
		'type'  	  		  => 'text',
		'id'				  => 'slicewp-user-login',
		'name'				  => 'user_login',
		'label'				  => __( 'Username', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'text',
		'id'				  => 'slicewp-first-name',
		'name'				  => 'first_name',
		'label'				  => __( 'First Name', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'text',
		'id'				  => 'slicewp-last-name',
		'name'				  => 'last_name',
		'label'				  => __( 'Last Name', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'email',
		'id'				  => 'slicewp-user-email',
		'name'				  => 'user_email',
		'label'				  => __( 'Email', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'password',
		'id'				  => 'slicewp-password',
		'name'				  => 'password',
		'label'				  => __( 'Password', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'password',
		'id'				  => 'slicewp-password-confirm',
		'name'				  => 'password_confirm',
		'label'				  => __( 'Password Confirmation', 'slicewp' ),
		'is_required'		  => true,
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'email',
		'id'				  => 'slicewp-payment-email',
		'name'				  => 'payment_email',
		'label'				  => __( 'Payment Email', 'slicewp' ),
		'is_required'		  => ( slicewp_get_setting( 'required_field_payment_email', false ) ? true : false ),
		'output_conditionals' => array( 'form' => array( 'affiliate_registration', 'affiliate_account' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'url',
		'id'				  => 'slicewp-website',
		'name'				  => 'website',
		'label'				  => __( 'Website', 'slicewp' ),
		'is_required'		  => ( slicewp_get_setting( 'required_field_website', false ) ? true : false ),
		'output_conditionals' => array( 'form' => array( 'affiliate_registration', 'affiliate_account' ) )
	);

	$fields[] = array(
		'type'  	  		  => 'textarea',
		'id'				  => 'slicewp-promotional-methods',
		'name'				  => 'promotional_methods',
		'label'				  => __( 'How will you promote us?', 'slicewp' ),
		'is_required'		  => ( slicewp_get_setting( 'required_field_promotional_methods', false ) ? true : false ),
		'output_conditionals' => array( 'form' => array( 'affiliate_registration' ) )
	);

	return $fields;

}
add_filter( 'slicewp_register_affiliate_fields', 'slicewp_register_default_affiliate_fields' );


/**
 * Localizez our main JS script and adds the country and states data
 *
 */
function slicewp_output_js_var_country_select() {

	wp_localize_script( 'slicewp-script', 'slicewp_country_select', slicewp_get_country_states() );

}
add_action( 'wp_enqueue_scripts', 'slicewp_output_js_var_country_select', 100 );
add_action( 'admin_enqueue_scripts', 'slicewp_output_js_var_country_select', 100 );


/**
 * Acts like a factory for form field objects.
 *
 * Receives an array of data, builds the correct form field object and returns it, if it can.
 *
 * @param array
 *
 * @return null|object - the object differs based on the given form field type
 *
 */
function slicewp_create_form_field_object( $field_data ) {

	if( empty( $field_data['type'] ) )
		return null;

	$form_field_types = slicewp_get_form_field_types();

	if( empty( $form_field_types[$field_data['type']]['class'] ) )
		return null;

	if( ! class_exists( $form_field_types[$field_data['type']]['class'] ) )
		return null;

	return new $form_field_types[$field_data['type']]['class']( (object)$field_data );

}


/**
 * Outputs the affiliate fields in the respective forms on the user's end.
 *
 * @param string $form
 *
 */
function slicewp_output_form_fields_affiliate( $form ) {

	$affiliate_fields = slicewp_get_affiliate_fields();

	foreach ( $affiliate_fields as $affiliate_field ) {

		// Create a field object
		$field = slicewp_create_form_field_object( $affiliate_field );

		if ( is_null( $field ) )
			continue;

		// Check conditionals if we should output
		$output_conditionals = $field->get( 'output_conditionals' );

		if ( empty( $output_conditionals['form'] ) || ! in_array( $form, $output_conditionals['form'] ) )
			continue;

		// Make sure WP_User specific fields only show on "affiliate_registration" form and only when the user is logged out.
		if ( $form != 'affiliate_registration' && in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm' ) ) )
			continue;

		// WP_User specific fields on the "affiliate_registration" form.
		if ( $form == 'affiliate_registration' && is_user_logged_in() ) {

			if ( in_array( $field->get('name'), array( 'password', 'password_confirm' ) ) ) {
				continue;
			}

			if ( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name' ) ) ) {

				$user = get_userdata( get_current_user_id() );

				$field->set( 'default_value', $user->{$field->get( 'name' )});

				if ( ! empty( $user->{$field->get( 'name' )} ) ) {

					$field->set( 'is_disabled', true );

				}

			}

		}

		// Set field value for all fields, excluding password.
		if ( is_null( $field->get( 'value' ) ) ) {

			if ( $field->get('type') != 'password' ) {

				if ( ! empty( $_POST ) ) {
	
					if ( isset( $_POST[$field->get( 'name' )] ) ) {

						$field->set( 'value', $_POST[$field->get('name')] );

					}
	
				} else {
	
					$affiliate = slicewp_get_affiliate_by_user_id( get_current_user_id() );
	
					if ( property_exists( 'SliceWP_Affiliate', $field->get('name') ) ) {
	
						$field->set( 'value', ( ! is_null( $affiliate ) && ! is_null( $field->get('name') ) ? $affiliate->get( $field->get('name') ) : null ) );
	
					} else {
	
						$field->set( 'value', ( ! is_null( $affiliate ) && ! is_null( $field->get('name') ) ? slicewp_get_affiliate_meta( $affiliate->get('id'), $field->get('name'), true ) : null ) );
	
					}
	
				}
	
			}

		}
		
		// Output field
		$field->output();

	}

}
add_action( 'slicewp_form_fields', 'slicewp_output_form_fields_affiliate' );


/**
 * Outputs the affiliate fields in the respective forms on the admin side.
 *
 * @param string $form
 *
 */
function slicewp_output_admin_form_fields_affiliate( $form ) {

	if( ! in_array( $form, array( 'add_affiliate', 'edit_affiliate', 'review_affiliate' ) ) )
		return;

	$affiliate_id 	  = ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : 0 );
	$affiliate 		  = slicewp_get_affiliate( $affiliate_id );

	$affiliate_fields = slicewp_get_affiliate_fields();

	foreach ( $affiliate_fields as $affiliate_field ) {

		// Create a field object
		$field = slicewp_create_form_field_object( $affiliate_field );

		if( is_null( $field ) )
			continue;

		// Make sure WP_User specific fields are not taken into account.
		if( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm' ) ) )
			continue;

		// Fields that are not set to appear on register shouldn't be outputted on the review affiliate form.
		$output_conditionals = $field->get('output_conditionals');

		if( $form == 'review_affiliate' && ( empty( $output_conditionals['form'] ) || ! in_array( 'affiliate_registration', $output_conditionals['form'] ) ) )
			continue;

		// For the review affiliate form, the fields should be disabled and not required.
		// They are for presentation purposes only.
		if( $form == 'review_affiliate' ) {

			$field->set( 'is_required', false );
			$field->set( 'is_disabled', true );

		}

		// Set the field's value
		if( ! empty( $_POST ) && ! $field->get( 'is_disabled' ) ) {

			$field->set( 'value', ( isset( $_POST[$field->get('name')] ) ? $_POST[$field->get('name')] : '' ) );

		} else {

			if( property_exists( 'SliceWP_Affiliate', $field->get('name') ) ) {

				$field->set( 'value', ( ! is_null( $affiliate ) && ! is_null( $field->get('name') ) ? $affiliate->get( $field->get('name') ) : null ) );

			} else {

				$field->set( 'value', ( ! empty( $affiliate_id ) && ! is_null( $field->get('name') ) ? slicewp_get_affiliate_meta( $affiliate_id, $field->get('name'), true ) : null ) );

			}

		}

		// Output the field
		$field->admin_output();

	}

}
add_action( 'slicewp_admin_form_fields', 'slicewp_output_admin_form_fields_affiliate' );


/**
 * Adds the affiliate field values to the database as affiliate metadata.
 *
 * @param int $affiliate_id
 *
 */
function slicewp_save_affiliate_fields_metadata( $affiliate_id ) {

	if ( empty( $_POST ) )
		return;

	if ( empty( $_POST['slicewp_action'] ) )
		return;

	if ( ! slicewp_verify_request_action( $_POST['slicewp_action'] ) )
		return;

	// Get affiliate.
	$affiliate = slicewp_get_affiliate( $affiliate_id );

	// Get all affiliate fields.
	$affiliate_fields = slicewp_get_affiliate_fields();

	foreach( $affiliate_fields as $affiliate_field ) {

		// Create a field object.
		$field = slicewp_create_form_field_object( $affiliate_field );

		if ( is_null( $field ) )
			continue;

		if ( empty( $field->get('name') ) )
			continue;

		if ( ! isset( $_POST[$field->get('name')] ) )
			continue;

		// Don't save SliceWP_Affiliate and WP_User object attributes as affiliate metadata.
		if ( in_array( $field->get('name'), array( 'user_login', 'user_email', 'first_name', 'last_name', 'password', 'password_confirm', 'payment_email', 'website' ) ) )
			continue;

		// Don't save other metadata that is handled by custom actions
		if ( in_array( $field->get('name'), array( 'reject_reason' ) ) )
			continue;

		// Don't save metadata if the field isn't outputted when on front-end.
		$output_conditionals = $field->get('output_conditionals');

		if ( ! is_admin() && empty( $output_conditionals['form'] ) )
			continue;

		// Set the user ID for the "file" field type.
		if ( 'file' == $field->get( 'type' ) ) {

			$field->set( 'user_id', $affiliate->get( 'user_id' ) );

		}

		slicewp_update_affiliate_meta( $affiliate_id, sanitize_text_field( $field->get('name') ), ( ! empty( $_POST[$field->get('name')] ) ? $field->sanitize( $_POST[$field->get('name')] ) : '' ) );

	}

}
add_action( 'slicewp_insert_affiliate', 'slicewp_save_affiliate_fields_metadata' );
add_action( 'slicewp_update_affiliate', 'slicewp_save_affiliate_fields_metadata' );