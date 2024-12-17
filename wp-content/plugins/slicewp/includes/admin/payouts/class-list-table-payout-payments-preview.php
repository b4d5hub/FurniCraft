<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Payments
 *
 */
Class SliceWP_WP_List_Table_Payout_Preview_Payments extends SliceWP_WP_List_Table {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'slicewp_payout_preview_payments',
			'singular' 	=> 'slicewp_payout_preview_payment',
			'ajax' 		=> false
		));

		// Add column headers and table items.
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		// Alter the $_GET variable for sorting.
		if ( empty( $_GET['orderby'] ) )
			$_GET['orderby'] = 'amount';

		if ( empty( $_GET['order'] ) )
			$_GET['order'] = 'desc';
	
	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns() {

		$columns = array(
			'affiliate'		=> __( 'Affiliate', 'slicewp' ),
            'amount'		=> __( 'Amount', 'slicewp' ),
            'commissions'   => __( 'Commissions', 'slicewp' ),
			'payout_method' => __( 'Payout Method', 'slicewp' )
		);

		/**
		 * Filter the columns of the payout payments preview table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payout_preview_payments_columns', $columns );

	}

	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'amount'		=> array( 'amount', false ),
			'commissions'	=> array( 'commissions', false)
        );

		/**
		 * Filter the sortable columns of the payments table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_payout_preview_payments_sortable_columns', $columns );

	}


	/**
	 * Prepares the current items for displaying.
	 * 
	 */
	public function prepare_items() {

		// Sort the items.
		$sort = array();

		if ( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], array( 'amount', 'commissions' ) ) && ! empty( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ){
			
			foreach ( $this->items as $index => $row_data ){

				$sort[$index] = ( $_GET['orderby'] == 'amount' ? $row_data['amount'] : count( $row_data['commission_ids'] ) );

			}

			array_multisort( $sort, ( $_GET['order'] == 'desc' ? SORT_DESC : SORT_ASC ), $this->items );

		}

		foreach ( $this->items as $index => $row_data ) {

			/**
			 * Filter the payment row data
			 *
			 * @param array $row_data
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_payout_preview_payments_row_data', $row_data );

			$this->items[$index] = $row_data;

		}

	}


	/**
	 * Returns the HTML that will be displayed in each columns
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '-';

	}


	/**
	 * Returns the HTML that will be displayed in the "affiliate" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_affiliate( $item ) {

		$affiliate 		= slicewp_get_affiliate( absint( $item['affiliate_id'] ) );
		$affiliate_name = ( ! is_null( $affiliate ) ? slicewp_get_affiliate_name( $affiliate ) : '' );

		if ( ! is_null( $affiliate ) ) {

			$output = '<a class="slicewp-affiliate-name" href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => absint( $affiliate->get( 'id' ) ) ) , admin_url( 'admin.php' ) ) ) . '">';
				$output .= '<span>' . $affiliate_name . '</span>';
			$output .= '</a>';

		} else {

			$output = __( '(inexistent affiliate)', 'slicewp' );

		}

		return $output;

    }
    
    
    /**
	 * Returns the HTML that will be displayed in the "amount" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_amount( $item ) {

		$output = slicewp_format_amount( $item['amount'], $item['currency'] );

		return $output;

	}


    /**
	 * Returns the HTML that will be displayed in the "commissions" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions( $item ) {

        $output = count( $item['commission_ids'] );

        return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "payout_method" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_payout_method( $item ) {

		$payout_methods 		 = slicewp_get_payout_methods();
		$affiliate_payout_method = slicewp_get_affiliate_payout_method( $item['affiliate_id'] );

        $output = ( ! empty( $payout_methods[$affiliate_payout_method]['label'] ) ? $payout_methods[$affiliate_payout_method]['label'] : $affiliate_payout_method );

        return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No payments found.', 'slicewp' );

	}

}