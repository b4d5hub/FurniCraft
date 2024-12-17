<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-ons">

	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Add-ons', 'slicewp' ); ?></h1>
	<hr class="wp-header-end" />

	<?php if ( ! slicewp_is_website_registered() && ! in_array( 'slicewp-pro/index.php', get_option( 'active_plugins', array() ) ) ): ?>

		<div class="slicewp-card slicewp-card-price-notice">
			<div class="slicewp-card-inner">
				<span><?php echo __( 'Take your affiliate program to the next level!', 'slicewp' ); ?></span>
				<p><?php echo __( 'Extend your affiliate program with the PRO growth tools your affiliates need to stand out in a crowded market.', 'slicewp' ); ?></p>
				<a href="https://slicewp.com/pricing/?utm_source=plugin-free&amp;utm_medium=plugin-add-ons-page&amp;utm_campaign=SliceWPFree" target="_blank" class="slicewp-button-secondary"><?php echo __( 'Get started with PRO', 'slicewp' ); ?></a>
			</div>
		</div>

	<?php endif; ?>

	<?php wp_nonce_field( 'slicewp_activate_deactivate_add_on', 'slicewp_token', false ); ?>

	<div class="slicewp-grid slicewp-grid-columns-3" style="margin-top: 1.5rem;">

		<?php foreach ( slicewp()->add_ons as $add_on ): ?>

			<div class="slicewp-card slicewp-card-add-on">

				<div class="slicewp-card-inner">

					<div class="slicewp-flex">

						<div>
							<?php if ( ! empty( $add_on->get( 'icon_url' ) ) ): ?>
								<img src="<?php echo esc_attr( $add_on->get( 'icon_url' ) ); ?>" />
							<?php else: ?>
								<?php
									/**
									 * @todo Add placeholder image. 
									 *
									 */	
								?>
							<?php endif; ?>
						</div>

						<div>
							<?php if ( ! empty( $add_on->get( 'documentation_url' ) ) ): ?>
								<h4>
									<a href="<?php echo esc_url( $add_on->get( 'documentation_url' ) ); ?>" title="<?php echo esc_attr( __( 'Click to learn more', 'slicewp' ) ); ?>" target="_blank">
										<span><?php echo esc_html( $add_on->get( 'name' ) ); ?></span>
										<?php echo slicewp_get_svg( 'outline-arrow-top-right-on-square' ); ?>
									</a>
								</h4>
							<?php else: ?>
								<h4><span><?php echo esc_html( $add_on->get( 'name' ) ); ?></span></h4>
							<?php endif; ?>
							<p><?php echo esc_html( $add_on->get( 'description' ) ); ?></p>
						</div>

					</div>

				</div>

				<div class="slicewp-card-footer">

					<div>
						<div class="slicewp-switch slicewp-is-ajax">

							<span class="slicewp-loader"></span>

							<input id="slicewp-enable-add-on-<?php echo esc_attr( $add_on->get( 'slug' ) ); ?>" class="slicewp-toggle slicewp-toggle-round" name="slicewp_active_add_ons[]" type="checkbox" value="<?php echo esc_attr( $add_on->get( 'slug' ) ); ?>" <?php echo ( $add_on->is_active() ? 'checked="checked"' : '' ); ?> />
							<label for="slicewp-enable-add-on-<?php echo esc_attr( $add_on->get( 'slug' ) ); ?>"></label>

						</div>
					
						<div class="slicewp-tag-wrapper">
							<span class="slicewp-tag-add-on-active" <?php echo ( $add_on->is_active() ? 'style="display: inline-block;"' : '' ); ?>><?php echo __( 'Active', 'slicewp' ); ?></span>
							<span class="slicewp-tag-add-on-inactive" <?php echo ( ! $add_on->is_active() ? 'style="display: inline-block;"' : '' ); ?>><?php echo __( 'Inactive', 'slicewp' ); ?></span>
						</div>
					</div>

					<div class="slicewp-card-add-on-actions">
						<?php if ( ! empty( $add_on->get( 'settings_url' ) ) ): ?>
							<a href="<?php echo esc_url( $add_on->get( 'settings_url' ) ); ?>" class="slicewp-button-secondary" <?php echo ( ! $add_on->is_active() ? 'style="display: none;"' : '' ); ?>>
								<?php echo slicewp_get_svg( 'outline-cog' ); ?>
								<span><?php echo __( 'Settings', 'slicewp' ); ?></span>
							</a>
						<?php endif; ?>
					</div>

				</div>

			</div>

		<?php endforeach; ?>

		<?php if ( ! in_array( 'slicewp-pro/index.php', get_option( 'active_plugins', array() ) ) ): ?>

			<?php if ( slicewp_is_website_registered() ): ?>

				<div class="slicewp-items-view-blank-state" style="grid-column: span 3; padding: 75px; margin-bottom: 1.75rem;">

					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M290.8 48.6l78.4 29.7L288 109.5 206.8 78.3l78.4-29.7c1.8-.7 3.8-.7 5.7 0zM136 92.5V204.7c-1.3 .4-2.6 .8-3.9 1.3l-96 36.4C14.4 250.6 0 271.5 0 294.7V413.9c0 22.2 13.1 42.3 33.5 51.3l96 42.2c14.4 6.3 30.7 6.3 45.1 0L288 457.5l113.5 49.9c14.4 6.3 30.7 6.3 45.1 0l96-42.2c20.3-8.9 33.5-29.1 33.5-51.3V294.7c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-1.3-.5-2.6-.9-3.9-1.3V92.5c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-12.8-4.8-26.9-4.8-39.7 0l-96 36.4C150.4 48.4 136 69.3 136 92.5zM392 210.6l-82.4 31.2V152.6L392 121v89.6zM154.8 250.9l78.4 29.7L152 311.7 70.8 280.6l78.4-29.7c1.8-.7 3.8-.7 5.7 0zm18.8 204.4V354.8L256 323.2v95.9l-82.4 36.2zM421.2 250.9c1.8-.7 3.8-.7 5.7 0l78.4 29.7L424 311.7l-81.2-31.1 78.4-29.7zM523.2 421.2l-77.6 34.1V354.8L528 323.2v90.7c0 3.2-1.9 6-4.8 7.3z"/></svg>
					<h3><?php echo __( 'Gain access to all pro add-ons by installing the SliceWP Pro plugin.', 'slicewp' ); ?></h3>
					<a href="https://slicewp.com/docs/installing-slicewp-pro/" target="_blank" class="slicewp-button-primary"><?php echo __( 'Learn how to install SliceWP Pro', 'slicewp' ); ?></a>

				</div>

			<?php endif; ?>

			<?php foreach ( $add_ons as $add_on ): ?>

				<div class="slicewp-card slicewp-card-add-on">

					<div class="slicewp-card-inner">

						<div class="slicewp-flex">

							<div>
								<?php if ( ! empty( $add_on['slug'] ) ): ?>
									<img src="<?php echo SLICEWP_PLUGIN_DIR_URL . '/assets/img/add-on-icon-' . esc_attr( $add_on['slug'] ) . '.png'; ?>" />
								<?php endif; ?>
							</div>

							<div>
								<h4><?php echo esc_html( $add_on['name'] ); ?></h4>
								<p><?php echo esc_html( $add_on['description'] ); ?></p>
							</div>

						</div>

					</div>

					<div class="slicewp-card-footer">
						<a href="<?php echo esc_url( ! slicewp_is_website_registered() ? add_query_arg( array( 'utm_source' => 'add-on-' . sanitize_title( $add_on['name'] ), 'utm_medium' => 'plugin-add-ons-page', 'utm_campaign' => 'SliceWPFree' ), $add_on['url'] ) : 'https://slicewp.com/docs/installing-slicewp-pro/' ); ?>" target="_blank" class="slicewp-button-secondary"><?php echo __( 'Get this add-on', 'slicewp' ) ?></a>
					</div>

				</div>

			<?php endforeach; ?>

		<?php endif; ?>

	</div>

</div>