<?php
/**
 * Shortcodes
 *
 * @package Albums-Wow
 */

namespace Albums_Wow;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Albums_WoW_Shortcodes
 */
class Shortcodes {
	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Shortcodes
	 */
	protected static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Shortcodes
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
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		$this->register_shortcodes();
	}

	/**
	 * Register shortcodes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		add_shortcode( 'albums_wow_lists', array( $this, 'lists' ) );
		add_shortcode( 'albums_wow_lists_wpdb', array( $this, 'lists_wpdb' ) );
	}

	/**
	 * Show albums list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function lists( $atts ) {
		$atts = shortcode_atts(
			Helper::get_instance()->get_albums_default_args(),
			$atts
		);

		ob_start();

		// Get filters.
		if ( ! isset( $atts['hide_filters'] ) || false === $atts['hide_filters'] ) {
			Helper::get_instance()->get_the_filters_html();
		}

		echo '<div id="albums">';

		// Get albums.
		echo Helper::get_instance()->get_albums_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped end #albums.

		return ob_get_clean();
	}

	/**
	 * Show albums list using WPDB
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function lists_wpdb( $atts ) {
		$atts = shortcode_atts(
			array(
				'query'        => '',
				'hide_filters' => false,
			),
			$atts,
		);

		if ( ! $atts['hide_filters'] ) {
			Helper::get_instance()->get_the_filters_html();
		}

		// Get posts.
		$posts = Helper::get_instance()->get_albums_wpdb_posts( $atts['query'] );

		if ( ! $posts ) {
			return '';
		}

		return '<div id="albums" class="wpdb-albums">' . Helper::get_instance()->get_albums_wpdb_html( $posts ) . '</div>';
	}
}
