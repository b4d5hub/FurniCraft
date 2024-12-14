( function( $, api ) {
	'use strict';

	// Active mobile mode when open Mobile panel.
	api.panel( 'mobile', function( panel ) {
		panel.expanded.bind( function( isExpanded ) {
			if ( isExpanded ) {
				api.previewedDevice.set( 'mobile' );
			} else {
				api.previewedDevice.set( 'desktop' );
			}
		} );
	} );
} )( jQuery, wp.customize );