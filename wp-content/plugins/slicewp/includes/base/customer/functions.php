<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Includes the files needed for the customers.
 *
 */
function slicewp_include_files_customer() {

	// Get customer dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main customer class
	if ( file_exists( $dir_path . 'class-customer.php' ) ) {
		include $dir_path . 'class-customer.php';
	}

	// Include the db layer classes
	if ( file_exists( $dir_path . 'class-object-db-customers.php' ) ) {
		include $dir_path . 'class-object-db-customers.php';
	}

	if ( file_exists( $dir_path . 'class-object-meta-db-customers.php' ) ) {
		include $dir_path . 'class-object-meta-db-customers.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_customer' );


/**
 * Register the class that handles database queries for the customers.
 *
 * @param array $classes
 *
 * @return array
 *
 */
function slicewp_register_database_classes_customers( $classes ) {

	$classes['customers']    = 'SliceWP_Object_DB_Customers';
	$classes['customermeta'] = 'SliceWP_Object_Meta_DB_Customers';

	return $classes;

}
add_filter( 'slicewp_register_database_classes', 'slicewp_register_database_classes_customers' );


/**
 * Returns an array with SliceWP_Customer objects from the database.
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function slicewp_get_customers( $args = array(), $count = false ) {

	$customers = slicewp()->db['customers']->get_customers( $args, $count );

	/**
	 * Add a filter hook just before returning
	 *
	 * @param array $customers
	 * @param array $args
	 * @param bool  $count
	 *
	 */
	return apply_filters( 'slicewp_get_customers', $customers, $args, $count );

}


/**
 * Gets a customer from the database.
 *
 * @param mixed int|object      - customer id or object representing the customer
 *
 * @return SliceWP_Customer|null
 *
 */
function slicewp_get_customer( $customer ) {

	return slicewp()->db['customers']->get_object( $customer );

}


/**
 * Returns a customer from the database based on the given user_id.
 *
 * @param int $user_id
 *
 * @return SliceWP_Customer|null
 *
 */
function slicewp_get_customer_by_user_id( $user_id ) {

	if ( empty( $user_id ) ) {
		return null;
	}

	$customers = slicewp_get_customers( array( 'user_id' => $user_id ) );

	if ( empty( $customers ) ) {
		return null;
	}

	if ( ! $customers[0] instanceof SliceWP_Customer ) {
		return null;
	}

	return $customers[0];

}


/**
 * Returns a customer from the database based on the given email.
 *
 * @param string $email
 *
 * @return SliceWP_Customer|null
 *
 */
function slicewp_get_customer_by_email( $email ) {

	if ( empty( $email ) ) {
		return null;
	}

	$customers = slicewp_get_customers( array( 'email' => $email ) );

	if ( empty( $customers ) ) {
		return null;
	}

	if ( ! $customers[0] instanceof SliceWP_Customer ) {
		return null;
	}

	return $customers[0];

}


/**
 * Inserts a new customer into the database.
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function slicewp_insert_customer( $data ) {

	return slicewp()->db['customers']->insert( $data );

}


/**
 * Updates a customer from the database.
 *
 * @param int 	$customer_id
 * @param array $data
 *
 * @return bool
 *
 */
function slicewp_update_customer( $customer_id, $data ) {

	return slicewp()->db['customers']->update( $customer_id, $data );

}


/**
 * Deletes a customer from the database.
 *
 * @param int $customer_id
 *
 * @return bool
 *
 */
function slicewp_delete_customer( $customer_id ) {

	$customer = slicewp_get_customer( $customer_id );

	if ( is_null( $customer ) ) {
		return false;
	}

	$deleted = slicewp()->db['customers']->delete( $customer_id );

	if ( ! $deleted ) {
		return false;
	}

	// Delete the customer's metadata.
	$customer_meta = slicewp_get_customer_meta( $customer_id );

	if ( ! empty( $customer_meta ) ) {

		foreach( $customer_meta as $key => $value ) {

			slicewp_delete_customer_meta( $customer_id, $key );

		}

	}

	return true;

}

