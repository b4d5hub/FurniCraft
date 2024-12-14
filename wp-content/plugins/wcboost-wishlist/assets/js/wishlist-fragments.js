/* global wcboost_wishlist_fragments_params, wcboost_wishlist_params */
jQuery( function( $ ) {
	if ( typeof wcboost_wishlist_fragments_params === 'undefined' ) {
		return false;
	}

	/* Storage Handling */
	var supportStorage = true,
		hash_key_name = wcboost_wishlist_fragments_params.hash_name;

	try {
		supportStorage = ( 'sessionStorage' in window && window.sessionStorage !== null );
		window.sessionStorage.setItem( 'wcboost', 'test' );
		window.sessionStorage.removeItem( 'wcboost' );
		window.localStorage.setItem( 'wcboost', 'test' );
		window.localStorage.removeItem( 'wcboost' );
	} catch( err ) {
		supportStorage = false;
	}

	/**
	 * Wishlist fragments class.
	 */
	var WCBoostWishlistFragments = function() {
		var self = this;

		this.updateFragments = this.updateFragments.bind( this );
		this.getProductIds   = this.getProductIds.bind( this );

		$( document.body )
			.on( 'wishlist_fragments_refresh wishlist_updated', { wishlistFragmentsHandler: this }, this.refreshFragments )
			.on( 'wishlist_fragments_refreshed wishlist_item_added wishlist_item_removed', { wishlistFragmentsHandler: this }, this.updateStorage )
			.on( 'added_to_wishlist removed_from_wishlist', { wishlistFragmentsHandler: this }, this.updateFragmentsOnChanges )
			.on( 'wishlist_storage_updated', { wishlistFragmentsHandler: this }, this.updateButtons );

		// Refresh when storage changes in another tab.
		$( window ).on( 'storage onstorage', function( e ) {
			if ( hash_key_name === e.originalEvent.key  && localStorage.getItem( hash_key_name ) !== sessionStorage.getItem( hash_key_name ) ) {
				$( document.body ).trigger( 'wishlist_fragments_refresh' );
			}
		} );

		// Refresh fragments if the option is enabled.
		if ( 'yes' === wcboost_wishlist_fragments_params.refresh_on_load ) {
			$( document.body ).trigger( 'wishlist_fragments_refresh' );
		} else {
			// Refresh when page is shown after back button (Safari).
			$( window ).on( 'pageshow', function( event ) {
				if ( event.originalEvent.persisted ) {
					$( document.body ).trigger( 'wishlist_fragments_refresh', [ true ] );
				}
			} );

			try {
				var wishlist_hash = sessionStorage.getItem( hash_key_name ),
					cookie_hash = Cookies.get( 'wcboost_wishlist_hash' );

				if ( wishlist_hash !== null && wishlist_hash !== '' && wishlist_hash === cookie_hash ) {
					this.updateFragmentsFromStorage();
					this.updateButtons();
				} else {
					throw 'No wishlist fragment';
				}
			} catch ( err ) {
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

	WCBoostWishlistFragments.prototype.refreshFragments = function( event, updateButtons, options ) {
		var self = event ? event.data.wishlistFragmentsHandler : this;
		var data = { time: new Date().getTime() };

		if ( 'yes' === wcboost_wishlist_fragments_params.refresh_on_load || updateButtons ) {
			data.product_ids = self.getProductIds();
		}

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_wishlist_fragments' ),
			data: data,
			dataType: 'json',
			timeout: wcboost_wishlist_fragments_params.request_timeout,
			success: function( response ) {
				if ( ! response.success ) {
					$( document.body ).trigger( 'wishlist_fragments_failed' );

					return;
				}

				self.updateFragments( response.data.fragments );

				$( document.body ).trigger( 'wishlist_fragments_refreshed', [ response.data, options ] );
			},
			error: function() {
				$( document.body ).trigger( 'wishlish_fragments_ajax_error' );
			}
		} );
	}

	WCBoostWishlistFragments.prototype.getProductIds = function() {
		var ids = [];

		$( '.wcboost-wishlist-button' ).each( function( index, button ) {
			ids.push( button.dataset.product_id );
		} );

		return ids;
	}

	WCBoostWishlistFragments.prototype.updateFragmentsOnChanges = function( event, $button, fragments ) {
		var self = event.data.wishlistFragmentsHandler;

		self.updateFragments( fragments );

		// Update buttons on product grid changes were made from elsewhere.
		// Since 1.1.0, this was handled with JS if sessionStorage is supported.
		if ( ! $button && ! supportStorage ) {
			self.refreshFragments( event, true );
		}
	}

	WCBoostWishlistFragments.prototype.updateFragmentsFromStorage = function() {
		if ( ! supportStorage ) {
			return;
		}

		var wishlist_hash = sessionStorage.getItem( hash_key_name );

		if ( ! wishlist_hash ) {
			return;
		}

		var hash_parts = wishlist_hash.split( '::' ),
			hash_key = hash_parts[0];

		if ( hash_key ) {
			var fragments = JSON.parse( sessionStorage.getItem( 'wcboost_wishlist_fragments_' + hash_key ) );

			if ( fragments !== null ) {
				this.updateFragments( fragments );
			}
		} else {
			this.refreshFragments();
		}
	}

	WCBoostWishlistFragments.prototype.updateStorage = function( event, data, options ) {
		if ( ! supportStorage ) {
			return;
		}

		var wishlist_hash = data.wishlist_hash ? data.wishlist_hash : '';

		sessionStorage.setItem( hash_key_name, wishlist_hash );
		localStorage.setItem( hash_key_name, wishlist_hash );

		if ( wishlist_hash ) {
			var hash_parts = wishlist_hash.split( '::' ),
				hash_key = hash_parts[0];

			if ( data.wishlist_items ) {
				sessionStorage.setItem( 'wcboost_wishlist_' + hash_key, JSON.stringify( data.wishlist_items ) );
			}

			if ( data.fragments ) {
				sessionStorage.setItem( 'wcboost_wishlist_fragments_' + hash_key, JSON.stringify( data.fragments ) );
			}

			$( document.body ).trigger( 'wishlist_storage_updated' );
		}
	}

	WCBoostWishlistFragments.prototype.updateFragments = function( fragments ) {
		$.each( fragments, function( key, value ) {
			$( key ).replaceWith( value );
		} );

		$( document.body ).trigger( 'wishlist_fragments_loaded' );
	}

	WCBoostWishlistFragments.prototype.updateButtons = function( event ) {
		if ( ! supportStorage ) {
			return;
		}

		var wishlist_hash = sessionStorage.getItem( hash_key_name );

		if ( ! wishlist_hash ) {
			return;
		}

		var hash_parts = wishlist_hash.split( '::' ),
			hash_key = hash_parts[0];

		if ( ! hash_key ) {
			return;
		}

		var items = JSON.parse( sessionStorage.getItem( 'wcboost_wishlist_' + hash_key ) );

		if ( items === null ) {
			return;
		}

		var self = event ? event.data.wishlistFragmentsHandler : this;

		$( '.wcboost-wishlist-button' ).each( function() {
			var product_id = this.dataset.product_id,
				data = items[ product_id ] ? items[ product_id ] : null;

			self.updateButtonStatus( this, data );

			if ( this.dataset.variations ) {
				self.updateButtonVariations( this, items );
			}
		} );
	}

	WCBoostWishlistFragments.prototype.updateButtonStatus = function( button, data ) {
		var $button = $( button );

		if ( ! $button.length ) {
			return;
		}

		if ( data ) {
			// No need to update correct button.
			if ( $button.hasClass( 'added' ) ) {
				return;
			}

			$button.removeClass( 'loading' ).addClass( 'added' );

			switch ( wcboost_wishlist_params.exists_item_behavior ) {
				case 'view_wishlist':
					$button.attr( 'href', data.wishlist_url ? data.wishlist_url : wcboost_wishlist_params.wishlist_url );
					$button.find( '.wcboost-wishlist-button__text' ).text( wcboost_wishlist_params.i18n_view_wishlist );
					$button.find( '.wcboost-wishlist-button__icon' ).html( wcboost_wishlist_params.icon_filled );
					break;

				case 'remove':
					$button.attr( 'href', data.remove_url );
					$button.find( '.wcboost-wishlist-button__text' ).text( wcboost_wishlist_params.i18n_remove_from_wishlist );
					$button.find( '.wcboost-wishlist-button__icon' ).html( wcboost_wishlist_params.icon_filled );
					break;

				case 'hide':
					$button.hide();
					break;
			}
		} else {
			// No need to update correct button.
			if ( ! $button.hasClass( 'added' ) && ! $button.hasClass( 'loading' ) ) {
				return;
			}

			$button.removeClass( 'added loading' );
			$button.attr( 'href', '?add-to-wishlist=' + $button.data( 'product_id' ) );
			$button.find( '.wcboost-wishlist-button__text' ).text( wcboost_wishlist_params.i18n_add_to_wishlist );
			$button.find( '.wcboost-wishlist-button__icon' ).html( wcboost_wishlist_params.icon_normal );
		}
	}

	WCBoostWishlistFragments.prototype.updateButtonVariations = function( button, items ) {
		var $button = $( button );

		if ( ! $button.length || ! $button.data( 'variations' ) ) {
			return;
		}

		var variations = $button.data( 'variations' );

		for ( var i in variations ) {
			variations[ i ] = $.extend(
				{},
				variations[ i ],
				{ 'added': items[ variations[ i ].variation_id ] === undefined ? 'no' : 'yes' }
			);
		}

		$button.data( 'variations', variations );
	}

	// Init wishlist fragments.
	new WCBoostWishlistFragments();
} );
