<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all core database objects.
 *
 */
abstract class SliceWP_Object_DB extends SliceWP_DB {

	/**
	 * Object type to query for
	 *
	 * @access public
	 * @var    string
	 *
	 */
	public $query_object_type = 'stdClass';


	/**
	 * Constructor
	 *
	 * Subclasses should set the $table_name, $primary_key, $context and $query_object_type.
	 *
	 * @access public
	 *
	 */
	public function __construct() {}


	/**
	 * Returns a table row for the given row id.
	 *
	 * @param int $row_id
	 *
	 * @return mixed object|null
	 *
	 */
	public function get( $row_id ) {

		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = %s LIMIT 1", $row_id ) );

		return $row;

	}


	/**
	 * Retrieves results for the given query clauses.
	 *
	 * @param array  $clauses  - an array with SQL ready clauses
	 * @param array  $args 	   - the query args
	 * @param string $callback - a callback to be run against every returned result
	 *
	 * @return mixed array|int|null
	 *
	 */
	protected function get_results( $clauses, $args, $callback = '' ) {

		global $wpdb;

		// Default clauses.
		$default_clauses = array(
			'distinct' => '',
			'fields'   => '*',
			'join'	   => '',
			'where'	   => '1=1',
			'orderby'  => $this->primary_key,
			'order'	   => 'DESC',
			'count'	   => false
		);

		// Merge default clauses with the given ones.
		$clauses = array_merge( $default_clauses, $clauses );

		// Prepare clauses based on given collection queries.
		if ( ! empty( $args['collections'] ) && $this->context !== 'collections' ) {

			$clauses = $this->prepare_query_clauses_for_collections( $clauses, $args['collections'] );

		}

		// Query the results.
		if ( true === $clauses['count'] ) {

			$results = $wpdb->get_var( "SELECT COUNT({$clauses['distinct']} {$this->table_name}.{$this->primary_key}) FROM {$this->table_name} {$clauses['join']} {$clauses['where']}" );

			return absint( $results );

		} else {

			$results = $wpdb->get_results( $wpdb->prepare( "SELECT {$clauses['distinct']} {$clauses['fields']} FROM {$this->table_name} {$clauses['join']} {$clauses['where']} ORDER BY {$clauses['orderby']} {$clauses['order']} LIMIT %d, %d", absint( $args['offset'] ), absint( $args['number'] ) ) );

			$fields = $this->parse_fields( ( ! empty( $args['fields'] ) ) ? $args['fields'] : array() );

			if ( $fields != '*' && false === strpos( $fields, ',' ) ) {
				$results = wp_list_pluck( $results, $fields );
			}
	
			if ( ! empty( $callback ) && is_callable( $callback ) ) {
				$results = array_map( $callback, $results );
			}
	
			return $results;

		}

	}


	/**
	 * Retrieves results for the given query clauses.
	 *
	 * @param array  $column  - a string containing the desired column for selection
	 * @param array  $clauses  - an array with SQL ready clauses
	 * @param array  $args 	   - the query args
	 * @param string $callback - a callback to be run against every returned result
	 *
	 * @return mixed array|int|null
	 *
	 */
	protected function get_column( $column, $clauses, $args, $callback = '' ) {

		global $wpdb;

		if ( true === $clauses['count'] ) {
 
			$results = $wpdb->get_var( "SELECT COUNT({$column}) FROM {$this->table_name} {$clauses['join']} {$clauses['where']}" );

			return absint( $results );

		} else {

			$results = $wpdb->get_col( $wpdb->prepare( "SELECT {$column} FROM {$this->table_name} {$clauses['join']} {$clauses['where']} ORDER BY {$clauses['orderby']} {$clauses['order']} LIMIT %d, %d", absint( $args['offset'] ), absint( $args['number'] ) ) );
			
		}

		if ( ! empty( $callback ) && is_callable( $callback ) ) {
			$results = array_map( $callback, $results );
		}

		return $results;

	}


