const swsUtils = {
	analyticCategoryTypes: {
		search_term_has_results:    "[Search term] has results",
		search_term_no_results:     "[Search term] no results",
		promo_banner_click:         "[Promo banner] link click",
		selected_categories_click:  "[Selected Categories] link click",
		recommended_products_click: "[Recommended Products] link click",
		search_results_link_click:  "[Found Suggestions] link click",
		search_results_cart_click:  "[Found Suggestions] add-to-cart click",
		view_all_click:             "[View All] button click"
	},

	pushToDataLayer: function (term, category, wId) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({
			event: 'swsEvent',
			swsTerm: term,
			swsCategory: category,
			swsWidgetId: wId
		});
	},

	pushToAnalytics: function (args) {
		let {term, category, wId} = args;
		if (window.swsL10n.analytics.ga4) {
			console.log('SWS pushToAnalytics ', `[${wId}]: [${term}] "${category}"`);
			swsUtils.pushToDataLayer(term, category, wId);
		}
	},

	updateRecentSearches: (removeHiddenClass = false)=> {
		if (removeHiddenClass === true) {
			document.querySelector('.sws-search-recent-wrapper')?.classList.remove('sws-search-recent-wrapper--hidden_mod');
		}

		const swsLatestFive = JSON.parse(localStorage.getItem('swsLatestSearches')).slice(-5);
		const list = document.querySelector('.sws-search-recent-list');

		list.innerHTML = '';

		swsLatestFive.forEach(item => {
			const li = document.createElement('li');
			li.className = 'sws-search-recent-list-item';
			li.innerHTML = `
			<span class="sws-search-recent-list-item-trigger">${item}</span>
			<span class="sws-search-recent-list-item-delete" data-item="${item}" aria-label="close">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
				<path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
				</svg>
			</span>
			`;
			list.appendChild(li);
		});
	}
};