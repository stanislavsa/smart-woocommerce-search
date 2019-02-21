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

			ysm_setting( $w_id, 'input_bg_color', array(
				'type' => 'color',
				'title' => __('Background Color', 'smart_search'),
				'description' => __('Search field background color', 'smart_search'),
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