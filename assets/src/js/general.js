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
							productSlug: swsL10n.widgets[wId].productSlug,
							maxHeight: swsL10n.widgets[wId].popupHeight,
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

			var $this = $( el ).find( 'input[type="search"]' ).length ? $( el ).find( 'input[type="search"]' ) : $( el ).find( 'input[type="text"]' ),
				$form = ( el.tagName === 'FORM' || el.tagName === 'form' ) ? $( el ) : $( el ).find( 'form' );

			if ( ! $this.length ) {
				return;
			}

			if ( $( el ).hasClass('ysm-active') ) {
				return;
			}

			$( el ).addClass( 'ysm-active' );

			var defaults = {
				id: '',
				serviceUrl: swsL10n.restUrl,
				layout: '',
				productSlug: 'product',
				maxHeight: 400,
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

			$( '<div class="smart-search-results"></div>' ).appendTo( $form );

			var $results_wrapper = $form.find( '.smart-search-results' ).css({
				maxHeight: options.maxHeight + 'px',
				width: $this.outerWidth() + 'px'
			});

			if ( navigator.userAgent.indexOf( 'Windows' ) !== -1 && navigator.userAgent.indexOf( 'Firefox' ) !== -1 ) {
				$results_wrapper.addClass( 'smart-search-firefox' );
			}

			$( window ).on( 'resize', function () {
				var _width = $this.outerWidth() + 'px';

				$results_wrapper.css({
					width: _width
				}).find( '.smart-search-suggestions' ).css({
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
				noCache         : ! options.cache,
				containerClass  : 'smart-search-suggestions',
				triggerSelectOnValidInput: false,
				showNoSuggestionNotice: options.no_results_text.length ? true : false,
				noSuggestionNotice: options.no_results_text,
				preventBadQueries: options.preventBadQueries,
				formatResult: function ( suggestion, currentValue ) {
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

					$this.css( {'background-image': 'url(' + options.loaderIcon + ')','background-repeat': 'no-repeat', 'background-position': '50% 50%'} );
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
						if ( ! $results_wrapper.find( '.smart-search-view-all-holder' ).length ) {
							$results_wrapper.addClass( 'has-viewall-button' ).append( '<div class="smart-search-view-all-holder"></div>' );
						}
						$results_wrapper.find( '.smart-search-view-all-holder' ).html( res.view_all_link );
					}

					return res;
				},
				onSearchComplete: function ( query, suggestions ) {

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
				onSearchError: function ( query, jqXHR, textStatus, errorThrown ) {
					$results_wrapper.css({
						maxHeight: 'auto',
						height: 0
					}).nanoScroller({
						contentClass: 'smart-search-suggestions',
						stop: true
					});

					$results_wrapper.find( '.smart-search-view-all-holder' ).hide();
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

					$results_wrapper.find( '.smart-search-view-all-holder' ).hide();
				}
			}).on( 'focus', function () {
				$this.devbridgeAutocomplete().onValueChange();
			});

			$( window ).on( 'touchstart' , function( event ) {
				var $wrapper = $( event.target ).hasClass( 'ysm-active' ) ? $( event.target ) : $( event.target ).parents( '.ysm-active' );
				if ( $wrapper.length ) {
					$wrapper.removeClass( 'ysm-hide' );
				} else {
					$( '.ysm-active' ).addClass( 'ysm-hide' );
				}
			});

		}

	});

})(jQuery);
