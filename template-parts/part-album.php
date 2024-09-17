<?php

namespace Albums_Wow;

Helper::get_instance()->get_breadcrumbs();
?>

<div class="album single-album album-<?php echo esc_attr( $post->ID ); ?>">
	<div class="row">
		<div class="col-sm-6">
			<div class="image">
				<?php echo Helper::get_instance()->get_post_thumbnail(); // phpcs:ignore Html.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>

		<div class="col-sm-6 content">
			<h1 class="title no-margin"><?php the_title(); ?></h1>
			<div class="info">
				<div class="col-sm-6">
					<?php echo Helper::get_instance()->get_social_block_html( $post, true ); // phpcs:ignore Html.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="views"><i title="<?php esc_attr_e( 'Views', 'albums-wow' ); ?>" class="fa fa-eye"></i> <?php echo esc_html( Helper::get_instance()->get_views() ); ?></div>
				</div>
				<div class="col-sm-6">
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
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</div>

<?php
