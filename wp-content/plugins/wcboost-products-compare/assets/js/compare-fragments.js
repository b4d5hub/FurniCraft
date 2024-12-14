/* global wcboost_products_compare_fragments_params, wcboost_products_compare_params, woocommerce_params */
jQuery( function( $ ) {

	if ( typeof wcboost_products_compare_fragments_params === 'undefined' ) {
		return false;
	}

	/* Storage Handling */
	var supportStorage = true,
		hashkey = wcboost_products_compare_fragments_params.hash_key;

	try {
		supportStorage = ( 'sessionStorage' in window && window.sessionStorage !== null );
		window.sessionStorage.setItem( 'wcboost', 'test' );
		window.sessionStorage.removeItem( 'wcboost' );
		window.localStorage.setItem( 'wcboost', 'test' );
		window.localStorage.removeItem( 'wcboost' );
	} catch( err ) {
		supportStorage = false;
	}

	var WCBoostProductsCompareFragments = function() {
		var self = this;

		// Methods.
		this.updateFragments = this.updateFragments.bind( this );
		this.getProductIds   = this.getProductIds.bind( this );

		// Events.
		$( document.body )
			.on( 'products_compare_fragments_refresh products_compare_list_updated', { productsCompareFragments: self }, self.refreshFragments )
			.on( 'products_compare_fragments_refreshed wcboost_compare_item_added wcboost_compare_item_removed', { productsCompareFragments: this }, this.updateStorage )
			.on( 'added_to_compare removed_from_compare', { productsCompareFragments: self }, self.updateFragmentsOnChanges )
			.on( 'wcboost_compare_storage_updated', { productsCompareFragments: self }, self.updateButtons );

		// Refresh fragments if the option is enabled.
		if ( 'yes' === wcboost_products_compare_fragments_params.refresh_on_load ) {
			$( document.body ).trigger( 'products_compare_fragments_refresh' );
		} else {
			// Refresh when page is shown after back button (safari).
			$( window ).on( 'pageshow' , function( event ) {
				if ( event.originalEvent.persisted ) {
					$( document.body ).trigger( 'products_compare_fragments_refresh', [ true ] );
				}
			} );

			if ( supportStorage ) {
				// Refresh when storage changes in another tab
				$( window ).on( 'storage onstorage', function ( e ) {
					if ( hashkey === e.originalEvent.key && localStorage.getItem( hashkey ) !== sessionStorage.getItem( hashkey ) ) {
						$( document.body ).trigger( 'products_compare_fragments_refresh' );
					}
				});

				try {
					var fragments = JSON.parse( sessionStorage.getItem( wcboost_products_compare_fragments_params.fragment_name ) ),
						hash = sessionStorage.getItem( hashkey ),
						cookie_hash = Cookies.get( 'wcboost_compare_hash' ),
						localHash = localStorage.getItem( hashkey );

					if ( fragments !== null && hash === localHash && hash === cookie_hash ) {
						this.updateFragments( fragments );
						this.updateButtons();
					} else {
						// Trigger refreshFragments in the catch block.
						throw 'No compare fragment';
					}
				} catch ( err ) {
					this.refreshFragments();
				}
			} else {
				this.refreshFragments();
			}
		}

		// Customiser support.
		var hasSelectiveRefresh = (
			'undefined' !== typeof wp &&
			wp.customize &&
			wp.customize.selectiveRefresh &&
			wp.customize.widgetsPreview &&
			wp.customize.widgetsPreview.WidgetPartial
		);

		if ( hasSelectiveRefresh ) {
			wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
				self.refreshFragments();
			} );
		}
	}

	WCBoostProductsCompareFragments.prototype.refreshFragments = function( event, includeButtons ) {
		var self = event ? event.data.productsCompareFragments : this;
		var data = { time: new Date().getTime() };

		if ( 'yes' === wcboost_products_compare_fragments_params.refresh_on_load || includeButtons ) {
			data.product_button_ids = self.getProductIds();
		}

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_compare_fragments' ),
			data: data,
			dataType: 'json',
			timeout: wcboost_products_compare_fragments_params.request_timeout,
			success: function( response ) {
				if ( ! response.success ) {
					$( document.body ).trigger( 'products_compare_fragments_failed' );

					return;
				}

				self.updateFragments( response.data.fragments );

				$( document.body ).trigger( 'products_compare_fragments_refreshed', [ response.data ] );
			},
			error: function() {
				$( document.body ).trigger( 'products_compare_fragments_ajax_error' );
			}
		} );
	}

	WCBoostProductsCompareFragments.prototype.getProductIds = function() {
		var ids = [];

		$( '.wcboost-products-compare-button' ).each( function( index, button ) {
			ids.push( button.dataset.product_id );
		} );

		return ids;
	}

	WCBoostProductsCompareFragments.prototype.updateFragmentsOnChanges = function( event, $button, fragments ) {
		var self = event.data.productsCompareFragments;

		self.updateFragments( fragments );
	}

	WCBoostProductsCompareFragments.prototype.updateStorage = function( event, data ) {
		if ( ! supportStorage ) {
			return;
		}

		var compare_hash = data.compare_hash ? data.compare_hash : '';

		localStorage.setItem( hashkey, compare_hash );
		sessionStorage.setItem( hashkey, compare_hash );

		if ( data.compare_items ) {
			sessionStorage.setItem( wcboost_products_compare_fragments_params.list_name, JSON.stringify( data.compare_items ) );
		}

		if ( data.fragments ) {
			sessionStorage.setItem( wcboost_products_compare_fragments_params.fragment_name, JSON.stringify( data.fragments ) );
		}

		$( document.body ).trigger( 'wcboost_compare_storage_updated' );
	}

	WCBoostProductsCompareFragments.prototype.updateButtons = function( event ) {
		if ( ! supportStorage ) {
			return;
		}

		var self = event ? event.data.productsCompareFragments : this,
			items = JSON.parse( sessionStorage.getItem( wcboost_products_compare_fragments_params.list_name ) );

		if ( ! items ) {
			return;
		}

		$( '.wcboost-products-compare-button' ).each( function() {
			var product_id = this.dataset.product_id,
				data = items[ product_id ] ? items[ product_id ] : null;

			self.updateButtonStatus( this, data );
		} );
	}

	WCBoostProductsCompareFragments.prototype.updateButtonStatus = function( button, data ) {
		var $button = $( button );

		if ( ! $button.length ) {
			return;
		}

		if ( data ) {
			if ( $button.hasClass( 'added' ) ) {
				return;
			}

			$button.removeClass( 'loading' ).addClass( 'added' );

			switch ( wcboost_products_compare_params.exists_item_behavior ) {
				case 'view':
					$button.attr( 'href', wcboost_products_compare_params.page_url );
					$button.find( '.wcboost-products-compare-button__text' ).text( wcboost_products_compare_params.i18n_button_view );
					$button.find( '.wcboost-products-compare-button__icon' ).html( wcboost_products_compare_params.icon_checked );
					break;

				case 'remove':
					$button.attr( 'href', data.remove_url );
					$button.find( '.wcboost-products-compare-button__text' ).text( wcboost_products_compare_params.i18n_button_remove );
					$button.find( '.wcboost-products-compare-button__icon' ).html( wcboost_products_compare_params.icon_checked );
					break;

				case 'popup':
					$button.attr( 'href', wcboost_products_compare_params.page_url );
					$button.find( '.wcboost-products-compare-button__text' ).text( wcboost_products_compare_params.i18n_button_view );
					$button.find( '.wcboost-products-compare-button__icon' ).html( wcboost_products_compare_params.icon_checked );
					$button.addClass( 'wcboost-products-compare-button--popup' );
					break;

				case 'hide':
					$button.hide();
					break;
			}
		} else {
			if ( ! $button.hasClass( 'added' ) && ! $button.hasClass( 'loading' ) ) {
				return;
			}

			$button.removeClass( 'added loading' );

			$button.attr( 'href', '?add_to_compare=' + $button.data( 'product_id' ) );
			$button.find( '.wcboost-products-compare-button__text' ).text( wcboost_products_compare_params.i18n_button_add );
			$button.find( '.wcboost-products-compare-button__icon' ).html( wcboost_products_compare_params.icon_normal );
		}
	}

	WCBoostProductsCompareFragments.prototype.updateFragments = function( fragments ) {
		$.each( fragments, function( key, value ) {
			$( key ).replaceWith( value );
		} );

		$( document.body ).trigger( 'products_compare_fragments_loaded' );
	}


	new WCBoostProductsCompareFragments();
} );
