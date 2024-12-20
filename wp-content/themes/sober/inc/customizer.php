<?php
/**
 * Sober theme customizer
 *
 * @package Sober
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sober_Customize {
	/**
	 * Customize settings
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * The class constructor
	 *
	 * @param array $config
	 */
	public function __construct( $config ) {
		$this->config = $config;

		if ( ! class_exists( 'Kirki' ) ) {
			return;
		}

		$this->register();

		add_action( 'customize_preview_init', array( $this, 'enqueue_preview_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues style and scripts for customizer controls
	 */
	public function enqueue_scripts() {
		$css = '.customize-control-kirki-radio-image label {margin-right: 5px} #customize-control-shop_cart_icon label > img { width: 20px; } li.control-section-kirki-default { min-height: unset; }';

		wp_add_inline_style( 'customize-controls', $css );
		wp_enqueue_script( 'sober-customize', get_template_directory_uri() . '/js/customizer.js', array( 'jquery' ), '', true );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function enqueue_preview_scripts() {
		$css = '.customize-control-kirki-radio-image label {margin-right: 5px} #customize-control-shop_cart_icon label > img { width: 20px; }';

		wp_add_inline_style( 'wp-admin', $css );
		wp_enqueue_script( 'sober-customizer-preview', get_template_directory_uri() . '/js/customizer-preview.js', array( 'customize-preview' ), '', true );
	}

	/**
	 * Register settings
	 */
	public function register() {
		/**
		 * Add the theme configuration
		 */
		if ( ! empty( $this->config['theme'] ) ) {
			Kirki::add_config( $this->config['theme'], array(
				'capability'  => 'edit_theme_options',
				'option_type' => 'theme_mod',
			) );
		}

		/**
		 * Add panels
		 */
		if ( ! empty( $this->config['panels'] ) ) {
			foreach ( $this->config['panels'] as $panel => $settings ) {
				Kirki::add_panel( $panel, $settings );
			}
		}

		/**
		 * Add sections
		 */
		if ( ! empty( $this->config['sections'] ) ) {
			foreach ( $this->config['sections'] as $section => $settings ) {
				Kirki::add_section( $section, $settings );
			}
		}

		/**
		 * Add fields
		 */
		if ( ! empty( $this->config['theme'] ) && ! empty( $this->config['fields'] ) ) {
			foreach ( $this->config['fields'] as $name => $settings ) {
				if ( ! isset( $settings['settings'] ) ) {
					$settings['settings'] = $name;
				}

				Kirki::add_field( $this->config['theme'], $settings );
			}
		}
	}

	/**
	 * Get config ID
	 *
	 * @return string
	 */
	public function get_theme() {
		return $this->config['theme'];
	}

	/**
	 * Get customize setting value
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option( $name ) {
		if ( class_exists( 'Kirki\Compatibility\Kirki' ) ) {
			$value = Kirki\Compatibility\Kirki::get_option( $this->get_theme(), $name );
		} else {
			$value = get_theme_mod( $name );
			$value = false === $value ? $this->get_option_default( $name ) : $value;
		}

		return $value;
	}

	/**
	 * Get default option values
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function get_option_default( $name ) {
		if ( ! isset( $this->config['fields'][ $name ] ) ) {
			return false;
		}

		return isset( $this->config['fields'][ $name ]['default'] ) ? $this->config['fields'][ $name ]['default'] : false;
	}
}

/**
 * This is a short hand function for getting setting value from customizer
 *
 * @param string $name
 *
 * @return mixed
 */
function sober_get_option( $name ) {
	global $sober_customize;

	if ( empty( $sober_customize ) ) {
		return false;
	}

	$value = $sober_customize->get_option( $name );
	$value = apply_filters( 'sober_get_option_' . $name, $value, $name );

	return apply_filters( 'sober_get_option', $value, $name );
}

/**
 * Get default option values
 *
 * @param $name
 *
 * @return mixed
 */
function sober_get_option_default( $name ) {
	global $sober_customize;

	if ( empty( $sober_customize ) ) {
		return false;
	}

	return $sober_customize->get_option_default( $name );
}

/**
 * Move some default sections to `general` panel that registered by theme
 *
 * @param object $wp_customize
 */
function sober_customize_modify( $wp_customize ) {
	$wp_customize->get_section( 'title_tagline' )->panel     = 'general';
	$wp_customize->get_section( 'static_front_page' )->panel = 'general';
}

add_action( 'customize_register', 'sober_customize_modify' );

/**
 * Get the list of fonts for Kirki
 *
 * @return array
 */
function sober_customize_fonts_choices() {
	$custom_fonts = apply_filters( 'sober_custom_fonts_options', array(
		'families' => array(
			array( 'id' => 'Sofia Pro', 'text' => 'Sofia Pro' ),
		),
		'variants' => array(
			'Sofia Pro' => array( '300' ),
		),
	) );

	$fonts = array(
		'standard' => array( 'serif', 'sans-serif', 'monospace' ),
		'google'   => array(),
	);

	if ( ! empty( $custom_fonts['families'] ) ) {
		$fonts['families'] = array(
			'custom' => array(
				'text'     => esc_html__( 'Sober Custom Fonts', 'sober' ),
				'children' => $custom_fonts['families'],
			),
		);

		if ( ! empty( $custom_fonts['variants'] ) ) {
			$fonts['variants'] = $custom_fonts['variants'];
		}
	}

	return apply_filters( 'sober_customize_fonts_choices', array(
		'fonts' => $fonts,
	) );
}

/**
 * Register theme options' panels, sections and fields
 *
 * @return array
 */
function sober_customize_settings() {
	$settings = array(
		'theme' => 'sober',
	);

	// Register panels
	$panels = array(
		'general'    => array(
			'priority' => 10,
			'title'    => esc_html__( 'General', 'sober' ),
		),
		'typography' => array(
			'priority' => 20,
			'title'    => esc_html__( 'Typography', 'sober' ),
		),
		'header'     => array(
			'priority' => 210,
			'title'    => esc_html__( 'Header', 'sober' ),
		),
		'shop'       => array(
			'priority' => 250,
			'title'    => esc_html__( 'Shop', 'sober' ),
		),
		'footer'     => array(
			'priority' => 300,
			'title'    => esc_html__( 'Footer', 'sober' ),
		),
		'mobile'     => array(
			'priority' => 450,
			'title'    => esc_html__( 'Mobile', 'sober' ),
		),
	);

	// Register sections
	$sections = array(
		'preloader'        => array(
			'title'    => esc_html__( 'Preloader', 'sober' ),
			'priority' => 200,
			'panel'    => 'general',
		),
		'popup'            => array(
			'title'    => esc_html__( 'Popup', 'sober' ),
			'priority' => 220,
			'panel'    => 'general',
		),
		'maintenance'      => array(
			'title'    => esc_html__( 'Maintenance', 'sober' ),
			'priority' => 240,
			'panel'    => 'general',
		),
		'background'       => array(
			'title'    => esc_html__( 'Background', 'sober' ),
			'priority' => 15,
		),
		'typo_main'        => array(
			'title'    => esc_html__( 'Main', 'sober' ),
			'priority' => 10,
			'panel'    => 'typography',
		),
		'typo_headings'    => array(
			'title'    => esc_html__( 'Headings', 'sober' ),
			'priority' => 20,
			'panel'    => 'typography',
		),
		'typo_header'      => array(
			'title'    => esc_html__( 'Header', 'sober' ),
			'priority' => 30,
			'panel'    => 'typography',
		),
		'typo_page_header' => array(
			'title'    => esc_html__( 'Page Header', 'sober' ),
			'priority' => 40,
			'panel'    => 'typography',
		),
		'typo_widgets'     => array(
			'title'    => esc_html__( 'Widgets', 'sober' ),
			'priority' => 50,
			'panel'    => 'typography',
		),
		'typo_posts'       => array(
			'title'    => esc_html__( 'Blog', 'sober' ),
			'priority' => 60,
			'panel'    => 'typography',
		),
		'typo_product'     => array(
			'title'    => esc_html__( 'Product', 'sober' ),
			'priority' => 70,
			'panel'    => 'typography',
		),
		'typo_footer'      => array(
			'title'    => esc_html__( 'Footer', 'sober' ),
			'priority' => 80,
			'panel'    => 'typography',
		),
		'layout'           => array(
			'title'    => esc_html__( 'Layout', 'sober' ),
			'priority' => 20,
		),
		'topbar'           => array(
			'title'    => esc_html__( 'Topbar', 'sober' ),
			'priority' => 10,
			'panel'    => 'header',
		),
		'header'           => array(
			'title'    => esc_html__( 'Header', 'sober' ),
			'priority' => 20,
			'panel'    => 'header',
		),
		'logo'             => array(
			'title'    => esc_html__( 'Logo', 'sober' ),
			'priority' => 30,
			'panel'    => 'header',
		),
		'header_icons'     => array(
			'title'    => esc_html__( 'Header Icons', 'sober' ),
			'priority' => 40,
			'panel'    => 'header',
		),
		'header_search'    => array(
			'title'    => esc_html__( 'Search', 'sober' ),
			'priority' => 50,
			'panel'    => 'header',
		),
		'page_header'      => array(
			'title'    => esc_html__( 'Page Header', 'sober' ),
			'priority' => 230,
		),
		'blog'             => array(
			'title'    => esc_html__( 'Blog', 'sober' ),
			'priority' => 250,
		),
		'footer_layout'           => array(
			'title'    => esc_html__( 'Footer Layout', 'sober' ),
			'priority' => 10,
			'panel'    => 'footer',
		),
		'footer_content'           => array(
			'title'    => esc_html__( 'Footer Content', 'sober' ),
			'priority' => 10,
			'panel'    => 'footer',
		),
		'footer_widget'           => array(
			'title'    => esc_html__( 'Footer Widget', 'sober' ),
			'priority' => 20,
			'panel'    => 'footer',
		),
		'footer_instagram'           => array(
			'title'    => esc_html__( 'Footer Instagram', 'sober' ),
			'priority' => 30,
			'panel'    => 'footer',
		),
		'footer_main'           => array(
			'title'    => esc_html__( 'Footer Main', 'sober' ),
			'priority' => 40,
			'panel'    => 'footer',
		),
		'mobile_header'    => array(
			'title'    => esc_html__( 'Header', 'sober' ),
			'panel'    => 'mobile',
			'priority' => 50,
		),
		'mobile_menu'      => array(
			'title'    => esc_html__( 'Menu', 'sober' ),
			'panel'    => 'mobile',
			'priority' => 20,
		),
	);

	// Register fields
	$fields = array(
		// Preloader
		'preloader'                      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Preloader', 'sober' ),
			'description' => esc_html__( 'Show a waiting screen when page is loading', 'sober' ),
			'section'     => 'preloader',
			'default'     => false,
		),
		'preloader_background_color'     => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Background Color', 'sober' ),
			'section'         => 'preloader',
			'default'         => 'rgba(255,255,255,0.95)',
			'choices'         => array(
				'alpha' => true,
			),
			'active_callback' => array(
				array(
					'setting'  => 'preloader',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		// Popup
		'popup'                          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Popup', 'sober' ),
			'description' => esc_html__( 'Show a popup after website loaded.', 'sober' ),
			'section'     => 'popup',
			'default'     => false,
		),
		'popup_layout'                   => array(
			'type'            => 'radio-image',
			'label'           => esc_html__( 'Popup Layout', 'sober' ),
			'description'     => esc_html__( 'Select the popup layout', 'sober' ),
			'section'         => 'popup',
			'default'         => 'modal',
			'choices'         => array(
				'fullscreen' => get_template_directory_uri() . '/images/options/popup/popup-1.jpg',
				'modal'      => get_template_directory_uri() . '/images/options/popup/popup-2.jpg',
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_overlay_color'            => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Overlay Color', 'sober' ),
			'description'     => esc_html__( 'Pickup the background color for popup overlay', 'sober' ),
			'section'         => 'popup',
			'default'         => 'rgba(35,35,44,0.5)',
			'choices'         => array(
				'alpha' => true,
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_image'                    => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Banner Image', 'sober' ),
			'description'     => esc_html__( 'Upload popup banner image', 'sober' ),
			'section'         => 'popup',
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'popup_layout',
					'operator' => '==',
					'value'    => 'modal',
				),
			),
		),
		'popup_content'                  => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Popup Content', 'sober' ),
			'description'     => esc_html__( 'Enter popup content. HTML and shortcodes are allowed.', 'sober' ),
			'section'         => 'popup',
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_frequency'                => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Frequency', 'sober' ),
			'description'     => esc_html__( 'Do NOT show the popup to the same visitor again until this much day has passed.', 'sober' ),
			'section'         => 'popup',
			'default'         => 1,
			'choices'         => array(
				'min'  => 0,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_visible'                  => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Popup Visible', 'sober' ),
			'description'     => esc_html__( 'Select when the popup appear', 'sober' ),
			'section'         => 'popup',
			'default'         => 'loaded',
			'choices'         => array(
				'loaded' => esc_html__( 'Right after page loads', 'sober' ),
				'delay'  => esc_html__( 'Wait for seconds', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_visible_delay'            => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Delay Time', 'sober' ),
			'description'     => esc_html__( 'Set how many seconds after the page loads before the popup is displayed.', 'sober' ),
			'section'         => 'popup',
			'default'         => 5,
			'choices'         => array(
				'min'  => 0,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'popup_visible',
					'operator' => '==',
					'value'    => 'delay',
				),
			),
		),
		// Maintenance
		'maintenance_enable'    => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Maintenance Mode', 'sober' ),
			'description' => esc_html__( 'Put your site into maintenance mode', 'sober' ),
			'default'     => false,
			'section'     => 'maintenance',
		),
		'maintenance_mode'      => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Mode', 'sober' ),
			'description' => esc_html__( 'Select the correct mode for your site', 'sober' ),
			/* translators: %s external document URL */
			'tooltip'     => wp_kses_post( sprintf( __( 'If you are putting your site into maintenance mode for a longer perior of time, you should set this to "Coming Soon". Maintenance will return HTTP 503, Comming Soon will set HTTP to 200. <a href="%s" target="_blank">Learn more</a>', 'sober' ), 'https://yoast.com/http-503-site-maintenance-seo/' ) ),
			'default'     => 'maintenance',
			'section'     => 'maintenance',
			'choices'     => array(
				'maintenance' => esc_attr__( 'Maintenance', 'sober' ),
				'coming_soon' => esc_attr__( 'Coming Soon', 'sober' ),
			),
		),
		'maintenance_page'      => array(
			'type'    => 'dropdown-pages',
			'label'   => esc_html__( 'Maintenance Page', 'sober' ),
			'default' => 0,
			'section' => 'maintenance',
		),
		// Background
		'404_bg'                         => array(
			'type'        => 'image',
			'label'       => esc_html__( '404 Page', 'sober' ),
			'description' => esc_html__( 'Background image for not found page', 'sober' ),
			'section'     => 'background',
			'default'     => '',
		),
		// Typography body
		'typo_body'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Body', 'sober' ),
			'description' => esc_html__( 'Customize the body font', 'sober' ),
			'section'     => 'typo_main',
			'default'     => array(
				'font-family' => 'Poppins',
				'variant'     => '400',
				'font-size'   => '14px',
				'line-height' => '2.14286',
				'color'       => '#7c7c80',
				'subsets'     => array( 'latin-ext' ),
			),
			'choices'   => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'body,button,input,select,textarea',
				),
			),
		),
		'typo_link'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Link', 'sober' ),
			'description' => esc_html__( 'Customize the link color', 'sober' ),
			'section'     => 'typo_main',
			'default'     => array(
				'color' => '#23232c',
			),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'a',
				),
			),
		),
		'typo_link_hover'                => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Link Hover', 'sober' ),
			'description' => esc_html__( 'Customize the link color when hover, visited', 'sober' ),
			'section'     => 'typo_main',
			'default'     => array(
				'color' => '#111114',
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'a:hover, a:visited',
				),
			),
		),
		// Typography headings
		'typo_h1'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 1', 'sober' ),
			'description' => esc_html__( 'Customize the H1 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '40px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h1, .h1',
				),
			),
		),
		'typo_h2'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 2', 'sober' ),
			'description' => esc_html__( 'Customize the H2 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '30px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h2, .h2',
				),
			),
		),
		'typo_h3'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 3', 'sober' ),
			'description' => esc_html__( 'Customize the H3 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '20px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h3, .h3',
				),
			),
		),
		'typo_h4'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 4', 'sober' ),
			'description' => esc_html__( 'Customize the H4 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '18px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h4, .h4',
				),
			),
		),
		'typo_h5'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 5', 'sober' ),
			'description' => esc_html__( 'Customize the H5 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '14px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h5, .h5',
				),
			),
		),
		'typo_h6'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 6', 'sober' ),
			'description' => esc_html__( 'Customize the H6 font', 'sober' ),
			'section'     => 'typo_headings',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '12px',
				'line-height'    => '1.2',
				'color'          => '#23232c',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => 'h6, .h6',
				),
			),
		),
		// Typography header
		'typo_menu'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Menu', 'sober' ),
			'description' => esc_html__( 'Customize the menu font', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '600',
				'font-size'      => '12px',
				'color'          => '#23232c',
				'text-transform' => 'uppercase',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.nav-menu > li > a, .side-menu .menu > li > a',
				),
			),
		),
		'typo_submenu'                   => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Sub-Menu', 'sober' ),
			'description' => esc_html__( 'Customize the sub-menu font', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '400',
				'font-size'      => '12px',
				'line-height'    => '1.4',
				'color'          => '#909097',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.nav-menu .sub-menu a, .side-menu .sub-menu a',
				),
			),
		),
		'typo_toggle_menu'               => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Side Menu', 'sober' ),
			'description' => esc_html__( 'Customize the menu font of side menu on header v6', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '600',
				'font-size'      => '16px',
				'color'          => '#23232c',
				'text-transform' => 'uppercase',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'   => array(
				array(
					'element' => '.primary-menu.side-menu .menu > li > a',
				),
			),
		),
		'typo_toggle_submenu'            => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Side Sub-Menu', 'sober' ),
			'description' => esc_html__( 'Customize the sub-menu font of side menu', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '400',
				'font-size'      => '12px',
				'line-height'    => '1.4',
				'color'          => '#909097',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.primary-menu.side-menu .sub-menu li a',
				),
			),
		),
		'typo_mobile_menu'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Mobile Menu', 'sober' ),
			'description' => esc_html__( 'Customize the mobile menu font', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '600',
				'font-size'      => '16px',
				'color'          => '#23232c',
				'text-transform' => 'uppercase',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.mobile-menu.side-menu .menu > li > a',
				),
			),
		),
		'typo_mobile_submenu'                   => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Mobile Sub-Menu', 'sober' ),
			'description' => esc_html__( 'Customize the sub-menu font of mobile menu', 'sober' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '400',
				'font-size'      => '12px',
				'line-height'    => '1.3',
				'color'          => '#909097',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.mobile-menu.side-menu .sub-menu li a',
				),
			),
		),
		// Typography page header
		'typo_page_header_title'         => array(
			'type'            => 'typography',
			'label'           => esc_html__( 'Page Header Title', 'sober' ),
			'description'     => esc_html__( 'Customize the page header title font', 'sober' ),
			'section'         => 'typo_page_header',
			'default'         => array(
				'font-family'    => 'Sofia Pro',
				'variant'        => '300',
				'font-size'      => '90',
				'line-height'    => '1',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.page-header .page-title',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_style',
					'operator' => '==',
					'value'    => 'normal',
				),
			),
		),
		'typo_page_header_minimal_title' => array(
			'type'            => 'typography',
			'label'           => esc_html__( 'Page Header Minimal Title', 'sober' ),
			'description'     => esc_html__( 'Customize the page header title font', 'sober' ),
			'section'         => 'typo_page_header',
			'default'         => array(
				'font-family'    => 'Sofia Pro',
				'variant'        => '300',
				'font-size'      => '24px',
				'line-height'    => '1',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.page-header-style-minimal .page-header .page-title',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_style',
					'operator' => '==',
					'value'    => 'minimal',
				),
			),
		),
		'typo_breadcrumb'                => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Breadcrumb', 'sober' ),
			'description' => esc_html__( 'Customize the breadcrumb font', 'sober' ),
			'section'     => 'typo_page_header',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '500',
				'font-size'      => '12px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.woocommerce .woocommerce-breadcrumb, .breadcrumb',
				),
			),
		),
		// Typography widgets
		'type_widget_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Widget Title', 'sober' ),
			'description' => esc_html__( 'Customize the widget title font', 'sober' ),
			'section'     => 'typo_widgets',
			'default'     => array(
				'font-family'    => 'Sofia Pro',
				'variant'        => '300',
				'font-size'      => '20px',
				'text-transform' => 'none',
				'color'          => '#23232c',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.widget-title',
				),
			),
		),
		// Typography product
		'type_product_title'             => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Product Name', 'sober' ),
			'description' => esc_html__( 'Customize the product name font on single product page', 'sober' ),
			'section'     => 'typo_product',
			'default'     => array(
				'font-family'    => 'Sofia Pro',
				'variant'        => '300',
				'font-size'      => '32px',
				'text-transform' => 'none',
				'color'          => '#1e1e23',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.woocommerce div.product .product_title',
				),
			),
		),
		'type_product_excerpt'           => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Product Short Description', 'sober' ),
			'description' => esc_html__( 'Customize the product short description font on single product page', 'sober' ),
			'section'     => 'typo_product',
			'default'     => array(
				'font-family'    => 'Poppins',
				'variant'        => '400',
				'font-size'      => '12px',
				'line-height'    => '2',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.woocommerce div.product .woocommerce-product-details__short-description, .woocommerce div.product div[itemprop="description"]',
				),
			),
		),
		'typo_woocommerce_headers'       => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Section Titles', 'sober' ),
			'description' => esc_html__( 'Customize the font of upsell, related section title', 'sober' ),
			'section'     => 'typo_product',
			'default'     => array(
				'font-family' => 'Sofia Pro',
				'variant'     => '300',
				'font-size'   => '24px',
				'color'       => '#23232c',
				'subsets'     => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.woocommerce .upsells h2, .woocommerce .related h2',
				),
			),
		),
		// Typography footer
		'type_footer_info'               => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Footer Info', 'sober' ),
			'description' => esc_html__( 'Customize the font of footer menu and text', 'sober' ),
			'section'     => 'typo_footer',
			'default'     => array(
				'font-family' => 'Poppins',
				'variant'     => '400',
				'font-size'   => '12px',
				'subsets'     => array( 'latin-ext' ),
			),
			'choices' => sober_customize_fonts_choices(),
			'transport' => 'auto',
			'output'      => array(
				array(
					'element' => '.footer-info',
				),
			),
		),
		// Layout
		'layout_default'                 => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Default Layout', 'sober' ),
			'description' => esc_html__( 'Default layout of blog and other pages', 'sober' ),
			'section'     => 'layout',
			'default'     => 'single-right',
			'choices'     => array(
				'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
				'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
				'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
			),
		),
		'layout_post'                    => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Post Layout', 'sober' ),
			'description' => esc_html__( 'Default layout of single post', 'sober' ),
			'section'     => 'layout',
			'default'     => 'no-sidebar',
			'choices'     => array(
				'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
				'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
				'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
			),
		),
		'layout_page'                    => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Page Layout', 'sober' ),
			'description' => esc_html__( 'Default layout of pages', 'sober' ),
			'section'     => 'layout',
			'default'     => 'no-sidebar',
			'choices'     => array(
				'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
				'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
				'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
			),
		),
		'layout_shop'                    => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Shop Layout', 'sober' ),
			'description' => esc_html__( 'Default layout of shop pages', 'sober' ),
			'section'     => 'layout',
			'default'     => 'no-sidebar',
			'choices'     => array(
				'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
				'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
				'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
			),
		),
		// Topbar
		'topbar_enable'                  => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Show topbar', 'sober' ),
			'section' => 'topbar',
			'default' => 0,
		),
		'topbar_color'                   => array(
			'type'     => 'radio',
			'label'    => esc_html__( 'Topbar Color', 'sober' ),
			'section'  => 'topbar',
			'default'  => 'dark',
			'priority' => 10,
			'choices'  => array(
				'dark'  => esc_html__( 'Dark', 'sober' ),
				'light' => esc_html__( 'Light', 'sober' ),
			),
		),
		'topbar_layout'                  => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Topbar Layout', 'sober' ),
			'section' => 'topbar',
			'default' => '2-columns',
			'choices' => array(
				'2-columns' => esc_html__( '2 Columns', 'sober' ),
				'1-column'  => esc_html__( '1 Column', 'sober' ),
			),
		),
		'topbar_left'                    => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Left Content', 'sober' ),
			'section'         => 'topbar',
			'default'         => 'switchers',
			'choices'         => array(
				'switchers'      => array(
					esc_html__( 'Currency and Language switchers', 'sober' ),
					esc_html__( 'It requires additional plugins installed', 'sober' ),
				),
				'custom_content' => array(
					esc_html__( 'Custom Content', 'sober' ),
					esc_html__( 'Custom content in center', 'sober' ),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar_layout',
					'operator' => '==',
					'value'    => '2-columns',
				),
			),
		),
		'topbar_content'                 => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Custom Content', 'sober' ),
			'description' => esc_html__( 'Allow HTML and Shortcodes', 'sober' ),
			'section'     => 'topbar',
			'default'     => '',
		),
		'topbar_language_flag'          => array(
			'type'            => 'checkbox',
			'label'           => esc_html__( 'Show flags in language switcher', 'sober' ),
			'section'         => 'topbar',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'topbar_layout',
					'operator' => '==',
					'value'    => '2-columns',
				),
				array(
					'setting'  => 'topbar_left',
					'operator' => '==',
					'value'    => 'switchers',
				),
			),
		),
		'topbar_currency_flag'          => array(
			'type'            => 'checkbox',
			'label'           => esc_html__( 'Show flags in currency switcher', 'sober' ),
			'section'         => 'topbar',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'topbar_layout',
					'operator' => '==',
					'value'    => '2-columns',
				),
				array(
					'setting'  => 'topbar_left',
					'operator' => '==',
					'value'    => 'switchers',
				),
			),
		),
		'topbar_closeable'               => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Show Close Icon', 'sober' ),
			'section'         => 'topbar',
			'default'         => 0,
			'active_callback' => array(
				array(
					'setting'  => 'topbar_layout',
					'operator' => '==',
					'value'    => '1-column',
				),
			),
		),
		// Header layout
		'header_layout'                  => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Header Layout', 'sober' ),
			'section' => 'header',
			'default' => 'v1',
			'choices' => array(
				'v1' => esc_html__( 'Header v1', 'sober' ),
				'v2' => esc_html__( 'Header v2', 'sober' ),
				'v3' => esc_html__( 'Header v3', 'sober' ),
				'v4' => esc_html__( 'Header v4', 'sober' ),
				'v5' => esc_html__( 'Header v5', 'sober' ),
				'v6' => esc_html__( 'Header v6', 'sober' ),
			),
		),
		'menu_top'             => array(
			'type'        => 'multicheck',
			'label'       => esc_html__( 'Menu Top', 'sober' ),
			'description' => esc_html__( 'Add items at top of the side menu', 'sober' ),
			'section'     => 'header',
			'choices'     => array(
				'search' => esc_html__( 'Search form', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v6',
				),
			),
		),
		'menu_bottom'             => array(
			'type'        => 'multicheck',
			'label'       => esc_html__( 'Menu Bottom', 'sober' ),
			'description' => esc_html__( 'Append items at end of the side menu', 'sober' ),
			'section'     => 'header',
			'default'     => array( 'cart', 'login' ),
			'choices'     => array(
				'cart'     => esc_html__( 'Shopping Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'login'    => esc_html__( 'Login/Account', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v6',
				),
			),
		),
		'header_wrapper'                 => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Header Wrapper', 'sober' ),
			'description' => esc_html__( 'Select the width of header container', 'sober' ),
			'section'     => 'header',
			'default'     => 'full-width',
			'choices'     => array(
				'full-width' => esc_html__( 'Full Width', 'sober' ),
				'wrapped'    => esc_html__( 'Wrapped', 'sober' ),
			),
		),
		'header_bg'                      => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Header Background', 'sober' ),
			'description' => esc_html__( 'Select header background color', 'sober' ),
			'section'     => 'header',
			'default'     => 'white',
			'choices'     => array(
				'white'       => esc_html__( 'White', 'sober' ),
				'dark'        => esc_attr__( 'Dark', 'sober' ),
				'transparent' => esc_html__( 'Transparent', 'sober' ),
				'custom'      => esc_html__( 'Custom', 'sober' ),
			),
		),
		'header_background_color'       => array(
			'type'            => 'color',
			'default'         => '#fff',
			'section'         => 'header',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'header_bg',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.header-custom .site-header',
					'property' => 'background-color',
				),
			),
		),
		'header_text_color'              => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Header Text Color', 'sober' ),
			'description'     => esc_html__( 'Text light only apply for transparent header', 'sober' ),
			'section'         => 'header',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'sober' ),
				'dark'  => esc_html__( 'Dark', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_bg',
					'operator' => 'in',
					'value'    => [ 'transparent', 'custom' ],
				),
			),
		),
		'header_hover'                   => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Header Hover', 'sober' ),
			'description'     => esc_html__( 'Enable hover effect for transparent header', 'sober' ),
			'section'         => 'header',
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'header_bg',
					'operator' => '==',
					'value'    => 'transparent',
				),
			),
		),
		'header_sticky'                  => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Sticky Header', 'sober' ),
			'description' => esc_html__( 'Make header always visible on top of site', 'sober' ),
			'section'     => 'header',
			'default'     => 'none',
			'choices'     => array(
				'none'   => esc_html__( 'Disable', 'sober' ),
				'normal' => esc_html__( 'Normal Sticky Header', 'sober' ),
				'smart'  => esc_html__( 'Smart Sticky Header', 'sober' ),
			),
		),
		'menu_animation'                 => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Menu Hover Animation', 'sober' ),
			'section' => 'header',
			'default' => 'fade',
			'choices' => array(
				'none'  => esc_html__( 'No Animation', 'sober' ),
				'fade'  => esc_html__( 'Fade', 'sober' ),
				'slide' => esc_html__( 'Slide', 'sober' ),
			),
		),
		// Logo
		'logo_type'                      => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Logo Type', 'sober' ),
			'section' => 'logo',
			'default' => 'image',
			'choices' => array(
				'image' => esc_html__( 'Image', 'sober' ),
				'text'  => esc_html__( 'Text', 'sober' ),
			),
		),
		'logo_text'                      => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Logo Text', 'sober' ),
			'section'         => 'logo',
			'default'         => get_bloginfo( 'name' ),
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'text',
				),
			),
		),
		'logo_font'                      => array(
			'type'            => 'typography',
			'label'           => esc_html__( 'Logo Font', 'sober' ),
			'section'         => 'logo',
			'default'         => array(
				'font-family'    => 'Poppins',
				'variant'        => '700',
				'font-size'      => '30px',
				'letter-spacing' => '0',
				'subsets'        => array( 'latin-ext' ),
				'text-transform' => 'uppercase',
			),
			'output'          => array(
				array(
					'element' => '.site-branding .logo',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'text',
				),
			),
		),
		'logo'                           => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Logo', 'sober' ),
			'section'         => 'logo',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_light'                     => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Logo Light', 'sober' ),
			'section'         => 'logo',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_width'                     => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Logo Width', 'sober' ),
			'section'         => 'logo',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_height'                    => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Logo Height', 'sober' ),
			'section'         => 'logo',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_position'                  => array(
			'type'    => 'spacing',
			'label'   => esc_html__( 'Logo Margin', 'sober' ),
			'section' => 'logo',
			'default' => array(
				'top'    => '0',
				'bottom' => '0',
				'left'   => '0',
				'right'  => '0',
			),
		),
		// Header Icons
		'header_icons'                   => array(
			'type'            => 'sortable',
			'label'           => esc_html__( 'Header Icons', 'sober' ),
			'description'     => esc_html__( 'Select icons to display on the header', 'sober' ),
			'section'         => 'header_icons',
			'default'         => array( 'search', 'login', 'cart' ),
			'choices'         => array(
				'search'   => esc_html__( 'Search', 'sober' ),
				'login'    => esc_html__( 'Login', 'sober' ),
				'cart'     => esc_html__( 'Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'currency' => esc_html__( 'Currency switcher', 'sober' ),
				'language' => esc_html__( 'Language switcher', 'sober' ),
				'socials'  => esc_html__( 'Social icons', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => 'in',
					'value'    => array( 'v1', 'v2', 'v3', 'v6' ),
				),
			),
		),
		'header_icons_left'              => array(
			'type'            => 'sortable',
			'label'           => esc_html__( 'Left Header Icons', 'sober' ),
			'description'     => esc_html__( 'Select icons to display on the left side of the header', 'sober' ),
			'section'         => 'header_icons',
			'default'         => array( 'search', 'login' ),
			'choices'         => array(
				'search'   => esc_html__( 'Search', 'sober' ),
				'login'    => esc_html__( 'Login', 'sober' ),
				'cart'     => esc_html__( 'Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'currency' => esc_html__( 'Currency switcher', 'sober' ),
				'language' => esc_html__( 'Language switcher', 'sober' ),
				'socials'  => esc_html__( 'Social icons', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v5',
				),
			),
		),
		'header_icons_right'             => array(
			'type'            => 'sortable',
			'label'           => esc_html__( 'Right Header Icons', 'sober' ),
			'description'     => esc_html__( 'Select icons to display on the left side of the header', 'sober' ),
			'section'         => 'header_icons',
			'default'         => array( 'wishlist', 'cart' ),
			'choices'         => array(
				'search'   => esc_html__( 'Search', 'sober' ),
				'login'    => esc_html__( 'Login', 'sober' ),
				'cart'     => esc_html__( 'Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'currency' => esc_html__( 'Currency switcher', 'sober' ),
				'language' => esc_html__( 'Language switcher', 'sober' ),
				'socials'  => esc_html__( 'Social icons', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v5',
				),
			),
		),
		'header_icons_left_v4'           => array(
			'type'            => 'sortable',
			'label'           => esc_html__( 'Left Header Icons', 'sober' ),
			'description'     => esc_html__( 'Select icons to display on the left side of the header', 'sober' ),
			'section'         => 'header_icons',
			'default'         => array( 'search' ),
			'choices'         => array(
				'search'   => esc_html__( 'Search', 'sober' ),
				'login'    => esc_html__( 'Login', 'sober' ),
				'cart'     => esc_html__( 'Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'currency' => esc_html__( 'Currency switcher', 'sober' ),
				'language' => esc_html__( 'Language switcher', 'sober' ),
				'socials'  => esc_html__( 'Social icons', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v4',
				),
			),
		),
		'header_icons_right_v4'          => array(
			'type'            => 'sortable',
			'label'           => esc_html__( 'Right Header Icons', 'sober' ),
			'description'     => esc_html__( 'Select icons to display on the right side of the header', 'sober' ),
			'section'         => 'header_icons',
			'default'         => array( 'cart' ),
			'choices'         => array(
				'search'   => esc_html__( 'Search', 'sober' ),
				'login'    => esc_html__( 'Login', 'sober' ),
				'cart'     => esc_html__( 'Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'currency' => esc_html__( 'Currency switcher', 'sober' ),
				'language' => esc_html__( 'Language switcher', 'sober' ),
				'socials'  => esc_html__( 'Social icons', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'v4',
				),
			),
		),
		'shop_cart_icon_source'          => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Shopping Cart Icon Source', 'sober' ),
			'section' => 'header_icons',
			'default' => 'icon',
			'choices' => array(
				'icon'  => esc_html__( 'Built-in Icon', 'sober' ),
				'image' => esc_html__( 'Upload Image', 'sober' ),
			),
		),
		'shop_cart_icon'                 => array(
			'type'            => 'radio-image',
			'label'           => esc_html__( 'Shopping Cart Icon', 'sober' ),
			'section'         => 'header_icons',
			'default'         => 'basket-addtocart',
			'choices'         => array(
				'basket-addtocart' => get_template_directory_uri() . '/images/options/carts/basket-addtocart.svg',
				'shop-cart'        => get_template_directory_uri() . '/images/options/carts/shop-cart.svg',
				'shop-cart-1'      => get_template_directory_uri() . '/images/options/carts/shop-cart-1.svg',
				'shop-cart-2'      => get_template_directory_uri() . '/images/options/carts/shop-cart-2.svg',
				'shop-bag'         => get_template_directory_uri() . '/images/options/carts/shop-bag.svg',
				'shop-bag-1'       => get_template_directory_uri() . '/images/options/carts/shop-bag-1.svg',
				'shop-bag-2'       => get_template_directory_uri() . '/images/options/carts/shop-bag-2.svg',
				'shop-bag-3'       => get_template_directory_uri() . '/images/options/carts/shop-bag-3.svg',
				'shop-bag-4'       => get_template_directory_uri() . '/images/options/carts/shop-bag-4.svg',
				'shop-bag-5'       => get_template_directory_uri() . '/images/options/carts/shop-bag-5.svg',
				'shop-bag-6'       => get_template_directory_uri() . '/images/options/carts/shop-bag-6.svg',
				'shop-bag-7'       => get_template_directory_uri() . '/images/options/carts/shop-bag-7.svg',
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_cart_icon_source',
					'operator' => '==',
					'value'    => 'icon',
				),
			),
		),
		'shop_cart_icon_image'           => array(
			'type'            => 'upload',
			'label'           => esc_html__( 'Shopping Cart Icon', 'sober' ),
			'section'         => 'header_icons',
			'active_callback' => array(
				array(
					'setting'  => 'shop_cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'shop_cart_icon_image_light'     => array(
			'type'            => 'upload',
			'label'           => esc_html__( 'Shopping Cart Icon Light', 'sober' ),
			'section'         => 'header_icons',
			'active_callback' => array(
				array(
					'setting'  => 'shop_cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'shop_cart_icon_width'           => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Shopping Cart Icon Width', 'sober' ),
			'section'         => 'header_icons',
			'default'         => '20',
			'active_callback' => array(
				array(
					'setting'  => 'shop_cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'shop_cart_icon_height'          => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Shopping Cart Icon Height', 'sober' ),
			'section'         => 'header_icons',
			'default'         => '20',
			'active_callback' => array(
				array(
					'setting'  => 'shop_cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'shop_cart_icon_behaviour'       => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Shopping Cart Icon Behaviour', 'sober' ),
			'section' => 'header_icons',
			'default' => 'modal',
			'choices' => array(
				'modal' => esc_html__( 'Open cart modal', 'sober' ),
				'link'  => esc_html__( 'Link to cart page', 'sober' ),
			),
		),
		'wishlist_icon_behaviour'        => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Wishlist Icon Behaviour', 'sober' ),
			'section' => 'header_icons',
			'default' => 'modal',
			'choices' => array(
				'modal' => esc_html__( 'Open wishlist modal', 'sober' ),
				'link'  => esc_html__( 'Link to wishlist page', 'sober' ),
			),
		),
		'account_icon_behaviour'        => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Account Icon Behaviour', 'sober' ),
			'section' => 'header_icons',
			'default' => 'modal',
			'choices' => array(
				'modal' => esc_html__( 'Open login modal', 'sober' ),
				'link'  => esc_html__( 'Link to my account page', 'sober' ),
			),
		),
		// Search
		'header_search_content'             => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Search For', 'sober' ),
			'description' => esc_html__( 'Select the content type of the search modal', 'sober' ),
			'section'     => 'header_search',
			'default'     => 'product',
			'choices'     => array(
				'product' => esc_html__( 'Products', 'sober' ),
				'post'    => esc_html__( 'Posts', 'sober' ),
			),
		),
		'header_search_product_cats'     => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Product Categories', 'sober' ),
			'description'     => esc_html__( 'Enter category names, separate by commas. Leave empty to get all categories. Enter "0" to disable the category selector. Enter a number to get limit number of top categories.', 'sober' ),
			'section'         => 'header_search',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'header_search_content',
					'operator' => '==',
					'value'    => 'product',
				),
			),
		),
		'header_search_product_cats_top'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Top Categories', 'sober' ),
			'description'     => esc_html__( 'Display first level categories only. This option does not work if you enter category names above.', 'sober' ),
			'section'         => 'header_search',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'header_search_content',
					'operator' => '==',
					'value'    => 'product',
				),
			),
		),
		'header_search_post_cats'        => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Post Categories', 'sober' ),
			'description'     => esc_html__( 'Enter category names, separate by commas. Leave empty to get all categories. Enter "0" to disable the category selector. Enter a number to get limit number of top categories.', 'sober' ),
			'section'         => 'header_search',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'header_search_content',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		),
		'header_search_post_cats_top'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Parent Categories', 'sober' ),
			'description'     => esc_html__( 'Display first level categories only. This option does not work if you enter category names above.', 'sober' ),
			'section'         => 'header_search',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'header_search_content',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		),
		// Page header
		'page_header_enable'             => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Show Page Header', 'sober' ),
			'section' => 'page_header',
			'default' => 1,
		),
		'show_breadcrumb'                => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Show Breadcrumb', 'sober' ),
			'section'         => 'page_header',
			'default'         => 1,
			'active_callback' => array(
				array(
					'setting'  => 'page_header_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'page_header_style'              => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Page Header Style', 'sober' ),
			'section'         => 'page_header',
			'default'         => 'normal',
			'choices'         => array(
				'normal'  => esc_html__( 'Normal', 'sober' ),
				'minimal' => esc_html__( 'Minimal', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'page_header_parallax'           => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Page Header Parallax', 'sober' ),
			'description'     => esc_html__( 'Select header parallax animation', 'sober' ),
			'section'         => 'page_header',
			'default'         => 'none',
			'choices'         => array(
				'none' => esc_html__( 'No Parallax', 'sober' ),
				'up'   => esc_html__( 'Move Up', 'sober' ),
				'down' => esc_html__( 'Move Down', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'page_header_style',
					'operator' => '==',
					'value'    => 'normal',
				),
			),
		),
		'page_header_bg'                 => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Page Header Image', 'sober' ),
			'description'     => esc_html__( 'The default background image for page header', 'sober' ),
			'section'         => 'page_header',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'page_header_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'page_header_style',
					'operator' => '==',
					'value'    => 'normal',
				),
			),
		),
		'page_header_text_color'         => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Text Color', 'sober' ),
			'section'         => 'page_header',
			'default'         => 'dark',
			'choices'         => array(
				'dark'  => esc_html__( 'Dark', 'sober' ),
				'light' => esc_html__( 'Light', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'page_header_style',
					'operator' => '!=',
					'value'    => 'minimal',
				),
			),
		),
		// Blog
		'post_header'                    => array(
			'type'        => 'sortable',
			'label'       => esc_html__( 'Post Header', 'sober' ),
			'description' => esc_html__( 'Drag and drop these fields to re-order the layout of single post header.', 'sober' ),
			'section'     => 'blog',
			'default'     => array( 'meta', 'title', 'share', 'image' ),
			'choices'     => array(
				'meta'  => esc_html__( 'Post Meta', 'sober' ),
				'title' => esc_html__( 'Post Title', 'sober' ),
				'share' => esc_html__( 'Sharing Icons', 'sober' ),
				'image' => esc_html__( 'Post Image', 'sober' ),
			),
		),
		'post_navigation'                => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Post Navigation', 'sober' ),
			'section' => 'blog',
			'default' => true,
		),
		'post_author_box'                => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Author Box', 'sober' ),
			'section' => 'blog',
			'default' => true,
		),
		'post_related_posts'             => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Related Posts', 'sober' ),
			'section' => 'blog',
			'default' => true,
		),
		'blog_hr_1'                    => array(
			'type'        => 'custom',
			'section'     => 'blog',
			'default'     => '<hr>',
		),
		'blog_layout'                    => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Blog Layout', 'sober' ),
			'section' => 'blog',
			'default' => 'classic',
			'choices' => array(
				'classic' => esc_html__( 'Classic', 'sober' ),
				'grid'    => esc_html__( 'Grid', 'sober' ),
			),
		),
		'blog_categories'                => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Category List', 'sober' ),
			'description' => esc_html__( 'Display category list on top of blog posts', 'sober' ),
			'section'     => 'blog',
			'default'     => true,
		),
		'blog_categories_limit'          => array(
			'type'        => 'text',
			'label'       => esc_html__( 'Categories', 'sober' ),
			'description' => esc_html__( 'Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get limit number of top categories.', 'sober' ),
			'section'     => 'blog',
			'default'     => '5',
			'active_callback' => array(
				array(
					'setting'  => 'blog_categories',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'excerpt_length'                 => array(
			'type'    => 'number',
			'label'   => esc_html__( 'Excerpt Length', 'sober' ),
			'section' => 'blog',
			'default' => 30,
		),
		'blog_hr_2'                    => array(
			'type'        => 'custom',
			'section'     => 'blog',
			'default'     => '<hr>',
		),
		'entry_meta'        => array(
			'type'     => 'multicheck',
			'label'    => esc_html__( 'Post Meta', 'sober' ),
			'section'  => 'blog',
			'default'  => array( 'date', 'cats' ),
			'choices'  => array(
				'author' => esc_html__( 'Author', 'sober' ),
				'date'   => esc_html__( 'Date', 'sober' ),
				'cats'   => esc_html__( 'Categories', 'sober' ),
			),
			'priority' => 40,
		),
		// Footer
		'footer_content_enable'          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Footer Content', 'sober' ),
			'description' => esc_html__( 'Display extra content above the footer', 'sober' ),
			'section'     => 'footer_content',
			'default'     => true,
		),
		'footer_gotop'                   => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Enable "Go To Top" Icon', 'sober' ),
			'section'         => 'footer_content',
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'footer_content_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_content'                 => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Footer Extra Content', 'sober' ),
			'section'         => 'footer_content',
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'footer_content_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_widgets'                 => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Footer Widgets', 'sober' ),
			'description' => esc_html__( 'Display widgets on footer', 'sober' ),
			'section'     => 'footer_widget',
			'default'     => false,
		),
		'footer_widgets_layout'          => array(
			'type'            => 'radio-image',
			'label'           => esc_html__( 'Footer Widgets Layout', 'sober' ),
			'description'     => esc_html__( 'Select number of columns for displaying widgets', 'sober' ),
			'section'         => 'footer_widget',
			'default'         => '4-columns',
			'choices'         => array(
				'2-columns'       => get_template_directory_uri() . '/images/options/footer/2-columns.png',
				'3-columns'       => get_template_directory_uri() . '/images/options/footer/3-columns.png',
				'4-columns-equal' => get_template_directory_uri() . '/images/options/footer/4-columns-equal.png',
				'4-columns'       => get_template_directory_uri() . '/images/options/footer/4-columns.png',
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_widgets',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_instagram'               => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Instagram Feed', 'sober' ),
			'description' => esc_html__( 'Display Instagram pictures on footer', 'sober' ),
			'section'     => 'footer_instagram',
			'default'     => false,
		),
		'footer_instagram_access_token'  => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Instagram Access Token', 'sober' ),
			'section'         => 'footer_instagram',
			'active_callback' => array(
				array(
					'setting'  => 'footer_instagram',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_copyright'               => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Footer Copyright', 'sober' ),
			'description' => esc_html__( 'Display copyright info on the left side of footer', 'sober' ),
			'section'     => 'footer_main',
			/* translators: %1$s copyright symbol, %2$s the year. */
			'default'     => sprintf( esc_html__( 'Copyright %1$s %2$s', 'sober' ), "&copy;", date( 'Y' ) ),
		),
		'footer_social_extra'            => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Footer Right Content', 'sober' ),
			'description' => esc_html__( 'Add extra content on the right side of footer', 'sober' ),
			'section'     => 'footer_main',
			'default'     => '',
		),
		'footer_bottom_content'          => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Footer Bottom Content', 'sober' ),
			'description' => esc_html__( 'Add extra content at the bottom of footer', 'sober' ),
			'section'     => 'footer_main',
			'default'     => '',
		),
		'footer_bottom_content_align'    => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Footer Bottom Content Alignment', 'sober' ),
			'section' => 'footer_main',
			'default' => 'center',
			'choices' => array(
				'left'   => esc_html__( 'Left', 'sober' ),
				'center' => esc_html__( 'Center', 'sober' ),
				'right'  => esc_html__( 'Right', 'sober' ),
			),
		),
		'footer_wrapper'                 => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Footer Wrapper', 'sober' ),
			'description' => esc_html__( 'Select the width of footer wrapper', 'sober' ),
			'section'     => 'footer_layout',
			'default'     => 'full-width',
			'choices'     => array(
				'full-width' => esc_html__( 'Full Width', 'sober' ),
				'wrapped'    => esc_html__( 'Wrapped', 'sober' ),
			),
		),
		'footer_background_hr_1'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
			'section' => 'footer_layout',
		),
		'footer_background'             => array(
			'type'      => 'select',
			'label'     => esc_html__( 'Background', 'sober' ),
			'section'   => 'footer_layout',
			'default'   => 'light',
			'transport' => 'postMessage',
			'choices'   => array(
				'light'  => esc_attr__( 'Light', 'sober' ),
				'dark'   => esc_attr__( 'Dark', 'sober' ),
				'custom' => esc_attr__( 'Custom', 'sober' ),
			),
		),
		'footer_background_color'       => array(
			'type'            => 'color',
			'default'         => '#fff',
			'section'         => 'footer_layout',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'footer_background',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.site-footer.custom',
					'property' => 'background-color',
				),
			),
		),
		'footer_textcolor'              => array(
			'type'      => 'radio',
			'label'     => esc_html__( 'Text Color', 'sober' ),
			'section'   => 'footer_layout',
			'transport' => 'postMessage',
			'default'   => 'dark',
			'choices'   => array(
				'light' => esc_html__( 'Light', 'sober' ),
				'dark'  => esc_html__( 'Dark', 'sober' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),

		// Mobile Header
		'mobile_topbar'             => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Topbar', 'sober' ),
			'description' => esc_html__( 'Display the topbar on mobile', 'sober' ),
			'section'     => 'mobile_header',
			'default'     => false,
		),
		'mobile_header_icon'       => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Header Icon', 'sober' ),
			'description' => esc_html__( 'Select the icon you want to show on mobile header', 'sober' ),
			'section'     => 'mobile_header',
			'default'     => 'cart',
			'choices'     => array(
				'cart'     => esc_html__( 'Shopping cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'search'   => esc_html__( 'Search', 'sober' ),
			),
		),
		'mobile_cart_badge'              => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Cart Counter Badge', 'sober' ),
			'description'     => esc_html__( 'Adds a badge beside cart icon to show number of items', 'sober' ),
			'section'         => 'mobile_header',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'mobile_header_icon',
					'operator' => '==',
					'value'    => 'cart',
				),
			),
		),
		'mobile_wishlist_badge'          => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Wishlist Counter Badge', 'sober' ),
			'description'     => esc_html__( 'Adds a badge beside wishlist icon to show number of items', 'sober' ),
			'section'         => 'mobile_header',
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'mobile_header_icon',
					'operator' => '==',
					'value'    => 'wishlist',
				),
			),
		),
		// Mobile Menu
		'mobile_menu_close'              => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Close Icon', 'sober' ),
			'description' => esc_html__( 'Adds a close icon on top of mobile menu', 'sober' ),
			'section'     => 'mobile_menu',
			'default'     => false,
		),
		'mobile_menu_width'              => array(
			'type'        => 'slider',
			'label'       => esc_html__( 'Mobile Menu Width', 'sober' ),
			'description' => esc_html__( 'Change mobile menu width', 'sober' ),
			'section'     => 'mobile_menu',
			'transport'   => 'auto',
			'default'     => 85,
			'choices'     => array(
				'min'  => '50',
				'max'  => '90',
				'step' => '1',
			),
			'output'      => array(
				array(
					'element'     => '.mobile-menu',
					'property'    => 'width',
					'units'       => '%',
					'media_query' => '@media screen and (max-width: 767px)',
				),
			),
		),
		'mobile_menu_search'             => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Mobile Menu Search', 'sober' ),
			'description' => esc_html__( 'Show search form in the mobile menu', 'sober' ),
			'section'     => 'mobile_menu',
			'default'     => true,
		),
		'mobile_menu_search_content'     => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Search For', 'sober' ),
			'description' => esc_html__( 'Select what the search form will look for', 'sober' ),
			'section'     => 'mobile_menu',
			'default'     => 'all',
			'choices'     => array(
				'all'     => esc_html__( 'All content types', 'sober' ),
				'product' => esc_html__( 'Products', 'sober' ),
				'post'    => esc_html__( 'Posts', 'sober' ),
			),
		),
		'mobile_menu_top'                => array(
			'type'        => 'multicheck',
			'label'       => esc_html__( 'Mobile Menu Top', 'sober' ),
			'description' => esc_html__( 'Show extra items on top of the mobile menu', 'sober' ),
			'section'     => 'mobile_menu',
			'choices'     => array(
				'currency' => esc_html__( 'Currency Switcher (require plugin)', 'sober' ),
				'language' => esc_html__( 'Language Switcher (require plugin)', 'sober' ),
			),
		),
		'mobile_menu_bottom'             => array(
			'type'        => 'multicheck',
			'label'       => esc_html__( 'Mobile Menu Bottom', 'sober' ),
			'description' => esc_html__( 'Append items at end of the mobile menu', 'sober' ),
			'section'     => 'mobile_menu',
			'default'     => array( 'cart', 'login' ),
			'choices'     => array(
				'cart'     => esc_html__( 'Shopping Cart', 'sober' ),
				'wishlist' => esc_html__( 'Wishlist', 'sober' ),
				'login'    => esc_html__( 'Login/Account', 'sober' ),
			),
		),
	);

	// Setting fields for WooCommerce
	if ( function_exists( 'WC' ) ) {
		$panels['shop'] = array(
			'priority' => 250,
			'title'    => esc_html__( 'Shop', 'sober' ),
		);

		$sections['shop_general'] = array(
			'title'    => esc_html__( 'General', 'sober' ),
			'priority' => 10,
			'panel'    => 'shop',
		);

		$sections['catalog'] = array(
			'title'    => esc_html__( 'Catalog', 'sober' ),
			'priority' => 20,
			'panel'    => 'shop',
		);

		$sections['product'] = array(
			'title'    => esc_html__( 'Product', 'sober' ),
			'priority' => 30,
			'panel'    => 'shop',
		);

		$sections['product_badges'] = array(
			'title'    => esc_html__( 'Product Badges', 'sober' ),
			'priority' => 40,
			'panel'    => 'shop',
		);

		$sections['product_quickview'] = array(
			'title'    => esc_html__( 'Quick View', 'sober' ),
			'priority' => 50,
			'panel'    => 'shop',
		);

		$sections['shop_checkout'] = array(
			'title'    => esc_html__( 'Checkout', 'sober' ),
			'priority' => 60,
			'panel'    => 'shop',
		);

		$sections['mobile_shop'] = array(
			'title'    => esc_html__( 'Shop Catalog', 'sober' ),
			'priority' => 30,
			'panel'    => 'mobile',
		);

		$sections['mobile_product'] = array(
			'title'    => esc_html__( 'Product', 'sober' ),
			'priority' => 40,
			'panel'    => 'mobile',
		);

		$fields = array_merge( $fields, array(
			'shop_page_header_bg'             => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Shop Page Header Image', 'sober' ),
				'description'     => esc_html__( 'The default background image for page header on shop pages', 'sober' ),
				'section'         => 'page_header',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_style',
						'operator' => '==',
						'value'    => 'normal',
					),
				),
			),
			'shop_page_header_text_color'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Shop Page Header Text Color', 'sober' ),
				'section'         => 'page_header',
				'default'         => 'dark',
				'choices'         => array(
					'dark'  => esc_html__( 'Dark', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_style',
						'operator' => '!=',
						'value'    => 'minimal',
					),
				),
			),
			// Shop General
			'open_cart_modal_after_add'       => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Ajax Open Cart Modal', 'sober' ),
				'description' => esc_html__( 'Open the cart modal after successful addition', 'sober' ),
				'section'     => 'shop_general',
				'default'     => false,
			),
			'added_to_cart_notice'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Added to Cart Notification', 'sober' ),
				'description' => esc_html__( 'Display a notification when a product is added to cart', 'sober' ),
				'section'     => 'shop_general',
				'default'     => false,
			),
			// Shop catalog
			'shop_toolbar'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Shop Toolbar', 'sober' ),
				'description' => esc_html__( 'Enable shop toolbar on the top of catalog pages', 'sober' ),
				'section'     => 'catalog',
				'default'     => true,
			),
			'products_toggle'                 => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Product Tabs', 'sober' ),
				'description'     => esc_html__( 'Enable product tabs on top left', 'sober' ),
				'section'         => 'catalog',
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'products_toggle_behaviour'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Tabs Behaviour', 'sober' ),
				'description'     => esc_html__( 'Select the behaviour of tabs', 'sober' ),
				'section'         => 'catalog',
				'default'         => 'ajax',
				'choices'         => array(
					'isotope' => esc_html__( 'Isotope Toggle', 'sober' ),
					'ajax'    => esc_html__( 'Ajax Load', 'sober' ),
					'link'    => esc_html__( 'Simple Link', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'products_toggle_type'            => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Products Tabs Type', 'sober' ),
				'description'     => esc_html__( 'Select how to group products in tabs', 'sober' ),
				'section'         => 'catalog',
				'default'         => 'group',
				'choices'         => array(
					'group'    => esc_html__( 'Groups', 'sober' ),
					'category' => esc_html__( 'Categories', 'sober' ),
					'tag'      => esc_html__( 'Tags', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'products_toggle_groups'          => array(
				'type'            => 'multicheck',
				'label'           => esc_html__( 'Products Tab Groups', 'sober' ),
				'description'     => esc_html__( 'Select how to group products in tabs', 'sober' ),
				'section'         => 'catalog',
				'default'         => array( 'featured', 'new', 'sale' ),
				'choices'         => array(
					'featured' => esc_html__( 'Hot Products', 'sober' ),
					'new'      => esc_html__( 'New Products', 'sober' ),
					'sale'     => esc_html__( 'Sale Products', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle_type',
						'operator' => '==',
						'value'    => 'group',
					),
				),
			),
			'products_toggle_category_amount' => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Categories', 'sober' ),
				'description'     => esc_html__( 'Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get limit number of top categories.', 'sober' ),
				'section'         => 'catalog',
				'default'         => 3,
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle_type',
						'operator' => '==',
						'value'    => 'category',
					),
				),
			),
			'products_toggle_tags'            => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Tags', 'sober' ),
				'description'     => esc_html__( 'Enter tag names. Separate by commas.', 'sober' ),
				'section'         => 'catalog',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle_type',
						'operator' => '==',
						'value'    => 'tag',
					),
				),
			),
			'products_toggle_taxonomy_keep'   => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Keep Using Tabs', 'sober' ),
				'description'     => esc_html__( 'Keep using selected tabs on the category/tag pages. By default, they will be replaced by groups.', 'sober' ),
				'section'         => 'catalog',
				'default'         => false,
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_toggle_type',
						'operator' => '!=',
						'value'    => 'group',
					),
				),
			),
			'products_sorting'                => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Products Sort', 'sober' ),
				'description'     => esc_html__( 'Show the sort options instead of the product count', 'sober' ),
				'section'         => 'catalog',
				'default'         => false,
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'products_filter'                 => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Products Filter', 'sober' ),
				'description'     => esc_html__( 'Show filter icon on the right side', 'sober' ),
				'tooltip'         => esc_html__( 'This requires Shop Filter sidebar must has at least one widget.', 'sober' ),
				'section'         => 'catalog',
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'shop_toolbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'products_item_style'             => array(
				'type'        => 'radio-image',
				'label'       => esc_html__( 'Product Style', 'sober' ),
				'description' => esc_html__( 'Select the style for product in grid while hovered', 'sober' ),
				'section'     => 'catalog',
				'default'     => 'default',
				'choices'     => array(
					'default'   => get_template_directory_uri() . '/images/options/products/default.png',
					'quickview' => get_template_directory_uri() . '/images/options/products/quickview.png',
					'addtocart' => get_template_directory_uri() . '/images/options/products/addtocart.png',
					'slider'    => get_template_directory_uri() . '/images/options/products/slider.png',
					'zoom'      => get_template_directory_uri() . '/images/options/products/zoom.png',
				),
			),
			'product_hover_thumbnail'         => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Hover Thumbnail', 'sober' ),
				'description'     => esc_html__( 'Show different product thumbnail when hover', 'sober' ),
				'section'         => 'catalog',
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'products_item_style',
						'operator' => 'in',
						'value'    => array( 'default', 'quickview', 'addtocart' ),
					),
				),
			),
			'product_hide_outstock_price'     => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Hide Out Of Stock Price', 'sober' ),
				'description' => esc_html__( 'Hide the price if a product is out of stock', 'sober' ),
				'section'     => 'catalog',
				'default'     => false,
			),
			'shop_hide_stars'     => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Hide Star Rating', 'sober' ),
				'section'     => 'catalog',
				'default'     => true,
			),
			'product_columns'                 => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Columns', 'sober' ),
				'section' => 'catalog',
				'default' => '5',
				'choices' => array(
					'4' => esc_html__( '4 Columns', 'sober' ),
					'5' => esc_html__( '5 Columns', 'sober' ),
					'6' => esc_html__( '6 Columns', 'sober' ),
				),
			),
			'products_per_page'               => array(
				'type'    => 'number',
				'label'   => esc_html__( 'Products Per Page', 'sober' ),
				'section' => 'catalog',
				'default' => 15,
			),
			'shop_nav_type'                   => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Navigation Type', 'sober' ),
				'section' => 'catalog',
				'default' => 'links',
				'choices' => array(
					'links'    => esc_html__( 'Numeric', 'sober' ),
					'ajax'     => esc_html__( 'Load more button', 'sober' ),
					'infinity' => esc_html__( 'Infinity Scroll', 'sober' ),
				),
			),
			// Product
			'single_product_style'            => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Single Product Style', 'sober' ),
				'section' => 'product',
				'default' => 'style-1',
				'choices' => array(
					'style-1' => esc_html__( 'Style 1', 'sober' ),
					'style-2' => esc_html__( 'Style 2', 'sober' ),
					'style-3' => esc_html__( 'Style 3', 'sober' ),
					'style-4' => esc_html__( 'Style 4', 'sober' ),
					'style-5' => esc_html__( 'Style 5', 'sober' ),
					'style-6' => esc_html__( 'Style 6', 'sober' ),
				),
			),
			'product_side_products'       => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Side Products', 'sober' ),
				'description' => esc_html__( 'Display recommended products on the right side', 'sober' ),
				'section'     => 'product',
				'default'     => 'upsell_products',
				'choices'     => array(
					'upsell_products'       => esc_html__( 'Upsell products', 'sober' ),
					'related_products'      => esc_html__( 'Related products', 'sober' ),
					'best_selling_products' => esc_html__( 'Best selling products', 'sober' ),
					'featured_products'     => esc_html__( 'Featured products', 'sober' ),
					'recent_products'       => esc_html__( 'Recent products', 'sober' ),
					'sale_products'         => esc_html__( 'Sale products', 'sober' ),
					'top_rated_products'    => esc_html__( 'Top rated products', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => '==',
						'value'    => 'style-3',
					),
				),
			),
			'product_side_products_title' => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Side Products Title', 'sober' ),
				'description'     => esc_html__( 'Section title of side products', 'sober' ),
				'section'         => 'product',
				'default'         => esc_html__( 'Our offer', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => '==',
						'value'    => 'style-3',
					),
				),
			),
			'product_side_products_limit' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Number of products', 'sober' ),
				'section'         => 'product',
				'default'         => 6,
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => '==',
						'value'    => 'style-3',
					),
				),
			),
			'product_hr_0'                    => array(
				'type'        => 'custom',
				'section'     => 'product',
				'default'     => '<hr>',
			),
			'product_gallery_autoheight'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Gallery Auto-height', 'sober' ),
				'description' => esc_html__( 'Change product gallery height dynamically', 'sober' ),
				'section'     => 'product',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => '!=',
						'value'    => 'style-5',
					),
				),
			),
			'product_gallery_autobg'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Auto Background', 'sober' ),
				'description' => esc_html__( 'Auto detect the color from gallery images', 'sober' ),
				'section'     => 'product',
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => '==',
						'value'    => 'style-5',
					),
				),
			),
			'product_lightbox'                => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Image Lightbox', 'sober' ),
				'description' => esc_html__( 'Open a lightbox when click on product images', 'sober' ),
				'section'     => 'product',
				'default'     => true,
			),
			'product_zoom'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Image Zoom', 'sober' ),
				'description' => esc_html__( 'Zoom the product image when mouse hover', 'sober' ),
				'section'     => 'product',
				'default'     => false,
			),
			'product_mute_video'                   => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Mute Video', 'sober' ),
				'description' => esc_html__( 'Mute the video on product page', 'sober' ),
				'section'     => 'product',
				'default'     => true,
			),
			'product_hr_1'                    => array(
				'type'        => 'custom',
				'section'     => 'product',
				'default'     => '<hr>',
				'active_callback'   => array(
					array(
						'setting'         => 'single_product_style',
						'operator'        => 'in',
						'value'           => array( 'style-1', 'style-2', 'style-3', 'style-6' ),
					),
				),
			),
			'product_toolbar'    => array(
				'type'              => 'toggle',
				'label'             => esc_html__( 'Product Toolbar', 'sober' ),
				'section'           => 'product',
				'default'           => true,
				'active_callback'   => array(
					array(
						'setting'         => 'single_product_style',
						'operator'        => 'in',
						'value'           => array( 'style-1', 'style-2', 'style-3', 'style-6' ),
					),
				),
			),
			'product_navigation' => array(
				'type'              => 'toggle',
				'label'             => esc_html__( 'Navigation', 'sober' ),
				'description'       => esc_html__( 'Display next & previous links on top of product page', 'sober' ),
				'section'           => 'product',
				'default'           => true,
				'active_callback'   => array(
					array(
						'setting'         => 'single_product_style',
						'operator'        => 'in',
						'value'           => array( 'style-1', 'style-2', 'style-3', 'style-6' ),
					),
					array(
						'setting'         => 'product_toolbar',
						'operator'        => '==',
						'value'           => true,
					),
				),
			),
			'product_navigation_same_cat' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Navigate to products in the same category', 'sober' ),
				'section'     => 'product',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'single_product_style',
						'operator' => 'in',
						'value'    => array( 'style-1', 'style-2', 'style-3', 'style-6' ),
					),
					array(
						'setting'  => 'product_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_navigation',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_hr_2'                    => array(
				'type'        => 'custom',
				'section'     => 'product',
				'default'     => '<hr>',
			),
			'product_sticky_addtocart'      => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Sticky Add To Cart', 'sober' ),
				'description' => esc_html__( 'Display the sticky bar of the Add To Cart button', 'sober' ),
				'section'     => 'product',
				'choices'     => array(
					''       => esc_html__( 'Disable', 'sober' ),
					'top'    => esc_html__( 'Stick on top', 'sober' ),
					'bottom' => esc_html__( 'Stick at bottom', 'sober' ),
				),
			),
			'product_hr_3'                    => array(
				'type'        => 'custom',
				'section'     => 'product',
				'default'     => '<hr>',
			),
			'product_share'                   => array(
				'type'        => 'multicheck',
				'label'       => esc_html__( 'Product Share', 'sober' ),
				'description' => esc_html__( 'Select social media for sharing products', 'sober' ),
				'section'     => 'product',
				'default'     => array( 'facebook', 'twitter', 'pinterest' ),
				'choices'     => array(
					'facebook'    => esc_html__( 'Facebook', 'sober' ),
					'twitter'     => esc_html__( 'Twitter', 'sober' ),
					'pinterest'   => esc_html__( 'Pinterest', 'sober' ),
					'email'       => esc_html__( 'Email', 'sober' ),
				),
			),
			'product_extra_content'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__( 'Extra Content', 'sober' ),
				'description' => esc_html__( 'Add extra content at the bottom of every product short description. Shortcodes and HTML are allowed.', 'sober' ),
				'section'     => 'product',
				'default'     => '',
			),
			'product_hr_4'                    => array(
				'type'        => 'custom',
				'section'     => 'product',
				'default'     => '<hr>',
			),
			'product_upsells_carousel'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Upsell Products Carousel', 'sober' ),
				'description' => esc_html__( 'Display upsell products as a carousel', 'sober' ),
				'section'     => 'product',
				'default'     => false,
				'active_callback' => function() {
					$product_layout = get_theme_mod( 'single_product_style' );
					$side_product = get_theme_mod( 'product_side_products' );

					if ( 'style-3' == $product_layout && 'upsell_products' == $side_product ) {
						return false;
					}

					return true;
				},
			),
			'product_related_carousel'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Products Carousel', 'sober' ),
				'description' => esc_html__( 'Display related products as a carousel', 'sober' ),
				'section'     => 'product',
				'default'     => false,
				'active_callback' => function() {
					$product_layout = get_theme_mod( 'single_product_style' );
					$side_product = get_theme_mod( 'product_side_products' );

					if ( 'style-3' == $product_layout && 'related_products' == $side_product ) {
						return false;
					}

					return true;
				},
			),
			'product_related_carousel_items'                    => array(
				'type'        => 'number',
				'label'       => esc_html__( 'Number of Related Products', 'sober' ),
				'section'     => 'product',
				'default'     => 12,
				'active_callback' => function() {
					$product_layout = get_theme_mod( 'single_product_style' );
					$side_product = get_theme_mod( 'product_side_products' );
					$product_related_carousel = get_theme_mod( 'product_related_carousel' );

					if (
						( 'style-3' == $product_layout && 'related_products' == $side_product )
						|| ! $product_related_carousel ) {
						return false;
					}

					return true;
				},
			),

			// Product Badges
			'product_badges'              => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Badges', 'sober' ),
				'description' => esc_html__( 'Display product badges', 'sober' ),
				'section'     => 'product_badges',
				'default'     => true,
			),
			'product_badges_hr_1'          => array(
				'type'    => 'custom',
				'section' => 'product_badges',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_sale'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sale Badge', 'sober' ),
				'description' => esc_html__( 'Display a badge for sale products', 'sober' ),
				'section'     => 'product_badges',
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_sale_format'           => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Sale badge text format', 'sober' ),
				'section'     => 'product_badges',
				'default'     => 'percent',
				'choices'     => array(
					'percent' => esc_html__( 'Percentage', 'sober' ),
					'text'    => esc_html__( 'Text', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_onsale_off'              => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Append "OFF" after the percentage', 'sober' ),
				'section'     => 'product_badges',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale_format',
						'operator' => '==',
						'value'    => 'percent',
					),
				),
			),
			'product_badge_sale_text'              => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Sale Badge Text', 'sober' ),
				'tooltip'     => esc_html__( 'Tip: use the pattern {%} to display the percentage number', 'sober' ),
				'section'     => 'product_badges',
				'default'     => esc_html__( 'Sale', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale_format',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'product_badge_sale_bg'              => array(
				'type'        => 'color',
				'label'       => esc_html__( 'Sale Badge Background', 'sober' ),
				'section'     => 'product_badges',
				'default'     => '#00d994',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_sale',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.woocommerce .ribbons .onsale',
						'property' => 'background-color',
					),
				),
			),
			'product_badges_hr_2'          => array(
				'type'    => 'custom',
				'section' => 'product_badges',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_new'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'New Badge', 'sober' ),
				'description' => esc_html__( 'Display a badge for new products', 'sober' ),
				'section'     => 'product_badges',
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_newness'                 => array(
				'type'        => 'number',
				'label'       => esc_html__( 'Product Newness', 'sober' ),
				'description' => esc_html__( 'Display the "New" badge for how many days?', 'sober' ),
				'section'     => 'product_badges',
				'default'     => 3,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_new',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_new_text'                 => array(
				'type'        => 'text',
				'label'       => esc_html__( 'New Badge Text', 'sober' ),
				'section'     => 'product_badges',
				'default'     => esc_html__( 'New', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_new',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_new_bg'              => array(
				'type'        => 'color',
				'label'       => esc_html__( 'New Badge Background', 'sober' ),
				'section'     => 'product_badges',
				'default'     => '#ffaa65',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_new',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.woocommerce .ribbons .newness',
						'property' => 'background-color',
					),
				),
			),
			'product_badges_hr_3'          => array(
				'type'    => 'custom',
				'section' => 'product_badges',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_featured'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Badge', 'sober' ),
				'description' => esc_html__( 'Display a badge for featured products', 'sober' ),
				'section'     => 'product_badges',
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_featured_text'                 => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Featured Badge Text', 'sober' ),
				'section'     => 'product_badges',
				'default'     => esc_html__( 'Hot', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_featured',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_featured_bg'              => array(
				'type'        => 'color',
				'label'       => esc_html__( 'Featured Badge Background', 'sober' ),
				'section'     => 'product_badges',
				'default'     => '#ff6962',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_badge_featured',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.woocommerce .ribbons .featured',
						'property' => 'background-color',
					),
				),
			),
			'product_badges_hr_4'          => array(
				'type'    => 'custom',
				'section' => 'product_badges',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_sold_out_ribbon'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sold Out Badge', 'sober' ),
				'description' => esc_html__( 'Display a badge for out of stock products', 'sober' ),
				'section'     => 'product_badges',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_soldout_text'                 => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Sold Out Badge Text', 'sober' ),
				'section'     => 'product_badges',
				'default'     => esc_html__( 'Sold Out', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_sold_out_ribbon',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_badge_soldout_bg'              => array(
				'type'        => 'color',
				'label'       => esc_html__( 'Featured Badge Background', 'sober' ),
				'section'     => 'product_badges',
				'default'     => '#827d7d',
				'active_callback' => array(
					array(
						'setting'  => 'product_badges',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_sold_out_ribbon',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.woocommerce .ribbons .sold-out',
						'property' => 'background-color',
					),
				),
			),
			// Quick view
			'product_quickview'               => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Quick View', 'sober' ),
				'description' => esc_html__( 'Show the product modal when a product clicked', 'sober' ),
				'section'     => 'product_quickview',
				'default'     => true,
			),
			'product_quickview_behavior'      => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Show Quick View When', 'sober' ),
				'section'         => 'product_quickview',
				'default'         => 'image',
				'choices'         => array(
					'image'       => esc_html__( 'Click on image', 'sober' ),
					'view_button' => esc_html__( 'Click on quick-view button', 'sober' ),
					'buy_button'  => esc_html__( 'Click on buy button', 'sober' ),
					'title'       => esc_html__( 'Click on product title', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_quickview',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'products_item_style',
						'operator' => 'in',
						'value'    => array( 'default', 'addtocart', 'slider', 'zoom' ),
					),
				),
			),
			'product_quickview_detail_link'   => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Quick View Details Link', 'sober' ),
				'description' => esc_html__( 'Add a link of product page at bottom of the quick view modal', 'sober' ),
				'section'     => 'product_quickview',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_quickview',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			// Shop checkout.
			'checkout_layout' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Checkout Layout', 'sober' ),
				'default' => '1-column',
				'section' => 'shop_checkout',
				'choices' => array(
					'1-column'  => esc_html__( '1 Column', 'sober' ),
					'2-columns' => esc_html__( '2 Columns', 'sober' ),
				),
			),
			// Mobile.
			'mobile_shop_add_to_cart'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Buttons', 'sober' ),
				'description' => esc_html__( 'Show add to cart & add to wishlist buttons', 'sober' ),
				'section'     => 'mobile_shop',
				'default'     => false,
			),
			'mobile_product_gallery_display'         => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Product Gallery Display', 'sober' ),
				'section'     => 'mobile_product',
				'default'     => 'thumbnails',
				'choices'     => array(
					'thumbnails' => esc_html__( 'Slider with thumbnails', 'sober' ),
					'dots'       => esc_html__( 'Slider with dots pagination', 'sober' ),
				),
			),
		) );
	}

	// Setting fields for Portfolio
	if ( get_option( 'sober_portfolio' ) ) {
		$panels['portfolio'] = array(
			'title'    => esc_html__( 'Portfolio', 'sober' ),
			'priority' => 350,
		);

		$sections['portfolio_archive'] = array(
			'title'    => esc_html__( 'Portfolio Page', 'sober' ),
			'priority' => 10,
			'panel'    => 'portfolio',
		);

		$sections['portfolio_single'] = array(
			'title'    => esc_html__( 'Project Page', 'sober' ),
			'priority' => 20,
			'panel'    => 'portfolio',
		);

		$fields = array_merge( $fields, array(
			'portfolio_page_header_bg'         => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Portfolio Page Header Image', 'sober' ),
				'description'     => esc_html__( 'The background image for portfolio page header', 'sober' ),
				'section'         => 'page_header',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_style',
						'operator' => '==',
						'value'    => 'normal',
					),
					array(
						'setting'  => 'portfolio_style',
						'operator' => '==',
						'value'    => 'masonry',
					),
				),
			),
			'portfolio_page_header_text_color' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Portfolio Page Header Text Color', 'sober' ),
				'section'         => 'page_header',
				'default'         => 'dark',
				'choices'         => array(
					'dark'  => esc_html__( 'Dark', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_style',
						'operator' => '!=',
						'value'    => 'minimal',
					),
					array(
						'setting'  => 'portfolio_style',
						'operator' => '==',
						'value'    => 'masonry',
					),
				),
			),
			// Portfolio archive
			'portfolio_filter'                 => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Portfolio Filter', 'sober' ),
				'description' => esc_html__( 'Enable portfolio filter on the archive page', 'sober' ),
				'section'     => 'portfolio_archive',
				'default'     => true,
			),
			'portfolio_style'                  => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Portfolio Style', 'sober' ),
				'section' => 'portfolio_archive',
				'default' => 'classic',
				'choices' => array(
					'classic'   => esc_html__( 'Classic', 'sober' ),
					'fullwidth' => esc_html__( 'Full Width', 'sober' ),
					'masonry'   => esc_html__( 'Masonry', 'sober' ),
				),
			),
			// Portfolio single
			'project_share'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Social Share', 'sober' ),
				'description' => esc_html__( 'Enable social sharing icons', 'sober' ),
				'section'     => 'portfolio_single',
				'default'     => false,
			),
			'project_navigation'               => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Single Navigation', 'sober' ),
				'description' => esc_html__( 'Enable next/previous navigation', 'sober' ),
				'section'     => 'portfolio_single',
				'default'     => true,
			),
			'project_nav_text_next'            => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Next Link Text', 'sober' ),
				'section'         => 'portfolio_single',
				'default'         => esc_html__( 'Next Project', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'project_navigation',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'project_nav_text_prev'            => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Prev Link Text', 'sober' ),
				'section'         => 'portfolio_single',
				'default'         => esc_html__( 'Previous Project', 'sober' ),
				'active_callback' => array(
					array(
						'setting'  => 'project_navigation',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		) );
	}

	$settings['panels']   = apply_filters( 'sober_customize_panels', $panels );
	$settings['sections'] = apply_filters( 'sober_customize_sections', $sections );
	$settings['fields']   = apply_filters( 'sober_customize_fields', $fields );

	return $settings;
}

