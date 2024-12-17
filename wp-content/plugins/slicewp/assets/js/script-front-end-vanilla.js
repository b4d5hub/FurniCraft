var slicewp_front_end = function() {

    /**
     * Event handler for dynamically added elements.
     * 
     * @param {string}   event_types
     * @param {string}   selector
     * @param {function} callback
     * 
     */
    function on( event_types, selector, callback ) {

        var event_types = event_types.split( ' ' );

        for ( var i = 0; i < event_types.length; i++ ) {

            document.body.addEventListener( event_types[i], function( event ) {

                for ( var target = event.target; target && target != this; target = target.parentNode ) {
    
                    if ( target.matches( selector ) ) {
    
                        callback.call( target, event );
                        break;
    
                    }
    
                }
    
            }, false );

        }

    }

    /**
     * Strips one query argument from a given URL string.
     *
     */
    function remove_query_arg( key, sourceURL ) {

        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

        if ( queryString !== "" ) {
            params_arr = queryString.split("&");
            for ( var i = params_arr.length - 1; i >= 0; i -= 1 ) {
                param = params_arr[i].split("=")[0];
                if ( param === key ) {
                    params_arr.splice(i, 1);
                }
            }

            rtn = rtn + "?" + params_arr.join("&");

        }

        if ( rtn.split("?")[1] == "" ) {
            rtn = rtn.split("?")[0];
        }

        return rtn;

    }

    /**
     * Adds an argument name, value pair to a given URL string.
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
	 * Checks if the provided URL is valid.
     *
     * @param {string} url
     *
     * @return bool
	 *
	 */
    function is_valid_url( url ) {

        var regex = new RegExp( /^(https?|s):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i );

        return regex.test( url );

    }


    /**
     * Checks if the two provided URLs are from the same domain.
     *
     * @param {string} base_url
     * @param {string} custom_url
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
     * Removes the "slicewp-updated" query argument on page load.
     *
     */
    var url = window.location.href;

    if ( url.indexOf( 'slicewp-updated' ) != -1 ) {

        url = remove_query_arg( 'slicewp-updated', url );
    
        window.history.replaceState( {}, '', url );

    }


    /**
     * Prevents double form submit requests.
     *
     */
    on( 'submit', '.slicewp-form', function( e ) {
        
        if ( e.target.classList.contains( 'slicewp-is-submitting' ) ) {

            e.preventDefault();

        } else {

            e.target.classList.add( 'slicewp-is-submitting' );

        }

    });


    /**
	 * Tab navigation.
	 *
	 */
    on( 'click', '.slicewp-nav-tab', function( e ) {

        var nav_tab = this.getAttribute( 'data-slicewp-tab' );

        if ( ! nav_tab ) {
            return true;
        }

        e.preventDefault();

        var nav_tabs = document.querySelectorAll( '.slicewp-nav-tab' );
        var tabs     = document.querySelectorAll( '.slicewp-tab' );

        // Remove the "slicewp-active" class from nav tabs.
        for ( var j = 0; j < nav_tabs.length; j++ ) {

            nav_tabs[j].classList.remove( 'slicewp-active' );

        }

        // Add the "slicewp-active" class to the current nav tab.
        this.classList.add( 'slicewp-active' );

        // Remove the "slicewp-active" class from the tabs.
        for ( var j = 0; j < tabs.length; j++ ) {

            tabs[j].classList.remove( 'slicewp-active' );

        }

        // Add the "slicewp-active" class to the correct tab.
        document.querySelector( '.slicewp-tab[data-slicewp-tab="' + nav_tab + '"]' ).classList.add( 'slicewp-active' );

        // Change "affiliate-account-tab" query var.
        var url = window.location.href;

        url = remove_query_arg( 'affiliate-account-tab', url );
        url = add_query_arg( 'affiliate-account-tab', nav_tab, url );

        window.history.replaceState( {}, '', url );

    });


    /**
	 * Generate affiliate link.
	 *
	 */
    on( 'click', '.slicewp-generate-affiliate-link', function( e ) {

        e.preventDefault();

        var site_url   = ( document.querySelector( '#slicewp-affiliate-link' ) ? document.querySelector( '#slicewp-affiliate-link' ).value : window.location.origin.replace( /(^\w+:|^)\/\//, '' ) );
        var custom_url = document.querySelector( '#slicewp-affiliate-custom-link-input' ).value;

        document.querySelector( '#slicewp-affiliate-custom-link-input-empty' ).style.display = 'none';
        document.querySelector( '#slicewp-affiliate-custom-link-input-invalid-url' ).style.display = 'none';
        document.querySelector( '.slicewp-affiliate-custom-link-output' ).style.display = 'none';

        if ( ! custom_url ) {
            
            document.querySelector( '#slicewp-affiliate-custom-link-input-empty' ).style.display = '';

        } else if ( ( ! is_valid_url( custom_url ) ) || ( ! is_same_domain( site_url, custom_url ) ) ) {

            document.querySelector( '#slicewp-affiliate-custom-link-input-invalid-url' ).style.display = '';

        } else {

			var query_arg              = document.querySelector( '#slicewp-affiliate-account' ).getAttribute( 'data-affiliate-keyword' );
            var query_arg_value        = document.querySelector( '#slicewp-affiliate-account' ).getAttribute( 'data-affiliate-keyword-value' );
			var affiliate_friendly_url = document.querySelector( '#slicewp-affiliate-account' ).getAttribute( 'data-affiliate-friendly-url' );

			// pretty urls
			if ( affiliate_friendly_url ) {
                document.querySelector( '#slicewp-affiliate-custom-link-output' ).value = process_friendly_url( query_arg, query_arg_value, custom_url );
            } else {
                document.querySelector( '#slicewp-affiliate-custom-link-output' ).value = add_query_arg( query_arg, query_arg_value, custom_url );
            }

            document.querySelector( '.slicewp-affiliate-custom-link-output' ).style.display = '';

        }

    });


    /**
     * Opens the creative overlay when clicking on the "View" creative action.
     *
     */
    on( 'click', '.slicewp-show-creative', function( e ) {

        e.preventDefault();

        this.blur();

        var overlay = this.closest( '.slicewp-card' ).querySelector( '.slicewp-global-overlay' );

        if ( overlay ) {
            window.slicewp.show_overlay( overlay );
        }

    });


    /**
	 * Copy inputs or creative textarea.
	 *
	 */
    on( 'click', '.slicewp-input-copy, .slicewp-copy-creative', function( e ) {

        e.preventDefault();

        var target = this;

        if ( target.classList.contains( 'slicewp-copy-creative' ) ) {

            var elem = target.closest( '.slicewp-card' ).querySelector( 'textarea, input[type="text"]' ).cloneNode( true );

            elem.style.position = 'absolute';
            elem.style.top      = '-10000px';
            elem.style.left     = '-10000px';

            document.querySelector( 'html' ).appendChild( elem );

            elem.select();

            document.execCommand( 'copy' );

            elem.parentNode.removeChild( elem );

        } else {

            target.parentNode.querySelector( 'textarea, input[type=text]' ).select();
            document.execCommand( 'copy' );

        }

        target.querySelector( '.slicewp-input-copy-label' ).style.display = 'none';
        target.querySelector( '.slicewp-input-copy-label-copied' ).style.display = 'inline';

        setTimeout( function() {

            target.querySelector( '.slicewp-input-copy-label' ).style.display = '';
            target.querySelector( '.slicewp-input-copy-label-copied' ).style.display = 'none';

        }, 2000 );

    });


    /**
     * Changes the "state" field to match the value of the previous "country" field.
     *
     */
    on( 'change refresh', '.slicewp-field-wrapper[data-type="country"] select', function( e ) {

        // Bail if the next field isn't a "state" type field.
        if ( this.closest( '.slicewp-field-wrapper' ).nextElementSibling.getAttribute( 'data-type' ) != 'state' ) {
            return false;
        }

        // Bail if we don't have the country select data.
        if ( typeof slicewp_country_select == 'undefined' ) {
            return false;
        }

        var country_code = this.value;
        var state_field  = this.closest( '.slicewp-field-wrapper' ).nextElementSibling.querySelector( 'input, select' );

        var field_id    = ( state_field.getAttribute( 'id' ) ? state_field.getAttribute( 'id' ) : '' ),
            field_name  = ( state_field.getAttribute( 'name' ) ? state_field.getAttribute( 'name' ) : '' ),
            field_class = ( state_field.getAttribute( 'class' ) ? state_field.getAttribute( 'class' ) : '' ),
            field_data_value    = ( state_field.getAttribute( 'data-value' ) ? state_field.getAttribute( 'data-value' ) : '' ),
            field_data_required = ( state_field.getAttribute( 'data-required' ) ? state_field.getAttribute( 'data-required' ) : '' ),
            field_is_disabled   = state_field.disabled;

        if ( slicewp_country_select[country_code] ) {

            if ( Object.keys( slicewp_country_select[country_code] ).length > 0 ) {

                // Create new state select.
                var new_state_field = document.createElement( 'select' );

                new_state_field.setAttribute( 'id', field_id );
                new_state_field.setAttribute( 'name', field_name );
                new_state_field.setAttribute( 'class', field_class );
                new_state_field.setAttribute( 'data-value', field_data_value );
                new_state_field.setAttribute( 'data-required', field_data_required );
                new_state_field.disabled = field_is_disabled;

                // Create and append empty first option.
                var option = document.createElement( 'option' );

                option.value     = '';
                option.innerHTML = 'Select...'

                new_state_field.appendChild( option );

                // Create and append states options.
                for ( var state_code in slicewp_country_select[country_code] ) {

                    var option = document.createElement( 'option' );

                    option.value     = state_code;
                    option.innerHTML = slicewp_country_select[country_code][state_code];

                    if ( state_code == field_data_value ) {
                        option.selected = true;
                    }

                    new_state_field.appendChild( option );

                }

                state_field.closest( '.slicewp-field-wrapper' ).style.display = '';

            } else {

                // Create hidden input.
                var new_state_field = document.createElement( 'input' );

                new_state_field.setAttribute( 'type', 'hidden' );
                new_state_field.setAttribute( 'id', field_id );
                new_state_field.setAttribute( 'name', field_name );
                new_state_field.setAttribute( 'class', field_class );
                new_state_field.setAttribute( 'value', field_data_value );
                new_state_field.setAttribute( 'data-value', field_data_value );
                new_state_field.setAttribute( 'data-required', field_data_required );
                new_state_field.disabled = field_is_disabled;

                state_field.closest( '.slicewp-field-wrapper' ).style.display = 'none';

            }

        } else {

            // Create text input.
            var new_state_field = document.createElement( 'input' );

            new_state_field.setAttribute( 'type', 'text' );
            new_state_field.setAttribute( 'id', field_id );
            new_state_field.setAttribute( 'name', field_name );
            new_state_field.setAttribute( 'class', field_class );
            new_state_field.setAttribute( 'value', field_data_value );
            new_state_field.setAttribute( 'data-value', field_data_value );
            new_state_field.setAttribute( 'data-required', field_data_required );
            new_state_field.disabled = field_is_disabled;

            state_field.closest( '.slicewp-field-wrapper' ).style.display = '';

        }

        state_field.parentNode.replaceChild( new_state_field, state_field );

    });

    if ( typeof document.body.dispatchEvent === 'function' ) {

        var country_fields = document.querySelectorAll( '.slicewp-field-wrapper[data-type="country"] select' );

        for ( var i = 0; i < country_fields.length; i++ ) {

            country_fields[i].dispatchEvent( new Event( 'change', { 'bubbles' : true } ) );

        }

    }


    /**
     * Fill in slicewp_hnp.
     * 
     */
    if ( document.querySelector( '[name="slicewp_hnp"]' ) ) {

        setTimeout( function() {
            document.querySelector( '[name="slicewp_hnp"]' ).value = 't7i5s2g1d8n4h9y6xpv0';
        }, 1500 );

    }


    /**
     * Trigger browse files on file upload drag and drop area click.
     *
     */
    on( 'click', '.slicewp-field-drag-drop-area', function( e ) {

        e.stopPropagation();

        if ( typeof document.body.dispatchEvent === 'function' ) {

            this.querySelector( 'input[type="file"]' ).click();

        }

    });


    /**
     * Handle files on drag and drop.
     *
     */
    on( 'dragenter dragover dragleave drop', '.slicewp-field-drag-drop-area', function( e ) {

        e.preventDefault();
        e.stopPropagation();

    });

    on( 'dragenter dragover', '.slicewp-field-drag-drop-area', function() {

        this.classList.add( 'slicewp-highlight' );

    });

    on( 'dragleave drop', '.slicewp-field-drag-drop-area', function() {

        this.classList.remove( 'slicewp-highlight' );

    });

    on( 'drop', '.slicewp-field-drag-drop-area', function( e ) {

        var input = this.querySelector( 'input[type="file"]' );

        input.files = e.dataTransfer.files;

        if ( typeof document.body.dispatchEvent === 'function' ) {

            input.dispatchEvent( new Event( 'change', { 'bubbles' : true } ) );
    
        }

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

    on( 'change', '.slicewp-field-drag-drop-area input[type="file"]', function() {

        var name = this.name.replace( '[]', '' );

        // Add file item DOM elements.
        for ( var i = 0; i < this.files.length; i++ ) {

            var html  = '<div class="slicewp-field-file-item" data-new="true">';
                    html += '<span class="slicewp-field-file-item-name">' + this.files.item(i).name + '</span>';
                    html += '<span class="slicewp-field-file-item-size">(' + bytes_to_size( this.files.item(i).size ) + ')</span>';
                    html += '<a href="#" class="slicewp-field-file-item-remove" title="Remove item"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" style="fill: none;" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>';
                html += '</div>';

            this.closest( '.slicewp-field-inner' ).querySelector( '.slicewp-field-file-list' ).innerHTML += html;

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
    on( 'click', '.slicewp-field-file-item-remove', function( e ) {

        e.preventDefault();

        var input = this.closest( '.slicewp-field-inner' ).querySelector( 'input[type="file"]' );
        
        // If the item is freshly added through the uploader, remove it from the files from the datatransfer.
        if ( this.closest( '.slicewp-field-file-item' ).querySelectorAll( 'input[type="hidden"]' ).length == 0 ) {

            // Get the name of the input file field.
            var name = input.getAttribute( 'name' ).replace( '[]', '' );

            // Remove the file from the DataTransfer object.
            var index = Array.prototype.slice.call( this.closest( '.slicewp-field-file-list' ).querySelectorAll( '.slicewp-field-file-item[data-new="true"]' ) ).indexOf( this.closest( '.slicewp-field-file-item' ) );

            fields[name].items.remove( index );

            input.files = fields[name].files;

        }

        // Remove DOM element.
        this.closest( '.slicewp-field-file-item' ).parentNode.removeChild( this.closest( '.slicewp-field-file-item' ) );

        // If the file field is a single one, show the input field.
        if ( ! input.getAttribute( 'multiple' ) ) {

            input.style.display = '';

        }

    });


    /**
     * Initialize date range pickers.
     * 
     */
    var date_picker_elements = document.querySelectorAll( '.slicewp-date-picker' );
    var pickers = [];

    for ( var i = 0; i < date_picker_elements.length; i++ ) {

        var wrapper = date_picker_elements[i].closest( '.slicewp-date-picker-wrapper' );

        wrapper.setAttribute( 'data-index', i );

        if ( date_picker_elements[i].getAttribute( 'data-sync-id' ) ) {
            wrapper.setAttribute( 'data-sync-id', parseInt( date_picker_elements[i].getAttribute( 'data-sync-id' ) ) );
        }

        var picker = new SliceWP_Litepicker({
            element         : date_picker_elements[i],
            inlineMode      : true,
            singleMode      : false,
            lang            : document.querySelector( 'html' ).getAttribute( 'lang' ),
            numberOfMonths  : 2,
            numberOfColumns : 2,
            switchingMonths : 1,
            format          : 'D MMM, YYYY',
            showTooltip     : false,
            buttonText      : { 'previousMonth' : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>', 'nextMonth' : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>' },
            setup           : function( picker ) {

                picker.on( 'before:show', function() {

                    if ( window.innerWidth < 721 ) {

                        picker.options.numberOfMonths = 1;
                        picker.options.numberOfColumns = 1;

                    } else {

                        var date = picker.getDate();

                        if ( ! date ) {
                            
                            date = new Date();

                            date.setMonth( date.getMonth() - 1 );
                            picker.gotoDate( date );

                        }

                    }
                    
                });

                picker.on( 'selected', function( date_start, date_end ) {
                    
                    var options = { year: 'numeric', month: 'short', day: 'numeric' };
                    var locale  = document.querySelector( 'html' ).getAttribute( 'lang' );
                    var wrapper = this.options.element.closest( '.slicewp-date-picker-wrapper' );

                    // Set span input value.
                    if ( this.options.selected_date_range && typeof window.slicewp.predefined_date_ranges[this.options.selected_date_range] != 'undefined' ) {
                        wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-date-range' ).innerHTML = window.slicewp.predefined_date_ranges[this.options.selected_date_range];
                    } else {
                        wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-date-range' ).innerHTML = wrapper.querySelector( '.slicewp-date-picker-predefined-date-range[data-range="custom"]' ).innerHTML;
                    }

                    wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-dates' ).innerHTML = date_start.dateInstance.toLocaleDateString( locale, options ) + ' - ' + date_end.dateInstance.toLocaleDateString( locale, options );
                    wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-dates' ).style.display = 'block';

                    // Hide the date picker modal.
                    wrapper.classList.remove( 'slicewp-is-open' );

                    // Add the dates to the hidden inputs.
                    var formatted_date_start = date_start.toLocaleString( 'default', { year: 'numeric' } ) + '-' + 
                                               date_start.toLocaleString( 'default', { month: '2-digit' } ) + '-' +
                                               date_start.toLocaleString( 'default', { day: '2-digit' } );
                    
                    var formatted_date_end   = date_end.toLocaleString( 'default', { year: 'numeric' } ) + '-' +
                                               date_end.toLocaleString( 'default', { month: '2-digit' } ) + '-' +
                                               date_end.toLocaleString( 'default', { day: '2-digit' } );
                    
                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-range' ).value = ( this.options.selected_date_range ? this.options.selected_date_range : 'custom' );
                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-start' ).value = formatted_date_start;
                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-end' ).value = formatted_date_end;

                    // Set the go to date.
                    var date_go_to = date_end;

                    date_go_to.setDate( 15 );

                    if ( this.options.numberOfMonths == 2 ) {
                        date_go_to.setMonth( date_go_to.getMonth() - 1 );
                    }

                    if ( this.options.trigger_sync ) {

                        for ( var i in pickers ) {

                            if ( i == parseInt( wrapper.getAttribute( 'data-index' ) ) ) {
                                continue;
                            }

                            if ( ! wrapper.getAttribute( 'data-sync-id' ) ) {
                                continue;
                            }
                
                            if ( pickers[i].options.element.closest( '.slicewp-date-picker-wrapper' ).getAttribute( 'data-sync-id' ) != wrapper.getAttribute( 'data-sync-id' ) ) {
                                continue;
                            }
                
                            pickers[i].options.selected_date_range = this.options.selected_date_range;
                            pickers[i].options.trigger_sync = false;
                            pickers[i].setDateRange( this.getStartDate(), this.getEndDate() );
                            pickers[i].gotoDate( date_go_to );
                            pickers[i].options.trigger_sync = true;
                
                        }

                    }

                    // Submit the form if it's AJAX powered.
                    if ( this.options.was_selected ) {

                        var parent_form = this.options.element.closest( 'form' );

                        if ( typeof parent_form != 'undefined' ) {

                            if ( parent_form.action.indexOf( '_action_ajax_' ) >= 0 ) {
                                apply_dashboard_filters( parent_form );
                            }

                        }
                        
                    }

                    this.options.was_selected        = true;
                    this.options.selected_date_range = '';

                    this.gotoDate( date_go_to );

                });

                picker.on( 'clear:selection', function() {

                    var wrapper = this.options.element.closest( '.slicewp-date-picker-wrapper' );

                    // Set span input value.
                    wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-date-range' ).innerHTML = wrapper.querySelector( '.slicewp-date-picker-predefined-date-range[data-range="all_time"]' ).innerHTML;
                    wrapper.querySelector( '.slicewp-date-picker-input span.slicewp-date-picker-input-dates' ).style.display = 'none';

                    // Hide the date picker modal.
                    wrapper.classList.remove( 'slicewp-is-open' );

                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-range' ).value = '';
                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-start' ).value = '';
                    wrapper.querySelector( 'input.slicewp-date-picker-input-date-end' ).value = '';

                    // Reset the selected date range.
                    this.options.selected_date_range = '';

                    var date_go_to = new Date();
                    date_go_to.setMonth( date_go_to.getMonth() - 1, 1 );

                    // Sync all date range pickers on the page.
                    if ( this.options.trigger_sync ) {

                        for ( var i in pickers ) {

                            if ( i == parseInt( wrapper.getAttribute( 'data-index' ) ) ) {
                                continue;
                            }

                            if ( ! wrapper.getAttribute( 'data-sync-id' ) ) {
                                continue;
                            }
                
                            if ( pickers[i].options.element.closest( '.slicewp-date-picker-wrapper' ).getAttribute( 'data-sync-id' ) != wrapper.getAttribute( 'data-sync-id' ) ) {
                                continue;
                            }
                
                            pickers[i].options.selected_date_range = '';
                            pickers[i].options.trigger_sync = false;
                            pickers[i].clearSelection();
                            pickers[i].gotoDate( date_go_to );
                            pickers[i].options.trigger_sync = true;
                
                        }

                    }

                    this.gotoDate( date_go_to );

                });

            }

        });

        picker.options.trigger_sync = true;

        pickers.push( picker );
        
    }

    // Set the dates for the datepickers.
    var processed_sync_ids = [];

    for ( var i = 0; i < pickers.length; i++ ) {

        var wrapper = pickers[i].options.element.closest( '.slicewp-date-picker-wrapper' );
        var sync_id = wrapper.getAttribute( 'data-sync-id' ); 

        // If this ID was processed, move to the next datepicker.
        if ( processed_sync_ids.includes( sync_id ) ) {
            continue;
        }

        // Mark sync ID as processed.
        processed_sync_ids.push( sync_id );

        // Set date start and end.
        var date_range = wrapper.querySelector( 'input.slicewp-date-picker-input-date-range' ).value;
        var date_start = wrapper.querySelector( 'input.slicewp-date-picker-input-date-start' ).value;
        var date_end   = wrapper.querySelector( 'input.slicewp-date-picker-input-date-end' ).value;

        if ( date_start != '' && date_end != '' ) {
            pickers[i].options.selected_date_range = date_range;
            pickers[i].setDateRange( date_start, date_end );
        }

    }


    /**
     * Select date start and date end based on predefined options.
     * 
     */
    on( 'click', '.slicewp-date-picker-predefined-date-range', function(e) {

        e.preventDefault();

        if ( ! this.getAttribute( 'data-range' ) ) {
            return false;
        }

        var day_in_ms   = 24 * 60 * 60 * 1000;

        var today       = new Date();
        var today_year  = today.toLocaleString( 'default', { year: 'numeric' } );
        var today_month = today.toLocaleString( 'default', { month: '2-digit' } );
        var today_day   = today.toLocaleString( 'default', { day: '2-digit' } );

        var date_start = '';
        var date_end   = '';

        var range = this.getAttribute( 'data-range' );

        if ( range == 'past_7_days' ) {

            date_start = new Date( today - day_in_ms * 6 );
            date_end   = today;

        }

        if ( range == 'past_30_days' ) {

            date_start = new Date( today - day_in_ms * 29 );
            date_end   = today;

        }

        if ( range == 'week_to_date' ) {

            var day_of_week = today.getDay();
            var diff        = today.getDate() - day_of_week + ( day_of_week === 0 ? -6 : 1 );

            date_start = new Date();
            
            date_start = new Date( date_start.setDate( diff ) );
            date_end   = today;

        }

        if ( range == 'month_to_date' ) {

            date_start = new Date( today_year + '-' + today_month + '-01' );
            date_end   = today;

        }

        if ( range == 'year_to_date' ) {

            date_start = new Date( today_year + '-01-01' );
            date_end = today;

        }

        if ( range == 'last_week' ) {

            var last_week_date = new Date( today - day_in_ms * 7 );
            var day_of_week    = last_week_date.getDay();
            var diff           = last_week_date.getDate() - day_of_week + ( day_of_week === 0 ? -6 : 1 );

            date_start = new Date();
            date_start = new Date( date_start.setDate( diff ) );

            date_end   = new Date( date_start );
            date_end   = new Date( date_end.setDate( date_end.getDate() + 6 ) );

        }

        if ( range == 'last_month' ) {

            date_end   = new Date( today - day_in_ms * today_day );
            date_start = new Date( date_end.getFullYear(), date_end.getMonth() );

        }

        if ( range == 'last_year' ) {

            date_start = new Date( ( today_year - 1 ) + '-01-01' );
            date_end   = new Date( ( today_year - 1 ) + '-12-31' );

        }

        var index = parseInt( this.closest( '.slicewp-date-picker-wrapper' ).getAttribute( 'data-index' ) );

        if ( date_start != '' && date_end != '' ) {

            pickers[index].options.selected_date_range = ( range != 'all_time' ? range : '' );
            pickers[index].options.trigger_sync = true;

            pickers[index].setDateRange( date_start, date_end );

        } else {

            pickers[index].options.trigger_sync = true;

            pickers[index].clearSelection();

        }

    });


    /**
     * Opens and closes the date picker when clicking on the date picker span field.
     * 
     */
    on( 'click', '.slicewp-date-picker-input', function(e) {

        e.preventDefault();

        var date_picker_wrapper = this.closest( '.slicewp-date-picker-wrapper' );

        if ( date_picker_wrapper.classList.contains( 'slicewp-is-open' ) ) {

            date_picker_wrapper.classList.remove( 'slicewp-is-open' );

        } else {

            date_picker_wrapper.classList.add( 'slicewp-is-open' );

        }

    });


    /**
     * Submits the wrapping form when "items_per_page" select field is changed.
     * 
     */
    on( 'change', '.slicewp-list-table-per-page-selector select[name="list_table_items_per_page"]', function(e) {

        e.preventDefault();

        this.closest( 'form' ).submit();
        this.setAttribute( 'disabled', true );

    });


    /**
     * Refreshes the data of the affiliate dashboard when applying the filters the affiliate selects in their account.
     * 
     */
    function apply_dashboard_filters( form ) {

        var account_wrapper = form.closest( '#slicewp-affiliate-account' );

        var kpi_wrappers    = account_wrapper.querySelectorAll( '[data-kpi]' );
        var filtrable_cards = account_wrapper.querySelectorAll( '[data-is-filtrable="true"]' );

        // Add loading spinners.
        for ( var i = 0; i < filtrable_cards.length; i++ ) {
            
            if ( filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ) ) {
                continue;
            }
            
            var overlay = document.createElement( 'div' );
            var loader  = document.createElement( 'span' );

            overlay.setAttribute( 'class', 'slicewp-loading-overlay' );
            loader.setAttribute( 'class', 'slicewp-loader' );

            overlay.appendChild( loader );
            filtrable_cards[i].appendChild( overlay );

        }

        setTimeout( function() {
            for ( var i = 0; i < filtrable_cards.length; i++ ) {
                filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ).classList.add( 'slicewp-active' );
            }
        }, 1 );


        // Make call.
        var data = new FormData( form );

        data.append( 'action', 'slicewp_action_ajax_apply_affiliate_dashboard_filters' );
        data.append( 'affiliate_id', parseInt( account_wrapper.getAttribute( 'data-affiliate-id' ) ) );
        data.append( 'slicewp_token', account_wrapper.querySelector( ':scope > [name="slicewp_token"]' ).value );
        
        var request = new XMLHttpRequest();

        request.open( 'POST', window.slicewp.ajaxurl, true );

        request.onload = function () {

            if ( this.status >= 200 && this.status < 400 ) {
                
                var response = JSON.parse( this.response );

                if ( response.success ) {

                    setTimeout( function() {

                        // Update KPIs data.
                        for ( var i = 0; i < kpi_wrappers.length; i++ ) {
                        
                            var kpi_data = response.data['datasets'][kpi_wrappers[i].getAttribute( 'data-kpi' )];
    
                            if ( ! kpi_data ) {
                                continue;
                            }
    
                            kpi_wrappers[i].querySelector( '.slicewp-kpi-value > span:first-of-type' ).innerHTML = ( kpi_data['current_formatted'] ? kpi_data['current_formatted'] : kpi_data['current'] );
                            kpi_wrappers[i].querySelector( '.slicewp-kpi-value .slicewp-kpi-direction > span:last-of-type' ).innerHTML = ( typeof kpi_data['comparison_change'] == 'number' ? kpi_data['comparison_change'] + '%' : '-' );
                            
                            kpi_wrappers[i].querySelector( '.slicewp-kpi-value .slicewp-kpi-direction' ).classList.remove( 'slicewp-positive', 'slicewp-negative' );
    
                            if ( typeof kpi_data['comparison_change_direction'] != 'undefined' ) {
                                kpi_wrappers[i].querySelector( '.slicewp-kpi-value .slicewp-kpi-direction' ).classList.add( 'slicewp-' + kpi_data['comparison_change_direction'] );
                            }
    
                        }

                        // Update the charts.
                        var datasets = [];

                        for ( var index in response.data['datasets'] ) {
                            datasets[index] = response.data['datasets'][index].timeline_current
                        }

                        update_charts_datasets_data( datasets );

                        // Remove the loaders.
                        for ( var i = 0; i < filtrable_cards.length; i++ ) {
                            if ( filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ) ) {
                                filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ).classList.remove( 'slicewp-active' );
                            }
                        }

                        setTimeout( function() {
                            for ( var i = 0; i < filtrable_cards.length; i++ ) {
                                if ( filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ) ) {
                                    filtrable_cards[i].querySelector( '.slicewp-loading-overlay' ).remove();
                                }
                            }
                        }, 175 );

                    }, 250 );

                }

            }

        };

        request.send( data );

    };


	/**
	 * Adds the friendly affiliate parameters to the url
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
		if ( match && match[0] ) {
            url = url.replace( match[0], '' );
        }

		// Remove the url parameters
		if ( match2 && match2[0] ) {
            url = url.replace( match2[0], '' );
        }

		// Check if we have the affiliate parameter without affiliate id in the url
		var re3 = new RegExp( "([\/]" + param + "$)" ), match3 = re3.exec( url );

		// Remove the affiliate parameter
		if ( match3 && match3[0] ) {
            url = url.replace( match3[0], '' );
        }

		// Remove the trailing slash
		url = url.replace( /\/+$/, '' );

		// Add the affiliate friendly endpoint
		url = url + '/' + param + '/' + value + '/';

		// Add back the parameters to the url
		if ( match2 && match2[0] ) {
            url = url + match2[0];
        }

        // Add back the hash if it exists.
        if ( hash ) {
            url += '#' + hash;
        }

		return url;

	}


    /**
     * Returns the chart's default config object.
     *
     */
    function get_chart_default_config() {

        var default_config = {

            // Chart type.
            type: 'line',
    
            // The data.
            data: {
                datasets: []
            },
    
            // Options.
            options: {
    
                locale: document.querySelector( 'html' ).getAttribute( 'lang' ),
    
                layout: {
                    padding: {
                        left: -15,
                        right: -15
                    }
                },
    
                // Aspect ratio.
                maintainAspectRatio: false,
    
                // Elements.
                elements: {
    
                    point: {
                        radius: 0,
                        borderWidth: 0,
                        hoverRadius: 4,
                        hoverBorderWidth: 0
                    },
    
                    // Line element.
                    line: {
                        borderWidth: 2,
                        borderJoinStyle: 'round'
                    },
    
                },
    
                // Animation.
                animation: {
                    duration: 0
                },
    
                // Scales.
                scales: {
    
                    // Horizontal axis.
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            isoWeekday: true,
                            tooltipFormat: 'MMM d, yyyy'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 12,
                            maxRotation: 0,
                            source: 'data'
                        },
                        adapters: {
                            date: {
                                locale: document.querySelector( 'html' ).getAttribute( 'lang' )
                            }
                        }
                    }
    
                },
    
                // Interaction.
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
    
                // Transitions.
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
    
                // Plugins.
                plugins: {
    
                    // Crosshair.
                    crosshair: {
                        line: {
                          color: 'rgba( 200, 215, 225, 1 )',
                          width: 1
                        },
                        snap: {
                            enabled: true
                        },
                        sync: {
                            enabled: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
    
                    // Legend.
                    legend: {
                        display: false
                    },
    
                    // Tooltip.
                    tooltip: {
                        enabled: false,
                        mode: 'index',
                        intersect: false
                    }
    
                }
    
            }
    
        }

        var config = JSON.parse( JSON.stringify( default_config ) );

        config.options.plugins.tooltip.external  = chart_external_tooltip_handler;
        config.options.plugins.tooltip.callbacks = { title: chart_external_tooltip_title_handler, label: chart_external_tooltip_label_handler };

        config.plugins = [html_legend_plugin];

        return config;

    }


    /**
     * Chart HTML legend plugin.
     * 
     */
    var html_legend_plugin = {

        id: 'html_legend',

        afterUpdate( chart, args, options ) {

            var legend_container = chart.canvas.parentNode.parentNode.querySelector( 'div.slicewp-chart-legend' );
            var list_container   = legend_container.querySelector( 'ul' );

            if ( ! list_container ) {
                list_container = document.createElement( 'ul' );
                legend_container.appendChild( list_container );
            }

            // Remove old legend items.
            while ( list_container.firstChild ) {
                list_container.firstChild.remove();
            }

            // Reuse the built-in legendItems generator.
            var items = chart.options.plugins.legend.labels.generateLabels( chart );

            items.forEach( function( item ) {

                var li = document.createElement( 'li' );

                li.onclick = function() {

                    if ( chart.getVisibleDatasetCount() == 1 && ! item.hidden ) {
                        return false;
                    }
                    
                    var type = chart.config.type;

                    if ( type === 'pie' || type === 'doughnut' ) {

                        // Pie and doughnut charts only have a single dataset and visibility is per item
                        chart.toggleDataVisibility( item.index );

                    } else {

                        chart.setDatasetVisibility( item.datasetIndex, ! chart.isDatasetVisible( item.datasetIndex ) );

                    }

                    chart.update();

                    update_chart_scales( chart );

                };

                // Color box.
                var box_span = document.createElement( 'span' );
                box_span.classList.add( 'slicewp-color-box' );

                if ( ! item.hidden ) {
                    box_span.style.background = item.strokeStyle;
                }

                // Text.
                var text_container = document.createElement( 'span' );

                var text = document.createTextNode( item.text );
                text_container.appendChild( text );

                li.appendChild( box_span );
                li.appendChild( text_container );

                list_container.appendChild( li );

            });

        }

    };


    /**
     * Initialize charts.
     * 
     */
    var chart_elements = document.querySelectorAll( '.slicewp-chart' );
    var charts = [];

    for ( var i = 0; i < chart_elements.length; i++ ) {

        var config = get_chart_default_config();

        // Add datasets data to the chart.
        var datasets = JSON.parse( chart_elements[i].getAttribute( 'data-datasets' ) );

        for ( var j in datasets ) {

            var dataset_config = {
                'label'                : datasets[j].label,
                'lineBorderColor'      : hex_to_rgba( datasets[j].color ),
                'pointBackgroundColor' : hex_to_rgba( datasets[j].color ),
                'data'                 : datasets[j].data.day,
                'is_amount'            : ( datasets[j].is_amount ? true : false )
            };
            
            config.data.datasets.push( dataset_config );

        }

        var chart = new Chart( chart_elements[i], config );

        charts.push( chart );

        update_chart_scales( chart );

    }


    /**
     * Update the chart data when switching between time units.
     * 
     */
    on( 'change', '.slicewp-chart-time-unit-selector', function(e) {

        e.preventDefault();

        var chart_id    = this.getAttribute( 'data-id' );
        var datasets    = JSON.parse( this.parentNode.parentNode.querySelector( '.slicewp-chart[data-id="' + chart_id + '"]' ).getAttribute( 'data-datasets' ) );
        var time_unit   = this.value;

        for ( var i = 0; i < charts.length; i++ ) {

            if ( charts[i].canvas.getAttribute( 'data-id' ) != chart_id ) {
                continue;
            }

            var index = 0;

            for ( var j in datasets ) {

                // Set the time unit.
                charts[i].config.options.scales.x.time.unit = time_unit;

                // Update the dataset.
                charts[i].data.datasets[index].data = datasets[j]['data'][time_unit];

                index++;

            }

            charts[i].update();

            update_chart_scales( charts[i] );

        }

    });


    /**
     * Updates the datasets data on the canvas.
     * 
     */
    function update_charts_datasets_data( datasets ) {

        for ( var i = 0; i < charts.length; i++ ) {

            var current_datasets = JSON.parse( charts[i].canvas.getAttribute( 'data-datasets' ) );

            for ( var index in current_datasets ) {

                if ( ! datasets[index] ) {
                    continue;
                }

                current_datasets[index].data = datasets[index];

            }

            charts[i].canvas.setAttribute( 'data-datasets', JSON.stringify( current_datasets ) );

            // Update the chart with the selected time unit.
            var time_unit_selector = document.querySelector( '.slicewp-chart-time-unit-selector[data-id="' + charts[i].canvas.getAttribute( 'data-id' ) + '"]' );

            if ( time_unit_selector ) {

                var time_unit_options     = time_unit_selector.querySelectorAll( 'option' );
                var available_time_units  = Object.keys( current_datasets[index].data );
                var selected_option_index = 0;

                for ( var j = 0; j < time_unit_options.length; j++ ) {

                    if ( available_time_units.indexOf( time_unit_options[j].value ) != -1 ) {
                        time_unit_options[j].removeAttribute( 'disabled' );
                    } else {
                        time_unit_options[j].setAttribute( 'disabled', 'true' );
                        time_unit_options[j].selected = false;
                    }

                    if ( time_unit_options[j].selected ) {
                        selected_option_index = j;
                    }

                }

                time_unit_options[selected_option_index].selected = true;

                time_unit_selector.dispatchEvent( new Event( 'change', { 'bubbles' : true } ) );

            }

        }

    }


    /**
     * Updates the scales of the given chart. 
     *
     */
    function update_chart_scales( chart ) {

        var visible_datasets_data = chart.getSortedVisibleDatasetMetas();

        // Reset the chart's padding.
        chart.config.options.layout.padding = { left: -15, right: -15 };

        // Build chart Y axes for all visible datasets, while maintaining the X axis.
        var scales = {};

        scales.x = chart.config.options.scales.x;
        scales.y = {
            display: false,
            ticks: {
                display: false
            }
        }

        // Reset datasets scales.
        for ( var index in chart.config.data.datasets ) {
            chart.config.data.datasets[index].yAxisID = 'y';
        }

        for ( var index in visible_datasets_data ) {

            var scale_data = get_dataset_scale_data( chart.config.data.datasets[visible_datasets_data[index].index] );

            var y_axis_data = {
                max: scale_data.scale_max,
                beginAtZero: true,
                grid: {
                    color: '#e9eff3',
                    drawBorder: false,
                    tickLength: 25
                },
                ticks: {
                    stepSize: scale_data.scale_step,
                    padding: 15,
                    callback : chart_tick_label_handler
                },
                is_amount: chart.config.data.datasets[visible_datasets_data[index].index].is_amount
            };

            if ( index > 0 ) {
                y_axis_data.position = 'right';
            }

            if ( visible_datasets_data.length > 2 && index > 1 ) {
                y_axis_data.display = false;
                y_axis_data.ticks.display = false;
            }

            if ( visible_datasets_data.length > 2 ) {

                y_axis_data.ticks.color = 'transparent';
                y_axis_data.is_hidden   = true;

                chart.config.options.layout.padding = { left: -30, right: -30 };

            }

            var y_axis_id = 'y' + index;

            scales[y_axis_id] = y_axis_data;

            chart.config.data.datasets[visible_datasets_data[index].index].yAxisID = y_axis_id;

        }

        chart.config.options.scales = scales;

        chart.update();

    }


    /**
     * Returns the scale's maximum value and step value for the given dataset.
     *
     */
    function get_dataset_scale_data( dataset ) {

        var data_max   = get_array_max( dataset.data );
        var scale_step = 1;

        if ( data_max != 0 ) {
            
            var round_data_max = Math.ceil( data_max );
            var round_unit 	   = ( data_max > 10 ? 5 : 1 );

            scale_step = Math.ceil( ( round_data_max / 3 ) / round_unit ) * round_unit;
            
        }

        var scale_max  = scale_step * 3;

        return {
            scale_max  : scale_max,
            scale_step : scale_step
        }
        
    }


    /**
     * The chart's tooltip handler.
     * 
     */
    function chart_external_tooltip_handler( context ) {

        var chart   = context.chart;
        var tooltip = context.tooltip;

        // Tooltip element.
        var tooltip_element = chart.canvas.parentNode.querySelector( 'div.slicewp-chart-tooltip' );

        if ( ! tooltip_element ) {

            tooltip_element = document.createElement( 'div' );
            tooltip_element.classList.add( 'slicewp-chart-tooltip' );
            
            chart.canvas.parentNode.appendChild( tooltip_element );

        }

        // Hide if no tooltip.
        if ( tooltip.opacity === 0 ) {

            tooltip_element.style.opacity = 0;
            return;

        }

        // Set tooltip content.
        if ( tooltip.body ) {

            var title_lines = tooltip.title || [];
            var body_lines  = tooltip.body.map( b => b.lines );

            var tooltip_title = document.createElement( 'div' );
            tooltip_title.classList.add( 'slicewp-chart-tooltip-title' );

            for ( var i in title_lines ) {

                var text = document.createTextNode( title_lines[i] );

                tooltip_title.appendChild( text );

            }

            var tooltip_body = document.createElement( 'div' );
            tooltip_body.classList.add( 'slicewp-chart-tooltip-body' );

            for ( var i in body_lines ) {

                var label_data = body_lines[i][0].split( ':::' );
                var colors     = tooltip.labelColors[i];

                var label_wrapper = document.createElement( 'div' );
                var label_text_wrapper  = document.createElement( 'div' );
                var label_value_wrapper = document.createElement( 'div' );

                var span = document.createElement( 'span' );
                span.style.background  = colors.backgroundColor;
                span.style.borderColor = colors.borderColor;

                var label_text = document.createTextNode( label_data[0] );

                label_text_wrapper.appendChild( span );
                label_text_wrapper.appendChild( label_text );

                var label_value = document.createTextNode( label_data[1] );

                label_value_wrapper.appendChild( label_value );

                label_wrapper.appendChild( label_text_wrapper );
                label_wrapper.appendChild( label_value_wrapper );

                tooltip_body.appendChild( label_wrapper );

            }

            // Remove old children.
            while ( tooltip_element.firstChild ) {
                tooltip_element.firstChild.remove();
            }

            // Add new children.
            tooltip_element.appendChild( tooltip_title );
            tooltip_element.appendChild( tooltip_body );

        }

        // Display, position, and set styles for font.
        var positionX = chart.canvas.offsetLeft;
        var positionY = chart.canvas.offsetTop;

        var tooltipXOffset = ( tooltip.caretX < chart.width / 2 ? ( tooltip_element.offsetWidth / 2 + 20 ) : -( tooltip_element.offsetWidth / 2 + 20 ) );

        tooltip_element.style.opacity = 1;
        tooltip_element.style.left    = positionX + tooltip.caretX + tooltipXOffset + 'px';
        tooltip_element.style.top     = positionY + 30 + 'px';
        tooltip_element.style.font    = tooltip.options.bodyFont.string;
        tooltip_element.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px';

    }


    /**
     * Handles the tooltip's title.
     *
     */
    function chart_external_tooltip_title_handler( context ) {

        if ( ! context[0] ) {
            return '';
        }

        var label     = context[0].dataset.label || '';
        var time_unit = context[0].chart.config.options.scales.x.time.unit;

        var data_index = context[0].dataIndex;
        var index      = 0;
        var date       = '';

        for ( var i in context[0].dataset.data ) {
            
            if ( index == data_index ) {
                date = i;
                break;
            }

            index++;
            
        }

        if ( date == '' ) {
            return label;
        }

        var locale = document.querySelector( 'html' ).getAttribute( 'lang' );
        var dn     = new Date( date );

        if ( 'day' == time_unit ) {

            label = dn.toLocaleDateString( locale, { year: 'numeric', month: 'short', day: 'numeric' } );
            
        }

        if ( 'week' == time_unit ) {

            var df = new Date( date )
            df.setDate( df.getDate() + 6 );

            label = dn.toLocaleDateString( locale, { year: 'numeric', month: 'short', day: 'numeric' } ) + ' - ' + df.toLocaleDateString( locale, { year: 'numeric', month: 'short', day: 'numeric' } );

        }

        if ( 'month' == time_unit ) {

            label = dn.toLocaleDateString( locale, { year: 'numeric', month: 'long' } );

        }

        return label;

    }


    /**
     * Handles the tooltip's label.
     *
     */
    function chart_external_tooltip_label_handler( context ) {

        var label = context.dataset.label || '';

        if ( context.dataset.is_amount ) {

            label += ':::' + new Intl.NumberFormat( 'en-US', { style: 'currency', currency: window.slicewp.settings.active_currency } ).format( context.parsed.y );

        } else {

            label += ':::' + context.parsed.y;

        }

        return label;

    }


    /**
     * Handles the values displayed for the Y axis scale ticks.
     *
     */
    function chart_tick_label_handler( value, index, values ) {

        if ( this.chart.config.options.scales[this.id].is_hidden ) {
            return '';
        }

        if ( this.chart.config.options.scales[this.id].is_amount ) {
            return new Intl.NumberFormat( 'en-US', { style: 'currency', currency: window.slicewp.settings.active_currency } ).format( value );
        }

        return value;

    }


    /**
     * Clones the QR code button into the needed places.
     * 
     */
    var referral_link_fields    = document.querySelectorAll( '[name="custom_slug_affiliate_link"], [name="affiliate_link"], [name="affiliate_link_output"], [name="affiliate_network_invite_link"]' );
    var button_qr_code_template = document.querySelector( '#slicewp-affiliate-account > .slicewp-button-view-qr-code' );

    if ( button_qr_code_template ) {

        for ( var i = 0; i < referral_link_fields.length; i++ ) {

            var button_qr_code = button_qr_code_template.cloneNode( true );
            button_qr_code.style.display = '';
    
            referral_link_fields[i].parentNode.querySelector( '.slicewp-input-copy' ).after( button_qr_code );
    
        }
    
        button_qr_code_template.remove();

    }

    /**
     * Opens the QR code overlay.
     * 
     */
    on( 'click', '.slicewp-button-view-qr-code', function(e) {

        e.preventDefault();

        this.blur();

        var overlay_qr_code = this.parentNode.querySelector( '.slicewp-global-overlay-qr-code' );
        var referral_link   = this.parentNode.querySelector( 'input[type="text"]' ).value;

        if ( ! overlay_qr_code ) {

            var overlay_qr_code = document.querySelector( '#slicewp-affiliate-account > .slicewp-global-overlay-qr-code' ).cloneNode( true );

            this.after( overlay_qr_code );

        }

        if ( overlay_qr_code.querySelector( '.slicewp-referral-link-span' ).innerHTML != referral_link ) {
            
            var image_src = 'https://quickchart.io/qr?size=400&margin=1&text=' + encodeURIComponent( referral_link );

            overlay_qr_code.querySelector( '.slicewp-referral-link-span' ).innerHTML = referral_link;
            overlay_qr_code.querySelector( 'img' ).setAttribute( 'src', image_src );

        }

        window.slicewp.show_overlay( overlay_qr_code );

    });


    /**
     * Triggers the download of the QR code image file.
     * 
     */
    on( 'click', '.slicewp-global-overlay-qr-code .slicewp-button-primary', function(e) {

        e.preventDefault();

        this.blur();

        download_file( this.closest( '.slicewp-global-overlay-inner' ).querySelector( 'img' ).getAttribute( 'src' ), 'referral-link-qr-code.png' );

    });


    /**
     * Triggers the download for the creative image.
     * 
     */
    on( 'click', '.slicewp-download-creative-image', function(e) {

        e.preventDefault();

        this.blur();

        var img = this.closest( '.slicewp-creative-wrapper' ).querySelector( 'img' );

        if ( img ) {
            download_file( img.getAttribute( 'src' ), img.getAttribute( 'data-file-name' ) );
        }

    });


    /**
     * Downloads the given file. 
     *
     */
    async function download_file( url, name ) {

        try {

            var response = await fetch( url );

            if ( response.status == 200 ) {

                var blob   = await response.blob();
                var anchor = document.createElement( 'a' );

                anchor.setAttribute( 'href', URL.createObjectURL( blob ) );
                anchor.setAttribute( 'download', name );

                document.body.appendChild( anchor );

                anchor.click();
                
                setTimeout( function() {

                    URL.revokeObjectURL( anchor.getAttribute( 'href' ) );
                    document.body.removeChild( anchor );

                }, 10 );

            }

        } catch ( error ) {

            window.open( url );

        }

    }


    /**
     * Show the given overlay.
     *
     */
    window.slicewp.show_overlay = function show_overlay( overlay ) {

        var overlay_clone = overlay.cloneNode( true );

        document.body.appendChild( overlay_clone );

        overlay_clone.classList.add( 'slicewp-prepare-open' );

        setTimeout( function() {

            overlay_clone.classList.remove( 'slicewp-prepare-open' );
            overlay_clone.classList.add( 'slicewp-opened' );

        }, 100 );

    }


    /**
     * Closes the given overlay.
     *
     */
    window.slicewp.close_overlay = function close_overlay( overlay ) {

        overlay.classList.remove( 'slicewp-opened' );
        overlay.classList.add( 'slicewp-prepare-close' );

        setTimeout( function() {

            overlay.classList.remove( 'slicewp-prepare-close' );
            overlay.remove();

        }, 200 );

    }


    /**
     * Closes the overlay when clicking on the close overlay button.
     *
     */
    on( 'click', '.slicewp-global-overlay', function( e ) {
        
        if ( this != e.target ) {
            return false;
        }

        window.slicewp.close_overlay( this );

    });


    /**
     * Closes the overlay when clicking on the close overlay button.
     *
     */
    on( 'click', '.slicewp-global-overlay-close', function( e ) {

        e.preventDefault();

        window.slicewp.close_overlay( this.closest( '.slicewp-global-overlay' ) );

    });


    /**
     * Show/hide the password as text in a password and addiacent password confirmation field.
     * 
     */
    on( 'click', '.slicewp-show-hide-password', function( e ) {

        e.preventDefault();

        var fields = this.closest( 'form' ).querySelectorAll( 'input[name="password"], input[name="password_confirm"]' );

        for ( var i = 0; i < fields.length; i++ ) {

            if ( fields[i].getAttribute( 'type' ) == 'password' ) {

                fields[i].setAttribute( 'type', 'text' );
                
                fields[i].parentNode.querySelector( '.slicewp-show-hide-password svg:first-of-type' ).style.display = 'none';
                fields[i].parentNode.querySelector( '.slicewp-show-hide-password svg:last-of-type' ).style.display = 'block';

            } else {

                fields[i].setAttribute( 'type', 'password' );

                fields[i].parentNode.querySelector( '.slicewp-show-hide-password svg:first-of-type' ).style.display = 'block';
                fields[i].parentNode.querySelector( '.slicewp-show-hide-password svg:last-of-type' ).style.display = 'none';

            }

        }

    });


    /**
     * Converts the given bytes into KB, MB etc.
     *
     */
    function bytes_to_size( bytes ) {

        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        if ( bytes == 0 ) {
            return '0 Byte';
        }
        
        var i = parseInt( Math.floor( Math.log( bytes ) / Math.log( 1024 ) ) );
        
        return Math.round( bytes / Math.pow( 1024, i ), 2 ) + ' ' + sizes[i];

    }


    /**
     * Returns the rgba value of the given hex color.
     *
     */
    function hex_to_rgba( hex, opacity = 1 ) {

        var r = parseInt( hex.slice(1, 3), 16 ),
            g = parseInt( hex.slice(3, 5), 16 ),
            b = parseInt( hex.slice(5, 7), 16 );

        return "rgba( " + r + "," + g + "," + b + ", " + opacity + " )";

    }


    /**
     * Returns the highest value in the given array.
     *
     */
    function get_array_max( arr ) {

        var max = 0;

        for ( var index in arr ) {

            if ( arr[index] > max ) {
                max = arr[index];
            }

        }

        return max;

    }

}


if ( document.readyState === "complete" || ( document.readyState !== "loading" && ! document.documentElement.doScroll ) ) {

	slicewp_front_end();

} else {

  document.addEventListener( "DOMContentLoaded", slicewp_front_end );

}