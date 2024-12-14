<?php
namespace WCBoost\Wishlist\Customize;

defined( 'ABSPATH' ) || exit;

use WCBoost\Packages\Utilities\Singleton_Trait;
use WCBoost\Wishlist\Plugin;

class Customizer {
	use Singleton_Trait;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Set priority to 20 to ensure WooCommerce section is added.
		add_action( 'customize_register', [ $this, 'register' ], 20 );
		add_action( 'customize_controls_print_styles', [ $this, 'add_styles' ] );
		add_action( 'customize_controls_print_scripts', [ $this, 'add_scripts' ], 30 );
		add_action( 'customize_preview_init', [ $this, 'preview_init' ] );
	}

	/**
	 * Add settings to the customizer.
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function register( $wp_customize ) {
		$this->register_sections( $wp_customize );
		$this->register_page_settings( $wp_customize );
		$this->register_button_settings( $wp_customize );
	}

	/**
	 * Register sections
	 *
	 * @param  \WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @return void
	 */
	private function register_sections( $wp_customize ) {
		if ( ! $wp_customize->get_panel( 'woocommerce' ) ) {
			$this->maybe_register_panel( $wp_customize );
			$panel = 'wcboost';
		} else {
			$panel = 'woocommerce';
		}

		$wp_customize->add_section( 'wcboost_wishlist_page', [
			'title'       => esc_html__( 'Wishlist Page', 'wcboost-wishlist' ),
			'description' => esc_html__( 'These options let you change the appearance of the wishlist page.', 'wcboost-wishlist' ),
			'priority'    => 20,
			'panel'    => $panel,
		] );

		$wp_customize->add_section( 'wcboost_wishlist_button', [
			'title'       => esc_html__( 'Wishlist Button', 'wcboost-wishlist' ),
			'description' => esc_html__( 'These options let you change the appearance of the wishlist button.', 'wcboost-wishlist' ),
			'priority'    => 20,
			'panel'       => $panel,
		] );
	}

	/**
	 * Add settings of the wishlist page
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function register_page_settings( $wp_customize ) {
		$section = 'wcboost_wishlist_page';

		$wp_customize->add_setting(
			'wcboost_wishlist_page_show_title',
			[
				'default'              => 'no',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_page_show_title',
			[
				'label'   => __( 'Show the wishlist title', 'wcboost-wishlist' ),
				'section' => $section,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_page_show_desc',
			[
				'default'              => 'no',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_page_show_desc',
			[
				'label'   => __( 'Show the wishlist description', 'wcboost-wishlist' ),
				'section' => $section,
				'type'    => 'checkbox',
			]
		);

		$columns = [
			'price'    => [ 'label' => __( 'Product price', 'wcboost-wishlist' ), 'default' => 'yes' ],
			'stock'    => [ 'label' => __( 'Stock status', 'wcboost-wishlist' ), 'default' => 'yes' ],
			'quantity' => [ 'label' => __( 'Quantity', 'wcboost-wishlist' ), 'default' => 'no' ],
			'date'     => [ 'label' => __( 'Date added', 'wcboost-wishlist' ), 'default' => 'no' ],
			'purchase' => [ 'label' => __( 'Add to cart', 'wcboost-wishlist' ), 'default' => 'yes' ],
		];

		// Add a hidden option for checkboxes title.
		$wp_customize->add_setting(
			'wcboost_wishlist_table_columns_customizable',
			[
				'default'    => 'yes',
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_table_columns_customizable',
			[
				'label'       => __( 'Table columns', 'wcboost-wishlist' ),
				'description' => __( 'Choose wishlist table columns', 'wcboost-wishlist' ),
				'section'     => $section,
				'type'        => 'hidden',
			]
		);

		foreach ( $columns as $column => $option ) {
			$wp_customize->add_setting(
				'wcboost_wishlist_table_columns[' . $column . ']',
				[
					'default'              => $option['default'],
					'type'                 => 'option',
					'capability'           => 'manage_woocommerce',
					'sanitize_callback'    => 'wc_bool_to_string',
					'sanitize_js_callback' => 'wc_string_to_bool',
				]
			);

			$wp_customize->add_control(
				'wcboost_wishlist_table_columns[' . $column . ']',
				[
					'label'   => $option['label'],
					'section' => $section,
					'type'    => 'checkbox',
				]
			);
		}

		$wp_customize->add_setting(
			'wcboost_wishlist_share',
			[
				'default'              => 'yes',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			]
		);

	}

	/**
	 * Add settings of the wishlist button
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function register_button_settings( $wp_customize ) {
		$section = 'wcboost_wishlist_button';

		if ( 'theme' != wc_get_theme_support( 'wishlist::single_button_position' ) ) {
			$wp_customize->add_setting(
				'wcboost_wishlist_single_button_position',
				[
					'default'    => wc_get_theme_support( 'wishlist::single_button_position', 'after_add_to_cart' ),
					'type'       => 'option',
					'capability' => 'manage_woocommerce',
				]
			);

			$wp_customize->add_control(
				'wcboost_wishlist_single_button_position',
				[
					'label'       => __( 'Button position', 'wcboost-wishlist' ),
					'description' => __( 'Choose where to show "Add to wishlist" button on the product page.', 'wcboost-wishlist' ),
					'section'     => $section,
					'type'        => 'select',
					'choices'     => [
						'before_add_to_cart' => __( 'Before "Add to cart" button', 'wcboost-wishlist' ),
						'after_add_to_cart'  => __( 'After "Add to cart" button', 'wcboost-wishlist' ),
						'after_title'        => __( 'After product name', 'wcboost-wishlist' ),
						'after_excerpt'      => __( 'After product short description', 'wcboost-wishlist' ),
						'manual'             => __( 'Manually use shortcode', 'wcboost-wishlist' ),
					],
				]
			);
		}

		if ( 'theme' != wc_get_theme_support( 'wishlist::loop_button_position' ) ) {
			$wp_customize->add_setting(
				'wcboost_wishlist_loop_button_position',
				[
					'default'    => wc_get_theme_support( 'wishlist::loop_button_position', '' ),
					'type'       => 'option',
					'capability' => 'manage_woocommerce',
				]
			);

			$wp_customize->add_control(
				'wcboost_wishlist_loop_button_position',
				[
					'label'       => __( 'Button in loop', 'wcboost-wishlist' ),
					'description' => __( 'Choose where to show "Add to wishlist" button on the product catalog pages.', 'wcboost-wishlist' ),
					'section'     => $section,
					'type'        => 'select',
					'choices'     => [
						''                   => __( 'Hide the button', 'wcboost-wishlist' ),
						'before_add_to_cart' => __( 'Before "Add to cart" button', 'wcboost-wishlist' ),
						'after_add_to_cart'  => __( 'After "Add to cart" button', 'wcboost-wishlist' ),
						'manual'             => __( 'Manually use shortcode', 'wcboost-wishlist' ),
					],
				]
			);
		}

		$wp_customize->add_setting(
			'wcboost_wishlist_button_icon',
			[
				'default'    => wc_get_theme_support( 'wishlist::button_icon', 'heart' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_button_icon',
			[
				'label'       => __( 'Button icon', 'wcboost-wishlist' ),
				'section'     => $section,
				'type'        => 'select',
				'choices'     => [
					''         => __( 'No icon', 'wcboost-wishlist' ),
					'heart'    => __( 'Heart', 'wcboost-wishlist' ),
					'star'     => __( 'Star', 'wcboost-wishlist' ),
					'bookmark' => __( 'Bookmark', 'wcboost-wishlist' ),
					'custom'   => __( 'Custom icon', 'wcboost-wishlist' ),
				],
			]
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_button_icon_custom[default]',
			[
				'default'    => wc_get_theme_support( 'wishlist::button_icon_custom_default', '' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_button_icon_custom[added]',
			[
				'default'    => wc_get_theme_support( 'wishlist::button_icon_custom_added', '' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			new \WP_Customize_Image_Control(
				$wp_customize,
				'wcboost_wishlist_button_icon_custom[default]',
				[
					'label'       => __( 'Custom icon', 'wcboost-wishlist' ),
					'description' => __( 'Default icon.', 'wcboost-wishlist' ),
					'section'     => $section,
					'mime_type'   => 'image',
				]
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Image_Control(
				$wp_customize,
				'wcboost_wishlist_button_icon_custom[added]',
				[
					'description' => __( 'Added icon.', 'wcboost-wishlist' ),
					'section'     => $section,
					'mime_type'   => 'image',
				]
			)
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_button_text[add]',
			[
				'default'    => __( 'Add to wishlist', 'wcboost-wishlist' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_button_text[remove]',
			[
				'default'    => __( 'Remove from wishlist', 'wcboost-wishlist' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_setting(
			'wcboost_wishlist_button_text[view]',
			[
				'default'    => __( 'View wishlist', 'wcboost-wishlist' ),
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_button_text[add]',
			[
				'label'       => __( 'Button text', 'wcboost-wishlist' ),
				'description' => __( 'Button add', 'wcboost-wishlist' ),
				'section'     => $section,
				'type'        => 'text',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_button_text[remove]',
			[
				'description' => __( 'Button remove', 'wcboost-wishlist' ),
				'section'     => $section,
				'type'        => 'text',
			]
		);

		$wp_customize->add_control(
			'wcboost_wishlist_button_text[view]',
			[
				'description' => __( 'Button view', 'wcboost-wishlist' ),
				'section'     => $section,
				'type'        => 'text',
			]
		);

		// Allow themes to fixed the button style.
		if ( 'theme' != wc_get_theme_support( 'wishlist::button_type' ) ) {
			$wp_customize->add_setting(
				'wcboost_wishlist_button_type',
				[
					'default'    => wc_get_theme_support( 'wishlist::button_type', 'button' ),
					'type'       => 'option',
					'capability' => 'manage_woocommerce',
				]
			);

			$wp_customize->add_control(
				'wcboost_wishlist_button_type',
				[
					'label'   => __( 'Button type', 'wcboost-wishlist' ),
					'section' => $section,
					'type'    => 'radio',
					'choices' => [
						'button' => __( 'Normal button', 'wcboost-wishlist' ),
						'text'   => __( 'Text button', 'wcboost-wishlist' ),
						'custom' => __( 'Custom button', 'wcboost-wishlist' ),
					]
				]
			);

			// Button normal.
			$wp_customize->add_setting(
				'wcboost_wishlist_button_style[background_color]',
				[
					'default'           => '#333333',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_setting(
				'wcboost_wishlist_button_style[border_color]',
				[
					'default'           => '#333333',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_setting(
				'wcboost_wishlist_button_style[text_color]',
				[
					'default'           => '#ffffff',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_style[background_color]',
				[
					'label'       => __( 'Button', 'wcboost-wishlist' ),
					'description' => __( 'Background color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_style[border_color]',
				[
					'description' => __( 'Border color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_style[text_color]',
				[
					'description' => __( 'Text color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );

			// Hover
			$wp_customize->add_setting(
				'wcboost_wishlist_button_hover_style[background_color]',
				[
					'default'           => '#111111',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_setting(
				'wcboost_wishlist_button_hover_style[border_color]',
				[
					'default'           => '#111111',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_setting(
				'wcboost_wishlist_button_hover_style[text_color]',
				[
					'default'           => '#ffffff',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				]
			);

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_hover_style[background_color]',
				[
					'label'       => __( 'Button Hover', 'wcboost-wishlist' ),
					'description' => __( 'Background color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_hover_style[border_color]',
				[
					'description' => __( 'Border color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );

			$wp_customize->add_control( new \WP_Customize_Color_Control(
				$wp_customize,
				'wcboost_wishlist_button_hover_style[text_color]',
				[
					'description' => __( 'Text color', 'wcboost-wishlist' ),
					'section'     => $section,
				]
			) );
		}
	}

	/**
	 * Check and register the WCBoost panel if WooCommerce panel doesn't exist.
	 *
	 * @param  \WP_Customize_Manager $wp_customize
	 * @return void
	 */
	private function maybe_register_panel( $wp_customize ) {
		if ( ! $wp_customize->get_panel( 'wcboost' ) ) {
			$wp_customize->add_panel( 'wcboost', [
				'priority'   => 200,
				'capability' => 'manage_woocommerce',
				'title'      => __( 'WCBoost', 'wcboost-wishlist' ),
			] );
		}
	}

	/**
	 * Sanitize wishlist title mode option
	 *
	 * @param string $mode
	 * @return string
	 */
	public function sanitize_wisthlist_title_mode( $mode ) {
		return in_array( $mode, ['page_title', 'wishlist_title'] ) ? $mode : 'page_title';
	}

	/**
	 * Add extra CSS to style controls
	 */
	public function add_styles() {
		?>
		<style type="text/css">
			#customize-control-wcboost_wishlist_table_columns-price,
			#customize-control-wcboost_wishlist_table_columns-stock,
			#customize-control-wcboost_wishlist_table_columns-quantity,
			#customize-control-wcboost_wishlist_table_columns-date {
				margin-bottom: 0;
			}
			#customize-control-wcboost_wishlist_button_icon_custom-default .thumbnail-image,
			#customize-control-wcboost_wishlist_button_icon_custom-added .thumbnail-image {
				float: left;
				margin-right: 10px;
				width: 60px;
			}
		</style>
		<?php
	}

	/**
	 * Customizer scritps
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
			jQuery( function( $ ) {

				<?php if ( 0 < wc_get_page_id( 'wishlist' ) ) : ?>

					wp.customize.section( 'wcboost_wishlist_page', function( section ) {
						section.expanded.bind( function( isExpanded ) {
							if ( isExpanded ) {
								wp.customize.previewer.previewUrl.set( '<?php echo esc_js( wc_get_page_permalink( 'wishlist' ) ); ?>' );
							}
						} );
					} );

				<?php endif; ?>

				wp.customize.control( 'wcboost_wishlist_button_icon', function( control ) {

					if ( 'custom' === control.setting.get() ) {
						wp.customize.control( 'wcboost_wishlist_button_icon_custom[default]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_icon_custom[added]' ).toggle( true );
					} else {
						wp.customize.control( 'wcboost_wishlist_button_icon_custom[default]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_icon_custom[added]' ).toggle( false );
					}

					control.setting.bind( function( value ) {
						if ( 'custom' === value ) {
							wp.customize.control( 'wcboost_wishlist_button_icon_custom[default]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_icon_custom[added]' ).toggle( true );
						} else {
							wp.customize.control( 'wcboost_wishlist_button_icon_custom[default]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_icon_custom[added]' ).toggle( false );
						}
					} );

				} );

				wp.customize.control( 'wcboost_wishlist_button_type', function( control ) {

					if ( 'custom' === control.setting.get() ) {
						wp.customize.control( 'wcboost_wishlist_button_style[background_color]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_style[border_color]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_style[text_color]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[background_color]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[border_color]' ).toggle( true );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[text_color]' ).toggle( true );
					} else {
						wp.customize.control( 'wcboost_wishlist_button_style[background_color]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_style[border_color]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_style[text_color]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[background_color]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[border_color]' ).toggle( false );
						wp.customize.control( 'wcboost_wishlist_button_hover_style[text_color]' ).toggle( false );
					}

					control.setting.bind( function( value ) {
						if ( 'custom' === value ) {
							wp.customize.control( 'wcboost_wishlist_button_style[background_color]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_style[border_color]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_style[text_color]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[background_color]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[border_color]' ).toggle( true );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[text_color]' ).toggle( true );
						} else {
							wp.customize.control( 'wcboost_wishlist_button_style[background_color]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_style[border_color]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_style[text_color]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[background_color]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[border_color]' ).toggle( false );
							wp.customize.control( 'wcboost_wishlist_button_hover_style[text_color]' ).toggle( false );
						}
					} );

				} );

			} );
		</script>
		<?php
	}

	/**
	 * Enqueue scripts need to automate the live setting preview.
	 *
	 * @return void
	 */
	public function preview_init() {
		$plugin = Plugin::instance();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wcboost-wishlist-customize-preview', $plugin->plugin_url( '/assets/js/customize-preview' . $suffix . '.js' ), [ 'jquery', 'customize-preview' ], $plugin->version, true );
	}
}
