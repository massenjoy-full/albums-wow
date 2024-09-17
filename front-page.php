<?php
// Get posts.
get_header();

echo '<div id="albums">';
	echo Albums_Wow\Helper::get_instance()->get_albums_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '</div>';

get_footer();
