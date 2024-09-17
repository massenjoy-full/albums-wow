<?php
/**
 * Helper
 *
 * @package Albums Wow
 */

namespace Albums_Wow;

use WP_Query;
use WP_Post;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Helper
 */
class Helper {

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Helper
	 */
	private static $instance = null;

	/**
	 * Default args for get_albums().
	 *
	 * @var array
	 */
	private static $default_args = array();

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Helper
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
		self::$default_args = array(
			'posts_per_page' => get_option( 'posts_per_page' ),
			'orderby'        => 'date',
			'order'          => 'desc',
			'single'         => '',
			'genre'          => '',
			'hide_filters'   => false,
		);
	}

	/**
	 * Get default args for get_albums().
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_albums_default_args() {
		return self::$default_args;
	}

	/**
	 * Get albums.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $atts Arguments.
	 *
	 * @return WP_Query|false Query of album objects.
	 */
	public function get_albums( $atts = array() ) {

		$atts = shortcode_atts(
			self::get_instance()->get_albums_default_args(),
			$atts
		);

		$args = array(
			'posts_per_page' => $atts['posts_per_page'],
			'post_type'      => 'album',
			'orderby'        => $atts['orderby'],
			'order'          => $atts['order'],
		);

		// Set filters via REQUEST.
		$args = self::get_instance()->set_filters_url( $args );

		// Single.
		if ( ! empty( $atts['single'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'albums_single',
					'field'    => 'slug',
					'terms'    => explode( ',', $atts['single'] ),
					'operator' => 'IN',
				),
			);
		}

		// Genre.
		if ( ! empty( $atts['genre'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'album_genre',
					'field'    => 'slug',
					'terms'    => explode( ',', $atts['genre'] ),
					'operator' => 'IN',
				),
			);
		}

		// Get albums.
		$cache_key = 'albums_wow_albums_' . md5( wp_json_encode( $args ) );
		$cache     = wp_cache_get( $cache_key, '_albums' );

		// Get albums from cache.
		if ( $cache ) {
			return $cache;
		}

		// Get albums from database.
		$posts = new WP_Query( $args );

		wp_cache_set( $cache_key, $posts, '_albums' );

		return $posts;
	}

	/**
	 * Set filters URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Query arguments.
	 *
	 * @return array Query arguments with filters applied.
	 */
	public function set_filters_url( $args = array() ) {
		// Genre.
		if ( isset( $_REQUEST['aw_genres'] ) ) {
			$args['genre'] = sanitize_text_field( $_REQUEST['aw_genres'] );
		}

		// single.
		if ( isset( $_REQUEST['aw_singles'] ) && ! empty( $_REQUEST['aw_singles'] ) ) {
			$args['single'] = sanitize_text_field( $_REQUEST['aw_singles'] );
		}

		return $args;
	}

	/**
	 * Apply filters to the albums query using wpdb.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $query_args Query arguments.
	 *
	 * @return false|string Query string with filters applied.
	 */
	public function albums_filters_wpdb( $query_args = array() ): string {
		if ( isset( $_REQUEST['aw_genres'] ) || isset( $_REQUEST['aw_singles'] ) ) {
			$query = self::get_instance()->set_filters_wpdb_url();
		} else {
			$query = self::get_instance()->set_filters_wpdb( $query_args );
		}

		return $query;
	}

	/**
	 * Apply filters to the albums query using wpdb.
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $wpdb
	 *
	 * @param array $query_args Query arguments.
	 *
	 * @return string Query string with filters applied.
	 */
	public function set_filters_wpdb( $query_args = array() ) {
		global $wpdb;
		$query = '';

		if ( isset( $query_args['single'] ) ) {
			$single = sanitize_text_field( $query_args['single'] );

			// single.
			$query .= $wpdb->prepare(
				"
				AND ID IN (
					SELECT object_id FROM {$wpdb->term_relationships} 
					WHERE term_taxonomy_id IN (
						SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
						WHERE taxonomy = 'albums_single' 
						AND term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE slug = %s)
					)
				)
				",
				$single
			);
		}

		if ( isset( $query_args['genre'] ) ) {
			$genre = sanitize_text_field( $query_args['genre'] );

			// Genre.
			$query .= $wpdb->prepare(
				"
				AND ID IN (
					SELECT object_id FROM {$wpdb->term_relationships} 
					WHERE term_taxonomy_id IN (
						SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
						WHERE taxonomy = 'genre' 
						AND term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE slug = %s)
					)
				)
				",
				$genre
			);
		}

		return $query;
	}

	/**
	 * Set filters for WP_Query URL.
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $wpdb
	 *
	 * @return string
	 */
	public function set_filters_wpdb_url() {
		global $wpdb;
		$query = '';

		// Singles.
		if ( isset( $_REQUEST['aw_singles'] ) && ! empty( $_REQUEST['aw_singles'] ) ) {
			$single = sanitize_text_field( $_REQUEST['aw_singles'] );

			// single.
			$query .= $wpdb->prepare(
				"
				AND ID IN (
					SELECT object_id FROM {$wpdb->term_relationships} 
					WHERE term_taxonomy_id IN (
						SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
						WHERE taxonomy = 'albums_single' 
						AND term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE slug = %s)
					)
				)
				",
				$single
			);
		}

		// Genre.
		if ( isset( $_REQUEST['aw_genres'] ) && ! empty( $_REQUEST['aw_genres'] ) ) {
			$single = sanitize_text_field( $_REQUEST['aw_genres'] );

			// Genre.
			$query .= $wpdb->prepare(
				"
				AND ID IN (
					SELECT object_id FROM {$wpdb->term_relationships} 
					WHERE term_taxonomy_id IN (
						SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
						WHERE taxonomy = 'genre' 
						AND term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE slug = %s)
					)
				)
				",
				$single
			);
		}

		return $query;
	}

	/**
	 * Get post thumbnail HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int|null $post_id Post ID.
	 * @param string   $size Size.
	 *
	 * @return string
	 */
	public function get_post_thumbnail( $post_id = null, $size = 'album_medium' ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return '';
			}
		}

		$thumbnail_html = get_the_post_thumbnail( $post_id, $size );
		if ( ! $thumbnail_html ) {
			$placeholder = 'no-image.png';
			if ( 'album_small' === $size ) {
				$placeholder = 'no-image-600x400.png';
			}

			$thumbnail_html = sprintf(
				'<img src="%s">',
				get_template_directory_uri() . '/assets/images/' . $placeholder
			);
		}

		return $thumbnail_html;
	}

	/**
	 * Get HTML for albums query.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param null|WP_Query $albums_query Albums query.
	 * @param array         $args Args.
	 *
	 * @return string
	 */
	public function get_albums_html( $albums_query = null, $args = array() ) {
		if ( $albums_query instanceof WP_Query || null === $albums_query ) {
			$albums_query = self::get_instance()->get_albums( $args );
		}

		ob_start();

		if ( $albums_query->have_posts() ) {
			while ( $albums_query->have_posts() ) {
				$albums_query->the_post();

				// Check if the template exists.
				if ( ! file_exists( get_template_directory() . '/template-parts/content-album.php' ) ) {
					printf(
						'<p>%s</p>',
						esc_html( __( 'No template found', 'albums-wow' ) )
					);
					continue;
				}

				get_template_part( 'template-parts/content', 'album' );
			}

			wp_reset_postdata();
		} else {
			printf(
				'<p>%s</p>',
				esc_html( __( 'No content found', 'albums-wow' ) )
			);
		}

		return ob_get_clean();
	}

	/**
	 * Get albums using WPDB.
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $wpdb
	 *
	 * @param string $query Query string.
	 *
	 * @return array|false Query results.
	 */
	public function get_albums_wpdb_posts( $query = '' ) {
		global $wpdb;

		$query = sanitize_text_field( $query );

		parse_str( $query, $query_args );

		// Set defaults.
		$post_type      = isset( $query_args['post_type'] ) ? $query_args['post_type'] : 'album';
		$post_status    = isset( $query_args['post_status'] ) ? $query_args['post_status'] : 'publish';
		$posts_per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : get_option( 'posts_per_page' );

		if ( '' === $posts_per_page ) {
			$posts_per_page = get_option( 'posts_per_page' );
		}

		// SQL prepare.
		$query = $wpdb->prepare(
			"
			SELECT * FROM {$wpdb->posts}
			WHERE post_type = %s
			AND post_status = %s
			",
			$post_type,
			$post_status,
		);

		// Apply filters.
		$query .= self::get_instance()->albums_filters_wpdb( $query_args );

		// Limit.
		$query .= $wpdb->prepare(
			'
			LIMIT %d
			',
			$posts_per_page
		);

		// Get albums.
		$cache_key = 'albums_wow_albums_wpdb_' . md5( wp_json_encode( $query ) );
		$cache     = wp_cache_get( $cache_key, '_albums_wpdb' );

		// Get albums from cache.
		if ( $cache ) {
			return $cache;
		}

		// Get albums from database.
		$results = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				array_fill(
					0,
					substr_count(
						$query,
						'%'
					),
					null
				)
			)
		);

		if ( ! $results ) {
			return false;
		}

		// Set albums to cache.
		wp_cache_set( $cache_key, $results, '_albums_wpdb' );

		return $results;
	}

	/**
	 * Get HTML for albums query.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $posts Album query.
	 *
	 * @return string
	 */
	public function get_albums_wpdb_html( $posts = '' ) {
		if ( empty( $posts ) ) {
			$posts = self::get_instance()->get_albums_wpdb_posts();
		}

		$template_exists = file_exists( ALBUMS_WOW_DIR . '/template-parts/content-album.php' );

		ob_start();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				// Check if the template exists.
				if ( ! $template_exists ) {
					printf(
						'<p>%s</p>',
						esc_html( __( 'No template found', 'albums-wow' ) )
					);
					continue;
				}

				$GLOBALS['post'] = $post;
				setup_postdata( $post );
				get_template_part( 'template-parts/content', 'album' );

				wp_reset_postdata();
			}
		} else {
			printf(
				'<p>%s</p>',
				esc_html( __( 'No content found', 'albums-wow' ) )
			);
		}

		return ob_get_clean();
	}

	/**
	 * Get HTML for social block.
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $post
	 *
	 * @param WP_Post $post     Optional. Post object.
	 * @param boolean $wrap     Optional. Whether to wrap or not.
	 *
	 * @return string
	 */
	public function get_social_block_html( $post = null, $wrap = false ) {
		if ( ! $post instanceof WP_Post ) {
			global $post;

			if ( ! $post ) {
				return '';
			}
		}

		// Get social links.
		$soundcloud = get_post_meta( $post->ID, '_soundcloud', true );
		$spotify    = get_post_meta( $post->ID, '_spotify', true );
		$wikipedia  = get_post_meta( $post->ID, '_wikipedia', true );
		$youtube    = get_post_meta( $post->ID, '_youtube', true );

		if ( ! file_exists( ALBUMS_WOW_DIR . 'template-parts/social-block.php' ) ) {
			return '';
		}

		ob_start();

		include_once ALBUMS_WOW_DIR . 'template-parts/social-block.php';

		return ob_get_clean();
	}

	/**
	 * Get views for a post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $post
	 *
	 * @return int Post views.
	 */
	public function get_views() {
		global $post;

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		// Get current views.
		$views = get_post_meta( $post->ID, '_views', true );
		$views = $views ? $views : 0;

		// Save old views.
		$old_views = $views;

		// Process views.
		$views = $this->views_processing( $post->ID, $views );

		return $views;
	}

	/**
	 * Process views for a post.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 * @param int $views   Post views.
	 *
	 * @return int Post views.
	 */
	public function views_processing( $post_id, $views = 0 ) {

		// Get current views from cookie.
		$albums_wow_views = isset( $_COOKIE['albums_wow_views'] ) ? wp_unslash( sanitize_text_field( $_COOKIE['albums_wow_views'] ) ) : false;

		if ( false !== $albums_wow_views && ! empty( $albums_wow_views ) ) {
			// If views are already counted for this post, don't count it again.
			$albums_wow_views = explode( ',', $albums_wow_views );

			if ( in_array( $post_id, $albums_wow_views ) ) {
				return $views;
			}

			// Count views.
			++$views;

			// Update cookie with new post ID.
			$albums_wow_views[] = $post_id;
			$albums_wow_views   = implode( ',', $albums_wow_views );

			setcookie( 'albums_wow_views', (string) $albums_wow_views, time() + ( 30 * 24 * 3600 ), '/' );
		} else {
			// Get current views from post meta.
			$views = get_post_meta( $post_id, '_views', true );
			++$views;

			// Set cookie with post ID.
			setcookie( 'albums_wow_views', (string) $post_id, time() + ( 30 * 24 * 3600 ), '/' );
		}

		// Update post meta with new views.
		update_post_meta( $post_id, '_views', $views );

		return $views;
	}

	/**
	 * Get breadcrumbs.
	 *
	 * Checks if popular SEO plugins are active and uses their breadcrumbs.
	 * If none of the plugins are active, it uses the custom breadcrumbs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_breadcrumbs() {
		// Check if Yoast SEO plugin is active.
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			\yoast_breadcrumb( '<div id="breadcrumbs">', '</div>' );
			return;
		}

		// Check if Rank Math plugin is active.
		if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
			\rank_math_the_breadcrumbs();
			return;
		}

		// Check if Breadcrumb NavXT plugin is active.
		if ( function_exists( 'breadcrumbs' ) ) {
			\breadcrumbs();
			return;
		}

		// Check if Breadcrumb Trail plugin is active.
		if ( function_exists( 'bcn_display' ) ) {
			\bcn_display();
			return;
		}

		// If none of the plugins are active, use the custom breadcrumbs.
		self::get_instance()->custom_breadcrumbs();
	}

	/**
	 * Custom breadcrumbs.
	 *
	 * @since 0.1.0
	 * @access public
	 * @global object $post
	 *
	 * @param string $separator Separator.
	 *
	 * @return void
	 */
	public function custom_breadcrumbs( string $separator = '-' ) {
		// Settings.
		$separator  = apply_filters( 'albums_wow_breadcrumbs_separator', $separator );
		$home_title = apply_filters( 'albums_wow_breadcrumbs_home_title', _x( 'Home', 'Home Breadcrumb Name', 'albums-wow' ) );
		$classes    = apply_filters( 'albums_wow_breadcrumbs_classes', 'breadcrumbs' );

		// Get global post object.
		global $post;

		// Start breadcrumb trail.
		printf(
			'<ul class="%s">',
			esc_attr( $classes ),
		);

		// Link to homepage.
		printf(
			'<li class="home"><a href=%s">%s</a></li>',
			esc_url( home_url() ),
			esc_html( $home_title )
		);

		// Separator.
		printf(
			'<li class="separator">%s</li>',
			esc_html( $separator )
		);

		if ( is_single() ) {
			// If it is single post.
			$categories = wp_get_post_terms( $post->ID, 'albums_single' );
			if ( ! empty( $categories ) ) {
				$single = $categories[0];
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_term_link( $single ) ),
					esc_html( $single->name ),
				);

				// Separator.
				printf(
					'<li class="separator">%s</li>',
					esc_html( $separator )
				);
			}

			printf(
				'<li>%s</li>',
				esc_html( get_the_title() ),
			);
		} elseif ( is_page() ) {
			// If it is page.
			if ( $post->post_parent ) {
				$parent_id   = $post->post_parent;
				$breadcrumbs = array();

				while ( $parent_id ) {
					$page          = get_post( $parent_id );
					$breadcrumbs[] = sprintf(
						'<li><a href="%s">%s</a></li>',
						get_permalink( $page->ID ),
						get_the_title( $page->ID )
					);
					$parent_id     = $page->post_parent;
				}

				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) {
					echo $crumb . $separator; // phpcs:ignore
				}
			}
			printf(
				'<li>%s</li>',
				esc_html( get_the_title() ),
			);
		} elseif ( is_single() ) {
			// If it is single.
			printf(
				'<li>%s</li>',
				esc_html( single_cat_title( '', false ) ),
			);
		} elseif ( is_archive() ) {
			// If it is archive.
			if ( is_tag() ) {
				printf(
					'<li>%s</li>',
					esc_html( single_tag_title( '', false ) ),
				);
			} elseif ( is_day() ) {
				printf(
					'<li>%s</li>',
					esc_html( get_the_date() ),
				);
			} elseif ( is_month() ) {
				printf(
					'<li>%s</li>',
					esc_html( get_the_date( 'F Y' ) ),
				);
			} elseif ( is_year() ) {
				printf(
					'<li>%s</li>',
					esc_html( get_the_date( 'Y' ) ),
				);
			} else {
				printf(
					'<li>%s</li>',
					esc_html( post_type_archive_title() ),
				);
			}
		} elseif ( is_search() ) {
			// If it is search.
			printf(
				'<li>%s %s</li>',
				esc_html_x( 'Search Results:', 'Search Results', 'albums-wow' ),
				get_search_query(),
			);
		} elseif ( is_404() ) {
			// If it is 404.
			printf(
				'<li>%s</li>',
				esc_html_x( '404 Not Found', '404 Not Found Error', 'albums-wow' )
			);
		}

		echo '</ul>';
	}

	/**
	 * Get album terms.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $taxonomy   Taxonomy name. Default 'albums_single'.
	 * @param bool   $hide_empty Whether to hide empty terms. Default false.
	 * @return \WP_Term[]|void Array of terms, or void if there are no terms.
	 */
	public function get_album_terms( $taxonomy = 'albums_single', $hide_empty = false ) {
		$args = array(
			'hide_empty' => $hide_empty,
			'taxonomy'   => $taxonomy,
			'order'      => 'ASC',
			'orderby'    => 'name',
		);

		$cache_key = 'albums_wow_terms_' . md5( serialize( $args ) );
		$terms     = wp_cache_get( $cache_key );
		if ( false === $terms ) {
			$terms = get_terms( $args );
			// Check if there are any terms.
			if ( ! is_wp_error( $terms ) ) {
				wp_cache_set( $cache_key, $terms, '_albums_terms' );
			} else {
				$terms = '';
			}
		}

		return $terms;
	}

	/**
	 * Get filters HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_the_filters_html() {
		// Include filter scripts.
		Includes::get_instance()->filters_scripts();

		// Filters.
		$filters = array(
			'singles' => self::get_instance()->get_album_terms(),
			'genres'  => self::get_instance()->get_album_terms( 'genre' ),
		);

		echo '<div class="filters">';

		// Loop through each filter.
		foreach ( $filters as $filter_name => $filter ) {
			if ( empty( $filter ) ) {
				continue;
			}

			// Include the filter template.
			include ALBUMS_WOW_DIR . 'template-parts/views/filter-select.php';
		}

		echo '</div>'; // end .filters.
	}
}
