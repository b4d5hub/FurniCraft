<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link.
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_nfo', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_nfo', 10, 2 );

/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_nfo( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'nfo' ) {
		return $output;
	}

	// Get the entry.
	$entry = Ninja_Forms()->form()->get_sub( absint( $item['reference'] ) );

	// Create link to entry only if the entry exists.
	if ( ! empty( $entry->get_status() ) ) {
		$output = '<a href="' . esc_url( add_query_arg( array( 'post' => absint( $item['reference'] ), 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . absint( $item['reference'] ) . '</a>';
	}

	return $output;

}