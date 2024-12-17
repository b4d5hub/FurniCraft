<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all add-ons.
 *
 */
abstract class SliceWP_Add_On {

    /**
     * The slug of the add-on.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $slug;

    /**
     * The name of the add-on.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $name;

    /**
     * A short description for the add-on.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $description;

    /**
     * The URL for the icon being displayed for the add-on.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $icon_url;

    /**
     * The URL that points to the documentation for the add-on.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $documentation_url = '';

    /**
     * The main URL where the admin can set the global settings for the add-on.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $settings_url = '';

    /**
     * The add-on author's name.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $author = '';

    /**
     * The URL of the add-on's author.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $author_url = '';


    /**
     * Constructor.
     * 
     */
    public function __construct() {

        if ( $this->is_active() ) {

            $this->include_files();

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            
        }

    }


    /**
	 * Getter.
	 *
	 * @param string $property
	 *
	 */
	public function get( $property ) {

		if ( method_exists( $this, 'get_' . $property ) ) {

            return $this->{'get_' . $property}();

        } else {

            return ( property_exists( $this, $property ) ? $this->$property : null );

        }

	}


	/**
	 * Setter.
	 *
	 * @param string $property
	 * @param string $value
	 *
	 */
	public function set( $property, $value ) {

		if ( method_exists( $this, 'set_' . $property ) ) {

            $this->{'set_' . $property}( $value );

        } else {

            $this->$property = $value;

        }

	}


    /**
     * Includes the add-on's files.
     * 
     */
    protected function include_files() {}


    /**
     * Returns the dir path of the add-on.
     * 
     * @return string
     * 
     */
    public function get_dir_path() {

        $reflector = new ReflectionClass( $this );

        return plugin_dir_path( $reflector->getFileName() );

    }


    /**
     * Returns the dir URL of the add-on.
     * 
     * @return string
     * 
     */
    public function get_dir_url() {

        $reflector = new ReflectionClass( $this );

        return plugin_dir_url( $reflector->getFileName() );

    }


    /**
     * Registers and enqueues scripts and styles on the admin side.
     * 
     */
    public function enqueue_admin_scripts() {

        // By default load admin scripts and styles only on our pages.
        if ( empty( $_GET['page'] ) || strpos( $_GET['page'], 'slicewp' ) === false ) {
            return;
        }

        // Add-on styles.
        if ( file_exists( $this->get_dir_path() . 'assets/css/style-admin.css' ) ) {

            wp_register_style( 'slicewp-' . esc_attr( str_replace( '_', '-', $this->slug ) ) . '-style', $this->get_dir_url() . 'assets/css/style-admin.css', array(), $this->get_parent_plugin_version() );
		    wp_enqueue_style( 'slicewp-' . esc_attr( str_replace( '_', '-', $this->slug ) ) . '-style' );

        }

		// Add-on script.
        if ( file_exists( $this->get_dir_path() . 'assets/js/script-admin.js' ) ) {

            wp_register_script( 'slicewp-' . esc_attr( str_replace( '_', '-', $this->slug ) ) . '-script', $this->get_dir_url() . 'assets/js/script-admin.js', array( 'jquery', 'slicewp-script' ), $this->get_parent_plugin_version() );
            wp_enqueue_script( 'slicewp-' . esc_attr( str_replace( '_', '-', $this->slug ) ) . '-script' );

        }

    }


    /**
     * Returns the parent's plugin version.
     * 
     * @return null|string
     * 
     */
    protected function get_parent_plugin_version() {

        if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

        $file_path_from_plugin_root = str_replace( WP_PLUGIN_DIR . '/', '', $this->get_dir_path() );

        $path_array = explode( '/', $file_path_from_plugin_root );

        $plugin_folder_name = reset( $path_array );

        foreach ( get_plugins() as $index => $plugin_data ) {

            if ( false !== strpos( $index, $plugin_folder_name . '/' ) ) {

                return $plugin_data['Version'];

            }

        }

        return null;

    }


    /**
     * Checks whether the add-on is active or not.
     * 
     * @return bool
     * 
     */
    public function is_active() {

        if ( in_array( $this->slug, slicewp_get_option( 'active_add_ons', array() ) ) ) {
            return true;
        }

        return false;

    }


    /**
     * Activates the add-on.
     * 
     * @return bool
     * 
     */
    public function activate() {

        $active_add_ons   = slicewp_get_option( 'active_add_ons', array() );
        $active_add_ons[] = $this->slug;

        $active_add_ons = array_unique( $active_add_ons );

        sort( $active_add_ons );

        $activated = slicewp_update_option( 'active_add_ons', $active_add_ons );

        if ( $activated ) {

            /**
             * Fires when the add-on has been activated.
             * 
             * @param string $add_on_slug
             * 
             */
            do_action( 'slicewp_activated_add_on', $this->slug );

        }

        return $activated;

    }


    /**
     * Deactivates the add-on.
     * 
     * @return bool
     * 
     */
    public function deactivate() {

        $active_add_ons = slicewp_get_option( 'active_add_ons', array() );

        unset( $active_add_ons[array_search( $this->slug, $active_add_ons )] );

        $active_add_ons = array_values( $active_add_ons );

        sort( $active_add_ons );

        $deactivated = slicewp_update_option( 'active_add_ons', $active_add_ons );

        if ( $deactivated ) {

            /**
             * Fires when the add-on has been deactivated.
             * 
             * @param string $add_on_slug
             * 
             */
            do_action( 'slicewp_deactivated_add_on', $this->slug );

        }

        return $deactivated;

    }

}