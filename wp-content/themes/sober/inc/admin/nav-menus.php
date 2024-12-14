<?php
/**
 * Customize and add more fields for mega menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sober_Mega_Menu_Edit
 *
 * Main class for adding mega setting modal
 */
class Sober_Mega_Menu_Edit {
	/**
	 * Modal screen of mega menu settings
	 *
	 * @var array
	 */
	public $modals = array();

	/**
	 * The single instance of the class
	 *
	 * @var Sober_Mega_Menu_Edit
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return Sober_Mega_Menu_Edit
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->modals = apply_filters( 'sober_mega_menu_modals', array(
			'menus',
			'title',
			'mega',
			'background',
			'icon',
			'content',
			'design',
			'settings',
		) );

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_settings_link' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'modal' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'templates' ) );
		add_action( 'wp_ajax_sober_save_menu_item_data', array( $this, 'save_menu_item_data' ) );
	}

	public function add_settings_link( $item_id ) {
		$mega_data    = get_post_meta( $item_id, '_menu_item_mega', true );
		$mega_data    = sober_parse_args( $mega_data, sober_get_mega_menu_setting_default() );
		$mega_content = $mega_data['content'];

		unset( $mega_data['content'] );
		?>
		<fieldset class="field-menu-settings hide-if-no-js description-wide">
			<span class="field-move-visual-label" aria-hidden="true"><?php esc_html_e( 'Mega Menu', 'sober' ) ?></span>
			<span class="hidden mega-data" data-mega="<?php echo esc_attr( json_encode( $mega_data ) ) ?>" aria-hidden="true"><?php echo trim( $mega_content ); ?></span>
			<button type="button" class="item-config-mega opensettings button-link"><?php esc_html_e( 'Open Settings', 'sober' ) ?></button>
		</fieldset>
		<?php
	}

	/**
	 * Load scripts on Menus page only
	 *
	 * @param string $hook
	 */
	public function scripts( $hook ) {
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_register_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.6.3' );
		wp_register_style( 'sober-mega-menu-admin', get_template_directory_uri() . '/css/admin/mega-menu.css', array(
			'media-views',
			'wp-color-picker',
			'font-awesome',
		) );
		wp_enqueue_style( 'sober-mega-menu-admin' );

		wp_register_script( 'sober-mega-menu-admin', get_template_directory_uri() . '/js/admin/mega-menu.js', array(
			'jquery',
			'jquery-ui-resizable',
			'wp-util',
			'wp-color-picker',
		), null, true );
		wp_enqueue_media();
		wp_enqueue_script( 'sober-mega-menu-admin' );

		wp_localize_script( 'sober-mega-menu-admin', 'soberMegaMenuConfig', array(
			'templates' => $this->modals,
			'l10n' => array(
				'close_confirm' => esc_html__( 'Your changes are not saved. Do you want to leave?', 'sober' ),
			),
		) );
	}

	/**
	 * Prints HTML of modal on footer
	 */
	public function modal() {
		?>
		<div id="smm-settings" tabindex="0" class="smm-settings">
			<div class="smm-modal media-modal wp-core-ui">
				<button type="button" class="button-link media-modal-close smm-modal-close">
					<span class="media-modal-icon"><span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span></span>
				</button>
				<div class="media-modal-content">
					<div class="smm-frame-menu media-frame-menu">
						<div class="smm-menu media-menu"></div>
					</div>
					<div class="smm-frame-title media-frame-title"></div>
					<div class="smm-frame-content media-frame-content">
						<div class="smm-content"></div>
					</div>
					<div class="smm-frame-toolbar media-frame-toolbar">
						<div class="smm-toolbar media-toolbar">
							<button type="button" class="button smm-button smm-button-save media-button button-primary button-large" disabled><?php esc_html_e( 'Save Changes', 'sober' ) ?></button>
							<button type="button" class="button smm-button smm-button-cancel media-button button-secondary button-large"><?php esc_html_e( 'Cancel', 'sober' ) ?></button>
							<span class="spinner"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="media-modal-backdrop smm-modal-backdrop"></div>
		</div>
		<?php
	}

	/**
	 * Prints underscore template on footer
	 */
	public function templates() {
		foreach ( $this->modals as $template ) {
			$file = get_theme_file_path( 'inc/admin/menu-templates/' . $template . '.php' );
			$file = apply_filters( 'sober_mega_menu_modal_template_file', $file, $template );

			if ( ! file_exists( $file ) ) {
				continue;
			}
			?>
			<script type="text/html" id="tmpl-sober-<?php echo esc_attr( $template ) ?>">
				<?php include( $file ); ?>
			</script>
			<?php
		}
	}

	/**
	 * Ajax function to save menu item data
	 */
	public function save_menu_item_data() {
		$_POST['data'] = stripslashes_deep( $_POST['data'] );
		parse_str( $_POST['data'], $data );
		$updated = $data;

		// Save menu item data
		foreach ( $data['menu-item-mega'] as $id => $meta ) {
			$old_meta = get_post_meta( $id, '_menu_item_mega', true );
			$old_meta = sober_parse_args( $old_meta, sober_get_mega_menu_setting_default() );
			$meta     = sober_parse_args( $meta, $old_meta );

			$updated['menu-item-mega'][ $id ] = $meta;

			update_post_meta( $id, '_menu_item_mega', $meta );
		}

		wp_send_json_success( $updated );
	}
}
