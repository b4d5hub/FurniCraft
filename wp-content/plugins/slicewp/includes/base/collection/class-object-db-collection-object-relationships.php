<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the collection object relationships.
 *
 */
Class SliceWP_Object_DB_Collection_Object_Relationships extends SliceWP_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'slicewp_collection_object_relationships';
		$this->primary_key = 'id';
		$this->context 	   = 'collection_object_relationship';

	}


	/**
	 * Return the table columns.
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		     => '%d',
			'collection_id'	 => '%d',
			'object_id'		 => '%d',
			'object_context' => '%s',
			'date_created' 	 => '%s',
			'date_modified'  => '%s'
		);

	}


	/**
	 * Returns an array of collection-object relationship objects from the database.
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_relationships( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'  		=> -1,
			'offset'  		=> 0,
			'orderby' 		=> 'id',
			'order'   		=> 'DESC',
			'collection_id' => '',
			'include' 		=> array(),
			'fields'  		=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		// Number args.
		if ( $args['number'] < 1 ) {
			$args['number'] = 999999;
		}

		// Join clause.
		$join = '';

		// Where clause.
		$where = "WHERE 1=1";

		// Collection IDs where clause.
		if ( is_numeric( $args['collection_id'] ) ) {

			$collection_id = absint( $args['collection_id'] );
			$where 		  .= " AND collection_id = {$collection_id}";

		}

		if ( is_array( $args['collection_id'] ) && ! empty( $args['collection_id'] ) ) {

			$collection_ids = implode( ',', array_map( 'absint', $args['collection_id'] ) );
			$where   	   .= " AND collection_id IN({$collection_ids})";

		}

		// Object ID where clause.
		if ( ! empty( $args['object_id'] ) ) {

			$object_id = absint( $args['object_id'] );
			$where    .= " AND object_id = {$object_id}";

		}

		// Object context where clause.
		if ( ! empty( $args['object_context'] ) ) {

			$object_context = sanitize_text_field( $args['object_context'] );
			$where  	   .= " AND object_context = '{$object_context}'";

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
		$callback = '';

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
			collection_id bigint(20) NOT NULL,
			object_id bigint(20) NOT NULL,
			object_context tinytext NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			PRIMARY KEY  id (id),
			KEY collection_id (collection_id),
			KEY object_id (object_id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}