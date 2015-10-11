<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'kidvideo_kids');

/** MySQL database username */
define('DB_USER', 'kidvideo_kids');

/** MySQL database password */
define('DB_PASSWORD', '!04P9(DPST');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'n8ojn42gqgbm2c7aiq28xzsrmrbsd37pssujua2musfp5mocgpfkurt1mwgm5gjl');
define('SECURE_AUTH_KEY',  'zc4efjtujrq26xbpq2ttyhxfiaibrewna7l7iapt9x2c9tuqpdl6tbo36jzq60ju');
define('LOGGED_IN_KEY',    'g9mdj6jxrns0yr1lf2h40qjs6afatj9ze0nagip203p8ruucapadjrv08weu18dn');
define('NONCE_KEY',        'oiqinvnitybn3q3v2pyksw4eomk1ckdzih0flb0d5q5b6fsgq8j3llmd03dj4nhr');
define('AUTH_SALT',        'uoybvf4nbi80ef7yelua3axm0sjm1pxeawpuancxixadosiz0b7e4jo6spimhdr8');
define('SECURE_AUTH_SALT', 'uvvk7wzjxlmb6nbwgy8zkwbgnfmgah5fmqyutcsssdysrlkgc4gbmq10psvglzsp');
define('LOGGED_IN_SALT',   'al9ekdsun6z9y7lrakwmp9bavigokjkhopzir3uygeelcynt5ysm5qicbprtebhp');
define('NONCE_SALT',       'vnd6b47lwsaqegwargz8136ujgdhl3ehlxdbpelpsjn3igcgyk8fehkkg0pxutt5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tu_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
