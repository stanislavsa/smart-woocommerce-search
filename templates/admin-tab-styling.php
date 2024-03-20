<div id="styles_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) { ?>

			<th class="ymapp-settings__title"><?php esc_html_e( 'Input Field', 'smart-woocommerce-search' ); ?></th>

			<?php
			ysm_setting( $w_id, 'input_round_border', array(
				'type'        => 'checkbox',
				'title'       => __( 'Rounded Border', 'smart-woocommerce-search' ),
				'description' => __( 'Display search field with rounded border', 'smart-woocommerce-search' ),
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
				'description' => __( 'Border width in pixels', 'smart-woocommerce-search' ),
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
				'description' => __( 'Select loader', 'smart-woocommerce-search' ) .
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

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
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
			'title'       => __( 'Title Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_desc_text_color', array(
			'type'        => 'color',
			'title'       => __( 'Excerpt Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_text_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Link Text Color', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
		));

		ysm_setting( $w_id, 'popup_view_all_link_bg_color', array(
			'type'        => 'color',
			'title'       => __( '"View all" Link Background Color', 'smart-woocommerce-search' ),
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

		<th class="ymapp-settings__title"><?php esc_html_e( 'Product Elements in the Results Popup', 'smart-woocommerce-search' ); ?></th>

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
</div>