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