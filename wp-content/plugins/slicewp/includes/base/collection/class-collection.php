<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Collection.
 *
 */
class SliceWP_Collection extends SliceWP_Base_Object {

    /**
	 * The Id of the collection.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The object context/type that the collection is tied to.
	 * 
	 * @access protected
	 * @var	   string
	 * 
	 */
    protected $object_context;

	/**
	 * The collection's type.
	 * 
	 * @access protected
	 * @var	   string
	 * 
	 */
    protected $type;

	/**
	 * The collection's name.
	 * 
	 * @access protected
	 * @var	   string
	 * 
	 */
    protected $name;

	/**
	 * The date when the collection was created.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the collection was last modified.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

}