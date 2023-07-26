<?php
namespace YSWS\Elements;

/**
 * Element "Product SKU" html
 * @param \WC_Product $product WooCommerce product object
 * @return string
 */
function product_sku( $product ) {
	if ( ! \Ysm_Search::get_var( 'display_sku' ) ) {
		return '';
	}

	return '<div class="smart-search-post-sku">' . esc_html( $product->get_sku() ) . '</div>';
}
