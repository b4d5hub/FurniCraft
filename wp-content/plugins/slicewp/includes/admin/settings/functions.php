<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Settings page.
 *
 */
function slicewp_include_files_admin_settings() {

	// Get legend admin dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include actions.
	if ( file_exists( $dir_path . 'functions-actions-settings.php' ) ) {
		include $dir_path . 'functions-actions-settings.php';
	}

	// Include submenu page.
	if ( file_exists( $dir_path . 'class-submenu-page-settings.php' ) ) {
		include $dir_path . 'class-submenu-page-settings.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_settings' );


/**
 * Register the Settings admin submenu page
 *
 */
function slicewp_register_submenu_page_settings( $submenu_pages ) {

	if ( ! is_array( $submenu_pages ) ) {
		return $submenu_pages;
	}

	$submenu_pages['settings'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Settings',
		'data' 		 => array(
			'page_title' => __( 'Settings', 'slicewp' ),
			'menu_title' => __( 'Settings', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_settings', 'manage_options' ),
			'menu_slug'  => 'slicewp-settings'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_settings', 70 );


/**
 * Returns all the email templates
 *
 * @return array
 * 
 */
function slicewp_get_email_templates() {

	$email_templates = array(
		'default' => array (
			'path' => SLICEWP_PLUGIN_DIR . 'includes/admin/settings/emails/templates/template-default.php',
			'name' => __( 'Default Template', 'slicewp' )
		)
	);

	/**
	 * Filter the email templates before returning them
	 *
	 * @param array $email_templates
	 *
	 */
	$email_templates = apply_filters( 'slicewp_register_email_templates', $email_templates );

	return $email_templates;

}

/**
 * The function will scan for email templates in current theme folder.
 * 
 * @param array $email_templates
 * 
 * @return array
 * 
 */
function slicewp_register_themes_email_templates( $email_templates ) {

	// Set the current theme directory.
	$theme_path 	  = get_template_directory() . '/slicewp-email-templates/';
	$child_theme_path = get_stylesheet_directory() . '/slicewp-email-templates/';

	$templates_paths = array_unique( array( $theme_path, $child_theme_path ) );

	foreach ( $templates_paths as $templates_path ) {

		if ( ! file_exists( $templates_path ) ) {
			continue;
		}

		// Scan the current theme directory for php files.
		$files = scandir( $templates_path );

		if ( empty( $files ) ) {
			continue;
		}

		foreach ( $files as $key => $file ) {

			if ( strpos( $file, '.php' ) == false ) {
				continue;
			}

			$template_data = implode( '', file( $templates_path . $file ) );

			// Check if the php file contains the SliceWP Email Template tag.
			if ( preg_match( '|SliceWP Email Template Name:(.*)$|mi', $template_data, $name ) ) {
				
				$name = array_map( 'trim', $name );

				// Save the template filename and the template name.
				if ( ! empty( $name[1] ) ) {

					$email_templates[ str_replace('.php', '', $file) ] = array(
						'path' => $templates_path . $file,
						'name' => $name[1]
					);

				}
				
			}

		}

	}

	return $email_templates;

}
add_filter( 'slicewp_register_email_templates', 'slicewp_register_themes_email_templates' );


/**
 * The function will return the requested SliceWP Email Template
 * 
 * @param string $email_template_slug
 * 
 * @return mixed array | null
 * 
 */
function slicewp_get_email_template( $email_template_slug ) {

	if ( empty( $email_template_slug ) ) {
		return null;
	}

	// Get all the email templates
	$email_templates = slicewp_get_email_templates();

	// Return the requested email template if exists
	if ( ! empty( $email_templates[$email_template_slug] ) ) {

		return $email_templates[$email_template_slug];
	
	}
	
	return null;

}