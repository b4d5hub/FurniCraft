<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Visits.
 *
 */
Class SliceWP_WP_List_Table_Visits extends SliceWP_WP_List_Table {

	/**
	 * The number of visits that should appear in the table.
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
			'plural' 	=> 'slicewp_visits',
			'singular' 	=> 'slicewp_visit',
			'ajax' 		=> false
		));

		$this->items_per_page = 10;
		$this->paged 		  = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		//Get the start date from the filter, or set it to the default value if not present
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );
		
		//Get the end date from the filter, or set it to the default value if not present
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		$this->set_pagination_args( array(
            'total_items' => slicewp_get_visits( array( 'number' => -1, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ), 'converted' => ( isset ( $_GET['converted'] ) ? (bool)$_GET['converted'] : '' ) ), true ),
 			'per_page'    => $this->items_per_page
        ));

		// Get and set table data.
		$this->set_table_data();
		
		// Add column headers and table items.
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->items 		   = $this->data;

	}


	/**
	 * Returns all the columns for the table.
	 *
	 */
	public function get_columns() {

		$columns = array(
			'id' 		    	=> __( 'ID', 'slicewp' ),
			'affiliate_name'	=> __( 'Affiliate', 'slicewp' ),
			'date_created'  	=> __( 'Date', 'slicewp' ),
			'ip_address'		=> __( 'IP Address', 'slicewp' ),
			'landing_url'		=> __( 'Landing URL', 'slicewp' ),
			'referrer_url'		=> __( 'Referrer URL', 'slicewp' ),
			'converted'			=> __( 'Converted', 'slicewp' )
		);

		/**
		 * Filter the columns of the affiliates table.
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_visits_columns', $columns );

	}


	/**
	 * Returns all the sortable columns for the table.
	 *
	 */
	public function get_sortable_columns() {

		$columns = array(
			'id'				=> array( 'id', false ),
			'date_created'		=> array( 'date_created', false ),
			'landing_url'		=> array( 'landing_url', false ),
			'referrer_url'		=> array( 'referrer_url', false ),
			'converted'			=> array( 'commission_id', false)
		);

		/**
		 * Filter the sortable columns of the visits table.
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'slicewp_list_table_visits_sortable_columns', $columns );

	}

	/**
     * Returns the possible views for the visits list table.
     *
     */
    protected function get_views() {

		$converted = ( isset ( $_GET['converted'] ) ? (bool)( $_GET['converted'] ) : NULL );

		//Get the start date from the filter, or set it to the default value if not present.
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		//Get the end date from the filter, or set it to the default value if not present.
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		// Set the view for "all" visits.
    	$views = array(
    		'all' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( is_null( $converted ) ? 'class="current"' : '' ) . '>' . __( 'All', 'slicewp' ) . ' <span class="count">(' . slicewp_get_visits( array( 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>'
		);
		
		// Set the views for each visits status.
		$views['converted'] 	= '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'converted' => 1, 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( $converted === true ? 'class="current"' : '' ) . '>' . __('Converted', 'slicewp') . ' <span class="count">(' . slicewp_get_visits( array( 'converted' => true, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>';
		$views['not_converted'] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-visits', 'user_search' => ( ! empty( $_GET['user_search'] ) ? $_GET['user_search'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? $date_min->format( 'Y-m-d' ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? $date_max->format( 'Y-m-d' ) : '' ), 'converted' => 0, 'paged' => 1 ), admin_url( 'admin.php' ) ) ) . '" ' . ( $converted === false ? 'class="current"' : '' ) . '>' . __('Not Converted', 'slicewp') . ' <span class="count">(' . slicewp_get_visits( array( 'converted' => false, 'search' => ( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ), 'affiliate_id' => ( ! empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '' ), 'landing_url' => ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ), 'date_min' => ( ! empty ( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ), 'date_max' => ( ! empty ( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ) ), true ) . ')</span></a>';

		/**
		 * Filter the views of the visits table.
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'slicewp_list_table_visits_views', $views );

	}
	
	/**
	 * Gets the visits data and sets it.
	 *
	 */
	private function set_table_data() {

		//Get the start date from the filter, or set it to the default value if not present.
		$date_min = ( ! empty( $_GET['date_min'] ) ? new DateTime( $_GET['date_min'] . ' 00:00:00' ) : '' );

		//Get the end date from the filter, or set it to the default value if not present.
		$date_max = ( ! empty( $_GET['date_max'] ) ? new DateTime( $_GET['date_max'] . ' 23:59:59') : '' );

		$visit_args = array(
			'number'		=> $this->items_per_page,
			'offset'		=> ( $this->get_pagenum() - 1 ) * $this->items_per_page,
			'affiliate_id'	=> ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '' ),
			'landing_url'	=> ( ! empty( $_GET['landing_url'] ) ? sanitize_text_field( $_GET['landing_url'] ) : '' ),
			'date_min'		=> ( ! empty( $date_min ) ? get_gmt_from_date( $date_min->format( 'Y-m-d H:i:s' ) ) : '' ),
			'date_max'		=> ( ! empty( $date_max ) ? get_gmt_from_date( $date_max->format( 'Y-m-d H:i:s' ) ) : '' ),
			'search'		=> ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ),
			'converted'		=> ( isset( $_GET['converted'] ) ? (bool)$_GET['converted'] : '' ),
			'orderby'		=> ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'			=> ( ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc' )
		);

		$visits = slicewp_get_visits( $visit_args );
		
		if ( empty( $visits ) ) {
			return;
		}

		foreach ( $visits as $visit ) {

			$row_data = $visit->to_array();

			/**
			 * Filter the visit row data
			 *
			 * @param array			$row_data
			 * @param slicewp_Visit		$visit
			 *
			 */
			$row_data = apply_filters( 'slicewp_list_table_visits_row_data', $row_data, $visit );

			$this->data[] = $row_data;

		}
		
	}


	/**
	 * Returns the HTML that will be displayed in each columns.
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
	 * Returns the HTML that will be displayed in the "name" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_affiliate_name( $item ) {

		/**
		 * Set user display name.
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
	 * Returns the HTML that will be displayed in the "landing_url" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_landing_url( $item ) {

		$output = '<a href="' . esc_url_raw( $item['landing_url'] ) . '" target="_blank">' . rawurldecode( $item['landing_url'] ) . '</a>';

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "referrer_url" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_referrer_url( $item ) {

		$output = '<a href="' . esc_url_raw( $item['referrer_url'] ) . '" target="_blank">' . rawurldecode( $item['referrer_url'] ) . '</a>';

		return $output;

	}

	
	/**
	 * Returns the HTML that will be displayed in the "converted" column.
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_converted( $item ) {

		if ( empty( $item['commission_id'] ) ) {
			$output = '<span class="slicewp-status-icon">' . slicewp_get_svg( 'outline-x' ) . '</span>';
		} else {
			$output = '<span class="slicewp-status-icon slicewp-status-converted">' . slicewp_get_svg( 'outline-check' ) . '</span>' . '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $item['commission_id'] ) , admin_url( 'admin.php' ) ) ) . '">' . '#' . absint( $item['commission_id'] ) . '</a>';
		}

		return $output;

	}


	/**
	 * HTML display when there are no items in the table.
	 *
	 */
	public function no_items() {

		echo __( 'No visits found.', 'slicewp' );

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

					// Output the visit status.
					if ( ! empty( $_GET['converted'] ) ) {
						echo '<input type="hidden" name="converted" value="' . esc_attr( $_GET['converted'] ) . '" />';
					}

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

	}

}