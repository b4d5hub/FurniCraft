jQuery( document ).ready( function( $ ) {
	"use strict";

	var soodi = {
		init: function() {
			this.$progress = $( '#soo-demo-import-progress' );
			this.$log = $( '#soo-demo-import-log' );
			this.$importer = $( '#soo-demo-importer' );

			// Events.
			$( document.body )
				.on( 'click', '.soo-tab-nav-wrapper > .nav-tab', soodi.switchTabs )
				.on( 'click', '.toggle-options', soodi.toggleOptions );


			// Start importing.
			this.startImporting();
		},

		switchTabs: function( event ) {
			event.preventDefault();
			var $tab = $( event.target );

			if ( $tab.hasClass( 'nav-tab-active' ) ) {
				return;
			}

			$tab.addClass( 'nav-tab-active' ).siblings().removeClass( 'nav-tab-active' );

			$( $tab.attr( 'href' ) ).addClass( 'tab-panel-active' ).siblings().removeClass( 'tab-panel-active' );
		},

		toggleOptions: function( event ) {
			event.preventDefault();

			$( event.target ).closest( 'form' ).find( '.demo-import-options' ).stop( true, true ).fadeToggle( 'fast' );
		},

		startImporting: function() {
			if ( ! soodi.$importer.length ) {
				return;
			}

			// Collect steps.
			var steps = soodi.$importer.find( 'input[name="demo_parts"]' ).val();

			if ( ! steps ) {
				return;
			}

			if ( 'all' === steps ) {
				soodi.steps = ['content', 'customizer', 'widgets', 'sliders'];
			} else {
				soodi.steps = steps.split( ',' );
			}

			// Check if content is selected.
			soodi.containsContent = soodi.steps.indexOf( 'content' ) >= 0;

			// Check if need to regenerate images.
			soodi.regenImages = !! parseInt( soodi.$importer.find( 'input[name="regenerate_images"]' ).val() );

			// Check if this is manually upload.
			soodi.isManual = !! parseInt( soodi.$importer.find( 'input[name="uploaded"]' ).val() );

			// Let's go.
			if ( soodi.isManual ) {
				soodi.import( soodi.steps.shift() );
			} else {
				soodi.download( soodi.steps.shift() );
			}
		},

		download: function( type ) {
			soodi.log( 'Downloading ' + type + ' file' );

			$.get(
				ajaxurl,
				{
					action: 'soodi_download_file',
					type: type,
					demo: soodi.$importer.find( 'input[name="demo"]' ).val(),
					uploaded: soodi.$importer.find( 'input[name="uploaded"]' ).val(),
					_wpnonce: soodi.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( response.success ) {
						soodi.import( type );
					} else {
						soodi.log( response.data );

						if ( soodi.steps.length ) {
							soodi.download( soodi.steps.shift() );
						} else {
							soodi.configTheme();
						}
					}
				}
			).fail( function() {
				soodi.log( 'Failed' );
			} );
		},

		import: function( type ) {
			soodi.log( 'Importing ' + type );

			var data = {
					action: 'soodi_import',
					type: type,
					_wpnonce: soodi.$importer.find( 'input[name="_wpnonce"]' ).val()
				};
			var url = ajaxurl + '?' + $.param( data );
			var evtSource = new EventSource( url );

			evtSource.addEventListener( 'message', function ( message ) {
				var data = JSON.parse( message.data );

				switch ( data.action ) {
					case 'updateTotal':
						console.log( data.delta );
						break;

					case 'updateDelta':
						console.log(data.delta);
						break;

					case 'complete':
						evtSource.close();
						soodi.log( type + ' has been imported successfully!' );

						if ( soodi.steps.length ) {
							if ( soodi.isManual ) {
								soodi.import( soodi.steps.shift() );
							} else {
								soodi.download( soodi.steps.shift() );
							}
						} else {
							soodi.configTheme();
						}

						break;
				}
			} );

			evtSource.addEventListener( 'log', function ( message ) {
				var data = JSON.parse( message.data );
				soodi.log( data.message );
			});
		},

		configTheme: function() {
			// Stop if no content imported.
			if ( ! soodi.containsContent ) {
				soodi.generateImages();
				return;
			}

			$.get(
				ajaxurl,
				{
					action: 'soodi_config_theme',
					demo: soodi.$importer.find( 'input[name="demo"]' ).val(),
					_wpnonce: soodi.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( response.success ) {
						soodi.generateImages();
					}

					soodi.log( response.data );
				}
			).fail( function() {
				soodi.log( 'Failed' );
			} );
		},

		generateImages: function() {
			// Stop if no content imported.
			if ( ! soodi.containsContent || ! soodi.regenImages ) {
				soodi.log( 'Import completed!' );
				soodi.$progress.find( '.spinner' ).hide();
				return;
			}

			$.get(
				ajaxurl,
				{
					action: 'soodi_get_images',
					_wpnonce: soodi.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					if ( ! response.success ) {
						soodi.log( response.data );
						soodi.log( 'Import completed!' );
						soodi.$progress.find( '.spinner' ).hide();
						return;
					} else {
						var ids = response.data;

						if ( ! ids.length ) {
							soodi.log( 'Import completed!' );
							soodi.$progress.find( '.spinner' ).hide();
						}

						soodi.log( 'Starting generate ' + ids.length + ' images' );

						soodi.generateSingleImage( ids );
					}
				}
			);
		},

		generateSingleImage: function( ids ) {
			if ( ! ids.length ) {
				soodi.log( 'Import completed!' );
				soodi.$progress.find( '.spinner' ).hide();
				return;
			}

			var id = ids.shift();

			$.get(
				ajaxurl,
				{
					action: 'soodi_generate_image',
					id: id,
					_wpnonce: soodi.$importer.find( 'input[name="_wpnonce"]' ).val()
				},
				function( response ) {
					soodi.log( response.data + ' (' + ids.length + ' images left)' );

					soodi.generateSingleImage( ids );
				}
			);
		},

		log: function( message ) {
			soodi.$progress.find( '.text' ).text( message );
			soodi.$log.prepend( '<p>' + message + '</p>' );
		}
	};


	soodi.init();
} );
