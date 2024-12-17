<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php

	/**
	 * Hook to add extra cards if needed to the Tools tab
	 *
	 */
	do_action( 'slicewp_view_settings_tab_tools_top' );

?>

<!-- Spam protection -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Spam Protection', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper slicewp-tooltip-wide">
				<label for="slicewp-affiliate-registration-blocklist">
					<?php echo __( 'Affiliate Registration Blocklist', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( '<p>' . "Add a list of email addresses or domains (one per line), that you'd like to block from creating affiliate accounts on your website." . '</p><p>' . __( 'For example:', 'slicewp' ) . '</p><ul><li>' . sprintf( __( '%ssomething@website.com%s - If you add a full email address, this email address cannot be used to create an affiliate account on your website. The attempt will be blocked.', 'slicewp' ), '<strong>', '</strong>' ) . '</li><li>' . sprintf( __( '%swebsite.com%s - If you add a domain, all email addresses associated with this domain will be blocked from being able to create an affiliate account.', 'slicewp' ), '<strong>', '</strong>' ) . '</li></ul>', 'slicewp' ) ); ?>
				</label>
			</div>

			<textarea id="slicewp-affiliate-registration-blocklist" name="settings[affiliate_registration_blocklist]" rows="5"><?php echo esc_textarea( ! empty( $_POST['settings']['affiliate_registration_blocklist'] ) ? $_POST['settings']['affiliate_registration_blocklist'] : slicewp_get_setting( 'affiliate_registration_blocklist' ) ); ?></textarea>

		</div>
	
	</div>

</div>
<!-- / Spam protection -->

<!-- Affiliate User Role -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Users Affiliate User Role', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<p style="margin-top: 0;"><?php echo __( 'If you want to add or remove the Affiliate user role in bulk, to or from all affiliates, please click the corresponding button below.', 'slicewp' ); ?></p>

		<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'bulk_add_affiliate_user_role' ) ), 'slicewp_bulk_add_affiliate_user_role', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to add the user role Affiliate to all users that are also affiliates in SliceWP?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Bulk Add Affiliate User Role', 'slicewp' ); ?></a>
		<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'bulk_remove_affiliate_user_role' ) ), 'slicewp_bulk_remove_affiliate_user_role', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to remove the user role Affiliate from all users?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Bulk Remove Affiliate User Role', 'slicewp' ); ?></a>

	</div>

</div>
<!-- / Affiliate User Role -->

<!-- Debug Log -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Debug Log', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- Enable Logging -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline <?php echo ( slicewp_get_setting( 'enable_logging' ) != '1' ? 'slicewp-last' : '' ); ?>">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-enable-logging">
					<?php echo __( 'Enable Logging', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-enable-logging" class="slicewp-toggle slicewp-toggle-round" name="settings[enable_logging]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['enable_logging'] ) ? esc_attr( $_POST['settings']['enable_logging'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'enable_logging' ) : '' ), '1' ); ?> />
				<label for="slicewp-enable-logging"></label>

			</div>

			<label for="slicewp-enable-logging"><?php echo __( 'Enable system logging for debugging purposes.', 'slicewp' ); ?></label>

		</div><!-- / Enable Logging -->

		<!-- Debug Log Textarea -->
		<?php if( slicewp_get_setting( 'enable_logging' ) == '1' ): ?>

			<div class="slicewp-field-wrapper slicewp-last">
				<textarea disabled style="min-height: 300px;"><?php echo esc_attr( slicewp_get_log() ); ?></textarea>
			</div>

		<?php endif; ?><!-- / Debug Textarea -->

	</div>

	<!-- Card Footer -->
	<?php if( slicewp_get_setting( 'enable_logging' ) == '1' ): ?>

		<div class="slicewp-card-footer">
			<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'download_debug_log' ) ), 'slicewp_download_debug_log', 'slicewp_token' ); ?>" class="slicewp-button-primary"><?php echo __( 'Download Debug Log', 'slicewp' ); ?></a>
			<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'clear_debug_log' ) ), 'slicewp_clear_debug_log', 'slicewp_token' ); ?>" onclick="return confirm( '<?php echo __( 'Are you sure you want to clear the debug log?', 'slicewp' ); ?>')" class="slicewp-button-secondary"><?php echo __( 'Clear Debug Log', 'slicewp' ); ?></a>
		</div>

	<?php endif; ?><!-- / Card Footer -->

</div><!-- / System Status -->

<!-- System Status -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'System Status', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- Activate System Status -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline <?php echo ( slicewp_get_setting( 'enable_system_status' ) != '1' ? 'slicewp-last' : '' ); ?>">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-enable-system-status">
					<?php echo __( 'Enable System Status', 'slicewp' ); ?>
				</label>
			</div>
			
			<div class="slicewp-switch">

				<input id="slicewp-enable-system-status" class="slicewp-toggle slicewp-toggle-round" name="settings[enable_system_status]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['enable_system_status'] ) ? esc_attr( $_POST['settings']['enable_system_status'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'enable_system_status' ) : '' ), '1' ); ?> />
				<label for="slicewp-enable-system-status"></label>

			</div>

			<label for="slicewp-enable-system-status"><?php echo __( 'Click to enable the System Status Report.', 'slicewp' ); ?></label>

		</div><!-- / Activate System Status -->

		<!-- System Status Textarea -->
		<?php if( slicewp_get_setting( 'enable_system_status' ) == '1' ): ?>

			<div class="slicewp-field-wrapper slicewp-last">
				<textarea disabled style="min-height: 300px;"><?php echo esc_attr( slicewp_system_status() ); ?></textarea>
			</div>

		<?php endif; ?><!-- / System Status Textarea -->

	</div>

</div><!-- / System Status -->

<!-- Setup Wizard -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Setup Wizard', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<p style="margin-top: 0;"><?php echo __( 'If you need to run the setup wizard again, please click the button below.', 'slicewp' ); ?></p>

		<a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_action' => 'show_setup_wizard' ) , admin_url( 'admin.php' ) ), 'slicewp_show_setup_wizard', 'slicewp_token' ); ?>" class="slicewp-button-secondary"><?php echo __( 'Setup Wizard', 'slicewp' ); ?></a>

	</div>

</div><!-- / Setup Wizard -->

<!-- Plugin Usage Tracking -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Usage Tracking', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- Activate Plugin Usage Tracking -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-allow-tracking">
					<?php echo __( 'Allow Tracking', 'slicewp' ); ?>
				</label>
			</div>
			
			<div class="slicewp-switch">

				<input id="slicewp-allow-tracking" class="slicewp-toggle slicewp-toggle-round" name="settings[allow_tracking]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['allow_tracking'] ) ? esc_attr( $_POST['settings']['allow_tracking'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'allow_tracking' ) : '' ), '1' ); ?> />
				<label for="slicewp-allow-tracking"></label>

			</div>

			<label for="slicewp-allow-tracking"><?php echo __( "Allow SliceWP to anonymously track the plugin's usage. The collected data can help us improve the plugin and provide better features. Sensitive data will not be tracked.", 'slicewp' ); ?></label>

			<p style="margin-bottom: 0;"><a href="https://slicewp.com/docs/usage-tracking/" target="_blank"><?php echo __( "Learn more about what we track and what we don't.", 'slicewp' ); ?></a></p>

		</div><!-- / Activate Plugin Usage Tracking -->

	</div>

</div><!-- / Plugin Usage Tracking -->

<?php 

	/**
	 * Hook to add extra cards if needed to the Tools tab
	 *
	 */
	do_action( 'slicewp_view_settings_tab_tools_bottom' );

?>

<!-- Save Settings Button -->
<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />