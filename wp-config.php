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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         'Z4GvARTuuAs8tO-l,t,sB,pXFFA$~vL5W}Zv=p-~T(IeH},Y7CRx0XzL8~FYOgla' );
define( 'SECURE_AUTH_KEY',  't(9Qj2KkP(!`xDqslw4^{E[Nqb`%Q2+C!,BEk9&cgN{-;lre/m3l*[r//lj~+Vus' );
define( 'LOGGED_IN_KEY',    'wpr3pstW*xm9NVg86cxnut4%t<&$l7ZugxCp9FqPZ;YkHScb`5bECeM*bhth>dXs' );
define( 'NONCE_KEY',        '|hKl>&u8pvgHq=epfhv6Qtr7BjYimK-BefO.J7Js2*?B+:xFhR`z%{SaY5+xwe,V' );
define( 'AUTH_SALT',        'W)2+U-3*3,DLW_Uej#o_,fa{i|kp!CA!: ~*AU#[KL3b[c>M^X~|y+;,0JBd9/iF' );
define( 'SECURE_AUTH_SALT', '4%N|!K|0~^0KCVG0Sd6N)%oOz0w%h)Zwe4W!4qJz>f=U*a{)*&`w{U$uR_kc,%o5' );
define( 'LOGGED_IN_SALT',   'm,X5*)x.n!L$,a{T|@H}C:9(l[</N-jceqrq`?F_4xtVnz,y?6mxR1Nir($:h!`(' );
define( 'NONCE_SALT',       'wQIi$.WDMRw&Fn@tw1ezcaMu?;cCD%s@?PL/`r~t  UxuJ+Ic7f[#9FdtIZ|neT4' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
