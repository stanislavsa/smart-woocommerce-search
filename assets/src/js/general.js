(function ($) {

	"use strict";

	$(function(){
		var swsL10n = window.swsL10n || {};

		if ( swsL10n ) {
			for ( var wId in swsL10n.widgets ) {
				if ( $( swsL10n.widgets[wId].selector ).length ) {

					const widgets = swsL10n.widgets[wId];

					let params = {
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
						preventBadQueries: swsL10n.widgets[wId].preventBadQueries,
						fullScreenMode: swsL10n.widgets[wId].fullScreenMode,
						placeholder: swsL10n.widgets[wId].placeholder,
						recentSearches: swsL10n.widgets[wId].recentSearches,
						recentSearchesTitle: swsL10n.widgets[wId].recentSearchesTitle,
						keywords: swsL10n.widgets[wId].keywords,
						keywordsLabel: swsL10n.widgets[wId].keywordsLabel,
						selectedCategories: swsL10n.widgets[wId].selectedCategories,
						selectedCategoriesLabel: swsL10n.widgets[wId].selectedCategoriesLabel,
						selectedCategoriesLocation: swsL10n.widgets[wId].selectedCategoriesLocation,
						selectedCategoriesMobile: swsL10n.widgets[wId].selectedCategoriesMobile,
						selectedCategoriesCount: swsL10n.widgets[wId].selectedCategoriesCount,
						selectedCategoriesOnOpen: swsL10n.widgets[wId].selectedCategoriesOnOpen,

						promoBannerLocation: swsL10n.widgets[wId].promoBannerLocation,
						promoBannerImage: swsL10n.widgets[wId].promoBannerImage,
						promoBannerLink: swsL10n.widgets[wId].promoBannerLink,
						promoBannerOnOpen: swsL10n.widgets[wId].promoBannerOnOpen,
					}

					$(widgets.selector).each(function () {
						const fullscreenMode = widgets.fullScreenMode;
						const isDesktop = $(window).width() >= 768;
						const shouldFullscreen =
							(fullscreenMode == 'desktop_only' && isDesktop) ||
							(fullscreenMode == 'mobile_only' && !isDesktop) ||
							fullscreenMode == 'enable';

						if (shouldFullscreen) {
							sws_init_autocomplete_fullscreen(this, params);
						} else {
							sws_init_autocomplete(this, params);
						}
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

			let $el = $( el ),
				$this = $el.find( 'input[type="search"]' ).length ? $el.find( 'input[type="search"]' ) : $el.find( 'input[type="text"]' ),
				$form = ( el.tagName === 'FORM' || el.tagName === 'form' ) ? $el : $el.find( 'form' ),
				swsCurrentArray = JSON.parse(localStorage.getItem("swsLatestSearches")) || [],
				$swsKeywords = [];


			if ( ! $this.length ) {
				return;
			}

			if ( $el.hasClass('ysm-active') || $form.hasClass('ysm-active') ) {
				return;
			}

			$form.addClass( 'ysm-active' ).addClass( 'ysm-hide' );

			// Body class if widget on page
			if (!$(document.body).toggleClass('ysm-widget-active', !$(document.body).hasClass('ysm-widget-active'))) {
				return false;
			}

			// Divi theme compatibility
			if ($(document.body).hasClass('ysm-widget-active')) {
				$el.parents('.et_pb_column').css({
					overflow: 'visible',
					zIndex: '10'
				});
				$el.parents('.et_pb_module').css('overflow', 'visible');
			}

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

			let swsUpdateRecentSearches = (removeHiddenClass = false)=> {

				if (removeHiddenClass === true) {
					$('.sws-search-recent-wrapper').removeClass('sws-search-recent-wrapper--hidden_mod');
				}
				let swsLatestFive = JSON.parse(localStorage.getItem("swsLatestSearches")).slice(-5);
				$('.sws-search-recent-list').empty();
				swsLatestFive.forEach(item => {
					$('.sws-search-recent-list').append(`
							<li class="sws-search-recent-list-item">
								<span class="sws-search-recent-list-item-trigger">${item}</span>
								<span class="sws-search-recent-list-item-delete" data-item="${item}" aria-label="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></span>
							</li>
						`);
				});
			}
			$(document).off('click', '.sws-search-recent-list-item-trigger').on('click', '.sws-search-recent-list-item-trigger', (e) => {
				e.stopPropagation();
				let targetText = $(e.target).text();
				$(e.currentTarget).parents('form').find('input[type="search"]').val(targetText).focus();
			});

			$(document).off('click', '.sws-search-recent-list-item-delete').on('click', '.sws-search-recent-list-item-delete', function(e) {
				e.stopPropagation();
				const itemToDelete = $(this).data('item');
				let currentStorage = JSON.parse(localStorage.getItem("swsLatestSearches")).filter(item => item !== itemToDelete)
				localStorage.setItem("swsLatestSearches", JSON.stringify(currentStorage));

				swsCurrentArray = JSON.parse(localStorage.getItem("swsLatestSearches"));

				swsUpdateRecentSearches(true);

				if (swsCurrentArray.length == 0) {
					$('.sws-search-recent-wrapper').addClass('sws-search-recent-wrapper--hidden_mod');
				}

			});
			if (options.recentSearches) {
				$('<div class="sws-search-recent-wrapper"><h4 class="sws-search-recent-title">'+ options.recentSearchesTitle+'</h4><ul class="sws-search-recent-list"></ul></div>').prependTo($popup);

				if (swsCurrentArray.length) {
					swsUpdateRecentSearches(true);
				}
			}


			$( window ).on( 'resize', function () {
				$popup.css({
					width: $this.outerWidth() + 'px'
				});
			});

			$(document).on('click', '.smart-search-popup-backdrop', ()=>{
				$form.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
			})

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

					$form.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
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

					$this.css( 'background-image', 'none' );

					if (res.keywords.length) {
						$popup.addClass( 'hidden-searches' )

					} else {
						$popup.removeClass( 'hidden-searches' )
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

						$form.removeClass( 'ysm-hide' ).removeClass( 'sws-no-results' );

						const $recentWrapper = $('.sws-search-recent-wrapper');
						var $viewAllEl = $popup.find( '.smart-search-view-all-holder' );

						setTimeout( function() {
							var $wrapperWidth = $results_wrapper.outerWidth();
							// var columns = suggestions.length < options.columns ? suggestions.length : options.columns;
							var columns = options.columns;


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



						}, 100);

						if ($viewAllEl.length || options.keywords) {

							const handleVisibility = (element, query, callback) => {
								if ($this.val().length < options.minChars) {
									element.hide();
								} else {
									const that = $this.devbridgeAutocomplete();
									let serviceUrl = options.serviceUrl;

									if ($.isFunction(serviceUrl)) {
										serviceUrl = serviceUrl.call(that.element, query);
									}

									const cacheKey = serviceUrl + '?' + $.param({ query: query });

									if (that.cachedResponse && that.cachedResponse[cacheKey]) {
										callback(that.cachedResponse[cacheKey]);
									}
									element.show();
								}
							};

							if ($viewAllEl.length) {
								handleVisibility($viewAllEl, query, (cachedResponse) => {
									$viewAllEl.html(cachedResponse.view_all_link);
								});
							}

							if (options.keywords) {
								const $recentWrapper = $('.sws-search-recent-wrapper');
								handleVisibility($('.smart-search-keywords-wrapper'), query, (cachedResponse) => {
									$swsKeywords = cachedResponse.keywords;

								});

								if ( ! $popup.find( '.smart-search-keywords-wrapper' ).length ) {
									$popup.addClass( 'sws-has-keywords' ).prepend( '<div class="smart-search-keywords-wrapper smart-search-keywords-wrapper--hidden_mod"><h3 class="smart-search-keywords-title">'+ options.keywordsLabel+'</h3><ul class="smart-search-keywords-list"></ul></div>' );
								}

								if ($swsKeywords.length) {
									$('.smart-search-keywords-list').empty();
									$('.smart-search-keywords-wrapper').removeClass('smart-search-keywords-wrapper--hidden_mod');
									$swsKeywords.forEach(item => {
										$('.smart-search-keywords-list').append(`
											<li class="sws-search-recent-list-item">
												<span class="sws-search-recent-list-item-trigger">${item}</span>
											</li>
										`);
									});
								}

								if ($recentWrapper.length) {
									if ($swsKeywords.length) {
										$('.smart-search-keywords-wrapper').removeClass('smart-search-keywords-wrapper--hidden_mod');
										$('.sws-search-recent-wrapper').addClass('sws-search-recent-wrapper--hidden_by_keywords');

									} else {
										$('.smart-search-keywords-wrapper').addClass('smart-search-keywords-wrapper--hidden_mod');
										$('.sws-search-recent-wrapper').removeClass('sws-search-recent-wrapper--hidden_by_keywords');
									}
								}
							}
						}

						if ( options.recentSearches ) {
							const currentSearchValue = query;


							if (!swsCurrentArray.includes(currentSearchValue)) {
								swsCurrentArray.push(currentSearchValue);
								if (swsCurrentArray.length > 10) {
									swsCurrentArray.shift(); // Remove the oldest item to keep the array size at 10
								}
								localStorage.setItem("swsLatestSearches", JSON.stringify(swsCurrentArray));
							}
							if (swsCurrentArray.length) {
								if ($recentWrapper.length == 0) {

									$('<div class="sws-search-recent-wrapper"><h4 class="sws-search-recent-title">'+ options.recentSearchesTitle+'</h4><ul class="sws-search-recent-list"></ul></div>').prependTo($popup);
								}
								swsUpdateRecentSearches(true);
							}
						}

					} else if ( options.no_results_text.length ) {
						$form.removeClass( 'ysm-hide' ).addClass( 'sws-no-results' );
					} else {
						$form.addClass( 'ysm-hide' ).addClass( 'sws-no-results' );
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

					$form.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
				},
				onInvalidateSelection: function () {

				},
				onHide: function (e) {
					$form.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );
				}
			}).on( 'focus', function () {
				$this.devbridgeAutocomplete().onValueChange();
			});
		}



		function sws_init_autocomplete_fullscreen( el, attr ) {

			let placeholder = attr.placeholder ? attr.placeholder :  '';

			let $el = $( el ),
				$form = ( el.tagName === 'FORM' || el.tagName === 'form' ) ? $el : $el.find( 'form' ),
				$popup = $form.find( '.smart-search-popup' );


			if ( $el.hasClass('ysm-active') || $form.hasClass('ysm-active') ) {
				return;
			}

			$form.addClass( 'ysm-active' ).addClass( 'ysm-hide' );

			// Body class if widget on page
			if (!$(document.body).toggleClass('ysm-widget-active', !$(document.body).hasClass('ysm-widget-active'))) {
				return false;
			}

			// Divi theme compatibility
			if ($(document.body).hasClass('ysm-widget-active')) {
				$el.parents('.et_pb_column').css({
					overflow: 'visible',
					zIndex: '10'
				});
				$el.parents('.et_pb_module').css('overflow', 'visible');
			}

			$( '<div class="smart-search-fullscreen">' +
				'<div class="smart-search-fullscreen-backdrop"></div>'+
				'<div class="smart-search-fullscreen-inner">'+
				'<div class="smart-search-input-wrapper">'+
				'<input type="search" class="ssf-search-input" placeholder="'+placeholder+'" name="s" id="smart-search-fullscreen-'+attr.id+'">' +
				'<span class="ssf-search-icon-search"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg></span>'+
				'<span class="ssf-search-icon-close" aria-label="close"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></span>'+
				'</div>'+
				'<div class="smart-search-results-main">'+
				'<div class="smart-search-results">' +
				'	<div class="smart-search-results-inner"></div>' +
				'</div>' +
				'</div>' +
				'</div>' +
				'</div>' ).appendTo( $form );

			let $fullscreen_wrapper = $form.find('.smart-search-fullscreen'),
				$fullscreen_backdrop = $form.find('.smart-search-fullscreen-backdrop'),
				$search_trigger = $el.find( 'input[type="search"]' ).length ? $el.find( 'input[type="search"]' ) : $el.find( 'input[type="text"]' ),
				$this = $el.find( '.ssf-search-input' ).length ? $el.find( '.ssf-search-input' ) : $el.find( '.ssf-search-input' ),
				$results_main = $form.find( '.smart-search-results-main' ),
				$results_wrapper = $form.find( '.smart-search-results' ),
				$resultsWrapperInner = $results_wrapper.find( '.smart-search-results-inner' ),
				$clear_search = $form.find( '.ssf-search-icon-close' ),
				$btn_trigger = $el.find( '.search-submit' ).length ? $el.find( '.search-submit' ) : '',
				swsCurrentArray = JSON.parse(localStorage.getItem("swsLatestSearches")) || [],
				$swsKeywords = [];

			let defaults = {
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

			let options = $.extend( {}, defaults, attr );

			let swsUpdateRecentSearches = (removeHiddenClass = false)=> {

				if (removeHiddenClass === true) {
					$('.sws-search-recent-wrapper').removeClass('sws-search-recent-wrapper--hidden_mod');
				}

				let swsLatestFive = JSON.parse(localStorage.getItem("swsLatestSearches")).slice(-5);
				$('.sws-search-recent-list').empty();
				swsLatestFive.forEach(item => {
					$('.sws-search-recent-list').append(`
							<li class="sws-search-recent-list-item">
								<span class="sws-search-recent-list-item-trigger">${item}</span>
								<span class="sws-search-recent-list-item-delete" data-item="${item}" aria-label="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></span>
							</li>
						`);
				});
			}

			if (options.selectedCategories || options.promoBannerImage) {

				$results_main.addClass('smart-search-results-main--column_mod');

				if(options.selectedCategoriesMobile) {
					$results_main.addClass('smart-search-results-main--hidden_mobile_mod');
				}

				if(options.selectedCategoriesOnOpen) {
					$results_main.addClass('smart-search-results-main--on_open_mod');
				}

				$('<div class="sws-sidebar-holder"></div>').prependTo($results_main);
				$('<div class="sws-sidebar sws-sidebar--left-mod"><div class="sws-sidebar-widget sws-sidebar--left_slot"></div><div class="sws-sidebar-widget sws-sidebar--left_slot_2"></div></div>').prependTo($results_main);

				$('<div class="sws-sidebar sws-sidebar--right-mod"><div class="sws-sidebar-widget sws-sidebar--right_slot"></div><div class="sws-sidebar-widget sws-sidebar--right_slot_2"></div></div>').appendTo($results_main);

				let selectedCategoriesHtml = $('<div class="sws-selected-categories ">' +
					'<h4 class="sws-selected-categories-title">'+ options.selectedCategoriesLabel +'</h4>' +
					'<ul class="sws-selected-categories-list"></ul>' +
					'</div>');

				let left_slot = $results_main.find('.sws-sidebar--left-mod .sws-sidebar--left_slot');
				let left_slot_2 = $results_main.find('.sws-sidebar--left-mod .sws-sidebar--left_slot_2');
				let right_slot = $results_main.find('.sws-sidebar--right-mod .sws-sidebar--right_slot');
				let right_slot_2 = $results_main.find('.sws-sidebar--right-mod .sws-sidebar--right_slot_2');


				let promoBanner = '';
				if (options.promoBannerLink.length) {
					promoBanner = $('<div class="sws-promo-banner">' +
					'<a href="'+ options.promoBannerLink +'" target="_blank"> ' +
					options.promoBannerImage +
					'</a>'+
					'</div>');
				} else {
					promoBanner = $('<div class="sws-promo-banner">' +
					options.promoBannerImage +
					'</div>');
				}

				$('.sws-sidebar-holder').hide();

				selectedCategoriesHtml.prependTo($results_main.find('.sws-sidebar--'+options.selectedCategoriesLocation+''));


				if (options.promoBannerImage) {

					if(options.promoBannerOnOpen) {
						$results_main.addClass('smart-search-results-main--banner_on_open_mod');
					}

					promoBanner.prependTo($results_main.find('.sws-sidebar--'+options.promoBannerLocation+''));
				}

				if (right_slot.is(':empty') && right_slot_2.is(':empty')) {
					$results_main.addClass('smart-search-results-main--widgets-on-left--mod');
				}

				if (left_slot.is(':empty') && left_slot_2.is(':empty')) {
					$('.sws-sidebar-holder').show();
					$results_main.addClass('smart-search-results-main--widgets-on-right--mod');
				}




				options.selectedCategories.forEach(item => {

					$results_main.find('.sws-selected-categories-list').append(`
						<li class="sws-selected-categories-item">
							<a class="sws-selected-categories-link" href="${item.url}">
								${item.name}
								${(options.selectedCategoriesCount && item.count > 0) ? `(${item.count})` : ''}
							</a>
						</li>
					`);
				});
			}

			if (options.recentSearches) {
				$('<div class="sws-search-recent-wrapper sws-search-recent-wrapper--hidden_mod"><h4 class="sws-search-recent-title">'+ options.recentSearchesTitle+'</h4><ul class="sws-search-recent-list"></ul></div>').prependTo($results_main);

				if (swsCurrentArray.length) {
					swsUpdateRecentSearches(true);
				}
			}

			$(document).off('click', '.sws-search-recent-list-item-trigger').on('click', '.sws-search-recent-list-item-trigger', (e)=> {
				e.stopPropagation();
				let targetText = $(e.target).text()
				$(e.currentTarget).parents('form').find( 'input[type="search"]' ).val(targetText).focus();
			});

			$(document).off('click', '.sws-search-recent-list-item-delete').on('click', '.sws-search-recent-list-item-delete', function(e) {
				e.stopPropagation();
				const itemToDelete = $(this).data('item');
				let currentStorage = JSON.parse(localStorage.getItem("swsLatestSearches")).filter(item => item !== itemToDelete)
				localStorage.setItem("swsLatestSearches", JSON.stringify(currentStorage));

				swsCurrentArray = JSON.parse(localStorage.getItem("swsLatestSearches"));

				swsUpdateRecentSearches(true);

				if (swsCurrentArray.length == 0) {
					$('.sws-search-recent-wrapper').addClass('sws-search-recent-wrapper--hidden_mod');
				}

			});

			let showPopup = ()=> {
				$fullscreen_wrapper.addClass('ssf-active');

				swsCurrentArray = JSON.parse(localStorage.getItem("swsLatestSearches")) || [];

				setTimeout(()=> {

					$fullscreen_wrapper.addClass('ssf-animated');
					// Body class if fullscreen widget is opened
					$(document.body).addClass('ysm-widget-opened')
					$('.ssf-search-input').focus();

				}, 100);
			}

			let closePopup = ()=>{
				$fullscreen_wrapper.removeClass('ssf-active ssf-animated');
				$(document.body).removeClass('ysm-widget-opened')
				$('.sws-search-recent-wrapper').removeClass('sws-search-recent-wrapper--hidden_by_keywords');
				$('.smart-search-keywords-wrapper').addClass('smart-search-keywords-wrapper--hidden_mod');
				setTimeout(()=>{
					$('.ssf-search-input').val('');

					$results_wrapper.css({
						maxHeight: 0,
					});

					$('.smart-search-view-all-holder').hide();
					$('.smart-search-suggestions').empty();
					$form.addClass( 'ysm-hide' )
				}, 500)
			}

			$search_trigger.on('click', ()=> {
				showPopup();
			});

			if ($btn_trigger.length) {
				$btn_trigger.on('click', ()=>{
					showPopup();
				});
			}

			$(document).on('keydown', (event) => {
				if (event.key === 'Tab') {
					setTimeout(() => {
						const $focusedElement = $(document.activeElement);
						if ($focusedElement.is($search_trigger)) {
							showPopup();
						}
					}, 0);
				}
				if (event.key === 'Escape') {
					closePopup();

				}
			});


			$fullscreen_backdrop.on('click', ()=> {
				closePopup();
			})

			if ($clear_search.length > 0) {
				$clear_search.on('click', function () {
					closePopup();
				});
			}



			var maxHeightValue = ( Math.min(window.screen.width, window.screen.height) < 768 ) ? options.maxHeightMobile : options.maxHeight;

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

			var popupWidth = 0;

			if ( $this.outerWidth() ) {
				popupWidth = $this.outerWidth();
				$popup.css({
					width: popupWidth + 'px'
				});
			}

			if ( navigator.userAgent.indexOf( 'Windows' ) !== -1 && navigator.userAgent.indexOf( 'Firefox' ) !== -1 ) {
				$results_wrapper.addClass( 'smart-search-firefox' );
			}

			$( window ).on( 'resize', function () {
				$popup.css({
					width: $this.outerWidth() + 'px'
				});
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

					$form.addClass( 'ysm-hide' ).removeClass( 'sws-no-results' );

					$results_main.addClass('sws-hiding-results');

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
						if ( ! $results_main.find( '.smart-search-view-all-holder' ).length ) {
							$results_main.addClass( 'has-viewall-button' ).append( '<div class="smart-search-view-all-holder"></div>' );
						}
						$results_main.find( '.smart-search-view-all-holder' ).html( res.view_all_link );
					}

					$this.css( 'background-image', 'none' );

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

						$form.removeClass( 'ysm-hide' ).removeClass( 'sws-no-results' );

						$results_main.removeClass('sws-hiding-results');

						var $wrapperWidth = $results_wrapper.outerWidth();
						// var columns = suggestions.length < options.columns ? suggestions.length : options.columns;
						var columns = options.columns;
						var $viewAllEl = $results_main.find( '.smart-search-view-all-holder' );

						if ( $wrapperWidth === 0 ) {
							$wrapperWidth = $this.outerWidth();
							$results_wrapper.width( $wrapperWidth + 'px' );
						}

						if ( $wrapperWidth < columns * 200 ) {
							columns = Math.floor( $wrapperWidth / 200 );
						}

						let swsWindowHeight = window.innerHeight
						let swsMarginsHeight = 180;
						let swsButtonHeight = $viewAllEl.length ? 60 : 0;
						let swsRecentSearchesHeight = 0;
						if (options.recentSearches || options.keywords) {
							swsRecentSearchesHeight = $(window).width() >= 768 ? 45 : 110;
						}
						let swsMaximumHeight = swsWindowHeight - swsMarginsHeight - swsButtonHeight - swsRecentSearchesHeight;

						if (maxHeightValue > swsMaximumHeight) {
							$results_wrapper.css({
								maxHeight: swsMaximumHeight + 'px',
							});
						} else {
							$results_wrapper.css({
								maxHeight: maxHeightValue + 'px',
							});
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

						if ($viewAllEl.length || options.keywords) {

							const handleVisibility = (element, query, callback) => {
								if ($this.val().length < options.minChars) {
									element.hide();
								} else {
									const that = $this.devbridgeAutocomplete();
									let serviceUrl = options.serviceUrl;

									if ($.isFunction(serviceUrl)) {
										serviceUrl = serviceUrl.call(that.element, query);
									}

									const cacheKey = serviceUrl + '?' + $.param({ query: query });

									if (that.cachedResponse && that.cachedResponse[cacheKey]) {
										callback(that.cachedResponse[cacheKey]);
									}
									element.show();
								}
							};

							if ($viewAllEl.length) {
								handleVisibility($viewAllEl, query, (cachedResponse) => {
									$viewAllEl.html(cachedResponse.view_all_link);
								});
							}

							if (options.keywords) {
								const $recentWrapper = $('.sws-search-recent-wrapper');
								handleVisibility($('.smart-search-keywords-wrapper'), query, (cachedResponse) => {
									$swsKeywords = cachedResponse.keywords;

								});

								if ( ! $results_main.find( '.smart-search-keywords-wrapper' ).length ) {
									$results_main.addClass( 'sws-has-keywords' ).prepend( '<div class="smart-search-keywords-wrapper smart-search-keywords-wrapper--hidden_mod"><h3 class="smart-search-keywords-title">'+ options.keywordsLabel+'</h3><ul class="smart-search-keywords-list"></ul></div>' );
								}

								if ($swsKeywords.length) {
									$('.smart-search-keywords-list').empty();
									$('.smart-search-keywords-wrapper').removeClass('smart-search-keywords-wrapper--hidden_mod');
									$swsKeywords.forEach(item => {
										$('.smart-search-keywords-list').append(`
											<li class="sws-search-recent-list-item">
												<span class="sws-search-recent-list-item-trigger">${item}</span>
											</li>
										`);
									});
								}

								if ($recentWrapper.length) {

									if ($swsKeywords.length) {
										$('.smart-search-keywords-wrapper').removeClass('smart-search-keywords-wrapper--hidden_mod');
										$recentWrapper.addClass('sws-search-recent-wrapper--hidden_by_keywords');

									} else {
										$('.smart-search-keywords-wrapper').addClass('smart-search-keywords-wrapper--hidden_mod');
										$recentWrapper.removeClass('sws-search-recent-wrapper--hidden_by_keywords');
									}
								}
							}
						}

						if ( options.recentSearches ) {
							const currentSearchValue = query;

							if (!swsCurrentArray.includes(currentSearchValue)) {
								swsCurrentArray.push(currentSearchValue);
								if (swsCurrentArray.length > 10) {
									swsCurrentArray.shift();
								}
								localStorage.setItem("swsLatestSearches", JSON.stringify(swsCurrentArray));
							}
							if (swsCurrentArray.length) {
								swsUpdateRecentSearches(true);
							}
						}

					} else if ( options.no_results_text.length ) {
						$form.removeClass( 'ysm-hide' ).addClass( 'sws-no-results' );
						$results_main.removeClass('sws-hiding-results');
					} else {
						$form.addClass( 'ysm-hide' ).addClass( 'sws-no-results' );
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

				},
				onInvalidateSelection: function () {

				},
				onHide: function () {}
			}).on( 'focus', function () {
				$this.devbridgeAutocomplete().onValueChange();
			});
		}

	});

})(jQuery);