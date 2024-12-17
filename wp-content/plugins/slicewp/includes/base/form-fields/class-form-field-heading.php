<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "heading"
 *
 */
class SliceWP_Form_Field_Heading extends SliceWP_Form_Field {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'heading';

	/**
	 * The heading's level.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $level = 3;

	/**
	 * The default value to be used if the value isn't set.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $default_value;

	/**
	 * The description that should appear for the field.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $description;


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 *
	 */
	protected function output_inner() {

		echo '<h' . absint( $this->level ) . '>' . esc_html( $this->default_value ) . '</h' . absint( $this->level ) . '>';

		$this->maybe_output_description();

	}

	/**
	 * Outputs the inner parts of the field, as needed on the admin's end.
	 *
	 */
	public function admin_output() {

		echo '<div class="slicewp-card-section-title">' . esc_html( $this->default_value ) . '</div>';

	}

}


/**
 * Registers the form field type "heading"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_heading( $field_types ) {

	$field_types['heading'] = array(
		'nicename' => __( 'Heading', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Heading'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_heading' );