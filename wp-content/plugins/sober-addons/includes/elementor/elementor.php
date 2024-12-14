<?php
namespace SoberAddons;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor {
	/**
	 * Instance
	 *
	 * @access private
	 */
	private static $_instance = null;

	private $modules = [];

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return SoberAddons/Elementor An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();
		$this->add_actions();
		$this->init_modules();
	}

	/**
	 * Auto load widgets
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$path = explode( '\\', $class );
		$filename = strtolower( array_pop( $path ) );
		$filename = str_replace( '_', '-', $filename );

		$module = array_pop( $path );

		if ( 'Widgets' == $module ) {
			$filename = SOBER_ADDONS_DIR . 'includes/elementor/widgets/' . $filename . '.php';
		} elseif ( 'Controls' == $module ) {
			$filename = str_replace( 'control-', '', $filename );
			$filename = SOBER_ADDONS_DIR . 'includes/elementor/controls/' . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	/**
	 * Includes files which are not widgets nor controls
	 */
	private function _includes() {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			include_once( SOBER_ADDONS_DIR . 'includes/elementor/modules/custom-css.php' );
			include_once( SOBER_ADDONS_DIR . 'includes/elementor/modules/motion-parallax.php' );
		}
	}

	/**
	 * Hooks to init
	 */
	protected function add_actions() {
		// On Editor - Register WooCommerce frontend hooks before the Editor init.
		// Priority = 5, in order to allow plugins remove/add their wc hooks on init.
		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
			add_action( 'init', [ $this, 'register_wc_hooks' ], 5 );
		}

		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
	}

	/**
	 * Register WC hooks for Elementor editor
	 */
	public function register_wc_hooks() {
		if ( ! function_exists( 'wc' ) ) {
			return;
		}

		wc()->frontend_includes();
	}

	/**
	 * Register styles
	 */
	public function styles() {
	}

	/**
	 * Register scripts
	 *
	 * @todo Use the dist version when releasing
	 */
	public function register_frontend_scripts() {
		wp_register_script( 'isotope-elementor', SOBER_ADDONS_URL . 'assets/js/isotope.pkgd.min.js', array(
			'jquery',
			'imagesloaded',
		), '3.0.1', true );
		wp_register_script( 'rellax', SOBER_ADDONS_URL . 'assets/js/rellax.min.js', [], '1.12.1', true );
		wp_register_script( 'sober-elementor-widgets', SOBER_ADDONS_URL . 'assets/js/elementor-widgets.js', ['jquery', 'elementor-frontend'], SOBER_ADDONS_VER, true );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_frontend_scripts() {
		wp_enqueue_script( 'sober-elementor-widgets' );
	}

	/**
	 * Enqueue editor scripts
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'sober-elementor-editor-modules',
			SOBER_ADDONS_URL . 'assets/js/elementor-modules.js',
			[
				'backbone-marionette',
				'elementor-common-modules',
				'elementor-editor-modules',
			],
			SOBER_ADDONS_VER,
			true
		);
	}

	/**
	 * Init Controls
	 */
	public function init_controls( $controls_manager ) {
	}

	/**
	 * Init Widgets
	 */
	public function init_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Accordion() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Tabs() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Section_Heading() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Icon_Box() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Icon_Box_Carousel() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Posts_Grid() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Countdown() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Chart() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Message_Box() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Price_Table() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Testimonial() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Team_Member() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Info_List() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\FAQ() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Button() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Google_Map() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Image_Slider() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Category_Banner() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner_Simple() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner_Grid_4() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner_Grid_5() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner_Grid_52() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Banner_Grid_6() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Logos() );
		$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Portfolio_Grid() );

		if ( class_exists( 'WooCommerce' ) ) {
			$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Products_Grid() );
			$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Products_Tabs() );
			$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Products_Carousel() );
			$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Product_Banner() );
		}

		if ( post_type_exists( 'mc4wp-form' ) ) {
			$widgets_manager->register( new \SoberAddons\Elementor\Widgets\Subscribe_Box() );
		}
	}

	/**
	 * Add Sober category
	 */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'sober',
			[ 'title' => __( 'Sober', 'sober' ) ]
		);
	}

	/**
	 * Init modules
	 */
	public function init_modules() {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$this->modules['custom_css'] = \SoberAddons\Elementor\Module\Custom_CSS::instance();
			$this->modules['motion_parallax'] = \SoberAddons\Elementor\Module\Motion_Parallax::instance();
		}
	}
}

Elementor::instance();
