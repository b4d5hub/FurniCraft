<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Customer
 *
 */
class SliceWP_Customer extends SliceWP_Base_Object {

	/**
	 * The Id of the customer
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The id of the user to which this customer is assigned
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $user_id;

	/**
	 * The email of the customer
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $email;

	/**
	 * The first name of the customer
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $first_name;

	/**
	 * The last name of the customer
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $last_name;

	/**
	 * The ID of the affiliate that referred the customer.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $affiliate_id;

	/**
	 * The date when the customer was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the customer was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;
	
}