<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field class with the common form fields attributes
 *
 */
abstract class SliceWP_Form_Field extends SliceWP_Base_Object {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = '';

	/**
	 * The array of classes to be applied to the field's <div> wrapper element.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $wrapper_class = array();

	/**
	 * An array containing conditionals that should be met to output the field.
	 * Conditional elements: - form: affiliate_registration/affiliate_account
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $output_conditionals = array();


	/**
	 * Constructor.
	 *
	 */
	public function __construct( $object ) {

		parent::__construct( $object );

		$this->init();

	}


	/**
	 * Initializer method that fires right after construct.
	 * Should be overwritten by child classes, if needed.
	 *
	 */
	protected function init() {}


	/**
	 * Outputs the field, as needed on the user's end.
	 *
	 */
	public function output() {

		?>

			<div class="<?php echo esc_attr( implode( ' ', array_merge( $this->get_default_wrapper_class(), $this->wrapper_class ) ) ); ?>" data-type="<?php echo esc_attr( $this->type ); ?>">

				<?php $this->output_inner(); ?>

			</div>

		<?php

	}


	/**
	 * Outputs the field, as needed on the admin's end.
	 *
	 */
	public function admin_output() {

		?>

			<div class="<?php echo esc_attr( implode( ' ', array_merge( array( 'slicewp-field-wrapper', 'slicewp-field-wrapper-inline' ), $this->wrapper_class ) ) ); ?>" data-type="<?php echo esc_attr( $this->type ); ?>">

				<?php $this->admin_output_inner(); ?>

			</div>

		<?php

	}


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 * Should be overwritten by child classes.
	 *
	 */
	protected function output_inner() {}


	/**
	 * Outputs the inner parts of the field, as needed on the admin's end.
	 * Should be overwritten by child classes.
	 *
	 */
	protected function admin_output_inner() {}


	/**
	 * Outputs the field's description, if all verifications are passed.
	 *
	 * @param string $description_placement
	 *
	 */
	protected function maybe_output_description( $description_placement = '' ) {

		if ( empty( $this->description ) )
			return;

		if ( ! empty( $description_placement ) && ! empty( $this->description_placement ) && $description_placement != $this->description_placement )
			return;

		?>

		<div class="slicewp-field-description">
			<?php echo wpautop( $this->description ); ?>
		</div>

		<?php

	}


	/**
	 * Outputs the field's first error, if the field has errrors.
	 *
	 */
	protected function maybe_output_error_message() {

		if ( ! $this->has_errors() )
			return;

		?>

		<div class="slicewp-field-error-message">
			<?php echo wpautop( $this->get_error_message() ); ?>
		</div>

		<?php

	}


	/**
	 * Returns an array with the default classes that are added to the field's wrapper element.
	 *
	 * @return array
	 *
	 */
	protected function get_default_wrapper_class() {

		$wrapper_class = array( 'slicewp-field-wrapper' );

		// Add error class
		if ( $this->has_errors() )
			$wrapper_class[] = 'slicewp-field-has-error';

		// Add description class
		if ( ! empty( $this->description ) )
			$wrapper_class[] = 'slicewp-field-has-description';

		// Add description placement class
		if ( ! empty( $this->description ) && ! empty( $this->description_placement ) )
			$wrapper_class[] = 'slicewp-field-description-placement-' . $this->description_placement;

		return $wrapper_class;

	}


	/**
	 * Sanitizes the given value. Defaults to "sanitize_text_field", but should be replaced
	 * by child classes if needed.
	 *
	 * @param mixed
	 *
	 * @return mixed
	 *
	 */
	public function sanitize( $value ) {

		return sanitize_text_field( $value );

	}


	/**
	 * Returns the "id" attribute in a nicely formatted way for CSS.
	 *
	 * @return string
	 *
	 */
	public function get_formatted_id() {

		$id = ( ! empty( $this->id ) ? $this->id : $this->name );

		return str_replace( '_', '-', $id );

	}


	/**
	 * Returns the value that is displayed in the field. It firstly checks for the "value" attribute,
	 * but defaults to "default_value" if the first is missing.
	 *
	 * @return mixed
	 *
	 */
	protected function get_display_value() {

		return ( ! is_null( $this->value ) ? $this->value : ( ! empty( $this->default_value ) ? $this->default_value : '' ) );

	}


	/**
	 * Verifies if the field has any validation errors.
	 *
	 * @return bool
	 *
	 */
	protected function has_errors() {

		if ( empty( $this->name ) )
			return false;

		$errors = slicewp_form_errors()->get_error_messages( $this->name );

		return ( ! empty( $errors ) );

	}


	/**
	 * Returns the first error message for the field, if there are any errors.
	 *
	 * @return string
	 *
	 */
	protected function get_error_message() {

		if ( empty( $this->name ) )
			return '';

		return slicewp_form_errors()->get_error_message( $this->name );

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

		// If the field isn't required, validation passes
		if ( empty( $this->is_required ) )
			return true;

		// If the field's value is set, validation passes
		if ( ! empty( $_request[$this->name] ) )
			return true;

		// Add errors in case the field is required and it has no value
		slicewp_form_errors()->add( $this->name, __( 'This field is required.', 'slicewp' ) );

		return false;

	}

}