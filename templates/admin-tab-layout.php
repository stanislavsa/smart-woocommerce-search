<div id="layout_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup Elements', 'smart-woocommerce-search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'popup_height', array(
			'type'        => 'text',
			'title'       => __( 'Popup Max Height, px', 'smart-woocommerce-search' ),
			'description' => __( 'Popup max height in pixels. Default is 400px', 'smart-woocommerce-search' ),
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
			'title'       => __( 'Variation Image Fallback', 'smart-woocommerce-search' ),
			'description' => __( 'Use the parent product image if a variation has no image', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'popup_thumb_size', array(
			'type'        => 'text',
			'title'       => __( 'Image Size, px', 'smart-woocommerce-search' ),
			'description' => __( 'Image size in pixels', 'smart-woocommerce-search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'display_excerpt', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Excerpt', 'smart-woocommerce-search' ),
			'description' => __( 'Display product description', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'popup_desc_pos', array(
			'type'        => 'select',
			'title'       => __( 'Excerpt Position', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => '',
			'choices'     => array(
				'below_image' => __( 'Below image', 'smart-woocommerce-search' ),
				'below_title' => __( 'Below title', 'smart-woocommerce-search' ),
				'below_price' => __( 'Below price and SKU', 'smart-woocommerce-search' ),
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
			'description' => __( 'Indicate if a product is out of stock', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'display_sale_label', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display "Sale" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Highlight products on sale', 'smart-woocommerce-search' ),
			'value'       => 1,
			'is_pro'      => true,
		));

		ysm_setting( $w_id, 'display_featured_label', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display "Featured" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Mark featured products', 'smart-woocommerce-search' ),
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

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/layout-settings/" target="_blank">Documentation</a>
</div>