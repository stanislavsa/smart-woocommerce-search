<?php
namespace YSWS\Elements;

/**
 * Element "Category" html
 * @param \WP_Post $cur_post
 * @return string
 */
function category( $cur_post ) {
	if ( ! \Ysm_Search::get_var( 'display_category' ) ) {
		return '';
	}

	$output = '';
	$category = '';

	if (
		class_exists( 'WooCommerce' )
		&& in_array( $cur_post->post_type, [ 'product', 'product_variation' ], true )
	) {
		$taxonomy = 'product_cat';
	} else {
		$taxonomy = 'category';
	}

	// get Yoast primary category
	if ( defined( 'WPSEO_VERSION' ) ) {
		$primary_term_id = (int) get_post_meta( $cur_post->ID, '_yoast_wpseo_primary_' . $taxonomy, true );

		if ( $primary_term_id ) {
			$primary_term = get_term_by( 'id', $primary_term_id, $taxonomy );

			if (
				$primary_term
				&& ! is_wp_error( $primary_term )
				&& $primary_term instanceof \WP_Term
			) {
				$category = $primary_term;
			}
		}
	}

	// get first category
	if ( ! $category ) {
		$terms = get_the_terms( $cur_post->ID, $taxonomy );
		if (
			! is_wp_error( $terms )
			&& ! empty( $terms[0] )
			&& $terms[0] instanceof \WP_Term
		) {
			$category = $terms[0];
		}
	}

	if ( $category ) {
		$output = '<div class="smart-search-post-category">' . esc_html( $category->name ). '</div>';
	}


	return $output;
}
