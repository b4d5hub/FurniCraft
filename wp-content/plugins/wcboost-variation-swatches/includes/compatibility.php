<?php
/**
 * Compatible with other plugins/themes
 */
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

class Compatibility {
	/**
	 * The single instance of the class
	 *
	 * @var WCBoost\VariationSwatches\Compatibility
	 */
	protected static $_instance = null;

	/**
	 * Main instance
	 *
	 * @return WCBoost\VariationSwatches\Compatibility
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'check_compatible_hooks' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'add_css_to_themes' ], 100 );
	}

	/**
	 * Check compatibility with other plugins/themes and add hooks
	 */
	public function check_compatible_hooks() {
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			add_filter( 'wcboost_variation_swatches_translate_term_meta', [ $this, 'translate_term_meta' ], 10, 3 );
		}
	}

	/**
	 * Add custom CSS to themes.
	 *
	 * @since 1.0.15
	 *
	 * @return void
	 */
	public function add_css_to_themes() {
		// Support the Sober theme.
		wp_add_inline_style( 'sober', $this->theme_sober_css() );

		// Support the Konte theme.
		wp_add_inline_style( 'konte-woocommerce', $this->theme_konte_css() );

		// Support the Motta theme.
		wp_add_inline_style( 'motta-woocommerce-style', $this->theme_motta_css() );
	}

	/**
	 * Copy swatches metadata from the original term
	 *
	 * @return mixed
	 */
	public function translate_term_meta( $meta_value, $term_id, $meta_key ) {
		$term         = get_term( $term_id );
		$default_lang = apply_filters( 'wpml_default_language', null );

		$original_term_id = apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, $default_lang );

		if ( $original_term_id ) {
			$meta_value = get_term_meta( $original_term_id, $meta_key, true );
		}

		return $meta_value;
	}

	/**
	 * Admin notice about missing WooCommerce plugin.
	 *
	 * @since 1.0.15
	 *
	 * @return void
	 */
	public static function woocommerce_missing_notice() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'WCBoost - Variation Swatches requires WooCommerce to work. Please install and activate WooCommerce before using this extension.', 'wcboost-variation-swatches' ); ?></p>
		</div>
		<?php
	}

	/**
	 * The admin notice that inform the free version of plugin has been automatically deactivated.
	 *
	 * @since 1.0.15
	 *
	 * @return void
	 */
	public static function free_version_deactive_notice() {
		?>
		<div class="notice is-dismissible">
			<p><?php esc_html_e( 'WCBoost - Variation Swatches (Free) has been automatically deactivated because you have installed the Pro version.', 'wcboost-variation-swatches' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Deactivate the free verion of the plugin
	 *
	 * @since  1.0.15
	 *
	 * @return void
	 */
	public static function deactivate_free_version() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( defined( 'WCBOOST_VARIATION_SWATCHES_FREE' ) ) {
			deactivate_plugins( WCBOOST_VARIATION_SWATCHES_FREE );
		}
	}

	/**
	 * Custom CSS for the Sober theme.
	 *
	 * @since 1.0.15
	 *
	 * @return string
	 */
	public function theme_sober_css() {
		$css = '
			:root {
				--wcboost-swatches-item-gap: 0px;
			}
			.wcboost-variation-swatches--catalog {
				--wcboost-swatches-item-gap: 4px;
			}
			.wcboost-variation-swatches--catalog .wcboost-variation-swatches--color .wcboost-variation-swatches__name {
				width: 100%;
				height: 100%;
			}
		';
		return $css;
	}

	/**
	 * Custom CSS for the Konte theme.
	 *
	 * @since 1.0.15
	 *
	 * @return string
	 */
	public function theme_konte_css() {
		$css = '
			:root {
				--wcboost-swatches-item-gap: 0;
			}
			.wcboost-variation-swatches--default {
				--wcboost-swatches-item-padding: 0px;
			}
			.wcboost-variation-swatches--catalog {
				--wcboost-swatches-item-gap: 16px;
				--wcboost-swatches-button-font-size: 14px;
				--wcboost-swatches-label-font-size: 14px;
			}
			.wcboost-variation-swatches--catalog .wcboost-variation-swatches__item {
				margin: 0;
			}
			.woocommerce .wcboost-variation-swatches--button .wcboost-variation-swatches__item {
				padding-top: 2px;
				padding-bottom: 2px;
				padding-left: calc(var(--wcboost-swatches-item-width) / 2);
				padding-right: calc(var(--wcboost-swatches-item-width) / 2);
			}
		';
		return $css;
	}

	/**
	 * Custom CSS for the Motta theme.
	 *
	 * @since 1.0.15
	 *
	 * @return string
	 */
	public function theme_motta_css() {
		$css = '
			:root {
				--wcboost-swatches-item-gap: 0;
			}
			.wcboost-variation-swatches--catalog {
				--wcboost-swatches-item-gap: 8px;
				--wcboost-swatches-button-font-size: 14px;
				--wcboost-swatches-label-font-size: 14px;
			}
			.single-product div.product .wcboost-variation-swatches__item {
				display: inline-flex;
			}
		';
		return $css;
	}
}
