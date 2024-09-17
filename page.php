<?php get_header(); ?>

<div id="primary" class="content-area">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_title( '<h1>', '</h1>' );
			the_content();
		}
	}
	?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
