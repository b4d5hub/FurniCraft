class SoberProductsCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				carousel: '.sober-product-carousel',
				products: 'ul.products'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$products: this.$element.find( selectors.products )
		};
	}

	getCarouselOptions() {
		const settings = this.getElementSettings();
		const breakpoints = elementorFrontend.config.breakpoints;
		const carouselOptions = {
			items: settings.columns || 4,
			autoplay: 'yes' === settings.autoplay,
			autoplayTimeout: settings.autoplay_speed,
			loop: 'yes' === settings.loop,
			pagination: true,
			navigation: false,
			slideSpeed: 300,
			paginationSpeed: 500,
			rtl: jQuery( document.body ).hasClass( 'rtl' ),
			responsive: {}
		};

		carouselOptions.responsive[breakpoints.xs] = { items: settings.columns_mobile };
		carouselOptions.responsive[breakpoints.md] = { items: settings.columns_tablet };
		carouselOptions.responsive[breakpoints.lg] = { items: settings.columns };

		return carouselOptions;
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$products.length ) {
			return;
		}

		this.elements.$products.addClass('owl-carousel').owlCarousel( this.getCarouselOptions() );
	}
}

class SoberCountDownHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				timer: '.timers',
				day: '.day',
				hour: '.hour',
				min: '.min',
				sec: '.secs',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$timer: this.$element.find( selectors.timer ),
			$day: this.$element.find( selectors.day ),
			$hour: this.$element.find( selectors.hour ),
			$min: this.$element.find( selectors.min ),
			$sec: this.$element.find( selectors.sec ),
		};
	}

	updateCounter( event ) {
		let output = '';

		const day = event.strftime( '%D' );
		for ( let i = 0; i < day.length; i++ ) {
			output += '<span>' + day[i] + '</span>';
		}
		this.elements.$day.html( output );

		output = '';
		const hour = event.strftime( '%H' );
		for ( let i = 0; i < hour.length; i++ ) {
			output += '<span>' + hour[i] + '</span>';
		}
		this.elements.$hour.html( output );

		output = '';
		const minu = event.strftime( '%M' );
		for ( let i = 0; i < minu.length; i++ ) {
			output += '<span>' + minu[i] + '</span>';
		}
		this.elements.$min.html( output );

		output = '';
		const secs = event.strftime( '%S' );
		for ( let i = 0; i < secs.length; i++ ) {
			output += '<span>' + secs[i] + '</span>';
		}
		this.elements.$sec.html( output );
	}

	onInit() {
		super.onInit();

		const endDate = this.elements.$timer.data( 'date' );

		this.elements.$timer.countdown( endDate, ( event ) => this.updateCounter( event ) );
	}
}

class SoberCircleChartWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				chart: '.sober-chart',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$chart: this.$element.find( selectors.chart ),
		};
	}

	getCircleProgressOptions() {
		const settings = this.getElementSettings();

		return {
			startAngle: -Math.PI / 2,
			value: settings.value.size/100,
			size: settings.size,
			emptyFill: settings.empty_color,
			fill: {color: settings.color},
			thickness: settings.thickness
		};
	}

	onInit() {
		super.onInit();

		elementorFrontend.waypoint( this.elements.$chart, () => {
			this.elements.$chart.circleProgress( this.getCircleProgressOptions() );
		} );
	}

	onElementChange( propertyName ) {
		if ( 'color' === propertyName || 'empty_color' === propertyName ) {
			this.elements.$chart.circleProgress(); // Redraw
		} else if ( 'size' === propertyName ) {
			this.elements.$chart.circleProgress( { size: this.getElementSettings( 'size' ) } );
		}
	}
}

class SoberImageSliderWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				carousel: '.sober-image-slider',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$carousel: this.$element.find( selectors.carousel )
		};
	}

	getCarouselOptions() {
		const settings = this.getElementSettings();
		const breakpoints = elementorFrontend.config.breakpoints;
		const carouselOptions = {
			items: 1,
			loop: 'yes' === settings.loop,
			autoplay: 'yes' === settings.autoplay,
			autoplayTimeout: settings.autoplay_speed,
			autoplaySpeed: 400,
			nav: false,
			dots: false,
			rtl: jQuery( document.body ).hasClass( 'rtl' ),
		};

		if ( settings.free_mode ) {
			carouselOptions.autoWidth = true;
			carouselOptions.center = true;
			carouselOptions.margin = 200;
			carouselOptions.nav = true;
			carouselOptions.navText = ['<svg viewBox="0 0 40 20"><use xlink:href="#right-arrow-wide"></use></svg>', '<svg viewBox="0 0 40 20"><use xlink:href="#right-arrow-wide"></use></svg>'];
			carouselOptions.responsive = {};
			carouselOptions.responsive[breakpoints.xs] = { margin: 0, autoWidth: false };
			carouselOptions.responsive[breakpoints.md] = { margin: 100, autoWidth: true };
			carouselOptions.responsive[breakpoints.lg] = { margin: 200, autoWidth: true };
		}

		return carouselOptions;
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$carousel.length ) {
			return;
		}

		this.elements.$carousel.addClass( 'owl-carousel' ).owlCarousel( this.getCarouselOptions() );
	}
}

let googleMapsScriptIsInjected = false;

class SoberGoogleMapWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				map: '.sober-google-map',
				markers: '.sober-google-map__markers'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$map: this.$element.find( selectors.map ),
			$markers: this.$element.find( selectors.markers )
		};
	}

	injectScript() {
		return new Promise( (resolve, reject) => {
			const script = document.createElement( 'script' );

			script.setAttribute( 'src', 'https://maps.googleapis.com/maps/api/js?key=' + this.getElementSettings( 'api_key' ) );
			script.setAttribute( 'async', '' );
			script.setAttribute( 'defer', '' );
			script.setAttribute( 'class', 'google-maps-api-script' );

			document.body.appendChild( script );

			script.addEventListener( 'load', () => {
				googleMapsScriptIsInjected = true;
				resolve( script );
			} );

			script.addEventListener( 'error', () => {
				reject( 'Google Map API failed to load' );
			} )
		} );
	}

	loadScript() {
		return new Promise( async (resolve, reject) => {
			if ( ! googleMapsScriptIsInjected ) {
				try {
					await this.injectScript();
					resolve( window.google );
				} catch ( error ) {
					reject( error );
				}
			} else {
				resolve( window.google );
			}
		} );
	}

	hasLocation( address ) {
		this.locations = this.locations || [];
		address = address.trim();

		let found = this.locations.filter( location => location.address === address );

		if ( ! found.length ) {
			return false;
		}

		return found[0].location;
	}

	getLocation( address ) {
		this.locations = this.locations || [];
		address = address.trim();

		let location = this.hasLocation( address );

		if ( location ) {
			return location.location;
		}

		return new Promise( (resolve, reject) => {
			const geocoder = new google.maps.Geocoder;

			geocoder.geocode( { address: address }, (results, status) => {
				if ( status === 'OK' ) {
					if ( results[0] ) {
						this.locations.push( {
							address: address,
							location: results[0].geometry.location
						} );

						resolve( results[0].geometry.location );
					} else {
						reject( 'No address found' );
					}
				} else {
					reject( status );
				}
			} )
		} );
	}

	getMapStyleOption() {
		let styles = [];

		switch ( this.getElementSettings( 'color' ) ) {
			case 'grey':
				styles = [{
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#e9e9e9"}, {"lightness": 17}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f5f5f5"}, {"lightness": 20}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f5f5f5"}, {"lightness": 21}]
				}, {
					"featureType": "poi.park",
					"elementType": "geometry",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 21}]
				}, {
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#333333"}, {"lightness": 40}]
				}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f2f2f2"}, {"lightness": 19}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
				}];
				break;

			case 'inverse':
				styles = [{
					"featureType": "all",
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#000000"}, {"lightness": 40}]
				}, {
					"featureType": "all",
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#000000"}, {"lightness": 16}]
				}, {
					"featureType": "all",
					"elementType": "labels.icon",
					"stylers"    : [{"visibility": "off"}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#000000"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}, {"weight": 1.2}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 20}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 21}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#000000"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 16}]
				}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 19}]
				}, {
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}]
				}];
				break;

			case 'vista-blue':
				styles = [{
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#a0d6d1"}, {"lightness": 17}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 20}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f1f1f1"}, {"lightness": 21}]
				}, {
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#333333"}, {"lightness": 40}]
				}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f2f2f2"}, {"lightness": 19}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
				}];
				break;
		}

		return styles;
	}

	async getMapOptions() {
		const settings = this.getElementSettings();
		const location = this.elements.$map.data( 'location' );
		const options = {
			scrollwheel      : false,
			navigationControl: true,
			mapTypeControl   : false,
			scaleControl     : false,
			streetViewControl: false,
			draggable        : true,
			mapTypeId        : google.maps.MapTypeId.ROADMAP,
			zoom             : settings.zoom.size
		};

		if ( location ) {
			options.center = location;
		} else {
			let latlng = settings.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				options.center = {
					lat: latlng[0],
					lng: latlng[1]
				};
			}
		}

		if ( ! options.center ) {
			options.center = await this.getLocation( settings.address );
		}

		let styles = this.getMapStyleOption( this.getElementSettings( 'color' ) );

		if ( styles ) {
			options.styles = styles;
		}

		return options;
	}

	async initMap() {
		if ( ! this.elements.$map.length ) {
			return;
		}

		if ( this.map ) {
			return;
		}

		this.map = new google.maps.Map( this.elements.$map.get( 0 ), await this.getMapOptions() );
	}

	async setMapLocation() {
		if ( ! this.isEdit ) {
			return;
		}

		if ( ! this.elements.$map.length ) {
			return;
		}

		if ( typeof this.map === 'undefined' ) {
			return;
		}

		const settings = this.getElementSettings();
		let location = {};
		let latlng = settings.latlng.split( ',' ).map( parseFloat );

		if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
			location = {
				lat: latlng[0],
				lng: latlng[1]
			};
		} else {
			location = await this.getLocation( settings.address );
		}

		if ( location ) {
			this.map.setCenter( location );
		}
	}

	clearMarkers() {
		if ( this.markers ) {
			for ( let i in this.markers ) {
				this.markers[i].setMap( null );
			}
		}

		this.markers = [];
	}

	async updateLocationList() {
		if ( ! this.elements.$markers.length ) {
			return;
		}

		const addresses = {
			name: [],
			latlng: []
		};

		this.elements.$markers.children().each( ( index, marker ) => {
			let data = JSON.parse( marker.dataset.marker );
			let address = data.address;

			if ( ! address ) {
				return;
			}

			if ( this.hasLocation( address ) ) {
				return;
			}

			let latlng = data.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				return;
			}

			addresses.name.push( address );
			addresses.latlng.push( this.getLocation( address ) );
		} );

		await Promise.all( addresses.latlng ).then( coordinates => {
			for ( let i in coordinates ) {
				if ( ! this.hasLocation( addresses.name[i] ) ) {
					this.locations.push( {
						address: addresses.name[i],
						location: coordinates[i]
					} );
				}
			}
		} ).catch( error => {
			console.warn( error );
		} );
	}

	async updateMarkers() {
		if ( typeof this.map === 'undefined' ) {
			return;
		}

		if ( ! this.elements.$markers.length ) {
			return;
		}

		// Reset all markers.
		this.clearMarkers();

		// Update locations.
		await this.updateLocationList();

		this.elements.$markers.children().each( ( index, marker ) => {
			let data = JSON.parse( marker.dataset.marker );
			let markerOptions = {
				map: this.map,
				animation: google.maps.Animation.DROP
			}

			if ( data.icon.url ) {
				markerOptions.icon = data.icon.url;
			}

			let latlng = data.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				markerOptions.position = {
					lat: latlng[0],
					lng: latlng[1]
				};
			} else {
				markerOptions.position = this.hasLocation( data.address )
			}

			let mapMarker = new google.maps.Marker( markerOptions );

			if ( marker.innerHTML ) {
				let infoWindow = new google.maps.InfoWindow( {
					content: '<div class="sober-google-map__info info_content">' + marker.innerHTML + '</div>'
				} );

				mapMarker.addListener( 'click', () => {
					infoWindow.open( this.map, mapMarker );
				} );
			}

			this.markers.push( mapMarker );
		} );
	}

	async onInit() {
		super.onInit();

		if ( ! this.isEdit ) {
			googleMapsScriptIsInjected = true;
		}

		try {
			await this.loadScript();
			await this.initMap();
			await this.setMapLocation();
			this.updateMarkers();
		} catch ( error ) {
			console.warn( error );
		}
	}

	async onElementChange( propertyName ) {
		if ( 'api_key' === propertyName ) {
			googleMapsScriptIsInjected = false;
			document.getElementById( 'google-maps-api-script' ).remove();

			await this.loadScript();
			google.maps.event.trigger( this.map, 'resize' );
		}

		if ( 'address' === propertyName || 'latlng' === propertyName ) {
			clearTimeout( this.timerAddressChange );
			this.timerAddressChange = setTimeout( () => {
				this.setMapLocation();
			}, 1000 );
		}

		if ( 'zoom' === propertyName ) {
			let zoom = this.getElementSettings( 'zoom' );

			this.map.setZoom( zoom.size );
		}

		if ( 'color' === propertyName ) {
			this.map.setOptions( {
				styles: this.getMapStyleOption()
			} );
		}
	}
}

class SoberIconBoxCarouselHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				carousel: '.sober-icon-box-carousel'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$carousel: this.$element.find( selectors.carousel )
		};
	}

	getCarouselOptions() {
		const settings = this.getElementSettings();
		const breakpoints = elementorFrontend.config.breakpoints;
		const carouselOptions = {
			items: settings.items_to_show || 3,
			autoplay: 'yes' === settings.autoplay,
			autoplayTimeout: settings.autoplay_speed,
			loop: 'yes' === settings.loop,
			pagination: true,
			navigation: false,
			slideSpeed: 300,
			paginationSpeed: 500,
			rtl: jQuery( document.body ).hasClass( 'rtl' ),
			responsive: {}
		};

		carouselOptions.responsive[breakpoints.xs] = { items: settings.items_to_show_mobile };
		carouselOptions.responsive[breakpoints.md] = { items: settings.items_to_show_tablet };
		carouselOptions.responsive[breakpoints.lg] = { items: settings.items_to_show };

		return carouselOptions;
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$carousel.length ) {
			return;
		}

		this.elements.$carousel.addClass('owl-carousel').owlCarousel( this.getCarouselOptions() );
	}
}

class SoberTabsHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				tab: '.sober-tab__title',
				panel: '.sober-tab__content'
			},
			classes: {
				active: 'sober-tab--active',
			},
			showFn: 'show',
			hideFn: 'hide',
			toggleSelf: false,
			autoExpand: true,
			hidePrevious: true
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$tabs: this.findElement( selectors.tab ),
			$panels: this.findElement( selectors.panel )
		};
	}

	activateDefaultTab() {
		const settings = this.getSettings();

		if ( ! settings.autoExpand || 'editor' === settings.autoExpand && ! this.isEdit ) {
			return;
		}

		const defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1,
			originalToggleMethods = {
				showFn: settings.showFn,
				hideFn: settings.hideFn
			};

		this.setSettings( {
			showFn: 'show',
			hideFn: 'hide'
		} );

		this.changeActiveTab( defaultActiveTab );

		this.setSettings( originalToggleMethods );
	}

	changeActiveTab( tabIndex ) {
		const settings = this.getSettings(),
			$tab = this.elements.$tabs.filter( '[data-tab="' + tabIndex + '"]' ),
			$panel = this.elements.$panels.filter( '[data-tab="' + tabIndex + '"]' ),
			isActive = $tab.hasClass( settings.classes.active );

		if ( ! settings.toggleSelf && isActive ) {
			return;
		}

		if ( ( settings.toggleSelf || ! isActive ) && settings.hidePrevious ) {
			this.elements.$tabs.removeClass( settings.classes.active );
			this.elements.$panels.removeClass( settings.classes.active )[settings.hideFn]();
		}

		if ( ! settings.hidePrevious && isActive ) {
			$tab.removeClass( settings.classes.active );
			$panel.removeClass( settings.classes.active )[settings.hideFn]();
		}

		if ( ! isActive ) {
			$tab.addClass( settings.classes.active );
			$panel.addClass( settings.classes.active )[settings.showFn]();
		}
	}

	bindEvents() {
		this.elements.$tabs.on( {
			keydown: ( event ) => {
				if ( 'Enter' !== event.key ) {
					return;
				}

				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
			},
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
			}
		} );
	}

	onInit() {
		super.onInit();

		this.activateDefaultTab();
	}
}

class SoberMotionParallaxHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		const options = {
			addBackgroundLayerTo: '',
			classes: {
				element: 'elementor-motion-parallax',
				container: 'elementor-motion-effects-container',
				layer: 'elementor-motion-effects-layer',
			}
		};

		return options;
	}

	addBackgroundLayer() {
		const settings = this.getSettings();

		this.elements.$motionParallaxContainer = jQuery( '<div>', { class: settings.classes.container } );
		this.elements.$motionParallaxLayer = jQuery( '<div>', { class: settings.classes.layer } );
		this.elements.$motionParallaxContainer.prepend( this.elements.$motionParallaxLayer );

		const $addBackgroundLayerTo = settings.addBackgroundLayerTo ? this.$element.find( settings.addBackgroundLayerTo ) : this.$element;
		$addBackgroundLayerTo.prepend( this.elements.$motionParallaxContainer );
	}

	removeBackgroundLayer() {
		if ( this.elements.$motionParallaxContainer ) {
			this.elements.$motionParallaxContainer.remove();
		}
	}

	activate() {
		this.addBackgroundLayer();
		this.$element.addClass( this.getSettings( 'classes.element' ) );

		this.rellax = new Rellax( this.elements.$motionParallaxLayer.get(0), {
			speed: 5,
			center: true
		} );
	}

	deactivate() {
		this.$element.removeClass( this.getSettings( 'classes.element' ) );

		if ( this.rellax ) {
			this.rellax.destroy();
		}

		this.removeBackgroundLayer();
	}

	toggle() {
		if ( this.getElementSettings( 'background_motion_fx_motion_fx_scrolling' ) ) {
			this.activate();
		} else {
			this.deactivate();
		}
	}

	onInit() {
		super.onInit();

		this.toggle();
	}

	onElementChange( propertyName ) {
		if ( 'background_motion_fx_motion_fx_scrolling' === propertyName ) {
			this.toggle();
		}
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-products-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberProductsCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-countdown.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberCountDownHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-circle-chart.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberCircleChartWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-image-slider.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberImageSliderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-google-map.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberGoogleMapWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-icon-box-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberIconBoxCarouselHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberTabsHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/sober-accordion.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( SoberTabsHandler, {
			$element: $element,
			showFn: 'slideDown',
			hideFn: 'slideUp',
			autoExpand: false,
			toggleSelf: true,
			selectors: {
				tab: '.sober-accordion__title',
				panel: '.sober-accordion__content'
			}
		} );
	} );

	if ( typeof ElementorProFrontendConfig === 'undefined' ) {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/section', ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( SoberMotionParallaxHandler, { $element: $element } );
		});

		elementorFrontend.hooks.addAction( 'frontend/element_ready/container', ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( SoberMotionParallaxHandler, { $element: $element } );
		});

		elementorFrontend.hooks.addAction( 'frontend/element_ready/column', ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( SoberMotionParallaxHandler, { $element: $element, addBackgroundLayerTo: ' > .elementor-element-populated' } );
		});
	}
} );