/**
 * Inserts a new meta entry for the customer.
 *
 * @param int    $customer_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function slicewp_add_customer_meta( $customer_id, $meta_key, $meta_value, $unique = false ) {

	return slicewp()->db['customermeta']->add( $customer_id, $meta_key, $meta_value, $unique );

}

/**
 * Updates a meta entry for the customer.
 *
 * @param int    $customer_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function slicewp_update_customer_meta( $customer_id, $meta_key, $meta_value, $prev_value = '' ) {

	return slicewp()->db['customermeta']->update( $customer_id, $meta_key, $meta_value, $prev_value );

}

/**
 * Returns a meta entry for the customer.
 *
 * @param int    $customer_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function slicewp_get_customer_meta( $customer_id, $meta_key = '', $single = false ) {

	return slicewp()->db['customermeta']->get( $customer_id, $meta_key, $single );

}

/**
 * Removes a meta entry for the customer.
 *
 * @param int    $customer_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function slicewp_delete_customer_meta( $customer_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return slicewp()->db['customermeta']->delete( $customer_id, $meta_key, $meta_value, $delete_all );

}


/**
 * Adds/updates the customer in the database.
 *
 * @param array $args
 *
 * @return int
 *
 */
function slicewp_process_customer( $args = array() ) {

	$customer_id   = 0;
	$customer_data = array();

	$affiliate_id  = ( ! empty( $args['affiliate_id'] ) ? absint( $args['affiliate_id'] ) : 0 );

	if ( empty( $args['email'] ) ) {
		return $customer_id;
	}

	// Set up customer data.
	if ( ! empty( $args['first_name'] ) ) {
		$customer_data['first_name'] = sanitize_text_field( $args['first_name'] );
	}

	if ( ! empty( $args['last_name'] ) ) {
		$customer_data['last_name'] = sanitize_text_field( $args['last_name'] );
	}


	/**
	 * Handle cases when the user ID is provided.
	 *
	 */
	if ( ! empty( $args['user_id'] ) ) {

		$customer_by_user_id = slicewp_get_customer_by_user_id( absint( $args['user_id'] ) );

		/**
		 * Handle the case when a customer with the provided user ID exists.
		 *
		 */
		if ( ! is_null( $customer_by_user_id ) ) {

			$customer_data = array_merge( $customer_data, array(
				'email' 		=> sanitize_email( $args['email'] ),
				'date_modified' => slicewp_mysql_gmdate()
			));

			slicewp_update_customer( $customer_by_user_id->get( 'id' ), $customer_data );

			$customer_id = $customer_by_user_id->get( 'id' );

		}

		/**
		 * Handle the case when a customer with the provided user ID does not exist.
		 *
		 */
		if ( is_null( $customer_by_user_id ) ) {

			$customer_by_email = slicewp_get_customer_by_email( sanitize_email( $args['email'] ) );

			// If a customer hasn't been found by either email or user ID, add them to the DB.
			if ( is_null( $customer_by_email ) ) {

				$customer_data = array_merge( $customer_data, array(
					'user_id' 		=> absint( $args['user_id'] ),
					'email'			=> sanitize_email( $args['email'] ),
					'affiliate_id'	=> $affiliate_id,
					'date_created'  => slicewp_mysql_gmdate(),
					'date_modified' => slicewp_mysql_gmdate()
				));

				$customer_id = slicewp_insert_customer( $customer_data );

			}

			// We have found a customer by email address.
			if ( ! is_null( $customer_by_email ) ) {

				$customer_id = $customer_by_email->get( 'id' );

				// If the customer doesn't have a user ID attached, attach the one provided.
				if ( empty( $customer_by_email->get( 'user_id' ) ) ) {

					$customer_data = array_merge( $customer_data, array(
						'user_id' 		=> absint( $args['user_id'] ),
						'date_modified' => slicewp_mysql_gmdate()
					));

					slicewp_update_customer( $customer_by_email->get( 'id' ), $customer_data );

				}

				// If the customer has a user ID attached, check to see if it's the same with the given one.
				// If it's not, add a new customer.
				if ( ! empty( $customer_by_email->get( 'user_id' ) ) ) {

					if ( $customer_by_email->get( 'user_id' ) != $args['user_id'] ) {

						$customer_data = array_merge( $customer_data, array(
							'user_id' 		=> absint( $args['user_id'] ),
							'email'			=> sanitize_email( $args['email'] ),
							'affiliate_id'	=> $affiliate_id,
							'date_created'  => slicewp_mysql_gmdate(),
							'date_modified' => slicewp_mysql_gmdate()
						));

						$customer_id = slicewp_insert_customer( $customer_data );

					}

				}
				
			}

		}

	}


	/**
	 * Handle cases when the user ID is not provided.
	 *
	 */
	if ( empty( $args['user_id'] ) ) {

		$customer_by_email = slicewp_get_customer_by_email( sanitize_email( $args['email'] ) );

		/**
		 * Handle the case when a customer with the provided email address exists.
		 * In this particular case, we just set the $customer_id variable that will be returned.
		 *
		 */
		if ( ! is_null( $customer_by_email ) ) {

			$customer_id = $customer_by_email->get( 'id' );

		}

		/**
		 * Handle the case when a customer with the provided email address does not exist.
		 *
		 */
		if ( is_null( $customer_by_email ) ) {

			// Try to get customer from an existing user
			$user 				 = get_user_by( 'email', sanitize_email( $args['email'] ) );
			$customer_by_user_id = ( $user ? slicewp_get_customer_by_user_id( $user->ID ) : null );

			// If a customer hasn't been found by either email or user ID, add them to the DB.
			if ( is_null( $customer_by_user_id ) ) {

				$customer_data = array_merge( $customer_data, array(
					'user_id'		=> ( $user ? $user->ID : 0 ),
					'email'			=> sanitize_email( $args['email'] ),
					'affiliate_id'	=> $affiliate_id,
					'date_created'  => slicewp_mysql_gmdate(),
					'date_modified' => slicewp_mysql_gmdate()
				));

				$customer_id = slicewp_insert_customer( $customer_data );

			}

			// If a customer is found by user ID, update their email address
			if ( ! is_null( $customer_by_user_id ) ) {

				$customer_data = array_merge( $customer_data, array(
					'email' 		=> sanitize_email( $args['email'] ),
					'date_modified' => slicewp_mysql_gmdate()
				));

				slicewp_update_customer( $customer_by_user_id->get( 'id' ), $customer_data );

				$customer_id = $customer_by_user_id->get( 'id' );

			}

		}

	}

	return absint( $customer_id );

}


