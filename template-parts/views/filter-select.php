<?php

// Get filter.
$selected_value = isset( $_REQUEST[ 'aw_' . $filter_name ] ) ? sanitize_text_field( $_REQUEST[ 'aw_' . $filter_name ] ) : '';

?>
<div class="filter filter-<?php echo esc_attr( $filter_name ); ?>">
	<div class="filter-title"><?php echo esc_html( ucfirst( $filter_name ) ); ?>: </div>
	<select name="aw_<?php echo esc_attr( $filter_name ); ?>" id="aw_<?php echo esc_attr( $filter_name ); ?>">
		<option value=""><?php esc_html_e( 'All', 'albums-wow' ); ?></option>
		<?php
		foreach ( $filter as $_term ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $_term->slug ),
				selected( $_term->slug, $selected_value, false ),
				esc_html( $_term->name )
			);
		}
		?>
	</select>
</div>
