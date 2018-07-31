<?php
if ( !defined('ABSPATH') ) exit;

wp_enqueue_script('postbox');

$tabs = array(
	'ysm_w_settings_general' => __('General', 'smart_search'),
	'ysm_w_settings_fields' => __('Items to Search through', 'smart_search'),
	'ysm_w_settings_styles' => __('Styling', 'smart_search'),
);
?>

<form method="post" action="" enctype="multipart/form-data">

	<?php if ($w_id !== 'product' && $w_id !== 'default') { ?>
	<p class="submit" style="float: right;">
		<input type="submit" value="<?php _e('Save', 'smart_search') ?>" name="save" class="button-primary" />
		<?php
		if ($w_id === 'product' || $w_id === 'default') {
			wp_nonce_field( 'smart_search_default' );
		} else {
			wp_nonce_field( 'smart_search_custom' );
		}
		?>
	</p>
	<?php } ?>

	<?php
	if ($w_id !== 'product' && $w_id !== 'default') {

		$w_title = !empty($w_id) ? $widgets[$w_id]['name'] : '';
		?>
		<div class="ysm-widget-edit-title-wrap">

			<input type="text" name="name" size="30" value="<?php echo esc_html($w_title) ?>" placeholder="<?php _e('Enter name', 'smart_search') ?>" autocomplete="off">

		</div>
		<?php
	}
	?>

	<div class="clear"></div>

	<div class="meta-box-sortables">

		<div class="postbox ysm-widget-edit-settings">

			<button type="button" class="handlediv button-link" aria-expanded="true">
				<span class="screen-reader-text"><?php _e('Toggle panel: Settings', 'smart_search') ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>

			<h2 class="hndle ui-sortable-handle"><span><?php _e('Settings', 'smart_search') ?></span></h2>

			<div class="inside">

				<h2 class="nav-tab-wrapper" id="ysm-widget-settings-nav-wrapper">
					<?php foreach ($tabs as $id => $title) { ?>
						<a href="#<?php echo esc_html($id) ?>" class="nav-tab<?php echo $id === 'ysm_w_settings_general' ? ' nav-tab-active' : '' ?>"><?php echo esc_html($title) ?></a>
					<?php } ?>
				</h2>

				<br>

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

						ysm_setting( $w_id, 'placeholder', array(
							'type' => 'text',
							'title' => __('Placeholder', 'smart_search'),
							'description' => __('Placeholder in search input', 'smart_search'),
							'value' => _x( 'Search &hellip;', 'placeholder', 'smart_search' ),
						));

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
							'description' => __('If not empty displays when any post match search query', 'smart_search'),
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
							'description' => __('Maximal number of symbols in excerpt', 'smart_search'),
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
							'type' => 'checkbox',
							'title' => __('Fuzzy Search', 'smart_search'),
							'description' => __('Enable multiple word search. May slow down the search request speed.', 'smart_search'),
							'value' => 0,
						));

						?>

						</tbody>
					</table>

				</div>

				<div id="ysm_w_settings_fields" class="ysm-widget-settings-tab" style="display: none">

					<table class="form-table">
						<tbody>

						<?php if ($w_id !== 'product') { ?>

						<th class="ysm-widget-settings-th"><?php _e('Post Types', 'smart_search') ?></th>

						<?php
						$post_types = get_post_types( array('public' => true) , 'object');

						$search_in = array(
							'post' => 1,
							'page' => 0,
							'product' => 0,
						);

						foreach ($post_types as $id => $post_type){

							if ( isset($search_in[$id]) ){

								ysm_setting( $w_id, 'post_type_' . $id, array(
									'type'  => 'checkbox',
									'title' => sprintf( __('Search in %s', 'smart_search'), $post_type->label ),
									'description' => sprintf( __('Enable search through "%s" post type', 'smart_search'), $post_type->labels->singular_name ),
									'value' => $search_in[$id],
								));

							}

						}
						?>

						<?php } ?>

						<th class="ysm-widget-settings-th"><?php _e('Product Variations', 'smart_search') ?></th>

						<?php
						ysm_setting( $w_id, 'post_type_product_variation', array(
							'type'  => 'checkbox',
							'title' => __('Search in Variations', 'smart_search'),
							'description' => __('Enable search through Variable Product Variations', 'smart_search'),
							'value' => 0,
						));
						?>

						<th class="ysm-widget-settings-th"><?php _e('Fields', 'smart_search') ?></th>

						<?php
						$fields = array(
							'title'   => __('Title', 'smart_search'),
							'content' => __('Content', 'smart_search'),
							'excerpt' => __('Excerpt', 'smart_search'),
							'tag' => __('Post Tag', 'smart_search'),
							'category' => __('Post Category', 'smart_search'),
							'product_tag' => __('Product Tag', 'smart_search'),
							'product_cat' => __('Product Category', 'smart_search'),
							'product_sku' => __('Product SKU', 'smart_search'),
						);

						if ($w_id === 'product') {
							unset($fields['tag']);
							unset($fields['category']);
						}

						$search_in = array(
							'title' => 1,
							'content' => 1,
							'excerpt' => 1,
						);

						foreach ($fields as $id => $field){

							ysm_setting( $w_id, 'field_' . $id, array(
								'type' => 'checkbox',
								'title' => sprintf( __('Search in %s', 'smart_search'), $field ),
								'description' => sprintf( __('Enable search through "%s"', 'smart_search'), $field ),
								'value' => isset($search_in[$id]) ? $search_in[$id] : '',
							));

						}

						?>

						<th class="ysm-widget-settings-th"><?php _e('Restrictions', 'smart_search') ?></th>

						<?php

						ysm_setting( $w_id, 'exclude_out_of_stock_products', array(
							'type' => 'checkbox',
							'title' => __('Exclude "Out of stock"', 'smart_search'),
							'description' => __('Exclude "Out of stock" products from results', 'smart_search'),
							'value' => 0,
						));

						ysm_setting( $w_id, 'allowed_product_cat', array(
							'type' => 'text',
							'title' => __('Allowed Product Categories', 'smart_search'),
							'description' => __('Product categories ids separated by comma.<br>Restrict product searching by chosen product categories', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'disallowed_product_cat', array(
							'type' => 'text',
							'title' => __('Disallowed Product Categories', 'smart_search'),
							'description' => __('Product categories ids separated by comma.<br>Do not search in chosen product categories', 'smart_search'),
							'value' => '',
						));

						?>

						</tbody>
					</table>

				</div>

				<div id="ysm_w_settings_styles" class="ysm-widget-settings-tab" style="display: none">

					<table class="form-table">
						<tbody>

						<?php if ($w_id !== 'default' && $w_id !== 'product') { ?>

							<th class="ysm-widget-settings-th"><?php _e('Input Field', 'smart_search') ?></th>

							<?php
							ysm_setting( $w_id, 'input_round_border', array(
								'type' => 'checkbox',
								'title' => __('Rounded border', 'smart_search'),
								'description' => __('Display search field with rounded border', 'smart_search'),
								'value' => '',
							));

							ysm_setting( $w_id, 'input_border_color', array(
								'type' => 'color',
								'title' => __('Border Color', 'smart_search'),
								'description' => __('Search field border color', 'smart_search'),
								'value' => '',
							));

							ysm_setting( $w_id, 'input_border_width', array(
								'type' => 'text',
								'title' => __('Border Width, px', 'smart_search'),
								'description' => __('Search field border width in pixels', 'smart_search'),
								'value' => '1',
							));

							ysm_setting( $w_id, 'input_text_color', array(
								'type' => 'color',
								'title' => __('Text Color', 'smart_search'),
								'description' => __('Search field text color', 'smart_search'),
								'value' => '',
							));

							ysm_setting( $w_id, 'input_icon_color', array(
								'type' => 'color',
								'title' => __('Icon Color', 'smart_search'),
								'description' => __('Search field icon color', 'smart_search'),
								'value' => '',
							));
							?>

						<?php } ?>

						<th class="ysm-widget-settings-th"><?php _e('Results Popup Elements', 'smart_search') ?></th>

						<?php
						ysm_setting( $w_id, 'popup_thumb_size', array(
							'type' => 'text',
							'title' => __('Image Size', 'smart_search'),
							'description' => __('Search results featured image size (px)', 'smart_search'),
							'value' => '50',
						));

						ysm_setting( $w_id, 'popup_border_color', array(
							'type' => 'color',
							'title' => __('Border Color', 'smart_search'),
							'description' => __('Popup border color', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_bg_color', array(
							'type' => 'color',
							'title' => __('Background Color', 'smart_search'),
							'description' => __('Popup background color', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_title_text_color', array(
							'type' => 'color',
							'title' => __('Title Text Color', 'smart_search'),
							'description' => __('Title text color in results popup', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_desc_text_color', array(
							'type' => 'color',
							'title' => __('Excerpt Text Color', 'smart_search'),
							'description' => __('Excerpt text color in results popup', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_desc_pos', array(
							'type' => 'select',
							'title' => __('Excerpt Position', 'smart_search'),
							'description' => __('Excerpt position in results popup', 'smart_search'),
							'value' => '',
							'choices' => array(
								'below_image' => __('Below image', 'smart_search'),
								'below_title' => __('Below title', 'smart_search'),
								'below_price' => __('Below price and SKU', 'smart_search'),
							),
						));

						ysm_setting( $w_id, 'popup_price_text_color', array(
							'type' => 'color',
							'title' => __('Price Color', 'smart_search'),
							'description' => __('Price text color in results popup', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_view_all_link_text_color', array(
							'type' => 'color',
							'title' => __('"View all" Link Text Color', 'smart_search'),
							'description' => __('"View all" link text color in results popup', 'smart_search'),
							'value' => '',
						));

						ysm_setting( $w_id, 'popup_view_all_link_bg_color', array(
							'type' => 'color',
							'title' => __('"View all" Link Text Background', 'smart_search'),
							'description' => __('"View all" link text background in results popup', 'smart_search'),
							'value' => '',
						));

						?>

						</tbody>
					</table>

				</div>

			</div>

		</div>

	</div>

	<p class="submit">
		<input type="submit" value="<?php _e('Save', 'smart_search') ?>" name="save" class="button-primary" />
		<?php
		if ($w_id === 'product' || $w_id === 'default') {
			wp_nonce_field( 'smart_search_default' );
		} else {
			wp_nonce_field( 'smart_search_custom' );
		}
		?>
	</p>

</form>
