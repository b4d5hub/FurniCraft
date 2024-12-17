<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "textarea"
 *
 */
class SliceWP_Form_Field_Textarea extends SliceWP_Form_Field {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'textarea';

	/**
	 * The field's HTML "id" attribute
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $id;

	/**
	 * The "name" attribute of the field.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name;

	/**
	 * The text that populates the field's <label> element.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $label = '';

	/**
	 * The value of the field.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $value;

	/**
	 * The default value to be used if the value isn't set.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $default_value;

	/**
	 * The value to be added to as the placeholder.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $placeholder;

	/**
	 * The description that should appear under the field.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $description;

	/**
	 * Where the description should be outputed in correlation with the field.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $description_placement = 'before';

	/**
	 * Whether the field is required or not.
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $is_required = false;

	/**
	 * Whether the field is readonly or not.
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $is_readonly = false;

	/**
	 * Whether the field is disabled or not.
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $is_disabled = false;

	/**
	 * The array of classes to be applied to the field element.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $field_class = array();


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 *
	 */
	protected function output_inner() {

		?>

			<div class="slicewp-field-label-wrapper">
				<label for="<?php echo esc_attr( $this->get_formatted_id() ); ?>">

					<?php esc_html_e( $this->label ); ?>
					
					<?php if( $this->is_required ): ?>
						<span class="slicewp-field-required-marker">*</span>
					<?php endif; ?>

				</label>
			</div>

			<div class="slicewp-field-inner">

				<?php $this->maybe_output_description( 'before' ); ?>

				<textarea
					id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
					name="<?php echo esc_attr( $this->name ); ?>" 
					placeholder="<?php esc_attr_e( $this->placeholder ); ?>" 
					<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
					<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
				><?php echo esc_textarea( $this->get_display_value() ); ?></textarea>

				<?php $this->maybe_output_description( 'after' ); ?>

				<?php $this->maybe_output_error_message(); ?>

			</div>

		<?php

	}

	/**
	 * Outputs the inner parts of the field, as needed on the admin's end.
	 *
	 */
	protected function admin_output_inner() {

		?>

			<div class="slicewp-field-label-wrapper">
				<label for="<?php echo esc_attr( $this->get_formatted_id() ); ?>">
					<?php esc_html_e( $this->label ); ?>
					<?php if( $this->is_required ): ?>*<?php endif; ?>
				</label>
			</div>
			
			<textarea
				id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
				name="<?php echo esc_attr( $this->name ); ?>" 
				<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?> 
				<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
				<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
			><?php echo esc_textarea( $this->get_display_value() ); ?></textarea>

		<?php

	}


	/**
	 * Sanitizes the given value.
	 *
	 * @param string $value
	 *
	 * @return string
	 *
	 */
	public function sanitize( $value ) {

		return sanitize_textarea_field( $value );

	}

}


/**
 * Registers the form field type "textarea"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_textarea( $field_types ) {

	$field_types['textarea'] = array(
		'nicename' => __( 'Paragraph Text', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Textarea'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_textarea' );