<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Form Field of type "file"
 *
 */
class SliceWP_Form_Field_File extends SliceWP_Form_Field {

	/**
	 * The form field's type.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $type = 'file';

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
	 * The user ID that should be associated with the uploaded attachments.
	 * 
	 * @access protected
	 * @var    int
	 * 
	 */
	protected $user_id = 0;

	/**
	 * An array with the allowed mime types.
	 * Defaults to get_allowed_mime_types() if no mime types are provided.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $allowed_mime_types = array();

	/**
	 * The maximum file size allowed for upload, expressed in bytes.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $maximum_file_size = 0;

	/**
	 * The maximum number of files a user can upload. 0 means unlimited.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $maximum_file_count = 0;

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
	 * Whether the field should accept single or multiple files.
	 *
	 * @access protected
	 * @var    bool 
	 *
	 */
	protected $is_multiple = false;

	/**
	 * The array of classes to be applied to the field element.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $field_class = array();


	/**
	 * Initializer method that fires right after construct.
	 *
	 */
	protected function init() {

		$this->maximum_file_size  = ( ! empty( $this->maximum_file_size ) ? $this->maximum_file_size : wp_max_upload_size() );
		$this->maximum_file_count = ( ! empty( $this->maximum_file_count ) ? $this->maximum_file_count : 0 );

	}


	/**
	 * Sets the "value" attribute for the field.
	 *
	 * Overwrites the parent's setter because some data sanitization is needed.
	 *
	 * @param array $value
	 *
	 */
	protected function set_value( $value ) {

		$this->value = ( is_array( $value ) ? array_values( array_filter( $value ) ) : array() );

	}


	/**
	 * Outputs the inner parts of the field, as needed on the user's end.
	 *
	 */
	protected function output_inner() {

		/**
		 * @todo handle single file upload in affiliate account settings
		 *
		 */

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

				<input type="hidden" name="<?php echo esc_attr( $this->name ); ?>[]" value="" />

				<?php if ( $this->is_multiple ): ?>

					<div class="slicewp-field-drag-drop-area">

						<?php echo slicewp_get_svg( 'outline-cloud-upload' ); ?>

						<p><?php echo __( 'Drag and drop or click to browse files.', 'slicewp' ); ?></p>

						<input
							type="<?php echo esc_attr( $this->type ); ?>" 
							id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
							name="<?php echo esc_attr( $this->name ); ?>[]" 
							multiple 
							<?php echo esc_attr( $this->is_required && empty( $this->value ) ? 'required' : '' ); ?> 
							<?php echo ( ! empty( $this->allowed_mime_types ) ? 'accept="' . implode( ',', $this->allowed_mime_types ) . '"' : '' ); ?> 
							<?php echo ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ); ?>
						/>

					</div>

				<?php else: ?>

					<input
						<?php echo ( ! empty( $this->value ) ? 'style="display: none;"' : '' ); ?> 
						type="<?php echo esc_attr( $this->type ); ?>" 
						id="<?php echo esc_attr( $this->get_formatted_id() ); ?>" 
						name="<?php echo esc_attr( $this->name ); ?>[]" 
						<?php echo esc_attr( $this->is_required && empty( $this->value ) ? 'required' : '' ); ?> 
						<?php echo ( ! empty( $this->allowed_mime_types ) ? 'accept="' . implode( ',', $this->allowed_mime_types ) . '"' : '' ); ?> 
						<?php echo ( ( ! empty( $this->field_class ) ? 'class="' . esc_attr( implode( ' ', $this->field_class ) ) . '"' : '' ) ); ?>
					/>

				<?php endif; ?>

				<div class="slicewp-field-file-list">

					<?php if ( ! empty( $this->value ) ): ?>

						<?php foreach ( $this->value as $attachment_id ): ?>

							<?php

								$attachment_file_name = basename( get_attached_file( $attachment_id ) );

								if ( empty( $attachment_file_name ) ) {
									continue;
								}

							?>

							<div class="slicewp-field-file-item">

								<span class="slicewp-field-file-item-name"><a href="<?php echo esc_url( wp_get_attachment_url( $attachment_id ) ); ?>" download><?php echo $attachment_file_name; ?></a></span>
								<a href="#" class="slicewp-field-file-item-remove" title="<?php echo __( 'Remove file', 'slicewp' ); ?>"><?php echo slicewp_get_svg( 'outline-x' ); ?></a>

