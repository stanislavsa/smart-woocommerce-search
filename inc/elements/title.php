<?php
namespace YSWS\Elements;

/**
 * Element "Title" html
 * @param \WP_Post $cur_post
 * @return string
 */
function title( $cur_post ) {
	$post_title = get_the_title( $cur_post );
	$post_title = wp_strip_all_tags( $post_title );
	$post_title = ysm_text_replace( $post_title );

	return '<div class="smart-search-post-title">' . wp_kses_post( $post_title ) . '</div>';
}
