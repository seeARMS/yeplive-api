<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'yeplive' );

/** MySQL database username */
define( 'DB_USER', '***' );//confidential information

/** MySQL database password */
define( 'DB_PASSWORD', '***' );//confidential information

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('DOMAIN_ROOT', 'http://yeplive.fora-soft.com/');

define('SERVER_ADDRESS', 'yeplive.fora-soft.com');
define('HTTP_WOWZA', 'http://'.SERVER_ADDRESS.':1935/');
define('RTMP_WOWZA', 'rtmp://'.SERVER_ADDRESS.':1935/');
define('WOWZA_CONTENT_PATH', '/wowza/content/');
define('SERVER_CONTENT_PATH', '/var/www/yeplive/wordpress/wp-content/content/');
define('SERVER_BASH_SCRIPTS_PATH', '/var/www/yeplive/wordpress/wp-content/scripts/');
define('THUMBNAILS_PATH', '/var/www/yeplive/wordpress/wp-content/uploads/thumbnails/');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

// for twitter SDK
define('CONSUMER_KEY', 'ruLHhRe2wB6Jl7P3Lm4zg');
define('CONSUMER_SECRET', 'Q3Eca0SenYeICfF9PIdUQg6cErVwzQiVN1wbMVM');
define('OAUTH_CALLBACK', DOMAIN_ROOT.'twitter_notify');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