								<input type="hidden" name="<?php echo esc_attr( $this->name ); ?>[]" value="<?php echo absint( $attachment_id ); ?>" />

							</div>

						<?php endforeach; ?>

					<?php endif; ?>

				</div>

				<?php if ( ! empty( $this->allowed_mime_types ) ): ?>
					<p class="slicewp-field-rule slicewp-field-rule-allowed-file-types"><?php echo sprintf( __( 'Accepted file types: %s', 'slicewp' ), str_replace( '|', ', ', implode( ', ', array_keys( $this->allowed_mime_types ) ) ) ); ?></p>
				<?php endif; ?>

				<p class="slicewp-field-rule slicewp-field-rule-file-size"><?php echo sprintf( __( 'Maximum file size: %s', 'slicewp' ), size_format( $this->maximum_file_size ) ); ?></p>

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

			<input type="hidden" name="<?php echo esc_attr( $this->name ); ?>[]" value="" />
			
			<?php if ( ! empty( $this->value ) && is_array( $this->value ) ): ?>

				<?php foreach ( $this->value as $attachment_id ): ?>

					<?php

						$attachment_file_name = basename( get_attached_file( $attachment_id ) );

						if ( ! $attachment_file_name ) {
							continue;
						}

					?>

					<div class="slicewp-field-file-item">

						<input type="hidden" name="<?php echo esc_attr( $this->name ); ?>[]" value="<?php echo absint( $attachment_id ); ?>" />

						<a href="<?php echo esc_url( wp_get_attachment_url( $attachment_id ) ); ?>" download><?php echo esc_html( $attachment_file_name ); ?></a>

						<?php if ( ! $this->is_disabled ): ?>
							<a href="#" class="slicewp-field-file-item-remove" title="<?php echo __( 'Remove file', 'slicewp' ); ?>"><?php echo slicewp_get_svg( 'outline-x' ); ?></a>
						<?php endif; ?>

					</div>

				<?php endforeach; ?>

			<?php endif; ?>


			<div class="slicewp-field-notice" style="margin-bottom: 7px;">
				<?php if ( $this->is_multiple ): ?>
					<p><?php echo __( 'No files selected.', 'slicewp' ); ?></p>
				<?php else: ?>
					<p><?php echo __( 'No file selected.', 'slicewp' ); ?></p>
				<?php endif; ?>
			</div>


			<?php if ( ! $this->is_disabled ): ?>
				<a href="#" class="slicewp-button-secondary slicewp-field-file-add-items" style="display: none;" data-name="<?php echo esc_attr( $this->name ); ?>" data-multiple="<?php echo ( $this->is_multiple ? 'true' : 'false' ); ?>"><?php echo __( 'Add file', 'slicewp' ); ?></a>
			<?php endif; ?>

