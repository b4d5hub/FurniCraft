<?php
/**
 * Template part for displaying topbar
 */
?>
<div id="topbar" class="topbar">
	<div class="sober-container">

		<?php if ( '2-columns' == sober_get_option( 'topbar_layout' ) ) : ?>

			<div class="row">
				<div class="topbar-left topbar-content text-left col-md-6">
					<?php
					switch ( sober_get_option( 'topbar_left' ) ) {
						case 'switchers':
							$language_flags = intval( sober_get_option( 'topbar_language_flag' ) );
							$currency_flags = intval( sober_get_option( 'topbar_currency_flag' ) );

							sober_currency_switcher( 'show_flag=' . $currency_flags );
							sober_language_switcher( 'show_flag=' . $language_flags );
							break;

						default:
							echo do_shortcode( sober_get_option( 'topbar_content' ) );
							break;
					}
					?>
				</div>

				<div class="topbar-menu text-right col-md-6">
					<?php
					if ( has_nav_menu( 'topbar' ) ) {
						wp_nav_menu( array( 'theme_location' => 'topbar', 'menu_id' => 'topbar-menu', 'menu_class' => 'topbar-menu nav-menu' ) );
					}
					?>
				</div>
			</div>

		<?php else : ?>
			<?php if ( sober_get_option( 'topbar_closeable' ) ) : ?>
				<button type="button" class="close" aria-label="<?php esc_attr_e( 'Close', 'sober' ) ?>">
					<?php sober_svg_icon( 'icon=close-delete-small&size=13' ) ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
				</button>
			<?php endif; ?>

			<div class="topbar-content text-center">
				<?php echo do_shortcode( sober_get_option( 'topbar_content' ) ); ?>
			</div>

		<?php endif; ?>

	</div>
</div>
