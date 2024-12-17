<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class that handles the different tracking done bt the plugin
 *
 */
class SliceWP_Tracking {

	/**
	 * Constructor.
	 *
	 * Handles the needed hooks and initializes the entire tracking process.
	 *
	 */
	public function __construct() {

		// Enqueue scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register callback for the AJAX visit register.
		add_action( 'wp_ajax_nopriv_slicewp_register_visit', array( $this, 'register_visit' ) );
		add_action( 'wp_ajax_slicewp_register_visit', array( $this, 'register_visit' ) );

	}


	/**
	 * Enqueues the needed scripts for the tracking.
	 *
	 */
	public function enqueue_scripts() {

		wp_register_script( 'slicewp-script-tracking', SLICEWP_PLUGIN_DIR_URL . 'assets/js/script-trk.js', array(), SLICEWP_VERSION );
		wp_enqueue_script( 'slicewp-script-tracking' );

		$script  = 'var slicewp_ajaxurl = ' . json_encode( admin_url( 'admin-ajax.php' ) ) . ';' . PHP_EOL;
		$script .= 'var slicewp = ' . json_encode( slicewp_get_global_script_vars() ) . ';';
		
		wp_add_inline_script( 'slicewp-script-tracking', $script, 'before' );

	}


	/**
	 * Callback for the AJAX register visit call.
	 *
	 */
	public function register_visit() {

		// Make sure the affiliate variable is set.
		if ( empty( $_POST['aff'] ) ) {
			wp_die( json_encode( array( 'success' => -1 ) ) );
		}

		$affiliate = $this->get_affiliate_by_query_arg( sanitize_text_field( $_POST['aff'] ) );

		if ( is_null( $affiliate ) ) {
			wp_die( json_encode( array( 'success' => -2 ) ) );
		}

		if ( $affiliate->get('status') != 'active' ) {
			wp_die( json_encode( array( 'success' => -3 ) ) );
		}

		$visit_data = array(
			'affiliate_id'  => absint( $affiliate->get('id') ),
			'landing_url'   => ( ! empty( $_POST['landing_url'] ) ? slicewp_sanitize_visit_landing_url( esc_url_raw( $_POST['landing_url'] ) ) : '' ),
			'referrer_url'  => ( ! empty( $_POST['referrer_url'] ) ? esc_url_raw( $_POST['referrer_url'] ) : '' ),
			'ip_address'    => slicewp_get_user_ip_address(),
			'date_created'  => slicewp_mysql_gmdate(),
			'date_modified' => slicewp_mysql_gmdate()
		);
		
		$inserted = slicewp_insert_visit( $visit_data );

		if ( ! $inserted ) {

			wp_die( json_encode( array( 'success' => -4 ) ) );

		} else {

			$return = array(
				'success'	   => 1,
				'affiliate_id' => absint( $affiliate->get('id') ),
				'visit_id'	   => $inserted
			);

			wp_die( json_encode( $return ) );

		}

	}


	/**
	 * Returns the affiliate object if it finds it for the given "aff" custom query argument.
	 *
	 * @param mixed $value
	 *
	 * @return SliceWP_Affiliate|null
	 *
	 */
	protected function get_affiliate_by_query_arg( $value ) {

		$affiliate 	  = null;
		$affiliate_id = ( is_numeric( $value ) ? absint( $value ) : 0 );

		// Try to get the affiliate by the ID.
		if ( ! empty( $affiliate_id ) ) {

			$affiliate = slicewp_get_affiliate( $affiliate_id );

		}

		/**
		 * Filter the value just before returning it.
		 *
		 * @param SliceWP_Affiliate|null $affiliate
		 * @param string 			 	 $value
		 *
		 */
		$affiliate = apply_filters( 'slicewp_tracking_get_affiliate_by_query_arg', $affiliate, $value );

		return $affiliate;

	}


	/**
	 * Returns the affiliate_id of the referrer saved in the cookie.
	 *
	 * @return null|int
	 *
	 */
	public function get_referrer_affiliate_id() {

		if ( empty( $_COOKIE['slicewp_aff'] ) ) {
			return null;
		}

		return absint( $_COOKIE['slicewp_aff'] );

	}


	/**
	 * Returns the visit_id of the referrer saved in the cookie.
	 *
	 * @return null|int
	 *
	 */
	public function get_referrer_visit_id() {

		if ( empty( $_COOKIE['slicewp_visit'] ) ) {
			return null;
		}

		return absint( $_COOKIE['slicewp_visit'] );

	}

}