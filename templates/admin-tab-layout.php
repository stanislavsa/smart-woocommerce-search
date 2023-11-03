<div id="layout_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Results Popup Elements', 'smart-woocommerce-search' ); ?></th>

		<?php

		ysm_setting( $w_id, 'display_icon', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Image', 'smart-woocommerce-search' ),
			'description' => '',
			'value'       => 1,
		));

		ysm_setting( $w_id, 'variation_thumb_fallback', array(
			'type'        => 'pro',
			'title'       => __( 'Variation Image Fallback', 'smart-woocommerce-search' ),
			'description' => __( 'Fallback to parent product image if a variation does not have an image', 'smart-woocommerce-search' ),
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
			'description' => '',
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
			'description' => __( 'Display product / post category', 'smart-woocommerce-search' ),
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
			'type'        => 'pro',
			'title'       => __( 'Display "Out of stock" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Out of stock" label if product is out of stock', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_sale_label', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Sale" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Sale" label if product is on sale', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_featured_label', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Featured" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Featured" label if product marked as featured', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_add_to_cart', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Add to Cart" Button', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Add to Cart" button for product', 'smart-woocommerce-search' ),
		));

		?>

		</tbody>
	</table>
</div>