( function( $ ) {

	"use strict";

	$( function() {

		/**
		 * Widget settings tabs
		 */
		$( '#ymapp-settings__nav > .nav-tab' ).on( 'click', function() {
			var id = $( this ).data( 'href' ),
				holder = $( this ).parent( '.nav-tab-wrapper' );

			if ( ! $( this ).hasClass( 'nav-tab-active' ) ) {
				holder.find( '.nav-tab' ).removeClass( 'nav-tab-active' );
				$( this ).addClass( 'nav-tab-active' );
				holder.parent().find( '.ymapp-settings__content' ).hide();
				$( id ).fadeIn();
				location.hash = id.replace( '_tab', '_tab_active' ).replace( '#', '' );
			}

			return false;
		} );

		/**
		 *  Checkbox Switcher
		 */
		$( '.ymapp-switcher' ).on( 'change', function() {
			if ( $( this ).prop( 'checked' ) ) {
				$( this ).prev( 'input[type=hidden]' ).val( 1 );
			} else {
				$( this ).prev( 'input[type=hidden]' ).val( 0 );
			}
		} );

		/**
		 * Select2
		 */
		if ( $( '.ymapp-settings__content select' ).length ) {
			$( '.ymapp-settings__content select' ).select2({
				placeholder: 'Select ...',
				width: '200px'
			});
		}

		/**
		 * Init colorpicker
		 */
		if ( $( '.ymapp-settings__content .ymapp-color-picker' ).length ) {
			$( '.ymapp-settings__content .ymapp-color-picker' ).wpColorPicker();
		}

		/**
		 * Image Uploader
		 */
		$( 'body' ).on( 'click', '.image-uploader .image-add', function( e ) {
			e.preventDefault();

			var holder = $( this ).parents( '.image-uploader' );
			var frame = wp.media( {
				title: wp.media.view.l10n.chooseImage,
				multiple: false,
				library: { type: 'image' }
			} );

			frame.on( 'select', function() {
				var attachment = frame.state().get( 'selection' ).first().toJSON();

				holder.addClass( 'has-image' )
					.find( 'input[type="hidden"]' ).val( attachment.id ).end()
					.find( '.image-preview-img' ).attr( 'src', attachment.url );
			} );

			frame.open();
		} );
		$( 'body' ).on( 'click', '.image-uploader .image-delete', function( e ) {
			e.preventDefault();

			$( this ).parents( '.image-uploader' ).removeClass( 'has-image' )
				.find( 'input[type="hidden"]' ).val( '' ).end()
				.find( '.image-preview-img' ).attr( 'src', '' );
		} );
		$( 'body' ).on( 'click', '.image-uploader .image-edit', function( e ) {
			e.preventDefault();
			var holder = $( this ).parents( '.image-uploader' );
			var val = holder.find( 'input[type="hidden"]' ).val();
			var frame = wp.media( {
				title: 'Edit Image',
				multiple: false,
				library: { type: 'image' },
				button: { text: 'Update Image' }
			} );

			frame.on( 'open', function() {

				if ( 'browse' !== wp.media.frame.content._mode ) {
					wp.media.frame.content.mode( 'browse' );
				}

				var attachment = wp.media.attachment( val );
				if ( $.isEmptyObject( attachment.changed ) ) {
					attachment.fetch();
				}

				wp.media.frame.state().get( 'selection' ).add( attachment );
			} );

			frame.on( 'select', function() {
				var attachment = frame.state().get( 'selection' ).first().toJSON();

				holder.addClass( 'active' )
					.find( 'input[type="hidden"]' ).val( attachment.id ).end()
					.find( '.image-preview-img' ).attr( 'src', attachment.url );
			} );

			frame.open();
		} );

		/**
		 * Repeater
		 */
		$( 'body' ).on( 'click', '.repeater-add', function( e ) {
			e.preventDefault();

			var holder = $( this ).siblings( '.repeater-holder' );
			var row    = holder.find( 'li:last' ).clone();
			row.find( 'input' ).val( '' );
			holder.append( row );

			$( '.repeater-holder' ).sortable( 'refresh' );
		} );

		$( 'body' ).on( 'click', '.repeater-delete', function( e ) {
			e.preventDefault();

			$( this ).parent( 'li' ).remove();
			$( '.repeater-holder' ).sortable( 'refresh' );
		} );

		if ( $( '.repeater-holder' ).length ) {
			$( '.repeater-holder' ).sortable({
				axis: 'y',
				handle: '.repeater-move'
			});
		}

		/**
		 * Tabs
		 */
		if ( location.hash.match(/_tab_active/) ) {
			var hash = location.hash.replace( '_tab_active', '_tab' ).replace( '#', '' ),
				currentTab = $( '#ymapp-settings__nav > .nav-tab[data-href="#' + hash + '"]' );
			if ( currentTab.length && ! currentTab.hasClass( 'nav-tab-active' ) ) {
				currentTab.trigger( 'click' );
			}
		}

	} );

} )( jQuery );
