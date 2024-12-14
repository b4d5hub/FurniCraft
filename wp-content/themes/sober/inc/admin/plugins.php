<?php
/**
 * Register the required plugins.
 *
 * @see        http://tgmpluginactivation.com/configuration/
 *
 * @package    Sober
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/lib/tgmpa/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'sober_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function sober_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => esc_html__( 'WooCommerce', 'sober' ),
			'slug'     => 'woocommerce',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Meta Box', 'sober' ),
			'slug'     => 'meta-box',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Kirki Customizer Framework', 'sober' ),
			'slug'     => 'kirki',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Sober Addons', 'sober' ),
			'slug'     => 'sober-addons',
			'source'   => 'https://uix.store/plugins/sober-addons.zip',
			'version'  => '1.6.20',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Slider Revolution', 'sober' ),
			'slug'     => 'revslider',
			'source'   => 'https://uix.store/plugins/revslider.zip',
			'required' => false,
			'version'  => '6.7.18',
		),
		array(
			'name'     => esc_html__( 'WooCommerce Currency Switcher', 'sober' ),
			'slug'     => 'woocommerce-currency-switcher',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Contact Form 7', 'sober' ),
			'slug'     => 'contact-form-7',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'MailChimp for WordPress', 'sober' ),
			'slug'     => 'mailchimp-for-wp',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Variation Swatches', 'sober' ),
			'slug'     => 'wcboost-variation-swatches',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Wishlist', 'sober' ),
			'slug'     => 'wcboost-wishlist',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Products Compare', 'sober' ),
			'slug'     => 'wcboost-products-compare',
			'required' => false,
		),
	);

	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		$plugins[] = array(
			'name'     => esc_html__( 'WPBakery Page Builder', 'sober' ),
			'slug'     => 'js_composer',
			'source'   => 'https://uix.store/plugins/js_composer.zip',
			'version'  => '7.9',
			'required' => true,
		);
	}

	$config = array(
		'id'           => 'sober',
		'default_path' => '',
		'menu'         => 'install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}
