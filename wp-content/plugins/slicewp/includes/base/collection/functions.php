<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the collection.
 *
 */
function slicewp_include_files_collection() {

	// Get collection dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main collection class.
	if ( file_exists( $dir_path . 'class-collection.php' ) ) {
		include $dir_path . 'class-collection.php';
	}

	// Include the db layer classes.
	if ( file_exists( $dir_path . 'class-object-db-collections.php' ) ) {
		include $dir_path . 'class-object-db-collections.php';
	}

	if ( file_exists( $dir_path . 'class-object-meta-db-collections.php' ) ) {
		include $dir_path . 'class-object-meta-db-collections.php';
	}

    if ( file_exists( $dir_path . 'class-object-db-collection-object-relationships.php' ) ) {
		include $dir_path . 'class-object-db-collection-object-relationships.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_collection' );


/**
 * Register the class that handles database queries for the collection.
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_collections( $classes ) {

	$classes['collections']                     = 'SliceWP_Object_DB_Collections';
    $classes['collectionmeta']                  = 'SliceWP_Object_Meta_DB_Collections';
    $classes['collection_object_relationships'] = 'SliceWP_Object_DB_Collection_Object_Relationships';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_collections' );


/**
 * Returns an array with SliceWP_Collection objects from the database.
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_collections( $args = array(), $count = false ) {

	$collections = slicewp()->db['collections']->get_collections( $args, $count );

	/**
	 * Add a filter hook just before returning.
	 *
	 * @param array $collections
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_collections', $collections, $args, $count );

}


/**
 * Gets a collection from the database.
 *
 * @param mixed int|object - collection id or object representing the collection.
 *
 * @return SliceWP_Collection|false
 *
 */
function slicewp_get_collection( $collection ) {

	return slicewp()->db['collections']->get_object( $collection );

}


/**
 * Inserts a new collection into the database.
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_collection( $data ) {

	return slicewp()->db['collections']->insert( $data );

}


/**
 * Updates a collection from the database.
 *
 * @param int 	$collection_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_collection( $collection_id, $data ) {

	return slicewp()->db['collections']->update( $collection_id, $data );

}


/**
 * Deletes a collection from the database.
 * Deletes the collection's metadata.
 * Deletes the relationships between the deleted collection and the connected objects.
 * 
 * @param int $collection_id
 * 
 * @return bool
 * 
 */
function slicewp_delete_collection( $collection_id ) {

    $deleted = slicewp()->db['collections']->delete( $collection_id );

	if ( ! $deleted ) {
		return false;
	}

	$collection_meta = slicewp_get_collection_meta( $collection_id );

	// Delete the collection's metadata.
	if ( ! empty( $collection_meta ) ) {

		foreach ( $collection_meta as $key => $value ) {

			slicewp_delete_collection_meta( $collection_id, $key );

		}

	}

    // Delete the relationships.
    $relationships = slicewp()->db['collection_object_relationships']->get_relationships( array( 'number' => -1, 'collection_id' => $collection_id ) );

    foreach ( $relationships as $relationship ) {

        slicewp()->db['collection_object_relationships']->delete( $relationship->id );

    }

	return true;

}


/**
 * Inserts a new meta entry for the collection.
 *
 * @param int    $collection_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function slicewp_add_collection_meta( $collection_id, $meta_key, $meta_value, $unique = false ) {

	return slicewp()->db['collectionmeta']->add( $collection_id, $meta_key, $meta_value, $unique );

}


/**
 * Updates a meta entry for the collection.
 *
 * @param int    $collection_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function slicewp_update_collection_meta( $collection_id, $meta_key, $meta_value, $prev_value = '' ) {

	return slicewp()->db['collectionmeta']->update( $collection_id, $meta_key, $meta_value, $prev_value );

}


/**
 * Returns a meta entry for the collection.
 *
 * @param int    $collection_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function slicewp_get_collection_meta( $collection_id, $meta_key = '', $single = false ) {

	return slicewp()->db['collectionmeta']->get( $collection_id, $meta_key, $single );

}


/**
 * Removes a meta entry for the collection.
 *
 * @param int    $collection_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function slicewp_delete_collection_meta( $collection_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return slicewp()->db['collectionmeta']->delete( $collection_id, $meta_key, $meta_value, $delete_all );

}


/**
 * Returns all available collection types.
 *
 * @return array
 *
 */
function slicewp_get_collection_types() {

	$collection_types = array();

	/**
	 * Filter to register more collection types.
	 *
	 * @param array $collection_types
	 *
	 */
	$collection_types = apply_filters( 'slicewp_register_collection_types', $collection_types );

	return $collection_types;

}


/**
 * Returns the collections associated to the object.
 * 
 * @param int             $object_id
 * @param string          $object_context
 * @param string|string[] $collection_type
 * @param array           $args
 * 
 * @return SliceWP_Collection[]
 * 
 */
function slicewp_get_object_collections( $object_id, $object_context, $collection_type = '', $args = array() ) {

    // Get the collection IDs associated with the object based on the relationships of the object.
    $query_args = array(
        'number'         => -1,
        'object_id'      => absint( $object_id ),
        'object_context' => sanitize_text_field( $object_context ),
        'fields'         => 'collection_id'
    );

    $relationships_collection_ids = slicewp()->db['collection_object_relationships']->get_relationships( $query_args );

    // If no relationships are found, return.
    if ( empty( $relationships_collection_ids ) ) {
        return array();
    }

    // Get the actual collections based on the IDs retrieved above.
    $query_args = array(
        'number'         => -1,
        'include'        => $relationships_collection_ids,
        'object_context' => $object_context,
        'type'           => ( ! empty( $collection_type ) ? $collection_type : '' )
    );

    $object_collections = slicewp()->db['collections']->get_collections( array_merge( $query_args, $args ) );

    return $object_collections;

}


/**
 * Inserts/updates the relationships between the given object and the collection ids.
 * 
 * @param int    $object_id
 * @param string $object_context
 * @param string $collection_type
 * @param bool   $append
 * 
 * @return bool
 * 
 */
function slicewp_set_object_collections( $object_id, $object_context, $collection_type, $collection_ids, $append = false ) {

    if ( empty( $collection_type ) || ! is_string( $collection_type ) ) {
        return false;
    }

    // Check for collection type.
    $collection_types     = slicewp_get_collection_types();
    $collection_type_data = array();

    foreach ( $collection_types as $_collection_type_data ) {

        if ( $_collection_type_data['slug'] == $collection_type && $_collection_type_data['object_context'] == $object_context ) {

            $collection_type_data = $_collection_type_data;
            break;

        }

    }

    if ( empty( $collection_type_data ) ) {
        return false;
    }

    // Transform collection IDs to array.
    $collection_ids = array_filter( array_map( 'absint', (array)$collection_ids ) );

    // Get the currently set relationship collection IDs.
    $query_args = array(
        'number'         => -1,
        'object_id'      => $object_id,
        'object_context' => $object_context,
        'type'           => $collection_type,
        'fields'         => 'id'
    );

    $all_collection_ids = slicewp()->db['collections']->get_collections( $query_args );

    // If there are no collections of this type for the object, return.
    if ( empty( $all_collection_ids ) ) {
        return false;
    }

    // Filter out the collection IDs that do not match the valid IDs for the given object and collection type.
    $_collection_ids = array_intersect( $all_collection_ids, $collection_ids );

    // If there are collection IDs supplied, but none are valid, return.
    // This return is necessary, because we will remove all invalid IDs from the given IDs. If all are removed,
    // we want to avoid detaching all relationships from the object, which is what we do when an empty array of IDs is supplied.
    if ( ! empty( $collection_ids ) && empty( $_collection_ids ) ) {
        return false;
    }

    // Set only the valid collection IDs.
    $collection_ids = $_collection_ids;

    // If the collection type supports only single relationships, but there are more collections supplied, return.
    if ( $collection_type_data['object_relationships'] == 'single' && count( $collection_ids ) > 1 ) {
        return false;
    }

    $query_args = array(
        'number'         => -1,
        'object_id'      => $object_id,
        'object_context' => $object_context,
        'collection_id'  => $all_collection_ids,
        'fields'         => 'collection_id'
    );

    $current_attached_collection_ids = slicewp()->db['collection_object_relationships']->get_relationships( $query_args );

    // If there are attached collections, return if the call is to append new collections when the collection type is single.
    if ( ! empty( $current_attached_collection_ids ) && ! empty( $collection_ids ) && $append && $collection_type_data['object_relationships'] == 'single' ) {
        return false;
    }

    // Remove all relationships if empty array given and we don't append.
    if ( empty( $collection_ids ) && ! $append ) {

        slicewp_remove_object_collections( $object_id, $object_context, $collection_type, $current_attached_collection_ids );

    }

    // Add and remove relationships based on the given collection IDs.
    if ( ! empty( $collection_ids ) ) {

        // Filter which collection IDs to remove and which to add as relationships.
        $collection_ids_to_remove = ( ! $append ? array_diff( $current_attached_collection_ids, $collection_ids ) : array() );
        $collection_ids_to_add    = array_diff( $collection_ids, $current_attached_collection_ids );

        // Remove relationships.
        if ( ! empty( $collection_ids_to_remove ) ) {
            slicewp_remove_object_collections( $object_id, $object_context, $collection_type, $collection_ids_to_remove );
        }

        // Add relationships.
        if ( ! empty( $collection_ids_to_add ) ) {

            foreach ( $collection_ids_to_add as $collection_id ) {

                $data = array(
                    'collection_id'  => absint( $collection_id ),
                    'object_id'      => absint( $object_id ),
                    'object_context' => sanitize_text_field( $object_context ),
                    'date_created'   => slicewp_mysql_gmdate()
                );

                slicewp()->db['collection_object_relationships']->insert( $data );

            }
            
        }

    }

    return true;

}


/**
 * Removes the relationships between the given object and the given collections.
 * 
 * @param int             $object_id
 * @param string          $object_context
 * @param string|string[] $collection_type
 * @param int|int[]       $collection_ids
 * 
 * @return bool
 * 
 */
function slicewp_remove_object_collections( $object_id, $object_context, $collection_type, $collection_ids ) {

    if ( empty( $collection_ids ) ) {
        return false;
    }

    // Filter out collection IDs that don't match the object context and collection type.
    $query_args = array(
        'number'         => -1,
        'object_context' => $object_context,
        'type'           => ( ! empty( $collection_type ) ? $collection_type : '' ),
        'include'        => $collection_ids,
        'fields'         => 'id'
    );

    $collection_ids = slicewp()->db['collections']->get_collections( $query_args );

    // Get all relationships based on the cleaned-up collection IDs.
    $query_args = array(
        'number'         => -1,
        'object_id'      => absint( $object_id ),
        'object_context' => sanitize_text_field( $object_context ),
        'collection_id'  => $collection_ids
    );

    $relationships = slicewp()->db['collection_object_relationships']->get_relationships( $query_args );

    // Remove the found relationships.
    foreach ( $relationships as $relationship ) {

        slicewp()->db['collection_object_relationships']->delete( $relationship->id );

    }

    return true;

}