<?php
namespace YSWS\Elements;

/**
 * Element "Excerpt" html
 * @param \WP_Post $cur_post
 * @return string
 */
function excerpt( $cur_post ) {
	if ( ! \Ysm_Search::get_var( 'display_excerpt' ) ) {
		return '';
	}

	$output = '';
	$post_excerpt = ! empty( $cur_post->post_excerpt ) ? $cur_post->post_excerpt : $cur_post->post_content;

	if ( $post_excerpt ) {
		if ( false !== strpos( $post_excerpt, '[et_pb_' ) ) {
			// Add DIVI shortcodes to remove them by strip_shortcodes()
			if ( ! shortcode_exists( 'et_pb_section' ) ) {
				add_shortcode( 'et_pb_section', '__return_false');
			}
			if ( ! shortcode_exists( 'et_pb_row' ) ) {
				add_shortcode( 'et_pb_row', '__return_false');
			}
			if ( ! shortcode_exists( 'et_pb_column' ) ) {
				add_shortcode( 'et_pb_column', '__return_false');
			}
		}

		$post_excerpt = wp_strip_all_tags( strip_shortcodes( $post_excerpt ) );
		$excerpt_symbols_count_max = \Ysm_Search::get_var( 'excerpt_symbols_count' ) ? (int) \Ysm_Search::get_var( 'excerpt_symbols_count' ) : 50;
		$excerpt_symbols_count = strlen( $post_excerpt );
		$post_excerpt = mb_substr( $post_excerpt, 0, $excerpt_symbols_count_max );

		if ( $excerpt_symbols_count > $excerpt_symbols_count_max ) {
			$post_excerpt .= ' ...';
		}

		$post_excerpt = ysm_text_replace( $post_excerpt );

		if ( $post_excerpt ) {
			$output = '<div class="smart-search-post-excerpt">' . wp_kses_post( $post_excerpt ) . '</div>';
		}
	}


	return $output;
}
