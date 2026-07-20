<?php
// temporary debug settings for local development
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);

define('WP_CACHE', true); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * @package WordPress
 */

// ** MySQL settings ** //
define( 'DB_NAME', 'local' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost' );
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// memory define
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// local site
define( 'WP_HOME', 'http://testmeduzua.local' );
define( 'WP_SITEURL', 'http://testmeduzua.local' );

/**#@+
 * Authentication Unique Keys and Salts.
 * Change these to different unique phrases when deploying to production!
 */
define('AUTH_KEY',         'xdxexc0l8cbwt8onenkcgu6umrbuo4axjv96nsvp58dmzgfelvfuwubggqhjnfdd');
define('SECURE_AUTH_KEY',  'koy3yldfaktll3sswtucr37wx2u8gnyaxz1rbun8pub026uzyhrjcnst6mduexv7');
define('LOGGED_IN_KEY',    'uu1sdrtozomklwnep26wctwynunduz0wtzarrsyhbvdny8sjrkwxhsw9s4ugzowj');
define('NONCE_KEY',        'lssb4r0f7yb51gvaxow9smix5qwkp4kxfkmbdqutgar7nprtwwubdlchzgwx5tut');
define('AUTH_SALT',        'wvwx7mbopcjn1neq25hbfjvh5b3rxqw8skrxo9suzhhy8r0ovijadjwo85pglsan');
define('SECURE_AUTH_SALT', 'kibxcrk9kkvwxalrqyve5z2fbcxouclewoqcudgtwt2tcm4atcuxdqna0j8ly5n3');
define('LOGGED_IN_SALT',   'gdunw2kxhvdfac2vaazgwu53qdon7plhnobfgqhqwkdlrjiedmwcokpetvywmjm3');
define('NONCE_SALT',       'qddba2zclxcyjklssjoba1ln155fsbxhmgn0qfimsdnwx5gwqd8vosy6anvtfond');
/**#@-*/

/**
 * WordPress Database Table prefix.
 */
$table_prefix  = 'wpj4_';

/**
 * WordPress Debugging Mode.
 * Errors are safely logged to wp-content/debug.log instead of being displayed on screen.
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

/**
 * Optimization Settings.
 */
define('WP_POST_REVISIONS', 5); // Limit page revisions to 5 (prevents DB bloat)
define('EMPTY_TRASH_DAYS', 14);  // Automatically empty trash after 14 days

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
