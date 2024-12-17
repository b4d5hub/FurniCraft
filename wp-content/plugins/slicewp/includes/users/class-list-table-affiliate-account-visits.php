<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that outputs the "visits" HTML table from the affiliate account.
 * 
 */
class SliceWP_List_Table_Affiliate_Account_Visits extends SliceWP_List_Table {

    /**
     * A string identifying the table.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $id = 'affiliate_account_visits';


    /**
     * Constructor.
     * 
     */
    public function __construct( $args = array() ) {

        parent::__construct( $args );

        $this->table_columns = array(
            'id'           => __( 'ID', 'slicewp' ),
            'date'         => __( 'Date', 'slicewp' ),
            'landing_url'  => __( 'Landing URL', 'slicewp' ),
            'referrer_url' => __( 'Referrer URL', 'slicewp' ),
        );

        $this->no_items      = ( empty( $_GET['list-table-filter-date-start'] ) ? __( 'You have no visits.', 'slicewp' ) : '' );
        $this->table_filters = array( 'date_range_picker' );

        $this->set_table_items_data();

    }


    /**
     * Sets the visits data.
     * 
     */
    protected function set_table_items_data() {

        $affiliate_id = slicewp_get_current_affiliate_id();

        // Prepare the visits args.
        $visit_args = array(
            'number'		=> $this->items_per_page,
            'offset'		=> ( $this->current_page - 1 ) * $this->items_per_page,
            'affiliate_id'	=> $affiliate_id,
            'date_min'      => ( ! empty( $_GET['list-table-filter-date-start'] ) ? get_gmt_from_date( ( new DateTime( $_GET['list-table-filter-date-start'] . ' 00:00:00' ) )->format( 'Y-m-d H:i:s' ) ) : '' ),
            'date_max'      => ( ! empty( $_GET['list-table-filter-date-end'] ) ? get_gmt_from_date( ( new DateTime( $_GET['list-table-filter-date-end'] . ' 23:59:59' ) )->format( 'Y-m-d H:i:s' ) ) : '' )
        );

        $this->items_total = slicewp_get_visits( $visit_args, true );
        $this->items 	   = slicewp_get_visits( $visit_args );

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