/*global soberTermData */

jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Only show the "remove image" button when needed
	if ( $( '#page-header-image-id' ).val() == 0 ) {
		$( '.remove-header-image-button' ).hide();
	}

	// Uploading files
	var file_frame;

	$( document.body ).on( 'click', '.upload-header-image-button', function ( event ) {

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media( {
			title   : soberTermData.l10n.title,
			button  : {
				text: soberTermData.l10n.button
			},
			multiple: false
		} );

		// When an image is selected, run a callback.
		file_frame.on( 'select', function () {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();

			$( '#page-header-image-id' ).val( attachment.id );
			$( '#page-header-image' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
			$( '.remove-header-image-button' ).show();
		} );

		// Finally, open the modal.
		file_frame.open();
	} );

	$( document.body ).on( 'click', '.remove-header-image-button', function ( event ) {
		event.preventDefault();

		$( '#page-header-image-id' ).val( '' );
		$( '#page-header-image' ).find( 'img' ).attr( 'src', soberTermData.placeholder );
		$( '.remove-header-image-button' ).hide();
	} );

	$( document ).ajaxComplete( function ( event, request, options ) {
		if ( request && 4 === request.readyState && 200 === request.status
			&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

			var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
			if ( !res || res.errors ) {
				return;
			}

			// Clear Thumbnail fields on submit
			$( '#page-header-image-id' ).val( '' );
			$( '#page-header-image' ).find( 'img' ).attr( 'src', soberTermData.placeholder );
			$( '.remove-header-image-button' ).hide();

			return;
		}
	} );
} );