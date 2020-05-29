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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'academiarego' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'p@ssw0rd12' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'yoKC2qX|{EsniHBNi)m7!?*mMLA_yd5z{.-259^@F1n.QKc:k{|XO*JY^gf595d[' );
define( 'SECURE_AUTH_KEY',  '6dvY+9i9w2)V}jC>_h5REojHDq*N(7= 3AXX6=/}(#Ri/F`zNGmj)<,>d;tD4/|h' );
define( 'LOGGED_IN_KEY',    'l)YmjokjI}>>@jAA>$d_7wUzPJUwW?(tlaEMMsrL7=Rk29Hhnk#ot*$(WZ^Dg t-' );
define( 'NONCE_KEY',        'B]~|gUO,<GIx8gv>Rn60tP2/d8<@F}*28e!:0u[+=ngOx?x._}@SOxs&P,7`TCN]' );
define( 'AUTH_SALT',        '+6oY><6>JZANlsf7fIh6f+(&~v@2c|@L(^n2Q{VZ0H)/>+H;^$rf,4V|0ycMwYC)' );
define( 'SECURE_AUTH_SALT', ' LM8P?Id2iJrD^:^i5/7-?~RHLq-C;@/#8$st[9nZ+fuTy/!3LfPg?}nT37-[w^Y' );
define( 'LOGGED_IN_SALT',   'n|uneoGXV]:PR.&}{; ek_sm]Th)e^/<)mF_Tx#:viPP)MS/{>A<9n B#X-=k?2[' );
define( 'NONCE_SALT',       'aRys}_rfDF<9aF{.tqR90.>Obgd6lX.&MO^?Q*:<.B3OWK~=*yU,[|lb&VKpyK x' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WPMS_ON', true );
define( 'WPMS_SMTP_PASS', 'colanda1' );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
