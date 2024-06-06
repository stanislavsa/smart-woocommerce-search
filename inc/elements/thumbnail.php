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

	$output = '';

	$has_thumbnail = has_post_thumbnail( $cur_post );
	$the_post = $cur_post;

	if ( $has_thumbnail ) {
		/**
		 * Pre define image html in the suggestion
		 *
		 * @param string $output Image html output.
		 * @param \WP_Post|\WC_Product $the_post WordPress post object or WooCommerce product object
		 *
		 * @since 2.5.0
		 */
		$pre_image_output = apply_filters( 'sws_suggestion_image_output', '', $the_post );
		if ( ! empty( $pre_image_output ) ) {
			$output = $pre_image_output;
		} else {
			$thumb_size = 'post-thumbnail';
			if ( \Ysm_Search::get_var( 'popup_thumb_media_size' ) ) {
				$selected_thumb_size = \Ysm_Search::get_var( 'popup_thumb_media_size' );
				if ( ! empty( $selected_thumb_size[0] ) ) {
					$thumb_size = $selected_thumb_size[0];
				}
			}
			$output = get_the_post_thumbnail(
				$the_post,
				/**
				 * Overwrite image size in the suggestion
				 * eg. 'post-thumbnail' or 'medium'
				 *
				 * @since 2.5.0
				 */
				apply_filters( 'sws_suggestion_image_size', $thumb_size, $the_post ),
				/**
				 * Overwrite image attributes in the suggestion
				 * eg. 'post-thumbnail' or 'medium'
				 *
				 * @since 2.5.0
				 */
				apply_filters( 'sws_suggestion_image_attributes', [], $the_post )
			);
		}
	}

	if ( ! empty( $output ) ) {
		$output = '<div class="smart-search-post-icon">' . $output . '</div>';
	}

	return $output;
}
