<?php
define('WP_CACHE', false); // Disabled for local development

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
define( 'DB_NAME', 'earningin_wp703' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         'h6jrl96snkyiq6zgtjsh1uejeoarldqpdrfbft0ljlezssapjeuuwgwukclaemel' );
define( 'SECURE_AUTH_KEY',  'joe57cvydvaawgq08pwa8gv1xecvdgbhhoif4mm6iz1yjzyex0fi7bpjqwstcicj' );
define( 'LOGGED_IN_KEY',    'fmyr97zzytme3r6hkivq2use55mvlph6mbtlua7lihtxyhgv1izsepb5nxdbufai' );
define( 'NONCE_KEY',        '3g9r5w2xhsmy38isamxadbtwa67j25aairycrc7cipsf2ww46ftusleykjtihuoz' );
define( 'AUTH_SALT',        'zg6l92mpbyeutndtva6lrjck3f93sbfdus0t5orqn4pxa0vmblk10b31h30xdy2d' );
define( 'SECURE_AUTH_SALT', 'p35glwi98pwivyob1xfj3m7yj4acstzttoyrnpdjvg2f8p8jzglccd5r1dxputtm' );
define( 'LOGGED_IN_SALT',   'lgybihyptzjwk3nadlmoiyyahzn7ipj4opsjvgzyh4aj6nvmjwsolo4gbdqdp31l' );
define( 'NONCE_SALT',       'btoktn2ko0dz5av9fntpufsfh6cwfoiw7ywgmzqsq5etiockh16lhkvbp3jcig9j' );

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
$table_prefix = 'wpa6_';

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
