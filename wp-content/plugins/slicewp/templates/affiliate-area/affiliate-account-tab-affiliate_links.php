<?php
/**
 * Affiliate account tab: Affiliate Links
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-affiliate_links.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="slicewp-section-general-affiliate-link slicewp-card">

    <div class="slicewp-card-inner">

        <div class="slicewp-field-wrapper">

            <div class="slicewp-field-label-wrapper slicewp-field-description-placement-before">
                <label for="slicewp-affiliate-link"><?php echo __( 'Your Affiliate Link', 'slicewp' ); ?></label>
                <div class="slicewp-field-description"><p><?php echo __( 'This is your referral URL. Share it with your audience to earn commissions.', 'slicewp' ); ?></p></div>
            </div>

            <input id="slicewp-affiliate-link" name="affiliate_link" type="text" readonly value="<?php echo esc_url( slicewp_get_affiliate_url( $args['affiliate_id'] ) ); ?>" />

            <button class="slicewp-button-primary slicewp-input-copy">
                <?php echo slicewp_get_svg( 'outline-duplicate' ); ?>
                <span class="slicewp-input-copy-label"><?php echo __( 'Copy', 'slicewp' ); ?></span>
                <span class="slicewp-input-copy-label-copied"><?php echo __( 'Copied!', 'slicewp' ); ?></span>
            </button>

            <?php

                /**
                 * Hook after the copy affiliate link button
                 *
                 */
                do_action( 'slicewp_affiliate_account_affiliate_link_actions' );

            ?>
            
        </div>
        
    </div>

</div>


<div class="slicewp-section-affiliate-link-generator slicewp-card">

    <div class="slicewp-card-inner">

        <div class="slicewp-affiliate-custom-link-input">

            <div class="slicewp-user-notice slicewp-error" id="slicewp-affiliate-custom-link-input-empty" style="display:none"><?php echo __( 'Please provide a link!', 'slicewp' );?></div>
            <div class="slicewp-user-notice slicewp-error" id="slicewp-affiliate-custom-link-input-invalid-url" style="display:none"><?php echo __( 'The provided link is not valid!', 'slicewp' );?></div>

            <div class="slicewp-field-label-wrapper slicewp-field-description-placement-before">
                <label for="slicewp-affiliate-custom-link-input"><?php echo __( 'Generate Affiliate Link', 'slicewp' ); ?></label>
                <div class="slicewp-field-description"><p><?php echo __( 'Add any URL from this website in the field below to generate a referral link.', 'slicewp' ); ?></p></div>
            </div>

            <input id="slicewp-affiliate-custom-link-input" name="affiliate_link_input" type="text" placeholder="<?php echo esc_attr( __( 'Paste the link here', 'slicewp' ) ); ?>" />
        
            <button type="submit" class="slicewp-button-primary slicewp-generate-affiliate-link"><?php echo esc_html( __( 'Generate', 'slicewp' ) ); ?></button>
        
        </div>

        <div class="slicewp-affiliate-custom-link-output" style="display:none">

            <div class="slicewp-field-label-wrapper slicewp-field-description-placement-before">
                <label for="slicewp-affiliate-custom-link-output"><?php echo __( 'Generated Referral Link', 'slicewp' ); ?></label>
                <div class="slicewp-field-description"><p><?php echo __( 'Share the affiliate referral link below to earn commissions.', 'slicewp' ); ?></p></div>
            </div>

            <input id="slicewp-affiliate-custom-link-output" name="affiliate_link_output" type="text" />

            <button class="slicewp-button-primary slicewp-input-copy">
                <?php echo slicewp_get_svg( 'outline-duplicate' ); ?>
                <span class="slicewp-input-copy-label"><?php echo __( 'Copy', 'slicewp' ); ?></span>
                <span class="slicewp-input-copy-label-copied"><?php echo __( 'Copied!', 'slicewp' ); ?></span>
            </button>
        
            <?php

                /**
                 * Hook after the generate custom link button
                 *
                 */
                do_action( 'slicewp_affiliate_account_custom_affiliate_link_actions' );

            ?>

        </div>

    </div>

</div>