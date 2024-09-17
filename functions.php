<?php
/**
 * Theme functions and definitions
 *
 * @package Albums-WoW
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALBUMS_WOW_VERSION', '1.0.0' );
define( 'ALBUMS_WOW_URL', get_stylesheet_directory_uri() . '/' );
define( 'ALBUMS_WOW_DIR', get_stylesheet_directory() . '/' );

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function albums_wow_enqueue_styles() {
	$min = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

	wp_enqueue_style(
		'albums-wow-style',
		ALBUMS_WOW_URL . 'assets/css/style' . $min . '.css',
		array(),
		ALBUMS_WOW_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'albums_wow_enqueue_styles' );

/**
 * Setup theme.
 *
 * @return void
 */
function albums_wow_setup() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'albums-wow' ),
		)
	);

	// Thumbnail support.
	add_theme_support( 'post-thumbnails' );

	// Albums image sizes.
	add_image_size( 'album_medium', 1200, 800, true );
	add_image_size( 'album_small', 600, 400, true );
}
add_action( 'after_setup_theme', 'albums_wow_setup' );

if ( ! function_exists( 'write_log' ) ) {
	/**
	 * Write log
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $data Data.
	 * @return void
	 */
	function write_log( $data ) {
		if ( WP_DEBUG ) {
			if ( is_array( $data ) || is_object( $data ) ) {
				error_log( print_r( $data, true ) );
			} else {
				error_log( $data );
			}
		}
	}
}

/**
 * Autoload classes.
 */
spl_autoload_register(
	function ( $class_name ) {
		if ( strpos( $class_name, 'Albums_Wow\\' ) === 0 ) {
			$class_name = str_replace( 'Albums_Wow\\', '', $class_name );

			// Get the name of the file from the class name.
			$file = get_template_directory() . '/includes/class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

			// Check if the file exists.
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	}
);

require_once ALBUMS_WOW_DIR . 'includes/class-includes.php';
