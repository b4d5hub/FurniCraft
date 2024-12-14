<?php
namespace WCBoost\VariationSwatches\Admin;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Helper;
use WCBoost\VariationSwatches\Plugin;

class Term_Meta {
	const COLOR_META_KEY = 'swatches_color';
	const LABEL_META_KEY = 'swatches_label';
	const IMAGE_META_KEY = 'swatches_image';

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var WCBoost\VariationSwatches\Admin\Term_Meta
	 */
	protected static $_instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return WCBoost\VariationSwatches\Admin\Term_Meta An instance of the class.
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'product_attributes_type_selector', [ $this, 'add_attribute_types' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) ) {
			return;
		}

		// Add custom fields.
		foreach ( $attribute_taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', [ $this, 'add_attribute_fields' ] );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', [ $this, 'edit_attribute_fields' ], 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', [ $this, 'add_attribute_columns' ] );
			add_action( 'manage_pa_' . $tax->attribute_name . '_custom_column', [ $this, 'add_attribute_column_content' ], 10, 3 );
		}

		add_action( 'created_term', [ $this, 'save_term_meta' ] );
		add_action( 'edit_term', [ $this, 'save_term_meta' ] );
	}

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( $types ) {
		$types = array_merge( $types, $this->get_swatches_types() );

		return $types;
	}

	/**
	 * Get types array.
	 *
	 * @return array
	 */
	public function get_swatches_types() {
		return Helper::get_swatches_types();
	}

	/**
	 * Enqueue stylesheet and javascript
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( strpos( $screen->id, 'edit-pa_' ) === false && strpos( $screen->id, 'product' ) === false ) {
			return;
		}

		$version = Plugin::instance()->version;
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_media();

		wp_enqueue_style( 'wcboost-variation-swataches-term', plugins_url( '/assets/css/admin.css', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'wp-color-picker' ], $version );
		wp_enqueue_script( 'wcboost-variation-swataches-term', plugins_url( '/assets/js/admin' . $suffix . '.js', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'jquery', 'wp-color-picker', 'wp-util', 'jquery-serialize-object' ], $version, true );
	}

	/**
	 * Create hook to add fields to add attribute term screen
	 *
	 * @param string $taxonomy
	 */
	public function add_attribute_fields( $taxonomy ) {
		$attribute = Helper::get_attribute_taxonomy( $taxonomy );

		if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
		?>

		<div class="form-field term-swatches-wrap">
			<label><?php echo esc_html( $this->field_label( $attribute->attribute_type ) ); ?></label>
			<?php $this->field_input( $attribute->attribute_type ); ?>
			<p class="description"><?php esc_html_e( 'This data will be used for variation swatches of variable products.', 'wcboost-variation-swatches' ) ?></p>
		</div>

		<?php
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attribute = Helper::get_attribute_taxonomy( $taxonomy );

		if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
		?>

		<tr class="form-field form-required">
			<th scope="row" valign="top">
				<label><?php echo esc_html( $this->field_label( $attribute->attribute_type ) ); ?></label>
			</th>
			<td>
				<?php $this->field_input( $attribute->attribute_type, $term ); ?>
				<p class="description"><?php esc_html_e( 'This data will be used for variation swatches of variable products.', 'wcboost-variation-swatches' ) ?></p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the field label
	 *
	 * @param string $type
	 * @return string
	 */
	public function field_label( $type ) {
		$labels = [
			'color'  => esc_html__( 'Swatches Color', 'wcboost-variation-swatches' ),
			'image'  => esc_html__( 'Swatches Image', 'wcboost-variation-swatches' ),
			'label'  => esc_html__( 'Swatches Label', 'wcboost-variation-swatches' ),
		];

		if ( array_key_exists( $type, $labels ) ) {
			return $labels[ $type ];
		}

		return '';
	}

	/**
	 * Field name
	 *
	 * @param string $type
	 * @return string
	 */
	protected function field_name( $type ) {
		return 'wcboost_variation_swatches_' . $type;
	}

	/**
	 * The input to edit swatches data
	 *
	 * @param string $type
	 * @param object|null $term
	 */
	public function field_input( $type, $term = null ) {
		if ( ! in_array( $type, [ 'image', 'color', 'label' ] ) ) {
			return;
		}

		$value = '';

		if ( $term && is_object( $term ) ) {
			$value = $this->get_meta( $term->term_id, $type );
		}

		$args = apply_filters(
			'wcboost_variation_swatches_term_field_args',
			[
				'type'  => $type,
				'value' => $value,
				'name'  => $this->field_name( $type ),
			],
			$term
		);

		static::swatches_field( $args );
	}

	/**
	 * Save term meta
	 *
	 * @param int $term_id
	 */
	public function save_term_meta( $term_id ) {
		$types = $this->get_swatches_types();

		foreach ( $types as $type => $label ) {
			$input_name = $this->field_name( $type );
			$term_meta  = isset( $_POST[ $input_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $input_name ] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( $term_meta ) {
				$this->update_meta( $term_id, $type, $term_meta );
			}
		}
	}

	/**
	 * Add thumbnail column to column list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( $columns ) {
		$attribute_name     = ! empty( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$attribute_taxonomy = $attribute_name ? Helper::get_attribute_taxonomy( $attribute_name ) : null;

		if ( ! $attribute_taxonomy ) {
			return $columns;
		}

		if ( ! Helper::attribute_is_swatches( $attribute_taxonomy, 'edit' ) ) {
			return $columns;
		}

		$new_columns = [];

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = '';

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Render thumbnail HTML depend on attribute type
	 *
	 * @param string $content
	 * @param string $column
	 * @param int $term_id
	 */
	public function add_attribute_column_content( $content, $column, $term_id ) {
		if ( 'thumb' != $column ) {
			return;
		}

		$attribute = ! empty( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $attribute ) {
			$attr = Helper::get_attribute_taxonomy( $attribute );
		}

		if ( ! $attr ) {
			return;
		}

		$value = $this->get_meta( $term_id, $attr->attribute_type );
		$html  = '';

		switch ( $attr->attribute_type ) {
			case 'color':
				$html = sprintf(
					'<div class="wcboost-variation-swatches-item wcboost-variation-swatches-item--color" style="--wcboost-swatches-color: %s"></div>',
					esc_attr( $value )
				);
				break;

			case 'image':
				$image_src = $value ? wp_get_attachment_image_url( $value ) : false;
				$image_src = $image_src ? $image_src : wc_placeholder_img_src( 'thumbnail' );
				$html      = sprintf(
					'<img class="wcboost-variation-swatches-item wcboost-variation-swatches-item--image" src="%s" width="40px" height="40px">',
					esc_url( $image_src )
				);
				break;

			case 'label':
				$html = sprintf(
					'<div class="wcboost-variation-swatches-item wcboost-variation-swatches-item--label">%s</div>',
					esc_html( $value )
				);
				break;
		}

		$html = apply_filters( 'wcboost_variation_swatches_attribute_thumb_column_content', $html, $value, $attr, $term_id );

		if ( ! empty( $html ) ) {
			echo '<div class="wcboost-variation-swatches__thumbnail wcboost-variation-swatches--' . esc_attr( $attr->attribute_type ) . '">';
			echo wp_kses_post( $html );
			echo '</div>';
		}
	}

	/**
	 * Insert a new attribute with swatches data
	 *
	 * @param string $name
	 * @param string $tax
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function insert_term( $name, $tax, $data = [] ) {
		$term = wp_insert_term( $name, $tax );

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		if ( ! empty( $data['type'] ) && isset( $data['value'] ) ) {
			$this->update_meta( $term['term_id'], $data['type'], $data['value'] );
		}

		return $term;
	}

	/**
	 * Update attribute swatches
	 *
	 * @param int $term_id
	 * @param string $type
	 * @param mixed $value
	 * @return void
	 */
	public function update_meta( $term_id, $type, $value ) {
		$meta_key = $this->get_meta_key( $type );

		if ( empty( $meta_key ) ) {
			return;
		}

		update_term_meta( $term_id, $meta_key, $value );

		do_action( 'wcboost_variation_swatches_term_meta_updated', $value, $term_id, $meta_key, $type );
	}

	/**
	 * Get term meta.
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed
	 */
	public function get_meta( $term_id, $type ) {
		if ( ! $term_id ) {
			return '';
		}

		$value = false;
		$key   = $this->get_meta_key( $type );
		$value = get_term_meta( $term_id, $key, true );

		if ( false === $value || '' === $value ) {
			$value = Plugin::instance()->get_mapping()->get_attribute_meta( $term_id, $type );

			// If this is a translation, copy value from the original attribute.
			// Use a hook to maximize performance and the compatibility in the future.
			if ( false === $value ) {
				/**
				 * This filter is used to translate term meta data.
				 *
				 * @param mixed $value The value of the term meta.
				 * @param int $term_id The term ID.
				 * @param string $key The key of this term meta.
				 * @param mixed $type The swatches type.
				 */
				$value = apply_filters( 'wcboost_variation_swatches_translate_term_meta', $value, $term_id, $key, $type );
			}

			// Save this meta data for faster loading in the next time.
			if ( ! empty( $value ) ) {
				update_term_meta( $term_id, $key, $value );
			}
		}

		/**
		 * Filter the swatche term meta value.
		 *
		 * @package mixed $value Swatche data
		 *
		 * @param int $term_id The term ID.
		 * @param string $key The meta_key of the term.
		 * @param string $type The type of the term.
		 */
		return apply_filters( 'wcboost_variation_swatches_term_meta', $value, $term_id, $key, $type );
	}

	/**
	 * Get meta key base type.
	 *
	 * @param string $type
	 * @return string
	 */
	public function get_meta_key( $type ) {
		switch ( $type ) {
			case 'color':
				$key = self::COLOR_META_KEY;
				break;

			case 'image':
				$key = self::IMAGE_META_KEY;
				break;

			case 'label':
				$key = self::LABEL_META_KEY;
				break;

			default:
				$key = '';
				break;
		}

		return $key;
	}

	/**
	 * Renders a swatch field
	 *
	 * @since 1.0.18
	 *
	 * @param array $args {
	 *     @type string $type Type of the swatch field (color, image, label).
	 *     @type string $name The name of the field.
	 *     @type string $label The label of the field.
	 *     @type string $value The value of the field.
	 *     @type bool $echo Whether to echo the field HTML or return it.
	 * }
	 *
	 * @return string The HTML of the field.
	 */
	public static function swatches_field( $args ) {
		$args = wp_parse_args( $args, [
			'type'  => 'color',
			'value' => '',
			'name'  => '',
			'label' => '',
			'desc'  => '',
			'echo'  => true,
		]);

		if ( empty( $args['name'] ) )  {
			return;
		}

		$html = '';

		switch ( $args['type'] ) {
			case 'image':
				$placeholder = wc_placeholder_img_src( 'thumbnail' );
				$image_src   = $args['value'] ? wp_get_attachment_image_url( $args['value'] ) : false;
				$image_src   = $image_src ? $image_src : $placeholder;

				$html = '<div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-image ' . ( empty( $args['value'] ) ? 'is-empty' : '' ) . '">';
				$html .= ! empty( $args['label'] ) ? '<span class="label">' . esc_html( $args['label'] ) . '</span>' : '';
				$html .= '<div class="wcboost-variation-swatches__field-image-controls">';
				$html .= sprintf( '<img src="%s" data-placeholder="%s" width="60" height="60">', esc_url( $image_src ), esc_url( $placeholder ) );
				$html .= sprintf(
					'<a href="javascript:void(0)" class="button-link button-add-image" aria-label="%s" data-choose="%s">
						<span class="dashicons dashicons-plus-alt2"></span>
						<span class="screen-reader-text">%s</span>
					</a>',
					esc_attr__( 'Swatches Image', 'wcboost-variation-swatches' ),
					esc_attr__( 'Use image', 'wcboost-variation-swatches' ),
					esc_html__( 'Upload', 'wcboost-variation-swatches' )
				);
				$html .= sprintf(
					'<a href="javascript:void(0)" class="button-link button-remove-image %s">
						<span class="dashicons dashicons-plus-alt2"></span>
						<span class="screen-reader-text">%s</span>
					</a>',
					empty( $args['value'] ) ? 'hidden' : '',
					esc_html__( 'Remove', 'wcboost-variation-swatches' )
				);
				$html .= '</div>';
				$html .= ! empty( $args['desc'] ) ? '<p class="description">' . esc_html( $args['desc'] ) . '</p>' : '';
				$html .= sprintf( '<input type="hidden" name="%s" value="%s">', esc_attr( $args['name'] ), esc_attr( $args['value'] ) );
				$html .= '</div>';
				break;

			case 'color':
				if ( is_array( $args['value'] ) && isset( $args['value']['colors'] ) ) {
					$color = $args['value']['colors'][0];
				} else {
					$color = is_array( $args['value'] ) ? current( $args['value'] ) : $args['value'];
				}

				$html = '<div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-color">';
				$html .= ! empty( $args['label'] ) ? '<span class="label">' . esc_html( $args['label'] ) . '</span>' : '';
				$html .= sprintf( '<input type="text" name="%s" value="%s">', esc_attr( $args['name'] ), esc_attr( $color ) );
				$html .= ! empty( $args['desc'] ) ? '<p class="description">' . esc_html( $args['desc'] ) . '</p>' : '';
				$html .= '</div>';
				break;

			case 'label':
				$html = '<div class="wcboost-variation-swatches-field wcboost-variation-swatches__field-label">';
				$html .= ! empty( $args['label'] ) ? '<span class="label">' . esc_html( $args['label'] ) . '</span>' : '';
				$html .= sprintf( '<input type="text" name="%s" value="%s" size="5">', esc_attr( $args['name'] ), esc_attr( $args['value'] ) );
				$html .= ! empty( $args['desc'] ) ? '<p class="description">' . esc_html( $args['desc'] ) . '</p>' : '';
				$html .= '</div>';
				break;
		}

		$html = apply_filters( 'wcboost_variation_swatches_field_html', $html, $args );

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}
}

Term_Meta::instance();
