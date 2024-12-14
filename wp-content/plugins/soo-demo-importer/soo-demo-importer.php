<?php
/**
 * Plugin Name: Soo Demo Importer
 * Plugin URI: https://uix.store/
 * Description: One click import demo content
 * Author: UIX Themes
 * Author URI: https://uix.store/
 * Version: 1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for importing demo content
 */
class Soo_Demo_Importer {
	/**
	 * Demo data configuration
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Nested array of data tabs
	 *
	 * @var array
	 */
	protected $data_tabs;

	/**
	 * Construction function
	 * Add new  submenu under Appearance menu
	 */
	public function __construct() {
		$this->data = apply_filters( 'soo_demo_packages', array() );

		$this->validate_data();
		$this->group_data_by_tab();

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		add_action( 'wp_ajax_soodi_download_file', array( $this, 'ajax_download_file' ) );
		add_action( 'wp_ajax_soodi_import', array( $this, 'ajax_import' ) );
		add_action( 'wp_ajax_soodi_config_theme', array( $this, 'ajax_config_theme' ) );
		add_action( 'wp_ajax_soodi_get_images', array( $this, 'ajax_get_images' ) );
		add_action( 'wp_ajax_soodi_generate_image', array( $this, 'ajax_generate_image' ) );
	}

	/**
	 * Validate data
	 */
	public function validate_data() {
		if ( empty( $this->data ) ) {
			return;
		}

		// Ensure all data has the "tab" key.
		array_walk( $this->data, function( &$data ) {
			if ( is_array( $data ) && ! isset( $data['tab'] ) ) {
				$data['tab'] = esc_html__( 'Default', 'soodi' );
			}
		} );
	}

	/**
	 * Group data by tab
	 */
	public function group_data_by_tab() {
		if ( empty( $this->data ) ) {
			return;
		}

		$this->data_tabs = array();

		foreach ( $this->data as $demo => $data ) {
			if ( is_array( $data ) ) {
				$this->data_tabs[ $data['tab'] ][ $demo ] = $data;
			}
		}
	}

	/**
	 * Add new menu under Appearance menu
	 */
	public function menu() {
		if ( empty( $this->data ) ) {
			return;
		}

		add_theme_page(
			esc_html__( 'Import Demo Data', 'soodi' ),
			esc_html__( 'Import Demo Data', 'soodi' ),
			'edit_theme_options',
			'import-demo-content',
			array( $this, 'page' )
		);
	}

