<div id="layout_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php

		ysm_setting( $w_id, 'display_icon', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Image', 'smart-woocommerce-search' ),
			'description' => __( 'Display featured image in results output', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'variation_thumb_fallback', array(
			'type'        => 'pro',
			'title'       => __( 'Variation Image Fallback', 'smart-woocommerce-search' ),
			'description' => __( 'Display featured image of the parent variable product if variation does not have a thumbnail', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_thumb_size', array(
			'type'        => 'text',
			'title'       => __( 'Image Size', 'smart-woocommerce-search' ),
			'description' => __( 'Search results featured image size (px)', 'smart-woocommerce-search' ),
			'value'       => '50',
		));

		ysm_setting( $w_id, 'display_excerpt', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Excerpt', 'smart-woocommerce-search' ),
			'description' => __( 'Display excerpt in results output', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'display_category', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Category', 'smart-woocommerce-search' ),
			'description' => __( 'Display product / post category', 'smart-woocommerce-search' ),
			'value'       => 0,
		));

		ysm_setting( $w_id, 'display_price', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display Price', 'smart-woocommerce-search' ),
			'description' => __( 'Display product price', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'display_sku', array(
			'type'        => 'checkbox',
			'title'       => __( 'Display SKU', 'smart-woocommerce-search' ),
			'description' => __( 'Display product SKU', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'display_out_of_stock_label', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Out of stock" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Out of stock" label if product is not in stock', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_sale_label', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Sale" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Sale" label for product', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_featured_label', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Featured" Label', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Featured" label for product', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'display_add_to_cart', array(
			'type'        => 'pro',
			'title'       => __( 'Display "Add to Cart" Button', 'smart-woocommerce-search' ),
			'description' => __( 'Display "Add to Cart" Button for product', 'smart-woocommerce-search' ),
		));

		ysm_setting( $w_id, 'popup_desc_pos', array(
			'type'        => 'select',
			'title'       => __( 'Excerpt Position', 'smart-woocommerce-search' ),
			'description' => __( 'Excerpt position in results popup', 'smart-woocommerce-search' ),
			'value'       => '',
			'choices'     => array(
				'below_image' => __( 'Below image', 'smart-woocommerce-search' ),
				'below_title' => __( 'Below title', 'smart-woocommerce-search' ),
				'below_price' => __( 'Below price and SKU', 'smart-woocommerce-search' ),
			),
		));

		?>

		</tbody>
	</table>
</div>