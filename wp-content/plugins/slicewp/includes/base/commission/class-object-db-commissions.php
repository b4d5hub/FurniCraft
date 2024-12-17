<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Commissions.
 *
 */
Class SliceWP_Object_DB_Commissions extends SliceWP_Object_DB {

	/**
	 * Construct.
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_commissions';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'commission';
		$this->query_object_type = 'SliceWP_Commission';

	}


	/**
	 * Return the table columns.
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    	=> '%d',
			'affiliate_id'		=> '%d',
			'visit_id'			=> '%d',
			'date_created' 		=> '%s',
			'date_modified' 	=> '%s',
			'type'				=> '%s',
			'status'			=> '%s',
			'reference'			=> '%s',
			'reference_amount'	=> '%s',
			'customer_id'		=> '%d',
			'origin'			=> '%s',
			'amount'			=> '%s',
			'parent_id'			=> '%d',
			'payment_id'		=> '%d',
			'currency'			=> '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Commission objects from the database.
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_commissions( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'		=> 20,
			'offset'		=> 0,
			'orderby'		=> 'id',
			'order'			=> 'DESC',
			'status'		=> '',
			'type'			=> '',
			'affiliate_id'	=> '',
			'visit_id'		=> '',
			'parent_id'		=> '',
			'payment_id'	=> '',
			'customer_id'	=> '',
			'reference'		=> '',
			'origin'		=> '',
			'include'		=> array(),
			'fields'		=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call.
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_commissions_args', $args );

		// Number args.
		if ( $args['number'] < 1 ) {
			$args['number'] = 999999;
		}

		// Join clause.
		$join = '';

		// Where clause.
		$where = "WHERE 1=1";

		// Status where clause.
		if ( ! empty( $args['status'] ) ) {

			if ( is_array( $args['status'] ) ) {

				$statuses = implode( "','", array_map( 'sanitize_text_field', $args['status'] ) );
				$where  .= " AND status IN('{$statuses}')";

			} else {

				$status = sanitize_text_field( $args['status'] );
				$where .= " AND status = '{$status}'";

			}

		}

		// Type where clause.
		if ( ! empty( $args['type'] ) ) {

			if ( is_array( $args['type'] ) ) {

				$types  = implode( "','", array_map( 'sanitize_text_field', $args['type'] ) );
				$where .= " AND type IN('{$types}')";

			} else {

				$type   = sanitize_text_field( $args['type'] );
				$where .= " AND type = '{$type}'";

			}

		}

		// Affiliate ID where clause.
		if ( is_numeric( $args['affiliate_id'] ) ) {

			$affiliate_id = absint( $args['affiliate_id'] );
			$where 		 .= " AND affiliate_id = '{$affiliate_id}'";

		}

		if ( is_array( $args['affiliate_id'] ) && ! empty( $args['affiliate_id'] ) ) {

			$affiliate_ids = implode( ',', array_map( 'absint', $args['affiliate_id'] ) );
			$where   	  .= " AND affiliate_id IN({$affiliate_ids})";

		}


		// Visit ID where clause.
		if ( is_numeric( $args['visit_id'] ) ) {

			$visit_id = absint( $args['visit_id'] );
			$where   .= " AND visit_id = '{$visit_id}'";

		}

		if ( is_array( $args['visit_id'] ) && ! empty( $args['visit_id'] ) ) {

			$visit_ids = implode( ',', array_map( 'absint', $args['visit_id'] ) );
			$where    .= " AND visit_id IN({$visit_ids})";

		}


		// Payment ID where clause.
		if ( is_numeric( $args['payment_id'] ) ) {

			$payment_id = absint( $args['payment_id'] );
			$where 	   .= " AND payment_id = '{$payment_id}'";

		}

		if ( is_array( $args['payment_id'] ) && ! empty( $args['payment_id'] ) ) {

			$payment_ids = implode( ',', array_map( 'absint', $args['payment_id'] ) );
			$where   	.= " AND payment_id IN({$payment_ids})";

		}


		// Customer ID where clause.
		if ( is_numeric( $args['customer_id'] ) ) {

			$customer_id = absint( $args['customer_id'] );
			$where 	    .= " AND customer_id = '{$customer_id}'";

		}

		if ( is_array( $args['customer_id'] ) && ! empty( $args['customer_id'] ) ) {

			$customer_ids = implode( ',', array_map( 'absint', $args['customer_id'] ) );
			$where   	 .= " AND customer_id IN({$customer_ids})";

		}


		// Reference where clause.
		if ( ! empty( $args['reference'] ) ) {

			$reference = sanitize_text_field( $args['reference'] );
			$where 	  .= " AND reference = '{$reference}'";

		}

		// Origin where clause.
		if ( ! empty( $args['origin'] ) ) {

			$origin = sanitize_text_field( $args['origin'] );
			$where .= " AND origin = '{$origin}'";

		}

		// Include date_min filter in where clause.
		if ( ! empty( $args['date_min'] ) ) {

			$date_min =  sanitize_text_field( $args['date_min']);
			$where  .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause.
		if ( ! empty( $args['date_max'] ) ) {

			$date_max =  sanitize_text_field( $args['date_max']);
			$where  .= " AND date_created <= '{$date_max}' ";

		}

		// Parent ID where clause.
		if ( is_numeric( $args['parent_id'] ) ) {

			$parent_id = absint( $args['parent_id'] );
			$where 	  .= " AND parent_id = '{$parent_id}'";

		}

		// Include where clause.
		if ( ! empty( $args['include'] ) ) {

			$include = implode( ',', wp_parse_id_list( $args['include'] ) );
			$where  .= " AND id IN({$include})";

		}

		// Search.
		if ( ! empty( $args['search'] ) ) {

			$search = sanitize_text_field( $args['search'] );

			$user_ids   = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_login LIKE %s OR user_email LIKE %s OR display_name LIKE %s", "%{$search}%", "%{$search}%", "%{$search}%" ) );
			$affiliates = ( ! empty( $user_ids ) ? slicewp_get_affiliates( array( 'user_id' => $user_ids ) ) : array() );

			$affiliate_ids = array();

			foreach( $affiliates as $affiliate ) {

				$affiliate_ids[] = $affiliate->get('id');

			}

			$affiliate_ids = ( ! empty( $affiliate_ids ) ? implode( ',', array_map( 'absint', $affiliate_ids ) ) : 0 );

			$where .= " AND (affiliate_id IN({$affiliate_ids}) OR id LIKE '%%{$search}%%' OR reference LIKE '%%{$search}%%')";

		}

		// Orderby.
		$orderby = sanitize_text_field( $args['orderby'] );

		if ( $args['orderby'] == 'amount' ) {
			$orderby = 'amount+0';
		}

		// Order.
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		// Fields.
		$fields = $this->parse_fields( $args['fields'] );

		// Callback.
		$callback = ( $fields == '*' ? 'slicewp_get_commission' : '' );

		$clauses = compact( 'fields', 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, $callback );

		return $results;

	}


	/**
	 * Returns an array of SliceWP_Commission objects from the database.
	 * 
	 * @deprecated 1.0.58 - No longer used in core and not recommended for external usage.
     * 					    Replaced by SliceWP_Object_DB_Commissions::get_commissions() method,
	 * 						by providing the "fields" argument with the name of the column.
	 * 						For example: slicewp_get_commissions( array( 'fields' => $column_name ) )
     *					    Slated for removal in version 2.0.0
	 *
	 * @param string $column
	 * @param array  $args
	 * @param bool   $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_commissions_column( $column, $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'    		=> 20,
			'offset'    		=> 0,
			'orderby'   		=> 'id',
			'order'     		=> 'DESC',
			'affiliate_id'		=> 0,
			'include'   		=> array()
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call.
		 * 
		 * @deprecated 1.0.58
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_commissions_column_args', $args );

		// Number args.
		if ( $args['number'] < 1 )
			$args['number'] = 999999;

		// Join clause.
		$join = '';

		// Where clause.
		$where = "WHERE 1=1";

		// Status where clause.
		if ( ! empty( $args['status'] ) ) {

			$status = sanitize_text_field( $args['status'] );
			$where .= " AND status = '{$status}'";

		}

		// Include affiliate_id filter in where clause.
		if ( ! empty( $args['affiliate_id'] ) ) {

			$affiliate_id =  sanitize_text_field( $args['affiliate_id']);
			$where  	 .= " AND affiliate_id = '{$affiliate_id}' ";

		}

		// Include date_min filter in where clause.
		if ( ! empty( $args['date_min'] ) ) {

			$date_min =  sanitize_text_field( $args['date_min']);
			$where   .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause.
		if ( ! empty( $args['date_max'] ) ) {

			$date_max =  sanitize_text_field( $args['date_max']);
			$where   .= " AND date_created <= '{$date_max}' ";

		}

		// Include where clause.
		if ( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}

		// Orderby.
		$orderby = sanitize_text_field( $args['orderby'] );

		if ( $args['orderby'] == 'amount' ) {
			$orderby = 'amount+0';
		}

		// Order.
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_column( $column, $clauses, $args );

		return $results;

	}
	
	/**
	 * Creates and updates the database table for the commissions.
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			affiliate_id bigint(20) NOT NULL,
			visit_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			type tinytext NOT NULL,
			status tinytext NOT NULL,
			reference text NOT NULL,
			reference_amount mediumtext NOT NULL,
			customer_id bigint(20) NOT NULL,
			origin mediumtext,
			amount mediumtext NOT NULL,
			parent_id bigint(20) NOT NULL,
			payment_id bigint(20) NOT NULL,
			currency char(3) NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}