( function( $ ) {

	"use strict";

	$( function() {

		/**
		 * Widget settings tabs
		 */
		$( '.js-sws-nav-sidebar-item' ).on( 'click', function() {
			var id = $( this ).data( 'href' ),
				holder = $('.sws_nav_sidebar');

			if ( ! $( this ).hasClass( 'nav-tab-active' ) ) {
				holder.find( '.js-sws-nav-sidebar-item' ).removeClass( 'nav-tab-active' );
				$( this ).addClass( 'nav-tab-active' );
				holder.parent().find( '.ymapp-settings__content' ).hide();

				$( id ).fadeIn();
				location.hash = id.replace( '_tab', '_tab_active' ).replace( '#', '' );
			}

			return false;
		} );

		$( '.js-sws-tab-mobile-heading' ).on( 'click', function() {
			// Close all sections except the clicked one
			var id = $( this ).data( 'href' );
			$('.js-sws-tab-mobile-heading').removeClass('sws_tab_mobile_heading--active');

			// Toggle the clicked section
			$(this).next('.sws_tab_content').slideToggle();

			if ($(this).next('.sws_tab_content').is(':visible')) {
				$(this).addClass('sws_tab_mobile_heading--active');
			}
			location.hash = id.replace( '_tab', '_tab_active' ).replace( '#', '' );

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

			if ("content" in document.createElement("template")) {
				var template = document.querySelector('#' + holder.attr('id') + '-tmpl');

				if ( template ) {
					holder.append( template.content.cloneNode(true) );
				}
			}

			holder.sortable( 'refresh' );
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
		 * Sortable Fields — Relevance Configurator
		 */
		if ( $( '.sws-sortable-fields' ).length ) {
			$( '.sws-sortable-fields' ).each( function() {
				var $list  = $( this );
				var $input = $list.siblings( 'input[type="hidden"]' );

				function updateOrder() {
					var order = [];
					var total = $list.find( 'li' ).length;
					$list.find( 'li' ).each( function( index ) {
						var key    = $( this ).data( 'key' );
						var weight = ( total - index ) * 10;
						$( this ).find( '.sws-sortable-fields__weight' ).text( weight );
						order.push( key );
					} );
					$input.val( JSON.stringify( order ) );
				}

				$list.sortable( {
					axis:   'y',
					handle: '.sws-sortable-fields__handle',
					update: function() {
						updateOrder();
					}
				} );
			} );
		}

		/**
		 * Tabs
		 */
		if ( location.hash.match(/_tab_active/) ) {
			var hash = location.hash.replace( '_tab_active', '_tab' ).replace( '#', '' ),
				currentTab = $( '.js-sws-nav-sidebar-item[data-href="#' + hash + '"]' ),
				currentTabMobile = $( '.js-sws-tab-mobile-heading[data-href="#' + hash + '"]' );
			if ( currentTab.length && ! currentTab.hasClass( 'nav-tab-active' ) ) {
				currentTab.trigger( 'click' );
			}


			if ($(window).width() <= 960 && currentTabMobile.length && ! currentTabMobile.hasClass( 'sws_tab_mobile_heading--active' ) ) {
				$('.sws_tab_content').hide();

				console.log(currentTabMobile.next('.sws_tab_content'))
				currentTabMobile.trigger( 'click' );
			}
		}

	} );

} )( jQuery );

(function($) {

	"use strict";

	$(function() {

		/**
		 * Init postboxes
		 */

		postboxes.add_postbox_toggles(pagenow);

		/**
		 * Widget list actions
		 */

		$('.ysm-default-widgets-list').on('click', '.ysm-widget-remove', function(){
			var $td      = $(this).parents('td'),
				$spinner = $td.find('.ysm-action-spinner'),
				id       = $(this).data('id');

			if ( confirm(ysm_L10n.widget_delete) ) {

				$spinner.addClass('is-active');

				$.post(ajaxurl, {
					action: 'ysm_default_widget_delete',
					id: id,
					nonce: $('#ysm_widgets_nonce').val()
				},
				function($r) {
					if ($r == 1) {
						$td.parent().remove();
					} else {
						$spinner.removeClass('is-active');
					}
				});

			}

			return false;
		});

		$('.ysm-custom-widgets-list').on('click', '.ysm-widget-remove', function(){
			var $td      = $(this).parents('td'),
				$spinner = $td.find('.ysm-action-spinner'),
				id       = $(this).data('id');

			if ( confirm(ysm_L10n.widget_delete) ) {

				$spinner.addClass('is-active');

				$.post(ajaxurl, {
					action: 'ysm_widget_delete',
					id: id,
					nonce: $('#ysm_widgets_nonce').val()
				},
				function($r) {
					if ($r == 1) {
						$td.parent().remove();
					} else {
						$spinner.removeClass('is-active');
					}
				});

			}

			return false;
		});

		$('.ysm-custom-widgets-list').on('click', '.ysm-widget-duplicate', function(){
			var $td      = $(this).parents('td'),
				$spinner = $td.find('.ysm-action-spinner'),
				id       = $(this).data('id'),
				tmpl     = wp.template('ysm-widget-list-row');

			$spinner.addClass('is-active');

			$.post(ajaxurl, {
				action: 'ysm_widget_duplicate',
				id: id,
				nonce: $('#ysm_widgets_nonce').val()
			},
			function($r) {
				$spinner.removeClass('is-active');

				if ($r && $r['id'] ) {
					var data = {
							id: $r['id'],
							name: $r['name']
						},
						output = tmpl(data),
						$row   = $(output);

					$row.find('.sws-enhance-toggle').addClass('sws-enhance-locked');

					$('.ysm-custom-widgets-list').find('tbody').append($row);
				}
			}, 'json');

			return false;
		});

		/**
		 * Enhance Default search bar toggle.
		 * Behaves like a radio group: enabling one disables all others.
		 * "Locked" toggles (sws-enhance-locked) are visually dimmed but still
		 * clickable — clicking one switches the selection to that widget.
		 */
		$(document).on('change', '.sws-enhance-toggle', function() {
			var $toggle  = $(this),
				$spinner = $toggle.siblings('.sws-enhance-spinner'),
				widgetId = $toggle.data('widget-id'),
				enabled  = $toggle.is(':checked');

			// Disable all toggles and show spinner while saving
			$('.sws-enhance-toggle').prop('disabled', true);
			$spinner.addClass('is-active');

			$.post(ajaxurl, {
				action:    'sws_enhance_default_toggle',
				widget_id: widgetId,
				enabled:   enabled ? 1 : 0,
				nonce:     $('#ysm_widgets_nonce').val()
			}, function(response) {
				$spinner.removeClass('is-active');
				$('.sws-enhance-toggle').prop('disabled', false);

				if ( ! response.success ) {
					$toggle.prop('checked', ! enabled);
					return;
				}

				if ( enabled ) {
					// Uncheck and lock every other toggle
					$('.sws-enhance-toggle').not($toggle).each(function() {
						$(this).prop('checked', false)
						       .addClass('sws-enhance-locked')
						       .removeClass('sws-enhance-active');
					});
					$toggle.addClass('sws-enhance-active').removeClass('sws-enhance-locked');
				} else {
					// No widget active — unlock everything
					$('.sws-enhance-toggle').removeClass('sws-enhance-locked sws-enhance-active');
				}
			}, 'json');
		});

		/**
		 * Shortcode column: click to copy to clipboard.
		 */
		$(document).on('click', '.sws-shortcode-input', function() {
			var $input = $(this),
				$msg   = $input.siblings('.sws-shortcode-copied'),
				text   = $input.val();

			function showCopied() {
				$msg.addClass('is-visible');
				setTimeout(function() { $msg.removeClass('is-visible'); }, 2000);
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText(text).then(showCopied);
			} else {
				$input[0].select();
				document.execCommand('copy');
				showCopied();
			}
		});

		/**
		 * GA4 master toggle: enable/disable Google Analytics 4 tracking.
		 * Shows/hides the event categories section without a page reload.
		 */
		$(document).on('change', '.sws-ga4-toggle', function() {
			var $toggle  = $(this),
				$spinner = $toggle.siblings('.sws-ga4-spinner'),
				enabled  = $toggle.is(':checked');

			$toggle.prop('disabled', true);
			$spinner.addClass('is-active');

			$.post(ajaxurl, {
				action:  'sws_ga4_toggle',
				enabled: enabled ? 1 : 0,
				nonce:   $('#ysm_analytics_nonce').val()
			}, function(response) {
				$spinner.removeClass('is-active');
				$toggle.prop('disabled', false);

				if ( ! response.success ) {
					$toggle.prop('checked', ! enabled);
					return;
				}

				$('.sws-ga-event-details').toggle(enabled);
			}, 'json');
		});

		/**
		 * GA event toggles: enable/disable individual analytics events.
		 */
		$(document).on('change', '.sws-ga-event-toggle', function() {
			var $toggle  = $(this),
				$row     = $toggle.closest('.sws-ga-event-row'),
				$spinner = $row.find('.sws-ga-event-spinner'),
				eventKey = $toggle.data('event'),
				enabled  = $toggle.is(':checked');

			$toggle.prop('disabled', true);
			$spinner.addClass('is-active');

			$.post(ajaxurl, {
				action:    'sws_ga_event_toggle',
				event_key: eventKey,
				enabled:   enabled ? 1 : 0,
				nonce:     $('#ysm_widgets_nonce').val()
			}, function(response) {
				$spinner.removeClass('is-active');
				$toggle.prop('disabled', false);

				if ( ! response.success ) {
					$toggle.prop('checked', ! enabled);
				}
			}, 'json');
		});

		$('.ymapp-settings__content #loader').on('change', function(){
			var new_val = $('.ysm-loader-preview').attr('src')
							.replace(/images\/\S*.gif$/, 'images/' + $(this).val() + '.gif');
			$('.ysm-loader-preview').attr('src', new_val);
		});


		// JS for autocomplete
		$('#selected_products').select2({
			ajax: {
				url: ysmProductSearch.ajax_url,
				type: 'GET',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					console.log('Select2 Search Params:', params);
					return {
						action: 'ysm_search_products',
						term: params.term
					};
				},
				processResults: function (data) {
					console.log('Select2 Received Data:', data);
					return {
						results: data || []
					};
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error('AJAX Error:', {
						status: jqXHR.status,
						statusText: jqXHR.statusText,
						responseText: jqXHR.responseText
					});
				}
			},
			minimumInputLength: 1,
			placeholder: 'Search products',
			templateResult: function(data) {
				return data.text;
			},
			templateSelection: function(data) {
				return data.text;
			}
		});

	});

})(jQuery);
