(function ($) {

	"use strict";

	$(function(){

		if ( ysm_L10n.enable_search == 1 ) {

			$( '.widget_search' ).each(function () {
				ysm_init_autocomplete( this, {
					id: 'default',
					serviceUrl: ysm_L10n.restUrl + 'id=default',
					layout: ysm_L10n['layout'],
					maxHeight: 400,
					minChars: ysm_L10n.char_count,
					no_results_text: ysm_L10n.no_results_text
				} );
			});

		}

		if ( ysm_L10n.enable_product_search == 1 ) {

			$( '.woocommerce.widget_product_search' ).each(function () {
				ysm_init_autocomplete( this, {
					id: 'product',
					serviceUrl: ysm_L10n.restUrl + 'id=product',
					layout: 'product',
					maxHeight: 400,
					minChars: ysm_L10n.product_char_count,
					no_results_text: ysm_L10n.product_no_results_text
				} );
			});
		}

		if ( ysm_L10n.enable_avada_search && ysm_L10n.enable_avada_search == 1 ) {

			$( '.fusion-search-form' ).each(function () {
				ysm_init_autocomplete( this, {
					id: 'avada',
					serviceUrl: ysm_L10n.restUrl + 'id=avada',
					layout: ysm_L10n.avada_layout,
					maxHeight: ysm_L10n.avada_popup_height,
					minChars: ysm_L10n.avada_char_count,
					no_results_text: ysm_L10n.avada_no_results_text,
					loaderIcon: ysm_L10n.avada_loader_icon,
					preventBadQueries: ysm_L10n.avada_prevent_bad_queries
				} );
			});
		}

		var $custom_widgets = $( '.ysm-search-widget' );

		if ( $custom_widgets.length ) {

			$custom_widgets.each(function () {
				var id   = $( this ).find('form').data('id'),
					attr = {
						id: id,
						serviceUrl: ysm_L10n.restUrl + 'id=' + encodeURIComponent( id ),
						layout: ysm_L10n['custom_'+ id +'_layout'],
						maxHeight: 400,
						minChars: ysm_L10n['custom_'+ id +'_char_count'],
						no_results_text: ysm_L10n['custom_'+ id +'_no_results_text']
					};

				ysm_init_autocomplete( this, attr );
			});

		}

		/**
		 * Init autocomplete on search widget input
		 *
		 * @param el
		 * @param attr
		 */
		function ysm_init_autocomplete(el, attr) {

			var $this = $(el).find('input[type="search"]').length ? $(el).find('input[type="search"]') : $(el).find('input[type="text"]'),
				$form = ( el.tagName === 'FORM' || el.tagName === 'form' ) ? $( el ) : $( el ).find( 'form' );

			if ( ! $this.length ) {
				return;
			}

			$(el).addClass('ysm-active');

			var defaults = {
				id: '',
				serviceUrl: ysm_L10n.restUrl,
				layout: '',
				maxHeight: 600,
				minChars: 3,
				loaderIcon: ysm_L10n.loader_icon
			};

			var options = $.extend({}, defaults, attr);

			$('<div class="smart-search-results"></div>').appendTo($form);

			var $results_wrapper = $form.find('.smart-search-results').css({
				maxHeight: options.maxHeight + 'px',
				width: $this.outerWidth() + 'px'
			});

			if (navigator.userAgent.indexOf('Windows') !== -1 && navigator.userAgent.indexOf('Firefox') !== -1) {
				$results_wrapper.addClass('smart-search-firefox');
			}

			$form.on('submit', function(e){
				var val = $this.val();

				if( val === '' || val.length < options.minChars ){
					return false;
				} else {
					var action = $(this).attr('action');

					val = val.replace(/\+/g, '%2b');
					val = val.replace(/\s/g, '+');
					action += ( -1 !== action.indexOf('?') ) ? '&' : '?';
					action += 's=' + val + '&search_id=' + options.id;

					if ( options.layout === 'product' ) {
						action += '&post_type=product';
					}

					e.preventDefault();
					location.href = action;
				}
			});

			$(window).on('resize', function () {
				var _width = $this.outerWidth() + 'px';

				$results_wrapper.css({
					width: _width
				}).find('.smart-search-suggestions').css({
					width: _width
				});
			});

			$this.devbridgeAutocomplete({
				minChars        : options.minChars,
				appendTo        : $results_wrapper,
				serviceUrl      : options.serviceUrl,
				maxHeight       : options.maxHeight,
				dataType        : 'json',
				deferRequestBy  : 100,
				noCache         : false,
				containerClass  : 'smart-search-suggestions',
				triggerSelectOnValidInput: false,
				showNoSuggestionNotice: options.no_results_text.length ? true : false,
				noSuggestionNotice: options.no_results_text,
				formatResult: function (suggestion, currentValue) {
					return suggestion.data;
				},
				onSearchStart   : function ( query ) {
					if ( this.value.indexOf( '  ' ) !== -1 ) {
						this.value = this.value.replace( /\s+/g, ' ' );
					}
					var trimmed = $.trim( this.value );
					if ( trimmed !== this.value ) {
						return false;
					}
					query.query = query.query.replace( /%20/g, ' ' );

					$this.css({'background-image': 'url(' + options.loaderIcon + ')','background-repeat': 'no-repeat', 'background-position': '50% 50%'});
				},
				onSelect        : function (suggestion) {
					if (suggestion.id != -1) {
						window.location.href = suggestion.url;
					}
				},
				transformResult: function (response) {
					var res = typeof response === 'string' ? $.parseJSON(response) : response,
						val = $this.val();

					if (res && res.view_all_link && res.view_all_link != '') {
						if ( !$results_wrapper.find('.smart-search-view-all-holder').length ) {
							$results_wrapper.addClass('has-viewall-button').append('<div class="smart-search-view-all-holder"></div>');
						}
						$results_wrapper.find('.smart-search-view-all-holder').html(res.view_all_link);
					}

					return res;
				},
				onSearchComplete: function (query, suggestions) {

					$this.css( 'background-image', 'none' );

					if ( suggestions.length > 0 ) {

						$results_wrapper.parents( '.ysm-active' ).removeClass( 'ysm-hide' );

						setTimeout( function() {
							var content = $results_wrapper.find( '.smart-search-suggestions' ),
								maxHeight = parseInt( $results_wrapper.css( "max-height" ), 10 ),
								contentEl = content[0],
								contentElChildren = $( contentEl ).find( '.autocomplete-suggestion' ),
								contentElHeight = 0,
								viewAllButton = $results_wrapper.find( '.smart-search-view-all-holder' );

							if ( contentElChildren.length ) {
								contentElChildren.each(function () {
									contentElHeight += this.scrollHeight + parseInt( $( this ).css( 'borderBottomWidth' ), 10 );
								});
								contentElHeight += parseInt( $( contentEl ).css( 'borderTopWidth' ), 10 ); // border top of .smart-search-suggestions
								contentElHeight += parseInt( $( contentEl ).css( 'borderBottomWidth' ), 10 ); // border bottom of .smart-search-suggestions
							}

							if ( $results_wrapper.outerWidth() == 0 ) {
								$results_wrapper.width( $this.outerWidth() + 'px' );
							}

							$results_wrapper.nanoScroller({
								contentClass: 'smart-search-suggestions',
								alwaysVisible: false,
								iOSNativeScrolling: true
							});

							$results_wrapper.height( contentElHeight > maxHeight ? maxHeight : contentElHeight );


							$( contentEl ).height( 'auto' );

							if ( viewAllButton.length ) {
								if ( $this.val().length < options.minChars ) {
									viewAllButton.hide();
								} else {
									var serviceUrl = options.serviceUrl,
										cacheKey,
										that = $this.devbridgeAutocomplete();

									if ( $.isFunction( serviceUrl ) ) {
										serviceUrl = serviceUrl.call( that.element, query );
									}
									cacheKey = serviceUrl + '?' + $.param( { query: query } );
									if ( that.cachedResponse && that.cachedResponse[cacheKey] ) {
										viewAllButton.html( that.cachedResponse[cacheKey].view_all_link );
									}
									viewAllButton.show();
								}
							}

						}, 50);

					} else if ( options.no_results_text.length ) {
						$results_wrapper.css({
							maxHeight: 'auto',
							height: 42
						}).nanoScroller({
							contentClass: 'smart-search-suggestions',
							stop: true
						});

						$results_wrapper.find( '.smart-search-suggestions' ).height( 40 );
						$results_wrapper.find( '.smart-search-view-all-holder' ).hide();
					} else {
						$results_wrapper.find( '.smart-search-suggestions' ).height( 0 );
						$results_wrapper.find( '.smart-search-view-all-holder' ).hide();
					}

				},
				onSearchError: function (query, jqXHR, textStatus, errorThrown) {
					$results_wrapper.css({
						maxHeight: 'auto',
						height: 0
					}).nanoScroller({
						contentClass: 'smart-search-suggestions',
						stop: true
					});

					$results_wrapper.find('.smart-search-view-all-holder').hide();
				},
				onInvalidateSelection: function () {

				},
				onHide: function () {
					$results_wrapper.css({
						maxHeight: 'auto',
						height: 0
					}).nanoScroller({
						contentClass: 'smart-search-suggestions',
						stop: true
					});

					$results_wrapper.find('.smart-search-view-all-holder').hide();
				}
			}).on('focus', function () {
				$this.devbridgeAutocomplete().onValueChange();
			});

			$(window).on( 'touchstart' , function(event) {
				var $wrapper = $( event.target ).hasClass( 'ysm-active' ) ? $( event.target ) : $( event.target ).parents( '.ysm-active' );
				if ( $wrapper.length ) {
					$wrapper.removeClass( 'ysm-hide' );
				} else {
					$('.ysm-active').addClass( 'ysm-hide' );
				}
			});

		}

	});

})(jQuery);
