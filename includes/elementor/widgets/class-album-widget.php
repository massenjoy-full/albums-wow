<?php
/**
 * Includes
 *
 * @package Albums-Wow
 */

if ( ! defined( 'ALBUMS_WOW_VERSION' ) ) {
	die( esc_html_x( 'No direct access allowed.', 'Error', 'albums-wow' ) );
}

/**
 * Elementor widget for displaying an album.
 *
 * @package Albums-Wow
 */
class Album_Widget extends \Elementor\Widget_Base {

	/**
	 * Constructor.
	 *
	 * Register the scripts and styles for the Elementor widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array      $data     Widget data.
	 * @param null|array $args     Widget arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// Register the styles for the widget.
		wp_register_style(
			'albums-wow-elementor-filters',
			ALBUMS_WOW_URL . '/assets/css/filters.css',
			array( 'elementor-frontend' ),
			ALBUMS_WOW_VERSION
		);

		// Register the scripts for the widget.
		wp_register_script(
			'albums-wow-elementor-filters',
			ALBUMS_WOW_URL . '/assets/js/filters.js',
			array( 'jquery' ),
			ALBUMS_WOW_VERSION,
			true
		);
	}

	/**
	 * Get the style dependencies for the widget.
	 *
	 * This function is used by Elementor to determine the CSS files to be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array The style dependencies for the widget.
	 */
	public function get_style_depends() {
		return array( 'albums-wow-elementor-filters' );
	}

	/**
	 * Get the scripts dependencies for the widget.
	 *
	 * This function is used by Elementor to determine the Script files to be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array The scripts dependencies for the widget.
	 */
	public function get_script_depends() {
		return array( 'albums-wow-elementor-filters' );
	}

	/**
	 * Get the widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'album_widget';
	}

	/**
	 * Get the widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Album', 'albums-wow' );
	}

	/**
	 * Get the widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Get the widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'basic' );
	}

	/**
	 * Get the widget keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'album', 'loop' );
	}

	/**
	 * Registers controls for the widget.
	 *
	 * This function is called at the beginning of the constructor.
	 * It registers the necessary controls for the widget.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	protected function register_controls() {
		$this->section_content();
	}

	/**
	 * Registers controls for the content section.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function section_content() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'elementor' ),
				'tab'   => Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Register the "Show Title" control.
		$this->add_control(
			'show_main_title',
			array(
				'label'        => esc_html__( 'Show Title', 'albums-wow' ),
				'type'         => Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'elementor' ),
				'label_off'    => esc_html__( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		// Register the "Main Title" control.
		$this->add_control(
			'main_title',
			array(
				'label'     => esc_html__( 'Main Title', 'elementor' ),
				'type'      => Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Lorem Ipsum', 'elementor' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'show_main_title' => 'yes',
				),
			)
		);

		// Register the "Title Alignment" control.
		$this->add_control(
			'main_title_text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'elementor' ),
				'type'      => Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .album-title' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'show_main_title' => 'yes',
				),
			)
		);

		// Register the "Title Tag" control.
		$this->add_control(
			'main_title_tag',
			array(
				'label'     => esc_html__( 'Title HTML Tag', 'elementor' ),
				'type'      => Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h3',
				'condition' => array(
					'show_main_title' => 'yes',
				),
				'separator' => 'after',
			)
		);

		// Register the "Post Per Page" control.
		$this->add_control(
			'posts_count',
			array(
				'label'   => esc_html__( 'Posts Per Page', 'albums-wow' ),
				'type'    => Elementor\Controls_Manager::NUMBER,
				'default' => get_option( 'posts_per_page' ), // Default number of posts per page.
			)
		);

		// Register the "Show Filters" control.
		$this->add_control(
			'show_filters',
			array(
				'label'        => esc_html__( 'Show Filters', 'albums-wow' ),
				'type'         => Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'elementor' ),
				'label_off'    => esc_html__( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		// Register the "Use WPDB" control.
		$this->add_control(
			'is_wpdb',
			array(
				'label'        => esc_html__( 'Use WPDB', 'albums-wow' ),
				'type'         => Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor' ),
				'label_off'    => esc_html__( 'No', 'elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'wpdb_query',
			array(
				'label'       => esc_html__( 'WPDB Query', 'albums-wow' ),
				'type'        => Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Enter WPDB query here. For example: "posts_per_page=5&post_type=album"', 'albums-wow' ),
				'default'     => '',
				'condition'   => array(
					'is_wpdb' => 'yes',
				),
			),
		);
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function render() {
		// Get the settings for the current instance of the widget.
		$settings       = $this->get_settings_for_display();
		$posts_per_page = isset( $settings['posts_count'] ) ? intval( $settings['posts_count'] ) : 0;

		// Display the "Main Title" control.
		if ( 'yes' === $settings['show_main_title'] ) {
			printf(
				'<%1$s class="album-title">%2$s</%1$s>',
				esc_html( $settings['main_title_tag'] ),
				esc_html( $settings['main_title'] )
			);
		}

		// Display the "Show Filters" control.
		if ( 'yes' === $settings['show_filters'] ) {
			echo Albums_Wow\Helper::get_instance()->get_the_filters_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Get args.
		$args = array(
			'posts_per_page' => $posts_per_page,
		);

		// Classes.
		$classes = 'elementor-album';

		if ( 'yes' === $settings['is_wpdb'] ) {
			$classes .= ' elementor-album-wpdb album-wpdb';
		}

		// Get content.
		if ( 'yes' === $settings['is_wpdb'] ) {
			$posts   = Albums_Wow\Helper::get_instance()->get_albums_wpdb_posts( $settings['wpdb_query'] );
			$content = Albums_Wow\Helper::get_instance()->get_albums_wpdb_html( $posts );
		} else {
			$content = Albums_Wow\Helper::get_instance()->get_albums_html( null, $args );
		}

		// Display content.
		printf(
			'<div id="albums" class="%s">%s</div>',
			esc_attr( $classes ),
			$content, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
}
