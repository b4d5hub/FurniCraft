<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Dashboard card: Need Help.
 *
 */
class SliceWP_Admin_Dashboard_Card_Help extends SliceWP_Admin_Dashboard_Card {

	/**
	 * Initialize the card.
	 *
	 */
	protected function init() {

		$this->slug    = 'need_help';
		$this->name    = __( 'Need help?', 'slicewp' );
		$this->context = 'secondary';

	}


	/**
	 * Output the card's content.
	 *
	 */
	public function output() {

		?>

			<img src="<?php echo SLICEWP_PLUGIN_DIR_URL; ?>/assets/img/need-help.png" />
			<p><?php echo __( 'Need help setting up SliceWP or have any questions about the plugin?', 'slicewp' ); ?></p>
			<a href="https://wordpress.org/support/plugin/slicewp/" target="_blank" class="slicewp-button-primary"><?php echo __( 'Open a support ticket', 'slicewp' ); ?></a>

		<?php

	}

}