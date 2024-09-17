<aside id="sidebar">
	<?php if ( is_active_sidebar( 'main-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'main-sidebar' ); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No sidebar widgets', 'albums-wow' ); ?></p>
	<?php endif; ?>
</aside>
