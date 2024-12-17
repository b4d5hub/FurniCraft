<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Commissions.
 *
 */
Class SliceWP_WP_List_Table_Commissions extends SliceWP_WP_List_Table {

	/**
	 * The number of commissions that should appear in the table.
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $items_per_page;

	/**
	 * The data of the table.
	 *
	 * @access public
	 * @var array
	 *
	 */
	public $data = array();


	/**
	 * Constructor.
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'slicewp_commissions',
			'singular' 	=> 'slicewp_commission',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );


		// Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );
		
		// Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_commissions( array( 'number' => -1, 'status' => ( ! empty( $_GET['commission_status'] ) ? sanitize_text_field( $_GET['commission_status'] ) : '' ), 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'visit_id' => ( ! empty( $_GET['visit_id'] ) ? explode( ',', $_GET['visit_id'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ),
            'per_page'    => $this->items_per_page
        ));

		// Get and set table data.
		$this->set_table_data();
		
		// Add column headers and table items.
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->items 		   = $this->data;

	}

	
	/**
	 * Get a list of CSS classes for the table tag.
	 *
	 * @return array
	 * 
	 */
	protected function get_table_classes() {

		return array( 'striped', $this->_args['plural'] );

	}


	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 * 
	 * @return array
	 * 
	 */
	protected function get_bulk_actions() {

		$actions = array(
			'delete' => __( 'Delete', 'slicewp' )
		);

		/**
		 * Filter the bulk actions for this table.
		 * 
		 */
		return apply_filters( 'slicewp_list_table_commissions_bulk_actions', $actions );

	}


	/**
	 * Returns all the columns for the table.
	 *
	 */
	public function get_columns() {

		$columns = array(
			'cb'			=> 'cb',
			'id' 		    => __( 'ID', 'slicewp' ),
			'affiliate'		=> __( 'Affiliate', 'slicewp' ),
			'amount'		=> __( 'Amount', 'slicewp' ),
			'reference'		=> __( 'Reference', 'slicewp' ),
			'type'			=> __( 'Type', 'slicewp' ),
			'date_created'	=> __( 'Date', 'slicewp' ),
			'notes'			=> '<span class="dashicons dashicons-admin-comments" title="' . __( 'Notes', 'slicewp' ) . '"></span>',
			'status'		=> __( 'Status', 'slicewp' ),
			'actions'		=> ''
		);

		/**
		 * Filter the columns of the commissions table.
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_commissions_columns', $columns );

	}


	/**
	 * Returns all the sortable columns for the table.
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id'			=> array( 'id', false ),
			'amount'		=> array( 'amount', false ),
			'date_created'	=> array( 'date_created', false)
		);

		/**
		 * Filter the sortable columns of the commissions table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_commissions_sortable_columns', $columns );

	}


	/**
     * Returns the possible views for the commission list table.
     *
     */
    protected function get_views() {

    	$statuses = slicewp_get_commission_available_statuses();

    	$commission_status = ( ! empty( $_GET['commission_status'] ) ? sanitize_text_field( $_GET['commission_status'] ) : '' );

		// Get the start date from the filter, or set it to the default value if not present.
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		// Get the end date from the filter, or set it to the default value if not present.
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		
		// Set the view for "all" commissions.
    	$views = array(
    		'all' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'visit_id' => ( ! empty( $_GET['visit_id'] ) ? $_GET['visit_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( empty( $commission_status ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_commissions( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'visit_id' => ( ! empty( $_GET['visit_id'] ) ? array_map( 'absint', explode( ',', $_GET['visit_id'] ) ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>'
    	);

    	// Set the views for each commission status.
    	foreach ( $statuses as $status_slug => $status_name ) {
    		$views[$status_slug] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'commission_status' => $status_slug, 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'visit_id' => ( ! empty( $_GET['visit_id'] ) ? $_GET['visit_id'] : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( $commission_status == $status_slug ? 'class="current"' : '' ) . '>' . $status_name . ' <span class="count">(' . slicewp_get_commissions( array( 'status' => $status_slug, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ),'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'visit_id' => ( ! empty( $_GET['visit_id'] ) ? array_map( 'absint', explode( ',', $_GET['visit_id'] ) ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>';
    	}

		/**
		 * Filter the views of the commissions table.
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_commissions_views', $views );

    }


	/**
	 * Gets the commissions data and sets it.
	 *
	 */
	private function set_table_data() {

		// Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		// Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		$commission_args = array(
			'number'  		=> $this->items_per_page,
			'offset'  		=> ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'status'  		=> ( ! empty( $_GET['commission_status'] ) ? sanitize_text_field( $_GET['commission_status'] ) : '' ),
			'search'  		=> ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'affiliate_id'	=> ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '' ),
			'visit_id'	    => ( ! empty( $_GET['visit_id'] ) ? array_map( 'absint', explode( ',', $_GET['visit_id'] ) ) : '' ),
			'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
			'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
			'orderby' 		=> ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'	  		=> ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);
		
		$commissions = slicewp_get_commissions( $commission_args );

		if ( empty( $commissions ) ) {
			return;
		}

		foreach ( $commissions as $commission ) {

			$row_data = $commission->to_array();

			/**
			 * Filter the commission row data
			 *
			 * @param array 		 $row_data
			 * @param SliceWP_Commission $commission
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_commissions_row_data', $row_data, $commission );

			$this->data[] = $row_data;

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
	 * Returns the HTML that will be displayed in the "cb" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_cb( $item ) {

		return sprintf( '<input type="checkbox" name="commission_ids[]" value="%1$s" />', absint( $item['id'] ) );

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

		/**
		 * Set user display name
		 *
		 */
		$affiliate_name = slicewp_get_affiliate_name( $item['affiliate_id'] );

		if ( null === $affiliate_name ) {
			$output = __( '(inexistent affiliate)', 'slicewp' );
		} else {
			$output = '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => $item['affiliate_id'] ) , admin_url( 'admin.php' ) ) ) . '">' . esc_html( $affiliate_name ) . '</a>';
		}

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "amount" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_amount( $item ) {

		$output = slicewp_format_amount( $item['amount'], slicewp_get_setting( 'active_currency', 'USD' ) );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "type" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_type( $item ) {

		$commission_types = slicewp_get_commission_types();

		return ( ! empty( $commission_types[$item['type']] ) ? $commission_types[$item['type']]['label'] : ( ! empty( $item['type'] ) ? ucfirst( $item['type'] ) : '-' ) );

	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_created( $item ) {

		$output = slicewp_date_i18n( $item['date_created'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "notes" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_notes( $item ) {

		$notes_count = slicewp_get_notes( array( 'object_context' => 'commission', 'object_id' => $item['id'] ), true );

		if ( empty( $notes_count ) ) {
			return '-';
		}

		$output = '<span class="slicewp-notes-count">' . absint( $notes_count ) . '</span>';

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "status" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_status( $item ) {

		$statuses = slicewp_get_commission_available_statuses();

		$output = ( ! empty( $statuses[$item['status']] ) ? '<span class="slicewp-status-pill slicewp-status-' . esc_attr( $item['status'] ) . '">' . $statuses[$item['status']] . '</span>' : '' );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "actions" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_actions( $item ) {

		/**
		 * Set row actions.
		 *
		 */
		$row_actions = array();

		if ( 'unpaid' == $item['status'] ) {

			$row_actions['mark_as_paid'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'mark_as_paid_commission', 'commission_id' => absint( $item['id'] ) ), slicewp_get_filtered_admin_url() ), 'slicewp_mark_as_paid_commission', 'slicewp_token' ) ) . '">' . __( 'Mark as Paid', 'slicewp' ) . '</a>';
			$row_actions['reject'] 		 = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'reject_commission', 'commission_id' => absint( $item['id'] ) ), slicewp_get_filtered_admin_url() ), 'slicewp_reject_commission', 'slicewp_token' ) ) . '">' . __( 'Reject', 'slicewp' ) . '</a>';

		}

		if ( 'pending' == $item['status'] ) {

			$row_actions['approve'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'approve_commission', 'commission_id' => absint( $item['id'] ) ) , slicewp_get_filtered_admin_url() ), 'slicewp_approve_commission', 'slicewp_token' ) ) . '">' . __( 'Approve', 'slicewp' ) . '</a>';
			$row_actions['reject']  = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'reject_commission', 'commission_id' => absint( $item['id'] ) ) , slicewp_get_filtered_admin_url() ), 'slicewp_reject_commission', 'slicewp_token' ) ) . '">' . __( 'Reject', 'slicewp' ) . '</a>';

		}

		if ( 'rejected' == $item['status'] ) {

			$row_actions['approve'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'approve_commission', 'commission_id' => absint( $item['id'] ) ) , slicewp_get_filtered_admin_url() ), 'slicewp_approve_commission', 'slicewp_token' ) ) . '">' . __( 'Approve', 'slicewp' ) . '</a>';

		}

		$row_actions['delete'] = '<a class="slicewp-trash" onclick="return confirm( \'' . __( "Are you sure you want to delete this commission?", "slicewp" ) . ' \' )" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'slicewp_action' => 'delete_commission', 'commission_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ), 'slicewp_delete_commission', 'slicewp_token' ) ) . '">' . __( 'Delete', 'slicewp' ) . '</a>';

		/**
		 * Filter the row actions.
		 * 
		 * @param array $row_actions
		 * @param array $item
		 * 
		 */
		$row_actions = apply_filters( 'slicewp_list_table_commissions_row_actions', $row_actions, $item );

		$output = '<div class="row-actions">';

			$output .= '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ) ) . '" class="slicewp-button-secondary">' . __( 'Edit', 'slicewp' ) . '</a>';
			$output .= '<a href="#" class="slicewp-button-toggle-actions"><svg class="" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 12c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2zm12-2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm-7 0c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg></a>';

			$output .= '<div class="slicewp-actions-dropdown">';
			
				foreach ( $row_actions as $row_action ) {

					if ( empty( $row_action ) ) {
						continue;
					}
					
					$output .= $row_action;

				}

			$output .= '</div>';

		$output .= '</div>';

		return $output;

	}

	/**
	 * HTML display when there are no items in the table.
	 *
	 */
	public function no_items() {

		echo __( 'No commissions found.', 'slicewp' );

	}


	/**
	 * Adds extra content to the table nav.
	 * 
	 * @param string $which
	 * 
	 */
	protected function extra_tablenav( $which ) {

		// Table ID.
		$table_id = str_replace( 'slicewp_', '', $this->_args['plural'] );

		// Add table data filters.
		if ( 'top' === $which ) {

			ob_start();

			?>

				<!-- Affiliate User ID -->
				<div class="slicewp-list-table-data-filter slicewp-list-table-data-filter-user-search">

					<label for="slicewp-list-table-data-filter-affiliate" class="screen-reader-text"><?php echo __( 'Affiliate', 'slicewp' ); ?></label>

					<?php slicewp_output_select2_user_search( array( 'id' => 'slicewp-affiliate-id', 'data_attributes' => array( 'container-class' => 'slicewp-select2-small' ), 'name' => 'affiliate_id', 'placeholder' => __( 'Select affiliate...', 'slicewp' ), 'user_type' => 'affiliate', 'value' => ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '' ) ) ); ?>
					
				</div>

				<!-- Date Min -->
				<div class="slicewp-list-table-data-filter slicewp-list-table-data-filter-datepicker">

					<input type="text" name="date_min" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'From', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_GET['date_min'] ) ? esc_attr( $_GET['date_min'] ) : '' )?>" />

				</div>

				<!-- Date Max -->
				<div class="slicewp-list-table-data-filter slicewp-list-table-data-filter-datepicker">

					<input type="text" name="date_max" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'To', 'slicewp' ); ?>" value="<?php echo ( ! empty( $_GET['date_max'] ) ? esc_attr( $_GET['date_max'] ) : '' )?>" />

				</div>

			<?php

			/**
			 * Action to add extra data filters.
			 * 
			 */
			do_action( "slicewp_list_table_{$table_id}_data_filters_output" );

			$output = ob_get_clean();

			if ( $output ) {

				echo '<div class="alignleft actions slicewp-list-table-data-filters" style="display: flex;">';

					// Output the filters.
					echo $output;

					// Output the commission status.
					echo '<input type="hidden" name="commission_status" value="' . esc_attr( ! empty( $_GET['commission_status'] ) ? $_GET['commission_status'] : '' ) . '" />';

					// Output the filters submit button.
					echo '<button type="submit" class="slicewp-form-submit slicewp-spinner-inner slicewp-button-secondary"><span>' . __( 'Filter', 'slicewp' ) . '</span></button>';

					// Output reset link.
					echo '<a class="slicewp-list-table-data-filters-reset" style="display: none;" href="' . esc_url( remove_query_arg( array( 'paged' ) ) ) . '">' . __( 'Clear', 'slicewp' ) . '</a>';

				echo '</div>';

			}

		}

		/**
		 * Add extra functionality from the outside.
		 * 
		 * @param string $which
		 * 
		 */
		do_action( "slicewp_list_table_{$table_id}_extra_table_nav", $which );

		if ( 'bottom' === $which ) {
		
			?>

				<script>

					var selector_top    = document.getElementById( 'bulk-action-selector-top' );
					var selector_bottom = document.getElementById( 'bulk-action-selector-bottom' );

					if ( selector_top ) {
						selector_top.querySelector( 'option[value="delete"]' ).setAttribute( 'data-confirmation-message', "<?php echo esc_attr( __( 'Are you sure you want to delete the selected commission(s)?', 'slicewp' ) ); ?>" );
					}
					
					if ( selector_bottom ) {
						selector_bottom.querySelector( 'option[value="delete"]' ).setAttribute( 'data-confirmation-message', "<?php echo esc_attr( __( 'Are you sure you want to delete the selected commission(s)?', 'slicewp' ) ); ?>" );
					}
					
				</script>

			<?php

		}

	}

}