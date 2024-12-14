/* global wcboost_products_compare_params, woocommerce_params */
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
	 * Trigger a custom event
	 *
	 * @param {Node} el
	 * @param {string} name
	 * @param {Object} data
	 */
	var triggerEvent = function( el, name, data ) {
		var e;

		try {
			e = new CustomEvent( name, {
				bubbles: true,
				cancelable: true,
				detail: data || null
			} );
		} catch( ex ) {
			e = document.createEvent( name );
			e.initCustomEvent( name, true, true, data || null );
		}

		el.dispatchEvent( e );
	}

	/**
	 * Add to compare button handler
	 */
	var AddToCompareHandler = function() {
		this.selectors = {
			text: '.wcboost-products-compare-button__text',
			icon: '.wcboost-products-compare-button__icon',
		};

		this.addToCompare      = this.addToCompare.bind( this );
		this.removeFromCompare = this.removeFromCompare.bind( this );

		$( document.body ).on( 'click', '.wcboost-products-compare-button--ajax', { compareButtonHandler: this }, this.onButtonClick );
	}

	AddToCompareHandler.prototype.onButtonClick = function( event ) {
		var self = event.data.compareButtonHandler;
		var $button = $( event.currentTarget );

		if ( $button.hasClass( 'loading' ) ) {
			event.preventDefault();
			return;
		}

		if ( ! $button.hasClass( 'added' ) ) {
			event.preventDefault();
			self.addToCompare( $button );
		} else if ( 'remove' === wcboost_products_compare_params.exists_item_behavior ) {
			event.preventDefault();
			self.removeFromCompare( $button );
		}
	}

	AddToCompareHandler.prototype.addToCompare = function( $button ) {
		var self = this;
		var data = { product_id: $button.data( 'product_id' ) };

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_compare' ),
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$button.removeClass( 'added' ).addClass( 'loading' );
				$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_loading );
			},
			success: function( response ) {
				if ( ! response.success ) {
					return;
				}

				var fragments = response.data.fragments;

				$button.addClass( 'added' );

				switch ( wcboost_products_compare_params.exists_item_behavior ) {
					case 'view':
						$button.attr( 'href', wcboost_products_compare_params.page_url );
						$button.find( self.selectors.text ).text( wcboost_products_compare_params.i18n_button_view );
						$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_checked );
						break;

					case 'remove':
						$button.attr( 'href', response.data.remove_url );
						$button.find( self.selectors.text ).text( wcboost_products_compare_params.i18n_button_remove );
						$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_checked );
						break;

					case 'popup':
						$button.attr( 'href', wcboost_products_compare_params.page_url );
						$button.find( self.selectors.text ).text( wcboost_products_compare_params.i18n_button_view );
						$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_checked );
						$button.addClass( 'wcboost-products-compare-button--popup' );
						break;

					case 'hide':
						$button.hide();
						break;
				}

				$( document.body )
					.trigger( 'wcboost_compare_item_added', [ response.data ] )
					.trigger( 'added_to_compare', [ $button, fragments, response.data.count ] );

				if ( 'redirect' === wcboost_products_compare_params.added_behavior && wcboost_products_compare_params.page_url ) {
					window.location = wcboost_products_compare_params.page_url;
				}
			},
			complete: function() {
				$button.removeClass( 'loading' );
			}
		} );
	}

	AddToCompareHandler.prototype.removeFromCompare = function( $button ) {
		var self   = this;
		var params = new URLSearchParams( $button[0].search );
		var data   = {
			item_key: params.get( 'remove_compare_item' ),
			_wpnonce: params.get( '_wpnonce' ),
		};

		if ( ! data.item_key ) {
			return;
		}

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_compare_item' ),
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$button.removeClass( 'added' ).addClass( 'loading' );
				$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_loading );
			},
			success: function( response ) {
				if ( ! response.success ) {
					return;
				}

				var fragments = response.data.fragments;

				$button.attr( 'href', response.data.add_url );
				$button.find( self.selectors.text ).text( wcboost_products_compare_params.i18n_button_add );
				$button.find( self.selectors.icon ).html( wcboost_products_compare_params.icon_normal );

				$( document.body )
					.trigger( 'wcboost_compare_item_removed', [ response.data ] )
					.trigger( 'removed_from_compare', [ $button, fragments ] );
			},
			complete: function() {
				$button.removeClass( 'loading' );
			}
		} );
	}


	/**
	 * Compare page/popup
	 */
	var WCBoostProductsCompare = function( node ) {
		var self = this;

		self.$container = $( node );

		// Initial state.
		self.$container.off( '.wcboost-products-compare' );

		// Methods.
		self.sendAjaxUpdateList = self.sendAjaxUpdateList.bind( self );
		self.updateList = self.updateList.bind( self );

		// Events.
		self.$container.on( 'click.wcboost-products-compare', 'a.wcboost-products-compare-remove', { productsCompare: self }, self.onRemoveItem );
		self.$container.on( 'click.wcboost-products-compare', '.wcboost-products-compare-clear', { productsCompare: self }, self.onClearList );
	}

	WCBoostProductsCompare.prototype.onRemoveItem = function( event ) {
		event.preventDefault();

		event.data.productsCompare.sendAjaxUpdateList( event.currentTarget.href );
	}

	WCBoostProductsCompare.prototype.onClearList = function( event ) {
		event.preventDefault();

		event.data.productsCompare.sendAjaxUpdateList( event.currentTarget.href );
	}

	WCBoostProductsCompare.prototype.sendAjaxUpdateList = function( url ) {
		var self = this;

		$.ajax( {
			url: url,
			type: 'POST',
			data: {
				'_wp_http_referer': wcboost_products_compare_params.page_url,
				time: new Date().getTime()
			},
			dataType: 'html',
			beforeSend: function() {
				block( self.$container );
			},
			success: function( response ) {
				self.updateList( response );

				// Scroll to notices.
				var $notices = $( '[role="alert"]' );

				if ( $notices.length && $notices.is( ':visible' ) ) {
					$( 'html, body' ).animate( {
						scrollTop: ( $notices.offset().top - 100 )
					}, 1000 );
				}
			},
			complete: function() {
				if ( self.$container ) {
					unblock( self.$container );
				}
			}
		} );
	}

	WCBoostProductsCompare.prototype.updateList = function( html ) {
		var self = this,
			$html = $.parseHTML( html ),
			$content = $( '.wcboost-products-compare', $html ),
			isEmpty = $( '.wcboost-products-compare--empty', $content ).length ? true : false,
			$notices = {};

		// Remove current notices.
		$( '.woocommerce-error, .woocommerce-message, .woocommerce-info, .woocommerce-info, .is-success, .is-error, .is-info' ).remove();

		// Update content.
		self.$container.html( $content.html() );

		// Some themes like Storefront displaying notices outside the list.
		// Needed to handle this situation manually.
		$content.remove();
		$notices = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info, .is-success, .is-error, .is-info', $html );

		if ( isEmpty ) {
			// Notify plugins that the cart was emptied.
			triggerEvent( document.body, 'products_compare_list_emptied' );
		}

		// Add notices.
		if ( $notices.length > 0 ) {
			self.$container.prepend( $notices );
		}

		triggerEvent( document.body, 'products_compare_list_updated' );
	}

	/**
	 * Compare popup
	 */
	var WCBoostComparePopup = function() {
		var self = this;

		self.opened = false;
		self.xhr = null;
		self.$popup = $( '#wcboost-products-compare-popup' );
		self.$content = $( '.wcboost-products-compare-popup__content', self.$popup );

		self.togglePopup = self.togglePopup.bind( self );

		$( document.body )
			.on( 'click', '.wcboost-products-compare-popup-trigger', { comparePopup: self }, self.triggerOpenPopup )
			.on( 'click', '.wcboost-products-compare-button--popup.added', { comparePopup: self }, self.triggerOpenPopup )
			.on( 'products_compare_popup_open', { comparePopup: self }, self.openPopup )
			.on( 'products_compare_popup_loaded', { comparePopup: self }, self.handleCompareActions )
			.on( 'products_compare_list_updated', { comparePopup: self }, self.refreshFragments )
			.on( 'products_compare_list_emptied', { comparePopup: self }, self.emptyPopup );

		if ( 'popup' === wcboost_products_compare_params.added_behavior ) {
			$( document.body ).on( 'added_to_compare', { comparePopup: self }, self.triggerOpenPopupOnAdded );
		}

		self.$popup.on( 'click', '.wcboost-products-compare-popup__backdrop, .wcboost-products-compare-popup__close', { comparePopup: self }, self.closePoup );
	}

	WCBoostComparePopup.prototype.triggerOpenPopup = function( event ) {
		event.preventDefault();

		triggerEvent( document.body, 'products_compare_popup_open' );
	}

	WCBoostComparePopup.prototype.triggerOpenPopupOnAdded = function( event, $button, fragments, count ) {
		if ( count > 1 ) {
			triggerEvent( document.body, 'products_compare_popup_open' );
		}
	}

	WCBoostComparePopup.prototype.openPopup = function( event ) {
		var self = event.data.comparePopup;

		if ( ! wcboost_products_compare_params.page_url ) {
			return;
		}

		if ( self.xhr ) {
			self.xhr.abort();
		}

		self.xhr = $.ajax( {
			url: wcboost_products_compare_params.page_url,
			data: { popup: 1 },
			type: 'GET',
			dataType: 'html',
			beforeSend: function() {
				block( self.$content );
				self.$popup.addClass( 'wcboost-products-compare-popup--loading' );
				self.togglePopup( true );

				triggerEvent( document.body, 'products_compare_popup_loading' );
			},
			success: function( response ) {
				var $newContent = $( '.wcboost-products-compare.woocommerce', response );

				self.$content.html( $newContent );

				triggerEvent( document.body, 'products_compare_popup_loaded' );
			},
			complete: function() {
				unblock( self.$content );
				self.$popup.removeClass( 'wcboost-products-compare-popup--loading' );
			}
		} );
	}

	WCBoostComparePopup.prototype.handleCompareActions = function( event ) {
		var self = event.data.comparePopup,
			$list = self.$content.find( '.wcboost-products-compare' );

		if ( $list.length ) {
			new WCBoostProductsCompare( $list );
		}
	}

	WCBoostComparePopup.prototype.closePoup = function( event ) {
		event.preventDefault();

		var self = event.data.comparePopup;

		self.togglePopup( false );
	}

	WCBoostComparePopup.prototype.refreshFragments = function( event ) {
		var self = event.data.comparePopup;

		// Just update buttons if the list is emptied from the popup.
		if ( ! self.opened ) {
			return;
		}

		// Trigger refresh fragments, include buttons.
		$( document.body ).trigger( 'products_compare_fragments_refresh', [ true ] );
	}

	WCBoostComparePopup.prototype.emptyPopup = function( event ) {
		var self = event.data.comparePopup;

		self.$content.html( '' );
		self.togglePopup( false );
	}

	WCBoostComparePopup.prototype.togglePopup = function( open ) {
		var self = this;

		if ( open ) {
			self.opened = true;
			self.$popup.stop( true, true ).fadeIn( 150, function() {
				self.$popup.addClass( 'wcboost-products-compare-popup--open' );
			} );

			triggerEvent( document.body, 'products_compare_popup_opened' );
		} else {
			self.$popup.stop( true, true ).fadeOut( 150, function() {
				self.$popup.removeClass( 'wcboost-products-compare-popup--open' );
				self.opened = false;
			} );

			triggerEvent( document.body, 'products_compare_popup_closed' );
		}
	}

	/**
	 * Widget handler
	 */
	var WCBoostCompareWidget = function() {
		var self = this;

		self.widgetClass = '.wcboost-products-compare-widget-content';

		self.checkEmptyWidgetVisibility = self.checkEmptyWidgetVisibility.bind( self );
		self.hideWidgets = self.hideWidgets.bind( self );
		self.showWidgets = self.showWidgets.bind( self );

		$( document.body )
			.on( 'click', self.widgetClass + ' a.remove', { compareWidget: self }, self.removeItem )
			.on( 'click', self.widgetClass + ' .wcboost-products-compare-clear', { compareWidget: self }, self.clearList )
			.on( 'click', self.widgetClass + ' .wcboost-products-compare-open', { compareWidget: self }, self.openCompare );

		self.checkEmptyWidgetVisibility();
	}

	WCBoostCompareWidget.prototype.checkEmptyWidgetVisibility = function() {
		var self = this;
		var $widgets = $( self.widgetClass );

		if ( ! $widgets.length ) {
			return;
		}

		$widgets.each( function() {
			var $currentWidget = $( this );

			// Check if the option to hide if empty is selected.
			if ( ! $currentWidget.closest( '.wcboost-products-compare-widget__hidden-content' ).length ) {
				return;
			}

			// Check if has products.
			if ( $currentWidget.find( '.wcboost-products-compare-widget__products' ).length ) {
				return;
			}

			$currentWidget.closest( '.wcboost-products-compare-widget' ).hide();
		} );

		// Add event listeners.
		$( document.body )
			.on( 'products_compare_list_emptied', { compareWidget: self, compareListEmpty: true }, self.toggleVisibility )
			.on( 'added_to_compare', { compareWidget: self, compareListEmpty: false }, self.toggleVisibility );
	}

	WCBoostCompareWidget.prototype.toggleVisibility = function( event ) {
		var self = event.data.compareWidget;
		var isEmpty = event.data.compareListEmpty;

		if ( isEmpty ) {
			self.hideWidgets();
		} else {
			self.showWidgets();
		}
	}

	WCBoostCompareWidget.prototype.hideWidgets = function() {
		var self = this;
		var $widgets = $( self.widgetClass );

		$widgets.each( function() {
			var $currentWidget = $( this );

			// Check if the option to hide if empty is selected.
			if ( ! $currentWidget.closest( '.wcboost-products-compare-widget__hidden-content' ).length ) {
				return;
			}

			$currentWidget.closest( '.wcboost-products-compare-widget' ).hide();
		} );
	}

	WCBoostCompareWidget.prototype.showWidgets = function() {
		var self = this;
		var $widgets = $( self.widgetClass );

		$widgets.each( function() {
			var $currentWidget = $( this );

			// Check if the option to hide if empty is selected.
			if ( ! $currentWidget.closest( '.wcboost-products-compare-widget__hidden-content' ).length ) {
				return;
			}

			$currentWidget.closest( '.wcboost-products-compare-widget' ).show();
		} );
	}

	WCBoostCompareWidget.prototype.removeItem = function( event ) {
		var self = event.data.compareWidget;
		var params = new URLSearchParams( event.currentTarget.search );
		var data   = {
			item_key: params.get( 'remove_compare_item' ),
			_wpnonce: params.get( '_wpnonce' ),
		};

		if ( ! data.item_key ) {
			return;
		}

		var $widget = $( self.widgetClass );

		event.preventDefault();

		$.post( {
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_compare_item' ),
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

				$( document.body ).trigger( 'removed_from_compare', [ null, fragments ] );

				// Trigger refresh fragments, include buttons.
				$( document.body ).trigger( 'products_compare_fragments_refresh', [ true ] );
			},
			complete: function() {
				unblock( $widget );
			}
		} );
	}

	WCBoostCompareWidget.prototype.clearList = function( event ) {
		event.preventDefault();

		var self = event.data.compareWidget;
		var $widget = $( self.widgetClass );

		$.ajax( {
			url: event.currentTarget.href,
			type: 'GET',
			dataType: 'html',
			beforeSend: function() {
				block( $widget );
			},
			success: function() {
				triggerEvent( document.body, 'products_compare_list_emptied' );
				$( document.body ).trigger( 'products_compare_fragments_refresh', [true] );
			},
			complete: function() {
				unblock( $widget );
			}
		} );
	}

	WCBoostCompareWidget.prototype.openCompare = function( event ) {
		var $widget = $( event.currentTarget ).closest( '[data-compare]' );

		if ( $widget.length && 'popup' === $widget.data( 'compare' ) ) {
			event.preventDefault();

			triggerEvent( document.body, 'products_compare_popup_open' );
		}
	}


	/**
	 * Compare bar
	 */
	var WCBoostCompareBar = function( node ) {
		var self = this;

		self.$bar = $( node );

		self.$bar.on( 'click', '.wcboost-products-compare-bar__toggle-button', { compareBar: self }, self.toggleCompareBar );
	}

	WCBoostCompareBar.prototype.toggleCompareBar = function( event ) {
		event.preventDefault();

		var self = event.data.compareBar;

		self.$bar.toggleClass( 'wcboost-products-compare-bar--open' );
	}

	// Document ready.
	$( function() {
		new AddToCompareHandler();
		new WCBoostComparePopup();
		new WCBoostCompareWidget();
		new WCBoostCompareBar( '#wcboost-products-compare-bar' );

		$( '.wcboost-products-compare' ).each( function() {
			new WCBoostProductsCompare( this );
		} );
	} );

} )( jQuery );
