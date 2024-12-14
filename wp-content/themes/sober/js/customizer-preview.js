/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $, api ) {
	'use strict';

	// Header background
	api( 'header_bg', function( value ) {
		value.bind( function( to ) {
			var $body = $( document.body ),
				textColorClass = 'header-' . to;

			$body.removeClass( 'header-white header-transparent header-custom' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Header text color.
	api( 'header_text_color', function( value ) {
		value.bind( function( to ) {
			$body.removeClass( 'header-text-dark header-text-light' ).addClass( 'header-text-' + to );
		} );
	} );

	/** FOOTER */
	// Footer background
	api( 'footer_background', function( value ) {
		value.bind( function( to ) {
			var $body = $( document.body ),
				textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'footer_textcolor' ).get();
					break;
			}

			$( '#colophon' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Footer text color.
	api( 'footer_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '#colophon' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

} )( jQuery, wp.customize );
