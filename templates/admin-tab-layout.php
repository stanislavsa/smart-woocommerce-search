<div id="layout_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'columns', array(
			'type'        => 'select',
			'title'       => __( 'Columns Layout', 'smart-woocommerce-search' ),
			'description' =>  __( 'Set the maximum number of columns to show in the popup.<br>To choose a Grid layout instead of List layout select columns number more than 1', 'smart-woocommerce-search' ),
			'value'       => '',
			'is_pro'      => true,
			'choices'     => array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5,
			),
		));

		ysm_setting( $w_id, 'popup_height', array(
			'type'        => 'text',
			'title'       => __( 'Popup Max Height, px', 'smart-woocommerce-search' ),
			'description' => __( 'Popup max height in pixels. Default is 500px', 'smart-woocommerce-search' ),
			'value'       => 500,
		));

		ysm_setting( $w_id, 'popup_height_mobile', array(
			'type'        => 'text',
			'title'       => __( 'Popup Max Height (on mobile screen), px', 'smart-woocommerce-search' ),
			'description' => __( 'Popup max height in pixels for mobile screen (max-width: 768px). Default is 400px', 'smart-woocommerce-search' ),
			'value'       => 400,
		));

		ysm_setting( $w_id, 'display_icon', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Image', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => 1,
		));

		ysm_setting( $w_id, 'variation_thumb_fallback', array(
			'type'        => 'checkbox',
			'title'       => 'Variation\'s Image Fallback',
			'description' => __( 'Use the parent product image if a variation has no image', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		$image_sizes = get_intermediate_image_sizes();
		ysm_setting( $w_id, 'popup_thumb_media_size', array(
			'type'        => 'select',
			'title'       => __( 'Image Size', 'smart-woocommerce-search' ),
			'description' => __( 'Select image size', 'smart-woocommerce-search' ),
			'value'       => '',
			'choices'     => array_combine( $image_sizes, $image_sizes ),
		));

		ysm_setting( $w_id, 'popup_thumb_size', array(
			'type'        => 'text',
			'title'       => __( 'Image Max Width, px', 'smart-woocommerce-search' ),
			'description' => __( 'Limit image maximal width in pixels', 'smart-woocommerce-search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'display_excerpt', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Description', 'smart-woocommerce-search' ),
			'description' => __( 'Display product description', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'popup_desc_pos', array(
			'type'        => 'select',
			'title'       => __( 'Description\'s Location', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'choices'     => array(
				'below_image' => __( 'Under the image', 'smart-woocommerce-search' ),
				'below_title' => __( 'Under the title', 'smart-woocommerce-search' ),
				'below_price' => __( 'Under the price and SKU', 'smart-woocommerce-search' ),
			),
		));

		ysm_setting( $w_id, 'display_category', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Category', 'smart-woocommerce-search' ),
			'description' => __( 'Display product or post category', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'display_price', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Product Price', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => 1,
		));

		ysm_setting( $w_id, 'display_sku', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Product SKU', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => 1,
		));

		ysm_setting( $w_id, 'display_out_of_stock_label', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display "Out of stock" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Indicates if a product is out of stock', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'display_sale_label', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display "Sale" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Indicates if product has discount price', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'display_featured_label', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display "Featured" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Indicates if product is marked as special', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'display_add_to_cart', array(
			'type' => 'checkbox',
			'title' => __( 'Display "Add to Cart" Button', 'smart-woocommerce-search' ),
			'description' => __( 'Allow users to add products to their cart directly from the popup', 'smart-woocommerce-search' ),
			'value' => 0,
			'is_pro'      => true,
		));

		?>

		</tbody>
	</table>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/layout-settings/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>