<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Setup Wizard admin area.
 *
 */
function slicewp_include_files_admin_setup_wizard() {

	// Get setup wizard admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page.
	if ( file_exists( $dir_path . 'class-submenu-page-setup-wizard.php' ) ) {
		include $dir_path . 'class-submenu-page-setup-wizard.php';
	}

	// Include actions.
	if ( file_exists( $dir_path . 'functions-actions-setup-wizard.php' ) ) {
		include $dir_path . 'functions-actions-setup-wizard.php';
	}

	if ( file_exists( $dir_path . 'functions-actions-ajax-setup-wizard.php' ) ) {
		include $dir_path . 'functions-actions-ajax-setup-wizard.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_setup_wizard' );


/**
 * Register the Setup Wizard admin submenu page.
 *
 */
function slicewp_register_submenu_page_setup_wizard( $submenu_pages ) {

	if ( ! is_array( $submenu_pages ) ) {
		return $submenu_pages;
	}

	$hidden = get_option( 'slicewp_setup_wizard_hidden', false );

	if ( ! empty( $hidden ) ) {
		return $submenu_pages;
	}

	$submenu_pages['setup'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Setup_Wizard',
		'data' 		 => array(
			'page_title' => __( 'Setup Wizard', 'slicewp' ),
			'menu_title' => __( 'Setup Wizard', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_setup_wizard', 'manage_options' ),
			'menu_slug'  => 'slicewp-setup'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_setup_wizard', 10 );


/**
 * Adds the "slicewp-setup" class to the <body> when viewing the Setup Wizard subpage.
 * 
 */
function slicewp_admin_body_class_setup_wizard( $classes ) {

	if ( empty( $_GET['page'] ) || $_GET['page'] != 'slicewp-setup' ) {
		return $classes;
	}

	return $classes . ' slicewp-setup ';

}
add_filter( 'admin_body_class', 'slicewp_admin_body_class_setup_wizard' );