<?php
/**
 * Template for displaying the form to edit a wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/form-edit-wishlist.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wishlist ) ) {
	return;
}

do_action( 'wcboost_wishlist_before_edit_form', $wishlist ); ?>

<form class="wcboost-wishlist-form-edit" method="post">
	<h3><?php esc_html_e( 'Edit Wishlist', 'wcboost-wishlist' ); ?></h3>

	<?php
	if (  wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_title', 'no' ) ) ) {
		woocommerce_form_field(
			'wishlist_title',
			[
				'type'     => 'text',
				'required' => true,
				'label'    => esc_html__( 'Wishlist name', 'wcboost-wishlist' ),
			],
			$wishlist->get_wishlist_title()
		);
	}

	if ( wc_string_to_bool( get_option( 'wcboost_wishlist_page_show_desc', 'no' ) ) ) {
		woocommerce_form_field(
			'wishlist_description',
			[
				'type'              => 'textarea',
				'required'          => false,
				'label'             => esc_html__( 'Description', 'wcboost-wishlist' ),
				'custom_attributes' => [
					'rows' => 8,
				],
			],
			$wishlist->get_description()
		);
	}

	woocommerce_form_field(
		'wishlist_privacy',
		[
			'type'     => 'radio',
			'required' => true,
			'class'    => 'form-row--wishlist-privacy',
			'label'    => esc_html__( 'Privacy settings', 'wcboost-wishlist' ),
			'options'  => [
				'publish' => esc_html__( 'Public - Anyone can view this list. Everyone can share this list.', 'wcboost-wishlist' ),
				'shared'  => esc_html__( 'Shared - Only people with the link can view this list. Only you can share this list.', 'wcboost-wishlist' ),
				'private' => esc_html__( 'Private - Only you can view this list.', 'wcboost-wishlist' ),
			],
		],
		$wishlist->get_status()
	);
	?>

	<?php do_action( 'wcboost_wishlist_edit_form' ); ?>

	<p>
		<?php wp_nonce_field( 'wcboost-wishlist-update' ); ?>
		<button type="submit" class="button alt" name="update_wishlist" value="<?php esc_attr_e( 'Save changes', 'wcboost-wishlist' ); ?>"><?php esc_html_e( 'Save changes', 'wcboost-wishlist' ); ?></button>
		<input type="hidden" name="action" value="update_wishlist" />
		<input type="hidden" name="wishlist_id" value="<?php echo esc_attr( $wishlist->get_id() ); ?>" />
	</p>
</form>

<?php do_action( 'wcboost_wishlist_after_edit_form', $wishlist ); ?>
