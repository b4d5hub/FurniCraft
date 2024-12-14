<?php
defined( 'ABSPATH' ) || exit;

class WOOCS_SESSION {
	protected $_cookie;
	protected $_session_expiring;
	protected $_session_expiration;
	protected $_has_cookie = false;
	protected $_table;
	protected $_dirty = false;
	protected $_data = array();
	protected $_customer_id;
	
	public function __construct() {
		$this->_cookie = apply_filters( 'woocs_cookie', 'wp_woocs_session_' . COOKIEHASH );
		$this->_table  = $GLOBALS['wpdb']->prefix . 'woocommerce_sessions';

	}

	public function init() {
		$this->init_session_cookie();

		add_action( 'shutdown', array( $this, 'save_data' ), 20 );

		$this->set_customer_session_cookie(true);
	}

	public function init_session_cookie() {
		$cookie = $this->get_session_cookie();

		if ( $cookie ) {

			$this->_customer_id        = $cookie[0];
			$this->_session_expiration = $cookie[1];
			$this->_session_expiring   = $cookie[2];
			$this->_has_cookie         = true;
			$this->_data               = $this->get_session_data();

			if ( ! $this->is_session_cookie_valid() ) {
				$this->destroy_session();
				$this->set_session_expiration();
			}

			if ( time() > $this->_session_expiring ) {
				$this->set_session_expiration();
				$this->update_session_timestamp( $this->_customer_id, $this->_session_expiration );
			}
		} else {
			$this->set_session_expiration();
			$this->_customer_id = $this->generate_customer_id();
			$this->_data        = $this->get_session_data();
		}
	}

	private function is_session_cookie_valid() {
		// If session is expired, session cookie is invalid.
		if ( time() > $this->_session_expiration ) {
			return false;
		}

		return true;
	}