	/**
	 * Inserts a new row into the database table.
	 *
	 * @param array $data
	 *
	 * @return mixed int|false
	 *
	 */
	public function insert( $data ) {

		global $wpdb;

		/**
		 * Modify the data to be added into a new row just before
		 * the insert procedure
		 *
		 * @param array $data
		 *
		 */
		$data = apply_filters( "slicewp_pre_insert_{$this->context}_data", $data );

		if ( empty( $data ) ) {
			return false;
		}

		$column_formats = $this->get_columns();

		// Make array keys lowercase
		$data = array_change_key_case( $data );

		// Filter out unwanted keys
		$data = array_intersect_key( $data, $column_formats );

		// Strip slashes
		$data = wp_unslash( $data );

		// Make sure the primary key is not included
		if ( isset( $data[ $this->primary_key ] ) ) {
			unset( $data[ $this->primary_key ] );
		}

		/**
		 * Fires just before a new row is to be inserted into the table.
		 *
		 * @param array $data
		 *
		 */
		do_action( 'slicewp_pre_insert_' . $this->context, $data );

		// Arrange column formats to match data elements
		$data_keys 		= array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		// Insert the new row
		$inserted = $wpdb->insert( $this->table_name, $data, $column_formats );

		if ( ! $inserted ) {
			return false;
		}

		$insert_id = $wpdb->insert_id;

		/**
		 * Fires right after a new row has been inserted.
		 *
		 * @param int   $insert_id
		 * @param array $data
		 *
		 */
		do_action( 'slicewp_insert_' . $this->context, $insert_id, $data );

		return $insert_id;

	}


	/**
	 * Updates a row from the database table.
	 *
	 * @param int $row_id
	 *
	 * @return bool
	 *
	 */
	public function update( $row_id, $data ) {

		global $wpdb;

		/**
		 * Modify the data to be updated just before the update procedure.
		 *
		 * @param array $data
		 * @param int   $row_id
		 *
		 */
		$data = apply_filters( "slicewp_pre_update_{$this->context}_data", $data, $row_id );

		if ( empty( $data ) ) {
			return false;
		}

		$row_id = absint( $row_id );

		$column_formats = $this->get_columns();

		// Make array keys lowercase
		$data = array_change_key_case( $data );

		// Filter out unwanted keys
		$data = array_intersect_key( $data, $column_formats );

		// Strip slashes
		$data = wp_unslash( $data );

		// Make sure the primary key is not included
		if ( isset( $data[ $this->primary_key ] ) ) {
			unset( $data[ $this->primary_key ] );
		}

		// Get current object.
		$object = $this->get_object( $row_id );

		/**
		 * Fires just before a new row is updated into the table.
		 *
		 * @param int   $row_id
		 * @param array $data
		 * @param array $current_data
		 *
		 */
		do_action( 'slicewp_pre_update_' . $this->context, $row_id, $data, ( ! is_null( $object ) ? $object->to_array() : array() ) );

		$data_keys 		= array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		// Update
		$updated = $wpdb->update( $this->table_name, $data, array( $this->primary_key => $row_id ), $column_formats );

		if ( false === $updated ) {
			return false;
		}

		/**
		 * Fires right after a row has been updated.
		 *
		 * @param int   $row_id
		 * @param array $data
		 * @param array $old_data
		 *
		 */
		do_action( 'slicewp_update_' . $this->context, $row_id, $data, ( ! is_null( $object ) ? $object->to_array() : array() ) );

		return true;

	}


	/**
	 * Removes a row from the database table.
	 *
	 * @param int $row_id
	 *
	 * @return bool
	 *
	 */
	public function delete( $row_id ) {

		global $wpdb;

		$row_id = absint( $row_id );

		/**
		 * Fires right before a row is removed from the database table.
		 *
		 * @param int $row_id
		 *
		 */
		do_action( 'slicewp_pre_delete_' . $this->context, $row_id );

		// Delete the row
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = %d", $row_id ) );

		if ( false === $deleted ) {
			return false;
		}

		/**
		 * Fires right after the row is removed from the database table.
		 *
		 * @param int $row_id
		 *
		 */
		do_action( 'slicewp_delete_' . $this->context, $row_id );

		return true;

	}


	/**
	 * Returns an instance of the $query_object_type given an object or an id.
	 *
	 * @param mixed int|object
	 *
	 * @return mixed object|null
	 *
	 */
	public function get_object( $object ) {

		if ( ! class_exists( $this->query_object_type ) ) {
			return null;
		}

		if ( $object instanceof $this->query_object_type ) {

			$_object = $object;

		} elseif ( is_object( $object ) ) {

			$_object = new $this->query_object_type( $object );

		} else {

			$object = $this->get( $object );

			if ( is_null( $object ) ) {

				$_object = null;

			} else {

				$_object = new $this->query_object_type( $object );

			}

		}
		
		return $_object;

	}


