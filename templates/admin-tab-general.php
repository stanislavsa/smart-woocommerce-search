<div id="general_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>
		<?php
		if ( 'default' === $w_id ) {
			ysm_setting( $w_id, 'enable_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable', 'smart-woocommerce-search' ),
				'description' => sprintf( __( 'Enhance the standard %s search widget with the %s features', 'smart-woocommerce-search' ), 'WordPress', 'Smart Search' ),
				'value'       => 1,
			));
		}

		if ( 'product' === $w_id ) {
			ysm_setting( $w_id, 'enable_product_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable', 'smart-woocommerce-search' ),
				'description' => sprintf( __( 'Enhance the standard %s search widget with the %s features', 'smart-woocommerce-search' ), 'WooCommerce', 'Smart Search' ),
				'value'       => 1,
			));
		}

		if ( 'avada' === $w_id ) {
			ysm_setting( $w_id, 'enable_avada_search', array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable', 'smart-woocommerce-search' ),
				'description' => sprintf( __( 'Enhance the standard %s search widget with the %s features', 'smart-woocommerce-search' ), 'Avada', 'Smart Search' ),
				'value'       => 1,
			));
		}
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Input Field', 'smart-woocommerce-search' ); ?></th>

		<?php
		if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) {
			ysm_setting( $w_id, 'placeholder', array(
				'type'        => 'text',
				'title'       => 'Input Placeholder',
				'description' => __( 'Placeholder text for the search field', 'smart-woocommerce-search' ),
				'value'       => __( 'Search', 'smart-woocommerce-search' ) . '&hellip;',
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
			'title'       => __( 'Description\'s Size', 'smart-woocommerce-search' ),
			'description' => __( 'Maximum number of characters in the description', 'smart-woocommerce-search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'no_results_text', array(
			'type'        => 'text',
			'title'       => __( 'Text of "No Results" notification', 'smart-woocommerce-search' ),
			'description' => __( 'Displays when no results are found', 'smart-woocommerce-search' ),
			'value'       => 'No Results',
		));

		ysm_setting( $w_id, 'view_all_link_text', array(
			'type'        => 'text',
			'title'       => __( 'Text of "View All" Button', 'smart-woocommerce-search' ),
			'description' => __( 'The button is only displayed if the field is not empty', 'smart-woocommerce-search' ),
			'value'       => 'View all',
		));

		ysm_setting( $w_id, 'view_all_link_target_blank', array(
			'type'        => 'checkbox',
			'title'       => __( 'New Tab when "View All" Button Clicked', 'smart-woocommerce-search' ),
			'description' => __( 'Open results in a new tab when "View All" button clicked. Adds target="_blank" attribute to the "View All" button', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'view_all_link_found_posts', array(
			'type'        => 'checkbox',
			'title'       => __( 'Number of Found Results in the "View All" Button', 'smart-woocommerce-search' ),
			'description' => __( 'Display total number of found results in the "View All" button', 'smart-woocommerce-search' ),
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

		<th class="ymapp-settings__title"><?php echo esc_html__( 'Search Engine Features', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'enable_fuzzy_search', array(
			'type'        => 'select',
			'title'       => __( 'Multiple Word Search', 'smart-woocommerce-search' ),
			'description' => __( 'Select how to handle multiple word search', 'smart-woocommerce-search' ),
			'value'       => '',
			'choices'     => array(
				'0' => __( 'Only exact match', 'smart-woocommerce-search' ),
				'1' => __( '"A" or "B"', 'smart-woocommerce-search' ),
				'2' => __( '"A" and "B"', 'smart-woocommerce-search' ),
			),
		));

		ysm_setting( $w_id, 'exclude_out_of_stock_products', array(
			'type'        => 'checkbox',
			'title'       => __( 'Exclude Out of Stock Products', 'smart-woocommerce-search' ),
			'description' => __( 'Exclude out of stock products and variations from results', 'smart-woocommerce-search' ),
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
			'title'       => __( 'Visibility of Products with Variations', 'smart-woocommerce-search' ),
			'description' => __( 'Select visibility of parent products and variations', 'smart-woocommerce-search' ),
			'choices'     => array(
				'all'        => __( 'Parent product with variations', 'smart-woocommerce-search' ),
				'parent'     => __( 'Only parent product', 'smart-woocommerce-search' ),
				'variations' => __( 'Only variations', 'smart-woocommerce-search' ),
			),
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
			'description' => __( 'Prevent WooCommerce from automatically redirecting to a product page when there is only one search result.', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'search_page_suppress_filters', array(
			'type'        => 'checkbox',
			'title'       => __( 'Suppress Database Query Altering', 'smart-woocommerce-search' ),
			'description' => __( 'Prevent the theme or plugins from modifying the database query on the search results page', 'smart-woocommerce-search' ),
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
			'title'       => __( 'Disable WooCommerce Template on the Search Results Page', 'smart-woocommerce-search' ),
			'description' => __( "Displays the default search results template defined in the theme instead of WooCommerce template.<br>Useful if you want to display posts with products together", 'smart-woocommerce-search' ),
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
		ysm_setting( $w_id, 'product_slug', array(
			'type'        => 'text',
			'title'       => 'Product Slug',
			'description' => __( 'It may be helpful if you changed the base slug for WooCommerce products. The base slug is "product"', 'smart-woocommerce-search' ),
			'value'       => 'product',
			'is_pro'      => true,
		));
		?>

		<?php
		ysm_setting( $w_id, 'css_selectors', array(
			'type'        => 'textarea_list',
			'title'       => 'Custom CSS selectors',
			'description' => __( 'To enhance existing search bar with the Smart Search features.', 'smart-woocommerce-search' )
			                 . '<br>' .  __( 'Add a CSS selector (id or class) of the search bar\'s form element.', 'smart-woocommerce-search' )
			                 . '<br>' .  __( 'Fill in each selector on a new line.', 'smart-woocommerce-search' )
			                 . ' Eg. <code>form.header-search-bar</code>',
			'value'       => '',
		));
		?>

		<?php
		ysm_setting( $w_id, 'css_styles', array(
			'type'        => 'textarea_list',
			'title'       => 'Custom CSS styles',
			'description' =>  __( 'Add extra CSS styles', 'smart-woocommerce-search' ),
			'value'       => '',
		));
		?>
		</tbody>
	</table>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/general-settings/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>