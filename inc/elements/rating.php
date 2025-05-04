<?php
namespace YSWS\Elements;

/**
 * Element "Rating" html
 *
 * @param \WC_Product $product WooCommerce product object
 *
 * @return string
 */
function rating( $product ): string {
	if ( ! \Ysm_Search::get_var( 'display_rating' ) ) {
		return '';
	}

	$output = '';

	if ($product) {
		$average_rating = $product->get_average_rating();
		$rating_count = $product->get_rating_count();

		if ($rating_count > 0) {
			$output .= '<div class="sws-product-rating" style="display: flex; gap: 2px; float: left;">';
			for ($i = 1; $i <= 5; $i++) {
				if ($average_rating >= $i) {
					$output .= '<span style="color: #ffc107; font-size: 20px;">★</span>';
				} elseif ($average_rating >= ($i - 0.5)) {
					$output .= '<span style="color: #ffc107; font-size: 20px;">☆</span>';
				} else {
					$output .= '<span style="color: #ddd; font-size: 20px;">☆</span>';
				}
			}
			$output .= '</div>';
		} else {
			$output .= '';
		}
	}

	return $output;
}
