<?php
class Sober_Taxonomy_Edit {
	/**
	 * The single instance of the class
	 *
	 * @var Sober_Taxonomy_Edit
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return Sober_Taxonomy_Edit
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construction
	 */
	public function __construct() {
		$taxonomies = get_object_taxonomies( 'post' );
		$taxonomies = array_merge( $taxonomies, get_object_taxonomies( 'portfolio' ) );

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'add_page_header_fields' ), 20 );
		}

		add_action( 'edit_term', array( $this, 'save' ), 20, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'term.php', 'edit-tags.php' ) ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'sober-term-page-header-image', get_template_directory_uri() . '/js/admin/terms.js', array( 'jquery' ), '3.0', true );
		wp_localize_script( 'sober-term-page-header-image', 'soberTermData', array(
			'placeholder' => get_theme_file_uri( '/images/placeholder.png' ),
			'l10n'        => array(
				'title'  => esc_html__( 'Choose an image', 'sober' ),
				'button' => esc_html__( 'Use image', 'sober' ),
			),
		) );
	}

	/**
	 * Add page header setting fields
	 *
	 * @param object $term Term being edited
	 */
	public function add_page_header_fields( $term ) {
		$text_color = get_term_meta( $term->term_id, 'page_header_text_color', true );
		$image_id   = absint( get_term_meta( $term->term_id, 'page_header_image_id', true ) );
		$image      = $image_id ? wp_get_attachment_thumb_url( $image_id ) : get_theme_file_uri( '/images/placeholder.png' );
		?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Page Header Image', 'sober' ); ?></label>
			</th>
			<td>
				<div id="page-header-image" style="float: left; margin-right: 10px;">
					<img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" />
				</div>
				<div style="line-height: 60px;">
					<input type="hidden" id="page-header-image-id" name="page_header_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
					<button type="button" class="upload-header-image-button button"><?php esc_html_e( 'Upload/Add Image', 'sober' ); ?></button>
					<button type="button" class="remove-header-image-button button"><?php esc_html_e( 'Remove Image', 'sober' ); ?></button>
				</div>
				<div class="clear"></div>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="page-header-text-color"><?php esc_html_e( 'Page Header Text Color', 'sober' ); ?></label>
			</th>
			<td>
				<select name="page_header_text_color" id="page-header-text-color" class="postform">
					<option value=""><?php esc_html_e( 'Default', 'sober' ) ?></option>
					<option value="dark" <?php selected( 'dark', $text_color ) ?>><?php esc_attr_e( 'Dark', 'sober' ) ?></option>
					<option value="light" <?php selected( 'light', $text_color ) ?>><?php esc_attr_e( 'Light', 'sober' ) ?></option>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save term's additional meta
	 *
	 * @param mixed  $term_id Term ID being saved
	 * @param mixed  $tt_id
	 * @param string $taxonomy
	 */
	public function save( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['page_header_image_id'] ) ) {
			update_term_meta( $term_id, 'page_header_image_id', absint( $_POST['page_header_image_id'] ) );
		}

		if ( isset( $_POST['page_header_text_color'] ) ) {
			update_term_meta( $term_id, 'page_header_text_color', $_POST['page_header_text_color'] );
		}
	}
}