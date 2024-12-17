<?php
/**
 * Affiliate account.
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get affiliate account tabs.
 *
 */
$tabs = slicewp_get_affiliate_account_tabs();

/**
 * Set default active tab.
 * 
 */
$default_active_tab = apply_filters( 'slicewp_affiliate_account_default_active_tab', array_keys( $tabs )[0] );

/**
 * Set the active tab
 *
 */
$active_tab = ( ! empty( $_GET['affiliate-account-tab'] ) ? sanitize_text_field( $_GET['affiliate-account-tab'] ) : $default_active_tab );

/**
 * Get the Affiliate ID.
 *
 */
$affiliate_id = slicewp_get_current_affiliate_id();

?>


<div id="slicewp-affiliate-account" <?php echo ( ! empty( $args['menu_position'] ) ? 'class="slicewp-menu-' . esc_attr( $args['menu_position'] ) . '"' : '' ); ?> data-affiliate-id="<?php echo absint( $affiliate_id ); ?>" data-affiliate-keyword="<?php echo esc_attr( slicewp_get_setting( 'affiliate_keyword', 'aff' ) ); ?>" data-affiliate-keyword-value="<?php echo esc_attr( slicewp_get_affiliate_url_referral_query_arg_value( $affiliate_id ) ); ?>" data-affiliate-friendly-url="<?php echo esc_attr( slicewp_get_setting( 'friendly_affiliate_url' ) ); ?>" >

    <?php

        /**
         * Add extra content at the top of the affiliate account.
         *
         */
        do_action( 'slicewp_affiliate_account_top' );

    ?>

	<!-- Tab Navigation -->
	<div id="slicewp-affiliate-account-nav-tab">

		<ul class="slicewp-nav-tab-wrapper">

			<?php

				foreach ( $tabs as $tab_slug => $tab ) {

					echo '<li class="slicewp-nav-tab ' . ( $tab_slug == $active_tab ? 'slicewp-active' : '' ) . '" ' . ( empty( $tab['url'] ) ? 'data-slicewp-tab="' . esc_attr( $tab_slug ) . '"' : '' ) . '><a href="' . esc_url( ( ! empty( $tab['url'] ) ? esc_url( $tab['url'] ) : '#' ) ) . '">' . wp_kses( $tab['icon'], slicewp_get_kses_allowed_html() ) . '<span>' . esc_attr( $tab['label'] ) . '</span></a></li>';

				}

			?>

		</ul>

	</div>
	
    <!-- Tabs -->
    <div id="slicewp-affiliate-account-tab">

        <?php
        
            foreach ( $tabs as $tab_slug => $tab ) {

                if ( ! empty( $tab['url'] ) ) {
                    continue;
                }

                echo '<div class="slicewp-tab ' . ( $active_tab == $tab_slug ? 'slicewp-active' : '' ) . '" data-slicewp-tab="' . esc_attr( $tab_slug ) . '">';

                    /**
                     * Add extra content at the top of the tab.
                     *
                     */
                    do_action( 'slicewp_affiliate_account_tab_' . $tab_slug . '_top' );

                    /**
                     * Include the template part for the tab.
                     * 
                     */
                    slicewp_get_template_part( 'affiliate-area/affiliate-account-tab-' . $tab_slug, null, array( 'affiliate_id' => $affiliate_id, 'active_tab' => $active_tab ) );

                    /**
                     * Add extra content at the bottom of the tab.
                     *
                     */
                    do_action( 'slicewp_affiliate_account_tab_' . $tab_slug . '_bottom' );

                echo '</div>';

            }

        ?>

    </div>

	<?php

		/**
		 * Add extra content at the bottom of the affiliate account.
		 *
		 */
		do_action( 'slicewp_affiliate_account_bottom' );

	?>

    <?php wp_nonce_field( 'slicewp_affiliate_account', 'slicewp_token', false ); ?>

</div>