		<?php

	}


	/**
	 * Sanitizes the given value.
	 *
	 * Additionally, uploads the media files present in the $_FILES global.
	 *
	 * @param string $value
	 *
	 * @return string
	 *
	 */
	public function sanitize( $values ) {

		// Make sure the values variable is an array.
		$values = ( ! empty( $values ) && is_array( $values ) ? $values : array() );

		// Upload files and merge returned attachment IDs to the existing values.
		$values = array_merge( $values, $this->handle_files_upload() );

		// Filter out empty values.
		$values = array_filter( array_map( 'absint', $values ) );

		// Remove attachments that aren't linked to the original user that uploaded them or that are not admin created.
		foreach ( $values as $key => $attachment_id ) {

			if ( ! $this->is_attachment_valid_for_user( $attachment_id, $this->get( 'user_id' ) ) )
				unset( $values[$key] );

		}

		// If the field is a single file field, remove all items, but the first.
		if ( ! $this->is_multiple && ! empty( $values ) ) {

			$values = array_slice( $values, 0, 1 );

		}

		return array_values( $values );

	}


	/**
	 * Uploads files found in $_FILES and returns an array of attachment IDs.
	 *
	 * @return array
	 *
	 */
	protected function handle_files_upload() {

		// Include needed dependencies.
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$values = array();
		$files  = $this->prepare_files_for_upload( $_FILES );

		if ( ! empty( $files[$this->name] ) ) {

			foreach ( $files[$this->name] as $file ) {

				$attachment_id = media_handle_sideload( $file );

				if ( ! is_wp_error( $attachment_id ) ) {

					// Add the attachment ID to the array that is returned.
					$values[] = $attachment_id;

					// Link the current user to the attachment.
					$args = array(
						'ID' 		  => $attachment_id,
						'post_author' => $this->get( 'user_id' )
					);

					wp_update_post( $args );

				}

			}

		}

		return $values;

	}


	/**
	 * Filters out empty files and formats the returned value in preparation for media upload.
	 *
	 * @param array $raw_files
	 *
	 * @return array
	 *
	 */
	protected function prepare_files_for_upload( $raw_files ) {

		if ( empty( $raw_files ) )
			return array();

		$files = array();

		// Format files.
		foreach ( $raw_files[$this->name]['name'] as $key => $file_name ) {

			foreach ( $raw_files[$this->name] as $data_type => $files_data ) {

				$files[$this->name][$key][$data_type] = $files_data[$key];

			}

		}

		// Filter out empty file names.
		foreach ( $files[$this->name] as $key => $file ) {

			if ( empty( $file['name'] ) )
				unset( $files[$this->name][$key] );

		}

		$files[$this->name] = array_values( $files[$this->name] );

		return $files;

	}


	/**
	 * Verifies if the given user can process the request to update the given attachment.
	 *
	 * @param int $attachment_id
	 * @param int $user_id
	 *
	 * @return bool
	 *
	 */
	protected function is_attachment_valid_for_user( $attachment_id, $user_id ) {

		$attachment = get_post( $attachment_id );

		if ( is_null( $attachment ) )
			return true;

		if ( empty( $attachment->post_author ) )
			return true;

		// The attachment is valid for the user that uploaded it.
		if ( $attachment->post_author == $user_id )
			return true;

		// If the file was uploaded by an administrator, it's considered valid for all users.
		if ( user_can( $attachment->post_author, 'administrator' ) )
			return true;

		return false;

	}


	/**
	 * Attempts to retrieve the user ID from the current POST request.
	 *
	 * @return int
	 *
	 */
	protected function attempt_get_request_user_id() {

		// If the request is made for an affiliate, return the associated user ID.
		if ( ! empty( $_POST['affiliate_id'] ) ) {

			$affiliate = slicewp_get_affiliate( absint( $_POST['affiliate_id'] ) );

			if ( ! is_null( $affiliate ) )
				return $affiliate->get( 'user_id' );

		}

		// If there's a user ID specified, return it.
		if ( ! empty( $_POST['user_id'] ) )
			return absint( $_POST['user_id'] );

		// Default to the current user ID.
		return get_current_user_id();

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

		// Filter out the empty file that's added by the placeholder hidden field.
		$_request[$this->name] = array_values( array_filter( $_request[$this->name] ) );

		// Get the filtered files for upload.
		$files = $this->prepare_files_for_upload( $_FILES );

		// Check for files if the field is required.
		if ( $this->is_required && empty( $_request[$this->name] ) && empty( $files[$this->name] ) ) {

			slicewp_form_errors()->add( $this->name, __( 'This field is required.', 'slicewp' ) );
			return false;

		}

		// Check for file types match.
		$flawed_files = false;

		if ( ! empty( $files[$this->name] ) ) {

			foreach ( $files[$this->name] as $file_data ) {

				$filetype = wp_check_filetype( $file_data['name'], ( ! empty( $this->allowed_mime_types ) ? $this->allowed_mime_types : array_merge( $this->allowed_mime_types, get_allowed_mime_types() ) ) );

				if ( ! $filetype['ext'] ) {

					$flawed_files = true;
					break;

				}

			}

		}

		if ( $flawed_files ) {

			if ( $this->is_multiple )
				slicewp_form_errors()->add( $this->name, __( 'Some of the selected files do not match the allowed file extensions. Please select the files again.', 'slicewp' ) );
			else
				slicewp_form_errors()->add( $this->name, __( 'The selected file does not match the allowed file extensions. Please select the file again.', 'slicewp' ) );

			return false;

		}

		// Check for file size.
		$flawed_files = false;

		if ( ! empty( $files[$this->name] ) ) {

			foreach ( $files[$this->name] as $file_data ) {

				if ( $file_data['size'] > $this->maximum_file_size ) {

					$flawed_files = true;
					break;

				}

			}

		}

		if ( $flawed_files ) {

			if ( $this->is_multiple )
				slicewp_form_errors()->add( $this->name, __( 'Some of the selected files are too large. Please select smaller files.', 'slicewp' ) );
			else
				slicewp_form_errors()->add( $this->name, __( 'The selected file is too large. Please select a smaller file.', 'slicewp' ) );

			return false;

		}

		// Check for maximum file count for single file field.			
		if ( ! $this->is_multiple ) {

			$count_files 	   = ( ! empty( $files[$this->name] ) ? count( $files[$this->name] ) : 0 );
			$count_attachments = ( ! empty( $_request[$this->name][0] ) ? count( $_request[$this->name] ) : 0 );

			if ( $count_files > 1 || $count_attachments > 1 || ( ! empty( $count_attachments ) && ! empty( $count_files ) ) ) {

				slicewp_form_errors()->add( $this->name, __( 'You cannot upload more than one file for this field.', 'slicewp' ) );
				return false;

			}

		}

		// Check for maximum file count for multiple files field.	
		if ( ! empty( $this->maximum_file_count ) && $this->is_multiple && ! is_admin() ) {

			$count_files 	   = ( ! empty( $files[$this->name] ) ? count( $files[$this->name] ) : 0 );
			$count_attachments = ( ! empty( $_request[$this->name][0] ) ? count( $_request[$this->name] ) : 0 );

			if ( $count_files + $count_attachments > $this->maximum_file_count ) {

				slicewp_form_errors()->add( $this->name, sprintf( __( 'Too many files selected. Please add up to %d files.', 'slicewp' ), $this->maximum_file_count ) );
				return false;

			}

		}

		// Check if the current user can update the attachment.
		$flawed_attachments = false;

		if ( ! empty( $_request[$this->name] ) ) {

			foreach ( $_request[$this->name] as $attachment_id ) {

				if ( ! $this->is_attachment_valid_for_user( $attachment_id, $this->attempt_get_request_user_id() ) ) {

					// Get attachment details.
					$attachment 		  = get_post( $attachment_id );
					$attachment_file_name = basename( get_attached_file( $attachment_id ) );

					if ( is_admin() ) {

						// Get user objects for the user that is the attachment's author and the user from the request.
						$attachment_user = get_userdata( $attachment->post_author );
						$request_user    = get_userdata( $this->attempt_get_request_user_id() );

						$attachment_user_name = $attachment_user->first_name . ( ! empty( $attachment_user->last_name ) ? ' ' . $attachment_user->last_name : '' );
						$request_user_name    = $request_user->first_name . ( ! empty( $request_user->last_name ) ? ' ' . $request_user->last_name : '' );

						slicewp_form_errors()->add( $this->name, sprintf( __( 'File %s cannot be attached to user %s, as it is already attached to user %s.', 'slicewp' ), '<a target="_blank" href="' . esc_url( add_query_arg( array( 'item' => absint( $attachment_id ) ), admin_url( 'upload.php' ) ) ) . '">' . $attachment_file_name . '</a>', '<a target="_blank" href="' . esc_url( add_query_arg( array( 'user_id' => absint( $request_user->ID ) ), admin_url( 'user-edit.php' ) ) ) . '">' . $request_user_name . '</a>', '<a target="_blank" href="' . esc_url( add_query_arg( array( 'user_id' => absint( $attachment_user->ID ) ), admin_url( 'user-edit.php' ) ) ) . '">' . $attachment_user_name . '</a>' ) );

					}

					$flawed_attachments = true;

				}

			}

		}

		if ( $flawed_attachments ) {

			if ( ! $this->has_errors() ) {
				slicewp_form_errors()->add( $this->name, __( 'Invalid attachments.', 'slicewp' ) );
			}

			return false;

		}

		return true;

	}

}


/**
 * Registers the form field type "file"
 *
 * @param array
 *
 * @return array
 *
 */
function slicewp_register_form_field_type_file( $field_types ) {

	$field_types['file'] = array(
		'nicename' => __( 'File Upload', 'slicewp' ),
		'class'	   => 'SliceWP_Form_Field_File'
	);

	return $field_types;

}
add_action( 'slicewp_register_form_field_types', 'slicewp_register_form_field_type_file' );