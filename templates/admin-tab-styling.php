<div id="styles_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) { ?>

			<th class="ymapp-settings__title"><?php esc_html_e( 'Input Field', 'smart-woocommerce-search' ); ?></th>

			<?php
			ysm_setting( $w_id, 'input_height', array(
				'type'        => 'text',
				'title'       => __( 'Search Field Height, px', 'smart-woocommerce-search' ),
				'description' => __( 'Set the search field height in pixels', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_round_border', array(
				'type'        => 'checkbox',
				'title'       => __( 'Rounded Border', 'smart-woocommerce-search' ),
				'description' => __( 'Display search field with a rounded border (frame)', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_border_color', array(
				'type'        => 'color',
				'title'       => __( 'Border Color', 'smart-woocommerce-search' ),
				'description' => '',
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_border_width', array(
				'type'        => 'text',
				'title'       => __( 'Border Width, px', 'smart-woocommerce-search' ),
				'description' => __( 'Border (frame) width in pixels', 'smart-woocommerce-search' ),
				'value'       => '1',
			));

			ysm_setting( $w_id, 'input_text_color', array(
				'type'        => 'color',
				'title'       => __( 'Text Color', 'smart-woocommerce-search' ),
				'description' => '',
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_bg_color', array(
				'type'        => 'color',
				'title'       => __( 'Background Color', 'smart-woocommerce-search' ),
				'description' => '',
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_icon_color', array(
				'type'        => 'color',
				'title'       => __( 'Icon Color', 'smart-woocommerce-search' ),
				'description' => '',
				'value'       => '',
			));

			ysm_setting( $w_id, 'input_icon_bg', array(
				'type'        => 'color',
				'title'       => __( 'Icon Background', 'smart-woocommerce-search' ),
				'description' => '',
				'value' => '',
				'is_pro'      => true,
			));

			$cur_loader = ysm_get_option( $w_id, 'loader' );
			if ( is_array( $cur_loader ) ) {
				$cur_loader = $cur_loader[0];
			}
			ysm_setting( $w_id, 'loader', array(
				'type'        => 'select',
				'title'       => __( 'Loader', 'smart-woocommerce-search' ),
				'description' => __( 'Select loader\'s image', 'smart-woocommerce-search' ) .
				                 ' <img style="margin-left:20px;" class="ysm-loader-preview" src="' .
				                 SWS_PLUGIN_URI . 'assets/images/' .
				                 ( $cur_loader ? $cur_loader : 'loader1' ) .
				                 '.gif">',
				'value' => 'loader1',
				'choices' => array(
					'loader1' => __( 'Loader', 'smart-woocommerce-search' ) . ' 1',
					'loader2' => __( 'Loader', 'smart-woocommerce-search' ) . ' 2',
					'loader3' => __( 'Loader', 'smart-woocommerce-search' ) . ' 3',
					'loader4' => __( 'Loader', 'smart-woocommerce-search' ) . ' 4',
					'loader5' => __( 'Loader', 'smart-woocommerce-search' ) . ' 5',
				),
				'is_pro'      => true,
			));
			?>

		<?php } ?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'popup_top_gap', array(
			'type'        => 'text',
			'title'       => __( 'Top Indentation', 'smart-woocommerce-search' ),
			'description' => __( 'Indentation between search bar and popup, px', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_round_border', array(
			'type'        => 'checkbox',
			'title'       => __( 'Rounded Border', 'smart-woocommerce-search' ),
			'description' => __( 'Display popup with a rounded border (frame)', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_border_color', array(
			'type'        => 'color',
			'title'       => __( 'Border Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_bg_color', array(
			'type'        => 'color',
			'title'       => __( 'Background Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_title_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Title\'s Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_desc_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Description\'s Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_text_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Button\'s Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Button\'s Background Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'category_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Category Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Product Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'popup_price_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Price Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_out_of_stock_label_text_color', array(
			'type'        => 'color',
			'title'       => __( '"Out of stock" Label Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_out_of_stock_label_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"Out of stock" Label Background Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_sale_label_text_color', array(
			'type'        => 'color',
			'title'       => __( '"Sale" Label Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_sale_label_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"Sale" Label Background Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_featured_label_text_color', array(
			'type'        => 'color',
			'title'       => __( '"Featured" Label Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_featured_label_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"Featured" Label Background Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'is_pro'      => true,
		));

		?>

		</tbody>
	</table>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/styling-settings/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>