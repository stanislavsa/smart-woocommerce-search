<div id="ysm_w_settings_general" class="ysm-widget-settings-tab">

	<table class="form-table">
		<tbody>

		<?php
		if ($w_id === 'default') {
			ysm_setting( $w_id, 'enable_search', array(
				'type' => 'checkbox',
				'title' => __('Default Search Widget', 'smart_search'),
				'description' => __('Enable Smart Search in Default Search Widget', 'smart_search'),
				'value' => 1,
			));
		}

		if ($w_id === 'product') {
			ysm_setting( $w_id, 'enable_product_search', array(
				'type' => 'checkbox',
				'title' => __('Product Search Widget', 'smart_search'),
				'description' => __('Enable Smart Search in Product Search Widget', 'smart_search'),
				'value' => 1,
			));
		}

		if ($w_id !== 'default' && $w_id !== 'product') {
			ysm_setting($w_id, 'placeholder', array(
				'type' => 'text',
				'title' => __('Placeholder', 'smart_search'),
				'description' => __('Placeholder in search input', 'smart_search'),
				'value' => _x('Search &hellip;', 'placeholder', 'smart_search'),
			));
		}

		ysm_setting( $w_id, 'char_count', array(
			'type' => 'text',
			'title' => __('Character Amount', 'smart_search'),
			'description' => __('Minimum number of character', 'smart_search'),
			'value' => 3,
		));

		ysm_setting( $w_id, 'max_post_count', array(
			'type' => 'text',
			'title' => __('Results Listing Amount', 'smart_search'),
			'description' => __('Maximum number of results', 'smart_search'),
			'value' => 3,
		));

		ysm_setting( $w_id, 'search_page_default_output', array(
			'type' => 'checkbox',
			'title' => __('Default Output on Search Page', 'smart_search'),
			'description' => __("Disable altering search results by Smart Search plugin on the search results page.<br>By default the plugin modified search results according to selected options", 'smart_search'),
			'value' => 0,
		));

		ysm_setting( $w_id, 'search_page_layout_posts', array(
			'type' => 'checkbox',
			'title' => __('Search Page Layout with Posts', 'smart_search'),
			'description' => __("Display posts with products using theme search results layout.<br>Usefull if you want to display posts with products.<br>By default if 'Search in Products' option selected only products displays using WooCommerce search results layout", 'smart_search'),
			'value' => 0,
		));

		ysm_setting( $w_id, 'no_results_text', array(
			'type' => 'text',
			'title' => __('"No Results" text', 'smart_search'),
			'description' => __('If not empty displays when no results returned', 'smart_search'),
			'value' => __( 'No Results', 'smart_search' ),
		));

		ysm_setting( $w_id, 'display_icon', array(
			'type' => 'checkbox',
			'title' => __('Display Image', 'smart_search'),
			'description' => __('Display featured image in results output', 'smart_search'),
			'value' => 1,
		));

		ysm_setting( $w_id, 'display_excerpt', array(
			'type' => 'checkbox',
			'title' => __('Display Excerpt', 'smart_search'),
			'description' => __('Display excerpt in results output', 'smart_search'),
			'value' => 1,
		));

		ysm_setting( $w_id, 'excerpt_symbols_count', array(
			'type' => 'text',
			'title' => __('Excerpt Symbols Amount', 'smart_search'),
			'description' => __('Maximum number of symbols for description in results', 'smart_search'),
			'value' => '50',
		));

		ysm_setting( $w_id, 'display_price', array(
			'type' => 'checkbox',
			'title' => __('Display Price', 'smart_search'),
			'description' => __('Display product price', 'smart_search'),
			'value' => 1,
		));

		ysm_setting( $w_id, 'display_sku', array(
			'type' => 'checkbox',
			'title' => __('Display SKU', 'smart_search'),
			'description' => __('Display product SKU', 'smart_search'),
			'value' => 1,
		));

		ysm_setting( $w_id, 'view_all_link_text', array(
			'type' => 'text',
			'title' => __('"View all" Link Text', 'smart_search'),
			'description' => __('If not empty displays a link at the bottom of results popup', 'smart_search'),
			'value' => __('View all', 'smart_search'),
		));

		ysm_setting( $w_id, 'accent_words_on_search_page', array(
			'type' => 'checkbox',
			'title' => __('Accent Words on Search Page', 'smart_search'),
			'description' => __('Accent searchable words on search page. Works only if "Default Output on Search Page" option is disabled.', 'smart_search'),
			'value' => 0,
		));

		ysm_setting( $w_id, 'enable_fuzzy_search', array(
			'type' => 'select',
			'title' => __('Fuzzy Search', 'smart_search'),
			'description' => __('Enable multiple word search.', 'smart_search'),
			'value' => '',
			'choices' => array(
				'0'  => __('No', 'smart_search'),
				'1' => __('"alpha" OR "beta"', 'smart_search'),
				'2' => __('"alpha" AND "beta"', 'smart_search'),
			),
		));

		?>

		</tbody>
	</table>

</div>