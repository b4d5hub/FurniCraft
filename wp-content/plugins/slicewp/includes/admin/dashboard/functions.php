<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Dashboard admin area
 *
 */
function slicewp_include_files_admin_dashboard() {

	// Get dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page.
	if ( file_exists( $dir_path . 'class-submenu-page-dashboard.php' ) )
		include $dir_path . 'class-submenu-page-dashboard.php';

	// Include all dashboard cards.
	$cards = scandir( $dir_path . 'cards' );

	foreach ( $cards as $card ) {

		if ( false === strpos( $card, '.php' ) )
			continue;

		include $dir_path . 'cards/' . $card;

	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_dashboard' );


/**
 * Register the Dashboard admin submenu page
 *
 */
function slicewp_register_submenu_page_dashboard( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['dashboard'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Dashboard',
		'data' 		 => array(
			'page_title' => __( 'Dashboard', 'slicewp' ),
			'menu_title' => __( 'Dashboard', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_dashboard', 'manage_options' ),
			'menu_slug'  => 'slicewp-dashboard'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_dashboard', 15 );


/**
 * Initializez the dashboard cards.
 *
 */
function slicewp_initialize_dashboard_cards() {

	// Built-in cards.
	$card_classes = array(
		'totals'			 => 'SliceWP_Admin_Dashboard_Card_Totals',
		'help'				 => 'SliceWP_Admin_Dashboard_Card_Help',
		'latest_affiliates'  => 'SliceWP_Admin_Dashboard_Card_Latest_Affiliates',
		'latest_commissions' => 'SliceWP_Admin_Dashboard_Card_Latest_Commissions',
		'latest_visits' 	 => 'SliceWP_Admin_Dashboard_Card_Latest_Visits'
	);

	/**
	 * Hook to register dashboard card handles.
	 * The array element should be 'card_id' => 'class_name'
	 *
	 * @param array
	 *
	 */
	$card_classes = apply_filters( 'slicewp_register_dashboard_card', $card_classes );

	if( empty( $card_classes ) )
		return;

	foreach( $card_classes as $card_class_slug => $card_class_name ) {

		new $card_class_name;

	}

}
add_action( 'slicewp_view_dashboard_top', 'slicewp_initialize_dashboard_cards', 9 );