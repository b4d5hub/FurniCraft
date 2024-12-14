<?php
/**
 * Abstract class for templates notices
 *
 * @version 1.0.1
 *
 * @package WCBoost\Packages\TemplatesStatus
 */
namespace WCBoost\Packages\TemplatesStatus;

if ( ! class_exists( 'WCBoost\Packages\TemplatesStatus\Templates_Trait' ) ) {
	include_once dirname( __FILE__ ) . '/templates-trait.php';
}

use WCBoost\Packages\TemplatesStatus\Templates_Trait;

/**
 * Class \WCBoost\Packages\TemplatesStatus\Notice
 */
abstract class Notice {
	use Templates_Trait;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'setup_template_paths' ] );
		add_action( 'switch_theme', [ $this, 'reset_notices' ] );

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', [ $this, 'template_files_notice' ] );
		}
	}

	/**
	 * Get the notice name.
	 * Override this method to define the unique name for the notice.
	 *
	 * @version 1.0.0
	 *
	 * @return string
	 */
	protected abstract function get_notice_name();

	/**
	 * Add the paths of template files.
	 * Override this method to add templates path to scan.
	 *
	 * @use add_templates_path()
	 *
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public abstract function setup_template_paths();

	/**
	 * Add notice about outdated templates
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_files_notice() {
		$notice_name = $this->get_notice_name();

		if ( ! $notice_name ) {
			return;
		}

		if ( \get_option( $notice_name . '_check' ) || \WC_Admin_Notices::has_notice( $notice_name ) ) {
			return;
		}

		if ( method_exists( 'WC_Admin_Notices', 'user_has_dismissed_notice' ) && \WC_Admin_Notices::user_has_dismissed_notice( $notice_name ) ) {
			return;
		}

		$has_outdated_templates = $this->check_override_templates( 'outdated' );

		if ( null === $has_outdated_templates ) {
			return;
		}

		if ( isset( $has_outdated_templates['outdated'] ) && $has_outdated_templates['outdated'] ) {
			\WC_Admin_Notices::add_custom_notice( $notice_name, $this->outdated_templates_notice_html() );
		} else {
			\WC_Admin_Notices::remove_notice( $notice_name );

			// Update the option to avoid multiple checkings.
			\update_option( $notice_name . '_check', time(), false );
		}
	}

	/**
	 * Notice html for the outdated templates notification
	 *
	 * @version 1.0.0
	 *
	 * @return string
	 */
	protected function outdated_templates_notice_html() {
		$message = $this->get_notice_message();

		return '<p>' . $message . '</p>';
	}

	/**
	 * Get the notice message.
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	abstract public function get_notice_message();

	/**
	 * Reset all notices
	 *
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function reset_notices() {
		$this->reset_templates_notice();
	}

	/**
	 * Reset outdated templates notice
	 *
	 * @version 1.0.0
	 *
	 * @return void
	 */
	private function reset_templates_notice() {
		$notice_name = $this->get_notice_name();

		// Remove dismissed option from all users.
		delete_metadata( 'user', 0, 'dismissed_' . $notice_name . '_notice', '', true );

		\WC_Admin_Notices::remove_notice( $notice_name );
		\delete_option( $notice_name . '_check' );
	}
}
