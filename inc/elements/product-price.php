<?php
namespace YSWS\Elements;

/**
 * Element "Product Price" html
 * @param \WC_Product $product WooCommerce product object
 * @return string
 */
function product_price( $product ) {
	if ( ! \Ysm_Search::get_var( 'display_price' ) ) {
		return '';
	}

	// @codingStandardsIgnoreStart
	$output = '<div class="smart-search-post-price">' . $product->get_price_html() . '</div>';
	// @codingStandardsIgnoreEnd

	return $output;
}
