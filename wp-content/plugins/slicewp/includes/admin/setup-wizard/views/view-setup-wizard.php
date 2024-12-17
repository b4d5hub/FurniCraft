<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-setup-wizard">

    <div class="slicewp-setup-logo"><a href="https://slicewp.com/" target="_blank"><img src="<?php echo SLICEWP_PLUGIN_DIR_URL; ?>/assets/img/slicewp-logo.png" /></a></div>

    <div class="slicewp-setup-welcome-panel" <?php echo ( $this->current_step_index != 0 ? 'style="display: none;"' : '' ); ?>>

        <h2 class="slicewp-setup-welcome-heading"><?php echo __( 'Welcome and thank you for choosing us!', 'slicewp' ); ?></h2>
        <p class="slicewp-setup-welcome-subheading"><?php echo __( "Let's get your affiliate program up and running! Click below, and we'll walk you through the initial process. It only takes a couple of minutes. And don't worry. You can change all of these settings, at anytime.", 'slicewp' ) ?></p>
        <a href="#" class="slicewp-button-primary slicewp-setup-welcome-start-button"><?php echo __( 'Get started', 'slicewp' ); ?></a>
        <br />
        <a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_action' => 'skip_setup_wizard' ) , admin_url( 'admin.php' ) ), 'slicewp_skip_setup_wizard', 'slicewp_token' ); ?>" class="slicewp-setup-skip"><?php echo __( 'Skip and hide setup wizard', 'slicewp' ); ?></a>

    </div>

    <!-- Setup Steps -->
	<div class="slicewp-setup-steps-wrapper" <?php echo ( $this->current_step_index != 0 ? 'style="display: block; opacity: 1; top: 0;"' : '' ); ?>>

        <ul class="slicewp-setup-steps">

            <?php $index_step = 0; ?>

            <?php foreach ( $this->steps as $step_slug => $step_name ): ?>

                <li class="slicewp-setup-step <?php echo ( $index_step < array_search( $this->current_step, array_keys( $this->steps ) ) ? 'slicewp-done' : '' ); ?> <?php echo ( $step_slug == $this->current_step ? 'slicewp-current' : '' ) ?>">

                    <span class="slicewp-setup-step-name">

                        <?php if ( $index_step < array_search( $this->current_step, array_keys( $this->steps ) ) ): ?>
                            <a href="<?php echo add_query_arg( array( 'page' => 'slicewp-setup', 'current_step' => $step_slug ), admin_url( 'admin.php' ) ); ?>"><?php echo esc_html( $step_name ); ?></a>
                        <?php else: ?>
                            <?php echo $step_name; ?>
                        <?php endif; ?>
                    </span>

                    <span class="slicewp-setup-step-index"><?php echo $index_step + 1; ?></span>

                </li>

                <?php $index_step++; ?>

            <?php endforeach; ?>

        </ul>

    </div>
    <!-- / Setup Steps -->

    <!-- Step: Integrations -->
    <div class="slicewp-card slicewp-card-setup-integrations">

        <div class="slicewp-card-inner">

            <form class="slicewp-setup-integrations">

                <h2><?php echo __( "First things first, let's select the eCommerce plugin that powers your business", 'slicewp' ); ?></h2>

                <p><?php echo __( "SliceWP will integrate seamlessly with any of these options, to track visits and generate commissions for orders referred by your affiliates.", 'slicewp' ); ?></p>

                <br />

                <div class="row">
                    <?php $index = 1; ?>
                    <?php foreach ( slicewp()->integrations as $integration_slug => $integration ): ?>

                        <?php if ( empty( $integration->get( 'supports' ) ) ) continue; ?>
                        
                        <div>
                            <input id="slicewp-integration-<?php echo esc_attr( $integration_slug ); ?>" type="checkbox" value="<?php echo esc_attr( $integration_slug ); ?>" name="integrations[]" />
                            <label for="slicewp-integration-<?php echo esc_attr( $integration_slug ); ?>">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php echo $integration->get( 'name' ); ?>
                            </label>
                        </div>

                        <?php if ( $index % 2 == 0 && $index != count( slicewp()->integrations ) ): ?>
                            </div><div class="row">
                        <?php endif; ?>

                        <?php $index++; ?>

                    <?php endforeach; ?>
                </div>

            </form>

        </div>

        <div class="slicewp-card-footer">

            <div class="slicewp-submit-wrapper-setup-wizard">
                
                <div class="spinner"></div>
                <a href="#" class="slicewp-button-primary" data-step="integrations"><?php echo __( 'Continue', 'slicewp' ); ?></a>

            </div>

        </div>

    </div>
    <!-- / Step: Integrations -->

    <!-- Step: Program Basics -->
    <div class="slicewp-card slicewp-card-setup-setup" <?php echo ( $this->current_step == 'setup' ? 'style="display: block; opacity: 1; left: 0;"' : '' ); ?>>

        <div class="slicewp-card-inner">

            <form class="slicewp-setup-setup">

                <h2><?php echo __( "A few essential things we need to set up", 'slicewp' ); ?></h2>

                <p><?php echo __( "Please set up the following options that are at the core of your affiliate program. If you're not quite sure how you want these options set don't worry, you can change them later.", 'slicewp' ); ?></p>

                <br />

                <!-- Commission Rates -->
                <?php $commission_types = slicewp_get_available_commission_types( true ); ?>
                <?php foreach ( $commission_types as $type => $details ): ?>

                    <?php 
                        $rate 	   = slicewp_get_setting( 'commission_rate_' . $type );
                        $rate_type = slicewp_get_setting( 'commission_rate_type_' . $type );
                    ?>

                    <div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate">

                        <div class="slicewp-field-label-wrapper">
                            <label for="slicewp-commission-rate-<?php echo esc_attr( str_replace( '_', '-', $type ) ); ?>">
                                <?php echo sprintf( __( '%s Commission Rate', 'slicewp' ), $details['label'] ); ?>
                            </label>
                        </div>
                        
                        <input id="slicewp-commission-rate-<?php echo esc_attr( str_replace( '_', '-', $type ) ); ?>" name="commission_rate_<?php echo esc_attr( $type ); ?>" type="number" step="any" min="0" value="<?php echo esc_attr( ! empty( $_POST['settings']['commission_rate_' . $type] ) ? $_POST['settings']['commission_rate_' . $type] : $rate ); ?>" />					

                        <select name="commission_rate_type_<?php echo esc_attr( $type ); ?>" class="slicewp-select2" <?php echo ( count( $details['rate_types'] ) == 1 ? 'disabled' : '' ); ?>>
                            <?php $currency_symbol = slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?>
                            <?php foreach ( $details['rate_types'] as $details_rate_type ): ?>
                                <option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) . ' (' . esc_attr( $currency_symbol ) . ')' ); ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                <?php endforeach; ?>
                <!-- / Commission Rates -->

                <!-- Currency -->
                <div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-active-currency">
                            <?php echo __( 'Currency', 'slicewp' ); ?>
                        </label>
                    </div>

                    <select id="slicewp-active-currency" name="active_currency" class="slicewp-select2">
                        <?php foreach ( slicewp_get_currencies() as $currency_code => $currency_name ): ?>
                            <?php $currency_symbol = slicewp_get_currency_symbol( $currency_code ); ?>
                            <option value="<?php echo esc_attr( $currency_code ); ?>"><?php echo esc_attr( $currency_name ) . ( ! empty( $currency_symbol ) ? ( ' (' . $currency_symbol . ')' ) : '' ); ?></option>
                        <?php endforeach; ?>
                    </select>

                </div><!-- / Currency -->

                <!-- Cookie Duration -->
                <div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide slicewp-last">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-cookie-duration">
                            <?php echo __( 'Tracking Cookie Duration', 'slicewp' ); ?>
                            <?php echo slicewp_output_tooltip( '<p>' . __( 'The number of days a referred visitor is being tracked.' , 'slicewp' ) . '</p><p>' . __( 'If the referred visitor makes a purchase in this timeframe, the referring affiliate will be rewarded a commission.', 'slicewp' ) . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/cookie-duration/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
                        </label>
                    </div>

                    <input id="slicewp-cookie-duration" name="cookie_duration" type="number" min="0" value="30" />

                </div><!-- / Cookie Duration -->

            </form>

        </div>

        <div class="slicewp-card-footer">

            <div class="slicewp-submit-wrapper-setup-wizard">

                <div class="spinner"></div>
                <a href="#" class="slicewp-button-tertiary"><?php echo __( 'Skip step', 'slicewp' ); ?></a>
                <a href="#" class="slicewp-button-primary" data-step="setup"><?php echo __( 'Continue', 'slicewp' ); ?></a>

            </div>

        </div>

    </div>
    <!-- / Step: Program Basics -->


    <!-- Step: Affiliate Pages -->
    <div class="slicewp-card slicewp-card-setup-pages" <?php echo ( $this->current_step == 'pages' ? 'style="display: block; opacity: 1; left: 0;"' : '' ); ?>>

        <div class="slicewp-card-inner">

            <form class="slicewp-setup-pages">

                <h2><?php echo __( "Welcome your affiliates", 'slicewp' ); ?></h2>

                <p><?php echo __( "To offer your affiliates a welcoming experience and have them interact with the website, SliceWP can automatically create a few pages designed precisely for them.", 'slicewp' ) ?></p>

                <p><?php echo __( "You can select just one or all of them below.", 'slicewp' ); ?></p>

                <br />

                <table class="slicewp-table-simple">

                    <!-- Affiliate Register Page -->
                    <tr>

                        <td>

                            <div class="slicewp-field-wrapper slicewp-last">

                                <div class="slicewp-field-label-wrapper">
                                    <label for="slicewp-affiliate-register-page">
                                        <?php echo __( 'Affiliate Register Page', 'slicewp' ); ?>
                                    </label>
                                </div>

                                <div class="slicewp-switch">

                                    <input id="slicewp-affiliate-register-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_register" type="checkbox" checked="checked" value="1" />
                                    <label for="slicewp-affiliate-register-page"></label>

                                </div>

                                <label for="slicewp-affiliate-register-page"><?php echo __( "This page will contain a form where users will have the ability to register as affiliates.", 'slicewp' ); ?></label>

                            </div>

                        </td>
                    
                    </tr>
                    <!-- / Affiliate Register Page -->

                    <!-- Affiliate Account Page -->
                    <tr>

                        <td>

                            <div class="slicewp-field-wrapper slicewp-last">

                                <div class="slicewp-field-label-wrapper">
                                    <label for="slicewp-affiliate-account-page">
                                        <?php echo __( 'Affiliate Account Page', 'slicewp' ); ?>
                                    </label>
                                </div>

                                <div class="slicewp-switch">

                                    <input id="slicewp-affiliate-account-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_account" type="checkbox" checked="checked" value="1" />
                                    <label for="slicewp-affiliate-account-page"></label>

                                </div>

                                <label for="slicewp-affiliate-account-page"><?php echo __( "This page will be your affiliates' personal dashboard, where they'll be able to generate referral links and view the visits and commissions they generated.", 'slicewp' ); ?></label>

                            </div>

                        </td>
                    
                    </tr>
                    <!-- / Affiliate Account Page -->

                    <!-- Affiliate Reset Password Page -->
                    <tr>

                        <td>

                            <div class="slicewp-field-wrapper slicewp-last">

                                <div class="slicewp-field-label-wrapper">
                                    <label for="slicewp-affiliate-reset-password-page">
                                        <?php echo __( 'Affiliate Reset Password Page', 'slicewp' ); ?>
                                    </label>
                                </div>

                                <div class="slicewp-switch">

                                    <input id="slicewp-affiliate-reset-password-page" class="slicewp-toggle slicewp-toggle-round" name="page_affiliate_reset_password" type="checkbox" checked="checked" value="1" />
                                    <label for="slicewp-affiliate-reset-password-page"></label>

                                </div>

                                <label for="slicewp-affiliate-reset-password-page"><?php echo __( 'This page will contain a form where affiliates will be able to reset their password in case they forgot it.', 'slicewp' ); ?></label>

                            </div>

                        </td>

                    </tr>
                    <!-- / Affiliate Reset Password Page -->
                
                </table>

            </form>

        </div>

        <div class="slicewp-card-footer">

            <div class="slicewp-submit-wrapper-setup-wizard">
                
                <div class="spinner"></div>
                <a href="#" class="slicewp-button-tertiary"><?php echo __( 'Skip step', 'slicewp' ); ?></a>
                <a href="#" class="slicewp-button-primary" data-step="pages"><?php echo __( 'Continue', 'slicewp' ); ?></a>

            </div>

        </div>

    </div>
    <!-- / Step: Affiliate Pages -->

    <!-- Step: Email Notifications -->
    <div class="slicewp-card slicewp-card-setup-emails" <?php echo ( $this->current_step == 'emails' ? 'style="display: block; opacity: 1; left: 0;"' : '' ); ?>>

        <div class="slicewp-card-inner">

            <form class="slicewp-setup-emails">

                <h2><?php echo __( "Email notifications. Which ones do you want activated to start with?", 'slicewp' ); ?></h2>

                <p><?php echo __( "You'll be able to customize each email to your needs later on in the settings page of SliceWP.", 'slicewp' ) ?></p>

                <br />

                <!-- Email Notifications -->
                <?php
                    $email_notifications 	   = slicewp_get_available_email_notifications();
                    $email_notifications_count = 0;
                ?>

                <table class="slicewp-table-simple">
                
                    <?php foreach ( $email_notifications as $email_notification_slug => $email_notification ): $email_notifications_count++; ?>

                        <?php if ( ! empty( $email_notification['sending'] ) && $email_notification['sending'] == 'manual' ) continue; ?>

                        <tr>
                            <td>
                                <div class="slicewp-field-wrapper slicewp-field-wrapper-email-notification <?php echo ( $email_notifications_count == count( $email_notifications ) ? 'slicewp-last' : '' ); ?>">

                                    <div class="slicewp-field-label-wrapper">
                                        <label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>">
                                            <?php echo sprintf( __( '%s Notification', 'slicewp' ), ucfirst( $email_notification['recipient'] ) ) . ' - ' . $email_notification['name']; ?>
                                        </label>
                                    </div>

                                    <div class="slicewp-switch">

                                        <input id="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>" class="slicewp-toggle slicewp-toggle-round" name="<?php echo esc_attr( $email_notification_slug ); ?>" type="checkbox" checked="checked" value="1" />
                                        <label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>"></label>

                                    </div>

                                    <label for="slicewp-<?php echo esc_attr( $email_notification_slug ); ?>"><?php echo $email_notification['description']; ?></label>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </table>

            </form>

        </div>

        <div class="slicewp-card-footer">

            <div class="slicewp-submit-wrapper-setup-wizard">

                <div class="spinner"></div>
                <a href="#" class="slicewp-button-tertiary"><?php echo __( 'Skip step', 'slicewp' ); ?></a>
                <a href="#" class="slicewp-button-primary" data-step="emails"><?php echo __( 'Continue', 'slicewp' ); ?></a>
                
            </div>

        </div>

    </div>
    <!-- / Step: Email Notifications -->

    <!-- Step: Finished -->
    <div class="slicewp-card slicewp-card-setup-finished" <?php echo ( $this->current_step == 'finished' ? 'style="display: block; opacity: 1; left: 0;"' : '' ); ?>>

        <div class="slicewp-card-inner">

            <div class="slicewp-setup-finished">

                <h2><?php echo __( "You're all set up and good to go!", 'slicewp' ); ?></h2>

                <p><?php echo __( "SliceWP is ready to run your affiliate program. Remember that you can always go to the plugin's settings page to modify the settings covered by the setup wizard.", 'slicewp' ); ?></p>

                <h2><?php echo __( "Next steps", 'slicewp' ); ?></h2>

                <table class="slicewp-table-simple">

                    <tr>
                        <td colspan="2">
                            <h3><?php echo __( 'Explore the SliceWP documentation', 'slicewp' ); ?></h3>

                            <div class="slicewp-flex">
                                <p><?php echo __( 'Learn the ins and outs of SliceWP and set up your affiliate program exactly how you want it.', 'slicewp' ) ?></p>
                                <a href="https://slicewp.com/docs/" target="_blank" class="slicewp-button-secondary"><?php echo __( 'Documentation', 'slicewp' ); ?></a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <h3><?php echo __( 'Take your affiliate program to the next level!', 'slicewp' ); ?></h3>

                            <div class="slicewp-flex">
                                <p><?php echo __( 'Extend your affiliate program with the PRO growth tools your affiliates need to stand out in a crowded market.', 'slicewp' ) ?></p>
                                <a href="https://slicewp.com/add-ons/" target="_blank" class="slicewp-button-primary"><?php echo __( 'Explore PRO', 'slicewp' ); ?></a>
                            </div>
                        </td>
                    </tr>

                    <tr>

                        <td class="slicewp-setup-newsletter">
                            <div>

                                <h3><?php echo __( "Stay in the loop", 'slicewp' ); ?></h3>
                                <p><?php echo __( "Get tips, news and product updates, straight to your inbox.", 'slicewp' ); ?></p>

                                <form class="slicewp-setup-newsletter-form" action="https://slicewp.us19.list-manage.com/subscribe/post?u=506ed65ce0a7eec2aa1c7cc61&id=5fe80d913e&SIGNUP=plugin_wizard" method="POST" target="_blank" novalidate>

                                    <div class="slicewp-setup-newsletter-form-email">
                                        <input type="email" value="" name="EMAIL" placeholder="<?php echo __( 'Your Email', 'slicewp'); ?>" class="required email" id="mce-EMAIL">
                                    </div>

                                    <div id="mce-responses" class="clear">
                                        <div class="response" id="mce-error-response" style="display:none"></div>
                                        <div class="response" id="mce-success-response" style="display:none"></div>
                                    </div>
                                    
                                    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_506ed65ce0a7eec2aa1c7cc61_5fe80d913e" tabindex="-1" value=""></div>
                                    <input type="submit" class="slicewp-button-secondary" value="<?php echo __( 'Yes, please!', 'slicewp' ); ?>" name="subscribe" id="mc-embedded-subscribe" class="button">

                                </form>

                            </div>
                        </td>

                        <td class="slicewp-setup-facebook-group">
                            <div>
                                <h3><?php echo __( "Join our Facebook group", 'slicewp' ); ?></h3>

                                <div>
                                    <p><?php echo __( 'Ask SliceWP related questions, connect with other business owners, learn and also help others to run a successful affiliate program.', 'slicewp' ); ?></p>
                                    <a class="slicewp-button-primary slicewp-button-facebook" href="https://www.facebook.com/groups/slicewp/" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" x="0px" y="0px" width="50" height="50" viewBox="0 0 50 50" style="null;fill: #fff;width: 16px;height: auto;margin-right: 7px;vertical-align: middle;" class="icon icons8-Facebook-Filled">    <path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z"></path></svg><?php echo __( 'Join group', 'slicewp' ); ?></a>
                                </div>
                            </div>
                        </td>

                    </tr>

                </table>

            </div>

        </div>

        <div class="slicewp-card-footer">

            <a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-settings', 'slicewp_action' => 'finish_setup_wizard' ) , admin_url( 'admin.php' ) ), 'slicewp_finish_setup_wizard', 'slicewp_token' ); ?>" class="slicewp-button-primary"><?php echo __( 'Finish and hide setup wizard', 'slicewp' ); ?></a>

        </div>

    </div>
    <!-- / Step: Finished -->

    <?php wp_nonce_field( 'slicewp_setup_wizard', 'slicewp_token', false ); ?>

</div>