var slicewp_register_visit = function() {

	if ( typeof slicewp === 'undefined' ) {
		return false;
	}

	var cookie_aff = get_cookie( 'slicewp_aff' );
	var aff;

	if ( slicewp.affiliate_credit == 'first' && cookie_aff ) {
		return false;
	}

	if ( slicewp.post_affiliate_id ) {
	
		aff = slicewp.post_affiliate_id;

		if ( cookie_aff && cookie_aff == aff ) {
			return false;
		}

	} else {

		aff = get_query_arg( slicewp.affiliate_keyword );
		aff = ( aff != '' ? aff : get_query_arg_pretty( slicewp.affiliate_keyword ) );

		if ( aff == '' ) {
			return false;
		}
	
		if ( slicewp.affiliate_credit == 'last' && cookie_aff && cookie_aff == aff ) {
			return false;
		}

	}


	var data = new FormData();

	data.append( 'action', 'slicewp_register_visit' );
	data.append( 'aff', aff );
	data.append( 'landing_url', document.URL );
	data.append( 'referrer_url', document.referrer );

	var request = new XMLHttpRequest();

	request.open( 'POST', slicewp_ajaxurl, true );

	request.onload = function () {

        if ( this.status >= 200 && this.status < 400 ) {
            
			var response = JSON.parse( this.response );

			if ( response.success > 0 ) {

				set_cookie( 'slicewp_aff', response.affiliate_id );
				set_cookie( 'slicewp_visit', response.visit_id );

			} else {

				// For debugging purposes.
				console.log( response );

			}

        }

    };

	request.send( data );


	/**
	 * Returns the value of the query argument requested from the current URL, if present.
	 *
	 * @param string arg
	 *
	 * @return string
	 *
	 */
	function get_query_arg( arg ) {

		var query = window.location.search.slice(1);
		var parts = query.split('&');
		var obj   = {};

		parts.map( function( part ) {

			part = part.split('=');

			obj[part[0]] = part[1];

		});

		return ( typeof obj[arg] != 'undefined' ? obj[arg] : '' );

	}


	/**
	 * Returns the value of the query argument requested from the current prettified URL, if present.
	 *
	 * @param string arg
	 *
	 * @return string
	 *
	 */
	function get_query_arg_pretty( arg ) {

		var path  = window.location.pathname;
		var parts = path.split( '/' );
		var val   = '';

		for ( var i = 0; i < parts.length; i++ ) {

			if ( parts[i] == arg ) {
				val = parts[i+1];
				break;
			}

		}

		return val;

	}


	/**
	 * Set a cookie.
	 *
	 * @param string name
	 * @param string value
	 *
	 */
	function set_cookie( name, value ) {

	    var d = new Date();
	    d.setTime( d.getTime() + ( slicewp.cookie_duration * 24 * 60 * 60 * 1000 ) );
	    var expires = "expires=" + d.toUTCString();

	    document.cookie = name + "=" + value + "; " + expires + "; " + "path=/;";

	}


	/**
	 * Get a cookie.
	 *
	 * @param string name
	 *
	 * @return string
	 *
	 */
	function get_cookie( name ) {

	    var name = name + "=";
	    var ca 	 = document.cookie.split(';');

	    for ( var i=0; i<ca.length; i++ ) {
	        var c = ca[i];
	        while ( c.charAt(0)==' ' ) c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }

	    return false;

	}

}


if ( document.readyState === "complete" || ( document.readyState !== "loading" && ! document.documentElement.doScroll ) ) {

	slicewp_register_visit();

} else {

  document.addEventListener( "DOMContentLoaded", slicewp_register_visit );

}