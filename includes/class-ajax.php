<?php
/**
 * Ajax
 *
 * @package Albums-Wow
 */

namespace Albums_Wow;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Ajax
 */
class Ajax {
	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Ajax
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Ajax
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
		add_action( 'wp_ajax_albums_wow_get_albums', array( $this, 'get_albums' ) );
		add_action( 'wp_ajax_nopriv_albums_wow_get_albums', array( $this, 'get_albums' ) );

		add_action( 'wp_ajax_albums_wow_get_albums_wpdb', array( $this, 'get_albums_wpdb' ) );
		add_action( 'wp_ajax_nopriv_albums_wow_get_albums_wpdb', array( $this, 'get_albums_wpdb' ) );
	}

	/**
	 * Get albums
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_albums() {
		$albums = Helper::get_instance()->get_albums_html();
		wp_send_json( array( 'html' => $albums ) );
	}

	/**
	 * Get albums with wpdb
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_albums_wpdb() {
		$albums = Helper::get_instance()->get_albums_wpdb_html();
		wp_send_json( array( 'html' => $albums ) );
	}
}
