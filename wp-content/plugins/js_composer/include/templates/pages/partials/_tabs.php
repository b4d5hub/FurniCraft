<?php
/**
 * Individual settings tab template.
 *
 * @var string $slug
 * @var string $active_tab
 * @var array $tabs
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<h2 class="nav-tab-wrapper">
	<?php foreach ( $tabs as $tab_slug => $title ) : ?>
		<?php $url = 'admin.php?page=' . rawurlencode( $slug ) . '&tab=' . rawurlencode( $tab_slug ); ?>
		<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ); ?>"
				class="nav-tab<?php echo $active_tab === $tab_slug ? esc_attr( ' nav-tab-active' ) : ''; ?>">
			<?php echo esc_html( $title ); ?>
		</a>
	<?php endforeach ?>
</h2>
