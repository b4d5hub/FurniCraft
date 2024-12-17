<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for the different merge tags used throughout the plugin.
 *
 */
class SliceWP_Merge_Tags {

	/**
	 * The data array containing information for tags to use.
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $data = array();


	/**
	 * Constructor.
	 *
	 */
	public function __construct( ) {}

	/**
	 * Data setter.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 */
	public function set_data( $key, $value ) {

		$this->data[$key] = $value;

	}

	/**
	 * Returns the categories of tags available.
	 * 
	 * @return array
	 * 
	 */
	public function get_tags_categories() {

		$categories = array(
			'affiliate'  => array( 'name' => __( 'Affiliate', 'slicewp' ) ),
			'commission' => array( 'name' => __( 'Commission', 'slicewp' ) ),
			'payment'	 => array( 'name' => __( 'Payment', 'slicewp' ) ),
			'general'	 => array( 'name' => __( 'General', 'slicewp' ) )
		);

		/**
		 * Filter to register additional categories.
		 *
		 * @param array $tags
		 *
		 */
		$categories = apply_filters( 'slicewp_register_merge_tags_categories', $categories );

		return $categories;

	}

	/**
	 * Defines the tags that will be replaced with content.
	 * 
	 * @return array
	 *
	 */	
	public function get_tags() {

		$tags = array(
			'affiliate_id' => array(
				'description'	=> __( "Replaces the tag with affiliate's id", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_id' ),
				'category'		=> 'affiliate'
			),
			'affiliate_referral_url' => array(
				'description'	=> __( "Replaced the tag with the affiliate's referral URL.", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_url' ),
				'category'		=> 'affiliate'
			),
			'affiliate_username' => array(
				'description'	=> __( "Replaces the tag with the affiliate's username", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_username' ),
				'category'		=> 'affiliate'
			),
			'affiliate_email' => array(
				'description'	=> __( "Replaces the tag with the affiliate's email", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_email' ),
				'category'		=> 'affiliate'
			),
			'affiliate_first_name' => array(
				'description'	=> __( "replaces the tag with the affiliate's first name", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_first_name' ),
				'category'		=> 'affiliate'
			),
			'affiliate_last_name' => array(
				'description'	=> __( "Replaces the tag with the affiliate's last name", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_last_name' ),
				'category'		=> 'affiliate'
			),
			'affiliate_website' => array(
				'description'	=> __( "Replaces the tag with the affiliate's website", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_website' ),
				'category'		=> 'affiliate'
			),
			'affiliate_status'  => array(
				'description'   => __( "Replaces the tag with the affiliate's status", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_affiliate_status' ),
				'category'		=> 'affiliate'
			),
			'promotional_methods' => array(
				'description'   => __( "Replaces the tag with the affiliate application promotional methods", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_promotional_methods' ),
				'category'		=> 'affiliate'
			),
			'reject_reason' => array(
				'description'	=> __( "Replaces the tag with the affiliate application reject reason", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_reject_reason' ),
				'category'		=> 'affiliate'
			),
			'commission_id' => array(
				'description'	=> __( "Replaces the tag with the commission's ID", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_commission_id' ),
				'category'		=> 'commission'
			),
			'commission_amount' => array(
				'description'	=> __( "Replaces the tag with the commission amount", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_commission_amount' ),
				'category'		=> 'commission'
			),
			'commission_reference' => array(
				'description'	=> __( "Replaces the tag with the commission reference", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_commission_reference' ),
				'category'		=> 'commission'
			),
			'payment_id' => array(
				'description'   => __( "Replaces the tag with the affiliate payment's ID", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_payment_id' ),
				'category'		=> 'payment'
			),
			'payment_amount' => array(
				'description'   => __( "Replaces the tag with the affiliate payment amount", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_payment_amount' ),
				'category'		=> 'payment'
			),
			'payment_payout_method' => array(
				'description'   => __( "Replaces the tag with the affiliate payment's payout method", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_payment_payout_method' ),
				'category'		=> 'payment'
			),
			'site_name' => array(
				'description'	=> __( "Replaces the tag with your site name", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_site_name' ),
				'category'		=> 'general'
			),
			'page_affiliate_account' => array(
				'description'	=> __( "Replaces the tag with the link to the Affiliate Account Page", 'slicewp' ),
				'callback'		=> array( $this, 'do_tag_page_affiliate_account' ),
				'category'		=> 'general'
			)
		);

		/**
		 * Filter to add more tags.
		 *
		 * @param array 			 $tags
		 * @param SliceWP_Merge_Tags $this
		 *
		 * @deprecated 1.0.47 - No longer used in core and not recommended for external usage.
     	 *                      Replaced by "slicewp_register_merge_tags" filter.
     	 *                      Slated for removal in version 2.0.0
		 *
		 */
		$tags = apply_filters( 'slicewp_merge_tags', $tags, $this );

		/**
		 * Filter to register additional tags.
		 *
		 * @param array $tags
		 *
		 */
		$tags = apply_filters( 'slicewp_register_merge_tags', $tags );

		// Add tags that don't have a category set to the "general" category.
		foreach ( $tags as $index => $tag ) {
			
			if ( empty( $tag['category'] ) ) {
				$tags[$index]['category'] = 'general';
			}

		}

		return $tags;

	}

	/**
	 * The affiliate's ID.
	 * 
	 * @return int
	 * 
	 */
	public function do_tag_affiliate_id() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		return absint( $this->data['affiliate']->get( 'id' ) );

	}

	/**
	 * The affiliate's referral URL.
	 *
	 * @return string
	 *
	 */
	public function do_tag_affiliate_url() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		return slicewp_get_affiliate_url( $this->data['affiliate']->get( 'id' ) );

	}

	/**
	 * The affiliate's username.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_affiliate_username() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		$user = get_userdata( $this->data['affiliate']->get( 'user_id' ) );

		return $user->user_login;

	}

	/**
	 * The affiliate's email address.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_affiliate_email() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		$user = get_userdata( $this->data['affiliate']->get( 'user_id' ) );

		return $user->user_email;

	}
	
	/**
	 * The affiliate's first name.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_affiliate_first_name() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		$user = get_userdata( $this->data['affiliate']->get( 'user_id' ) );

		return $user->first_name;

	}

	/**
	 * The affiliate's last name.
	 * 
	 * @return string
	 * 
	 */	
	public function do_tag_affiliate_last_name() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}
		
		$user = get_userdata( $this->data['affiliate']->get( 'user_id' ) );

		return $user->last_name;

	}

	/**
	 * The affiliate's website.
	 * 
	 * @return string
	 * 
	 */	
	public function do_tag_affiliate_website() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		return $this->data['affiliate']->get( 'website' );

	}

	/**
	 * The affiliate's status.
	 * 
	 * @return string
	 * 
	 */	
	public function do_tag_affiliate_status() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		$affiliate_statuses = slicewp_get_affiliate_available_statuses();

		if ( empty( $affiliate_statuses[$this->data['affiliate']->get( 'status' )] ) ) {
			return '';
		}

		return $affiliate_statuses[$this->data['affiliate']->get( 'status' )];

	}

	/**
	 * The promotional methods added by the affiliate on registration.
	 *
	 * @return string
	 *
	 */
	public function do_tag_promotional_methods() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		return slicewp_get_affiliate_meta( $this->data['affiliate']->get( 'id' ), 'promotional_methods', true );

	}

	/**
	 * The reject reason.
	 * 
	 * @return string
	 * 
	 */	
	public function do_tag_reject_reason() {

		if ( empty( $this->data['affiliate'] ) ) {
			return '';
		}

		return slicewp_get_affiliate_meta( $this->data['affiliate']->get( 'id' ), 'reject_reason', true );

	}

	/**
	 * The commission's ID.
	 * 
	 * @return int
	 * 
	 */
	public function do_tag_commission_id() {

		if ( empty( $this->data['commission'] ) ) {
			return '';
		}

		return absint( $this->data['commission']->get( 'id' ) );

	}

	/**
	 * The commission amount.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_commission_amount() {

		if ( empty( $this->data['commission'] ) ) {
			return '';
		}
		
		return slicewp_format_amount( $this->data['commission']->get( 'amount' ), $this->data['commission']->get( 'currency' ) );		

	}

	/**
	 * The commission reference.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_commission_reference() {

		if ( empty( $this->data['commission'] ) ) {
			return '';
		}

		return $this->data['commission']->get( 'reference' );
		
	}

	/**
	 * The payment's ID.
	 * 
	 * @return int
	 * 
	 */
	public function do_tag_payment_id() {

		if ( empty( $this->data['payment'] ) ) {
			return '';
		}

		return absint( $this->data['payment']->get( 'id' ) );

	}

	/**
	 * The payment amount.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_payment_amount() {

		if ( empty( $this->data['payment'] ) ) {
			return '';
		}
		
		return slicewp_format_amount( $this->data['payment']->get( 'amount' ), $this->data['payment']->get( 'currency' ) );		

	}

	/**
	 * The payment payout method.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_payment_payout_method() {

		if ( empty( $this->data['payment'] ) ) {
			return '';
		}

		$methods = slicewp_get_payout_methods();

		if ( empty( $methods[$this->data['payment']->get( 'payout_method' )]['label'] ) ) {
			return $this->data['payment']->get( 'payout_method' );
		}

		return $methods[$this->data['payment']->get( 'payout_method' )]['label'];

	}

	/**
	 * The site name.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_site_name() {
		
		return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	}

	/**
	 * The affiliate account permalink.
	 * 
	 * @return string
	 * 
	 */
	public function do_tag_page_affiliate_account() {

		$page_id = slicewp_get_setting( 'page_affiliate_account', 0 );

		return ( ! empty( $page_id ) ? get_permalink( $page_id ) : '' );

	}

	/**
	 * Replaces the merge tags with the corresponding data in the given content.
	 *
	 * @param string $content
	 *
	 * @return string
	 *
	 */
	public function replace_tags( $content ) {

		$tags = $this->get_tags();

		foreach ( $tags as $tag_slug => $tag ) {

			// Call the callback.
			$tag_value = call_user_func( $tag['callback'], $this->data );

			// Make sure the tag_value is either a string or an integer.
			if ( ! is_string( $tag_value ) && ! is_int( $tag_value ) ) {
				continue;
			}

			// Replace the tag.
			$content = str_replace( '{{'. $tag_slug . '}}', $tag_value, $content );

		}

		return $content;

	}

}