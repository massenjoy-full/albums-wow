<?php
/**
 * Posts
 *
 * @package Albums-Wow
 */

namespace Albums_Wow;

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Class Posts
 */
class Posts {
	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Posts
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Posts
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
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Register post type
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Albums', 'post type general name', 'albums-wow' ),
			'singular_name'      => _x( 'Album', 'post type singular name', 'albums-wow' ),
			'menu_name'          => _x( 'Albums', 'admin menu', 'albums-wow' ),
			'name_admin_bar'     => _x( 'Album', 'add new on admin bar', 'albums-wow' ),
			'add_new'            => _x( 'Add New', 'album', 'albums-wow' ),
			'add_new_item'       => __( 'Add New Album', 'albums-wow' ),
			'new_item'           => __( 'New Album', 'albums-wow' ),
			'edit_item'          => __( 'Edit Album', 'albums-wow' ),
			'view_item'          => __( 'View Album', 'albums-wow' ),
			'all_items'          => __( 'All Albums', 'albums-wow' ),
			'search_items'       => __( 'Search Albums', 'albums-wow' ),
			'parent_item_colon'  => __( 'Parent Albums:', 'albums-wow' ),
			'not_found'          => __( 'No albums found.', 'albums-wow' ),
			'not_found_in_trash' => __( 'No albums found in Trash.', 'albums-wow' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Albums', 'albums-wow' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'album' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 4,
			'supports'           => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'comments',
				'revisions',
				'page-attributes',
			),
			'menu_icon'          => 'dashicons-portfolio',
		);

		register_post_type( 'album', $args );
	}

	/**
	 * Register taxonomies
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		// Categories.
		$this->register_albums_singles();

		// Genres.
		$this->register_albums_genres();
	}

	/**
	 * Register taxonomy for album singles
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_albums_singles() {
		$labels = array(
			'name'              => _x( 'Singles', 'taxonomy general name', 'albums-wow' ),
			'singular_name'     => _x( 'Single', 'taxonomy singular name', 'albums-wow' ),
			'search_items'      => __( 'Search Singles', 'albums-wow' ),
			'all_items'         => __( 'All Singles', 'albums-wow' ),
			'parent_item'       => __( 'Parent Single', 'albums-wow' ),
			'parent_item_colon' => __( 'Parent Single:', 'albums-wow' ),
			'edit_item'         => __( 'Edit Single', 'albums-wow' ),
			'update_item'       => __( 'Update Single', 'albums-wow' ),
			'add_new_item'      => __( 'Add New Single', 'albums-wow' ),
			'new_item_name'     => __( 'New Single Name', 'albums-wow' ),
			'menu_name'         => __( 'Singles', 'albums-wow' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'single' ),
		);

		register_taxonomy( 'albums_single', 'album', $args );
	}

	/**
	 * Register taxonomy for album genres
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_albums_genres() {
		$labels = array(
			'name'              => _x( 'Genres', 'taxonomy general name', 'albums-wow' ),
			'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'albums-wow' ),
			'search_items'      => __( 'Search Genres', 'albums-wow' ),
			'all_items'         => __( 'All Genres', 'albums-wow' ),
			'parent_item'       => __( 'Parent Genre', 'albums-wow' ),
			'parent_item_colon' => __( 'Parent Genre:', 'albums-wow' ),
			'edit_item'         => __( 'Edit Genre', 'albums-wow' ),
			'update_item'       => __( 'Update Genre', 'albums-wow' ),
			'add_new_item'      => __( 'Add New Genre', 'albums-wow' ),
			'new_item_name'     => __( 'New Genre Name', 'albums-wow' ),
			'menu_name'         => __( 'Genres', 'albums-wow' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'genre' ),
		);

		register_taxonomy( 'genre', 'album', $args );
	}

	/**
	 * Register meta box for albums
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $post_type The post type.
	 *
	 * @return void
	 */
	public function register_metabox( $post_type ) {
		if ( 'album' === $post_type ) {
			// Register meta box for albums.
			$this->register_albums_metabox();
		}
	}

	/**
	 * Register meta box for albums
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_albums_metabox() {
		add_meta_box(
			'albums_wow_albums_metabox',
			__( 'Albums', 'albums-wow' ),
			array( $this, 'render_metabox' ),
			'album',
			'normal',
			'high'
		);
	}

	/**
	 * Render meta box for albums
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function render_metabox( $post ) {
		$date_meta       = get_post_meta( $post->ID, '_date', true );
		$soundcloud_meta = get_post_meta( $post->ID, '_soundcloud', true );
		$spotify_meta    = get_post_meta( $post->ID, '_spotify', true );
		$wikipedia_meta  = get_post_meta( $post->ID, '_wikipedia', true );
		$youtube_meta    = get_post_meta( $post->ID, '_youtube', true );

		// Nonce.
		wp_nonce_field( 'albums_wow_metabox_nonce', 'albums_wow_save_metabox' );
		?>
		<table>
			<tr>
				<td><label for="date"><?php esc_html_e( 'Date:', 'albums-wow' ); ?> </label></td>
				<td><input type="number" id="date" name="date" min="1900" max="<?php echo esc_attr( gmdate( 'Y' ) ); ?>" value="<?php echo esc_attr( $date_meta ); ?>"></td>
			</tr>
			<tr>
				<td><label for="soundcloud"><?php esc_html_e( 'Soundcloud:', 'albums-wow' ); ?> </label></td>
				<td><input type="url" id="soundcloud" name="soundcloud" value="<?php echo esc_attr( $soundcloud_meta ); ?>"></td>
			</tr>
			<tr>
				<td><label for="soundcloud"><?php esc_html_e( 'Spotify:', 'albums-wow' ); ?> </label></td>
				<td><input type="url" id="soundcloud" name="spotify" value="<?php echo esc_attr( $spotify_meta ); ?>"></td>
			</tr>
			<tr>
				<td><label for="youtube"><?php esc_html_e( 'Youtube:', 'albums-wow' ); ?> </label></td>
				<td><input type="url" id="youtube" name="youtube" value="<?php echo esc_attr( $youtube_meta ); ?>"></td>
			</tr>
			<tr>
				<td><label for="wikipedia"><?php esc_html_e( 'Wikipedia:', 'albums-wow' ); ?> </label></td>
				<td><input type="url" id="wikipedia" name="wikipedia" value="<?php echo esc_attr( $wikipedia_meta ); ?>"></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save meta box data for albums
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The post object.
	 *
	 * @return void
	 */
	public function save_metabox( $post_id, $post ) {
		// Check if this is an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) || 'album' !== $post->post_type ) {
			return;
		}

		// Check is referer is valid.
		if ( ! isset( $_POST['_wp_http_referer'] ) || ! wp_get_referer() ) {
			return;
		}

		// Get values from $_POST.
		$date       = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
		$soundcloud = isset( $_POST['soundcloud'] ) ? sanitize_text_field( wp_unslash( $_POST['soundcloud'] ) ) : '';
		$spotify    = isset( $_POST['spotify'] ) ? sanitize_text_field( wp_unslash( $_POST['spotify'] ) ) : '';
		$wikipedia  = isset( $_POST['wikipedia'] ) ? sanitize_text_field( wp_unslash( $_POST['wikipedia'] ) ) : '';
		$youtube    = isset( $_POST['youtube'] ) ? sanitize_text_field( wp_unslash( $_POST['youtube'] ) ) : '';

		// Update meta values.
		update_post_meta( $post_id, '_date', $date );
		update_post_meta( $post_id, '_soundcloud', $soundcloud );
		update_post_meta( $post_id, '_spotify', $spotify );
		update_post_meta( $post_id, '_wikipedia', $wikipedia );
		update_post_meta( $post_id, '_youtube', $youtube );
	}
}
