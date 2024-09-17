<?php
/**
 * Cache
 *
 * @package Albums Wow
 */

namespace Albums_Wow;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Cache
 */
class Cache {

	/**
	 * Supported posts
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $supported_posts = array( 'albums' );

	/**
	 * Supported terms
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $supported_terms = array( 'albums_single', 'genre' );

	/**
	 * Groups
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $groups = array( '_albums', '_albums_wpdb' );

	/**
	 * Term groups
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $term_groups = array( '_albums_terms' );

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Cache
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Cache
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
		add_action( 'save_post', array( $this, 'flush_cache' ) );

		// When terms created/updated/deleted.
		add_action( 'created_term', array( $this, 'flush_cache' ) );
		add_action( 'edited_term', array( $this, 'flush_cache' ) );
		add_action( 'delete_term', array( $this, 'flush_cache' ) );
	}

	/**
	 * Flush supported posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function flush_cache() {
		if ( in_array( get_post_type(), self::$supported_posts, true ) ) {
			if ( wp_cache_supports( 'flush_group' ) ) {
				foreach ( self::$groups as $group ) {
					// Flush group.
					wp_cache_flush_group( $group );
				}
			} else {
				// Flush all.
				wp_cache_flush();
			}
		}
	}

	/**
	 * Flush supported terms cache
	 *
	 * @since 1.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function flush_terms_cache() {
		if ( in_array( get_queried_object()->taxonomy, self::$supported_terms, true ) ) {
			if ( wp_cache_supports( 'flush_group' ) ) {
				foreach ( self::$term_groups as $group ) {
					// Flush group.
					wp_cache_flush_group( $group );
				}
			} else {
				// Flush all.
				wp_cache_flush();
			}
		}
	}
}
