<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Payout
 *
 */
class SliceWP_Payout extends SliceWP_Base_Object {

	/**
	 * The id of the payout.
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The ID of the user that generated the payout.
	 * 
	 * @access protected
	 * @var    int
	 * 
	 */
	protected $originator_user_id;
    
    /**
	 * The date when the payout was created.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the payout was last modified.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The total amount of the payments from the payout.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $amount;

	/**
	 * The admin that generated the payout.
	 * 
	 * @deprecated This attribute was deprecated in favor of "originator_user_id".
	 *
	 * @access protected
	 * @var    int
	 *
	 */
    protected $admin_id;
	
}