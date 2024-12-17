jQuery( function($) {

    /**
     * Removes the "slicewp-updated" query argument on page load.
     *
     */
    var url = window.location.href;

    if ( url.indexOf( 'slicewp-updated' ) != -1 ) {

        url = remove_query_arg( 'slicewp-updated', url );
    
        window.history.replaceState( {}, '', url );

    }

 	/**
     * Strips one query argument from a given URL string
     *
     */
    function remove_query_arg( key, sourceURL ) {

        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }

            rtn = rtn + "?" + params_arr.join("&");

        }

        if(rtn.split("?")[1] == "") {
            rtn = rtn.split("?")[0];
        }

        return rtn;
    }

    /**
     * Adds an argument name, value pair to a given URL string
     *
     */
    function add_query_arg( param, value, url ) {

        var re   = new RegExp("[\\?&]" + param + "=([^&#]*)"), match = re.exec(url), delimiter, newString;
        var hash = url.split('#')[1];

        url = url.split('#')[0];

	    if ( match === null ) {

	        var hasQuestionMark = /\?/.test(url);
	        delimiter = hasQuestionMark ? "&" : "?";
	        newString = url + delimiter + param + "=" + value;

	    } else {

	        delimiter = match[0].charAt(0);
	        newString = url.replace(re, delimiter + param + "=" + value);

	    }

        if ( hash ) {
            newString += '#' + hash;
        }

	    return newString;
    }

    /**
     * Converts the given bytes into KB, MB etc.
     *
     */
    function bytes_to_size( bytes ) {

        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        if ( bytes == 0 )
            return '0 Byte';
        
        var i = parseInt( Math.floor( Math.log( bytes ) / Math.log( 1024 ) ) );
        
        return Math.round( bytes / Math.pow( 1024, i ), 2 ) + ' ' + sizes[i];

    }


    /**
     * Prevents double form submit requests
     *
     */
    $(document).on( 'submit', '.slicewp-form', function(e) {

        if( $(this).hasClass( 'slicewp-is-submitting' ) )
            e.preventDefault();

        $(this).addClass( 'slicewp-is-submitting' );

    });


    /**
	 * Tab Navigation
	 *
	 */
	$(document).on( 'click', '.slicewp-nav-tab', function(e) {

        var nav_tab = $(this).attr('data-slicewp-tab');

        // Return if the nav tab data isn't set
        if( typeof nav_tab == 'undefined' )
            return true;

		e.preventDefault();

		// Nav Tab activation
		$('.slicewp-nav-tab').removeClass('slicewp-active');
		$(this).addClass('slicewp-active');

		// Show tab
		$('.slicewp-tab').removeClass('slicewp-active');

		$('.slicewp-tab[data-slicewp-tab="' + nav_tab + '"]').addClass('slicewp-active');
		$('input[name=active_tab]').val( nav_tab );


        // Change "tab" query var
        var url = window.location.href;
        url = remove_query_arg( 'affiliate-account-tab', url );
        url = add_query_arg( 'affiliate-account-tab', $(this).attr('data-slicewp-tab'), url );

        window.history.replaceState( {}, '', url );

        // Change hidden tab input
        $(this).closest('form').find('input[name=active_tab]').val( $(this).attr('data-slicewp-tab') );

	});


    /**
	 * Copy Creative textarea
	 *
	 */
    $(document).on( 'click', '.slicewp-input-copy, .slicewp-copy-creative', function(e) {

        e.preventDefault();

        var $this = $(this);

        if ( $this.hasClass( 'slicewp-copy-creative' ) ) {

            var $elem = $this.closest( '.slicewp-card' ).find( 'textarea, input[type=text]' ).first().clone();

            $elem.css({ 'position' : 'absolute', 'top' : -10000, 'left': - 10000 });

            $('html').append( $elem );

            $elem.select();

            document.execCommand('copy');

            $elem.remove();

        } else {

            $this.siblings( 'textarea, input[type=text]' ).select();
            document.execCommand('copy');

        }

        $this.find( '.slicewp-input-copy-label' ).hide();
        $this.find( '.slicewp-input-copy-label-copied' ).show();

        setTimeout( function() {

            $this.find( '.slicewp-input-copy-label' ).show();
            $this.find( '.slicewp-input-copy-label-copied' ).hide();

        }, 2000 )

    });


    /**
	 * Checks if the provided URL is valid
     *
     * @param string url
     *
     * @return bool
	 *
	 */
    function is_valid_url( url ) {

        var regex = new RegExp( /^(https?|s):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i );

        return regex.test( url );

    }

    /**
     * Checks if the two provided URLs are from the same domain
     *
     * @param string base_url
     * @param string custom_url
     *
     * @return bool
     *
     */
    function is_same_domain( base_url, custom_url ) {

        base_url   = base_url.replace('http://','').replace('https://','').replace('www.','').split(/[/?#]/)[0];
        custom_url = custom_url.replace('http://','').replace('https://','').replace('www.','').split(/[/?#]/)[0];

        return ( base_url == custom_url || base_url.indexOf( custom_url ) != -1 || custom_url.indexOf( base_url ) != -1 );

    }

    /**
	 * Generate Affiliate Link
	 *
	 */
    $(document).on( 'click', '.slicewp-generate-affiliate-link', function(e) {

        e.preventDefault();

        var site_url   = $('#slicewp-affiliate-link').val() || window.location.origin.replace(/(^\w+:|^)\/\//, '');
        var custom_url = $('#slicewp-affiliate-custom-link-input').val();

        $('#slicewp-affiliate-custom-link-input-empty').hide();
        $('#slicewp-affiliate-custom-link-input-invalid-url').hide();
        $('.slicewp-affiliate-custom-link-output').hide();

        if( ! custom_url ) {

            $('#slicewp-affiliate-custom-link-input-empty').show();

        } else if ( ( ! is_valid_url( custom_url ) ) || ( ! is_same_domain( site_url, custom_url ) ) ) {

            $('#slicewp-affiliate-custom-link-input-invalid-url').show();

        } else {

            var query_arg              = $('#slicewp-affiliate-account').attr( 'data-affiliate-keyword' );
            var query_arg_value        = $('#slicewp-affiliate-account').attr( 'data-affiliate-keyword-value' );
			var affiliate_friendly_url = $('#slicewp-affiliate-account').attr( 'data-affiliate-friendly-url' );

			if ( affiliate_friendly_url ) 
				$('#slicewp-affiliate-custom-link-output').val( process_friendly_url( query_arg, query_arg_value, custom_url ) );
			else
            	$('#slicewp-affiliate-custom-link-output').val( add_query_arg( query_arg, query_arg_value, custom_url ) );

			$('.slicewp-affiliate-custom-link-output').show();

        }

    });

    
    /**
     * Changes the "state" field to match the value of the previous "country" field
     *
     */
    $(document).on( 'change refresh', '.slicewp-field-wrapper[data-type="country"] select', function() {

        // Bail if the next field isn't a "state" type field
        if( $(this).closest( '.slicewp-field-wrapper' ).next('.slicewp-field-wrapper').attr( 'data-type' ) != 'state' )
            return false;

        // Bail if we don't have the country select data
        if( typeof slicewp_country_select == 'undefined' )
            return false;

        var country_code = $(this).val();

        var $state_field = $(this).closest( '.slicewp-field-wrapper' ).next('.slicewp-field-wrapper').find( 'input, select' ).first();

        var field_id    = $state_field.attr( 'id' ),
            field_name  = $state_field.attr( 'name' ),
            field_class = $state_field.attr( 'class' ),
            field_data_value    = $state_field.attr( 'data-value' ),
            field_data_required = $state_field.attr( 'data-required' ),
            field_is_disabled   = $state_field.is( ':disabled' );

        if( slicewp_country_select[country_code] ) {

            if( ! $.isEmptyObject( slicewp_country_select[country_code] ) ) {

                $new_state_field = $('<select></select>')
                    .attr( 'id', field_id )
                    .attr( 'name', field_name )
                    .attr( 'class', field_class )
                    .attr( 'data-value', field_data_value )
                    .attr( 'data-required', field_data_required );

                if( field_is_disabled )
                    $new_state_field.attr( 'disabled', true );

                $new_state_field.append( '<option value="">Select...</option>' );

                $.each( slicewp_country_select[$(this).val()], function( index ) {
                    $new_state_field.append( '<option value="' + index + '" ' + ( field_data_value == index ? 'selected' : '' ) + '>' + slicewp_country_select[country_code][index] + '</option>' );
                });

                $state_field.closest('.slicewp-field-wrapper').show();

            } else {

                $new_state_field = $('<input />')
                    .attr( 'type', 'hidden' )
                    .attr( 'id', field_id )
                    .attr( 'name', field_name )
                    .attr( 'class', field_class )
                    .attr( 'value', field_data_value )
                    .attr( 'data-value', field_data_value )
                    .attr( 'data-required', field_data_required );

                if( field_is_disabled )
                    $new_state_field.attr( 'disabled', true );

                $state_field.closest('.slicewp-field-wrapper').hide();

            }

        } else {

            $new_state_field = $('<input />')
                .attr( 'type', 'text' )
                .attr( 'id', field_id )
                .attr( 'name', field_name )
                .attr( 'class', field_class )
                .attr( 'value', field_data_value )
                .attr( 'data-value', field_data_value )
                .attr( 'data-required', field_data_required );

            if( field_is_disabled )
                $new_state_field.attr( 'disabled', true );

            $state_field.closest('.slicewp-field-wrapper').show();

        }

        $state_field.replaceWith( $new_state_field );

    });

    $('.slicewp-field-wrapper[data-type="country"] select').trigger( 'change' );


    /**
     * Trigger browse files on file upload drag and drop area click.
     *
     */
    $(document).on( 'click', '.slicewp-field-drag-drop-area', function(e) {

        e.stopPropagation();

        $(this).find( 'input[type="file"]' ).trigger( 'click' );

    });

    $(document).on( 'click', '.slicewp-field-drag-drop-area input[type="file"]', function(e) {

        e.stopPropagation();

    });


    /**
     * Handle files on drag and drop.
     *
     */
    $(document).on( 'dragenter dragover dragleave drop', '.slicewp-field-drag-drop-area', function(e) {

        e.preventDefault();
        e.stopPropagation();

    });

    $(document).on( 'dragenter dragover', '.slicewp-field-drag-drop-area', function() {

        $(this).addClass( 'slicewp-highlight' );

    });

    $(document).on( 'dragleave drop', '.slicewp-field-drag-drop-area', function() {

        $(this).removeClass( 'slicewp-highlight' );

    });

    $(document).on( 'drop', '.slicewp-field-drag-drop-area', function(e) {

        var $input = $(this).find( 'input[type="file"]' ).first();

        $input[0].files = e.originalEvent.dataTransfer.files;
        $input.trigger( 'change' );

    });


    /**
     * @todo mandle maximum files count on the front-end. a span with 2/5 and block new uploads if 5/5
     *       if unlimited, don't add the span
     *
     */


    /**
     * Append new selected files to the "file" field's files list.
     *
     */
    var fields = [];

    $(document).on( 'change', '.slicewp-field-drag-drop-area input[type="file"]', function() {

        var name = this.name.replace( '[]', '' );

        // Add file item DOM elements.
        for ( var i = 0; i < this.files.length; i++ ) {

            var html  = '<div class="slicewp-field-file-item" data-new="true">';
                    html += '<span class="slicewp-field-file-item-name">' + this.files.item(i).name + '</span>';
                    html += '<span class="slicewp-field-file-item-size">(' + bytes_to_size( this.files.item(i).size ) + ')</span>';
                    html += '<a href="#" class="slicewp-field-file-item-remove"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" style="fill: none;" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>';
                html += '</div>';

            $(this).closest( '.slicewp-field-inner' ).find( '.slicewp-field-file-list' ).append( html );

        }

        // Add the file to the DataTransfer object.
        fields[name] = fields[name] || new DataTransfer();

        for ( var file of this.files ) {

            fields[name].items.add( file );

        }

        this.files = fields[name].files;

    });


    /**
     * Remove a file from the "file" field's file list.
     *
     */
    $(document).on( 'click', '.slicewp-field-file-item-remove', function(e) {

        e.preventDefault();

        var $input = $(this).closest( '.slicewp-field-inner' ).find( 'input[type="file"]' ).first();

        // If the item is freshly added through the uploader, remove it from the files from the datatransfer.
        if ( $(this).closest( '.slicewp-field-file-item' ).find( 'input[type="hidden"]' ).length == 0 ) {

            // Get the name of the input file field.
            var name = $input.attr('name').replace( '[]', '' );

            // Remove the file from the DataTransfer object.
            var index = $(this).closest('.slicewp-field-file-list').find( '.slicewp-field-file-item[data-new="true"]' ).index( $(this).closest( '.slicewp-field-file-item' ) );

            fields[name].items.remove( index );

            $input[0].files = fields[name].files;

        }

        // Remove DOM element.
        $(this).closest( '.slicewp-field-file-item' ).remove();

        // If the file field is a single one, show the input field.
        if ( typeof $input.attr( 'multiple' ) == 'undefined' ) {
            $input.show();
        }


    });


    /**
     * Opens the creative overlay when clicking on the "View" creative action.
     *
     */
    $(document).on( 'click', '.slicewp-show-creative', function(e) {

        e.preventDefault();

        var $this = $(this);

        $this.closest( '.slicewp-card' ).find( '.slicewp-creative-overlay' ).addClass( 'slicewp-prepare-open' );

        setTimeout( function() {

            $this.closest( '.slicewp-card' ).find( '.slicewp-creative-overlay' ).removeClass( 'slicewp-prepare-open' ).addClass( 'slicewp-opened' );

        }, 100 );

    });


    /**
     * Closes the creative overlay when clicking on the close overlay button.
     *
     */
    $(document).on( 'click', '.slicewp-creative-overlay-close', function(e) {

        e.preventDefault();

        var $this = $(this);

        $this.closest( '.slicewp-creative-overlay' ).addClass( 'slicewp-prepare-close' ).removeClass( 'slicewp-opened' );

        setTimeout( function() {

            $this.closest( '.slicewp-creative-overlay' ).removeClass( 'slicewp-prepare-close' );

        }, 100 );

    });


	/**
	 * Adds the the friendly affiliate parameters to the url
	 *
	 */
	function process_friendly_url( param, value, url ) {

		// Save the hash, if it's present.
        var hash = url.split('#')[1];

        url = url.split('#')[0];

		// Check if this is already an affiliate friendly url
		var re = new RegExp( "([\/]" + param + "[\/][^?]*)" ), match = re.exec( url );

		// Check if we have any parameters in the url
		var re2 = new RegExp( "([?].*)" ), match2 = re2.exec( url );

		// Remove the affiliate friendly endpoint
		if ( match && match[0] )
			url = url.replace( match[0], '' );

		// Remove the url parameters
		if ( match2 && match2[0] )
			url = url.replace( match2[0], '' );

		// Check if we have the affiliate parameter without affiliate id in the url
		var re3 = new RegExp( "([\/]" + param + "$)" ), match3 = re3.exec( url );

		// Remove the affiliate parameter
		if ( match3 && match3[0] )
			url = url.replace( match3[0], '' );

		// Remove the trailing slash
		url = url.replace( /\/+$/, '' );

		// Add the affiliate friendly endpoint
		url = url + '/' + param + '/' + value + '/';

		// Add back the parameters to the url
		if ( match2 && match2[0] )
			url = url + match2[0];

        // Add back the hash if it exists.
        if ( hash )
            url += '#' + hash;

		return url;

	}

});