// Fix downloaded fonts from Google.
if ( defined( 'KIRKI_VERSION' ) && version_compare( KIRKI_VERSION, '5.0.0', '<=' ) ) {

	/**
	 * Change the http request headers of 'user-agent' to download .woff2 fonts from Google.
	 *
	 * @param  array $args
	 * @param  string $url
	 *
	 * @return array
	 */
	function sober_fix_kirki_fonts_request_headers( $args, $url ) {
		if ( false === strpos( $url, 'https://fonts.googleapis.com/css' ) ) {
			return $args;
		}

		if ( isset( $args['user-agent'] ) && $args['user-agent'] == 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8' ) {
			$args['user-agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0';
		}

		return $args;
	}

	add_filter( 'http_request_args', 'sober_fix_kirki_fonts_request_headers', 10, 2 );

	/**
	 * Fix incorrect fonts files downloaded from Google.
	 * Delete the Kirki's transients to force downloading font files again.
	 *
	 * @return void
	 */
	function sober_fix_kirki_fonts() {
		delete_transient( 'kirki_remote_url_contents' );
	}

	add_action( 'after_switch_theme', 'sober_fix_kirki_fonts' );
	add_action( 'wp_ajax_kirki_clear_font_cache', 'sober_fix_kirki_fonts' );
}

/**
 * Global variable
 */
$sober_customize = new Sober_Customize( sober_customize_settings() );
