<?php
/**
 * Handle admin notices
 *
 * @since 1.1.0
 */
namespace WCBoost\Wishlist\Admin;

use WCBoost\Packages\TemplatesStatus\Notice as Templates_Status_Notice;
use WCBoost\Wishlist\Plugin;

/**
 * Admin/Notices class
 */
class Templates_Notice extends Templates_Status_Notice {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wcboost_wishlist_installed', [ $this, 'reset_notices' ] );
		register_deactivation_hook( WCBOOST_WISHLIST_FILE, [ $this, 'reset_notices' ] );
	}

	/**
	 * Get the notice name
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	protected function get_notice_name() {
		return 'wcboost_wishlist_templates';
	}

	/**
	 * Set the path to template files
	 *
	 * @return void
	 */
	public function setup_template_paths() {
		$this->add_templates_path( 'WCBoost - Wishlist', Plugin::instance()->plugin_path() . '/templates/' );
	}

	/**
	 * Notice html for the outdated templates notification
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_notice_message() {
		$theme = wp_get_theme();

		/* translators: %s Theme name */
		return sprintf( __( '<strong>Your theme (%s) contains outdated copies of some template files from the WBoost - Wishlist plugin.</strong> These files may need updating to ensure they are compatible with the current version of WCBoost - Wishlist.', 'wcboost-wishlist' ), esc_html( $theme['Name'] ) );
	}
}
