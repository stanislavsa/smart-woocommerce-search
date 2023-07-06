<div id="styles_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php if ( $w_id !== 'default' && $w_id !== 'product' ) { ?>

			<th class="ymapp-settings__title"><?php esc_html_e( 'Input Field', 'smart-woocommerce-search' ); ?></th>

			<?php
			ysm_setting( $w_id, 'input_round_border', array(
				'type'        => 'checkbox',
				'title'       => __( 'Rounded border', 'smart-woocommerce-search' ),
				'description' => __( 'Display search field with rounded border', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_border_color', array(
				'type'        => 'color',
				'title'       => __( 'Border Color', 'smart-woocommerce-search' ),
				'description' => __( 'Search field border color', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_border_width', array(
				'type'        => 'text',
				'title'       => __( 'Border Width, px', 'smart-woocommerce-search' ),
				'description' => __( 'Search field border width in pixels', 'smart-woocommerce-search' ),
				'value'       => '1',
			));

			ysm_setting( $w_id, 'input_text_color', array(
				'type'        => 'color',
				'title'       => __( 'Text Color', 'smart-woocommerce-search' ),
				'description' => __( 'Search field text color', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_bg_color', array(
				'type'        => 'color',
				'title'       => __( 'Background Color', 'smart-woocommerce-search' ),
				'description' => __( 'Search field background color', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_icon_color', array(
				'type'        => 'color',
				'title'       => __( 'Icon Color', 'smart-woocommerce-search' ),
				'description' => __( 'Search field icon color', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_icon_bg', array(
				'type'        => 'pro',
				'title'       => __( 'Icon Background', 'smart-woocommerce-search' ),
				'description' => __( 'Search field icon background', 'smart-woocommerce-search' ),
			));

			$cur_loader = ysm_get_option($w_id, 'loader');
			if ( is_array( $cur_loader ) ) {
				$cur_loader = $cur_loader[0];
			}
			ysm_setting( $w_id, 'loader', array(
				'type'        => 'pro',
				'title'       => __( 'Loader', 'smart-woocommerce-search' ),
				'description' => __( 'Select loader', 'smart-woocommerce-search' ),
			));
			?>

		<?php } ?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'popup_border_color', array(
			'type'        => 'color',
			'title'       => __( 'Border Color', 'smart-woocommerce-search' ),
			'description' => __( 'Popup border color', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_bg_color', array(
			'type'        => 'color',
			'title'       => __( 'Background Color', 'smart-woocommerce-search' ),
			'description' => __( 'Popup background color', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_title_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Title Text Color', 'smart-woocommerce-search' ),
			'description' => __( 'Title text color in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_desc_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Excerpt Text Color', 'smart-woocommerce-search' ),
			'description' => __( 'Excerpt text color in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_text_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Link Text Color', 'smart-woocommerce-search' ),
			'description' => __( '"View all" link text color in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Link Text Background', 'smart-woocommerce-search' ),
			'description' => __( '"View all" link text background in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'category_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Category Color', 'smart-woocommerce-search' ),
			'description' => __( 'Category text color in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Product Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'popup_price_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Price Color', 'smart-woocommerce-search' ),
			'description' => __( 'Price text color in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_out_of_stock_label_text_color', array(
			'type'        => 'pro',
			'title'       => __( '"Out of stock" Label Color', 'smart-woocommerce-search' ),
			'description' => __( '"Out of stock" label text color in results popup', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_out_of_stock_label_bg_color', array(
			'type'        => 'pro',
			'title'       => __( '"Out of stock" Label Background Color', 'smart-woocommerce-search' ),
			'description' => __( '"Out of stock" label background color in results popup', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_sale_label_text_color', array(
			'type'        => 'pro',
			'title'       => __( '"Sale" Label Color', 'smart-woocommerce-search' ),
			'description' => __( '"Sale" label text color in results popup', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_sale_label_bg_color', array(
			'type'        => 'pro',
			'title'       => __( '"Sale" Label Background Color', 'smart-woocommerce-search' ),
			'description' => __( '"Sale" label background color in results popup', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_featured_label_text_color', array(
			'type'        => 'pro',
			'title'       => __( '"Featured" Label Color', 'smart-woocommerce-search' ),
			'description' => __( '"Featured" label text color in results popup', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_featured_label_bg_color', array(
			'type'        => 'pro',
			'title'       => __( '"Featured" Label Background Color', 'smart-woocommerce-search' ),
			'description' => __( '"Featured" label background color in results popup', 'smart-woocommerce-search' ),
		));

		?>

		</tbody>
	</table>
</div>