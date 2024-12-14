<?php
/**
 * Template status trait
 *
 * @version 1.0.1
 *
 * @package WCBoost\Packages\TemplatesStatus
 */
namespace WCBoost\Packages\TemplatesStatus;

trait Templates_Trait {
	/**
	 * Plugin template paths
	 *
	 * @var array
	 */
	protected $paths = [];

	/**
	 * Add the path to template files
	 *
	 * @param  string $plugin_name
	 * @param  string $templates_path
	 *
	 * @return void
	 */
	public function add_templates_path( $plugin_name, $templates_path ) {
		$this->paths[ $plugin_name ] = $templates_path;
	}

	/**
	 * Check if the active theme contains outdated templates of this plugin.
	 *
	 * @param string $fields The returned fields.
	 * 		Leave it empty to get full data.
	 * 		Set as "outdated" for faster checking.
	 * 		Set as "plugins" to get full list of plugins.
	 *
	 * @return null|array The result with parameters:
	 *		bool  "outdated" - True if the theme contains outdated templates
	 *		array "plugins" - List of plugins containing new templates
	 *		array "files" - List of overrided templates
	 */
	public function check_override_templates( $fields = '' ) {
		if ( ! class_exists( 'WC_Admin_Status' ) && function_exists( 'WC' ) ) {
			require_once WC()->plugin_path() . '/includes/admin/class-wc-admin-status.php';
		}

		if ( empty( $this->paths ) ) {
			return null;
		}

		$override_files   = [];
		$outdated_plugins = [];
		$is_outdated      = false;

		foreach ( $this->paths as $plugin_name => $templates_path ) {
			$templates_path = trailingslashit( $templates_path );
			$template_files = \WC_Admin_Status::scan_template_files( $templates_path );

			foreach ( $template_files as $file ) {
				if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
					$theme_file = get_stylesheet_directory() . '/' . $file;
				} elseif ( file_exists( get_stylesheet_directory() . '/' . WC()->template_path() . $file ) ) {
					$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
				} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
					$theme_file = get_template_directory() . '/' . $file;
				} elseif ( file_exists( get_template_directory() . '/' . WC()->template_path() . $file ) ) {
					$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
				} else {
					$theme_file = false;
				}

				if ( false !== $theme_file ) {
					$core_version  = \WC_Admin_Status::get_file_version( $templates_path . $file );
					$theme_version = \WC_Admin_Status::get_file_version( $theme_file );

					if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
						$is_outdated = true;

						if ( 'plugins' == $fields ) {
							$outdated_plugins[] = $plugin_name;
						}

						if ( 'outdated' == $fields || 'plugins' == $fields ) {
							break;
						}
					}

					if ( empty( $fields ) ) {
						$override_files[] = [
							'file'         => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
							'version'      => $theme_version,
							'core_version' => $core_version,
						];
					}
				}
			}

			if ( 'outdated' == $fields && $is_outdated ) {
				break;
			}
		}

		return [
			'outdated' => $is_outdated,
			'plugins'  => $outdated_plugins,
			'files'    => $override_files,
		];
	}
}
