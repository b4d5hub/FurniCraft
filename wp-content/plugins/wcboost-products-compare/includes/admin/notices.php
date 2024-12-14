<?php
/**
 * Admin notices
 *
 * @version 1.0.4
 */
namespace WCBoost\ProductsCompare\Admin;

use WCBoost\ProductsCompare\Plugin;

/**
 * Admin notices class
 */
class Notices {

	const TEMPLATES_NOTICE_NAME = 'wcboost_products_compare_templates';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'switch_theme', [ $this, 'reset_notices' ] );
		add_action( 'wcboost_products_compare_installed', [ $this, 'reset_notices' ] );
		add_action( 'admin_init', [ $this, 'reset_notices_on_request' ] );

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', [ $this, 'template_files_notice' ] );
		}
	}

	/**
	 * Add notice about outdated templates
	 *
	 * @return void
	 */
	public function template_files_notice() {
		if ( \get_option( static::TEMPLATES_NOTICE_NAME . '_check' ) || \WC_Admin_Notices::has_notice( static::TEMPLATES_NOTICE_NAME ) ) {
			return;
		}

		if ( method_exists( 'WC_Admin_Notices', 'user_has_dismissed_notice' ) && \WC_Admin_Notices::user_has_dismissed_notice( static::TEMPLATES_NOTICE_NAME ) ) {
			return;
		}

		$core_templates = \WC_Admin_Status::scan_template_files( Plugin::instance()->plugin_path() . '/templates/' );
		$outdated       = false;

		foreach ( $core_templates as $file ) {
			$theme_file = false;

			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
			}

			if ( false !== $theme_file ) {
				$core_version  = \WC_Admin_Status::get_file_version( Plugin::instance()->plugin_path() . '/templates/' . $file );
				$theme_version = \WC_Admin_Status::get_file_version( $theme_file );

				if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
					$outdated = true;
					break;
				}
			}
		}

		if ( $outdated ) {
			\WC_Admin_Notices::add_custom_notice( static::TEMPLATES_NOTICE_NAME, $this->outdated_templates_notice_html() );
		} else {
			\WC_Admin_Notices::remove_notice( static::TEMPLATES_NOTICE_NAME );

			// Update the option to avoid multiple checkings.
			\update_option( static::TEMPLATES_NOTICE_NAME . '_check', time(), false );
		}
	}

	/**
	 * Notice html for the outdated templates notification
	 *
	 * @return string
	 */
	protected function outdated_templates_notice_html() {
		$theme = wp_get_theme();

		/* translators: %s Theme name */
		return '<p>' . sprintf( __( '<strong>Your theme (%s) contains outdated copies of some template files of WBoost - Products Compare.</strong> These files may need updating to ensure they are compatible with the current version of WCBoost - Products Compare.', 'wcboost-products-compare' ), esc_html( $theme['Name'] ) ) . '</p>';
	}

	/**
	 * Reset all notices
	 *
	 * @return void
	 */
	public function reset_notices() {
		$this->reset_templates_notice();
	}

	/**
	 * Reset notices when users peform a tool
	 *
	 * @return void
	 */
	public function reset_notices_on_request() {
		// Reset templates notice.
		if ( isset( $_GET['action'] ) && 'clear_template_cache' == $_GET['action'] ) {
			$this->reset_templates_notice();
		}
	}

	/**
	 * Reset outdated templates notice
	 *
	 * @return void
	 */
	private function reset_templates_notice() {
		// Remove dismissed option from all users.
		delete_metadata( 'user', 0, 'dismissed_' . static::TEMPLATES_NOTICE_NAME . '_notice', '', true );

		\WC_Admin_Notices::remove_notice( static::TEMPLATES_NOTICE_NAME );
		\delete_option( static::TEMPLATES_NOTICE_NAME . '_check' );
	}
}

new Notices();
