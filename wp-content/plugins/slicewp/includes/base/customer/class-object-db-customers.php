<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Customer
 *
 */
Class SliceWP_Object_DB_Customers extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_customers';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'customer';
		$this->query_object_type = 'SliceWP_Customer';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'user_id'		=> '%d',
			'email'			=> '%s',
			'first_name'	=> '%s',
			'last_name'		=> '%s',
			'affiliate_id'	=> '%d',
			'date_created' 	=> '%s',
			'date_modified' => '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Customer objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_customers( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'orderby'      => 'id',
			'order'        => 'DESC',
			'user_id'	   => '',
			'email'		   => '',
			'affiliate_id' => '',
			'include'      => array(),
			'fields'	   => ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_customers_args', $args );

		// Number args
		if ( $args['number'] < 1 )
			$args['number'] = 999999;

		// Join clause
		$join = '';

		// Where clause
		$where = "WHERE 1=1";

		// User ID where clause
		if ( ! empty( $args['user_id'] ) ) {

			if ( is_array( $args['user_id'] ) ) {

				$user_ids = implode( ',', array_map( 'absint', $args['user_id'] ) );
				$where   .= " AND user_id IN({$user_ids})";

			} else {

				$user_id = absint( $args['user_id'] );
				$where  .= " AND user_id = '{$user_id}'";

			}

		}

		// Email where clause
		if ( ! empty( $args['email'] ) ) {

			$email = sanitize_text_field( $args['email'] );
			$where  .= " AND email = '{$email}'";

		}

		// Affiliate ID where clause.
		if ( ! empty( $args['affiliate_id'] ) ) {

			if ( is_array( $args['affiliate_id'] ) ) {

				$affiliate_ids = implode( ',', array_map( 'absint', $args['affiliate_id'] ) );
				$where   	  .= " AND affiliate_id IN({$affiliate_ids})";

			} else {

				$affiliate_id = absint( $args['affiliate_id'] );
				$where  	 .= " AND affiliate_id = '{$affiliate_id}'";

			}

		}

		// Include where clause
		if ( ! empty( $args['include'] ) ) {

			$include = implode( ',', wp_parse_id_list( $args['include'] ) );
			$where  .= " AND id IN({$include})";

		}
		
		// Include date_min filter in where clause
		if ( ! empty( $args['date_min'] )) {

			$date_min =  sanitize_text_field( $args['date_min'] );
			$where  .= " AND date_created >= '{$date_min}' ";

		}

		// Include date_max filter in where clause
		if ( ! empty( $args['date_max'] )) {

			$date_max =  sanitize_text_field( $args['date_max'] );
			$where  .= " AND date_created <= '{$date_max}' ";

		}
		
		// Default orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		// Fields.
		$fields = $this->parse_fields( $args['fields'] );

		// Callback.
		$callback = ( $fields == '*' ? 'slicewp_get_customer' : '' );

		$clauses = compact( 'fields', 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, $callback );

		return $results;

	}


	/**
	 * Creates and updates the database table for the customers
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			email mediumtext NOT NULL,
			first_name mediumtext NOT NULL,
			last_name mediumtext NOT NULL,
			affiliate_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}