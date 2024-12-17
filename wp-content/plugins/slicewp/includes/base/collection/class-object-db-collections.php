<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Collection.
 *
 */
Class SliceWP_Object_DB_Collections extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'slicewp_collections';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'collection';
		$this->query_object_type = 'SliceWP_Collection';

	}


	/**
	 * Return the table columns.
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		     => '%d',
			'object_context' => '%s',
			'type'			 => '%s',
			'name'			 => '%s',
			'date_created' 	 => '%s',
			'date_modified'  => '%s'
		);

	}


	/**
	 * Returns an array of SliceWP_Collection objects from the database.
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_collections( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'  	=> 20,
			'offset'  	=> 0,
			'orderby' 	=> 'id',
			'order'   	=> 'DESC',
			'name'		=> '',
			'include' 	=> array(),
			'fields'  	=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call.
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'slicewp_get_collections_args', $args );

		// Number args.
		if ( $args['number'] < 1 ) {
			$args['number'] = 999999;
		}

		// Join clause.
		$join = '';

		// Where clause.
		$where = "WHERE 1=1";

		// Object context where clause.
		if ( ! empty( $args['object_context'] ) ) {

			$object_context = sanitize_text_field( $args['object_context'] );
			$where  	   .= " AND object_context = '{$object_context}'";

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

		// Name where clause.
		if ( ! empty( $args['name'] ) ) {

			$name   = sanitize_text_field( $args['name'] );
			$where .= " AND name = '{$name}'";

		}

		// Include where clause.
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

		// Orderby.
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order.
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		// Fields.
		$fields = $this->parse_fields( $args['fields'] );

		// Callback.
		$callback = ( $fields == '*' ? 'slicewp_get_collection' : '' );

		$clauses = compact( 'fields', 'where', 'join', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, $callback );

		return $results;

	}


	/**
	 * Creates and updates the database table for the collections.
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			object_context tinytext NOT NULL,
			type tinytext NOT NULL,
			name mediumtext NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}