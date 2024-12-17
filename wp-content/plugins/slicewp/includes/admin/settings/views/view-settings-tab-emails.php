<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<!-- General Settings -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'General Settings', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- From Email -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-from-email">
					<?php echo __( 'From Email', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The email address from which the emails will be sent.' , 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-from-email" name="settings[from_email]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['from_email'] ) ? $_POST['settings']['from_email'] : ( slicewp_get_setting( 'from_email' ) ) ); ?>">

		</div><!-- / From Email -->

		<!-- From Name -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-from-name">
					<?php echo __( 'From Name', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The name of the email sender.' , 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-from-name" name="settings[from_name]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['from_name'] ) ? $_POST['settings']['from_name'] : ( slicewp_get_setting( 'from_name' ) ) ); ?>">

		</div><!-- / From Name -->

		<!-- Email Template -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-email-template">
					<?php echo __( 'Email Template', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The template will be used to format the email.' , 'slicewp' ) ); ?>
				</label>
			</div>

			<select id="slicewp-email-template" name="settings[email_template]" class="slicewp-select2">
				<option value=""><?php echo __('Plain Text', 'slicewp'); ?></option>
			<?php

				$templates = slicewp_get_email_templates();
				foreach( $templates as $key => $template )
					echo '<option value="' . esc_attr( $key ) . '"' . ( slicewp_get_setting( 'email_template' ) == $key ? 'selected="selected"' : '' ) . '>' . $template['name'] . '</option>';

			?>
			</select>

		</div><!-- / Email Template -->

		<!-- Email Logo -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-email-logo" style="display: none;">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-email-logo"><?php echo __( 'Logo', 'slicewp' ); ?></label>
				<?php echo slicewp_output_tooltip( __( "Select an image logo if you'd like to place it in the header of your email notifications.", 'slicewp' ) ); ?>
			</div>
			
			<input id="slicewp-email-logo" name="settings[email_logo]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['email_logo'] ) ? $_POST['settings']['email_logo'] : slicewp_get_setting( 'email_logo' ) ); ?>" />
			<input class="slicewp-button-secondary slicewp-image-select" type="button" value="<?php echo (__( 'Browse', 'slicewp' ) ); ?>" />

		</div>
		<!-- / Email Logo -->

		<!-- Admin Emails -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-admin-emails">
					<?php echo __( 'Admin Emails', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The Admin Email Notifications will be sent to these email addresses.' , 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-admin-emails" name="settings[admin_emails]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['admin_emails'] ) ? $_POST['settings']['admin_emails'] : ( slicewp_get_setting( 'admin_emails' ) ) ); ?>">

		</div><!-- / Admin Emails -->
		
	</div>

</div><!-- / General Settings -->

<!-- Email Notifications -->
<div class="slicewp-card" id="slicewp-email-notifications-settings">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Email Notifications', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<?php 
			$email_notifications 		  = slicewp_get_available_email_notifications();
			$email_notifications_settings = slicewp_get_setting( 'email_notifications', array() );
		?>

		<div class="slicewp-email-notifications-header">
			<div class="slicewp-email-notifications-header-name"><?php echo __( 'Email', 'slicewp' ); ?></div>
			<div class="slicewp-email-notifications-header-recipients"><?php echo __( 'Recipient', 'slicewp' ); ?></div>
			<div class="slicewp-email-notifications-header-actions"></div>
		</div>

		<?php foreach ( $email_notifications as $email_notification_slug => $email_notification ): ?>

			<?php

				$email_notification_enabled = ! empty( $email_notifications_settings[$email_notification_slug]['enabled'] ) ? $email_notifications_settings[$email_notification_slug]['enabled'] : '';
				$email_notification_subject = ! empty( $email_notifications_settings[$email_notification_slug]['subject'] ) ? $email_notifications_settings[$email_notification_slug]['subject'] : '';
				$email_notification_content = ! empty( $email_notifications_settings[$email_notification_slug]['content'] ) ? $email_notifications_settings[$email_notification_slug]['content'] : '';
				$email_notification_sending = ! empty( $email_notifications[$email_notification_slug]['sending'] ) ? $email_notifications[$email_notification_slug]['sending'] : '';
			
			?>

			<div class="slicewp-email-notification-settings-wrapper slicewp-expandable-item">

				<div class="slicewp-email-notification-settings-header">

					<!-- Notification Name -->
					<div class="slicewp-email-notification-settings-name">

						<?php if ( $email_notification_sending != 'manual' ): ?>

							<div class="slicewp-switch">
								<input id="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-enabled" class="slicewp-toggle slicewp-toggle-round" name="settings[email_notifications][<?php echo esc_attr( $email_notification_slug ); ?>][enabled]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['enabled'] ) ? '1' : ( empty( $_POST ) ? $email_notification_enabled : '' ), '1' ); ?> />
								<label for="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-enabled"></label>
							</div>

						<?php else: ?>

							<span class="slicewp-email-notification-switch-manual">
								<span class="slicewp-tooltip-wrapper">
									<?php echo slicewp_get_svg( 'outline-arrow-circle-right' ); ?>

									<span class="slicewp-tooltip-message">
										<?php echo ( $email_notification_slug == 'affiliate_account_approved' ? __( "You can enable or disable this email notification when manually approving the affiliate's application for an account.", 'slicewp' ) : '' ); ?>
										<?php echo ( $email_notification_slug == 'affiliate_account_rejected' ? __( "You can enable or disable this email notification when manually rejecting the affiliate's application for an account.", 'slicewp' ) : '' ); ?>

										<span class="slicewp-tooltip-arrow"></span>
									</span>

								</span>
							</span>

						<?php endif; ?>

						<span><label for="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-enabled"><?php echo $email_notification['name']; ?></label></span>
						<?php echo slicewp_output_tooltip( $email_notification['description'] ); ?>

					</div>
					<!-- / Notification Name -->

					<!-- Notification Recipient -->
					<div class="slicewp-email-notification-settings-recipient">

						<span><?php echo ( $email_notification['recipient'] == 'affiliate' ? __( 'Affiliate', 'slicewp' ) : __( 'Administrators', 'slicewp' ) ); ?></span>

					</div>
					<!-- / Notification Recipient -->

					<!-- / Notification Actions -->
					<div class="slicewp-email-notification-settings-actions">
						<a href="#" class="slicewp-expand-item"><?php echo __( 'Customize', 'slicewp' ); ?><?php echo slicewp_get_svg( 'outline-chevron-down' ); ?></a></a>
					</div>
					<!-- / Notification Actions -->

				</div>

				<div class="slicewp-email-notification-setting-panel">

					<!-- Email Subject -->
					<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-<?php echo str_replace( '_','-', $email_notification_slug); ?>-subject">
								<?php echo __( 'Email Subject', 'slicewp' ); ?>
							</label>
						</div>

						<input id="slicewp-<?php echo str_replace( '_','-', $email_notification_slug ); ?>-subject" name="settings[email_notifications][<?php echo $email_notification_slug; ?>][subject]" type="text" value="<?php echo ( ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['subject'] ) ? esc_attr( $_POST['settings']['email_notifications'][$email_notification_slug]['subject'] ) : ( $email_notification_subject ) ); ?>">

					</div><!-- / Email Subject -->

					<!-- Email Content -->
					<div class="slicewp-field-wrapper slicewp-email-label-wrapper">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp_<?php echo $email_notification_slug ?>_content">
								<?php echo __( 'Email Content', 'slicewp' ); ?>
							</label>
						</div>
						
						<?php 

							$content   = ! empty( $_POST['settings']['email_notifications'][$email_notification_slug]['content'] ) ? esc_attr( $_POST['settings']['email_notifications'][$email_notification_slug]['content'] ) : $email_notification_content;
							$editor_id = 'slicewp_' . $email_notification_slug . '_content';
							$settings  = array(
								'textarea_name' => 'settings[email_notifications][' . $email_notification_slug . '][content]',
								'editor_height' => 250
							);

							wp_editor( $content, $editor_id, $settings );
						
							// Add explanation about the tags the user can use in the emails.
							$merge_tags = new SliceWP_Merge_Tags();
							
							$categories = $merge_tags->get_tags_categories();
							$tags 		= $merge_tags->get_tags();

							$notification_tags = ( ! empty( $email_notification['merge_tags'] ) ? $email_notification['merge_tags'] : array() );

							if ( ! empty( $notification_tags ) ) {

								$tags = array_intersect_key( $tags, array_flip( $notification_tags ) );

							}

							$tags_explanation = '<div>';
								$tags_explanation .= '<ul>';
									$tags_explanation .= '<p>' . __( 'You can use the following tags in the email subject and email content to personalise your emails:', 'slicewp' ) . '</p>';
									
									foreach ( array_values( array_unique( array_column( $tags, 'category' ) ) ) as $category_slug ) {

										$tags_explanation .= ( ! empty( $categories[$category_slug] ) ? '<li style="margin-top: 1.25rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e5e5e5;"><p style="margin: 0 !important;">' . esc_html( sprintf( __( '%s tags', 'slicewp' ), $categories[$category_slug]['name'] ) ) . '</p></li>' : '' );

										foreach ( $tags as $tag_slug => $tag ) {

											if ( empty( $tag['category'] ) || $tag['category'] != $category_slug ) {
												continue;
											}

											$tags_explanation .= '<li><input type="text" onclick="select()" class="slicewp-email-notification-merge-tag-input" readonly value="' . '{{' . esc_attr( $tag_slug ) . '}}' . '" /> - ' . $tag['description'] . '</li>';
	
										}

									}
							
								$tags_explanation .= '</ul>';
							$tags_explanation .= '</div>';

							echo $tags_explanation;

						?>

					</div><!-- / Email Content -->


					<!-- Preview Email / Send Test Email Buttons -->
					<div class="slicewp-field-wrapper slicewp-email-label-wrapper slicewp-last">
					
						<a class="slicewp-button-secondary" href="<?php echo( wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'preview_email' , 'email_notification' => $email_notification_slug ) , site_url() ), 'slicewp_preview_email', 'slicewp_token' ) ); ?>" target="_blank"><?php echo __( 'Preview Email', 'slicewp' ); ?></a>
						<a class="slicewp-button-secondary" href="<?php echo( wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-settings', 'tab' => 'emails', 'slicewp_action' => 'send_test_email' , 'email_notification' => $email_notification_slug ) , admin_url( 'admin.php' ) ), 'slicewp_admin_send_test_email', 'slicewp_token' ) ); ?>"><?php echo __( 'Send Test Email', 'slicewp' ); ?></a>
					
					</div><!-- / Preview Email / Send Test Email Buttons -->

				</div>

			</div>

		<?php endforeach; ?>

	</div>

</div><!-- / General Settings -->

<!-- Save Settings Button -->
<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />
