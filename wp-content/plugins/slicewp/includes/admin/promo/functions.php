<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the admin area.
 *
 */
function slicewp_include_files_admin_promo() {

	// Get dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include the reports promo page.
	if ( file_exists( $dir_path . 'class-submenu-page-promo-reports.php' ) ) {
        include $dir_path . 'class-submenu-page-promo-reports.php';
    }

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_promo' );


/**
 * Register the promo reports admin submenu page.
 *
 */
function slicewp_register_submenu_page_promo_reports( $submenu_pages ) {

    if ( slicewp_is_website_registered() ) {
        return $submenu_pages;
    }

	if ( slicewp_add_ons_exist() ) {
        return $submenu_pages;
    }

	if ( class_exists( 'SliceWP_Pro' ) ) {
		return $submenu_pages;
	}

	if ( ! is_array( $submenu_pages ) ) {
        return $submenu_pages;
    }

	$submenu_pages['reports'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Promo_Reports',
		'data' 		 => array(
			'page_title' => __( 'Reports', 'slicewp' ),
			'menu_title' => __( 'Reports', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_promo_reports', 'manage_options' ),
			'menu_slug'  => 'slicewp-promo-reports'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_promo_reports', 40 );


/**
 * Adds a call-to-action at the bottom of pages that have list tables
 *
 */
function slicewp_promo_add_upgrade_card_cta() {

	if ( slicewp_is_website_registered() ) {
		return;
	}

	if ( class_exists( 'SliceWP_Pro' ) ) {
		return;
	}

	?>

	<a id="slicewp-upgrade-card-cta" href="<?php echo add_query_arg( array( 'page' => 'slicewp-add-ons' ), 'admin.php' ); ?>">

		<div class="slicewp-card">
			<div class="slicewp-card-inner">
				<p><?php echo __( 'Missing anything? Discover more powerful features in the premium version now!', 'slicewp' ); ?></p>
				<span><?php echo __( "I'm interested", 'slicewp' ); ?></span>
			</div>
		</div>

	</a>

	<?php

}
add_action( 'slicewp_view_affiliates_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_commissions_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_creatives_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_visits_bottom', 'slicewp_promo_add_upgrade_card_cta' );
add_action( 'slicewp_view_payouts_bottom', 'slicewp_promo_add_upgrade_card_cta' );


/**
 * Include the promo commission rates view for the affiliate.
 *
 */
function slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates() {

	if ( slicewp_is_website_registered() ) {
		return;
	}

	if ( slicewp_add_ons_exist() ) {
		return;
	}

	if ( class_exists( 'SliceWP_Pro' ) ) {
		return;
	}

	$affiliate_id = ( ! empty( $_GET['affiliate_id'] ) ? sanitize_text_field( $_GET['affiliate_id'] ) : 0 );

	?>

	<div class="slicewp-card slicewp-card-promo">

		<div class="slicewp-card-header">
			<span class="slicewp-card-title"><?php echo __( 'Affiliate Commission Rates', 'slicewp' ); ?></span>
			<a class="slicewp-promo-pill" href="https://slicewp.com/" target="_blank"><?php echo __( 'Pro Feature', 'slicewp' ); ?></a>
		</div>

		<div class="slicewp-card-inner">

			<!-- Enable Custom Commission Rates -->
			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

				<div class="slicewp-field-label-wrapper">
					<label><?php echo __( 'Commission Rates', 'slicewp' ); ?></label>
				</div>
				
				<div class="slicewp-switch">

					<input class="slicewp-toggle slicewp-toggle-round" disabled type="checkbox" value="1" checked />
					<label></label>

				</div>

				<label><?php echo __( 'Enable custom commission rates for this affiliate.', 'slicewp' ); ?></label>

			</div>
			<!-- / Enable Custom Commission Rates -->

			<!-- Commissions Rates -->
			<?php 
				$commission_types = slicewp_get_available_commission_types( true );
				$count = 0;
			?>

			<?php foreach( $commission_types as $type => $details ): ?>

				<?php if( $type == 'recurring' || $type == 'lifetime_sale' ) continue; ?>

				<?php
					$rate 	   = slicewp_get_affiliate_meta( $affiliate_id, 'commission_rate_' . $type, true );
					$rate_type = slicewp_get_affiliate_meta( $affiliate_id, 'commission_rate_type_' . $type, true );
				?>

				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>">
							<?php echo sprintf( __( '%s rate', 'slicewp' ), $details['label'] ); ?>
						</label>
					</div>
					
					<input id="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>" type="text" value="25" disabled />					

					<select name="commission_rate_type_<?php echo $type; ?>" class="slicewp-select2" disabled>
						<?php foreach( $details['rate_types'] as $details_rate_type ): ?>
							<option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) ); ?></option>
						<?php endforeach; ?>
					</select>

				</div>

				<?php $count++; ?>

			<?php endforeach; ?>
			<!-- / Commisions Rates -->

		</div>

	</div>

	<?php

}
add_action( 'slicewp_view_affiliates_add_affiliate_bottom', 'slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates' );
add_action( 'slicewp_view_affiliates_edit_affiliate_bottom', 'slicewp_promo_view_affiliates_add_affiliate_bottom_affiliate_commission_rates' );


/**
 * Adds a new "affiliate_fields" tab in the Settings page, just after the "tools" tab
 *
 * @param array $tabs
 *
 * @return array
 *
 */
function slicewp_promo_submenu_page_settings_tabs_affiliate_fields( $tabs ) {

	if ( slicewp_is_website_registered() ) {
		return $tabs;
	}

	if ( slicewp_add_ons_exist() ) {
		return $tabs;
	}

	if ( class_exists( 'SliceWP_Pro' ) ) {
		return $tabs;
	}

	$tabs = array_merge( 
		array_slice( $tabs, 0, array_search( 'tools', array_keys( $tabs ) ) ),
		array( 
			'affiliate_fields' => array( 
				'label' => __( 'Affiliate Fields', 'slicewp' ), 
				'icon' => 'dashicons-groups'
			)),
		array_slice( $tabs, array_search( 'tools', array_keys( $tabs ) ), count( $tabs ) )
	);

	return $tabs;

}
add_filter( 'slicewp_submenu_page_settings_tabs', 'slicewp_promo_submenu_page_settings_tabs_affiliate_fields' );


/**
 * Outputs the content for the "affiliate_fields" tab in the Settings page
 *
 */
function slicewp_promo_view_settings_tab_affiliate_fields() {

	if ( slicewp_is_website_registered() ) {
		return;
	}

	if ( slicewp_add_ons_exist() ) {
		return;
	}

	if ( class_exists( 'SliceWP_Pro' ) ) {
		return;
	}

	?>

	<div class="slicewp-card slicewp-card-promo">

		<div class="slicewp-card-header">
			<span class="slicewp-card-title"><?php echo __( 'Affiliate Fields', 'slicewp' ); ?></span>
			<a class="slicewp-promo-pill" href="https://slicewp.com/products/custom-affiliate-fields/" target="_blank"><?php echo __( 'Pro Feature', 'slicewp' ); ?></a>
		</div>

		<div class="slicewp-card-inner">

			<h4 style="margin-top: 0;"><?php echo __( 'Customize your affiliate registration and affiliate account forms:', 'slicewp' ); ?></h4>
			<ul style="margin-bottom: 0;">
				<li><span class="dashicons dashicons-yes-alt" style="color: #16a085; margin-right: 5px;"></span><?php echo __( 'Add custom text fields, checkboxes, radio buttons, selects and more.', 'slicewp' ); ?></li>
				<li><span class="dashicons dashicons-yes-alt" style="color: #16a085; margin-right: 5px;"></span><?php echo __( 'Display these fields on the affiliate register form, affiliate account form or just in the admin area.', 'slicewp' ); ?></li>
				<li><span class="dashicons dashicons-yes-alt" style="color: #16a085; margin-right: 5px;"></span><?php echo __( 'Customize each field! Change the label, placeholder, description and much more.', 'slicewp' ); ?></li>
				<li><span class="dashicons dashicons-yes-alt" style="color: #16a085; margin-right: 5px;"></span><?php echo __( 'Lastly, re-order the fields to create the perfect affiliate onboarding form.', 'slicewp' ) ?></li>				
			</ul>
		</div>

		<div class="slicewp-card-footer">
			<a class="slicewp-button-primary" href="https://slicewp.com/products/custom-affiliate-fields/" target="_blank"><?php echo __( 'Learn how to customize your affiliate registration form', 'slicewp' ); ?></a>
		</div>

	</div>

	<?php

}
add_action( 'slicewp_view_settings_tab_affiliate_fields', 'slicewp_promo_view_settings_tab_affiliate_fields' );


/**
 * Registers a notice to review SliceWP.
 *
 */
function slicewp_admin_notice_review_request() {

	if ( empty( $_GET['page'] ) ) {
		return;
	}

	if ( false === strpos( $_GET['page'], 'slicewp' ) ) {
		return;
	}

	if ( $_GET['page'] == 'slicewp-setup' ) {
		return;
	}

	if ( ( (int)slicewp_get_option( 'first_activation' ) + 21 * DAY_IN_SECONDS ) > time() ) {
		return;
	}

	// Check if the user dismissed the notice, show it only once every two weeks
	$review_request = slicewp_get_option( 'review_request', array() );

	if ( isset( $review_request['dismissed_temp'] ) && empty( $review_request['dismissed_temp'] ) ) {
        return;
    }

	if ( isset( $review_request['dismissed_temp'] ) && isset( $review_request['dismissed_time'] ) && ( $review_request['dismissed_time'] + 7 * DAY_IN_SECONDS ) > time() ) {
        return;
    }

	?>

		<div class="notice notice-info">
			<p><?php esc_html_e( "Hey, I noticed you've been using SliceWP for a few weeks now - that’s awesome! Could you please do me a BIG favor and give the plugin a 5-star rating on WordPress to help us spread the word? It would mean the world to us!", 'slicewp' ); ?></p>
			<p><strong><?php esc_html_e( '~ Iova Mihai, SliceWP co-founder', 'slicewp' ); ?></strong></p>
			<p>
				<a href="https://wordpress.org/support/plugin/slicewp/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 3px;"><?php esc_html_e( 'Ok, you deserve it', 'slicewp' ); ?></a><br />
				<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'dismiss_notice_review_request', 'temp' => 1 ) ), 'slicewp_dismiss_notice_review_request', 'slicewp_token' ); ?>" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 5px;"><?php esc_html_e( 'Nope, maybe later', 'slicewp' ); ?></a><br />
				<a href="<?php echo wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'dismiss_notice_review_request', 'temp' => 0 ) ), 'slicewp_dismiss_notice_review_request', 'slicewp_token' ); ?>" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 5px;"><?php esc_html_e( 'I already did', 'slicewp' ); ?></a>
			</p>
		</div>

	<?php

}
add_action( 'admin_notices', 'slicewp_admin_notice_review_request' );


/**
 * Handles the dismissal of the review request admin notice
 *
 */
function slicewp_admin_action_dismiss_notice_review_request() {

	// Verify for nonce
	if ( empty( $_GET['slicewp_token'] ) || ! wp_verify_nonce( $_GET['slicewp_token'], 'slicewp_dismiss_notice_review_request' ) ) {
        return;
    }

	if ( ! isset( $_GET['temp'] ) ) {
        return;
    }

	$review_request = array(
		'dismissed_temp' => absint( $_GET['temp'] ),
		'dismissed_time' => time()
	);

	update_option( 'slicewp_review_request', $review_request );

	// Redirect to the current page
	wp_redirect( remove_query_arg( array( 'slicewp_action', 'slicewp_token', 'temp' ) ) );
	exit;

}
add_action( 'slicewp_admin_action_dismiss_notice_review_request', 'slicewp_admin_action_dismiss_notice_review_request' );