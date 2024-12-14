<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '53pvxjMn0!`&>FjpS|F7Fkw};fG5tPkg8_B{Uo8F|J;m_T!<a1!u0jQ>o2rG+ ^B' );
define( 'SECURE_AUTH_KEY',  'a$>(itAs~o+ea)l[KV{H+Za*(!@&{W 5#:^$suZ;P`wQkEN!<ol^QWj(w00E|U@g' );
define( 'LOGGED_IN_KEY',    'wfL%#8lzSvlN5^g:smi]5%^I]nC0 .9?9yN]YC(..`acn=s#W{y.pv:X3!LoO|-!' );
define( 'NONCE_KEY',        '99LByt/C+3v_sk30BG]1g`(Y1hqjD-#^Xc~m$4n8VUa645-7sZSw]0^}I^5/JzG#' );
define( 'AUTH_SALT',        'k*.&#|=i[[SUuow7*Kt+A$V6466K+YE~W7DJqv81Oc}H}K62RBng$jR2j,2y5T]+' );
define( 'SECURE_AUTH_SALT', 'I>]Jxt1jiuhW o<Un?mm/<oH{_(FO:s%G<=nIc}=C):pViG!^G-X{c!~S.lkP^;}' );
define( 'LOGGED_IN_SALT',   '}> *E|W_:)1OW!9^oDR>gVNJBU<t~ZXi{@em.g(KNQOfW;?z}>Cm*3^!V_TKp=p]' );
define( 'NONCE_SALT',       'Rfmh_}`D]v8<78`KYKO_D7/fNfD~mKEq{zP9gc`3IW)pxU2LQs-?tBA.K1/Ja ks' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
