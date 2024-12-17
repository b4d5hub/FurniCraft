<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that outputs the "commissions" HTML table for a particular payment from the affiliate account.
 * 
 */
class SliceWP_List_Table_Affiliate_Account_Payment_Commissions extends SliceWP_List_Table {

    /**
     * A string identifying the table.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $id = 'affiliate_account_payment_commissions';

    /**
     * Array containing all available commission types.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $commission_types = array();

    /**
     * Array containing all available commission statuses.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $commission_statuses = array();


    /**
     * Constructor.
     * 
     */
    public function __construct( $args = array() ) {

        parent::__construct( $args );

        $this->table_columns = array(
            'id'     => __( 'ID', 'slicewp' ),
            'date'   => __( 'Date', 'slicewp' ),
            'type'   => __( 'Type', 'slicewp' ),
            'amount' => __( 'Amount', 'slicewp' ),
            'status' => __( 'Status', 'slicewp' )
        );

        $this->commission_types    = slicewp_get_commission_types();
        $this->commission_statuses = slicewp_get_commission_available_statuses();
        $this->no_items            = ( empty( $_GET['list-table-filter-date-start'] ) ? __( 'You have no commissions.', 'slicewp' ) : '' );

        $this->set_table_items_data();

    }


    /**
     * Sets the commissions data.
     * 
     */
    protected function set_table_items_data() {

        if ( empty( $this->args['payment'] ) ) {
            return;
        }

        $affiliate_id = slicewp_get_current_affiliate_id();

        // Prepare the commission args.
        $commission_args = array(
            'number'		=> -1,
            'affiliate_id'	=> $affiliate_id,
            'payment_id'    => $this->args['payment']->get( 'id' ),
            'status'		=> array( 'paid', 'unpaid' )
        );

        $this->items_total = slicewp_get_commissions( $commission_args, true );
        $this->items 	   = slicewp_get_commissions( $commission_args );

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
     * Column "type".
     * 
     * @param array $item
     * 
     * @return string
     * 
     */
    public function column_type( $item ) {

        return ( ! empty( $this->commission_types[$item['type']]['label'] ) ? $this->commission_types[$item['type']]['label'] : $item['type'] );

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

        return ( ! empty( $this->commission_statuses[$item['status']] ) ? $this->commission_statuses[$item['status']] : $item['status'] );

    }


    /**
     * Outputs elements before the table.
     * 
     */
    public function output_table_before() {}


    /**
     * Outputs the per page selector.
     * 
     */
    public function output_per_page_selector() {}

}