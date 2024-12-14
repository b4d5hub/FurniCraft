<?php
/**
 * Singleton class trait.
 *
 * @version 1.0.0
 *
 * @package WCBoost\Packages\Utilities
 */

namespace WCBoost\Packages\Utilities;

/**
 * Singleton trait.
 */
trait Singleton_Trait {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	final public function __wakeup() {
		// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '1.0' );
		die();
	}
}
