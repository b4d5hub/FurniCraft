<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "url"
 *
 */
class SliceWP_Form_Field_Url extends SliceWP_Form_Field_Text {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'url';


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
		if( empty( $_request[$this->name] ) || filter_var( $_request[$this->name], FILTER_VALIDATE_URL ) !== false )
			return true;

		// Add errors in case the field is not a valid URL
		slicewp_form_errors()->add( $this->name, __( 'The provided URL is not valid.', 'slicewp' ) );

		return false;

	}

}


/**
 * Registers the form field type "url"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_url( $field_types ) {

	$field_types['url'] = array(
		'nicename' => __( 'Website', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Url'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_url' );