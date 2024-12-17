<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "password"
 *
 */
class SliceWP_Form_Field_Password extends SliceWP_Form_Field_Text {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'password';

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

				<div class="slicewp-field-input-password">
						
					<input
						type="<?php echo esc_attr( $this->type ); ?>" 
						id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
						name="<?php echo esc_attr( $this->name ); ?>" 
						value="<?php echo esc_attr( $this->get_display_value() ); ?>" 
						placeholder="<?php esc_attr_e( $this->placeholder ); ?>" 
						<?php echo esc_attr( ( $this->is_required ? 'required' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_readonly ? 'readonly' : '' ) ); ?> 
						<?php echo esc_attr( ( $this->is_disabled ? 'disabled' : '' ) ); ?> 
						<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
					/>

					<a href="#" class="slicewp-show-hide-password">
						<?php echo slicewp_get_svg( 'outline-eye' ); ?>
						<?php echo slicewp_get_svg( 'outline-eye-off' ); ?>
					</a>

				</div>

				<?php $this->maybe_output_description( 'after' ); ?>

				<?php $this->maybe_output_error_message(); ?>

			</div>

		<?php

	}

}


/**
 * Registers the form field type "password"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_password( $field_types ) {

	$field_types['password'] = array(
		'nicename' => __( 'Password', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_Password'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_password' );