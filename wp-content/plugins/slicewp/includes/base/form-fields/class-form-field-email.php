<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "email"
 *
 */
class SliceWP_Form_Field_Email extends SliceWP_Form_Field_Text {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'email';


	/**
	 * Sanitizes the email value.
	 *
	 * Overwrites the default "sanitize_text_field" from parent class, with "sanitize_email".
	 *
	 * @param string
	 *
	 * @return string
	 *
	 */
	public function sanitize( $value ) {

		return sanitize_email( $value );

	}


	/**
	 * Validates the field against the given request data.
	 *
	 * If validation issues occur, errors will be added to the form errors.
	 *
	 * @param array $_request
	 *
	 * @return bool
	 *
	 */
	public function validate( $_request ) {

		// Check if default validation is passed
		$validate = parent::validate( $_request );

		if( false === $validate )
			return $validate;

		// If the value isn't set or it's an email address, validation passes
		if( empty( $_request[$this->name] ) || is_email( $_request[$this->name] ) )
			return true;

		// Add errors in case the field is not a valid email address
		slicewp_form_errors()->add( $this->name, __( 'The provided email address is not valid.', 'slicewp' ) );

		return false;

	}

}


/**
 * Registers the form field type "email"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_email( $field_types ) {

	$field_types['email'] = array(
		'nicename' => __( 'Email', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Email'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_email' );