	public function set_customer_session_cookie( $set ) {
		if ( $set ) {
			$to_hash           = $this->_customer_id . '|' . $this->_session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, $this->_hash( $to_hash ) );
			$cookie_value      = $this->_customer_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
			$this->_has_cookie = true;

			if ( ! isset( $_COOKIE[ $this->_cookie ] ) || $_COOKIE[ $this->_cookie ] !== $cookie_value ) {
				$this->_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, $this->use_secure_cookie(), true );
			}
		}
	}

	protected function use_secure_cookie() {
		return apply_filters( 'wc_session_use_secure_cookie', (false !== strstr( get_option( 'home' ), 'https:' )) && is_ssl() );
	}

	public function has_session() {
		return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie; // @codingStandardsIgnoreLine.
	}

	public function set_session_expiration() {
		$this->_session_expiring   = time() + intval( apply_filters( 'wc_session_expiring', 60 * 60 * 47 ) ); // 47 Hours.
		$this->_session_expiration = time() + intval( apply_filters( 'wc_session_expiration', 60 * 60 * 48 ) ); // 48 Hours.
	}

	public function generate_customer_id() {
		$customer_id = '';


		if ( empty( $customer_id ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$hasher      = new PasswordHash( 8, false );
			$customer_id = 't_' . substr( md5( $hasher->get_random_bytes( 32 ) ), 2 );
		}

		return $customer_id;
	}

	private function is_customer_guest( $customer_id ) {
		$customer_id = strval( $customer_id );

		if ( empty( $customer_id ) ) {
			return true;
		}

		if ( 't_' === substr( $customer_id, 0, 2 ) ) {
			return true;
		}

		if ( strval( (int) $customer_id ) !== $customer_id ) {
			return true;
		}

		return false;
	}

	public function get_customer_unique_id() {
		$customer_id = '';

		if ( $this->has_session() && $this->_customer_id ) {
			$customer_id = $this->_customer_id;
		} elseif ( is_user_logged_in() ) {
			$customer_id = (string) get_current_user_id();
		}

		return $customer_id;
	}

	public function get_session_cookie() {
		$cookie_value = isset( $_COOKIE[ $this->_cookie ] ) ? wp_unslash( $_COOKIE[ $this->_cookie ] ) : false; // @codingStandardsIgnoreLine.

		if ( empty( $cookie_value ) || ! is_string( $cookie_value ) ) {
			return false;
		}

		list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $cookie_value );

		if ( empty( $customer_id ) ) {
			return false;
		}

		// Validate hash.
		$to_hash = $customer_id . '|' . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, $this->_hash( $to_hash ) );

		if ( empty( $cookie_hash ) || ! hash_equals( $hash, $cookie_hash ) ) {
			return false;
		}

		return array( $customer_id, $session_expiration, $session_expiring, $cookie_hash );
	}

	public function get_session_data() {
		return $this->has_session() ? (array) $this->get_session( $this->_customer_id, array() ) : array();
	}

	private function get_cache_prefix() {
		return "woocs_storage_";
	}
	private function get_cache_group() {
		return "woocs_storage_cache";
	}

	public function save_data( $old_session_key = 0 ) {
		// Dirty if something changed - prevents saving nothing new.
		if ( $this->_dirty && $this->has_session() ) {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}woocommerce_sessions (`session_key`, `session_value`, `session_expiry`) VALUES (%s, %s, %d)
 					ON DUPLICATE KEY UPDATE `session_value` = VALUES(`session_value`), `session_expiry` = VALUES(`session_expiry`)",
					$this->_customer_id,
					maybe_serialize( $this->_data ),
					$this->_session_expiration
				)
			);

			wp_cache_set( $this->get_cache_prefix() . $this->_customer_id, $this->_data, $this->get_cache_group(), $this->_session_expiration - time() );
			$this->_dirty = false;
			//if ( get_current_user_id() != $old_session_key && ! is_object( get_user_by( 'id', $old_session_key ) ) ) {
			//$this->delete_session( $old_session_key );
			//}
		}
	}
	
	public function destroy_session() {
		$this->delete_session( $this->_customer_id );
		$this->forget_session();
	}

	public function forget_session() {
		$this->_setcookie( $this->_cookie, '', time() - YEAR_IN_SECONDS, $this->use_secure_cookie(), true );

		$this->_data        = array();
		$this->_dirty       = false;
		$this->_customer_id = $this->generate_customer_id();
	}


	public function get_session( $customer_id, $default = false ) {
		global $wpdb;

		$value = wp_cache_get( $this->get_cache_prefix() . $customer_id, $this->get_cache_group() );

		if ( false === $value ) {
			$value = $wpdb->get_var( $wpdb->prepare( "SELECT session_value FROM $this->_table WHERE session_key = %s", $customer_id ) ); // @codingStandardsIgnoreLine.

			if ( is_null( $value ) ) {
				$value = $default;
			}

			$cache_duration = $this->_session_expiration - time();
			if ( 0 < $cache_duration ) {
				wp_cache_add( $this->get_cache_prefix() . $customer_id, $value, $this->get_cache_group(), $cache_duration );
			}
		}

		return maybe_unserialize( $value );
	}

	public function delete_session( $customer_id ) {
		global $wpdb;

		wp_cache_delete( $this->get_cache_prefix() . $customer_id, $this->get_cache_group() );

		$wpdb->delete(
			$this->_table,
			array(
				'session_key' => $customer_id,
			)
		);
	}

	public function update_session_timestamp( $customer_id, $timestamp ) {
		global $wpdb;

		$wpdb->update(
			$this->_table,
			array(
				'session_expiry' => $timestamp,
			),
			array(
				'session_key' => $customer_id,
			),
			array(
				'%d',
			)
		);
	}
	public function __get( $key ) {
		return $this->get( $key );
	}

	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	public function __isset( $key ) {
		return isset( $this->_data[ sanitize_title( $key ) ] );
	}

	public function __unset( $key ) {
		if ( isset( $this->_data[ $key ] ) ) {
			unset( $this->_data[ $key ] );
			$this->_dirty = true;
		}
	}

	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );
		return isset( $this->_data[ $key ] ) ? maybe_unserialize( $this->_data[ $key ] ) : $default;
	}

	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$this->_data[ sanitize_key( $key ) ] = maybe_serialize( $value );
			$this->_dirty                        = true;
		}
	}

	public function get_customer_id() {
		return $this->_customer_id;
	}
	private function _hash( $data, $scheme = 'auth' ) {

		return hash_hmac( 'md5', $data, AUTH_KEY);
	}
	private function _setcookie( $name, $value, $expire = 0, $secure = false, $httponly = false ) {

		if ( ! apply_filters( 'woocommerce_set_cookie_enabled', true, $name, $value, $expire, $secure ) ) {
			return;
		}
		if ( ! headers_sent() ) {
			$options = apply_filters(
				'woocommerce_set_cookie_options',
				array(
					'expires'  => $expire,
					'secure'   => $secure,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'httponly' => apply_filters( 'woocommerce_cookie_httponly', $httponly, $name, $value, $expire, $secure ),
				),
				$name,
				$value
			);

			if ( version_compare( PHP_VERSION, '7.3.0', '>=' ) ) {
				setcookie( $name, $value, $options );
			} else {
				setcookie( $name, $value, $options['expires'], $options['path'], $options['domain'], $options['secure'], $options['httponly'] );
			}
		} 
	}	
}

