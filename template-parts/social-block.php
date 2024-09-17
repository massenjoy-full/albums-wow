<?php

if ( $wrap ) {
	echo '<div class="social-block">';
}

if ( $soundcloud ) {
	printf(
		'<a class="soundcloud" href="%s" target="_blank" rel="nofollow" title="%s"><i></i></a>',
		esc_url( $soundcloud ),
		esc_attr( 'SoundCloud' ),
	);
}

if ( $spotify ) {
	printf(
		'<a class="spotify" href="%s" target="_blank" rel="nofollow" title="%s"><i></i></a>',
		esc_url( $spotify ),
		esc_attr( 'Spotify' ),
	);
}

if ( $wikipedia ) {
	printf(
		'<a class="wikipedia" href="%s" target="_blank" rel="nofollow" title="%s"><i></i></a>',
		esc_url( $wikipedia ),
		esc_attr( 'Wikipedia' ),
	);
}

if ( $youtube ) {
	printf(
		'<a class="youtube" href="%s" target="_blank" rel="nofollow" title="%s"><i></i></a>',
		esc_url( $youtube ),
		esc_attr( 'YouTube' ),
	);
}

if ( $wrap ) {
	echo '</div>'; // end social-block.
}
