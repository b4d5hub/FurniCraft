<?php
/**
 * Packages manager
 *
 * @version 1.0.1
 */
namespace WCBoost\Packages;

/**
 * Class WCBoost\Packages\Manager
 */
class Manager {

	/**
	 * Loaded packages
	 *
	 * @var array
	 */
	protected static $packages = [];

	/**
	 * Base path for packages
	 *
	 * @var string
	 */
	protected $base;

	/**
	 * Packages manager constructor
	 *
	 * @param string $dir The base directory path
	 */
	public function __construct( $dir ) {
		$this->base = untrailingslashit( $dir );

		$this->load_package( 'utilities\singleton-trait' );
	}

	/**
	 * Loads and initializes the package
	 *
	 * @param  string $name The package name
	 *
	 * @return void
	 */
	public function load_package( $name ) {
		$files   = [];
		$package = strtolower( $name );

		if ( array_key_exists( $name, static::$packages ) ) {
			return;
		}

		switch ( $package ) {
			case 'utilities\singleton-trait':
				if ( ! trait_exists( 'WCBoost\Packages\Utilities\Singleton_Trait' ) ) {
					$files[] = $this->base . '/utilities/singleton-trait.php';
				}
				break;

			case 'templates-status':
				if ( ! trait_exists( 'WCBoost\Packages\TemplatesStatus\Templates_Trait' ) ) {
					$files[] = $this->base . '/templates-status/templates-trait.php';
				}

				if ( ! class_exists( 'WCBoost\Packages\TemplatesStatus\Notice' ) ) {
					$files[] =  $this->base . '/templates-status/notice.php';
				}

				if ( ! class_exists( 'WCBoost\Packages\TemplatesStatus\Status' ) ) {
					$files[] =  $this->base . '/templates-status/status.php';
				}
				break;
		}

		foreach ( $files as $file ) {
			if ( ! file_exists( $file ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( "Missing the package file of `$package`: $file" );
				}

				continue;
			}

			include_once( $file );

			static::$packages[ $package ] = null;
		}

		$this->init_package( $package );
	}

	/**
	 * Initializes the package
	 *
	 * @param  string $name
	 *
	 * @return void
	 */
	public function init_package( $name ) {
		switch ( $name ) {
			case 'templates-status':
				static::$packages[ $name ] = \WCBoost\Packages\TemplatesStatus\Status::instance();
				break;
		}
	}

	/**
	 * Get package instance
	 *
	 * @since  1.0.1
	 *
	 * @param  string $name
	 *
	 * @return mixed|null
	 */
	public function get( $name ) {
		return static::package( $name );
	}

	/**
	 * Get the package instance
	 *
	 * @param  string $name
	 *
	 * @return mixed|null
	 */
	public static function package( $name ) {
		if ( array_key_exists( $name, self::$packages ) ) {
			return self::$packages[ $name ];
		}

		return null;
	}
}
