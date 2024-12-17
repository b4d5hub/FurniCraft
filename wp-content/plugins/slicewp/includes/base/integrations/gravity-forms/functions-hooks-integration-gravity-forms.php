<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link.
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_gfo', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_gfo', 10, 2 );

/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_gfo( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'gfo' ) {
		return $output;
	}

	// Get the entry
	$entry = GFAPI::get_entry( absint( $item['reference'] ) );

	// Create link to entry only if the entry exists
	if ( ! is_wp_error( $entry ) ) {
		$output = '<a href="' . esc_url( add_query_arg( array( 'page' => 'gf_entries', 'view' => 'entry', 'id' => $entry['form_id'], 'lid' => absint( $item['reference'] ) ), admin_url( 'admin.php' ) ) ) . '">' . absint( $item['reference'] ) . '</a>';
	}

	return $output;

}