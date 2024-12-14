<?php
/**
 * Integrate with Elementor
 */
namespace WCBoost\Wishlist\Integrations\Elementor;

defined( 'ABSPATH' ) || exit;

use \WCBoost\Wishlist\Plugin;
use \WCBoost\Wishlist\Frontend;
use \WCBoost\Wishlist\Integrations\Integration_Interface;

/**
 * Class Elementor Integration
 */
class Elementor implements Integration_Interface {

	/**
	 * Determine if the integration should be loaded
	 *
	 * @return bool
	 */
	public function should_load() {
		return did_action( 'elementor/loaded' );
	}

	/**
	 * Load the integration
	 *
	 * @return void
	 */
	public function load() {
		add_action( 'load-post.php', [ $this, 'load_frontend_hooks' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}


	/**
	 * Loads frontend hooks when Elementor is being edited.
	 *
	 * @return void
	 */
	public function load_frontend_hooks() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === wp_unslash( $_REQUEST['action'] ) ) {
			Frontend::instance()->template_hooks();
		}
	}

	/**
	 * Enqueue editor styles
	 *
	 * @return void
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'wcboost-wishlist-elementor-editor',
			plugin_dir_url( WCBOOST_WISHLIST_FILE ) . 'assets/css/elementor-editor.css',
			[],
			Plugin::instance()->version
		);
	}

	/**
	 * Add category
	 *
	 * @param  \Elementor\Elements_Manager $elements_manager
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'wcboost',
			[
				'title' => 'WCBoost',
			]
		);
	}

	/**
	 * Register widgets
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/wishlist-widget.php';
		require_once __DIR__ . '/wishlist-button-widget.php';

		$widgets_manager->register( new \WCBoost\Wishlist\Integrations\Elementor\Wishlist_Widget() );
		$widgets_manager->register( new \WCBoost\Wishlist\Integrations\Elementor\Wishlist_Button_Widget() );
	}
}
