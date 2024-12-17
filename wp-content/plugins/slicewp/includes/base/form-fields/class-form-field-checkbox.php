<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "checkbox"
 *
 */
class SliceWP_Form_Field_Checkbox extends SliceWP_Form_Field {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'checkbox';

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
	 * @var    array
	 *
	 */
	protected $value;

	/**
	 * The default value to be used if the value isn't set.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $default_value;

	/**
	 * The array of options that populates the checkboxes.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $options = array();

	/**
	 * The description that should appear for the field.
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
	 * Initializer method that fires right aftr construct.
	 *
	 */
	protected function init() {

		// Make sure value and default value are of type array when empty
		$this->value 		 = ( ! is_null( $this->value ) && ! is_array( $this->value ) ? array() : $this->value );
		$this->default_value = ( ! is_null( $this->default_value ) && ! is_array( $this->default_value ) ? array() : $this->default_value );

	}


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 *
	 */
	protected function output_inner() {

		?>

			<div class="slicewp-field-label-wrapper">
				<label>

					<?php esc_html_e( $this->label ); ?>
					
					<?php if( $this->is_required ): ?>
						<span class="slicewp-field-required-marker">*</span>
					<?php endif; ?>

				</label>
			</div>

			<div class="slicewp-field-inner">

				<?php $this->maybe_output_description( 'before' ); ?>

				<ul class="slicewp-field-checkbox-wrapper">

					<?php foreach( $this->options as $option_value => $option_name ): ?>

						<li>
							<input
								type="checkbox" 
								id="<?php echo esc_attr( $this->get_formatted_id() . '-' . $option_value ); ?>" 
								name="<?php echo esc_attr( $this->name ); ?>[]" 
								value="<?php echo esc_attr( $option_value ) ?>" 
								<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
								<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?> 
								<?php echo esc_attr( ( in_array( $option_value, $this->get_display_value() ) ? 'checked' : '' ) ); ?> 
							/>
							<label for="<?php echo esc_attr( $this->get_formatted_id() . '-' . $option_value ); ?>" ><?php esc_html_e( $option_name ); ?></label>
						</li>

					<?php endforeach; ?>

				</ul>

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
			
			<ul class="slicewp-field-checkbox-wrapper">

				<?php foreach( $this->options as $option_value => $option_name ): ?>

					<li>
						<label for="<?php echo esc_attr( $this->get_formatted_id() . '-' . $option_value ); ?>" >
							<input
								type="checkbox" 
								id="<?php echo esc_attr( $this->get_formatted_id() . '-' . $option_value ); ?>" 
								name="<?php echo esc_attr( $this->name ); ?>[]" 
								value="<?php echo esc_attr( $option_value ) ?>" 
								<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
								<?php echo esc_attr( ( in_array( $option_value, $this->get_display_value() ) ? 'checked' : '' ) ); ?> 
							/>
							
							<?php esc_html_e( $option_name ); ?>
						</label>
					</li>

				<?php endforeach; ?>

			</ul>

		<?php

	}


	/**
	 * Sanitizes the given value.
	 *
	 * @param array
	 *
	 * @return array
	 *
	 */
	public function sanitize( $values ) {

		foreach( $values as $key => $value ) {

			$values[$key] = sanitize_text_field( $value );

		}

		return $values;

	}


	/**
	 * Returns the value that is displayed in the field. It firstly checks for the "value" attribute,
	 * but defaults to "default_value" if the first is missing.
	 *
	 * @return mixed
	 *
	 */
	protected function get_display_value() {

		return ( ! is_null( $this->value ) ? ( is_array( $this->value ) ? $this->value : array() ) : ( ! empty( $this->default_value ) ? $this->default_value : array() ) );

	}

}


/**
 * Registers the form field type "checkbox"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_checkbox( $field_types ) {

	$field_types['checkbox'] = array(
		'nicename' => __( 'Checkboxes', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Checkbox'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_checkbox' );