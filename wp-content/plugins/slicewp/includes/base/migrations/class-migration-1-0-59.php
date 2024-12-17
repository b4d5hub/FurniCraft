<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class SliceWP_Migration_1_0_59
 *
 * Migration that runs when updating to version 1.0.59
 *
 */
class SliceWP_Migration_1_0_59 extends SliceWP_Abstract_Migration {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$this->id          = 'slicewp-update-1-0-59';
		$this->notice_type = 'notice';

		parent::__construct();

	}


    /**
	 * Initialize the components this migration needs to function
	 *
	 */
	public function init() {

        // Check if fresh installation.
        $first_activation = get_option( 'slicewp_first_activation', '' );

        // If fresh installation, just mark this migration as ran.
        if ( time() - 5 * MINUTE_IN_SECONDS < $first_activation ) {

            $this->mark_as_ran();

        } else {

            $payments = slicewp_get_payments( array( 'number' => -1 ), true );

            // If no payments exist, there's nothing to update, so just mark this migration as ran.
            if ( empty( $payments ) ) {
                
                $this->mark_as_ran();

            // If we do have payments, show the notice to run the migration.
            } else {

                add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			    add_action( 'admin_init', array( $this, 'check_for_migrate_actions' ), 50 );

            }

        }

	}


    /**
	 * Get the full notice HTML used to output the admin notice.
	 * This function can be overridden in a migration class if needed.
	 *
	 */
	protected function get_notice() {

		$href = wp_nonce_url( add_query_arg( array( 'slicewp_action' => 'migrate', 'slicewp_migration' => $this->id ) ), 'slicewp_migrate', 'slicewp_token' );
		
		?>

		<div class="notice notice-info">
			<p><strong><?php echo __( 'SliceWP database update required', 'slicewp' ); ?></strong></p>
            <p><?php echo __( "SliceWP has been updated! To keep things running smoothly, we have to update SliceWP's database to the latest version.", 'slicewp' ); ?></p>
			
            <p><a href="<?php echo esc_url( $href ); ?>" class="slicewp-button-primary"><?php echo __( 'Run the updater', 'slicewp' ); ?></a></p>
		</div>

		<?php

	}


	/**
	 * Actually run the migration
	 *
	 */
	public function migrate() {

        if ( ! function_exists( 'slicewp_get_payments' ) || ! function_exists( 'slicewp_update_commission' ) )
            return false;

        $payments = slicewp_get_payments( array( 'number' => -1 ) );

        if ( empty( $payments ) )
            return true;

        foreach ( $payments as $payment ) {

            $commission_ids = array_map( 'absint', explode( ',', $payment->get( 'commission_ids' ) ) );

            if ( empty( $commission_ids ) )
                continue;

            foreach ( $commission_ids as $commission_id ) {

                $commission = slicewp_get_commission( $commission_id );

                if ( is_null( $commission ) )
                    continue;

                $commission_data = array(
                    'date_modified' => slicewp_mysql_gmdate(),
                    'payment_id'    => $payment->get( 'id' )
                );

                slicewp_update_commission( $commission_id, $commission_data );

            }

        }

		return true;

	}

}