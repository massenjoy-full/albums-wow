<?php

$views = get_post_meta( $post->ID, '_views', true );
$views = $views ? $views : 0;

?>
<div class="album album-<?php echo esc_attr( $post->ID ); ?>">
	<div class="image">
		<a href="<?php the_permalink(); ?>">
			<?php echo Albums_Wow\Helper::get_instance()->get_post_thumbnail( null, 'album_small' ); // phpcs:ignore Html.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
	<div class="title-section">
		<h2 class="title no-margin">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="info">
			<div class="tags">
				<?php
				$tags = get_the_terms( $post->ID, 'genre' );
				if ( $tags && ! is_wp_error( $tags ) ) {
					foreach ( $tags as $_tag ) {
						printf(
							'<a href="%s">%s</a>',
							esc_url( get_term_link( $_tag ) ),
							esc_html( $_tag->name )
						);
					}
				}
				?>
			</div>
		</div>
	</div>


	<div class="description">
		<?php the_excerpt(); ?>
	</div>

	<div class="button">
		<a class="default-button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'albums-wow' ); ?></a>
	</div>
	
</div>

<?php
