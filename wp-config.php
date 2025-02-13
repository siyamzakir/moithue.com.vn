<?php

require_once __DIR__ . '/main-config.php';

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
 

/** The home URL */
define('WP_HOME', WP_HOME);

/** The site URL */
define('WP_SITEURL', WP_SITEURL);

/** The name of the database for WordPress */
define('DB_NAME', DATABASE_CONFIGURATION['database']);

/** Database username */
define('DB_USER', DATABASE_CONFIGURATION['username']);

/** Database password */
define('DB_PASSWORD', DATABASE_CONFIGURATION['password']);

/** Database hostname */
define('DB_HOST', DATABASE_CONFIGURATION['host']);

/** Database charset to use in creating database tables. */
define('DB_CHARSET', DATABASE_CONFIGURATION['charset']);

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', DATABASE_CONFIGURATION['collation']);

/**
 * Debug settings
 * when PRODUCTION is true, WP_DEBUG is false
 */
if (!defined('WP_DEBUG')) define('WP_DEBUG', false);
if(!PRODUCTION && WP_DEBUG):
	define('WP_DEBUG_LOG', true);
	define('WP_DEBUG_DISPLAY', false);
	@ini_set('log_errors', 1);
	@ini_set('display_errors', 1);
endif;


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
define('AUTH_KEY',          '/ 6#EDFqq;H7zg%^h=U}y#:QYELNWLeo&wD$`Flap7(n%_NM(~arlb9ZS}*)[76W');
define('SECURE_AUTH_KEY',   'qQBY/je4gEZycg6sJ,9,9>ULGW`[R,-HkdNWmkORn?1OfO;=}|ZeS|zQ@sr_GIkZ');
define('LOGGED_IN_KEY',     'VviEkE(2,(&DhOpsS[8/QUgXiQaq=LqF/O<E]OiCX%{ZeYR.COZ 0Je),#kh6~h_');
define('NONCE_KEY',         '$+KP%d>6bE1z)nv.o8C4MR~f(H.^q/g_a`0~ql=Smo#s,W@Q,eUEzh*<l[<x{F+#');
define('AUTH_SALT',         'x[)Wl,n#fL]1k&[}Yv$#.by6k}?oRy;YH!f]Rx;cgs-|~hpSOS#9SGV*^T]yKdJ_');
define('SECURE_AUTH_SALT',  '](d-.:2iBXk:D11p}bdvlN0sacBraF7@-Gf6nr5xpGqx1SiQY_ZHq<=R)}r=_p~T');
define('LOGGED_IN_SALT',    'mq+`N6[LZr-(j|/..hx}R*2%gd,NrM@2+h%*8+E+n<BD;I2%vT`P8eBzX+@?nQV3');
define('NONCE_SALT',        'Q2I1_rNQx) ]IWzoi*Loj]g,RsFs5]T/Q=u&lfz?32keboI9&HbU:W<un_qP@ gM');
define('WP_CACHE_KEY_SALT', '5>/Az N^Bhk?Lj NMfo+=h:%2g^@v<5OPYsAb=>]RG>sn4iL@]}Bu<zO|~o,m>?5');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 * $P$BCwDbAMsKOUz5dxhP.uhb2bo/RfbFD0
 * $P$BHnHNAlnR98i7HE03rzqXE.UZYWIGU/
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * 
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */


define('FS_METHOD', 'direct');
define('COOKIEHASH', '7b179b98225d972b6be82dc6f80e969c');
define('WP_AUTO_UPDATE_CORE', 'minor');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

define('DB_PATH', ABSPATH . 'modules/DB.php');
define('LOG_PATH', ABSPATH . 'logs/log.php');

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

// Include the DB and LOG modules
require_once DB_PATH;
require_once LOG_PATH;


