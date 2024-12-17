<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handled the deactivation pop-up module
 *
 */
class SliceWP_Deactivation {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		global $pagenow;

	    if( is_admin() && $pagenow == 'plugins.php' ) {

	    	add_action( 'admin_footer', array( $this, 'add_form' ) );
			add_action( 'admin_footer', array( $this, 'add_css' ) );
			add_action( 'admin_footer', array( $this, 'add_js' ) );

	    }

	    add_action( 'wp_ajax_slicewp_send_deactivation_feedback', array( $this, 'send_feedback' ) );

	}


	/**
	 * Adds the deactivation modal fomr in the Plugins' page footer
	 *
	 */
	public function add_form() {

		?>

		<div id="slicewp-deactivate-modal-wrapper">
		    <div id="slicewp-deactivate-modal">
		    	<form action="" method="post">

		    		<!-- Modal header -->
		    		<div id="slicewp-deactivate-header">
		    			<img src="<?php echo SLICEWP_PLUGIN_DIR_URL; ?>/assets/img/slicewp-logo.png" />
		    		</div>

		    		<!-- Modal inner -->
		    		<div id="slicewp-deactivate-inner">

			    	    <p><strong><?php echo __( "If you have a moment, please let us know why you are deactivating:", 'slicewp' ); ?></strong></p>

			    	    <ul>

			    	    	<li>
								<label>
									<input type="radio" name="slicewp_disable_reason" value="technical-issue" />
									<?php echo __( 'Technical issues', 'slicewp' ); ?>
								</label>
							</li>

			                <li>
			                	<label>
			                		<input type="radio" name="slicewp_disable_reason" value="missing-feature" />
			                		<?php echo __( 'Missing features I need', 'slicewp' ); ?>
								</label>
							</li>

							<li>
								<label>
									<input type="radio" name="slicewp_disable_reason" value="other" />
									<?php echo __( 'Other reason', 'slicewp' ); ?>
			    			  	</label>
			    			</li>

			    	    </ul>

			    	    <p><strong><?php echo __( 'Have more feedback about the plugin? Don’t hold back.', 'slicewp' ); ?></strong></p>
			    	    <textarea name="slicewp_disable_text[]" placeholder="<?php echo __( 'Type your feedback here...', 'slicewp' ); ?>"></textarea>
			    	    <a href="https://slicewp.com/contact/" target="_blank"><?php echo __( 'Or contact us for support', 'slicewp' ); ?></a>

			    	</div>

			    	<!-- Modal footer -->
		    	    <div id="slicewp-deactivate-footer">
			    	    <input disabled id="slicewp-feedback-submit" class="button button-secondary" type="submit" name="slicewp-feedback-submit" value="<?php echo __( 'Submit & Deactivate', 'slicewp' ); ?>" />
			    	    <a id="slicewp-deactivate-close" href="#"><?php echo __( 'Do not deactivate', 'slicewp'); ?></a>
			    	</div>

			    	<!-- Token -->
			    	<?php wp_nonce_field( 'slicewp_deactivation', 'slicewp_token', false ); ?>

		    	</form>
		    </div>
		</div>

		<?php

	}


	/**
	 * Adds the needed styles in the Plugins' page footer
	 *
	 */
	public function add_css() {

		?>

		<style>

			#slicewp-deactivate-modal-wrapper { display: none; z-index: 9999; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,.7);  }
			#slicewp-deactivate-modal { z-index: 10000; position: fixed; top: 7.5%; left: 50%; background: #fff; border-radius: 4px; max-width: 600px; margin-left: -300px; width: 100%; }
			
			#slicewp-deactivate-header { padding: 15px 0 10px 20px; border-bottom: 1px solid rgba( 200, 215, 225, 0.5 ); }
			#slicewp-deactivate-header img { max-height: 30px; width: auto; }

			#slicewp-deactivate-inner { padding: 20px; }

			#slicewp-deactivate-inner > p { margin-top: 0; font-size: 1.1em; }

			#slicewp-deactivate-modal ul { margin: 0 0 25px; }
			#slicewp-deactivate-modal li { margin: 10px 0; transition: opacity 0.2s ease-in-out; }

			#slicewp-deactivate-modal textarea,
			#slicewp-deactivate-modal input[type="text"] { width: 100%; }
			#slicewp-deactivate-modal textarea { min-height: 65px; font-size: 13px; padding: 6px 11px; }
			#slicewp-deactivate-modal #slicewp-deactivate-close { float: right; line-height: 30px; }

			#slicewp-deactivate-footer { border-top: 1px solid rgba( 200, 215, 225, 0.5 ); background: rgba( 200, 215, 225, 0.15 ); padding: 20px; }

		</style>

		<?php

	}


	/**
	 * Adds the needed JS in the Plugins' page footer
	 *
	 */
	public function add_js() {

		?>

		<script>

			jQuery( function($) {

				/**
				 * Show the deactivation modal when clicking the "Deactivate" link in the Plugins page
				 *
				 */
			    $(document).on( 'click', '.wp-admin.plugins-php tr[data-slug="slicewp"] .row-actions .deactivate a', function(e) {

			        e.preventDefault();  
			        $('#slicewp-deactivate-modal-wrapper').show();

			    });

			    /**
			     * Show/hide the description and feedback textarea for each list item when clicking on the
			     * corresponding radio input
			     *
			     */
			    $(document).on( 'click', '#slicewp-deactivate-modal form input[type="radio"]', function () {
			        
			        $('#slicewp-feedback-submit').attr( 'disabled', false );

			    });

			    /**
			     * Hide the modal, make AJAX call to send feedback and then deactivate the plugin
			     *
			     */
			    $(document).on( 'click', '#slicewp-feedback-submit', function(e) {

			        e.preventDefault();

			        $('#slicewp-deactivate-modal-wrapper').hide();

			        $.ajax({
			            type 	 : 'POST',
			            url 	 : ajaxurl,
			            dataType : 'json',
			            data 	 : {
			                action 		  : 'slicewp_send_deactivation_feedback',
			                slicewp_token : $('#slicewp_token').val(),
			                data   		  : $('#slicewp-deactivate-modal form').serialize()
			            },
			            complete : function( MLHttpRequest, textStatus, errorThrown ) {

			                $('#slicewp-deactivate-modal').remove();

			                window.location.href = $('.wp-admin.plugins-php tr[data-slug="slicewp"] .row-actions .deactivate a').attr('href');   

			            }
			        });

			    });
			    
			    /**
			     * Hide the modal and deactivate the plugin
			     *
			     */
			    $(document).on( 'click', '#slicewp-only-deactivate', function(e) {

			        e.preventDefault();

			        $('#slicewp-deactivate-modal-wrapper').hide();        
			        $('#slicewp-deactivate-modal-wrapper').remove();
			        
			        window.location.href = $('.wp-admin.plugins-php tr[data-slug="slicewp"] .row-actions .deactivate a').attr('href');
			        
			    });
			    
			    /**
			     * Hide the modal and do nothing else
			     *
			     */
			    $(document).on( 'click', '#slicewp-deactivate-close', function(e) {

			        e.preventDefault();

			        $('#slicewp-deactivate-modal-wrapper').hide();

			    });

			});

		</script>

		<?php

	}


	/**
	 * AJAX callback to send feedback to our email address
	 *
	 */
	public function send_feedback() {

		if( empty( $_POST['slicewp_token'] ) || ! wp_verify_nonce( $_POST['slicewp_token'], 'slicewp_deactivation' ) )
			wp_die( 0 );

		// Exit if the user doesn't have priviledges
		if( ! current_user_can( 'manage_options' ) )
			wp_die( 0 );

		if( isset( $_POST['data'] ) ) {
	        parse_str( $_POST['data'], $form );
	    }
	    
	    $subject = "SliceWP Deactivation Notification";
	    $message = isset( $form['slicewp_disable_reason'] ) ? 'Deactivation reason: ' . sanitize_text_field( $form['slicewp_disable_reason'] ) : '(no reason given)';
	    
	    if( isset( $form['slicewp_disable_text'] ) ) {
	        $message .= "\n\r\n\r";
	        $message .= 'Message: ' . sanitize_text_field( implode('', $form['slicewp_disable_text']) );
	    }
	    
	    $success = wp_mail( array( 'support@slicewp.com' ), $subject, $message );

	    wp_die();

	}

}

new SliceWP_Deactivation();