<?php
/**
 * Affiliate account tab: Settings
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-settings.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<form class="slicewp-form" action="" method="POST" enctype="multipart/form-data">

    <!-- Notices -->
    <?php do_action( 'slicewp_user_notices' ); ?>
    
    <div class="slicewp-card">

        <!-- Form Fields -->
        <div class="slicewp-card-inner">

            <?php 

                /**
                 * Hooks to output form fields.
                 *
                 * @param string $form
                 *
                 */
                do_action( 'slicewp_form_fields', 'affiliate_account' );

            ?>

        </div>

    </div>

    <!-- Action and nonce -->
    <input type="hidden" name="slicewp_action" value="update_affiliate_settings" />
    <?php wp_nonce_field( 'slicewp_update_affiliate_settings', 'slicewp_token', false ); ?>

    <!-- Submit -->
    <button type="submit" class="slicewp-button-primary"><?php echo esc_html( __( 'Save', 'slicewp' ) ); ?></button>

</form>