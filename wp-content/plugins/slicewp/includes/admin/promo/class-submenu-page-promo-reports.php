<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Promo_Reports extends SliceWP_Submenu_Page {

	/**
	 * Callback for the HTML output for the Promo Reports page.
	 *
	 */
	public function output() {

		?>

            <script>
                window.onload = function() {
                    document.getElementById( 'slicewp-promo-modal' ).classList.add( 'loaded' );
                }
            </script>

            <style>
                body .notice { display: none !important; }
                
                .slicewp-wrap-promo-reports { margin: -20px 0 0 -20px; padding: 20px 20px 0 20px; }
                .slicewp-wrap-promo-reports img { max-width: 100%; height: auto; filter: blur( 4px ); }

                .slicewp-wrap-promo-reports .slicewp-promo-modal-wrapper { position: relative; }
                .slicewp-wrap-promo-reports .slicewp-modal-overlay { position: absolute; z-index: 1; top: -20px; left: -20px; right: -20px; bottom: -20px; background: rgba( 0, 0, 0, 0.2 ); }
                
                .slicewp-wrap-promo-reports .slicewp-promo-modal { position: absolute; z-index: 1; background: #fff; padding: 30px; border-radius: 0.625rem; box-shadow: 0 5px 30px rgb(20 25 60 / 32%); top: 100px; left: 50%; max-width: 540px; width: 100%; margin-left: -270px; box-sizing: border-box; text-align: center; opacity: 0; transform: scale( 1.2 ); transition: all 0.15s ease-in-out; }
                .slicewp-wrap-promo-reports .slicewp-promo-modal.loaded { opacity: 1; transform: scale( 1 ); }

                .slicewp-wrap-promo-reports .slicewp-promo-modal h2 { margin: 0 !important; font-size: 1.4rem; }
                .slicewp-wrap-promo-reports .slicewp-promo-modal p { margin-top: 25px; font-size: 14px; line-height: 1.5rem; }
                
                .slicewp-wrap-promo-reports .slicewp-modal-footer { margin-top: 25px; justify-content: center; }
                .slicewp-wrap-promo-reports .slicewp-modal-footer .slicewp-button-upgrade { padding: 10px 30px; }
            </style>

            <div class="wrap slicewp-wrap slicewp-wrap-promo-reports">

                <div class="slicewp-promo-modal-wrapper">

                    <div class="slicewp-modal-overlay"></div>

                    <div id="slicewp-promo-modal" class="slicewp-promo-modal">
                        <h2><?php echo __( 'Enable advanced reports', 'slicewp' ); ?></h2>
                        <p><?php echo __( 'Track the performance of your affiliate partners and monitor key metrics to help you take your affiliate program to the next level.', 'slicewp' ); ?></p>

                        <div class="slicewp-modal-footer">
                            <a href="https://slicewp.com/features/reports/?utm_source=reports-upgrade&amp;utm_medium=plugin-admin&amp;utm_campaign=SliceWPFree" target="_blank" class="slicewp-button-upgrade"><?php echo __( 'Upgrade to PRO', 'slicewp' ); ?></a>
                        </div>
                    </div>

                    <img src="<?php echo SLICEWP_PLUGIN_DIR_URL; ?>/assets/img/slicewp-promo-reports.png" />

                </div>

            </div>

        <?php

	}

}