<?php
namespace WCBoost\ProductsCompare\Widget;

defined( 'ABSPATH' ) || exit;

use WCBoost\ProductsCompare\Helper;

/**
 * Widget compare products class
 */
class Products_Compare_Widget extends \WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	public function __construct() {
		$this->defaults = array(
			'title'             => esc_html__( 'Products Compare', 'wcboost-products-compare' ),
			'hide_if_empty'     => false,
			'compare_behaviour' => 'page',
		);

		parent::__construct(
			'wcboost-products-compare-widget',
			esc_html__( 'Products Compare', 'wcboost-products-compare' ),
			array(
				'classname'   => 'wcboost-products-compare-widget',
				'description' => esc_html__( 'Displays the compare list', 'wcboost-products-compare' ),
			)
		);
	}

	/**
	 * Outputs the content for the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Settings for the widget.
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'wcboost_products_compare_widget_is_hidden', Helper::is_compare_page() ) ) {
			return;
		}

		$instance = wp_parse_args( $instance, $this->defaults );

		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		$wrapper_class = 'wcboost-products-compare-widget__content-wrapper';
		if ( $instance['hide_if_empty'] ) {
			$wrapper_class .= ' wcboost-products-compare-widget__hidden-content';
		}

		echo '<div class="' . esc_attr( $wrapper_class ) . '" data-compare="' . esc_attr( $instance['compare_behaviour'] ) . '">';

		Helper::widget_content();

		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wcboost-products-compare' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'hide_if_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_if_empty' ); ?>" type="checkbox" value="1" <?php checked( 1, $instance['hide_if_empty'] ) ?> />
			<label for="<?php echo $this->get_field_id( 'hide_if_empty' ); ?>"><?php esc_html_e( 'Hide if the compare list empty', 'wcboost-products-compare' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'compare_behaviour' ); ?>"><?php esc_html_e( 'Compare button behaviour', 'wcboost-products-compare' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'compare_behaviour' ); ?>" name="<?php echo $this->get_field_name( 'compare_behaviour' ); ?>">
				<option value="page" <?php selected( 'page', $instance['compare_behaviour'] ) ?>><?php esc_attr_e( 'Open the compare page', 'wcboost-products-compare' ); ?></option>
				<option value="popup" <?php selected( 'popup', $instance['compare_behaviour'] ) ?>><?php esc_attr_e( 'Open the compare popup', 'wcboost-products-compare' ); ?></option>
			</select>
		</p>

		<?php
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance New widget settings
	 * @param array $old_instance Old widget settings
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['title']         = strip_tags( $new_instance['title'] );
		$new_instance['hide_if_empty'] = isset( $new_instance['hide_if_empty'] );

		if( ! in_array( $new_instance['compare_behaviour'], [ 'page', 'popup' ] ) ) {
			$new_instance['compare_behaviour'] = isset( $old_instance['compare_behaviour'] ) ? $old_instance['compare_behaviour'] : $this->defaults['compare_behaviour'];
		}

		return $new_instance;
	}
}
