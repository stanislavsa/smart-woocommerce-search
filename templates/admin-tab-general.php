<div id="general_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>
		<?php
		if ( 'default' === $w_id ) {
			ysm_setting( $w_id, 'enable_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart-woocommerce-search' ),
				'description' => __( 'Extend the default WordPress "Search" widget with the Smart Search features.', 'smart-woocommerce-search' ),
				'value'       => 1,
			));
		}

		if ( 'product' === $w_id ) {
			ysm_setting( $w_id, 'enable_product_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart-woocommerce-search' ),
				'description' => __( 'Extend the WooCommerce "Product Search" widget with the Smart Search features.', 'smart-woocommerce-search' ),
				'value'       => 1,
			));
		}

		if ( 'avada' === $w_id ) {
			ysm_setting( $w_id, 'enable_avada_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Smart Search', 'smart-woocommerce-search' ),
				'description' => __( 'Extend the Avada search bar with the Smart Search features.', 'smart-woocommerce-search' ),
				'value'       => 1,
			));
		}
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Input Field', 'smart-woocommerce-search' ); ?></th>

		<?php
		if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) {
			ysm_setting( $w_id, 'placeholder', array(
				'type'        => 'text',
				'title'       => __( 'Placeholder', 'smart-woocommerce-search' ),
				'description' => __( 'Custom search bar placeholder', 'smart-woocommerce-search' ),
				'value'       => _x( 'Search &hellip;', 'placeholder', 'smart-woocommerce-search' ),
			));
		}

		ysm_setting( $w_id, 'char_count', array(
			'type'        => 'text',
			'title'       => __( 'Minimum Number of Characters', 'smart-woocommerce-search' ),
			'description' => __( 'Minimum number of characters required to start a search', 'smart-woocommerce-search' ),
			'value'       => 3,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'max_post_count', array(
			'type'        => 'text',
			'title'       => __( 'Maximum Number of Results', 'smart-woocommerce-search' ),
			'description' => __( 'Maximum number of results that can be displayed in a popup', 'smart-woocommerce-search' ),
			'value'       => 3,
		));

		ysm_setting( $w_id, 'excerpt_symbols_count', array(
			'type'        => 'text',
			'title'       => __( 'Excerpt Size Limit', 'smart-woocommerce-search' ),
			'description' => __( 'Maximum number of characters in description', 'smart-woocommerce-search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'no_results_text', array(
			'type'        => 'text',
			'title'       => __( '"No Results" Text', 'smart-woocommerce-search' ),
			'description' => __( 'Displays when no results are found', 'smart-woocommerce-search' ),
			'value'       => 'No Results',
		));

		ysm_setting( $w_id, 'view_all_link_text', array(
			'type'        => 'text',
			'title'       => __( '"View All" Button Text', 'smart-woocommerce-search' ),
			'description' => __( 'The button is only displayed if the field is not empty', 'smart-woocommerce-search' ),
			'value'       => 'View all',
		));

		ysm_setting( $w_id, 'view_all_link_target_blank', array(
			'type'        => 'checkbox',
			'title'       => __( 'New Tab on "View All" Button Click', 'smart-woocommerce-search' ),
			'description' => __( 'Open results in a new tab when "View All" button clicked. Adds target="_blank" attribute to the "View All" button', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'view_all_link_found_posts', array(
			'type'        => 'checkbox',
			'title'       => __( 'Number of Found Posts in the "View All" Button', 'smart-woocommerce-search' ),
			'description' => __( 'Display total number of found posts in the "View All" button.<br>This setting may affect search performance', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'disable_ajax', array(
			'type'        => 'checkbox',
			'title'       => __( 'Disable AJAX', 'smart-woocommerce-search' ),
			'description' => __( 'Disables AJAX functionality. The results popup will not appear', 'smart-woocommerce-search' ),
			'value'       => 0,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Search Features', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'enable_fuzzy_search', array(
			'type'        => 'select',
			'title'       => __( 'Multiple Word Search', 'smart-woocommerce-search' ),
			'description' => __( 'Enable multiple word search', 'smart-woocommerce-search' ),
			'value'       => '',
			'choices'     => array(
				'0' => __( 'No', 'smart-woocommerce-search' ),
				'1' => __( '"alpha" OR "beta"', 'smart-woocommerce-search' ),
				'2' => __( '"alpha" AND "beta"', 'smart-woocommerce-search' ),
			),
		));

		ysm_setting( $w_id, 'exclude_out_of_stock_products', array(
			'type'        => 'checkbox',
			'title'       => __( 'Exclude "Out of stock"', 'smart-woocommerce-search' ),
			'description' => __( 'Exclude "Out of stock" products/variations from results', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'exclude_out_of_stock_parent_variations', array(
			'type'        => 'checkbox',
			'title'       => '',
			'description' => __( 'Exclude variations from results if the parent product is "Out of stock"', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'product_variation_visibility', array(
			'type'        => 'select',
			'title'       => __( 'Visibility of Variable Products', 'smart-woocommerce-search' ),
			'description' => __( 'Select visibility of variable products and variations', 'smart-woocommerce-search' ),
			'choices'     => array(
				'all'        => __( 'Parent variable product with variations', 'smart-woocommerce-search' ),
				'parent'     => __( 'Only parent variable product', 'smart-woocommerce-search' ),
				'variations' => __( 'Only variations', 'smart-woocommerce-search' ),
			),
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'search_sku_first', array(
			'type'        => 'checkbox',
			'title'       => __( 'SKU Search Top Priority', 'smart-woocommerce-search' ),
			'description' => __( 'Search by SKU first. Only works if the request is numeric', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'skip_punctuation', array(
			'type'        => 'checkbox',
			'title'       => __( 'Skip Punctuation', 'smart-woocommerce-search' ),
			'description' => __( 'Skip punctuation in the search query', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Search Results Page', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'search_page_disable_redirect_single_result', array(
			'type'        => 'checkbox',
			'title'       => __( 'Disable Redirect to Product Page', 'smart-woocommerce-search' ),
			'description' => __( 'Disable default WooCommerce redirect to product page if there is only one search result', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_page_default_output', array(
			'type'        => 'checkbox',
			'title'       => __( 'Disable Smart Search Feature on the Search Results Page', 'smart-woocommerce-search' ),
			'description' => __( "Disable altering search results by Smart Search plugin on the search results page.<br>By default the plugin modified search results according to selected options", 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_page_layout_posts', array(
			'type'        => 'checkbox',
			'title'       => __( 'Disable WooCommerce Layout on the Search Results Page', 'smart-woocommerce-search' ),
			'description' => __( "Displays the default search results layout defined in the theme instead of WooCommerce template.<br>Useful if you want to display posts with products together", 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'accent_words_on_search_page', array(
			'type'        => 'checkbox',
			'title'       => __( 'Highlight Search Terms on the Search Results Page', 'smart-woocommerce-search' ),
			'description' => __( 'Highlight words that match a search query on the search results page. Works only if the Smart Search Feature is not disabled on the search results page', 'smart-woocommerce-search' ),
			'value'       => 0,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Advanced', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'enable_transient', array(
			'type'        => 'checkbox',
			'title'       => __( 'Enable Transient', 'smart-woocommerce-search' ),
			'description' => __( 'Save query results in a transient.<br>Might be useful if your server does not have Memcache/Redis', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'product_slug', array(
			'type'        => 'text',
			'title'       => __( 'Product Slug Override', 'smart-woocommerce-search' ),
			'description' => __( 'It may be helpful if you changed the base slug for WooCommerce products. The base slug is "product"', 'smart-woocommerce-search' ),
			'value'       => 'product',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'select_limit', array(
			'type'        => 'text',
			'title'       => __( 'Set Limit for DB Query [Deprecated]', 'smart-woocommerce-search' ),
			'description' => __( 'Set number of posts that should be retrieved from the database. This is advanced option', 'smart-woocommerce-search' ),
			'value'       => '',
			'is_pro'      => true,
		));
		?>
		</tbody>
	</table>
</div>