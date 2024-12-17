<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class that handles admin notifications displayed in SliceWP's dashboard.
 *
 */
Class SliceWP_Admin_Dashboard_Notifications {

	/**
	 * The current instance of the object
	 *
	 * @access private
	 * @var    SliceWP_Admin_Dashboard_Notifications
	 *
	 */
	private static $instance;

	/**
	 * List of notifications that have been registered
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $notifications = array();


	/**
	 * Returns an instance of the object
	 *
	 * @return SliceWP_Admin_Dashboard_Notifications
	 *
	 */
	public static function instance() {

		if( ! isset( self::$instance ) && ! ( self::$instance instanceof SliceWP_Admin_Dashboard_Notifications ) )
			self::$instance = new SliceWP_Admin_Dashboard_Notifications;

		return self::$instance;

	}


	/**
	 * Adds a new notification to the $notices property
	 *
	 * @param string $slug
	 * @param string $message
	 *
	 */
	public function register_notification( $slug, $message ) {

		$this->notifications[$slug] = array(
			'message' => $message
		);

	}


	/**
	 * Returns all notifications.
	 *
	 * @return array
	 *
	 */
	public function get_notifications() {

		return $this->notifications;

	}


	/**
	 * Returns a notification based on the given slug.
	 *
	 * @param string $slug
	 *
	 * @return array|null
	 *
	 */
	public function get_notification( $slug ) {

		return ( ! empty( $this->notifications[$slug] ) ? $this->notifications[$slug] : null );

	}


	/**
	 * Returns the number of registered notifications.
	 *
	 * @return int
	 *
	 */
	public function notifications_count() {

		return count( $this->notifications );

	}


}


/**
 * Returns the instance of SliceWP_Admin_Dashboard_Notifications
 *
 * @return SliceWP_Admin_Dashboard_Notifications
 *
 */
function slicewp_admin_dashboard_notifications() {

	return SliceWP_Admin_Dashboard_Notifications::instance();

}

slicewp_admin_dashboard_notifications();