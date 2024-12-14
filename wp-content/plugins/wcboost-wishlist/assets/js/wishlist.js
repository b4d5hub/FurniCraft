/* global wcboost_wishlist_params */
( function( $ ) {
	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! $.fn.block || ! $node ) {
			return;
		}

		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		if ( ! $.fn.unblock || ! $node ) {
			return;
		}

		$node.removeClass( 'processing' ).unblock();
	};

	/**
	 * Flyout notices
	 *
	 * @param {string} message
	 * @param {object} options
	 */
	var WCBoostNotice = function( message, options ) {
		this.options = $.extend( {
			duration: 3000,
			type: 'notice',
		}, options );

		this.$notice = null;
		this.$wrapper = $( '#wcboost-flyout-notices-container' );

		if ( ! this.$wrapper.length ) {
			this.$wrapper = $( '<div id="wcboost-flyout-notices-container" class="wcboost-flyout-notices-container" />' ).appendTo( document.body );
		}

		this.$notice = $( '<div class="wcboost-flyout-notice wcboost-notice" role="alert"/>' )
			.addClass( 'wcboost-notice--' + this.options.type )
			.append( $( message ) );

		this.wait = null;

		// Methods.
		this.show = this.show.bind( this );
		this.hide = this.hide.bind( this );
		this.delayHide = this.delayHide.bind( this );
		this.resetDelayHide = this.resetDelayHide.bind( this );

		// Events.
		if ( this.options.duration > 0 ) {
			this.$notice
				.on( 'mouseenter', this.resetDelayHide )
				.on( 'mouseleave', this.delayHide );
		}
	}

	WCBoostNotice.prototype.show = function() {
		if ( ! this.$notice ) {
			return;
		}

		this.$notice.hide();
		this.$wrapper.append( this.$notice );
		this.$notice.fadeIn();

		if ( this.options.duration > 0 ) {
			this.delayHide();
		}
	}

	WCBoostNotice.prototype.delayHide = function() {
		this.resetDelayHide();
		this.wait = setTimeout( this.hide, this.options.duration );
	}

	WCBoostNotice.prototype.hide = function() {
		this.$notice.fadeOut();
	}

	WCBoostNotice.prototype.resetDelayHide = function() {
		clearTimeout( this.wait );
	}

	/**
	 * AddToWishlistHandler class
	 */
	var AddToWishlistHandler = function() {
		this.selectors = {
			text: '.wcboost-wishlist-button__text',
			icon: '.wcboost-wishlist-button__icon',
		};

		this.addToWishlist      = this.addToWishlist.bind( this );
		this.removeFromWishlist = this.removeFromWishlist.bind( this );
		this.updateButton       = this.updateButton.bind( this );

		// Events.
		$( document.body ).on( 'click', '.wcboost-wishlist-button--ajax', { addToWishlistHandler: this }, this.onButtonClick );

		if ( 'yes' === wcboost_wishlist_params.allow_adding_variations ) {
			$( '.variations_form' )
				.on( 'found_variation', { addToWishlistHandler: this }, this.onVariationFound )
				.on( 'reset_data', { addToWishlistHandler: this }, this.onVariationReset );
		}
	}

	AddToWishlistHandler.prototype.onButtonClick = function( event ) {
		var self = event.data.addToWishlistHandler;
		var $button = $( event.currentTarget );

		if ( ! $button.hasClass( 'added' ) ) {
			event.preventDefault();
			self.addToWishlist( $button );
		} else if ( 'remove' === wcboost_wishlist_params.exists_item_behavior ) {
			event.preventDefault();
			self.removeFromWishlist( $button );
		}
	}

	AddToWishlistHandler.prototype.addToWishlist = function( $button ) {
		var self = this;
		var data = {
			product_id: $button.data( 'product_id' ),
			quantity: $button.data( 'quantity' ),
		};

		if ( ! data.product_id ) {
			return;
		}

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_wishlist' ),
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$button.removeClass( 'added' ).addClass( 'loading' );
				self.updateButton( $button, 'loading' );
			},
			success: function( response ) {
				if ( ! response.success ) {
					if ( response.data && response.data.message ) {
						var notice = new WCBoostNotice( response.data.message );
						notice.show();
					}

					if ( response.data && response.data.redirect_url ) {
						location.href = response.data.redirect_url;
					}

					self.updateButton( $button, 'removed' );
					return;
				}

				var fragments = response.data.fragments;

				self.updateButton( $button, 'added', response.data );

				// if ( $button.data( 'variations' ) ) {
				// 	self.updateButton( $button, 'update_variations', { variation_id: response.data.product_id, added: 'yes' } );
				// }

				$( document.body )
					.trigger( 'added_to_wishlist', [ $button, fragments ] )
					.trigger( 'wishlist_item_added', [ response.data ] );

				if ( 'yes' === wcboost_wishlist_params.wishlist_redirect_after_add ) {
					window.location = wcboost_wishlist_params.wishlist_url;
				}
			},
			complete: function() {
				$button.removeClass( 'loading' );
			}
		} );
	}

	AddToWishlistHandler.prototype.removeFromWishlist = function( $button ) {
		var self = this;
		var params = new URLSearchParams( $button[0].search );
		var data = {
			item_key: params.get( 'remove-wishlist-item' ),
			_wpnonce: params.get( '_wpnonce' ),
		};

		if ( ! data.item_key ) {
			return;
		}

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_wishlist_item' ),
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$button.removeClass( 'added' ).addClass( 'loading' );
				self.updateButton( $button, 'loading' );
			},
			success: function( response ) {
				if ( ! response.success ) {
					return;
				}

				var fragments = response.data.fragments;

				self.updateButton( $button, 'removed', response.data );

				// if ( $button.data( 'variations' ) ) {
				// 	self.updateButton( $button, 'update_variations', { variation_id: response.data.product_id, added: 'no' } );
				// }

				$( document.body )
					.trigger( 'removed_from_wishlist', [ $button, fragments ] )
					.trigger( 'wishlist_item_removed', [ response.data ] );
			},
			complete: function() {
				$button.removeClass( 'loading' );
			}
		} );
	}

	AddToWishlistHandler.prototype.onVariationFound = function( event, data ) {
		var self       = event.data.addToWishlistHandler;
		var $button    = $( event.target ).closest( '.product' ).find( '.wcboost-wishlist-button' );
		var variations = $button.data( 'variations' );

		self.updateButton( $button, 'update_id', { product_id: data.variation_id } );

		if ( 'yes' === wcboost_wishlist_params.allow_adding_variations && variations ) {
			var found = variations.find( function( variation ) {
				return variation.variation_id === data.variation_id;
			} );

			if ( found ) {
				self.updateButton( $button, found.added === 'yes' ? 'added' : 'removed', found );
			}
		}
	}

	AddToWishlistHandler.prototype.onVariationReset = function( event ) {
		var self       = event.data.addToWishlistHandler;
		var $button    = $( event.target ).closest( '.product' ).find( '.wcboost-wishlist-button' );
		var variations = $button.data( 'variations' );
		var product_id = null;

		self.updateButton( $button, 'update_id', { product_id: null } );

		if ( variations ) {
			var parent = variations.find( function( variation ) {
				return variation.is_parent;
			} );

			if ( parent ) {
				product_id = parent.variation_id;
			}

			if ( 'yes' === wcboost_wishlist_params.allow_adding_variations && parent ) {
				self.updateButton( $button, parent.added === 'yes' ? 'added' : 'removed', parent );
			}
		}

		if ( ! product_id ) {
			var params = new URLSearchParams( $button[0].search );
			product_id = params.get( 'add-to-wishlist' );
		}

		self.updateButton( $button, 'update_id', { product_id: product_id } );
	}

	AddToWishlistHandler.prototype.updateButton = function( $button, status, data ) {
		switch ( status ) {
			case 'loading':
				$button.addClass( 'loading' );
				$button.find( this.selectors.icon ).html( wcboost_wishlist_params.icon_loading );
				break;

			case 'added':
				$button.removeClass( 'loading' ).addClass( 'added' );

				switch ( wcboost_wishlist_params.exists_item_behavior ) {
					case 'view_wishlist':
						$button.attr( 'href', data.wishlist_url ? data.wishlist_url : wcboost_wishlist_params.wishlist_url );
						$button.find( this.selectors.text ).text( wcboost_wishlist_params.i18n_view_wishlist );
						$button.find( this.selectors.icon ).html( wcboost_wishlist_params.icon_filled );
						break;

					case 'remove':
						$button.attr( 'href', data.remove_url );
						$button.find( this.selectors.text ).text( wcboost_wishlist_params.i18n_remove_from_wishlist );
						$button.find( this.selectors.icon ).html( wcboost_wishlist_params.icon_filled );
						break;

					case 'hide':
						$button.hide();
						break;
				}

				break;

			case 'removed':
				$button.removeClass( 'added loading' );
				$button.find( this.selectors.text ).text( wcboost_wishlist_params.i18n_add_to_wishlist );
				$button.find( this.selectors.icon ).html( wcboost_wishlist_params.icon_normal );

				if ( data && data.add_url ) {
					$button.attr( 'href', data.add_url );
				}
				break;

			case 'update_id':
				$button.data( 'product_id', data.product_id ).attr( 'data-product_id', data.product_id );
				break;

			// case 'update_variations':
			// 	var variations = $button.data( 'variations' );

			// 	if ( variations && data.variation_id ) {
			// 		var index = variations.findIndex( function( variation ) { return variation.variation_id === data.variation_id; } );

			// 		variations[ index ] = $.extend( {}, variations[ index ], data );

			// 		$button.data( 'variations', variations );
			// 	}
			// 	break;
		}
	}

	/**
	 * WCBoostWishlist class
	 */
	var WCBoostWishlist = function( $form ) {
		var self = this;

		self.$form = $form;
		self.$wrapper = $form.closest( '.wcboost-wishlist' );

		self.updateForm = self.updateForm.bind( self );
		self.showNotices = self.showNotices.bind( self );

		// Initial states.
		self.$wrapper.off( '.wcboost-wishlist' );

		// Events.
		self.$wrapper.on( 'click.wcboost-wishlist', '.wcboost-wishlist-form .product-remove > a', { wishlist: self }, self.onRemoveItem );
		self.$wrapper.on( 'click.wcboost-wishlist', 'a.restore-item', { wishlist: self }, self.onRestoreItem );
		self.$wrapper.on( 'change.wcboost-wishlist input.wcboost-wishlist', ':input', { wishlist: self }, self.onInputChagne );
		self.$wrapper.on( 'submit.wcboost-wishlist', 'form',  { wishlist: self }, self.onSubmitForm );

		self.$form.find( 'button[name="update_wishlist"]' ).prop( 'disabled', true ).attr( 'aria-disabled', true );
	}

	WCBoostWishlist.prototype.onRemoveItem = function( event ) {
		event.preventDefault();

		var self = event.data.wishlist;

		$.ajax( {
			url:       event.currentTarget.href,
			type:     'GET',
			dataType: 'html',
			beforeSend: function() {
				block( self.$form );
			},
			success:  function( response ) {
				self.updateForm( response );
			},
			complete: function() {
				if ( self.$form ) {
					unblock( self.$form );
				}

				// Scroll to notices.
				var $notices = $( '[role="alert"]' );

				if ( $notices.length ) {
					$( 'html, body' ).animate( {
						scrollTop: ( $notices.offset().top - 100 )
					}, 1000 );
				}
			}
		} );
	}

	WCBoostWishlist.prototype.onRestoreItem = function( event ) {
		event.preventDefault();

		var self = event.data.wishlist,
			$blocking = self.$form ? self.$form : self.$wrapper;

		$.ajax( {
			url     : event.currentTarget.href,
			type    : 'GET',
			dataType: 'html',
			beforeSend: function() {
				block( $blocking );
			},
			success:  function( response ) {
				self.updateForm( response );
			},
			complete: function() {
				unblock( $blocking );
			}
		} );
	}

	WCBoostWishlist.prototype.onInputChagne = function( event ) {
		var self = event.data.wishlist;

		self.$form.find( 'button[name="update_wishlist"]' ).prop( 'disabled', false ).attr( 'aria-disabled', false );
	}

	WCBoostWishlist.prototype.onSubmitForm = function( event ) {
		event.preventDefault();

		var self = event.data.wishlist;

		// Provide the action input because the form-handler expectes it.
		$( '<input />' )
			.attr( 'type', 'hidden' )
			.attr( 'name', 'update_wishlist' )
			.attr( 'value', 'Update Wishlist' )
			.appendTo( self.$form );

		$.ajax( {
			type: self.$form.attr( 'method' ),
			url:  self.$form.attr( 'action' ),
			data: self.$form.serialize(),
			dataType: 'html',
			beforeSend: function() {
				block( self.$form );
			},
			success: function( response ) {
				self.updateForm( response );
			},
			complete: function() {
				if ( self.$form ) {
					unblock( self.$form );
				}
			}
		} );
	}

	WCBoostWishlist.prototype.updateForm = function( html ) {
		var self = this,
			$html = $.parseHTML( html ),
			$form = $( '.wcboost-wishlist-form', $html ),
			$notices = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info, .is-info, .is-success, .is-error', $html );

		// Remove current notices.
		$( '.woocommerce-error, .woocommerce-message, .woocommerce-info, .is-info, .is-success, .is-error' ).remove();

		if ( ! $form.length ) {

			var $emptyWishlist = $( '.wishlist-empty', $html ).closest( '.wcboost-wishlist' );

			// Notices are handled later.
			$emptyWishlist
				.find( '.woocommerce-error, .woocommerce-message, .woocommerce-info, .is-info, .is-success, .is-error' )
				.filter( function() {
					if ( $( this ).closest( '.wishlist-empty' ).length ) {
						return false;
					}

					return ! $( this ).hasClass( 'wishlist-empty' );
				} )
				.remove();

			self.$wrapper.html( $emptyWishlist.html() );
			self.$form = null;

			// Remove the template's notice from the list of notices.
			$notices = $notices.filter( function() {
				if ( $( this ).closest( '.wishlist-empty' ).length ) {
					return false;
				}

				return ! $( this ).hasClass( 'wishlist-empty' );
			} );

			// Notify plugins that the cart was emptied.
			$( document.body ).trigger( 'wishlist_emptied', [ self ] );
		} else {
			if ( self.$form ) {
				self.$form.replaceWith( $form );
				self.$form = $form;
			} else {
				var $wrapper = $form.closest( '.wcboost-wishlist' );

				self.$wrapper.html( $wrapper.html() );
				self.$form = self.$wrapper.find( '.wcboost-wishlist-form' );
			}

			self.$form.find( 'button[name="update_wishlist"]' ).prop( 'disabled', true ).attr( 'aria-disabled', true );
		}

		if ( $notices && $notices.length > 0 ) {
			self.showNotices( $notices );
		}

		$( document.body ).trigger( 'wishlist_updated', [ self ] );
	}

	WCBoostWishlist.prototype.showNotices = function( $notices, $target ) {
		if ( $target ) {
			$target.prepend( $notices );
		} else {
			this.$wrapper.prepend( $notices );
		}
	}

	/**
	 * WishlistShareHandler class
	 */
	var WishlistShareHandler = function() {
		var self = this;

		self.openSocialShareIframe = self.openSocialShareIframe.bind( self );
		self.showCopiedNotice = self.showCopiedNotice.bind( self );
		self.showWishlistURLNotice = self.showWishlistURLNotice.bind( self );

		// Events.
		$( document.body ).on( 'click.wcboost-wishlist', '.wcboost-wishlist-share-link', { wishlistShareHandler: self }, self.onClickShareLink );
	}

	WishlistShareHandler.prototype.onClickShareLink = function( event ) {
		var self = event.data.wishlistShareHandler,
			url = event.currentTarget.href,
			social = event.currentTarget.dataset.social;

		// Do nothing if sending emails.
		if ( 'email' === social ) {
			return;
		}

		// Copy wishlist page URL to clipboard.
		if ( 'link' === social ) {
			event.preventDefault();

			try {
				navigator.clipboard.writeText( url ).then( self.showCopiedNotice );
			} catch ( e ) {
				self.showWishlistURLNotice( url );
			}
		} else {
			var opened = self.openSocialShareIframe( url );

			if ( opened ) {
				event.preventDefault();
			}
		}
	}

	WishlistShareHandler.prototype.openSocialShareIframe = function( url ) {
		var width = 500,
			height = 450,
			top = (window.screen.height / 2) - ((height / 2) + 50),
			left = (window.screen.width / 2) - ((width / 2) + 10),
			features = "status=no,resizable=yes,width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";

		if ( window.screen.width <= width ) {
			return false;
		}

		var sharer = window.open( url, 'sharer', features );
		sharer.focus();

		return true;
	}

	WishlistShareHandler.prototype.showCopiedNotice = function() {
		if ( ! this.copiedNotice ) {
			this.copiedNotice = new WCBoostNotice(
				$( '<div class="wcboost-wishlist-link-copied-notice wcboost-wishlist-share-notice" />' ).text( wcboost_wishlist_params.i18n_link_copied_notice ),
				{
					type: 'message'
				}
			);
		}

		this.copiedNotice.show();
	}

	WishlistShareHandler.prototype.showWishlistURLNotice = function( url ) {
		if ( ! this.urlNotice ) {
			var $notice = $( '<div class="wcboost-wishlist-share-notice" />' );

			$notice.append( '<input type="text" value="" />' );
			$notice.append( '<span class="wcboost-wishlist-share-notice__close" role="button">' + wcboost_wishlist_params.i18n_close_button_text + '</span>' );

			var notice = new WCBoostNotice( $notice, {
				duration: -1,
				type: 'info'
			});

			$notice.on( 'click', '.wcboost-wishlist-share-notice__close', function() {
				notice.hide();
			} );

			this.urlNotice = notice;
		}

		this.urlNotice.show();
		this.urlNotice.$notice.find( 'input' ).val( url ).focus();
	}

	/**
	 * Wishlist widget class
	 */
	var WCBoostWishlistWidget = function() {
		var self = this;

		self.selectors = {
			widget: '.wcboost-wishlist-widget',
			content: '.wcboost-wishlist-widget-content',
		};

		self.checkWidgetVisibility = self.checkWidgetVisibility.bind( self );

		$( document.body )
			.on( 'click', self.selectors.content + ' a.remove', { wishlistWidget: self }, self.removeItem )
			.on( 'wishlist_fragments_loaded', { wishlistWidget: self }, self.checkWidgetVisibility );

		self.checkWidgetVisibility();
	}

	WCBoostWishlistWidget.prototype.checkWidgetVisibility = function() {
		var self = this;
		var $widgets = $( self.selectors.widget );

		if ( ! $widgets.length ) {
			return;
		}

		$widgets.each( function() {
			var $currentWidget = $( this );

			// Check if the option to hide if empty is selected.
			if ( ! $currentWidget.find( '.wcboost-wishlist-widget__hide-if-empty' ).length ) {
				return;
			}

			// Check if has products.
			if ( $currentWidget.find( '.wcboost-wishlist-widget__products' ).length ) {
				$currentWidget.show();
			} else {
				$currentWidget.hide();
			}
		} );
	}

	WCBoostWishlistWidget.prototype.removeItem = function( event ) {
		event.preventDefault();

		var self = event.data.wishlistWidget;
		var params = new URLSearchParams( event.currentTarget.search );
		var data = {
			item_key: params.get( 'remove-wishlist-item' ),
			_wpnonce: params.get( '_wpnonce' ),
		};

		if ( ! data.item_key ) {
			return;
		}

		var $widget = $( self.selectors.widget );

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_wishlist_item' ),
			data: data,
			dataType: 'json',
			beforeSend: function() {
				block( $widget );
			},
			success: function( response ) {
				if ( ! response.success ) {
					return;
				}

				var fragments = response.data.fragments;

				$( document.body ).trigger( 'removed_from_wishlist', [ null, fragments ] );
			},
			complete: function() {
				unblock( $widget );
			}
		} );
	}

	$( function() {
		new AddToWishlistHandler();
		new WCBoostWishlist( $( '.wcboost-wishlist-form' ) );
		new WishlistShareHandler();
		new WCBoostWishlistWidget();
	} );
} )( jQuery );
