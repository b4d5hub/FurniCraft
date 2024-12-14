;( function( $, document ) {
	'use strict';

	var VariationSwatches = function( $form ) {
		var self = this;

		self.$form            = $form;
		self.$attributeFields = $form.find( '.variations select' );
		self.variationData    = $form.data( 'product_variations' );

		// Initial states.
		$form.off( '.wcboost-variation-swatches' );

		// Add a new CSS to the form.
		if ( $form.find( '.wcboost-variation-swatches__wrapper' ).length ) {
			$form.addClass( 'swatches-support' );
		}

		// Methods.
		self.getChosenAttributes = self.getChosenAttributes.bind( self );

		// Events.
		$form.on( 'click.wcboost-variation-swatches', '.wcboost-variation-swatches__item', { variationSwatches: self }, self.onSelect );
		$form.on( 'change.wcboost-variation-swatches', '.variations select', { variationSwatches: self }, self.onValueChange );
		$form.on( 'keydown.wcboost-variation-swatches', '.wcboost-variation-swatches__item', { variationSwatches: self }, self.onKeyPress );
		$form.on( 'woocommerce_update_variation_values.wcboost-variation-swatches', { variationSwatches: self }, self.onUpdateAttributes );

		if ( wcboost_variation_swatches_params.show_selected_label ) {
			$form.on( 'change.wcboost-variation-swatches', '.value select', { variationSwatches: self }, self.updateLabel );
			$form.on( 'wc_variation_form.wcboost-variation-swatches', { variationSwatches: self }, self.updateAllLabels );
		}

		$( document.body ).trigger( 'wcboost_variation_swatches', self );
	};

	/**
	 * Update the selected value on selection change
	 */
	VariationSwatches.prototype.onSelect = function( event ) {
		event.preventDefault();

		var $el = $( this );

		if ( $el.hasClass( 'disabled' ) || $el.data( 'disabled' ) ) {
			return;
		}

		var $select = $el.closest( '.wcboost-variation-swatches' ).find( 'select' ),
			value = ! $el.hasClass( 'selected' ) ? $el.data( 'value' ) : '';


		$select.trigger( 'focusin' ); // Compatible with old version of WooCommerce.
		$select.val( value );
		$select.trigger( 'change' );
	};

	/**
	 * Toggle the "selected" class when the selection changes.
	 */
	VariationSwatches.prototype.onValueChange = function () {
		var $select = $( this ),
			$swatches = $select.closest( '.wcboost-variation-swatches' ),
			value = $select.val();

		if ( ! $swatches.length ) {
			return;
		}

		$swatches.find( '.wcboost-variation-swatches__item.selected' )
			.removeClass( 'selected' )
			.attr( 'aria-pressed', false );

		if ( value ) {
			$swatches
				.find( '.wcboost-variation-swatches__item' )
				.filter( function() { return this.dataset.value === value; } )
				.addClass( 'selected' )
				.attr( 'aria-pressed', true );
		}
	}

	/**
	 * Keyboard toggle select value
	 */
	VariationSwatches.prototype.onKeyPress = function( event ) {
		if ( event.keyCode && 32 === event.keyCode || event.key && ' ' === event.key || event.keyCode && 13 === event.keyCode || event.key && 'enter' === event.key.toLowerCase() ) {
			event.preventDefault();

			$( this ).trigger( 'click.wcboost-variation-swatches' );
		}
	}

	/**
	 * Update state of swatches on attribute change
	 */
	VariationSwatches.prototype.onUpdateAttributes = function( event ) {
		var self              = event.data.variationSwatches ,
		    attributes        = self.getChosenAttributes() ,
		    currentAttributes = attributes.data;

		// Reset if no attributes chosen.
		if ( ! attributes.chosenCount ) {
			self.$form.find( '.wcboost-variation-swatches__item' ).removeClass( 'selected disabled' ).data( 'disabled', false ).attr( 'tabindex', 0 );

			// return;
		}

		setTimeout( function() {
			// Disable invalid swatches.
			self.$attributeFields.each( function() {
				var $select = $( this ),
					$items = $select.siblings( '.wcboost-variation-swatches__wrapper' ).find( '.wcboost-variation-swatches__item' );

				$items.each( function() {
					var $item = $( this ),
						$option = $select.find( 'option[value="' + $item.data( 'value' ) + '"]' );

					if ( ! $option.length || $option.prop( 'disabled' ) ) {
						$item.addClass( 'disabled' ).removeClass( 'selected' ).data( 'disabled', true ).attr( 'tabindex', -1 );
					} else {
						$item.removeClass( 'disabled' ).data( 'disabled', false ).attr( 'tabindex', 0 );
					}
				} );
			} );
		}, 10 );
	};

	/**
	 * Update the label of selected attribute
	 */
	VariationSwatches.prototype.updateLabel = function( event ) {
		event.data.variationSwatches.appendSelectedLabel( this );
	}

	/**
	 * Update labels of all attributes on form init
	 */
	VariationSwatches.prototype.updateAllLabels = function( event ) {
		var self = event.data.variationSwatches;

		self.$form.find( '.value select' ).each( function() {
			self.appendSelectedLabel( this );
		} );
	}

	/**
	 * Append the selected attribute's label to the variation's label
	 *
	 * @param {object} select The select tag
	 */
	VariationSwatches.prototype.appendSelectedLabel = function( select ) {
		var $label = $( select ).closest( '.value' ).siblings( '.label' ).find( 'label' ),
			$holder = $label.find( '.wcboost-variation-swatches__selected-label' );

		if ( ! $holder.length ) {
			$holder = $( '<span class="wcboost-variation-swatches__selected-label" />' );

			$label.append( $holder )
		}

		if ( select.value ) {
			$holder.text( select.options[ select.selectedIndex ].text ).show();
		} else {
			$holder.text( '' ).hide();
		}
	}

	/**
	 * Get chosen attributes from form.
	 * @see woocommerce/assets/js/frontend/add-to-cart-variation.js
	 * @return array
	 */
	VariationSwatches.prototype.getChosenAttributes = function() {
		var data   = {};
		var count  = 0;
		var chosen = 0;

		this.$attributeFields.each( function() {
			var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			var value          = $( this ).val() || '';

			if ( value.length > 0 ) {
				chosen ++;
			}

			count ++;
			data[ attribute_name ] = value;
		});

		return {
			'count'      : count,
			'chosenCount': chosen,
			'data'       : data
		};
	};

	/**
	 * Function to call wcboost_variation_swatches on jquery selector.
	 */
	$.fn.wcboost_variation_swatches = function() {
		new VariationSwatches( this );
		return this;
	};

	/**
	 * Function to init variation swatches on all variation forms
	 */
	function init_variation_swatches() {
		$( '.variations_form:not(.swatches-support)' ).each( function() {
			$( this ).wcboost_variation_swatches();
		} );
	}

	/**
	 * Support other plugins/themes.
	 */
	function init_compatibility() {
		if ( $( '.bundle_form .bundle_data' ).length > 0 ) {
			$( '.bundle_form .bundle_data' ).on( 'woocommerce-product-bundle-initialized.wcboost-variation-swatches', init_variation_swatches );
		}
	}

	$( function() {
		init_variation_swatches();
		init_compatibility();

		// Support third-party plugins.
		$( document.body ).on( 'init_variation_swatches.wcboost-variation-swatches, wc_variation_form.wcboost-variation-swatches', init_variation_swatches );

		$( document.body ).trigger( 'wcboost_variation_swatches_initialized' );
	} );
} )( jQuery, document )
