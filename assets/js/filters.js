jQuery(document).ready(function($) {
	jQuery('.filters select').on('change', function() {
		filter_processing( $, $(this) );
	})
});

function filter_processing( $, item ) {
	var url = new URL( window.location.href ),
		searchParams = new URLSearchParams( url.search );

	$('.filters select').each(function() {
		var val = $(this).val();
		if ( val ) {
			searchParams.set( $(this).attr('name'), val );
		} else {
			searchParams.delete( $(this).attr('name') );
		}
	});

	url.search = searchParams.toString();

	params = url.search;
	if ($('#albums').hasClass('wpdb-albums')) {
		params += '&action=albums_wow_get_albums_wpdb';
	} else {
		params += '&action=albums_wow_get_albums';
	}

	// Set url to url history.
	window.history.replaceState( null, null, url.toString() );

	// Filter ajax.
	filter_ajax($, params );
}

function filter_ajax( $, data ) {
	data = data.replace('?', '')
	$.ajax({
		url: ajax_object.ajax_url,
		type: 'POST',
		dataType: 'JSON',
		data: data,
		success: function( response ) {
			$('#albums').html( response.html );
		}
	});
}
