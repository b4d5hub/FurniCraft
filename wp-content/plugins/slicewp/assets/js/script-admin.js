jQuery( function($) {

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
    function add_query_arg( key, value, sourceURL ) {

        sourceURL = remove_query_arg( key, sourceURL );

        return sourceURL + '&' + key + '=' + value;

    }


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
	 * Initialize colorpicker
	 *
	 */
	$('.slicewp-colorpicker').wpColorPicker();

	/**
	 * Initialize Chosen
	 *
	 */
	if( typeof $.fn.chosen != 'undefined' ) {

		$('.slicewp-chosen').chosen();

	}

    /**
     * Initialize jQuery select2
     *
     */
    if( $.fn.select2 ) {

        /**
         * select2 Template Result callback.
         * 
         */
         function select2_template_result( option ) {

            if ( ! option.id ) {
                return option.text;
            }

            var $select     = $(option.element).closest( 'select' );
            var description = $select.siblings( '.slicewp-select2-option-result-description[data-option="' + option.id + '"]' ).html();

            if ( ! option.element.dataset.description && ! description ) {
                return option.text;
            }

            var $option = $(
                '<span>' + option.text + '</span><div class="slicewp-option-description">' + ( option.element.dataset.description || description ) + '</div>'
            );
            
            return $option;

        }

        /**
         * select2 Template Selection callback.
         * 
         */
        function select2_template_selection( option ) {

            if ( ! option.id ) {
                return option.text;
            }

            var $select     = $(option.element).closest( 'select' );
            var description = $select.siblings( '.slicewp-select2-option-selection-description[data-option="' + option.id + '"]' ).html();

            if ( ! option.element.dataset.description && ! description ) {
                return option.text;
            }

            var $option = $(
                '<span>' + option.text + '</span><div class="slicewp-option-description">' + ( option.element.dataset.description || description ) + '</div>'
            );
            
            return $option;

        }

        (function($) {

            var Defaults = $.fn.select2.amd.require('select2/defaults');
          
            $.extend(Defaults.defaults, {
              dropdownPosition: 'auto'
            });
          
            var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');
          
            var _positionDropdown = AttachBody.prototype._positionDropdown;
          
            AttachBody.prototype._positionDropdown = function() {
          
              var $window = $(window);
          
              var isCurrentlyAbove = this.$dropdown.hasClass('select2-dropdown--above');
              var isCurrentlyBelow = this.$dropdown.hasClass('select2-dropdown--below');
          
              var newDirection = null;
          
              var offset = this.$container.offset();
          
              offset.bottom = offset.top + this.$container.outerHeight(false);
          
              var container = {
                  height: this.$container.outerHeight(false)
              };
          
              container.top = offset.top;
              container.bottom = offset.top + container.height;
          
              var dropdown = {
                height: this.$dropdown.outerHeight(false)
              };
          
              var viewport = {
                top: $window.scrollTop(),
                bottom: $window.scrollTop() + $window.height()
              };
          
              var enoughRoomAbove = viewport.top < (offset.top - dropdown.height);
              var enoughRoomBelow = viewport.bottom > (offset.bottom + dropdown.height);
          
              var css = {
                left: offset.left,
                top: container.bottom
              };
          
              // Determine what the parent element is to use for calciulating the offset
              var $offsetParent = this.$dropdownParent;
          
              // For statically positoned elements, we need to get the element
              // that is determining the offset
              if ($offsetParent.css('position') === 'static') {
                $offsetParent = $offsetParent.offsetParent();
              }
          
              var parentOffset = $offsetParent.offset();
          
              css.top -= parentOffset.top
              css.left -= parentOffset.left;
          
              var dropdownPositionOption = this.options.get('dropdownPosition');
          
              if (dropdownPositionOption === 'above' || dropdownPositionOption === 'below') {
                newDirection = dropdownPositionOption;
              } else {
          
                if (!isCurrentlyAbove && !isCurrentlyBelow) {
                  newDirection = 'below';
                }
          
                if (!enoughRoomBelow && enoughRoomAbove && !isCurrentlyAbove) {
                  newDirection = 'above';
                } else if (!enoughRoomAbove && enoughRoomBelow && isCurrentlyAbove) {
                  newDirection = 'below';
                }
          
              }
          
              if (newDirection == 'above' ||
              (isCurrentlyAbove && newDirection !== 'below')) {
                  css.top = container.top - parentOffset.top - dropdown.height;
              }
          
              if (newDirection != null) {
                this.$dropdown
                  .removeClass('select2-dropdown--below select2-dropdown--above')
                  .addClass('select2-dropdown--' + newDirection);
                this.$container
                  .removeClass('select2-container--below select2-container--above')
                  .addClass('select2-container--' + newDirection);
              }
          
              this.$dropdownContainer.css(css);
          
            };
          
        })(window.jQuery);

        // Selects with predefined options.
        $('.slicewp-select2:not(.slicewp-select2-users-autocomplete)').each( function() {

            var $this   = $(this);
            var classes = $this.attr( 'class' );
            var data    = { minimumResultsForSearch : 20, placeholder : $this.attr( 'placeholder' ) };

            if ( $this.attr( 'data-has-template-result' ) ) {
                data.templateResult = select2_template_result;
            }

            if ( $this.attr( 'data-has-template-selection' ) ) {
                data.templateSelection = select2_template_selection;
            }

            $this.select2(data).on('select2:open', function() {

                var container = $('.select2-container').last();

                container.addClass('slicewp-select2-container');

                if ( $this.hasClass( 'slicewp-select2-small' ) ) {
                    container.addClass( 'slicewp-select2-small' );
                }

                if ( typeof $this.attr( 'data-container-class' ) != 'undefined' ) {
                    container.addClass( $this.attr( 'data-container-class' ) );
                }
                
                container.find('.select2-search__field').attr( 'placeholder', 'Type to search...' );

                // Focus the search field for single selects.
                if ( typeof $this.attr( 'multiple' ) == 'undefined' ) {
                    container.find('.select2-search__field')[0].focus();
                }

            });

            if ( data.templateResult )
                $this.siblings('.select2-container').addClass( 'slicewp-has-template-result' );

            if ( data.templateSelection )
                $this.siblings('.select2-container').addClass( 'slicewp-has-template-selection' );

            $this.siblings('.select2-container').addClass( classes );

        });

        // Users autocomplete selects with AJAX returned options.
        $('.slicewp-select2-users-autocomplete').each( function() {

            var $this = $(this);
            var classes = $this.attr( 'class' );

            $this.select2({
                dropdownPosition : 'below',
                minimumInputLength : 2,
                placeholder : $this.attr( 'placeholder' ),
                allowClear : ( typeof $this.attr( 'multiple' ) == 'undefined' ? true : false ),
                ajax : {
                    url      : ajaxurl + '?action=slicewp_action_ajax_get_users',
                    delay    : 250,
                    dataType : 'json',
                    data     : function( params ) {
    
                        var query = {
                            term          : params.term,
                            affiliates    : $this.attr( 'data-affiliates' ),
                            user_role     : $this.attr( 'data-user-role' ),
                            return_value  : ( typeof $this.attr( 'data-return-value' ) != 'undefined' ? $this.attr( 'data-return-value' ) : '' ),
                            slicewp_token : ( typeof $this.attr( 'data-nonce' ) != 'undefined' ? $this.attr( 'data-nonce' ) : '' )
                        };
    
                        return query;
    
                    },
                    processResults : function( data ) {
    
                        var results = [];

                        for ( var index in data ) {
                            results.push({ id: data[index].value, text: data[index].label });
                        }
    
                        return {
                            results : results
                        }
    
                    }
                }
            }).on('select2:open', function() {

                var container = $('.select2-container').last();
                
                container.addClass( 'slicewp-select2-container' );
                
                if ( $this.hasClass( 'slicewp-select2-small' ) ) {
                    container.addClass( 'slicewp-select2-small' );
                }

                if ( typeof $this.attr( 'data-container-class' ) != 'undefined' ) {
                    container.addClass( $this.attr( 'data-container-class' ) );
                }

                container.find('.select2-search__field').attr( 'placeholder', 'Type to search...' );

                // Focus the search field for single selects.
                if ( typeof $this.attr( 'multiple' ) == 'undefined' ) {
                    container.find('.select2-search__field')[0].focus();
                }

            });

            $this.siblings('.select2-container').addClass( classes );

        });


		// Change select2 defaults for the post fields.
		$('.slicewp-select2-posts-field').each( function() {

			var $this = $(this);

			if ( typeof $this.attr( 'data-is-ajax' ) == 'undefined' ) {

				$this.select2({
					dropdownPosition : 'below',
					placeholder : $this.attr( 'placeholder' ),
                    allowClear : ( typeof $this.attr( 'multiple' ) == 'undefined' ? true : false ),
				});

			} else {

				$this.select2({
					dropdownPosition : 'below',
					minimumInputLength : 2,
					placeholder : $this.attr( 'placeholder' ),
                    allowClear : ( typeof $this.attr( 'multiple' ) == 'undefined' ? true : false ),
					ajax : {
						url      : ajaxurl + '?action=slicewp_action_ajax_get_posts',
						delay    : 250,
						dataType : 'json',
						data     : function( params ) {

							var query = {
								term          : params.term,
								query_args	  : JSON.parse( $this.attr( 'data-query-args' ) ),
								slicewp_token : $this.attr( 'data-nonce' )
							};

							return query;

						},
						processResults : function( data ) {

							var results = [];

							for ( var index in data ) {
								results.push({ id: data[index].id, text: data[index].title });
							}

							return {
								results : results
							}

						}
					}
				}).on('select2:open', function() {

					var container = $('.select2-container').last();

					container.addClass('slicewp-select2-container');
					container.find('.select2-search__field').attr( 'placeholder', 'Type to search...' );

                    // Focus the search field for single selects.
                    if ( typeof $this.attr( 'multiple' ) == 'undefined' ) {
                        container.find('.select2-search__field')[0].focus();
                    }

				});
			}
		});

	}

    /**
     * Initialize datepicker
     *
     */
    if( $.fn.datepicker ) {

        $('.slicewp-datepicker').datepicker({
            dateFormat : 'yy-mm-dd',
            beforeShow : function(i, o) { if ($(i).attr('readonly')) { return false; } }
        });

    }

    /**
     * Initializa datetimepicker
     *
     */
    if( $.fn.datetimepicker ) {

        $('.slicewp-dtpicker').datetimepicker({
            dateFormat  : 'yy-mm-dd',
            timeFormat  : 'HH:mm:00',
            controlType : 'select'
        });

    }

	/**
	 * Tab Navigation
	 *
	 */
	$('.slicewp-nav-tab').on( 'click', function(e) {

        if ( typeof $(this).attr('data-tab')  != 'undefined' ) {            

            e.preventDefault();

            // Nav Tab activation
            $('.slicewp-nav-tab').removeClass('slicewp-active');
            $(this).addClass('slicewp-active');

            // Show tab
            $('.slicewp-tab').removeClass('slicewp-active');

            var nav_tab = $(this).attr('data-tab');
            $('.slicewp-tab[data-tab="' + nav_tab + '"]').addClass('slicewp-active');
            $('input[name=active_tab]').val( nav_tab );


            // Change "tab" query var
            var url = window.location.href;
            url = remove_query_arg( 'tab', url );
            url = add_query_arg( 'tab', $(this).attr('data-tab'), url );

            window.history.replaceState( {}, '', url );

            // Change http referrer
            $_wp_http_referer = $('input[name=_wp_http_referer]');

            if ( $_wp_http_referer.length > 0 ) {

                var _wp_http_referer = $_wp_http_referer.val();
                _wp_http_referer = remove_query_arg( 'tab', _wp_http_referer );
                $_wp_http_referer.val( add_query_arg( 'tab', $(this).attr('data-tab'), _wp_http_referer ) );
                
            }

            // Change hidden tab input
            $(this).closest('form').find('input[name=active_tab]').val( $(this).attr('data-tab') );

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

        var field_id   = $state_field.attr( 'id' ),
            field_name = $state_field.attr( 'name' ),
            field_data_value    = $state_field.attr( 'data-value' ),
            field_data_required = $state_field.attr( 'data-required' ),
            field_is_disabled   = $state_field.is( ':disabled' );

        if( slicewp_country_select[country_code] ) {

            if( ! $.isEmptyObject( slicewp_country_select[country_code] ) ) {

                $new_state_field = $('<select></select>')
                    .attr( 'id', field_id )
                    .attr( 'name', field_name )
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
                .attr( 'value', field_data_value )
                .attr( 'data-value', field_data_value )
                .attr( 'data-required', field_data_required );

            if( field_is_disabled )
                $new_state_field.attr( 'disabled', true );

            $state_field.closest('.slicewp-field-wrapper').show();

        }

        // Destroy existing select2 field
        if( $state_field.is('select') ) {
            $state_field.select2('destroy');
        }

        // Replace state field
        $state_field.replaceWith( $new_state_field );

        // Initialize select2 for selects
        if( $new_state_field.is('select') ) {
            $new_state_field.addClass('slicewp-select2').select2({
                minimumResultsForSearch : 20
            }).on('select2:open', function() {
                var container = $('.select2-container').last();
                container.addClass('slicewp-select2-container');
                container.find('.select2-search__field').attr( 'placeholder', 'Type to search...' );
            });
        }
        
    });

    $('.slicewp-field-wrapper[data-type="country"] select').trigger( 'change' );


    /**
     * Users autocomplete field
     *
     */
    $('.slicewp-field-users-autocomplete').each( function() {

        var $this = $(this);

        $this.autocomplete({
            source    : ajaxurl + '?action=slicewp_action_ajax_get_users&term=' + $this.val() + '&affiliates=' + $this.data('affiliates') + '&return_value=' + ( typeof $this.data('return-value') != 'undefined' ? $this.data('return-value') : '' ) + '&slicewp_token=' + $('#slicewp_user_search_token').val(),
            minLength : 2,
            delay     : 350,
            search    : function( e, ui ) {
                $this.after( '<div class="spinner"></div>' )
            },
            response  : function( e, ui ) {
                $this.siblings( '.spinner' ).remove();

                if( ui.content.length == 0 )
                    ui.content.push( { value : '', label : 'No results found.' } );
                
            },
            select    : function( e, ui ) {
                e.preventDefault();

                if( ui.item.value != '' ) {
                    $this.val( ui.item.label );
                    $this.siblings('input[type=hidden]').first().val( ui.item.value );
                }

            }
        });

    });

    $(document).on( 'focus', '.slicewp-field-users-autocomplete', function() {

        if( $(this).siblings('[type=hidden]').first().val() == '' )
            $(this).autocomplete('search');

    });

    $(document).on( 'change', '.slicewp-field-users-autocomplete', function() {
        
        if ( $(this).val() == '' ) {

            $(this).siblings('input[type=hidden]').first().val('');

        }
        
    });

    /**
     * Show/hide commission rate when rate type isn't selected.
     *
     */
    $(document).on( 'change', '.slicewp-field-wrapper-commission-rate select', function() {

        var $select = $(this);

        if ( $select.find( 'option:selected' ).val() == '' ) {
            $select.closest( '.slicewp-field-wrapper-commission-rate' ).find( 'input' ).hide();
        } else {
            $select.closest( '.slicewp-field-wrapper-commission-rate' ).find( 'input' ).show();

            // setTimeout( function() {
            //     $select.closest('.slicewp-field-wrapper-commission-rate').find( 'input' ).focus();
            // }, 1 );
        }

    });

    // $('.slicewp-field-wrapper-commission-rate select').trigger( 'change' );
    $('.slicewp-field-wrapper-commission-rate select').each( function() {

        var $select = $(this);

        if ( $select.find( 'option:selected' ).val() == '' ) {
            $select.closest( '.slicewp-field-wrapper-commission-rate' ).find( 'input' ).hide();
        } else {
            $select.closest( '.slicewp-field-wrapper-commission-rate' ).find( 'input' ).show();
        }

    });


    /**
     * Page: Affiliates
     *
     */
    var affiliate_url_tooltip_hover;

    $(document).on( 'mouseenter', '.slicewp-wp-list-table.slicewp_affiliates .column-name .slicewp-tooltip-wrapper', function(e) {
        
        var $this = $(this);

        affiliate_url_tooltip_hover = setTimeout( function() {
            $this.addClass('slicewp-hover');
        }, 600 );

    });

    $(document).on( 'mouseleave', '.slicewp-wp-list-table.slicewp_affiliates .column-name .slicewp-tooltip-wrapper', function(e) {

        $(this).removeClass('slicewp-hover');
        
        clearTimeout( affiliate_url_tooltip_hover );

    });
    
    $(document).on( 'click', '.slicewp-wp-list-table.slicewp_affiliates .column-name .slicewp-tooltip-wrapper.slicewp-copy-affiliate-url', function() {

        var $tooltip = $(this);

        if ( $tooltip.hasClass( 'slicewp-copied' ) ) {
            return false;
        }

        $tooltip.find('input').select();
        document.execCommand('copy');

        $tooltip.addClass( 'slicewp-copied' );

        $tooltip.find( '.slicewp-tooltip-message span:nth-child(1)' ).hide();
        $tooltip.find( '.slicewp-tooltip-message span:nth-child(2)' ).fadeIn( 200 );

        setTimeout( function() {

            $tooltip.removeClass( 'slicewp-copied' );
            $tooltip.removeClass( 'slicewp-hover' );

        }, 1500 );

        setTimeout( function() {

            $tooltip.find( '.slicewp-tooltip-message span:nth-child(1)' ).fadeIn( 200 );
            $tooltip.find( '.slicewp-tooltip-message span:nth-child(2)' ).hide();

        }, 1750 );

    });

    // Show the affiliate's QR code modal.
    $(document).on( 'click', '.slicewp-wp-list-table.slicewp_affiliates .column-name .slicewp-tooltip-wrapper.slicewp-view-affiliate-qr-code', function(e) {

        e.preventDefault();

        var $row   = $(this).closest( 'tr' );
        var $modal = $('.slicewp-modal-affiliate-qr-code');
        
        var referral_link = $(this).find( 'input[type="hidden"]' ).first().val();

        // Populate the affiliate avatar.
        $modal.find( '.slicewp-affiliate-avatar' ).html( '<img src="' + $row.find( '.column-name .slicewp-affiliate-name img' ).attr( 'src' ) + '" srcset="' + $row.find( '.column-name .slicewp-affiliate-name img' ).attr( 'srcset' ) + '" />' );

        // Populate the affiliate name.
        $modal.find( '.slicewp-affiliate-name' ).html( '<span class="slicewp-affiliate-name">' + $row.find( '.column-name .slicewp-affiliate-name span' ).html() + '</span>' );

        if ( $modal.find( '.slicewp-referral-link-span' ).first().innerHTML != referral_link ) {

            $modal.find( '.slicewp-affiliate-qr-code' ).attr( 'src', '' );

            var image_src = 'https://quickchart.io/qr?size=400&margin=1&text=' + encodeURIComponent( referral_link );

            $modal.find( '.slicewp-referral-link-span' ).first().html( referral_link );
            $modal.find( '.slicewp-affiliate-qr-code' ).attr( 'src', image_src );
            $modal.find( '.slicewp-affiliate-qr-code' ).attr( 'data-affiliate-id', $row.find( '[name="affiliate_ids[]"]' ).val() );

        }

        // Show the modal.
        $('.slicewp-modal-affiliate-qr-code').parent().addClass( 'slicewp-active' );

    });

    // Triggers the download of the QR code image file.
    $(document).on( 'click', '.slicewp-modal-affiliate-qr-code .slicewp-button-secondary', function(e) {

        e.preventDefault();

        this.blur();

        var $qr_code = $(this).closest( '.slicewp-modal-affiliate-qr-code' ).find( '.slicewp-affiliate-qr-code' ).first();

        download_file( $qr_code.attr( 'src' ), 'referral-link-qr-code' + ( $qr_code.attr( 'data-affiliate-id' ) != 'undefined' ? '-' + $qr_code.attr( 'data-affiliate-id' ) : '' ) + '.png' );

    });


    /**
     * Page: Add New Affiliate
     *
     */
    $(document).on( 'change', '.slicewp-wrap-add-affiliate #slicewp-affiliate-status', function() {

        if( $(this).val() == 'active' ) {

            $(this).closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-welcome-email').closest('.slicewp-field-wrapper').show();

        } else {

            $(this).closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-welcome-email').closest('.slicewp-field-wrapper').hide();

        }

    });

    $(document).on( 'click', '#slicewp-field-wrapper-payout-method .slicewp-field-unlock', function(e) {

        e.preventDefault();

        var $this = $(this);

        $this.closest( '.slicewp-field-wrapper' ).find( '.select2-container' ).css( 'display', 'inline-block' );
        $this.closest( '.slicewp-field-locked-wrapper' ).hide();

        setTimeout( function() {

            $this.closest( '.slicewp-field-wrapper' ).find( '.select2-container' ).siblings( 'select:enabled' ).select2( 'open' );
            $this.closest( '.slicewp-field-locked-wrapper' ).remove();

        }, 50 );

    });


    /**
     * Page: Add New Creative
     *
     */
    $(document).on( 'change', '#slicewp-creative-type', function() {

        $('#slicewp-creative-landing-url').closest('.slicewp-field-wrapper').show();

        if( $(this).val() == 'image' ) {

            $('#slicewp-creative-image').closest('.slicewp-field-wrapper').show();
            $('#slicewp-creative-alt-text').closest('.slicewp-field-wrapper').show();
            $('#slicewp-creative-text').closest('.slicewp-field-wrapper').hide();

        } else {

            $('#slicewp-creative-image').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-creative-alt-text').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-creative-text').closest('.slicewp-field-wrapper').show();

            $text_field_wrapper = $('#slicewp-creative-text').closest('.slicewp-field-wrapper');

            // Display text versus long-text
            if( $(this).val() == 'text' ) {

                $text_field_wrapper.find('input').attr( { 'id' : 'slicewp-creative-text', 'name' : 'text' } ).show();
                $text_field_wrapper.find('textarea').removeAttr( 'id name' ).hide();

            }

            if( $(this).val() == 'long_text' ) {

                $text_field_wrapper.find('textarea').attr( { 'id' : 'slicewp-creative-text', 'name' : 'text' } ).show();
                $text_field_wrapper.find('input').removeAttr( 'id name' ).hide();

                $('#slicewp-creative-landing-url').closest('.slicewp-field-wrapper').hide();

            }

        }

    });

    $('#slicewp-creative-type').trigger( 'change' );


    /**
     * Page: Settings.
     *
     */
    $(document).on( 'input', '#slicewp-affiliate-keyword', function() {

        $(this).closest( '.slicewp-field-wrapper' ).find( '.slicewp-tooltip-message code strong' ).html( $(this).val() );
        $(this).closest( '.slicewp-card' ).find( 'label[for="slicewp-friendly-affiliate-urls"] .slicewp-tooltip-message code span' ).html( $(this).val() );

    });

    $(document).on( 'change', '#slicewp-email-template', function() {

        if ( $(this).val() == '' ) {
            $('#slicewp-email-logo').closest( '.slicewp-field-wrapper' ).hide();
        } else {
            $('#slicewp-email-logo').closest( '.slicewp-field-wrapper' ).show();
        }

    });

    $('#slicewp-email-template').trigger('change');


    /**
     * Page: Settings - shows/hides commission types based on the enabled integration.
     *
     */
    $(document).on( 'change', '[id^="slicewp-integration-switch-"]', function() {

        var commission_types = [];

        // Get all commission types from all integrations
        $('[id^="slicewp-integration-switch-"]').each( function() {

            if( $(this).is( ':checked' ) ) {

                var supports = JSON.parse( $(this).attr( 'data-supports' ) );

                if ( typeof supports['commission_types'] != 'undefined' ) {
                    commission_types = commission_types.concat( supports['commission_types'] );
                }

            }

        });

        // Remove duplicates
        commission_types = commission_types.filter( function( elem, pos, arr ) {
            return arr.indexOf( elem ) == pos;
        });

        // Make sure the "sale" type exists if the array is empty
        if ( commission_types.length == 0 ) {
            commission_types.push( 'sale' );
        }
        
        // Show/hide the commission types
        $('[id^="slicewp-commission-rate-"]').closest( '.slicewp-field-wrapper' ).hide();

        commission_types.forEach( function( commission_type ) {

            $('[id="slicewp-commission-rate-' + commission_type + '"]').closest( '.slicewp-field-wrapper' ).show();

        });

        // We need to trigger this change as there might be fields that have visibility dependent on this, for example the sale commission basis
        $('[id^="slicewp-commission-rate-"]').siblings('select').trigger( 'change' );

    });
    
    $('[id^="slicewp-integration-switch-"]').first().trigger( 'change' );


    /**
     * Page: Settings - add/remove the integration from active integrations global.
     * 
     */
     $(document).on( 'change', '[id^="slicewp-integration-switch-"]', function() {

        var integration = $(this).val();

        if ( $(this).is( ':checked' ) ) {

            if ( window.slicewp.settings.active_integrations.indexOf( integration ) === -1 ) {

                window.slicewp.settings.active_integrations.push( integration );

            }

        } else {

            window.slicewp.settings.active_integrations = window.slicewp.settings.active_integrations.filter( function(e) {
                return e !== integration;
            });

        }

     });


    /**
     * Page: Settings - show/hide fixed amount sale commission basis field
     *
     */
    $(document).on( 'change', '[name="settings[commission_rate_type_sale]"], [name="commission_rate_type_sale"]', function() {

        if( $(this).val() == 'fixed_amount' && $(this).closest('.slicewp-field-wrapper').css( 'display' ) != 'none' )
            $('#slicewp-fixed-amount-commission-basis').closest('.slicewp-field-wrapper').show();

        else
            $('#slicewp-fixed-amount-commission-basis').closest('.slicewp-field-wrapper').hide();

        $(this).closest('.slicewp-card').find('.slicewp-field-wrapper').removeClass('slicewp-last');
        $(this).closest('.slicewp-card').find('.slicewp-field-wrapper:visible').last().addClass('slicewp-last');

    });

    $('[name="settings[commission_rate_type_sale]"], [name="commission_rate_type_sale"]').trigger( 'change' );


    /**
     * Page: Settings - change the thousands and decimal separators on currency change
     *
     */
    $(document).on( 'change', '[name="settings[active_currency]"]', function(e) {

        if( typeof slicewp_currencies != 'undefined' && typeof slicewp_currencies[$(this).val()] != 'undefined' ) {

            $('[name="settings[currency_thousands_separator]"]').val( slicewp_currencies[$(this).val()]['thousands_separator'] );
            $('[name="settings[currency_decimal_separator]"]').val( slicewp_currencies[$(this).val()]['decimal_separator'] );

        }

    });


    /**
     * Media Library Browser
     *
     */

    var frame;

	$('.slicewp-image-select').on('click', function(e) {
		
		e.preventDefault();

		$btn_select = $(this);

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Use Image'
			},
			multiple: false
		});

		// Select image from media frame
		frame.on( 'select', function() {
      
			var attachment = frame.state().get('selection').first().toJSON();

            $btn_select.siblings('[type="text"]').val( attachment.url );

	    });

		frame.open();

    });


    /**
     * Show/hide the expandable item's panel on expand link click.
	 *
	 */
     $(document).on( 'click', '.slicewp-expandable-item-actions .slicewp-expand-item', function(e) {

        e.preventDefault();

        $(this).closest( '.slicewp-expandable-item' ).toggleClass( 'slicewp-active' );
        $(this).closest( '.slicewp-expandable-item' ).find( '.slicewp-expandable-item-panel' ).slideToggle( 200 );

        $(this).blur();

    });


    /**
     * Page: Settings. Show/hide email notification settings fields.
	 *
	 */
    $(document).on( 'click', '.slicewp-email-notification-settings-wrapper .slicewp-email-notification-settings-actions a', function(e) {

        e.preventDefault();

        $(this).closest( '.slicewp-email-notification-settings-wrapper' ).toggleClass( 'slicewp-active' );
        $(this).closest( '.slicewp-email-notification-settings-wrapper' ).find( '.slicewp-email-notification-setting-panel' ).slideToggle( 200 );

        $(this).blur();

    });


    /**
     * Page: Settings. Show settings panel when enabling the captcha service if no keys are set.
     * 
     */
    $(document).on( 'change', '.slicewp-captcha-service .slicewp-toggle', function() {

        $toggle = $(this);

        if ( $toggle.is( ':checked' ) ) {
            
            if ( ! $toggle.closest( '.slicewp-expandable-item' ).hasClass( 'slicewp-active' ) ) {

                var has_keys = true;

                $toggle.closest( '.slicewp-expandable-item' ).find( 'input[type="text"], input[type="password"]' ).each( function() {
                    
                    if ( $(this).val() == '' ) {
                        has_keys = false;
                    }

                });

                if ( ! has_keys ) {

                    $toggle.closest( '.slicewp-expandable-item' ).find( '.slicewp-expand-item' ).click();
                    
                    setTimeout( function() {
                        $toggle.closest( '.slicewp-expandable-item' ).find( 'input[type="text"]' ).first().focus();
                    }, 150 );

                }

            }

        }

    });


    /**
     * Page: Review Affiliate
     *
     */
    $(document).on( 'change', '#slicewp-affiliate-application-status', function() {

        if ( $(this).val() == 'application_approved' ) {

            $('#slicewp-send-email-notification').prop('checked', true);
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-approve-affiliate').show();
            $('#slicewp-reject-affiliate').hide();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            
            $('#slicewp-link-approve-email-notification').show();
            $('#slicewp-link-reject-email-notification').hide();

        } else {

            $('#slicewp-send-email-notification').prop('checked', true);
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').show();
            $('#slicewp-approve-affiliate').hide();
            $('#slicewp-reject-affiliate').show();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').addClass('slicewp-last');

            $('#slicewp-link-approve-email-notification').hide();
            $('#slicewp-link-reject-email-notification').show();

        }

    });

    $('#slicewp-affiliate-application-status').trigger('change');


    $(document).on( 'change', '#slicewp-send-email-notification', function() {

        if( $(this).prop('checked') == false && $('#slicewp-affiliate-application-status').val() == 'application_rejected' ) {

            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').hide();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').removeClass('slicewp-last');

        }
        else if ( $(this).prop('checked') == true && $('#slicewp-affiliate-application-status').val() == 'application_rejected' )
        {
            
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').show();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').addClass('slicewp-last');

        }

    });
    

    $(document).on( 'change', '#slicewp-payment-status', function() {

        var status = $('#slicewp-payment-status :selected').text();
        $('#slicewp-review-payment-button').attr( 'data-confirmation-message', "Are you sure you want to mark the payment as " + status + "?" );

    });

    
    /**
     * Register and deregister customer website from our servers
     *
     */
    $(document).on( 'click', '#slicewp-register-license-key', function(e) {

        e.preventDefault();

        if( $('#slicewp-is-website-registered').length == 0 )
            return false;

        var action = ( $('#slicewp-is-website-registered').val() == 'false' ? 'register' : 'deregister' );

        $button = $(this);

        // Exit if button is disabled
        if( $button.hasClass( 'slicewp-disabled' ) )
            return false;

        // Exit if the license key field is empty
        if( $button.siblings( 'input[type="text"]' ).val() == '' ) {
            $button.siblings( 'input[type="text"]' ).focus();
            return false;
        }

        // Disable license key field
        $button.siblings( 'input[type="text"]' ).attr( 'disabled', 'true' );

        // Disable the button
        $button.addClass( 'slicewp-disabled' );
        
        // Show spinner
        $button.addClass( 'slicewp-spinner-active' );

        // Prepare AJAX call data
        var data = {
            action        : 'slicewp_action_ajax_' + action + '_website',
            slicewp_token : $('#slicewp_token').val(),
            license_key   : $('#slicewp-license-key').val()
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            // Remove API notice
            $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-notice' ).remove();

            // Re-enable the button
            $button.siblings( 'input[type="text"]' ).removeAttr( 'disabled' );

            // Re-enable the button
            $button.removeClass( 'slicewp-disabled' );

            // Hide spinner
            $button.removeClass( 'slicewp-spinner-active' );
            
            if( response.success == false ) {

                if( action == 'register' ) {
                    $button.find( 'span.slicewp-register' ).show();
                    $button.find( 'span.slicewp-deregister' ).hide();
                }

                if( action == 'deregister' ) {
                    $button.find( 'span.slicewp-register' ).hide();
                    $button.find( 'span.slicewp-deregister' ).show();
                }

                $button.closest( '.slicewp-field-wrapper' ).append( '<div class="slicewp-field-notice slicewp-field-notice-error">' + response.data.message + '</div>' );
                $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-notice' ).fadeIn();

            } else {

                if( action == 'register' ) {
                    $button.find( 'span.slicewp-register' ).hide();
                    $button.find( 'span.slicewp-deregister' ).show();
                }

                if( action == 'deregister' ) {
                    $button.find( 'span.slicewp-register' ).show();
                    $button.find( 'span.slicewp-deregister' ).hide();
                }

                $button.closest( '.slicewp-field-wrapper' ).append( '<div class="slicewp-field-notice slicewp-field-notice-success">' + response.data.message + '</div>' );
                $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-notice' ).fadeIn();

                if( action == 'register' )
                    $('#slicewp-is-website-registered').val( 'true' );

                if( action == 'deregister' )
                    $('#slicewp-is-website-registered').val( 'false' );

                if( action == 'register' )
                    $('#slicewp-license-key').attr( 'type', 'password' );

                if( action == 'deregister' )
                    $('#slicewp-license-key').attr( 'type', 'text' ).val( '' );

            }

        });

    });


    /**
     * Page: Create Payout
     * 
     */
    $(document).on( 'change', '#slicewp-payout-date-range', function() {

        if ( $(this).val() == 'custom_range' ) {

            $('#slicewp-date-min').closest( '.slicewp-field-wrapper' ).show();
            $('#slicewp-date-up-to').closest( '.slicewp-field-wrapper' ).hide();

            $('#slicewp-date-min').attr( 'required', true );
            $('#slicewp-date-max').attr( 'required', true );

        } else {

            $('#slicewp-date-min').closest( '.slicewp-field-wrapper' ).hide();
            $('#slicewp-date-up-to').closest( '.slicewp-field-wrapper' ).show();

            $('#slicewp-date-min').attr( 'required', false );
            $('#slicewp-date-max').attr( 'required', false );

        }

    });

    $('#slicewp-payout-date-range').trigger( 'change' );

    $(document).on( 'change', '#slicewp-payout-included-affiliates', function() {

        if ( $(this).val() == 'selected' ) {

            $('#slicewp-payout-selected-affiliates').closest( '.slicewp-field-wrapper' ).show();

        } else {

            $('#slicewp-payout-selected-affiliates').closest( '.slicewp-field-wrapper' ).hide();

        }

    });

    $('#slicewp-payout-included-affiliates').trigger( 'change' );


    /**
     * Payout method selection.
     *
     */
    $(document).on( 'change', '#slicewp-card-payout-progress select', function() {

        var $submit        = $(this).closest( '.slicewp-card-footer' ).find( 'button' );
        var $field_wrapper = $(this).closest( '.slicewp-field-wrapper' );

        // Show/hide field notices.
        $field_wrapper.find( '.slicewp-field-notice' ).hide();

        if ( '' != $(this).val() ) {
            $field_wrapper.find( '.slicewp-field-notice[data-payout-method="' + $(this).val() + '"]' ).show();
        }

        // Disable the submit button.
        if ( $(this).val() != '' ) {
            $submit.attr( 'disabled', false );
        } else {
            $submit.attr( 'disabled', true );
        }

        // Set the button's label.
        $submit.find('.slicewp-button-label').hide();

        if ( $(this).val() == '' || $(this).val() == 'manual' ) {
            $submit.find('.slicewp-button-label-manual').show();
        } else {
            $submit.find('.slicewp-button-label-other').show();
        }

        // Set the onclick parameter for the button.
        $submit.removeAttr( 'onclick' );

        if ( typeof slicewp_payout_methods_messages != 'undefined' && typeof slicewp_payout_methods_messages[$(this).val()] != 'undefined' && typeof slicewp_payout_methods_messages[$(this).val()]['payout_action_confirmation_bulk_payments'] != 'undefined' ) {
            $submit.attr( 'data-confirmation-message', slicewp_payout_methods_messages[$(this).val()]['payout_action_confirmation_bulk_payments'] );
        }

    });

    $('.slicewp-field-wrapper-payout-method select').trigger( 'change' );


    /**
     * Handles the payout Pay Affiliates button confirmation.
     *
     */
    $(document).on( 'click', '#slicewp-card-payout-progress .slicewp-button-primary, #slicewp-card-do-single-payment .slicewp-button-primary', function() {

        if( $(this).hasClass( 'slicewp-disabled' ) )
            return false;

        var confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        // Disable button and show loading spinner.
        if( confirmation ) {

            $(this).addClass( 'slicewp-disabled' );
            $(this).after( '<div class="spinner"></div>' );

        }

        return confirmation;

    });


    /**
     * Disable submit buttons and add spinners next to them
     *
     */
    $(document).on( 'click', '.slicewp-form-submit', function(e) {

        if ( ! $(this).closest('form')[0].checkValidity() ) {
            return true;
        }

        if ( $(this).hasClass( 'slicewp-disabled' ) ) {
            e.preventDefault();
            return false;
        }

        if ( ! $(this).hasClass( 'slicewp-spinner-inner' ) ) {

            if ( $(this).next().hasClass( '.slicewp-form-submit-spinner' ) ) {
                e.preventDefault();
                return false;
            }

        } else {

            if ( $(this).find( '.slicewp-form-submit-spinner' ).length > 0 ) {
                e.preventDefault();
                return false;
            }

        }
        
        // Handle confirmation cases.
        var confirmation = true;

        if ( typeof $(this).attr( 'data-confirmation-message' ) != 'undefined' ) {

            confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        }

        if ( confirmation == false ) {
            return false;
        }

        // Remove any onclick.
        $(this).removeAttr( 'onclick' );

        // Disable the button.
        $(this).addClass( 'slicewp-disabled' );

        // Add the spinner.
        if ( ! $(this).hasClass( 'slicewp-spinner-inner' ) ) {
            $(this).after( '<div class="spinner slicewp-form-submit-spinner"></div>' );
        } else {
            $(this).find( 'span' ).css( { opacity: 0 } );
            $(this).append( '<div class="spinner slicewp-form-submit-spinner"></div>' );
        }

    });


    /**
     * Makes an AJAX call to insert a new note into the database
     *
     */
    $(document).on( 'click', '.slicewp-add-note', function(e) {

        e.preventDefault();

        var $button = $(this);

        if( $button.hasClass( 'slicewp-disabled' ) )
            return false;

        if( $('#slicewp-note-content').val() == '' ) {
            $('#slicewp-note-content').focus();
            return false;
        }

        // Add animations
        $button.addClass( 'slicewp-disabled' );
        $('#slicewp-note-content').attr( 'disabled', true );
        $button.siblings( '.spinner' ).css( 'visibility', 'visible' ).css( 'opacity', 1 );

        // Prepare AJAX call data
        var data = {
            action         : 'slicewp_action_ajax_insert_note',
            slicewp_token  : $('#slicewp_token_notes').val(),
            object_context : $('[name="note_object_context"]').val(),
            object_id      : $('[name="note_object_id"]').val(),
            note_content   : $('#slicewp-note-content').val()
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            if ( response == 0 ) {
                return false;
            }

            // Remove the no notes message
            if ( $('#slicewp-notes-wrapper .slicewp-notes-empty').is( ':visible' ) ) {
                $('#slicewp-notes-wrapper .slicewp-notes-empty').stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 250 );
            }

            // Wait for the remove notes animation to finish
            setTimeout( function() {

                // Add the note to the top of the list
                $('#slicewp-notes-wrapper .slicewp-card-header').after( response );
                $('#slicewp-notes-wrapper .slicewp-note').removeClass( 'slicewp-first' )
                    .first().addClass( 'slicewp-first' )
                    .css( 'padding-top', 0 ).css( 'padding-bottom', 0 )
                    .stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 250 );

                // Remove the animations
                $button.removeClass( 'slicewp-disabled' );
                $('#slicewp-note-content').attr( 'disabled', false );
                $button.siblings( '.spinner' ).css( 'visibility', 'hidden' ).css( 'opacity', 0 );

                // Empty the textarea
                $('#slicewp-note-content').val( '' );

                // Update notes count
                $('#slicewp-notes-wrapper .slicewp-notes-count').html( parseInt( $('#slicewp-notes-wrapper .slicewp-note').length ) );

                // Set the IDs of the notes in the hidden fields
                var note_ids = [];

                $('#slicewp-notes-wrapper .slicewp-note').each( function() {
                    note_ids.push( parseInt( $(this).attr( 'data-note-id' ) ) )
                });

                $('[name="note_ids"]').val( note_ids.join( ',' ) );

            }, 250 );

        });

    });

    
    /**
     * Makes an AJAX call to delete a note from the database
     *
     */
    $(document).on( 'click', '.slicewp-note-delete', function(e) {

        e.preventDefault();

        // Handle confirmation cases
        var confirmation = true;

        if( typeof $(this).attr( 'data-confirmation-message' ) != 'undefined' ) {

            confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        }

        if( confirmation == false ) {
            return false;
        }

        var $link = $(this);

        $link.blur();

        // Add animations
        if( $link.find( '.slicewp-note-loading-overlay' ).length == 0 ) {
            $link.closest( '.slicewp-note' ).append( '<div class="slicewp-note-loading-overlay"><div class="spinner"></div></div>' );
            $link.closest( '.slicewp-note' ).find( '.slicewp-note-loading-overlay' ).fadeIn( 100 );
            $link.closest( '.slicewp-note' ).find( '.spinner' ).fadeIn( 100 );
        }

        // Prepare AJAX call data
        var data = {
            action         : 'slicewp_action_ajax_delete_note',
            slicewp_token  : $('#slicewp_token_notes').val(),
            note_id        : $link.closest( '.slicewp-note' ).attr( 'data-note-id' )
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            if( response == 0 )
                return false;

            // Animate the removal of the note
            $link.closest( '.slicewp-note' ).find( '.spinner' ).stop( true, false ).animate({ opacity: 0}, 100 );
            $link.closest( '.slicewp-note' ).stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 250 );

            setTimeout( function() {

                // Remove the actual note
                $link.closest( '.slicewp-note' ).remove();

                // Add the "slicewp-first" class to the first note
                $('#slicewp-notes-wrapper .slicewp-note').removeClass( 'slicewp-first' ).first().addClass( 'slicewp-first' );

                // Update notes count
                $('#slicewp-notes-wrapper .slicewp-notes-count').html( parseInt( $('#slicewp-notes-wrapper .slicewp-note').length ) );

                if( $('#slicewp-notes-wrapper .slicewp-note').length == 0 )
                    $('#slicewp-notes-wrapper .slicewp-notes-empty').stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 250 );

                // Set the IDs of the notes in the hidden fields
                var note_ids = [];

                $('#slicewp-notes-wrapper .slicewp-note').each( function() {
                    note_ids.push( parseInt( $(this).attr( 'data-note-id' ) ) )
                });

                $('[name="note_ids"]').val( note_ids.join( ',' ) );

            }, 250 );

        });

    });


    /**
     * Shows all notes
     *
     */
    $(document).on( 'click', '.slicewp-notes-view-all a', function(e) {

        e.preventDefault();

        $('.slicewp-note.slicewp-note-hidden')
            .css( 'padding-top', 0 ).css( 'padding-bottom', 0 )
            .stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 100 );

        $('.slicewp-notes-view-all')
            .stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 100 );

        setTimeout( function() {

            $('.slicewp-note.slicewp-note-hidden').removeClass( 'slicewp-note-hidden' );

        }, 100 );

    });


    /**
     * Integrations options fields: Shows/hides the commission rate based on the commission rate type selected
     *
     */
    $(document).on( 'change', '.slicewp-option-field-wrapper-commission-rate-type select', function() {

        var wrapper_classes = $(this).closest('.slicewp-option-field-wrapper')[0].className.split(' ');
        var commission_type = '';

        // Try to get the commission type
        for ( var i in wrapper_classes ) {

            if( wrapper_classes[i].indexOf( 'slicewp-commission-type-' ) == 0 )
                commission_type = wrapper_classes[i].replace( 'slicewp-commission-type-', '' );

        }

        // Hide or show the commission rate
        if ( commission_type != '' ) {

            // Grab the correct parent
            if ( $(this).closest('.slicewp-options-group').length == 0 )
                var $parent = $(this).closest('.slicewp-option-field-wrapper').parent();
            else
                var $parent = $(this).closest('.slicewp-options-group');

            if ( $(this).val() == '' ) {

                $parent.find( '.slicewp-option-field-wrapper-commission-rate.slicewp-commission-type-' + commission_type ).hide();

            } else {

                if ( ! $parent.find( '.slicewp-option-field-disable-commissions' ).is( ':checked' ) )
                    $parent.find( '.slicewp-option-field-wrapper-commission-rate.slicewp-commission-type-' + commission_type ).show();

            }

        }

    });

    /**
     * Integrations options fields: Shows/hides the elements with "slicewp-hide-if-disabled-commissions" class
     *                              when commissions are disabled for the options groups wrapper
     *
     */
    $(document).on( 'click', 'input[type="checkbox"].slicewp-option-field-disable-commissions', function() {

        show_hide_disabled_commissions_elements( $(this) );

    });

    $('input[type="checkbox"].slicewp-option-field-disable-commissions').each( function() {

        show_hide_disabled_commissions_elements( $(this) );

    });


    /**
     * Integrations options fields - WooCommerce: Show/hide the options groups if all commissions are
     *                                            disabled for the product when changing the product type
     *
     */
    $(document).on( 'change', 'body.post-type-product #product-type', function() {

        show_hide_product_subscription_elements( ( $(this).val() == 'subscription' || $(this).val() == 'variable-subscription' ? 'subscription' : 'product' ) );

    });

    if ( $('body.post-type-product #product-type').length > 0 ) {

        show_hide_product_subscription_elements( ( $('body.post-type-product #product-type').val() == 'subscription' || $('body.post-type-product #product-type').val() == 'variable-subscription' ? 'subscription' : 'product' ) );

    }


    /**
     * Integrations options fields - WooCommerce: Trigger the product type select when opening
     *
     */
    $(document).ajaxComplete( function( event, request, settings ) {

        if( typeof settings.data != 'undefined' ) {

            var params = new URLSearchParams( settings.data );

            if( params.get( 'action' ) == 'woocommerce_load_variations' ) {

                // Show/hide elements that are dependent on commissions being enabled
                $('.woocommerce_variable_attributes input[type="checkbox"].slicewp-option-field-disable-commissions').each( function() {

                    show_hide_disabled_commissions_elements( $(this) );

                });

                // Show/hide elements that depend on a product type
                show_hide_product_subscription_elements( ( $('#product-type').val() == 'subscription' || $('#product-type').val() == 'variable-subscription' ? 'subscription' : 'product' ) );

            }

        }

    });


    /**
     * Integrations options fields - EDD: Show/hide the options groups if all commissions are
     *                                    disabled for the product when changing the product type
     *
     */
    $(document).on( 'change', '#edd_recurring', function() {

        // Show/hide elements that depend on a product type
        show_hide_product_subscription_elements( ( $('#edd_recurring').val() == 'yes' ? 'subscription' : 'product' ) );

    });

	
    /**
     * Integrations options fields - GPD: Show/hide the options groups if all commissions are
     *                                    enabled for the product when changing the product type
     *
     */
    $(document).on( 'change', '#wpinv_is_recurring', function() {

        // Show/hide elements that depend on a product type
        show_hide_product_subscription_elements( ( $('#wpinv_is_recurring').is( ':checked' ) ? 'subscription' : 'product' ) );

    });

    if ( $('#wpinv_is_recurring').length > 0 ) {

        show_hide_product_subscription_elements( ( $('#wpinv_is_recurring').is( ':checked' ) ? 'subscription' : 'product' ) );
        
    }


	/**
	 * Integrations options fields - Studiocart: Hide the commission settings on page load
	 *
	 */
	$('body.post-type-sc_product .slicewp-show-if-product').addClass( 'slicewp-hidden' );
	$('body.post-type-sc_product .slicewp-show-if-subscription').addClass( 'slicewp-hidden' );


	/**
	 * Integrations options fields - Studiocart: Show the commission settings on page load based on product type
	 *
	 */
	$('body.post-type-sc_product .ridproduct_type').not(':last').each( function() {

		// Skip last element because it's reserved for the 'add new plan'
		show_product_subscription_elements( $(this).find('option:selected').val() );

	});


	/**
	 * Integrations options fields - Studiocart: Show the commission settings based on each product type selection
	 *
	 */
	$(document).on( 'change', 'body.post-type-sc_product select[name^="product_type["]', function() {

		// Hide the commission settings
		$('body.post-type-sc_product .slicewp-show-if-product').addClass( 'slicewp-hidden' );
		$('body.post-type-sc_product .slicewp-show-if-subscription').addClass( 'slicewp-hidden' );
	
		// Show the settings for the current product type
		show_product_subscription_elements( $(this).val() );

		// Show the settings for other product types except the last one, which is reserved for 'add new'
		$(this).parents('.repeater').siblings('.repeater').not(':last').each( function() {

			show_product_subscription_elements( $(this).find('select[name^="product_type["]').val() );
		
		});

	});


    /**
     * Shows elements that depend on a product type
     *
     */
	function show_product_subscription_elements( elements ) {

		if ( elements == 'recurring' )
			$('.slicewp-show-if-subscription').removeClass( 'slicewp-hidden' );
		else
			$('.slicewp-show-if-product').removeClass( 'slicewp-hidden' );

	}


    /**
     * Shows/hides elements that are dependent on commissions being enabled
     *
     */
    function show_hide_disabled_commissions_elements( $checkbox ) {

        // Grab the correct parent
        if( $checkbox.closest('.slicewp-options-groups-wrapper').length == 0 || $checkbox.closest('.slicewp-options-group').length == 0 )
            var $parent = $checkbox.closest('.slicewp-option-field-wrapper').parent();
        else
            var $parent = $checkbox.closest('.slicewp-options-groups-wrapper');

        if( $checkbox.is( ':checked' ) )
            $parent.find( '.slicewp-hide-if-disabled-commissions' ).hide();
        
        else {

            // Show all elements that should be hidden when commissions are disabled
            $parent.find( '.slicewp-hide-if-disabled-commissions' ).show();

            // Trigger the commission rate type change, so we hide the associated rates if needed
            $parent.find( '.slicewp-option-field-wrapper-commission-rate-type select' ).trigger( 'change' );

        }

    }


    /**
     * Shows/hides elements that depend on a product type
     *
     */
    function show_hide_product_subscription_elements( elements ) {

        if( elements == 'subscription' ) {

            $('.slicewp-show-if-product').addClass( 'slicewp-hidden' );
            $('.slicewp-show-if-subscription').removeClass( 'slicewp-hidden' );

        } else {

            $('.slicewp-show-if-product').removeClass( 'slicewp-hidden' );
            $('.slicewp-show-if-subscription').addClass( 'slicewp-hidden' );

        }

    }

    /**
     * Makes all cards on the Upgrade to Premium page the same height
     *
     */
    $(window).on( 'resize load', function() {

        var rows = $('.slicewp-wrap-upgrade-to-premium #slicewp-primary .slicewp-row');

        rows.each( function() {

            var min_height = 0;

            $(this).find( '.slicewp-card-inner' ).css( 'min-height', min_height );
            
            $(this).find( '.slicewp-card-inner' ).each( function() {

                if( $(this).height() > min_height )
                    min_height = $(this).height();

            });

            $(this).find( '.slicewp-card-inner' ).css( 'min-height', min_height );

        });

    });


    /**
     * Show add new file button.
     *
     */
    $('.slicewp-field-file-add-items').each( function() {

        // Always show button for multiple file field.
        if ( $(this).attr( 'data-multiple' ) == 'true' )
            $(this).show();

        // For single file field show the button only if there's no file.
        if ( $(this).attr( 'data-multiple' ) == 'false' && $(this).closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-file-item' ).length == 0 )
            $(this).show();

    });


    /**
     * Remove file item.
     *
     */
    $(document).on( 'click', '.slicewp-field-file-item-remove', function(e) {

        e.preventDefault();

        var $item = $(this).closest( '.slicewp-field-file-item' );

        if ( $item.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-file-item' ).length == 1 ) {

            // Show the no files field notice.
            $item.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-notice' ).show();

            // Show the add items button.
            $item.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-file-add-items' ).show();

        }

        $item.remove();

    });

    /**
     * Add file item.
     *
     */
    $(document).on( 'click', '.slicewp-field-file-add-items', function(e) {

        e.preventDefault();

        $btn_select = $(this);
        
        // Set is multiple
        var is_multiple = ( $btn_select.attr( 'data-multiple' ) == 'true' ? true : false )

        // Set current selected attachments.
        var selected_attachments = [];

        $(this).closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-file-item input[type="hidden"]' ).each( function() {
            selected_attachments.push( parseInt( $(this).val() ) );
        });

        // Create a new media frame
        frame = wp.media({
            title: ( is_multiple ? 'Choose Files' : 'Choose File' ),
            button: {
                text: ( is_multiple ? 'Add files' : 'Add file' )
            },
            multiple: is_multiple
        });

        // Select attachment from media frame.
        frame.on( 'select', function() {
      
            var attachments = frame.state().get('selection').toJSON();
            var html        = '';

            for ( var attachment of attachments ) {

                // Exclude already added attachments.
                if ( selected_attachments.indexOf( attachment.id ) != -1 )
                    continue;

                html += '<div class="slicewp-field-file-item">';
                    html += '<input type="hidden" name="' + $btn_select.attr( 'data-name' ) + '[]" value="' + attachment.id + '">';
                    html += '<a href="' + attachment.url + '" download="">' + attachment.filename + '</a>';
                    html += '<a href="#" class="slicewp-field-file-item-remove" title="Remove file"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></a>';
                html += '</div>';

            }

            $btn_select.before( html );

            // Hide the no files field notice.
            $btn_select.closest( '.slicewp-field-wrapper' ).find( '.slicewp-field-notice' ).hide();

            // Hide the add items button for single file fields.
            if ( ! is_multiple ) {

                $btn_select.hide();

            }

        });

        frame.open();

    });

    /**
     * Show the "no files selected" field notice on document ready.
     *
     */
    $('.slicewp-field-wrapper[data-type="file"]').each( function() {

        if ( $(this).find( '.slicewp-field-file-item' ).length == 0 ) {

            $(this).find( '.slicewp-field-notice' ).show();

        }

    });


    /**
     * Enable toggles for the dashboard cards postboxes.
     *
     */
    if ( typeof postboxes !== 'undefined' && pagenow == 'slicewp_page_slicewp-dashboard' ) {

        postboxes.add_postbox_toggles( pagenow );

    }


    /**
     * Dashboard page, Totals card.
     * 
     */
    $( '#slicewp-dashboard-widgets-wrap #totals .handle-actions' ).append( $('#slicewp-totals-period-filter-wrapper') );
    $('#slicewp-totals-period-filter-wrapper').show();

    $(document).on( 'change', '#slicewp-totals-period-filter', function(e) {

        var $this        = $(this);
        var $card_inside = $this.closest( '#totals' ).find( '.inside' );

        $card_inside.append( '<div class="slicewp-overlay"><div class="spinner"></div></div>' );

        setTimeout( function() {

            $card_inside.find( '.slicewp-card-table-full-width' ).hide();
            $card_inside.find( '.slicewp-card-table-full-width[data-period="' + $this.val() + '"]' ).show();

            $card_inside.find( '.slicewp-overlay' ).remove();

        }, 850 )

    });


    /**
     * Shows/hides the toggle actions.
     * 
     */
    $(document).on( 'click', 'html', function(e) {

        var $toggle_button = $(e.target).closest( '.slicewp-button-toggle-actions' );

        if ( $(e.target).closest( '.slicewp-actions-dropdown' ).length == 0 && $toggle_button.length == 0 ) {

            $('.slicewp-button-toggle-actions').removeClass( 'slicewp-active' );
            $('.slicewp-actions-dropdown').removeClass( 'slicewp-active' );

        }

        if ( $toggle_button.length > 0 ) {

            e.preventDefault();

            if ( $toggle_button.hasClass( 'slicewp-active' ) ) {

                $('.slicewp-button-toggle-actions').removeClass( 'slicewp-active' );
                $('.slicewp-actions-dropdown').removeClass( 'slicewp-active' );

            } else {

                $('.slicewp-button-toggle-actions').removeClass( 'slicewp-active' );
                $('.slicewp-actions-dropdown').removeClass( 'slicewp-active' );

                $toggle_button.addClass( 'slicewp-active' );
                $toggle_button.parent().find( '.slicewp-actions-dropdown' ).addClass( 'slicewp-active' );
                
            }

        }

    });


    /**
     * Shows the bulk actions on document ready.
     * 
     */
    $( 'body.slicewp-pagestyles .bulkactions .button' ).removeClass( 'button' );
    $( 'body.slicewp-pagestyles .bulkactions' ).css( 'display', 'flex' );

    $( document ).on( 'change', 'body.slicewp-pagestyles #bulk-action-selector-top', function(e) {

        // Add the confirmation message to the button if needed.
        $(this).siblings( '.slicewp-form-submit' ).removeAttr( 'data-confirmation-message' );

        if ( $(this).find( 'option[value="' + $(this).val() + '"]' ).attr( 'data-confirmation-message' ) != 'undefined' ) {

            $(this).siblings( '.slicewp-form-submit' ).attr( 'data-confirmation-message', $(this).find( 'option[value="' + $(this).val() + '"]' ).attr( 'data-confirmation-message' ) );

        }

        // Trigger the bottom bulk selector to match the top bulk selector.
        if ( e.target.tagName == 'SELECT' ) {

            $( 'body.slicewp-pagestyles #bulk-action-selector-bottom option' ).attr( 'selected', false );
            $( 'body.slicewp-pagestyles #bulk-action-selector-bottom option[value="' + $(this).val() + '"]' ).attr( 'selected', true ).trigger( 'change' );

        }

    });

    /**
     * Links the two bulk actions together and adds confirmation messages where needed.
     * 
     */
    $( document ).on( 'change', 'body.slicewp-pagestyles #bulk-action-selector-bottom', function(e) {

        // Add the confirmation message to the button if needed.
        $(this).siblings( '.slicewp-form-submit' ).removeAttr( 'data-confirmation-message' );

        if ( $(this).find( 'option[value="' + $(this).val() + '"]' ).attr( 'data-confirmation-message' ) != 'undefined' ) {

            $(this).siblings( '.slicewp-form-submit' ).attr( 'data-confirmation-message', $(this).find( 'option[value="' + $(this).val() + '"]' ).attr( 'data-confirmation-message' ) );

        }

        // Trigger the top bulk selector to match the bottom bulk selector.
        if ( e.target.tagName == 'SELECT' ) {

            $( 'body.slicewp-pagestyles #bulk-action-selector-top option' ).attr( 'selected', false );
            $( 'body.slicewp-pagestyles #bulk-action-selector-top option[value="' + $(this).val() + '"]' ).attr( 'selected', true ).trigger( 'change' );

        }

    });

    /**
	 * Resets the bulk actions when the filter button is clicked.
	 *
	 */
	$(document).on( 'mousedown focus', '.slicewp-table-filters input', function() {

		$('select[name^="action"]').val( '-1' );

	});

    $(document).on( 'change', '.slicewp-table-filters select', function() {

        $('select[name^="action"]').val( '-1' );

    });


    /**
     * Activate/Deactivate add-on.
     * 
     */
    $(document).on( 'click', '.slicewp-card-add-on .slicewp-switch.slicewp-is-ajax', function(e) {
       
        e.preventDefault();

        var $this  = $(this);
        var $input = $this.find( 'input' );

        if ( $this.hasClass( 'slicewp-loading' ) ) {
            return false;
        }

        $this.addClass( 'slicewp-loading' );
        $this.siblings( '.slicewp-tag-wrapper' ).addClass( 'slicewp-loading' );

        // Prepare AJAX call data
        var data = {
            action        : 'slicewp_action_ajax_' + ( $input.is( ':checked' ) ? 'deactivate' : 'activate' ) + '_add_on',
            add_on        : $input.val(),
            slicewp_token : $('#slicewp_token').val()
        }

        $.post( ajaxurl, data, function( response ) {
        
            if ( response === 0 ) {
                return false;
            }

            if ( response.success ) {

                setTimeout( function() {

                    if ( $input.is( ':checked' ) ) {

                        $input.attr( 'checked', false );

                        $this.parent().find( '.slicewp-tag-add-on-active' ).fadeOut( 200, function() {
                            $this.parent().find( '.slicewp-tag-add-on-inactive' ).fadeIn( 200 );
                            $this.siblings( '.slicewp-tag-wrapper' ).removeClass( 'slicewp-loading' );
                        });

                        $this.closest( '.slicewp-card-footer' ).find( '.slicewp-card-add-on-actions a' ).fadeOut( 200 );
    
                    } else {
    
                        $input.attr( 'checked', true );

                        $this.parent().find( '.slicewp-tag-add-on-inactive' ).fadeOut( 200, function() {
                            $this.parent().find( '.slicewp-tag-add-on-active' ).fadeIn( 200 );
                            $this.siblings( '.slicewp-tag-wrapper' ).removeClass( 'slicewp-loading' );
                        });

                        $this.closest( '.slicewp-card-footer' ).find( '.slicewp-card-add-on-actions a' ).fadeIn( 200 );
    
                    }
    
                    $this.removeClass( 'slicewp-loading' );

                }, 500 );

            } else {

                setTimeout( function() {

                    if ( $input.is( ':checked' ) ) {

                        $input.attr( 'checked', true );

                        $this.parent().find( '.slicewp-tag-add-on-inactive' ).fadeOut( 200, function() {
                            $this.parent().find( '.slicewp-tag-add-on-active' ).fadeIn( 200 );
                            $this.siblings( '.slicewp-tag-wrapper' ).removeClass( 'slicewp-loading' );
                        });

                        $this.closest( '.slicewp-card-footer' ).find( '.slicewp-card-add-on-actions a' ).fadeIn( 200 );
    
                    } else {
    
                        $input.attr( 'checked', false );

                        $this.parent().find( '.slicewp-tag-add-on-active' ).fadeOut( 200, function() {
                            $this.parent().find( '.slicewp-tag-add-on-inactive' ).fadeIn( 200 );
                            $this.siblings( '.slicewp-tag-wrapper' ).removeClass( 'slicewp-loading' );

                            $this.closest( '.slicewp-card-footer' ).find( '.slicewp-card-add-on-actions a' ).fadeOut( 200 );
                        });
    
                    }
    
                    $this.removeClass( 'slicewp-loading' );
                    
                }, 500 );

            }

        });

    });


    /**
     * Setup wizard
     * 
     * Get started button click.
     * 
     */
    $(document).on( 'click', '.slicewp-setup-welcome-start-button', function(e) {

        e.preventDefault();

        // Hide setup welcome elements.
        $('.slicewp-setup-skip').hide();

        $('.slicewp-setup-welcome-start-button').animate( { 'opacity': 0, 'top': '40px' }, 160 );

        setTimeout( function() {

            $('.slicewp-setup-welcome-subheading').animate( { 'opacity': 0, 'top': '40px' }, 160 );

        }, 135 );

        setTimeout( function() {

            $('.slicewp-setup-welcome-heading').animate( { 'opacity': 0, 'top': '40px' }, 160, function() {
                $('.slicewp-setup-welcome-panel').hide();
            });

        }, 230 );

        // Show the setup steps.
        setTimeout( function() {

            $('.slicewp-setup-steps-wrapper').show().animate( { 'opacity': '1', 'top': 0 }, 230 );

        }, 600 );

        setTimeout( function() {

            $('.slicewp-card-setup-integrations').show().animate( { 'opacity': '1', 'top': 0 }, 260 );

        }, 775 );

    });


    /**
     * Setup wizard
     * 
     * Handle the collection of data from the current step and continue to the next step.
     * 
     */
     $(document).on( 'click', '.slicewp-submit-wrapper-setup-wizard .slicewp-button-primary', function(e) {

        e.preventDefault();
        
        var $button = $(this);
        var $card   = $button.closest( '.slicewp-card' );

        var form_data     = {};
        var form_data_raw = $card.find( 'form' ).serializeArray();

        $.each( form_data_raw, function() {

            this.name = this.name.replace( '[]', '' );
            
            if ( form_data[this.name] ) {

                if ( ! form_data[this.name].push ) {
                    form_data[this.name] = [form_data[this.name]];
                }

                form_data[this.name].push(this.value || '');

            } else {

                form_data[this.name] = this.value || '';

            }

        });

        // Disable the continue button, the skip button and show the spinner.
        $button.addClass( 'slicewp-disabled' );

        if ( $button.siblings( '.slicewp-button-tertiary' ).length > 0 ) {

            $button.siblings( '.slicewp-button-tertiary' ).fadeOut( 100, function() {
                $button.siblings( '.spinner' ).fadeIn( 100 );
            });

        } else {

            $button.siblings( '.spinner' ).fadeIn( 100 );

        }
        
        // Prepare AJAX call data.
        var data = {
            action        : 'slicewp_action_ajax_process_setup_wizard_step_' + $(this).attr( 'data-step' ),
            slicewp_token : $('#slicewp_token').val(),
            form_data     : form_data
        };

        // Make AJAX call.
        $.post( ajaxurl, data, function( response ) {

            if ( response == 0 ) {
                return false;
            }

            setTimeout( function() {

                if ( is_setup_wizard_steps_in_view() ) {

                    setup_wizard_next_step();
        
                } else {
        
                    $( 'html, body' ).animate( { scrollTop: parseInt( $('.slicewp-setup-steps-wrapper').offset().top ) - 50 }, 350, function() {
        
                        setup_wizard_next_step();
                        
                    });
                    
                }

            }, 350 );

        });

    });


    /**
     * Setup wizard
     * 
     * Handle the "skip step" action.
     * 
     */
    $(document).on( 'click', '.slicewp-submit-wrapper-setup-wizard .slicewp-button-tertiary', function(e) {

        e.preventDefault();

        var $card = $(this).closest( '.slicewp-card' );

        if ( is_setup_wizard_steps_in_view() ) {

            setup_wizard_next_step();

        } else {

            $( 'html, body' ).animate( { scrollTop: parseInt( $('.slicewp-setup-steps-wrapper').offset().top ) - 50 }, 350, function() {

                setup_wizard_next_step();
                
            });
            
        }

    });


    /**
     * Checks if the element showing the setup wizard steps is in view or not.
     * 
     */
    function is_setup_wizard_steps_in_view() {

        return ( $('.slicewp-setup-steps-wrapper').offset().top > $(window).scrollTop() && $('.slicewp-setup-steps-wrapper').offset().top < $(window).scrollTop() + $(window).innerHeight() );

    }

    /**
     * Progresses the setup wizard to the next step.
     *
     */
    function setup_wizard_next_step() {

        var $card = $('.slicewp-card:visible').first();

        // Hide current card and show the next one.
        $card.animate( { 'opacity': '0', 'left': '-40px' }, 230, function() {

            $card.hide();
            
            $card.next( '.slicewp-card' ).css( { 'display': 'block' } ).animate( { 'opacity': 1, 'left': '0' }, 230 );

        });

        // Progress the setup steps.
        $( '.slicewp-setup-steps .slicewp-setup-step.slicewp-current' ).addClass( 'slicewp-done' ).removeClass( 'slicewp-current' );

        setTimeout( function() {

            $( '.slicewp-setup-steps .slicewp-setup-step.slicewp-done' ).last().next( '.slicewp-setup-step' ).addClass( 'slicewp-current' );

        }, 230 );

    }

    /**
     * Closes screen overlay modals.
     * 
     */
    $(document).on( 'click', '.slicewp-close-modal', function(e) {

        e.preventDefault();

        var $overlay = $(this).closest( '.slicewp-screen-overlay' );

        $overlay.removeClass( 'slicewp-active' );

    });


    /**
     * Removes the filters names from the filters reset link and displays the link
     * if any filters are present in the URL.
     * 
     */
    $('.slicewp-list-table-data-filters-reset').each( function() {

        var $this         = $(this);
        var filters_names = [];

        // Remove the filters query attributes from the reset URL.
        $(this).parent().find( '.slicewp-list-table-data-filter [name]' ).each( function() {

            $this.attr( 'href', remove_query_arg( $(this).attr( 'name' ), $this.attr( 'href' ) ) );

            filters_names.push( $(this).attr( 'name' ) );

        });

        // Show the filters reset link if filters are present.
        var url_params = new URLSearchParams( window.location.search );

        for ( var i in filters_names ) {
            
            if ( url_params.get( filters_names[i] ) ) {

                $this.show();
                break;

            }

        }

    });

});