<?php
/**
 * Integrate with other plugins/themes
 */
namespace WCBoost\Wishlist\Integrations;

defined( 'ABSPATH' ) || exit;

use \WCBoost\Packages\Utilities\Singleton_Trait;

// Include the interface for registering integrations.
include_once( __DIR__ . '/interface.php' );

class Manager {

	use Singleton_Trait;

	/**
	 * Initializes the integrations.
	 */
	protected function __construct() {
		if ( did_action( 'plugins_loaded' ) || doing_action( 'plugins_loaded' ) ) {
			$this->init_integrations();
		} else {
			add_action( 'plugins_loaded', [ $this, 'init_integrations' ] );
		}
	}

	/**
	 * Initializes integrations.
	 *
	 * @return void
	 */
	public function init_integrations() {
		$integrations = [
			[
				'path'       => __DIR__ . '/elementor/elementor.php',
				'class_name' => __NAMESPACE__ . '\Elementor\Elementor',
			]
		];

		foreach ( $integrations as $integration ) {
			if ( ! empty( $integration['path'] ) && file_exists( $integration['path'] ) ) {
				require_once $integration['path'];
			}

			if ( $integration['class_name'] && class_exists( $integration['class_name'] ) ) {
				$this->load_integration( new $integration['class_name']() );
			}
		}
	}

	/**
	 * Load an integration.
	 *
	 * @param Integration_Interface $integration The integration to be loaded.
	 *
	 * @return void
	 */
	public function load_integration( Integration_Interface $integration ) {
		if ( $integration->should_load() ) {
			$integration->load();
		}
	}
}
