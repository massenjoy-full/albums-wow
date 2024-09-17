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
 * Class Elementor
 */
class Elementor {

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Elementor
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Elementor
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
		// Register new widget for elementor.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widget' ) );
	}

	/**
	 * Register the album widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager instance.
	 *
	 * @return void
	 */
	public function register_widget( $widgets_manager ) {
		require_once ALBUMS_WOW_DIR . '/includes/elementor/widgets/class-album-widget.php';

		/**
		 * Register the Album widget.
		 *
		 * @since 1.0.0
		 *
		 * @var \Album_Widget $album_widget Elementor album widget instance.
		 */
		$album_widget = new \Album_Widget();

		$widgets_manager->register( $album_widget );
	}
}
