<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-dashboard">

	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Dashboard', 'slicewp' ); ?></h1>
	<hr class="wp-header-end" />

	<?php

		/**
		 * Hook to output extra elements.
		 *
		 */
		do_action( 'slicewp_view_dashboard_top' );

	?>

	<div id="slicewp-dashboard-widgets-wrap">

		<div id="dashboard-widgets-wrap">

			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">

					<!-- Notifications -->
					<?php if ( slicewp_admin_dashboard_notifications()->notifications_count() != 0 ): ?>

						<div id="slicewp-card-notifications" class="slicewp-card">

							<div class="slicewp-card-header">
								<span class="slicewp-card-title"><?php echo __( 'Notifications', 'slicewp' ); ?></span>
								<span class="slicewp-notifications-count"><?php echo slicewp_admin_dashboard_notifications()->notifications_count(); ?></span>
							</div>

							<div class="slicewp-card-inner">

								<?php foreach ( slicewp_admin_dashboard_notifications()->get_notifications() as $notification ): ?>

									<div>
										<?php echo $notification['message']; ?>
									</div>

								<?php endforeach; ?>

							</div>

						</div>

					<?php endif; ?>
					<!-- / Notifications -->

					<?php do_meta_boxes( 'slicewp_page_slicewp-dashboard', 'primary', null ); ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( 'slicewp_page_slicewp-dashboard', 'secondary', null ); ?>
				</div>

				<div id="postbox-container-3" class="postbox-container">
					<?php do_meta_boxes( 'slicewp_page_slicewp-dashboard', 'tertiary', null ); ?>
				</div>

			</div>

		</div>

	</div>

	<?php

		/**
		 * Hook to output extra elements.
		 *
		 */
		do_action( 'slicewp_view_dashboard_bottom' );

	?>

	<?php
	
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce' , false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce' , false );

	?>

</div>