	/**
	 * Load scripts
	 */
	public function scripts( $hook ) {
		if ( 'appearance_page_import-demo-content' != $hook ) {
			return;
		}

		wp_enqueue_style( 'soo-import', plugins_url( 'assets/css/soo-import.css', __FILE__ ) );
		wp_enqueue_script( 'soo-import', plugins_url( 'assets/js/soo-import.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Admin page for importing demo content
	 */
	public function page() {
		if ( isset( $_GET['step'] ) ) {
			if ( 'upload' == $_GET['step'] ) {
				$this->upload_files_page();
			} else {
				$this->import_demo_page();
			}
		} else {
			$this->select_demo_page();
		}
	}

	/**
	 * HTML for select demo page
	 */
	private function select_demo_page() {
		$active_tab = ! empty( $this->data['active_tab'] ) ? $this->data['active_tab'] : key( $this->data_tabs );
		$parts = array(
			'content' => __( 'Content', 'soodi' ),
			'posts' => __( 'Posts', 'soodi' ),
			'pages' => __( 'Pages', 'soodi' ),
			'products' => __( 'Products', 'soodi' ),
			'portfolio' => __( 'Portfolio', 'soodi' ),
			'widgets' => __( 'Widgets', 'soodi' ),
			'customizer' => __( 'Customizer/Theme Settings', 'soodi' ),
			'sliders' => __( 'Sliders', 'soodi' ),
		);
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="updated notice is-dismissible">
				<p><?php _e( 'Before start importing, you have to install all required plugins and other plugins that you want to use.', 'soodi' ) ?></p>
				<p><?php _e( 'It usally take few minutes to finish. Please be patient.', 'soodi' ) ?></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'soodi' ) ?></span></button>
			</div>


			<nav class="nav-tab-wrapper soo-tab-nav-wrapper <?php echo ( 1 === count( $this->data_tabs ) ) ? 'hidden' : ''; ?>">
				<?php foreach( $this->data_tabs as $tab => $demos ) : ?>
					<a href="#<?php echo esc_attr( sanitize_title( $tab ) ) ?>-tab" class="nav-tab <?php echo $active_tab == $tab ? 'nav-tab-active' : ''; ?>">
						<?php echo $tab; ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php foreach( $this->data_tabs as $tab => $demos ) : ?>

				<div id="<?php echo esc_attr( sanitize_title( $tab ) ) ?>-tab" class="soo-tab-panel <?php echo $active_tab == $tab ? 'tab-panel-active' : ''; ?> demos-container">

					<?php foreach( $demos as $demo => $data ) : ?>

						<form action="<?php echo esc_url( add_query_arg( array( 'step' => 'import' ) ) ) ?>" method="post" class="demo-selector">
							<input type="hidden" name="demo" value="<?php echo esc_attr( $demo ) ?>">

							<div class="demo-image">
								<span class="tab-tag <?php echo ( 1 === count( $this->data_tabs ) ) ? 'hidden' : ''; ?>"><?php echo esc_html( $data['tab'] ) ?></span>
								<img src="<?php echo esc_url( $data['preview'] ) ?>">
							</div>

							<div class="demo-selector-tools">
								<div class="demo-install-progress"></div>
								<div class="demo-install-actions">
									<button type="submit" class="button button-primary"><?php _e( 'Import', 'soodi' ) ?></button>
									<button type="button" class="button-link toggle-options"><?php _e( 'Options', 'soodi' ) ?></button>
									<h2 class="demo-title"><?php echo $data['name'] ?></h2>
									<div class="clear"></div>
								</div>
							</div>

							<div class="demo-import-options hidden">
								<div class="demo-import-options__wrapper">
									<?php
									foreach ( $parts as $part => $label ) {
										$file_url = '';

										if ( isset( $data['files'] ) && ! empty( $data['files'][ $part ] ) ) {
											$file_url = $data['files'][ $part ];
										} elseif ( isset( $data[ $part ] ) ) {
											$file_url = $data[ $part ];
										}

										if ( empty( $file_url ) || ! is_string( $file_url ) ) {
											continue;
										}

										printf(
											'<p><label><input type="checkbox" name="demo_parts[]" value="%s" checked> %s</label></p>',
											esc_attr( $part ),
											esc_html( $label )
										);
									}
									?>
									<p>
										<label>
											<input type="checkbox" name="regenerate_images" value="1" checked>
											<?php esc_html_e( 'Regenerate images (Optional)', 'soodi' ); ?>
										</label>
									</p>
								</div>
							</div>
						</form>

					<?php endforeach; ?>

				</div>

			<?php endforeach; ?>

			<p style="text-align:center;">
				<a href="<?php echo esc_url( add_query_arg( array( 'step' => 'upload' ) ) ); ?>"><?php _e( 'Manually upload demo files', 'soodi' ); ?></a>
			</p>

		</div>

		<?php
	}

	/**
	 * HTML for upload demo files
	 */
	private function upload_files_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<form method="post" action="<?php echo esc_url( add_query_arg( array( 'step' => 'import' ) ) ) ?>" enctype="multipart/form-data">
				<input type="hidden" name="upload_files" value="1">

				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="soodi-select-demo"><?php _e( 'Select demo', 'soodi' ); ?></label></th>
							<td>
								<select name="demo" id="soodi-select-demo">
									<?php foreach ( $this->data_tabs as $tab => $data ) : ?>
										<?php if ( count( $this->data_tabs ) > 1 ) : ?>
											<optgroup label="<?php echo esc_attr( $tab ) ?>">
										<?php endif; ?>

										<?php foreach( $data as $demo => $data ) : ?>
											<option value="<?php echo esc_attr( $demo ) ?>"><?php echo $data['name'] ?></option>
										<?php endforeach; ?>

										<?php if ( count( $this->data_tabs ) > 1 ) : ?>
											</optgroup>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for="soodi-content-file"><?php _e( 'Content file', 'soodi' ); ?></label></th>
							<td>
								<input type="file" name="content_file" id="soodi-content-file">
								<p class="description"><?php _e( 'File demo-content.xml', 'soodi' ) ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="soodi-customizer-file"><?php _e( 'Customizer file', 'soodi' ); ?></label></th>
							<td>
								<input type="file" name="customizer_file" id="soodi-customizer-file">
								<p class="description"><?php _e( 'File customizer.dat', 'soodi' ) ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="soodi-widgets-file"><?php _e( 'Widgets file', 'soodi' ); ?></label></th>
							<td>
								<input type="file" name="widgets_file" id="soodi-widgets-file">
								<p class="description"><?php _e( 'File widgets.wie', 'soodi' ) ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="soodi-sliders-file"><?php _e( 'Sliders file', 'soodi' ); ?></label></th>
							<td>
								<input type="file" name="sliders_file" id="soodi-sliders-file">
								<p class="description"><?php _e( 'Upload the slider file', 'soodi' ) ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Upload & Import', 'soodi' ) ?>">
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * HTML for import demo page
	 */
	private function import_demo_page() {
		$demo = $_POST['demo'];
		$demo_parts = isset( $_POST['demo_parts'] ) ? implode( ',', $_POST['demo_parts'] ) : 'content,customizer,widgets,sliders';

		if ( ! array_key_exists( $demo, $this->data ) ) {
			printf(
				'<div class="notice warning"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'This import is invalid.', 'soodi' ),
				remove_query_arg( 'step' ),
				esc_html__( 'Please try again', 'soodi' )
			);
			return;
		}

		if ( isset( $_POST['upload_files'] ) ) {
			$this->upload_files();
		}
		?>

		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<form id="soo-demo-importer" class="hidden" method="post">
				<input type="hidden" name="demo" value="<?php echo esc_attr( $demo ) ?>">
				<input type="hidden" name="demo_parts" value="<?php echo esc_attr( $demo_parts ) ?>">
				<input type="hidden" name="uploaded" value="<?php echo isset( $_POST['upload_files'] ) ? 1 : 0 ?>">
				<input type="hidden" name="regenerate_images" value="<?php echo isset( $_POST['regenerate_images'] ) ? 1 : 0 ?>">
				<?php wp_nonce_field( 'soo_demo_import' ) ?>
			</form>

			<div id="soo-demo-import-progress" class="notice warning notice-warning">
				<p>
					<span class="spinner is-active"></span>
					<span class="text"><?php esc_html_e( 'Starting...', 'soodi' ) ?></span>
				</p>
			</div>

			<div id="soo-demo-import-log" class="soo-demo-log"></div>
		</div>

		<?php
	}

	/**
	 * Upload demo files
	 */
	private function upload_files() {
		$path = $this->get_demo_path();

		@rmdir( $path );
		wp_mkdir_p( $path );

		if ( ! empty( $_FILES['content_file'] ) ) {
			$file = $path . '/demo-content.xml';

			@ move_uploaded_file( $_FILES['content_file']['tmp_name'], $file );
		}

		if ( ! empty( $_FILES['customizer_file'] ) ) {
			$file = $path . '/customizer.dat';

			@ move_uploaded_file( $_FILES['customizer_file']['tmp_name'], $file );
		}

		if ( ! empty( $_FILES['widgets_file'] ) ) {
			$file = $path . '/widgets.wie';

			@ move_uploaded_file( $_FILES['widgets_file']['tmp_name'], $file );
		}

		if ( ! empty( $_FILES['sliders_file'] ) ) {
			$file = $path . '/sliders.zip';

			if ( file_exists( $file ) ) {
				unlink( $file );
			}

			@ move_uploaded_file( $_FILES['sliders_file']['tmp_name'], $file );
		}
	}

	/**
	 * Import content
	 * Import posts, pages, menus, custom post types
	 *
	 * @param  string $file The exported file's name
	 */
	public function import_content( $file ) {
		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		add_filter( 'intermediate_image_sizes_advanced', '__return_null' );

		if ( ! file_exists( $file ) ) {
			return false;
		}

		// Import content.
		if ( ! class_exists( 'Soo_Demo_Content_Importer' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-content-importer.php';
		}

		do_action( 'soodi_before_import_content', $file );

		$importer = new Soo_Demo_Content_Importer( array(
			'fetch_attachments' => true
		) );

		if ( ! class_exists( 'WP_Importer_Logger' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-logger.php';
		}

		if ( ! class_exists( 'WP_Importer_Logger_ServerSentEvents' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-logger-serversentevents.php';
		}

		$logger = new WP_Importer_Logger_ServerSentEvents();
		$err = $importer->set_logger( $logger );

		return $importer->import( $file );
	}

	/**
	 * Import theme options
	 *
	 * @param  string $file The exported file's name
	 */
	public function import_customizer( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		if( ! class_exists( 'Soo_Demo_Customizer_Importer' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/customizer-importer.php';
		}

		do_action( 'soodi_before_import_customizer', $file );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		add_filter( 'intermediate_image_sizes_advanced', '__return_null' );

		$import = new Soo_Demo_Customizer_Importer();
		$import->download_images = true;
		$import->import( $file );

		return true;
	}

	/**
	 * Import widgets
	 *
	 * @param  string $file The exported file's name
	 */
	function import_widgets( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( ! class_exists( 'Soo_Demo_Widgets_Importer') ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/widgets-importer.php';
		}

		do_action( 'soodi_before_import_widgets', $file );

		$data 		= json_decode( file_get_contents( $file ) );

		$importer   = new Soo_Demo_Widgets_Importer();
		$importer->import( $data );

		return true;
	}

	/**
	 * Import exported revolution sliders
	 */
	public function import_sliders() {
		if ( ! class_exists( 'RevSliderSliderImport' ) ) {
			return new WP_Error( 'plugin-not-installed', esc_html__( 'Revolution Slider plugin is not installed', 'soodi' ) );
		}

		$path = $this->get_demo_path();
		$sliders_zip = $path . '/sliders.zip';

		if ( empty( $sliders_zip ) ) {
			return new WP_Error( 'import-sliders', esc_html__( 'There is no slider to import', 'soodi' ) );
		}

		$unzipfile = unzip_file( $sliders_zip, $path );

		if( is_wp_error( $unzipfile ) ) {
			define('FS_METHOD', 'direct'); //lets try direct.

			WP_Filesystem();

			$unzipfile = unzip_file( $sliders_zip, $path );
			@unlink( $sliders_zip );
		}

		$files = scandir( $path . '/sliders/' );

		if ( empty( $files ) ) {
			return new WP_Error( 'import-sliders', esc_html__( 'There is no slider to import', 'soodi' ) );
		}

		$sliderImporter = new RevSliderSliderImport();

		$this->emit_sse_message( array(
			'action' => 'updateTotal',
			'type'   => 'slider',
			'delta'  => count( $files ) - 2,
		));

		foreach( $files as $file ) {
			if ( $file == '.' || $file == '..' ) {
				continue;
			}
			$file = $path . '/sliders/' . $file;

			$response = $sliderImporter->import_slider( true, $file );

			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'slider',
				'delta'  => $response,
			));

			unlink( $file );
		}
	}

	/**
	 * Import menu locations
	 *
	 * @param  string $file The exported file's name
	 */
	public function setup_menus( $demo ) {
		$demo = $this->data[$demo];

		if ( ! isset( $demo['menus'] ) ) {
			return;
		}

		$data = $demo['menus'];
		$menus = wp_get_nav_menus();
		$locations = array();

		foreach( $data as $key => $val ) {
			foreach( $menus as $menu ) {
				if( $val && $menu->slug == $val ) {
					$locations[$key] = absint( $menu->term_id );
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Setup pages
	 */
	public function setup_pages( $demo ) {
		$demo = $this->data[$demo];

		if ( ! isset( $demo['pages'] ) ) {
			return;
		}

		// Front Page
		if ( isset( $demo['pages']['front_page'] ) ) {
			$home = get_page_by_title( $demo['pages']['front_page'] );

			if ( $home ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $home->ID );
			}
		}

		// Blog Page
		if ( isset( $demo['pages']['blog'] ) ) {
			$blog = get_page_by_title( $demo['pages']['blog'] );

			if ( $blog ) {
				update_option( 'page_for_posts', $blog->ID );
			}
		}

		// WooCommerce Pages
		if ( class_exists( 'WooCommerce' ) ) {
			// Shop page
			if ( isset( $demo['pages']['shop'] ) ) {
				$shop = get_page_by_title( $demo['pages']['shop'] );

				if ( $shop ) {
					update_option( 'woocommerce_shop_page_id', $shop->ID );
				}
			}

			// Cart page
			if ( isset( $demo['pages']['cart'] ) ) {
				$cart = get_page_by_title( $demo['pages']['cart'] );

				if ( $cart ) {
					update_option( 'woocommerce_cart_page_id', $cart->ID );
				}
			}

			// Checkout page
			if ( isset( $demo['pages']['checkout'] ) ) {
				$checkout = get_page_by_title( $demo['pages']['checkout'] );

				if ( $checkout ) {
					update_option( 'woocommerce_checkout_page_id', $checkout->ID );
				}
			}

			// Myaccount page
			if ( isset( $demo['pages']['my_account'] ) ) {
				$account = get_page_by_title( $demo['pages']['my_account'] );

				if ( $account ) {
					update_option( 'woocommerce_myaccount_page_id', $account->ID );
				}
			}
		}

		do_action( 'soodi_after_setup_pages', $demo['pages'] );

		flush_rewrite_rules();
	}

	/**
	 * Update options
	 *
	 * @param  int $demo
	 */
	public function update_options( $demo ) {
		$demo = $this->data[$demo];

		if ( empty( $demo['options'] ) ) {
			return;
		}

		foreach ( $demo['options'] as $option => $value) {
			update_option( $option, $value );
		}
	}

	/**
	 * Generate image sizes
	 * @param  int $id
	 */
	public function generate_image( $id ) {
		$fullsizepath = get_attached_file( $id );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
			return false;
		}

		$metadata = wp_generate_attachment_metadata( $id, $fullsizepath );

		if ( ! $metadata || is_wp_error( $metadata ) ) {
			return false;
		}

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $id, $metadata );

		return true;
	}

	/**
	 * Ajax function to import demo content
	 */
	public function ajax_import() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'soo_demo_import' ) ) {
			wp_send_json_error( esc_html__( 'Verifing failed', 'soodi' ) );
			exit;
		}

		// Turn off PHP output compression
		$previous = error_reporting( error_reporting() ^ E_WARNING );
		ini_set( 'output_buffering', 'off' );
		ini_set( 'zlib.output_compression', false );
		error_reporting( $previous );

		if ( $GLOBALS['is_nginx'] ) {
			// Setting this header instructs Nginx to disable fastcgi_buffering
			// and disable gzip for this request.
			header( 'X-Accel-Buffering: no' );
			header( 'Content-Encoding: none' );
		}

		// Start the event stream.
		header( 'Content-Type: text/event-stream, charset=UTF-8' );

		set_time_limit( 0 );

		// Ensure we're not buffered.
		wp_ob_end_flush_all();
		flush();

		$type = $_GET['type'];
		$dir = $this->get_demo_path();

		switch ( $type ) {
			case 'content':
				$file = $dir . '/demo-content.xml';
				$result = $this->import_content( $file );
				break;

			case 'posts':
				$file = $dir . '/posts.xml';
				$result = $this->import_content( $file );
				break;

			case 'pages':
				$file = $dir . '/pages.xml';
				$result = $this->import_content( $file );
				break;

			case 'products':
				$file = $dir . '/products.xml';
				$result = $this->import_content( $file );
				break;

			case 'portfolio':
				$file = $dir . '/portfolio.xml';
				$result = $this->import_content( $file );
				break;

			case 'customizer':
				$file = $dir . '/customizer.dat';
				$result = $this->import_customizer( $file );
				break;

			case 'widgets':
				$file = $dir . '/widgets.wie';
				$result = $this->import_widgets( $file );
				break;

			case 'sliders':
				$result = $this->import_sliders();
				break;
		}

		// Let the browser know we're done.
		$complete = array(
			'action' => 'complete',
			'error' => false,
		);

		if ( is_wp_error( $result ) ) {
			$complete['error'] = $result->get_error_message();
		}

		@unlink( $file );

		$this->emit_sse_message( $complete );
		exit;
	}

	/**
	 * Ajax function to download file
	 */
	public function ajax_download_file() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'soo_demo_import' ) ) {
			wp_send_json_error( esc_html__( 'Verifing failed', 'soodi' ) );
			exit;
		}

		$demo = $_GET['demo'];
		$demo = $this->data[$demo];

		$type = $_GET['type'];

		if ( ! isset( $demo[$type] ) && ! isset( $demo['files'][$type] ) ) {
			wp_send_json_error( esc_html__( 'This demo dose not need', 'soodi' ) . " $type" );
			exit;
		}

		if ( isset( $demo['files'] ) && isset( $demo['files'][$type] ) ) {
			$file_url = $demo['files'][$type];
		} elseif ( isset( $demo[ $type ] ) ) {
			$file_url = $demo[ $type ];
		}

		if ( empty( $file_url ) ) {
			wp_send_json_error( esc_html__( 'File does not exists', 'soodi' ) );
			exit;
		}

		if ( $_GET['uploaded'] ) {
			wp_send_json_success();
			return;
		}

		@set_time_limit(0);

		$file = $this->download_file( $file_url );

		if ( is_wp_error( $file ) ) {
			wp_send_json_error( $file->get_error_message() );
			exit;
		}

		wp_send_json_success();
	}

	/**
	 * Ajax function to setup front page and blog page
	 */
	public function ajax_config_theme() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'soo_demo_import' ) ) {
			wp_send_json_error( esc_html__( 'Verifing failed', 'soodi' ) );
			exit;
		}

		$demo = $_GET['demo'];

		// Setup pages
		$this->setup_pages( $demo );

		// Setup menu locations
		$this->setup_menus( $demo );

		// Update options
		$this->update_options( $demo );

		wp_send_json_success( esc_html__( 'Finish setting up front page and blog page.', 'soodi' ) );
	}


	/**
	 * Ajax function to get all image ids
	 */
	public function ajax_get_images() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'soo_demo_import' ) ) {
			wp_send_json_error( esc_html__( 'Verifing failed', 'soodi' ) );
			exit;
		}

		global $wpdb;

		$images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" );

		$ids = array();

		if ( $images ) {
			foreach ( $images as $image ) {
				$ids[] = $image->ID;
			}
		}

		wp_send_json_success( $ids );
	}

	/**
	 * Ajax function to generate a single image
	 */
	public function ajax_generate_image() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'soo_demo_import' ) ) {
			wp_send_json_error( esc_html__( 'Verifing failed', 'soodi' ) );
			exit;
		}

		$id = absint( $_REQUEST['id'] );

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY

		$result = $this->generate_image( $id );

		if ( ! $result ) {
			wp_send_json_error( esc_html__( 'Failed to generate image ID:', 'soodi' ) . " $id" );
		} else {
			wp_send_json_success( sprintf( esc_html__( 'Generated image ID %s successfully', 'soodi' ), $id ) );
		}
	}

	/**
	 * Download file from URL
	 *
	 * @param  staring $file_url
	 * @return string  Downloaded file path
	 */
	protected function download_file( $file_url ) {
		$filename = basename( $file_url );
		$path = $this->get_demo_path();
		$file = $path . '/' . $filename;

		wp_mkdir_p( $path );

		$ifp = @fopen( $file, 'wb' );

		if ( ! $ifp ) {
			return new WP_Error( 'import_file_error', sprintf( __( 'Could not write file %s' ), $file ) );
		}

		@fwrite( $ifp, 0 );
		fclose( $ifp );
		clearstatcache();

		// Set correct file permissions
		$stat = @stat( dirname( $file ) );
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@chmod( $file, $perms );
		clearstatcache();

		$response = wp_remote_get( $file_url, array(
			'stream' => true,
			'filename' => $file,
			'timeout' => 500,
		) );

		// request failed
		if ( is_wp_error( $response ) ) {
			@unlink( $file );
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );

		// make sure the fetch was successful
		if ( $code !== 200 ) {
			@unlink( $file );

			return new WP_Error(
				'import_file_error',
				sprintf(
					esc_html__( 'Remote server returned %1$d %2$s for %3$s', 'soodi' ),
					$code,
					get_status_header_desc( $code ),
					$file_url
				)
			);
		}

		if ( 0 == filesize( $file ) ) {
			@unlink( $file );
			return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded', 'soodi' ) );
		}


		return $file;
	}

	/**
	 * Emit a Server-Sent Events message.
	 *
	 * @param mixed $data Data to be JSON-encoded and sent in the message.
	 */
	protected function emit_sse_message( $data ) {
		echo "event: message\n";
		echo 'data: ' . wp_json_encode( $data ) . "\n\n";

		// Extra padding.
		echo ':' . str_repeat( ' ', 2048 ) . "\n\n";

		flush();
	}

	/**
	 * Get the path to demo directory
	 *
	 * @return string
	 */
	private function get_demo_path() {
		$upload_dir = wp_upload_dir();
		$dir = path_join( $upload_dir['basedir'], 'demo_files' );

		return $dir;
	}
}

if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	add_action( 'admin_notices', function() {
		$message = sprintf( __( 'The <strong>Soo Demo Importer</strong> plugin requires <strong>PHP 5.4.0+</strong> to run properly. Please contact your hosting company and ask them to update the PHP version of your site to at least PHP 5.4.0.<br> Your current version of PHP: <strong>%s</strong>', 'soodi' ), phpversion() );

		printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
	} );
} else {
	add_action( 'init', function() {
		new Soo_Demo_Importer();
	} );
}
