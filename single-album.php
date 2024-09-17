<?php get_header(); ?>

<div id="primary" class="content-area">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			// Check if the template exists.
			if ( ! file_exists( get_template_directory() . '/template-parts/part-album.php' ) ) {
				the_post();
				the_title( '<h1>', '</h1>' );
				the_content();
				continue;
			}

			get_template_part( 'template-parts/part', 'album' );
		}
	}
	?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
