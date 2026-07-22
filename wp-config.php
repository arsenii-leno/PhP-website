<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/home/a49718/public_html/med.uz.ua/wp-content/plugins/wp-super-cache/' );
define( 'DB_NAME', 'a49718_med-uz-ua' );

/** Користувач бази даних у cPanel */
define( 'DB_USER', 'a49718_cool-admin' );

/** Пароль користувача бази даних */
define( 'DB_PASSWORD', 'bW7mF9tnJ5feL3qlDytV2noIcOkp6j' );

/** Сервер бази даних */
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
define( 'AUTH_KEY',          ':([k<`,^,i-GUZ 1EQt?,A*34qNm(Q3/q?9^$sU_Mhn]/z6|WGeBT>9?LWN_x#u@' );
define( 'SECURE_AUTH_KEY',   '/rItfrc_+$,Bp};XmHHywjKMFds]R+,Q,W)m=!*UxhUl~KdGS2-`?^(7DR#=ea)l' );
define( 'LOGGED_IN_KEY',     '-bS]y[2S>F K9YTg:5Y87RJ!oY.TlM(&}Lr]HP%%RJXLV{<},5SL3<(+lI;N^#;&' );
define( 'NONCE_KEY',         'Pw)mtTxx]jL{v>K^C}p]R>/AhtPB~Kp7ND@@;/N&sKg-[|m~Z8|WxbU5$ {YcqwK' );
define( 'AUTH_SALT',         'F|z|%6g6x$Ew1y}8a.)HM9IdwcO=%`(8@D=Oc9%_obdSv{iLI6ymUlJjfWPn,jio' );
define( 'SECURE_AUTH_SALT',  '6Y0::S.Vz>tr)M.tPHkI7T+bt}=>#cAQ/*BK}|h)%uQy.CWyP^=`BN0O^/CS2#,N' );
define( 'LOGGED_IN_SALT',    '!c4A2(K=cC)oj1 M9G?Fh=D:;!^ |+s_0y`UwoC@kgxQM?:g{D ld4otq(p/E)DR' );
define( 'NONCE_SALT',        '91uZwE[2UM9oY% GU1wC!5L4yK49:Q%slEv=2`&y}o fP.R)NP#z){.f7qo10RXY' );
define( 'WP_CACHE_KEY_SALT', '!9hk?^n]?_perG/c$/7qwtsbjK.k3Kd9?{mTJoY4e8SVK5RMjD~slHWgMnIEPI7~' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpj4_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */


/* Налаштування середовища та URL */
define( 'WP_ENVIRONMENT_TYPE', 'production' );
define( 'WP_HOME', 'https://med.uz.ua' );
define( 'WP_SITEURL', 'https://med.uz.ua' );

define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/* That's all, stop editing! Happy publishing. */