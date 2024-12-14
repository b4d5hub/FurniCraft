<?php
namespace WCBoost\Wishlist\Widget;

defined( 'ABSPATH' ) || exit;

use WCBoost\Wishlist\Helper;

/**
 * Widget compare products class
 */
class Wishlist extends \WC_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce wcboost-wishlist-widget';
		$this->widget_description = __( 'Display the customer wishlist.', 'wcboost-wishlist' );
		$this->widget_id          = 'wcboost-wishlist-widget';
		$this->widget_name        = __( 'Wishlist', 'wcboost-wishlist' );
		$this->settings           = array(
			'title'         => array(
				'type'  => 'text',
				'std'   => __( 'Wishlist', 'wcboost-wishlist' ),
				'label' => __( 'Title', 'wcboost-wishlist' ),
			),
			'hide_if_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide if wishlist is empty', 'wcboost-wishlist' ),
			),
		);

		if ( is_customize_preview() ) {
			wp_enqueue_script( 'wcboost-wishlist-fragments' );
		}

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'wcboost_wishlist_widget_is_hidden', Helper::is_wishlist() ) ) {
			return;
		}

		$hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = $this->settings['title']['std'];
		}

		$this->widget_start( $args, $instance );

		if ( $hide_if_empty ) {
			echo '<div class="wcboost-wishlist-widget__hide-if-empty">';
		}

		Helper::widget_content();

		if ( $hide_if_empty ) {
			echo '</div>';
		}

		$this->widget_end( $args );
	}
}
