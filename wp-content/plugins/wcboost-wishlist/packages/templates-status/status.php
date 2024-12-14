<?php
/**
 * Template status class
 *
 * @version 1.0.2
 *
 * @package WCBoost\Packages\TemplatesStatus
 */
namespace WCBoost\Packages\TemplatesStatus;

if ( ! trait_exists( 'WCBoost\Packages\Utilities\Singleton_Trait' ) ) {
	include_once dirname( __FILE__, 2 ) . '/utilities/singleton-trait.php';
}

if ( ! trait_exists( 'WCBoost\Packages\TemplatesStatus\Templates_Trait' ) ) {
	include_once dirname( __FILE__ ) . '/templates-trait.php';
}

use WCBoost\Packages\TemplatesStatus\Templates_Trait;
use WCBoost\Packages\Utilities\Singleton_Trait;

/**
 * Class \WCBoost\Packages\TemplatesStatus\Status
 */
class Status {
	use Singleton_Trait;
	use Templates_Trait;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'pre_set_transient_wc_system_status_theme_info', [ $this, 'theme_templates_info' ] );
	}

	/**
	 * Get the updated theme info with custom WooCommerce templates provided by WCBoost Wishlist
	 *
	 * @since  1.0.0
	 * @param  array $info
	 *
	 * @return array
	 */
	public function theme_templates_info( $info ) {
		$templates_info = $this->check_override_templates();

		if ( null === $templates_info ) {
			return $info;
		}

		// Update the 'has_outdated_templates' status only if
		// the theme contains plugins' templates.
		if ( $templates_info['outdated'] ) {
			$info['has_outdated_templates'] = true;
		}

		// Merge the override templates array.
		if ( ! empty( $templates_info['files'] ) ) {
			$info['overrides'] = array_merge( $info['overrides'], $templates_info['files'] );
		}

		return $info;
	}
}
