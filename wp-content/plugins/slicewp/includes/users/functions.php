<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the user area.
 *
 */
function slicewp_include_files_user() {

	// Get dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include AJAX functions.
	if ( file_exists( $dir_path . 'functions-actions-ajax.php' ) ) {
		include $dir_path . 'functions-actions-ajax.php';
	}

	// Include the db layer classes
	if ( file_exists( $dir_path . 'class-user-notices.php' ) ) {
		include $dir_path . 'class-user-notices.php';
	}

	// Include HTML list table class.
	if ( file_exists( $dir_path . 'class-list-table.php' ) ) {
		include $dir_path . 'class-list-table.php';
	}

	if ( file_exists( $dir_path . 'class-list-table-affiliate-account-commissions.php' ) ) {
		include $dir_path . 'class-list-table-affiliate-account-commissions.php';
	}

	if ( file_exists( $dir_path . 'class-list-table-affiliate-account-payment-commissions.php' ) ) {
		include $dir_path . 'class-list-table-affiliate-account-payment-commissions.php';
	}

	if ( file_exists( $dir_path . 'class-list-table-affiliate-account-visits.php' ) ) {
		include $dir_path . 'class-list-table-affiliate-account-visits.php';
	}

	if ( file_exists( $dir_path . 'class-list-table-affiliate-account-payments.php' ) ) {
		include $dir_path . 'class-list-table-affiliate-account-payments.php';
	}

	// Include HTML list table class.
	if ( file_exists( $dir_path . 'functions-actions-list-table.php' ) ) {
		include $dir_path . 'functions-actions-list-table.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_user' );


/**
 * Adds a central action hook on the init that the plugin and add-ons
 * can use to do certain actions, like adding a new user, editing a user, etc.
 *
 */
function slicewp_register_user_do_actions() {

	// Exit if is accessed from admin panel
	if ( is_admin() ) {
		return;
	}

	if ( empty( $_REQUEST['slicewp_action'] ) ) {
		return;
	}

	$action = sanitize_text_field( $_REQUEST['slicewp_action'] );

	/**
	 * Hook that should be used by all processes that make a certain action
	 * withing the plugin, like adding a new user, editing an user, etc.
	 *
	 */
	do_action( 'slicewp_user_action_' . $action );

}
add_action( 'init', 'slicewp_register_user_do_actions' );


/**
 * Returns an array with the registered tabs found in the affiliate account.
 * 
 * @return array
 * 
 */
function slicewp_get_affiliate_account_tabs() {

	$tabs = array(
		'dashboard' => array(
			'label' => __( 'Dashboard', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-home' )
		),
		'affiliate_links' => array(
			'label' => __( 'Affiliate Links', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-link' )
		),
		'commissions' => array(
			'label' => __( 'Commissions', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-chart-pie' )
		),
		'visits' => array(
			'label' => __( 'Visits', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-cursor-click' )
		),
		'creatives' => array(
			'label' => __( 'Creatives', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-color-swatch' )
		),
		'payments' => array(
			'label' => __( 'Payouts', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-cash' )
		),
		'settings' => array(
			'label' => __( 'Settings', 'slicewp' ),
			'icon'  => slicewp_get_svg( 'outline-adjustments' )
		)
	);
	
	// Add logout if enabled.
	if ( slicewp_get_setting( 'affiliate_account_logout' ) ) {
	
		$tabs['logout'] = array(
			'label' => __( 'Logout', 'slicewp' ),
			'icon'	=> slicewp_get_svg( 'outline-logout' ),
			'url'	=> slicewp_get_logout_url()
		);
	
	}
	
	
	/**
	 * Filter the tabs for the settings edit screen
	 *
	 * @param array $tabs
	 *
	 */
	$tabs = apply_filters( 'slicewp_affiliate_account_tabs', $tabs );

	return $tabs;

}


/**
 * Returns an array with the options the user has for the items per page selector.
 * 
 * @return array
 * 
 */
function slicewp_get_list_table_items_per_page_options() {

	$options = array( 10, 25, 50, 100 );

	/**
	 * Filter the array before returning.
	 * 
	 * @param array
	 * 
	 */
	$options = apply_filters( 'slicewp_list_table_items_per_page_options', $options );

	return $options;

}


/**
 * Returns a date range picker element configured by the given arguments.
 * 
 * @param array $args
 * 
 */
function slicewp_element_date_range_picker( $args = array() ) {

	$defaults = array(
		'input_name'			 => 'date_range_picker',
		'predefined_date_ranges' => slicewp_get_predefined_date_ranges(),
		'selected_date_range'    => 'past_30_days',
		'selected_date_start'    => '',
		'selected_date_end'    	 => '',
		'sync_id'				 => null
	);

	$args = array_merge( $defaults, $args );

	// Set default dates.
	$dates = slicewp_get_date_range_dates( $args['selected_date_range'] );

	if ( ! empty( $dates ) ) {

		if ( empty( $args['selected_date_start'] ) ) {
			$args['selected_date_start'] = date( 'Y-m-d', strtotime( $dates['date_start'] ) );
		}

		if ( empty( $args['selected_date_end'] ) ) {
			$args['selected_date_end'] = date( 'Y-m-d', strtotime( $dates['date_end'] ) );
		}

	}

	$output = '<div class="slicewp-date-picker-wrapper">';

		$output .= '<div class="slicewp-date-picker-input">';
			$output .= slicewp_get_svg( 'solid-clock' );
			$output .= '<span class="slicewp-date-picker-input-date-range">' . ( in_array( $args['selected_date_range'], array_keys( $args['predefined_date_ranges'] ) ) ? esc_html( $args['predefined_date_ranges'][$args['selected_date_range']] ) : '' ) . '</span>';
			$output .= '<span class="slicewp-date-picker-input-dates"></span>';
		$output .= '</div>';

		$output .= '<div class="slicewp-date-picker-modal">';

			$output .= '<div class="slicewp-date-picker-predefined-date-ranges">';

				foreach ( $args['predefined_date_ranges'] as $slug => $label ) {
					$output .= '<a href="#" class="slicewp-date-picker-predefined-date-range" data-range="' . esc_attr( $slug ) . '">' . esc_html( $label ) . '</a>';
				}

				$output .= '<a href="#" class="slicewp-date-picker-predefined-date-range" data-range="custom">' . __( 'Custom', 'slicewp' ) . '</a>';

			$output .= '</div>';

			$output .= '<div class="slicewp-date-picker" ' . ( ! is_null( $args['sync_id'] ) ? 'data-sync-id="' . esc_attr( $args['sync_id'] ) . '"' : '' ) . '></div>';

		$output .= '</div>';

		$output .= '<input class="slicewp-date-picker-input-date-range" type="hidden" name="' . esc_attr( $args['input_name'] ) . '-date-range" value="' . esc_attr( $args['selected_date_range'] ) . '" />';
		$output .= '<input class="slicewp-date-picker-input-date-start" type="hidden" name="' . esc_attr( $args['input_name'] ) . '-date-start" value="' . esc_attr( $args['selected_date_start'] ) . '" />';
		$output .= '<input class="slicewp-date-picker-input-date-end" type="hidden" name="' . esc_attr( $args['input_name'] ) . '-date-end" value="' . esc_attr( $args['selected_date_end'] ) . '" />';

	$output .= '</div>';

	return $output;

}


/**
 * Outputs the template for the QR code button.
 * 
 * This template is cloned via JS and added to all input elements that have a referral URL the affiliate can copy.
 * 
 */
function slicewp_output_qr_code_templates() {

	if ( empty( slicewp_get_setting( 'referral_link_qr_code' ) ) ) {
		return;
	}

	echo '<button class="slicewp-button-primary slicewp-button-view-qr-code" style="display: none;">';
		echo slicewp_get_svg( 'outline-qrcode' );
		echo __( 'View QR Code', 'slicewp' );
	echo '</button>';

	echo '<div class="slicewp-global-overlay slicewp-global-overlay-qr-code">';

		echo '<div class="slicewp-global-overlay-inner">';

			// Close button.
			echo '<span class="slicewp-global-overlay-close">' . slicewp_get_svg( 'outline-x' ) . '</span>';

			// QR code image.
			echo '<img src="" />';

			echo '<div>';

				echo '<strong>' . __( 'QR code for link:', 'slicewp' ) . '</strong>';
				echo '<span class="slicewp-referral-link-span"></span>';
				
				// Download button.
				echo '<button class="slicewp-button-primary">';
					echo slicewp_get_svg( 'outline-download' );
					echo __( 'Download QR code', 'slicewp' );
				echo '</button>';

			echo '</div>';

		echo '</div>';
	echo '</div>';

}
add_action( 'slicewp_affiliate_account_bottom', 'slicewp_output_qr_code_templates' );