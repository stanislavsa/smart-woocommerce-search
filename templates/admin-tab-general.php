<div id="general_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>
		<?php
		if ( 'default' === $w_id ) {
			ysm_setting( $w_id, 'enable_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart_search' ),
				'description' => __( 'Extend Default search bar with the Smart Search features.', 'smart_search' ),
				'value'       => 1,
			));
		}

		if ( 'product' === $w_id ) {
			ysm_setting( $w_id, 'enable_product_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart_search' ),
				'description' => __( 'Extend Product search bar with the Smart Search features.', 'smart_search' ),
				'value'       => 1,
			));
		}

		if ( 'avada' === $w_id ) {
			ysm_setting( $w_id, 'enable_avada_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart_search' ),
				'description' => __( 'Extend Avada search bar with the Smart Search features.', 'smart_search' ),
				'value'       => 1,
			));
		}

		if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) {
			ysm_setting( $w_id, 'placeholder', array(
				'type'        => 'text',
				'title'       => __( 'Placeholder', 'smart_search' ),
				'description' => __( 'Placeholder in search input', 'smart_search' ),
				'value'       => _x( 'Search &hellip;', 'placeholder', 'smart_search' ),
			));
		}

		ysm_setting( $w_id, 'char_count', array(
			'type'        => 'text',
			'title'       => __( 'Character Amount', 'smart_search' ),
			'description' => __( 'Minimum number of character', 'smart_search' ),
			'value'       => 3,
		));

		ysm_setting( $w_id, 'max_post_count', array(
			'type'        => 'text',
			'title'       => __( 'Results Listing Amount', 'smart_search' ),
			'description' => __( 'Maximum number of results', 'smart_search' ),
			'value'       => 3,
		));

		ysm_setting( $w_id, 'no_results_text', array(
			'type'        => 'text',
			'title'       => __( '"No Results" text', 'smart_search' ),
			'description' => __( 'If not empty displays when no results returned', 'smart_search' ),
			'value'       => 'No Results',
		));

		ysm_setting( $w_id, 'excerpt_symbols_count', array(
			'type'        => 'text',
			'title'       => __( 'Excerpt Symbols Amount', 'smart_search' ),
			'description' => __( 'Maximum number of symbols for description in results', 'smart_search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'view_all_link_text', array(
			'type'        => 'text',
			'title'       => __( '"View all" Link Text', 'smart_search' ),
			'description' => __( 'If not empty displays a link at the bottom of results popup', 'smart_search' ),
			'value'       => 'View all',
		));

		ysm_setting( $w_id, 'view_all_link_found_posts', array(
			'type'        => 'pro',
			'title'       => __( 'Number of Found Posts in the "View all" Link', 'smart_search' ),
			'description' => __( 'Display total number of found posts in the "View all" link<br>This is advanced option, use it only if you know what you do.<br>It may affect search performance', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_page_default_output', array(
			'type'        => 'checkbox',
			'title'       => __( 'Default Output on Search Page', 'smart_search' ),
			'description' => __( "Disable altering search results by Smart Search plugin on the search results page.<br>By default the plugin modified search results according to selected options", 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_page_layout_posts', array(
			'type'        => 'checkbox',
			'title'       => __( 'Search Page Layout with Posts', 'smart_search' ),
			'description' => __( "Display posts with products using theme search results layout.<br>Usefull if you want to display posts with products.<br>By default if 'Search in Products' option selected only products displays using WooCommerce search results layout", 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'accent_words_on_search_page', array(
			'type'        => 'checkbox',
			'title'       => __( 'Accent Words on Search Page', 'smart_search' ),
			'description' => __( 'Accent searchable words on search page. Works only if "Default Output on Search Page" option is disabled.', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'enable_fuzzy_search', array(
			'type'        => 'select',
			'title'       => __( 'Multiple Word Search', 'smart_search' ),
			'description' => __( 'Enable multiple word search.', 'smart_search' ),
			'value'       => '',
			'choices'     => array(
				'0' => __( 'No', 'smart_search' ),
				'1' => __( '"alpha" OR "beta"', 'smart_search' ),
				'2' => __( '"alpha" AND "beta"', 'smart_search' ),
			),
		));

		ysm_setting( $w_id, 'exclude_out_of_stock_products', array(
			'type'        => 'checkbox',
			'title'       => __( 'Exclude "Out of stock"', 'smart_search' ),
			'description' => __( 'Exclude "Out of stock" products from results', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'product_variation_visibility', array(
			'type'        => 'pro',
			'title'       => __( 'Visibility of Variable Products', 'smart_search' ),
			'description' => __( 'Select visibility of variable products and variations', 'smart_search' ),
		));

		ysm_setting( $w_id, 'enable_transient', array(
			'type'        => 'pro',
			'title'       => __( 'Enable Transient', 'smart_search' ),
			'description' => __( 'Save query results in a transient', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_sku_first', array(
			'type'        => 'pro',
			'title'       => __( 'SKU Search Top Priority', 'smart_search' ),
			'description' => __( 'Search by SKU first if this option is checked and if request is numeric', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'skip_punctuation', array(
			'type'        => 'pro',
			'title'       => __( 'Skip Punctuation', 'smart_search' ),
			'description' => __( 'Skip punctuation in the search string', 'smart_search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'disable_ajax', array(
			'type'        => 'checkbox',
			'title'       => __( 'Disable AJAX', 'smart_search' ),
			'description' => __( 'Disable AJAX functionality (results popup)', 'smart_search' ),
			'value'       => 0,
		));
		?>
		</tbody>
	</table>
</div>