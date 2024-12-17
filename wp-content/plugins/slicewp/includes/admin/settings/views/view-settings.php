<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$tabs = array(
	'general' => array(
		'label' => __( 'General', 'slicewp' ),
		'icon'  => 'dashicons-admin-generic'
	),
	'integrations' => array(
		'label' => __( 'Integrations', 'slicewp' ),
		'icon'  => 'dashicons-admin-plugins'
	),
	'emails' => array(
		'label' => __( 'Email Notifications', 'slicewp' ),
		'icon'  => 'dashicons-email-alt'
	),
	'tools' => array(
		'label' => __( 'Tools', 'slicewp' ),
		'icon'  => 'dashicons-admin-tools'
	)
);

/**
 * Filter the tabs for the settings edit screen.
 *
 * @param array $tabs
 *
 */
$tabs = apply_filters( 'slicewp_submenu_page_settings_tabs', $tabs );

$active_tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general' );

/**
 * Prepare the Email Notification Settings section.
 *
 * @param array $tabs
 *
 */
$email_notifications 		   = slicewp_get_available_email_notifications();
$first_email_notification_slug = array_keys( $email_notifications )[0];

$selected_email_notification = ( ! empty( $_GET['email_notification'] ) ? sanitize_text_field( $_GET['email_notification'] ) : $first_email_notification_slug );

/**
 * Prepare the needed variables.
 *
 */
$user 	 = wp_get_current_user();
$user_id = $user->ID;
$affiliate 	  = slicewp_get_affiliate_by_user_id( $user_id );
$affiliate_id = absint( empty( $affiliate ) ? $user_id : $affiliate->get( 'id' ) );

?>

<div class="wrap slicewp-wrap slicewp-wrap-settings">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Settings', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<!-- Tab Navigation -->
		<div class="slicewp-card">

			<!-- Navigation Tab Links -->
			<ul class="slicewp-nav-tab-wrapper">
				<?php 
					foreach ( $tabs as $tab_slug => $tab ) {
						echo '<li class="slicewp-nav-tab ' . ( $tab_slug == $active_tab ? 'slicewp-active' : '' ) . '" data-tab="' . esc_attr( $tab_slug ) . '"><a href="#"><span class="dashicons ' . esc_attr( $tab['icon'] ) . '"></span>' . esc_html( $tab['label'] ) . '</a></li>';
					}
				?>
			</ul>

			<!-- Hidden active tab -->
			<input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />

		</div>


		<?php foreach ( $tabs as $tab_slug => $tab ): ?>

			<div class="slicewp-tab <?php echo ( $active_tab == $tab_slug ? 'slicewp-active' : '' ); ?>" data-tab="<?php echo esc_attr( $tab_slug ); ?>">

				<?php

					if ( file_exists( plugin_dir_path( __FILE__ ) . 'view-settings-tab-' . $tab_slug . '.php' ) ) {

						include_once plugin_dir_path( __FILE__ ) . 'view-settings-tab-' . $tab_slug . '.php';
					
					} else {

						/**
						 * Hook to add additional settings tab content.
						 *
						 */
						do_action( 'slicewp_view_settings_tab_' . $tab_slug );

					}
				
				?>

			</div>

		<?php endforeach; ?>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="save_settings" />
		<?php wp_nonce_field( 'slicewp_save_settings', 'slicewp_token', false ); ?>

	</form>

</div>