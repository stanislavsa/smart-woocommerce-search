<?php
namespace YSWS\Compat\Gutenberg;

add_filter( 'render_block', __NAMESPACE__ . '\\add_class_to_block', 999, 2 );
add_filter( 'pre_render_block', __NAMESPACE__ . '\\update_block_query', 999999, 2 );
//add_filter( 'render_block_data', __NAMESPACE__ . '\\update_block_data', PHP_INT_MAX, 3 );

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

/**
 * Update block's query
 * @param $pre_render
 * @param $parsed_block
 *
 * @return mixed
 */
function update_block_query( $pre_render, $parsed_block ) {
	if ( ! isset( $_GET['s'] ) || ! isset( $_GET['search_id'] ) ) {
		return $pre_render;
	}

	if ( ! is_search() ) {
		return $pre_render;
	}

	if ( 'core/query' === $parsed_block['blockName'] ) {
		add_filter( 'query_loop_block_query_vars', __NAMESPACE__ . '\\build_query', 999999, 1 );
	} elseif ( 'woocommerce/product-collection' === $parsed_block['blockName'] ) {
		add_filter( 'query_loop_block_query_vars', __NAMESPACE__ . '\\build_product_collection', 999999, 1 );
	}

	return $pre_render;
}

/**
 * Update block's data
 */
function update_block_data( $parsed_block, $source_block, $parent_block ) {
	if ( ! isset( $_GET['s'] ) || ! isset( $_GET['search_id'] ) ) {
		return $parsed_block;
	}
	if ( ! is_search() ) {
		return $parsed_block;
	}
	if ( 'woocommerce/product-collection' === $parsed_block['blockName'] || 'core/query' === $parsed_block['blockName'] ) {
//		$parsed_block['attrs']['query']['inherit'] = 1;
	}

	return $parsed_block;
}

/**
 * Overwrite query params in the 'core/query' block
 * @param $query
 *
 * @return array
 */
function build_query( $query ) {
	if ( ! \Ysm_Search::$processed ) {
		return $query;
	}
	if ( empty( \Ysm_Search::$post_ids ) ) {
		return [];
	}
	return [
		'post_type'      => \Ysm_Search::get_post_types(),
		'post__in'       => \Ysm_Search::$post_ids,
		'post_status'    => 'publish',
		'posts_per_page' => $query['posts_per_page'],
	];
}

/**
 * Overwrite query params in the 'woocommerce/product-collection' block
 * @param $query
 *
 * @return array
 */
function build_product_collection( $query ) {
	if ( ! \Ysm_Search::$processed ) {
		return $query;
	}

	if ( empty( \Ysm_Search::$post_ids ) ) {
		return [];
	}

	$args = [];
	$orderby_param = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	if ( 'popularity' === $orderby_param ) {
		$args['meta_key'] = 'total_sales';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'DESC';

	} elseif ( 'rating' === $orderby_param ) {
		$args['meta_key'] = '_wc_average_rating';
		$args['order']    = 'ASC';
		$args['orderby']  = 'meta_value_num';

	} elseif ( 'price' === $orderby_param ) {
		$args['meta_key'] = '_price';
		$args['order']    = 'ASC';
		$args['orderby']  = 'meta_value_num';

	} elseif ( 'price-desc' === $orderby_param ) {
		$args['meta_key'] = '_price';
		$args['order']    = 'DESC';
		$args['orderby']  = 'meta_value_num';

	} elseif ( 'random' === $orderby_param ) {
		$args['orderby'] = 'rand';

	} elseif ( 'date' === $orderby_param ) {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';

	} else if ( 'title' === $orderby_param ){
		$args['orderby'] = 'title';
		$args['order']   = 'ASC';

	} else if ( 'title-desc' === $orderby_param ) {
		$args['orderby'] = 'title';
		$args['order']   = 'DESC';

	} else {
		$args['orderby'] = 'post__in';
		$args['order']   = 'ASC';
	}

	return wp_parse_args( $args, [
		'meta_query'     => [],
		'tax_query'      => [],
		'post__in'       => \Ysm_Search::$post_ids,
		'post_status'    => 'publish',
		'post_type'      => \Ysm_Search::get_post_types(),
		'posts_per_page' => $query['posts_per_page'],
		'paged'          => $query['paged'],
		'offset'         => $query['offset'],
		's'              => '',
		'order'          => $query['order'],
		'orderby'        => $query['orderby'],
	] );
}
