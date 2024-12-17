<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "state"
 *
 */
class SliceWP_Form_Field_State extends SliceWP_Form_Field {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'state';

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
	 * The description that should for the field.
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
	 * The country code, to populate the states for
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $country;


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 *
	 */
	protected function output_inner() {

		$options = ( ! empty( $this->country ) ? slicewp_get_country_states( $this->country ) : null );

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

				<?php if( ! is_null( $options ) && ! empty( $options ) ): ?>

					<?php $options = array_merge( array( '' => __( 'Select...', 'slicewp' ) ), $options ); ?>

					<select
						id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
						name="<?php echo esc_attr( $this->name ); ?>" 
						data-value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
						<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
						<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
					>

					<?php foreach( $options as $option_value => $option_name ): ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $this->get_display_value(), $option_value ); ?>><?php esc_html_e( $option_name ); ?></option>
					<?php endforeach; ?>

					</select>

				<?php else: ?>

					<input
						type="<?php echo esc_attr( is_array( $options ) ? 'hidden' : 'text' ); ?>"
						id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
						name="<?php echo esc_attr( $this->name ); ?>" 
						value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
						data-value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
						data-required="<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?>" 
						<?php echo esc_attr( ( $this->is_required && is_array( $options ) ? 'required' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
						<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
					/>

				<?php endif; ?>

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

		$options = ( ! empty( $this->country ) ? slicewp_get_country_states( $this->country ) : null );

		?>

			<div class="slicewp-field-label-wrapper">
				<label for="<?php echo esc_attr( $this->get_formatted_id() ); ?>">
					<?php esc_html_e( $this->label ); ?>
					<?php if( $this->is_required ): ?>*<?php endif; ?>
				</label>
			</div>
			
			<?php if( ! is_null( $options ) && ! empty( $options ) ): ?>

				<?php $options = array_merge( array( '' => __( 'Select...', 'slicewp' ) ), $options ); ?>

				<select
					class="slicewp-select2" 
					id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
					name="<?php echo esc_attr( $this->name ); ?>" 
					data-value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
					<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?>
				>

				<?php foreach( $options as $option_value => $option_name ): ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $this->get_display_value(), $option_value ); ?>><?php esc_html_e( $option_name ); ?></option>
				<?php endforeach; ?>

				</select>

			<?php else: ?>

				<input
					type="<?php echo esc_attr( is_array( $options ) ? 'hidden' : 'text' ); ?>"
					id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
					name="<?php echo esc_attr( $this->name ); ?>" 
					value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
					data-value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
					data-required="<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?>" 
					<?php echo esc_attr( ( $this->is_required && is_array( $options ) ? 'required' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
					<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
					<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
				/>

			<?php endif; ?>

		<?php

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

		// Get affiliate fields
		$affiliate_fields = slicewp_get_affiliate_fields();

		// Set the internal pointer of the array to the current key
		while( key( $affiliate_fields ) !== array_search( $this->name, array_column( $affiliate_fields, 'name' ) ) ) next( $affiliate_fields );

		// Get previous field
		$previous_field = slicewp_create_form_field_object( prev( $affiliate_fields ) );

		// We need to make this validation only if the previous field is a "country" field
		if( ! is_null( $previous_field ) && $previous_field->get('type') == 'country' && ! empty( $_request[$previous_field->get('name')] ) ) {

			$country_states = slicewp_get_country_states( $_request[$previous_field->get('name')] );

			// If the states for the country is set to empty, it means we don't have any states.
			if( is_array( $country_states ) && empty( $country_states ) )
				$this->is_required = false;

		}

		return parent::validate( $_request );

	}

}


/**
 * Registers the form field type "state"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_state( $field_types ) {

	$field_types['state'] = array(
		'nicename' => __( 'State', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_State'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_state' );