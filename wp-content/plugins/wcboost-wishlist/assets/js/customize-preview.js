/** global jQuery, wp.customize */
( function( $, api ) {
	var settings = [
		'wcboost_wishlist_button_style[background_color]',
		'wcboost_wishlist_button_style[border_color]',
		'wcboost_wishlist_button_style[text_color]',
		'wcboost_wishlist_button_hover_style[background_color]',
		'wcboost_wishlist_button_hover_style[border_color]',
		'wcboost_wishlist_button_hover_style[text_color]'
	];

	// Add listeners for button style controls.
	for ( var key in settings ) {
		api( settings[ key ], function( value ) {
			value.bind( function( to ) {
				updateButtonStyle();
			} );
		} );
	}

	// Update global CSS vars for buttons.
	var updateButtonStyle = function() {
		var id = 'wcboost-wishlist-preview-css',
			styles = getButtonCSSVars(),
			$stylesheet = $( '#' + id );

		if ( ! $stylesheet.length ) {
			$stylesheet = $( '<style></style>' ).attr( 'id', id ).appendTo( 'head' );
		}

		$stylesheet.html( ':root {' + styles + '}' );

		console.log($stylesheet);
	};

	// Get button CSS variables.
	var getButtonCSSVars = function() {
		var prefixNormal = '--wcboost-wishlist-button-color--',
			prefixHover = '--wcboost-wishlist-button-hover-color--',
			styles = '';

		styles += prefixNormal + 'background:' + api( 'wcboost_wishlist_button_style[background_color]' ).get() + ';';
		styles += prefixNormal + 'border:' + api( 'wcboost_wishlist_button_style[border_color]' ).get() + ';';
		styles += prefixNormal + 'text:' + api( 'wcboost_wishlist_button_style[text_color]' ).get() + ';';
		styles += prefixHover + 'background:' + api( 'wcboost_wishlist_button_hover_style[background_color]' ).get() + ';';
		styles += prefixHover + 'border:' + api( 'wcboost_wishlist_button_hover_style[border_color]' ).get() + ';';
		styles += prefixHover + 'text:' + api( 'wcboost_wishlist_button_hover_style[text_color]' ).get() + ';';

		return styles;
	};
} )( jQuery, wp.customize );
