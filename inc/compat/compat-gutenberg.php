<?php
namespace YSWS\Compat\Gutenberg;

add_filter( 'render_block', __NAMESPACE__ . '\\add_class_to_block', 10, 2 );

/**
 * Add class to block
 */
function add_class_to_block( $block_content, $block ) {
	if ( 'core/search' === $block['blockName'] ) {
		$block_content = new \WP_HTML_Tag_Processor( $block_content );
		$block_content->next_tag();
		if ( ! empty( $block['attrs']['query']['post_type'] ) && 'product' === $block['attrs']['query']['post_type'] ) {
			$block_content->add_class( 'sws-search-block-product' );
		} else {
			$block_content->add_class( 'sws-search-block-default' );
		}
		$block_content->get_updated_html();
	}

	return $block_content;
}
