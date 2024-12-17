<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for admin dashboard cards objects.
 *
 */
abstract class SliceWP_Admin_Dashboard_Card {

	/**
	 * The card's slug.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $slug;

	/**
	 * The card's name.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name;

	/**
	 * The card's context.
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $context;


	/**
	 * Constructor.
	 *
	 */
	public function __construct() {

		$this->init();

		add_action( 'slicewp_view_dashboard_top', array( $this, 'add_meta_box' ) );

	}


	/**
	 * Adds the metabox.
	 *
	 */
	public function add_meta_box() {

		add_meta_box( $this->slug, $this->name, array( $this, 'output' ), 'slicewp_page_slicewp-dashboard', $this->context, 'default' );

	}


	/**
	 * Initializer. Needs to be overwritten by subclasses.
	 *
	 */
	protected function init() {}


	/**
	 * Content output inside the card. Needs to be overwritten by subclasses.
	 *
	 */
	public function output() {}

}