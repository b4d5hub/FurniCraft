<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Gravity Forms files
 *
 */
function slicewp_include_files_gfo() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main class
	if ( file_exists( $dir_path . 'class-integration-gravity-forms.php' ) )
		include $dir_path . 'class-integration-gravity-forms.php';

	// Include hooks functions
	if ( slicewp_is_integration_active( 'gfo' ) && slicewp_is_integration_plugin_active( 'gfo' ) ) {

		if ( file_exists( $dir_path . 'functions-hooks-integration-gravity-forms.php' ) )
			include $dir_path . 'functions-hooks-integration-gravity-forms.php';
		
	}

}
add_action( 'slicewp_include_files_late', 'slicewp_include_files_gfo' );


/**
 * Register the class that handles Gravity Forms related actions
 *
 * @param array $integrations
 *
 * @return array
 *
 */
function slicewp_register_integration_gfo( $integrations ) {

	$integrations['gfo'] = 'SliceWP_Integration_Gravity_Forms';

	return $integrations;

}
add_filter( 'slicewp_register_integration', 'slicewp_register_integration_gfo', 100 );


/**
 * Verifies if Gravity Forms is active
 *
 * @param bool $is_active
 *
 * @return bool
 *
 */
function slicewp_is_integration_plugin_active_gfo( $is_active = false ) {

	if ( class_exists( 'GFForms' ) )
		$is_active = true;
	
	return $is_active;

}
add_filter( 'slicewp_is_integration_plugin_active_gfo', 'slicewp_is_integration_plugin_active_gfo' );


/**
 * Returns the emails filled in a Gravity Forms entry
 *
 * @param int $entry
 * @param int $form
 *
 * @return array
 *
 */
function slicewp_get_form_entry_emails_gfo( $entry, $form ) {

	$email_fields = GFCommon::get_email_fields( $form );
	
	$emails = array();
	
	if ( ! empty( $email_fields ) ){
	
		foreach ( $email_fields as $email_field ) {
			if ( ! empty( $entry[ $email_field->id ] ) ) {
				$emails[] = $entry[ $email_field->id ];
			}
		}

	}

	return $emails;

}


/**
 * Returns the names filled in a Gravity Forms entry
 *
 * @param int $entry
 * @param int $form
 *
 * @return array
 *
 */
function slicewp_get_form_entry_names_gfo( $entry, $form ) {

	$customer_names = array();

	// Search for 'name' fields in the form
	foreach ( $form['fields'] as $field ) {

		if ( $field->type == 'name' ) {

			$customer_names[] = array(

				'first_name' => rgar( $entry, $field->id . '.3' ),	// .3 id is used by Gravity Forms for the First name
				'last_name'  => rgar( $entry, $field->id . '.6' )	// .6 id is used by Gravity Forms for the Last name
			
			);

		}

	}

	return $customer_names;
	
}