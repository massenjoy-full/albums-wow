<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

	<header>
		<h1><?php bloginfo( 'name' ); ?></h1>
		<p><?php bloginfo( 'description' ); ?></p>
		<nav><?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?></nav>
	</header>

	<div id="content">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_title( '<h2>', '</h2>' );
				the_content();
			}
		} else {
			printf(
				'<p>%s</p>',
				esc_html( __( 'No content found', 'albums-wow' ) )
			);
		}
		?>
	</div>

	<?php get_sidebar(); ?>
	<?php get_footer(); ?>

	<?php wp_footer(); ?>
</body>
</html>
