<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Add commission table reference column edit screen link.
add_filter( 'slicewp_list_table_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_wsf', 10, 2 );
add_filter( 'slicewp_list_table_payout_commissions_column_reference', 'slicewp_list_table_commissions_add_reference_edit_link_wsf', 10, 2 );


/**
 * Adds the edit screen link to the reference column value from the commissions list table.
 *
 * @param string $output
 * @param array  $item
 *
 * @return string
 *
 */
function slicewp_list_table_commissions_add_reference_edit_link_wsf( $output, $item ) {

	if ( empty( $item['reference'] ) ) {
		return $output;
	}

	if ( empty( $item['origin'] ) || $item['origin'] != 'wsf' ) {
		return $output;
	}

    try {

        $submit = new WS_Form_Submit();
        $submit->id = absint( $item['reference'] );
        $submit->db_read( false, false );

    } catch( Exception $e ) {}

    if ( ! empty( $submit->form_id ) ) {
        $output = '<a href="' . esc_url( add_query_arg( array( 'page' => 'ws-form-submit', 'id' => absint( $submit->form_id ) ), admin_url( 'admin.php' ) ) . '#' . absint( $item['reference'] ) ) . '">' . absint( $item['reference'] ) . '</a>';
    }

	return $output;

}