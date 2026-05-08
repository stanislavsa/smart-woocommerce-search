<?php
namespace YSWS\Elements;

/**
 * Element "Product SKU" html
 * @param \WC_Product $product WooCommerce product object
 * @return string
 */
function product_sku( $product ) {
	if ( ! ysws_get_var( 'display_sku' ) ) {
		return '';
	}

	$label = ysws_get_var( 'sku_label' );
	// fallback if widget settings not saved yet
	if ( $label === null ) {
		$label = 'SKU';
	}
	if ( $label ) {
		$label .= ': ';
	}

	return '<div class="smart-search-post-sku">' . esc_html($label) . esc_html( $product->get_sku() ) . '</div>';
}