	/**
	 * Modifies the built query clauses based on the given collection queries.
	 * 
	 * @param array $clauses
	 * @param array $collection_queries
	 * 
	 * @return array
	 * 
	 */
	protected function prepare_query_clauses_for_collections( $clauses, $collection_queries ) {

		$join  = '';
		$where = '';

		$collections_table	 = slicewp()->db['collections']->table_name;
		$relationships_table = slicewp()->db['collection_object_relationships']->table_name;

		$clauses = $this->prepare_query_clauses_for_join( $clauses );

		// Try to determine the needed query.
		foreach ( $collection_queries as $key => $collection_query ) {

			if ( $collection_query[0] == 'in' ) {

				$join .= " JOIN {$relationships_table} AS {$relationships_table}_{$key} ON {$this->table_name}.{$this->primary_key} = {$relationships_table}_{$key}.object_id ";

				$where .= " AND {$relationships_table}_{$key}.object_context = '{$this->context}'";

				if ( $collection_query[1] == 'collection_type' ) {

					$types = "'" . ( is_array( $collection_query[2] ) ? implode( "','", $collection_query[2] ) : $collection_query[2] ) . "'";

					$join .= " JOIN {$collections_table} AS {$collections_table}_{$key} ON {$collections_table}_{$key}.id = {$relationships_table}_{$key}.collection_id AND {$collections_table}_{$key}.object_context = {$relationships_table}_{$key}.object_context";

					$where .= " AND {$collections_table}_{$key}.type IN({$types}) ";

				}

				if ( $collection_query[1] == 'collection_id' ) {

					$ids = ( is_array( $collection_query[2] ) ? implode( ",", array_map( 'absint', $collection_query[2] ) ) : absint( $collection_query[2] ) );

					$where .= " AND {$relationships_table}_{$key}.collection_id IN({$ids}) ";

				}

			}

		}

		$clauses['distinct'] = 'DISTINCT';
		$clauses['join']  	.= $join;
		$clauses['where'] 	.= $where;

		return $clauses;

	}


	/**
	 * Prepares the built query clauses for a table join by prepending the table name before
	 * the table column names.
	 * 
	 * @param array $clauses
	 * 
	 * @return array
	 * 
	 */
	protected function prepare_query_clauses_for_join( $clauses ) {

		// Prepare fields clause.
		if ( ! empty( $clauses['fields'] ) ) {

			$fields = array_map( 'trim', explode( ',', $clauses['fields'] ) );

			foreach ( $fields as $key => $value ) {

				if ( strpos( $value, '.' ) !== false ) {
					continue;
				}

				$fields[$key] = $this->table_name . '.' . $value;

			}

			$clauses['fields'] = implode( ',', $fields );

		}

		// Prepare where clause.
		if ( ! empty( $clauses['where'] ) ) {

			$where_parts = explode( ' ', $clauses['where'] );

			foreach ( $where_parts as $key => $value ) {

				if ( in_array( $value, array_keys( $this->get_columns() ) ) ) {

					$where_parts[$key] = $this->table_name . '.' . $value;

				}

			}

			$clauses['where'] = implode( ' ', $where_parts );

		}

		// Prepare orderby clause.
		if ( ! empty( $clauses['orderby'] ) ) {

			if ( in_array( $clauses['orderby'], array_keys( $this->get_columns() ) ) ) {
				$clauses['orderby'] = $this->table_name . '.' . $clauses['orderby'];
			}

		}

		return $clauses;

	}


	/**
	 * Prepares the given fields for the SQL query.
	 * 
	 * @param string|array $fields
	 * 
	 * @return string
	 * 
	 */
	protected function parse_fields( $fields ) {

		if ( ! is_array( $fields ) ) {
			$fields = (array)$fields;
		}

		$allowed_fields = array_keys( $this->get_columns() );

		foreach ( $fields as $key => $field ) {

			if ( ! in_array( $field, $allowed_fields ) ) {
				unset( $fields[$key] );
			}

		}

		$fields = implode( ',', $fields );

		if ( empty( $fields ) ) {
			$fields = '*';
		}

		return $fields;

	}

}