(function () {
	"use strict";

	document.addEventListener('DOMContentLoaded', function () {

		if ( window.swsL10n.analytics.ga4 ) {

			// promo_banner_click
			document.addEventListener('click', function(e) {
				const banner = e.target.closest('.sws-promo-banner');
				if (!banner) return;

				const el = e.target.closest('a');
				if (!el) return;

				e.preventDefault();

				const href = el.getAttribute('href');
				const form = el.closest('form');

				swsUtils.pushToAnalytics({
					term: href,
					category: swsUtils.analyticCategoryTypes.promo_banner_click,
					wId: form ? form.getAttribute('data-id') : null
				});

				window.open(href, '_blank');
			}, true);

			// selected_categories_click
			document.addEventListener('click', function(e) {
				const el = e.target.closest('.sws-selected-categories-link');
				if (!el) return;

				e.preventDefault();

				const href = el.getAttribute('href');
				const form = el.closest('form');

				swsUtils.pushToAnalytics({
					term: href,
					category: swsUtils.analyticCategoryTypes.selected_categories_click,
					wId: form ? form.getAttribute('data-id') : null
				});

				window.open(href, '_blank');
			}, true);

			// recommended_products_click
			document.addEventListener('click', function(e) {
				const item = e.target.closest('.sws-selected-products-item');
				if (!item) return;

				const el = e.target.closest('a');
				if (!el) return;

				e.preventDefault();
				e.stopImmediatePropagation();

				const href = el.getAttribute('href');
				const form = el.closest('form');

				swsUtils.pushToAnalytics({
					term: href,
					category: swsUtils.analyticCategoryTypes.recommended_products_click,
					wId: form ? form.getAttribute('data-id') : null
				});

				window.open(href, '_blank');
			}, true);

			// search_results_link_click
			document.addEventListener('click', function(e) {
				const el = e.target.closest('a.smart-search-post-url');
				if (!el) return;

				if (el.classList.contains('add_to_cart_button')) return;

				e.preventDefault();
				e.stopImmediatePropagation();

				const href = el.getAttribute('href');
				const form = el.closest('form');

				swsUtils.pushToAnalytics({
					term: href,
					category: swsUtils.analyticCategoryTypes.search_results_link_click,
					wId: form ? form.getAttribute('data-id') : null
				});

				window.open(href, '_blank');
			}, true);

			// search_results_cart_click
			document.addEventListener('click', function(e) {
				const holder = e.target.closest('.smart-search-add_to_cart-holder');
				if (!holder) return;

				const el = e.target.closest('a');
				if (!el) return;

				const isAjax = el.classList.contains('ajax_add_to_cart');

				if (!isAjax) {
					e.preventDefault();
				}

				const suggestion = el.closest('.autocomplete-suggestion');
				const href = suggestion
					? suggestion.querySelector('.smart-search-post-url')?.getAttribute('href')
					: null;

				const form = el.closest('form');

				swsUtils.pushToAnalytics({
					term: href,
					category: swsUtils.analyticCategoryTypes.search_results_cart_click,
					wId: form ? form.getAttribute('data-id') : null
				});

				if (!isAjax) {
					window.open(el.getAttribute('href'), '_blank');
				}
			}, true);

			// view_all_click
			document.addEventListener('click', function(e) {
				const holder = e.target.closest('.smart-search-view-all-holder');
				if (!holder) return;

				const el = e.target.closest('a');
				if (!el) return;

				e.preventDefault();
				e.stopImmediatePropagation();

				const href = el.getAttribute('href');
				const form = el.closest('form');

				if (form) {
					const input =
						form.querySelector('.smart-search-input-wrapper input[type="search"]') ||
						form.querySelector('input[type="search"]') ||
						form.querySelector('input[type="text"]');

					if (input) {
						swsUtils.pushToAnalytics({
							term: input.value,
							category: swsUtils.analyticCategoryTypes.view_all_click,
							wId: form.getAttribute('data-id')
						});
					}
				}

				window.open(href, '_blank');
			}, true);
		}

	});

})();