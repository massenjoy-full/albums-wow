<?php
/**
 * Includes
 *
 * @package Albums-Wow
 */

namespace Albums_Wow;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Includes
 */
class Includes {
	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Includes
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Includes
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		// Register shortcodes.
		Shortcodes::get_instance();

		// Register posts.
		Posts::get_instance();

		// Register cache.
		if ( is_admin() ) {
			Cache::get_instance();
		}

		// If Elementor is active.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			Elementor::get_instance();
		}

		// Ajax.
		if ( isset( $_REQUEST['action'] ) && str_contains( $_REQUEST['action'], 'albums_wow' ) ) { // phpcs:ignore
			Ajax::get_instance();
		}
	}

	/**
	 * Enqueue scripts and styles for filters.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function filters_scripts() {
		$min = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
		if ( ! is_admin() ) {
			wp_enqueue_script(
				'albums-wow-filters',
				ALBUMS_WOW_URL . 'assets/js/filters' . $min . '.js',
				array( 'jquery' ),
				ALBUMS_WOW_VERSION,
				true
			);

			wp_enqueue_style(
				'albums-wow-filters',
				ALBUMS_WOW_URL . 'assets/css/filters' . $min . '.css',
				array(),
				ALBUMS_WOW_VERSION
			);

			wp_localize_script( 'albums-wow-filters', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		}
	}
}

/**
 * Get instance
 */
if ( ! function_exists( 'albums_wow_includes' ) ) {
	Includes::get_instance();
}
