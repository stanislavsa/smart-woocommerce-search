<?php
namespace YSWS\Elements;

/**
 * Element "Thumbnail" html
 * @param \WP_Post $cur_post
 * @return string
 */
function thumbnail( $cur_post ) {
	if ( ! \Ysm_Search::get_var( 'display_icon' ) ) {
		return '';
	}

	$output   = '';
	$the_post = $cur_post;

	if ( has_post_thumbnail( $cur_post ) ) {
		/**
		 * Overwrite image html in the suggestion
		 *
		 * @param $the_post \WP_Post|\WC_Product WordPress post object or WooCommerce product object
		 *
		 * @since 2.5.0
		 */
		$pre_image_output = apply_filters( 'ysm_suggestion_image_output', '', $the_post );
		if ( ! empty( $pre_image_output ) ) {
			$output = $pre_image_output;
		} else {
			$output = get_the_post_thumbnail(
				$the_post,
				/**
				 * Overwrite image size in the suggestion
				 * eg. 'post-thumbnail' or 'medium'
				 *
				 * @since 2.5.0
				 */
				apply_filters( 'ysm_suggestion_image_size',
					// compatibility with old version
					apply_filters( 'smart_search_suggestions_image_size', 'post-thumbnail' )
				),
				/**
				 * Overwrite image attributes in the suggestion
				 * eg. 'post-thumbnail' or 'medium'
				 *
				 * @since 2.5.0
				 */
				apply_filters( 'ysm_suggestion_image_attributes',
					// compatibility with old version
					apply_filters( 'smart_search_suggestions_image_attributes', array() )
				)
			);
		}
	}

	if ( ! empty( $output ) ) {
		$output = '<div class="smart-search-post-icon">' . $output . '</div>';
	}

	return $output;
}
