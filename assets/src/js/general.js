(function ($) {

	"use strict";

	$(function(){
		var swsL10n = window.swsL10n || {};

		if ( swsL10n ) {
			for ( var wId in swsL10n.widgets ) {
				if ( $( swsL10n.widgets[wId].selector ).length ) {
					$( swsL10n.widgets[wId].selector ).each(function () {
						sws_init_autocomplete( this, {
							id: wId,
							serviceUrl: swsL10n.restUrl + 'id=' + encodeURIComponent( wId ),
							layout: swsL10n.widgets[wId].layout,
							columns: swsL10n.widgets[wId].columns,
							productSlug: swsL10n.widgets[wId].productSlug,
							maxHeight: swsL10n.widgets[wId].popupHeight,
							maxHeightMobile: swsL10n.widgets[wId].popupHeightMobile,
							minChars: swsL10n.widgets[wId].charCount,
							disableAjax: swsL10n.widgets[wId].disableAjax,
							no_results_text: swsL10n.widgets[wId].noResultsText,
							loaderIcon: swsL10n.widgets[wId].loaderIcon,
							preventBadQueries: swsL10n.widgets[wId].preventBadQueries
						} );
					});
				}
			}
		}

		/**
		 * Init autocomplete on search widget input
		 *
		 * @param el
		 * @param attr
		 */
		function sws_init_autocomplete( el, attr ) {

			var $el = $( el ),
				$this = $el.find( 'input[type="search"]' ).length ? $el.find( 'input[type="search"]' ) : $el.find( 'input[type="text"]' ),
				$form = ( el.tagName === 'FORM' || el.tagName === 'form' ) ? $el : $el.find( 'form' );

			if ( ! $this.length ) {
				return;
			}

			if ( $el.hasClass('ysm-active') || $form.hasClass('ysm-active') ) {
				return;
			}

			$el.addClass( 'ysm-active' ).addClass( 'ysm-hide' );
			$form.addClass( 'ysm-active' );

			var defaults = {
				id: '',
				serviceUrl: swsL10n.restUrl,
				layout: '',
				columns: 1,
				productSlug: 'product',
				maxHeight: 500,
				maxHeightMobile: 400,
				minChars: 3,
				disableAjax: false,
				no_results_text: '',
				loaderIcon: '',
				preventBadQueries: true,
				cache: true
			};

			var options = $.extend( {}, defaults, attr );

			$form.on( 'submit', function( e ) {
				var val = $this.val();

				if ( val === '' || val.length < options.minChars ) {
					return false;
				} else {
					var action = swsL10n.searchPageUrl;

					val = val.replace( /\+/g, '%2b' );
					val = val.replace( /\s/g, '+' );
					action += ( -1 !== action.indexOf( '?' ) ) ? '&' : '?';
					action += 's=' + val + '&search_id=' + options.id;

					if ( options.layout === 'product' ) {
						action += '&post_type=' + options.productSlug;
					}

					e.preventDefault();
					location.href = action;
				}
			} );

			if ( options.disableAjax ) {
				return;
			}

			$( '<div class="smart-search-popup"><div class="smart-search-results"><div class="smart-search-results-inner"></div></div></div>' ).appendTo( $form );

			var $popup = $form.find( '.smart-search-popup' );
			var popupWidth = 0;

			if ( $this.outerWidth() ) {
				popupWidth = $this.outerWidth();
				$popup.css({
					width: popupWidth + 'px'
				});
			}

			var $results_wrapper = $form.find( '.smart-search-results' );
			var $resultsWrapperInner = $results_wrapper.find( '.smart-search-results-inner' );
			var maxHeightValue = ( Math.min(window.screen.width, window.screen.height) < 768 ) ? options.maxHeightMobile : options.maxHeight;

			$results_wrapper.css({
				maxHeight: maxHeightValue + 'px',
			});

			if ( navigator.userAgent.indexOf( 'Windows' ) !== -1 && navigator.userAgent.indexOf( 'Firefox' ) !== -1 ) {
				$results_wrapper.addClass( 'smart-search-firefox' );
			}

			$( window ).on( 'resize', function () {
				$popup.css({
					width: $this.outerWidth() + 'px'
				});
			});

			$( window ).on( 'touchstart' , function( event ) {
				var $wrapper = $( event.target ).hasClass( 'ysm-active' ) ? $( event.target ) : $( event.target ).parents( '.ysm-active' );
				if ( ! $wrapper.length ) {
					$( '.ysm-active' ).addClass( 'ysm-hide' );
				}
			});

			$this.devbridgeAutocomplete({
				minChars        : options.minChars,
				appendTo        : $resultsWrapperInner,
				serviceUrl      : options.serviceUrl,
				maxHeight       : 100000,
				dataType        : 'json',
				deferRequestBy  : 100,
				noCache         : ! options.cache,
				containerClass  : 'smart-search-suggestions',
				triggerSelectOnValidInput: false,
				showNoSuggestionNotice: options.no_results_text.length ? true : false,
				noSuggestionNotice: options.no_results_text,
				preventBadQueries: options.preventBadQueries,
				ajaxSettings: {
					beforeSend: function(xhr) {
						if ( swsL10n.nonce ) {
							xhr.setRequestHeader('X-WP-Nonce', swsL10n.nonce);
						}
					},
				},
				formatResult: function ( suggestion, currentValue ) {
					return suggestion.data;
				},
				onSearchStart   : function ( query ) {

					if ( this.value.indexOf( '  ' ) !== -1 ) {
						this.value = this.value.replace( /\s+/g, ' ' );
					}

					query.query = query.query.replace( /%20/g, ' ' );

					$this.css( {'background-image': 'url(' + options.loaderIcon + ')','background-repeat': 'no-repeat', 'background-position': '50% 50%'} );

					$el.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
				},
				onSelect        : function ( suggestion ) {
					if ( suggestion.id != -1 && suggestion.url && ! suggestion.addToCart ) {
						window.location.href = suggestion.url;
					}
				},
				transformResult: function ( response ) {
					var res = typeof response === 'string' ? $.parseJSON( response ) : response,
						val = $this.val();

					if ( res && res.view_all_link && res.view_all_link != '' ) {
						if ( ! $popup.find( '.smart-search-view-all-holder' ).length ) {
							$popup.addClass( 'has-viewall-button' ).append( '<div class="smart-search-view-all-holder"></div>' );
						}
						$popup.find( '.smart-search-view-all-holder' ).html( res.view_all_link );
					}

					return res;
				},
				onSearchComplete: function ( query, suggestions ) {
					if ( query !== $this.val() ) {
						return;
					}

					if ( ! popupWidth ) {
						popupWidth = $this.outerWidth();
						$popup.css({
							width: popupWidth + 'px'
						});
					}

					$this.css( 'background-image', 'none' );

					if ( suggestions.length > 0 ) {

						$el.removeClass( 'ysm-hide' ).removeClass( 'sws-no-results' );

						setTimeout( function() {
							var $wrapperWidth = $results_wrapper.outerWidth();
							// var columns = suggestions.length < options.columns ? suggestions.length : options.columns;
							var columns = options.columns;
							var $viewAllEl = $popup.find( '.smart-search-view-all-holder' );

							if ( $wrapperWidth === 0 ) {
								$wrapperWidth = $this.outerWidth();
								$results_wrapper.width( $wrapperWidth + 'px' );
							}

							if ( $wrapperWidth < columns * 200 ) {
								columns = Math.floor( $wrapperWidth / 200 );
							}

							if ( ! $results_wrapper.outerHeight() ) {
								var suggestionsHeight = $resultsWrapperInner.find('.smart-search-suggestions').outerHeight();

								if ( suggestionsHeight ) {
									suggestionsHeight = parseInt( suggestionsHeight, 10 );
									$results_wrapper.height( suggestionsHeight > maxHeightValue ? maxHeightValue : suggestionsHeight );
								}
							}

							$results_wrapper
								.attr( 'data-columns', columns )
								.nanoScroller({
									contentClass: 'smart-search-results-inner',
									alwaysVisible: false,
									iOSNativeScrolling: true
								});

							if ( $viewAllEl.length ) {
								if ( $this.val().length < options.minChars ) {
									$viewAllEl.hide();
								} else {
									var serviceUrl = options.serviceUrl,
										cacheKey,
										that = $this.devbridgeAutocomplete();

									if ( $.isFunction( serviceUrl ) ) {
										serviceUrl = serviceUrl.call( that.element, query );
									}
									cacheKey = serviceUrl + '?' + $.param( { query: query } );
									if ( that.cachedResponse && that.cachedResponse[cacheKey] ) {
										$viewAllEl.html( that.cachedResponse[cacheKey].view_all_link );
									}
									$viewAllEl.show();
								}
							}

						}, 100);

					} else if ( options.no_results_text.length ) {
						$el.removeClass( 'ysm-hide' ).addClass( 'sws-no-results' );
					} else {
						$el.addClass( 'ysm-hide' ).addClass( 'sws-no-results' );
					}

				},
				onSearchError: function ( query, jqXHR, textStatus, errorThrown ) {
					if ( textStatus === 'error' ) {
						var nonceRefresh = jqXHR.getResponseHeader('X-Wp-Nonce');

						if ( nonceRefresh && swsL10n.nonce !== nonceRefresh ) {
							window.swsL10n.nonce = nonceRefresh;
							$this.devbridgeAutocomplete().onValueChange();
							return;
						} else if ( swsL10n.nonce ) {
							window.swsL10n.nonce = '';
							$this.devbridgeAutocomplete().onValueChange();
							return;
						}
					}

					$el.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
				},
				onInvalidateSelection: function () {

				},
				onHide: function () {
					$el.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
				}
			}).on( 'focus', function () {
				$this.devbridgeAutocomplete().onValueChange();
			});
		}

	});

})(jQuery);