/**
 * Updates the customer email on user update.
 *
 * @param int		$user_id
 * @param WP_User	$old_user_data
 *
 */
function slicewp_profile_update_customer( $user_id, $old_user_data ) {

	// Search the customer
	$customer = slicewp_get_customer_by_user_id( $user_id );

	// Check if the customer was found
	if ( empty( $customer ) ) {
		return;
	}
	
	// Get user
	$user = get_userdata( $user_id );

	// Exit if data is identical
	if ( $customer->get( 'email' ) == $user->get( 'user_email' ) && $customer->get( 'first_name' ) == $user->get( 'first_name' ) && $customer->get( 'last_name' ) == $user->get( 'last_name' ) ) {
		return;
	}

	// Prepare customer data
	$customer_data = array(
		'date_modified' => slicewp_mysql_gmdate(),
		'email'			=> sanitize_email( $user->get( 'user_email' ) ),
		'first_name'	=> sanitize_text_field( $user->get( 'first_name' ) ),
		'last_name'		=> sanitize_text_field( $user->get( 'last_name' ) )
	);

	// Update the customer email with the new one
	slicewp_update_customer( $customer->get( 'id' ), $customer_data );

}
add_action( 'profile_update', 'slicewp_profile_update_customer', 10, 2 );


/**
 * Process the customer on user registration.
 *
 * @param int $user_id
 * 
 */
function slicewp_user_register_process_customer( $user_id ) {

	// Get user
	$user = get_userdata( $user_id );

	// Check if we already have a customer with the user email address
	$customer = slicewp_get_customer_by_email( $user->get( 'user_email' ) );

	// Process the customer if already exists in customers table
	if ( ! empty( $customer ) ) {

		$customer_args = array(
			'user_id' 	 => $user_id,
			'email' 	 => $user->get( 'user_email' ),
			'first_name' => $user->get( 'first_name' ),
			'last_name'  => $user->get( 'last_name' )
		);

		slicewp_process_customer( $customer_args );

	}

}
add_action( 'user_register', 'slicewp_user_register_process_customer', 50 );


/**
 * Deletes the customer when a user is deleted.
 * 
 * @param int $user_id
 * 
 */
function slicewp_deleted_user_delete_customer( $user_id ) {

	$customer = slicewp_get_customer_by_user_id( $user_id );

	if ( is_null( $customer ) ) {
		return;
	}

	slicewp_delete_customer( $customer->get( 'id' ) );

}
add_action( 'deleted_user', 'slicewp_deleted_user_delete_customer', 50 );