<?php
/**
 * Adds more settings for WooCommerce plugin
 *
 * @package Sober
 */

/**
 * Class Sober_WC_Settings
 * Adds more settings into WooCommerce's settings
 */
class Sober_WC_Settings {
	/**
	 * Initialize
	 */
	public static function init() {
		add_filter( 'woocommerce_get_settings_checkout', array( __CLASS__, 'add_order_tracking_setting' ) );
		add_filter( 'woocommerce_get_settings_advanced', array( __CLASS__, 'add_order_tracking_setting' ), 10, 2 );
		add_filter( 'woocommerce_get_settings_products', array( __CLASS__, 'product_settings' ), 10, 2 );
	}

	/**
	 * Adds sober_order_tracking_page_id setting field to Checkout or Advanced tab.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public static function add_order_tracking_setting( $settings ) {
		$new_settings = array();

		foreach ( $settings as $index => $setting ) {
			$new_settings[ $index ] = $setting;

			if ( isset( $setting['id'] ) && 'woocommerce_terms_page_id' == $setting['id'] ) {
				$new_settings['order_tracking_page_id'] = array(
					'title'    => esc_html__( 'Order Tracking Page', 'sober' ),
					'desc'     => esc_html__( 'Page content: [woocommerce_order_tracking]', 'sober' ),
					'id'       => 'sober_order_tracking_page_id',
					'type'     => 'single_select_page',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => true,
				);
			}
		}

		return $new_settings;
	}

	/**
	 * Adds sober_order_tracking_page_id setting field to product display tab.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public static function product_settings( $settings, $section ) {
		if ( 'display' == $section || '' == $section ) {
			$new_settings = array();

			foreach ( $settings as $index => $setting ) {
				$new_settings[ $index ] = $setting;

				if ( isset( $setting['id'] ) && 'woocommerce_cart_redirect_after_add' == $setting['id'] ) {
					$new_settings['sober_enable_single_ajax_add_to_cart'] = array(
						'desc'          => esc_html__( 'Enable AJAX add to cart button on single', 'sober' ),
						'id'            => 'sober_enable_single_ajax_add_to_cart',
						'default'       => 'no',
						'type'          => 'checkbox',
						'checkboxgroup' => '',
					);
				}
			}

			$settings = $new_settings;
		}

		return $settings;
	}
}

/**
 * Class Sober_WC_Term_Settings
 * Adds more fields into term edit form
 */
class Sober_WC_Term_Settings {
	/**
	 * Initialize
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Add fields
		$taxonomies = get_object_taxonomies( 'product' );

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_edit_form_fields', array( __CLASS__, 'edit_category_fields' ), 20 );
		}

		add_action( 'edit_term', array( __CLASS__, 'save_category_fields' ), 20, 3 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $hook
	 */
	public static function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'term.php', 'edit-tags.php' ) ) ) {
			return;
		}

		wp_enqueue_script( 'sober-term-page-header-image', get_template_directory_uri() . '/js/admin/terms.js', array( 'jquery' ), '3.0', true );
		wp_localize_script( 'sober-term-page-header-image', 'soberTermData', array(
			'placeholder' => wc_placeholder_img_src(),
			'l10n'        => array(
				'title'  => esc_html__( 'Choose an image', 'sober' ),
				'button' => esc_html__( 'Use image', 'sober' ),
			),
		) );
	}

	/**
	 * Edit category page header fields.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public static function edit_category_fields( $term ) {
		$text_color   = get_term_meta( $term->term_id, 'page_header_text_color', true );
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'page_header_image_id', true ) );
		$image        = $thumbnail_id ? wp_get_attachment_thumb_url( $thumbnail_id ) : wc_placeholder_img_src();
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
					<input type="hidden" id="page-header-image-id" name="page_header_image_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="button" class="upload-header-image-button button"><?php esc_html_e( 'Upload/Add image', 'sober' ); ?></button>
					<button type="button" class="remove-header-image-button button"><?php esc_html_e( 'Remove image', 'sober' ); ?></button>
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
					<option value="dark" <?php selected( 'dark', $text_color ) ?>><?php esc_html_e( 'Dark', 'sober' ) ?></option>
					<option value="light" <?php selected( 'light', $text_color ) ?>><?php esc_html_e( 'Light', 'sober' ) ?></option>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed  $term_id Term ID being saved
	 * @param mixed  $tt_id
	 * @param string $taxonomy
	 */
	public static function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['page_header_image_id'] ) && is_object_in_taxonomy( 'product', $taxonomy ) ) {
			update_term_meta( $term_id, 'page_header_image_id', absint( $_POST['page_header_image_id'] ) );
		}

		if ( isset( $_POST['page_header_text_color'] ) && is_object_in_taxonomy( 'product', $taxonomy ) ) {
			update_term_meta( $term_id, 'page_header_text_color', $_POST['page_header_text_color'] );
		}
	}
}

/**
 * Class Sober_WC_Product_Settings
 *
 * Add for fields into a product data meta box
 */
class Sober_WC_Product_Settings {
	/**
	 * Initialize
	 */
	public static function init() {
		add_action( 'woocommerce_product_options_advanced', array( __CLASS__, 'add_advanced_options' ) );

		add_action( 'save_post', array( __CLASS__, 'save_product_data' ) );
	}

	/**
	 * Add more fields to "Advanced" product data tab
	 */
	public static function add_advanced_options() {
		woocommerce_wp_checkbox( array(
			'id'            => '_is_new',
			'label'         => esc_html__( 'New product?', 'sober' ),
			'description'   => esc_html__( 'Enable to set this product as a new product. A "New" badge will be added to this product.', 'sober' ),
		) );
	}

	/**
	 * Save product data
	 *
	 * @param int $post_id
	 */
	public static function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_is_new'] ) ) {
			delete_post_meta( $post_id, '_is_new' );
		} else {
			update_post_meta( $post_id, '_is_new', 'yes' );
		}
	}
}


/**
 * Clear new product ids cache with the sale schedule which is run daily.
 */
function sober_woocommerce_clear_cache_daily() {
	delete_transient( 'sober_woocommerce_products_new' );
}

add_action( 'woocommerce_scheduled_sales', 'sober_woocommerce_clear_cache_daily' );

/**
 * Clear new product ids cache when update/trash/delete products.
 *
 * @param int $post_id
 */
function sober_woocommerce_clear_cache( $post_id ) {
	if ( 'product' != get_post_type( $post_id ) ) {
		return;
	}

	delete_transient( 'sober_woocommerce_products_new' );
}

add_action( 'save_post', 'sober_woocommerce_clear_cache' );
add_action( 'wp_trash_post', 'sober_woocommerce_clear_cache' );
add_action( 'before_delete_post', 'sober_woocommerce_clear_cache' );
