jQuery( document ).ready( function ( $ ) {
	"use strict";

	var $box = $( '#display-settings' );

	// Toggle "Display Settings" for page template
	$( '#page_template' ).on( 'change', function () {
		handlePageTemplateChanges( $( this ).val() );
	} );

	handlePageTemplateChanges( $( '#page_template' ).val() );

	/**
	 * Handle template change event.
	 */
	function handlePageTemplateChanges( template ) {
		if ( template == 'templates/homepage.php' ) {
			$( '#full-screen-display-settings' ).hide();
			$box.show().find( '.rwmb-field.page_header_heading' ).hide().nextAll().hide();
			$box.show().find( '.rwmb-field.footer-option' ).show();
		} else if ( template === 'templates/full-screen.php' ) {
			$box.hide();
			$( '#full-screen-display-settings' ).show();
		} else {
			$( '#full-screen-display-settings' ).hide();
			$box.show().find( '.rwmb-field.page_header_heading' ).show().nextAll().show();
			$( '#top_spacing, #bottom_spacing, #custom_layout, #hide_page_header' ).trigger( 'change' );
		}
	}

	// Toggle footer background field
	$( '#footer_background' ).on( 'change', function( event ) {
		if ( event.target.value === 'custom' ) {
			$( '.footer-background-color', $box ).show();
			$( '.footer-text-color', $box ).show();
		} else if( event.target.value === 'transparent' ) {
			$( '.footer-text-color', $box ).show();
			$( '.footer-background-color', $box ).hide();
		} else {
			$( '.footer-background-color', $box ).hide();
			$( '.footer-text-color', $box ).hide();
		}
	} ).trigger( 'change' );

	// Show/hide settings for post format when choose post format
	var $format = $( '#post-formats-select' ).find( 'input.post-format' ),
		$formatBox = $( '#post-format-settings' );

	$format.on( 'change', function () {
		var type = $format.filter( ':checked' ).val();

		handlePostFormatChanges( type );
	} );
	$format.filter( ':checked' ).trigger( 'change' );

	/**
	 * Handle post format change event.
	 */
	function handlePostFormatChanges( format ) {
		$formatBox.hide();
		if ( $formatBox.find( '.rwmb-field' ).hasClass( format ) ) {
			$formatBox.show();
		}

		$formatBox.find( '.rwmb-field' ).hide();
		$formatBox.find( '.' + format ).show();
	}

	// Show/hide settings for custom layout settings
	$( '#custom_layout' ).on( 'change', function () {
		if ( $( this ).is( ':checked' ) ) {
			$( '.rwmb-field.custom-layout' ).show();
		}
		else {
			$( '.rwmb-field.custom-layout' ).hide();
		}
	} ).trigger( 'change' );

	// Toggle page header fields
	$( '#hide_page_header' ).on( 'change', function () {
		var $el = $( this );

		if ( $el.is( ':checked' ) ) {
			$( '.rwmb-field.page-header-field' ).hide();
			$( '.rwmb-field.hide-page-title' ).show();
		} else {
			$( '.rwmb-field.page-header-field' ).show();
			$( '.rwmb-field.hide-page-title' ).hide();
		}
	} ).trigger( 'change' );

	// Toggle header fields
	$( '#site_header_bg' ).on( 'change', function () {
		var $el = $( this );

		if ( 'transparent' == $el.val() ) {
			$( '.rwmb-field.site_header_text_color' ).show();
			$( '.rwmb-field.header-background-color' ).hide();
		} else if ( 'custom' == $el.val() ) {
			$( '.rwmb-field.site_header_text_color' ).show();
			$( '.rwmb-field.header-background-color' ).show();
		} else {
			$( '.rwmb-field.site_header_text_color' ).hide();
			$( '.rwmb-field.header-background-color' ).hide();
		}
	} ).trigger( 'change' );

	// Toggle spacing fields
	$( '#top_spacing, #bottom_spacing' ).on( 'change', function() {
		var $el = $( this );

		if ( 'custom' === $el.val() ) {
			$el.closest( '.rwmb-field' ).next( '.custom-spacing' ).show();
		} else {
			$el.closest( '.rwmb-field' ).next( '.custom-spacing' ).hide();
		}
	} ).trigger( 'change' );

	/**
	 * This section for Gutenberg
	 */
	if ( typeof window.wp.data !== 'undefined' ) {
		var editor = wp.data.select( 'core/editor' );

		if ( editor ) {
			var currentTemplate = editor.getEditedPostAttribute( 'template' ),
				currentFormat = editor.getEditedPostAttribute( 'format' ),
				firstFire = false;

			wp.data.subscribe( function() {
				var template = editor.getEditedPostAttribute( 'template' ),
					format = editor.getEditedPostAttribute( 'format' );

				// Use this variable to run the theme check after editor loaded fully.
				if ( ! firstFire ) {
					handlePageTemplateChanges( template );
					handlePostFormatChanges( format );
					firstFire = true;
				}

				if ( currentTemplate !== template ) {
					handlePageTemplateChanges( template );
					currentTemplate = template;
				}

				if ( currentFormat !== format ) {
					handlePostFormatChanges( format );
					currentFormat = format;
				}
			} );

			// Run once again after page loaded to make sure all conditionals work correctly.
			$( window ).on( 'load', function() {
				handlePageTemplateChanges( currentTemplate );
				handlePostFormatChanges( currentFormat );
			} );
		}
	}
} );
