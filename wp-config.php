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
define('DB_NAME', 'test_work');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123321');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '|?^M*g7toY(B2p0^#;e|^Qwf uiA#Efn yO2,g&PS6X)>jO@42F1}w4GqgZF..Xd');
define('SECURE_AUTH_KEY',  'VVcEMl!CElj4X@0z/#0m{~TbohAD8G+I6,Mb@^V~r//N8QaZ=m_2w]b~YwQ0qM3H');
define('LOGGED_IN_KEY',    '?;kEQnTlYNVOThc!]cGQN-A=q}w3k7lIX_hLWJrn>bA.(}LpA5)nQ!cj!CP50Zqg');
define('NONCE_KEY',        'hr_3EmB|BVb`$/^8K)9@>m*/43n~H#tG+e/MkC5u##eUL0, !Kevf;8m8YnMzHVA');
define('AUTH_SALT',        'Y&K,:%E:XCab5#wx`~]5yB5EPG[kYH/0|DqSV8nMGv^Xo75AR6esC/e_8?5py7xM');
define('SECURE_AUTH_SALT', 'm)8}L|R;C&o6Sp~d9qa?-R;@|t5#7SW7I71Z11g|E]Liu1z3!myhh(4,0-`CDkg*');
define('LOGGED_IN_SALT',   'Is|H6*H#-oHN9)9m/X4=jJ)Xo^5lX7Z!0K/ld{5q0|0]`2L]}fFv6u@Ua<&-NxC9');
define('NONCE_SALT',       ',.A_m<iZRMy?IH}?`~2?CA4>G?;XYo5bm([SE^k`<;D(}_%Hg#, H^=YchzWmAeW');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('FS_METHOD','direct');
define('DISALLOW_FILE_EDIT', true);
define('WP_POST_REVISIONS', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
