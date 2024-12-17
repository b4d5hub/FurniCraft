<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Affiliates.
 *
 */
Class SliceWP_WP_List_Table_Affiliates extends SliceWP_WP_List_Table {

	/**
	 * The number of affiliates that should appear in the table.
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
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'slicewp_affiliates',
			'singular' 	=> 'slicewp_affiliate',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_affiliates( array( 'number' => -1, 'status' => ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' ), 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ),
            'per_page'    => $this->items_per_page
        ));

		// Get and set table data
		$this->set_table_data();
		
		// Add column headers and table items
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
		return apply_filters( 'slicewp_list_table_affiliates_bulk_actions', $actions );

	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns() {

		$columns = array(
			'cb'				 => 'cb',
			'id' 		    	 => __( 'ID', 'slicewp' ),
			'name'		    	 => __( 'Name', 'slicewp' ),
			'earnings_paid'		 => __( 'Paid Earnings', 'slicewp' ),
			'earnings_unpaid'	 => __( 'Unpaid Earnings', 'slicewp' ),
			'commissions_paid'	 => __( 'Paid Commissions', 'slicewp' ),
			'commissions_unpaid' => __( 'Unpaid Commissions', 'slicewp' ),
			'notes'				 => '<span class="dashicons dashicons-admin-comments" title="' . __( 'Notes', 'slicewp' ) . '"></span>',
			'status'			 => __( 'Status', 'slicewp' ),
			'actions'			 => ''
		);

		/**
		 * Filter the columns of the affiliates table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_columns', $columns );

	}


	/**
	 * Returns all the sortable columns for the table
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id' 			  	 => array( 'id', false ),
			'earnings_paid'   	 => array( 'earnings_paid', false ),
			'earnings_unpaid'  	 => array( 'earnings_unpaid', false ),
			'commissions_paid' 	 => array( 'commissions_paid', false ),
			'commissions_unpaid' => array( 'commissions_unpaid', false )
		);

		/**
		 * Filter the sortable columns of the affiliates table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_sortable_columns', $columns );

	}


	/**
     * Returns the possible views for the affiliate list table.
     *
     */
    protected function get_views() {

    	$statuses = slicewp_get_affiliate_available_statuses();

    	$affiliate_status = ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' );

    	// Set the view for "all" affiliates.
    	$views = array(
    		'all' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_group_id' => ( ! empty( $_GET['affiliate_group_id'] ) ? absint( $_GET['affiliate_group_id'] ) : '' ), 'affiliate_parent_id' => ( ! empty( $_GET['affiliate_parent_id'] ) ? absint( $_GET['affiliate_parent_id'] ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( empty( $affiliate_status ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_affiliates( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>'
    	);

    	// Set the views for each affiliate status.
    	foreach ( $statuses as $status_slug => $status_name ) {
    		$views[$status_slug] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'affiliate_status' => $status_slug, 'affiliate_group_id' => ( ! empty( $_GET['affiliate_group_id'] ) ? absint( $_GET['affiliate_group_id'] ) : '' ), 'affiliate_parent_id' => ( ! empty( $_GET['affiliate_parent_id'] ) ? absint( $_GET['affiliate_parent_id'] ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( $affiliate_status == $status_slug ? 'class="current"' : '' ) . '>' . $status_name . ' <span class="count">(' . slicewp_get_affiliates( array( 'status' => $status_slug, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ) ), true ) . ')</span></a>';
    	}

		/**
		 * Filter the views of the affiliates table.
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_affiliates_views', $views );

    }


	/**
	 * Gets the affiliates data and sets it
	 *
	 */
	private function set_table_data() {

		$affiliate_args = array(
			'number'  => $this->items_per_page,
			'offset'  => ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'status'  => ( ! empty( $_GET['affiliate_status'] ) ? sanitize_text_field( $_GET['affiliate_status'] ) : '' ),
			'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'orderby' => ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'	  => ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$affiliates = slicewp_get_affiliates( $affiliate_args );

		if ( empty( $affiliates ) ) {
			return;
		}

		foreach ( $affiliates as $affiliate ) {

			$row_data = $affiliate->to_array();

			/**
			 * Filter the affiliate row data
			 *
			 * @param array 		    $row_data
			 * @param Slicewp_Affiliate $affiliate
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_affiliates_row_data', $row_data, $affiliate );

			$this->data[] = $row_data;

		}
		
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

		return sprintf( '<input type="checkbox" name="affiliate_ids[]" value="%1$s" />', absint( $item['id'] ) );

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
	 * Returns the HTML that will be displayed in the "name" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_name( $item ) {

		$affiliate 		= slicewp_get_affiliate( absint( $item['id'] ) );
		$affiliate_name = ( ! is_null( $affiliate ) ? slicewp_get_affiliate_name( $affiliate ) : '' );
		
		if ( is_null( $affiliate ) ) {

			$output = __( '(inexistent affiliate)', 'slicewp' );

		} else if ( $item['status'] != 'pending' ) {

			$output  = '<span>';

				$output .= '<a class="slicewp-affiliate-name" href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ) ) . '">';
					$output .= get_avatar( $affiliate->get( 'user_id' ), 64 );
					$output .= '<span>' . $affiliate_name . '</span>';
				$output .= '</a>';
				
				if ( $item['status'] == 'active' ) {

					$affiliate_referral_url = slicewp_get_affiliate_url( $item['id'] );

					$output .= '<span class="slicewp-tooltip-wrapper slicewp-copy-affiliate-url">';
						$output .= slicewp_get_svg( 'outline-link' );
						$output .= '<span class="slicewp-tooltip-message">';
							$output .= '<span>' . __( "Copy the affiliate's link", 'slicewp' ) . '</span>';
							$output .= '<span style="display: none;">' . __( 'Copied!', 'slicewp' ) . '</span>';
							$output .= '<span class="slicewp-tooltip-arrow"></span>';
						$output .= '</span>';
						$output .= '<input type="text" value="' . esc_url( $affiliate_referral_url ) . '" />';
					$output .= '</span>';

					if ( ! empty( slicewp_get_setting( 'referral_link_qr_code' ) ) ) {

						$output .= '<span class="slicewp-tooltip-wrapper slicewp-view-affiliate-qr-code">';
							$output .= slicewp_get_svg( 'outline-qrcode' );
							$output .= '<span class="slicewp-tooltip-message">';
								$output .= '<span>' . __( "View the affiliate's QR code", 'slicewp' ) . '</span>';
								$output .= '<span class="slicewp-tooltip-arrow"></span>';
							$output .= '</span>';
							$output .= '<input type="hidden" value="' . esc_url( $affiliate_referral_url ) . '" />';
						$output .= '</span>';
						
					}

				}
						
			$output .= '</span>';

		} else {

			$output = '<a class="slicewp-affiliate-name" href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'review-affiliate', 'affiliate_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ) ) . '">';
				$output .= get_avatar( $affiliate->get( 'user_id' ), 64 );
				$output .= '<span>' . $affiliate_name . '</span>';
			$output .= '</a>';

		}

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "earnings_paid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_earnings_paid( $item ) {

		$output = slicewp_get_affiliate_earnings_paid( $item['id'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "earnings_unpaid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_earnings_unpaid( $item ) {

		$output = slicewp_get_affiliate_earnings_unpaid( $item['id'] );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "commissions_paid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions_paid( $item ) {

		$output = slicewp_get_commissions( array( 'number' => -1, 'affiliate_id' => $item['id'], 'status' => 'paid' ), true );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "commissions_unpaid" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_commissions_unpaid( $item ) {

		$output = slicewp_get_commissions( array( 'number' => -1, 'affiliate_id' => $item['id'], 'status' => 'unpaid' ), true );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "notes" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_notes( $item ) {

		$notes_count = slicewp_get_notes( array( 'object_context' => 'affiliate', 'object_id' => $item['id'] ), true );

		if ( empty( $notes_count ) ) {
			return '-';
		}

		$output = '<span class="slicewp-notes-count">' . absint( $notes_count ) . '</span>';

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "status" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_status( $item ) {

		$statuses = slicewp_get_affiliate_available_statuses();

		$output = ( ! empty( $statuses[$item['status']] ) ? '<span class="slicewp-status-pill slicewp-status-' . esc_attr( $item['status'] ) . '">' . $statuses[$item['status']] . '</span>' : '' );

		return $output;

	}
	

	/**
	 * Returns the HTML that will be displayed in the "actions" column
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
		$row_actions = array(
			'delete' => '<a class="slicewp-trash" onclick="return confirm( \'' . __( "Are you sure you want to delete this affiliate?", "slicewp" ) . ' \' )" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_action' => 'delete_affiliate', 'affiliate_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ), 'slicewp_delete_affiliate', 'slicewp_token' ) ) . '">' . __( 'Delete', 'slicewp' ) . '</a>'
		);

		/**
		 * Filter the row actions.
		 * 
		 * @param array $row_actions
		 * @param array $item
		 * 
		 */
		$row_actions = apply_filters( 'slicewp_list_table_affiliates_row_actions', $row_actions, $item );

		$output = '<div class="row-actions">';

			if ( $item['status'] != 'pending' ) {

				$output .= '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'edit-affiliate', 'affiliate_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ) ) . '" class="slicewp-button-secondary">' . __( 'Edit', 'slicewp' ) . '</a>';

			} else {

				$output .= '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => 'review-affiliate', 'affiliate_id' => absint( $item['id'] ) ) , admin_url( 'admin.php' ) ) ) . '" class="slicewp-button-secondary">' . __( 'Review', 'slicewp' ) . '</a>';

			}
			
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
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No affiliates found.', 'slicewp' );

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

					// Output the affiliate status.
					echo '<input type="hidden" name="affiliate_status" value="' . esc_attr( ! empty( $_GET['affiliate_status'] ) ? $_GET['affiliate_status'] : '' ) . '" />';

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

		// Add elements to the bottom.
		if ( 'bottom' === $which ) {

			// Add the needed scripts for the bulk actions.
			?>

				<script>

					var selector_top    = document.getElementById( 'bulk-action-selector-top' );
					var selector_bottom = document.getElementById( 'bulk-action-selector-bottom' );

					if ( selector_top ) {
						selector_top.querySelector( 'option[value="delete"]' ).setAttribute( 'data-confirmation-message', "<?php echo esc_attr( __( 'Are you sure you want to delete the selected affiliate(s)?', 'slicewp' ) ); ?>" );
					}

					if ( selector_bottom ) {
						selector_bottom.querySelector( 'option[value="delete"]' ).setAttribute( 'data-confirmation-message', "<?php echo esc_attr( __( 'Are you sure you want to delete the selected affiliate(s)?', 'slicewp' ) ); ?>" );
					}

				</script>

			<?php

			// Adds the modal for the affiliate QR code.
			if ( ! empty( slicewp_get_setting( 'referral_link_qr_code' ) ) ) {

				?>

					<div class="slicewp-screen-overlay">

						<div class="slicewp-modal-frame slicewp-modal-affiliate-qr-code">

							<div class="slicewp-modal-content">

								<div class="slicewp-modal-header">
									<h1><?php echo __( "Affiliate's QR code", 'slicewp' ); ?></h1>
									<a href="#" class="slicewp-close-modal"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></a>
								</div>

								<div class="slicewp-affiliate-details">
									<div class="slicewp-affiliate-avatar"></div>
									<div class="slicewp-affiliate-name"></div>
								</div>

								<img class="slicewp-affiliate-qr-code" src="" />

								<div>

									<div><strong><?php echo esc_html( __( 'QR code for link:', 'slicewp' ) ); ?></strong></div>
									<div><span class="slicewp-referral-link-span"></span></div>
									
									<div>
										<button class="slicewp-button-secondary slicewp-has-svg-icon">
											<?php
												echo slicewp_get_svg( 'outline-download' );
												echo __( 'Download QR code', 'slicewp' );
											?>
										</button>
									</div>

								</div>

							</div>

						</div>

					</div>

				<?php

			}

		}

	}

}