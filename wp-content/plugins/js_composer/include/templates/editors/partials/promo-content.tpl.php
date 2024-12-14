<?php
/**
 * Promo content template.
 *
 * @var bool $is_about_page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<img class="vc-featured-img" src="<?php echo esc_url( vc_asset_url( 'vc/wpb-8-0-about.png' ) ); ?>"/>

<div class="vc-feature-text">
	<h3><?php esc_html_e( 'Introducing New Features in 8.0 Release', 'js_composer' ); ?></h3>

	<p><?php esc_html_e( 'This update brings powerful features including the ability to upload custom fonts, add row titles in the backend editor, a dynamic copyright element, and plenty of other improvements.', 'js_composer' ); ?></p>
	<ul>
		<li><?php esc_html_e( 'New element icons and Add Element window improvements', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Upload custom Adobe Fonts & sync Google Fonts', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Drag and drop images directly into the editor', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'New Copyright element with dynamic year', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Add row titles in the backend editor', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Responsiveness improvements', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Clear button in the Color Picker', 'js_composer' ); ?></li>
	</ul>
	<?php
	$tabs = vc_settings()->getTabs();
	$is_license_tab_access = isset( $tabs['vc-updater'] ) && vc_user_access()->part( 'settings' )->can( 'vc-updater-tab' )->get();
	if ( $is_about_page && ! vc_license()->isActivated() && $is_license_tab_access ) :
		?>
		<div class="vc-feature-activation-section">
			<?php $url = 'admin.php?page=vc-updater'; ?>
			<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ); ?>" class="vc-feature-btn" id="vc_settings-updater-button" data-vc-action="activation"><?php esc_html_e( 'Activate License', 'js_composer' ); ?></a>
			<p class="vc-feature-info-text">
				<?php esc_html_e( 'Direct plugin activation only.', 'js_composer' ); ?>
				<a href="https://wpbakery.com/wpbakery-page-builder-license/?utm_source=wpdashboard&utm_medium=wpb-settings-about-whats-new&utm_content=text" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Don\'t have a license?', 'js_composer' ); ?></a>
			</p>
		</div>
	<?php endif; ?>
</div>
