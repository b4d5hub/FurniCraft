<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that outputs the "payments" HTML table from the affiliate account.
 * 
 */
class SliceWP_List_Table_Affiliate_Account_Payments extends SliceWP_List_Table {

    /**
     * A string identifying the table.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $id = 'affiliate_account_payments';

    /**
     * Array containing all available payment statuses.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $payment_statuses = array();


    /**
     * Constructor.
     * 
     */
    public function __construct( $args = array() ) {

        parent::__construct( $args );

        $this->table_columns = array(
            'id'     => __( 'ID', 'slicewp' ),
            'date'   => __( 'Date', 'slicewp' ),
            'amount' => __( 'Amount', 'slicewp' ),
            'status' => __( 'Status', 'slicewp' ),
            'action' => __( 'Action', 'slicewp' )
        );

        $this->payment_statuses = slicewp_get_payment_available_statuses();
        $this->no_items         = ( empty( $_GET['list-table-filter-date-start'] ) ? __( 'You have no payouts.', 'slicewp' ) : '' );
        $this->table_filters    = array( 'date_range_picker' );

        $this->set_table_items_data();

    }


    /**
     * Sets the payments data.
     * 
     */
    protected function set_table_items_data() {

        $affiliate_id = slicewp_get_current_affiliate_id();
        $statuses     = $this->payment_statuses;

        // Remove the "requested" payment status.
        if ( ! empty( $statuses['requested'] ) ) {
            unset( $statuses['requested'] );
        }

        // Prepare the payment args.
        $payment_args = array(
            'number'		=> $this->items_per_page,
            'offset'		=> ( $this->current_page - 1 ) * $this->items_per_page,
            'affiliate_id'	=> $affiliate_id,
            'date_min'      => ( ! empty( $_GET['list-table-filter-date-start'] ) ? get_gmt_from_date( ( new DateTime( $_GET['list-table-filter-date-start'] . ' 00:00:00' ) )->format( 'Y-m-d H:i:s' ) ) : '' ),
            'date_max'      => ( ! empty( $_GET['list-table-filter-date-end'] ) ? get_gmt_from_date( ( new DateTime( $_GET['list-table-filter-date-end'] . ' 23:59:59' ) )->format( 'Y-m-d H:i:s' ) ) : '' ),
            'status'        => array_keys( $statuses )
        );

        $this->items_total = slicewp_get_payments( $payment_args, true );
        $this->items 	   = slicewp_get_payments( $payment_args );

    }


    /**
     * Column "date".
     * 
     * @param array $item
     * 
     * @return string
     * 
     */
    public function column_date( $item ) {

        return slicewp_date_i18n( $item['date_created'] );

    }


    /**
     * Column "amount".
     * 
     * @param array $item
     * 
     * @return string
     * 
     */
    public function column_amount( $item ) {

        return slicewp_format_amount( $item['amount'], slicewp_get_setting( 'active_currency', 'USD' ) );

    }


    /**
     * Column "status".
     * 
     * @param array $item
     * 
     * @return string
     * 
     */
    public function column_status( $item ) {

        return ( ! empty( $this->payment_statuses[$item['status']] ) ? $this->payment_statuses[$item['status']] : $item['status'] );

    }


    /**
     * Column "action".
     * 
     * @param array $item
     * 
     * @return string
     * 
     */
    public function column_action( $item ) {

        $redirect_url = remove_query_arg( array( 'affiliate-account-tab', 'page_number_commissions' ) );
        $redirect_url = add_query_arg( array( 'affiliate-account-tab' => 'commissions', 'payment_id' => $item['id'] ), $redirect_url );

        return '<a href="' . esc_url( $redirect_url ) . '">' . __( 'View', 'slicewp' ) . '</a>';

    }


    /**
     * Outputs the table date range picker.
     * 
     */
    public function output_table_filter_date_range_picker() {

        $args = array(
            'input_name'             => 'list-table-filter',
            'predefined_date_ranges' => array_merge( slicewp_get_predefined_date_ranges(), array( 'all_time' => __( 'All time', 'slicewp' ) ) ),
            'selected_date_range'    => ( ! empty( $_GET['list-table-filter-date-range'] ) ? $_GET['list-table-filter-date-range'] : 'all_time' ),
            'selected_date_start'    => ( ! empty( $_GET['list-table-filter-date-start'] ) ? $_GET['list-table-filter-date-start'] : '' ),
            'selected_date_end'      => ( ! empty( $_GET['list-table-filter-date-end'] ) ? $_GET['list-table-filter-date-end'] : '' ),
            'sync_id'                => 1
        );

        echo slicewp_element_date_range_picker( $args );

    }

}