<?php
/**
 * The template part for displaying the popup in modal layout
 *
 * @package Sober
 */
?>

<div id="popup" class="sober-modal sober-popup popup-layout-modal">
	<div class="sober-modal-backdrop popup-backdrop"></div>
	<div class="popup-modal">
		<a href="#" class="close-modal">
			<?php sober_svg_icon( 'icon=close-delete' ) ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
		</a>

		<div class="popup-container">
			<div class="popup-content popup-image">
				<?php
				if ( $popup_banner = sober_get_option( 'popup_image' ) ) {
					$image_id = attachment_url_to_postid( $popup_banner );

					if ( $image_id ) {
						echo wp_get_attachment_image( $image_id, 'full' );
					} else {
						$image_info = pathinfo( $popup_banner );
						printf( '<img src="%s" alt="%s">', esc_url( $popup_banner ), esc_attr( basename( $popup_banner, '.' . $image_info['extension'] ) ) );
					}
				}
				?>
			</div>

			<div class="popup-content">
				<div class="popup-content-wrap">
					<?php echo do_shortcode( wp_kses_post( sober_get_option( 'popup_content' ) ) ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
