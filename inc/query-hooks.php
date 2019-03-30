<?php
namespace YSM\Query;

add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\search_exclude' );
add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\check_variations_parent', 10, 2 );

/**
 * Search exclude
 * @param $where
 * @return mixed
 */
function search_exclude( $where ) {
	if ( class_exists( 'SearchExclude' ) ) {
		$search_exclude = get_option( 'sep_exclude', array() );
		if ( ! empty( $search_exclude ) && is_array( $search_exclude ) ) {
			$search_exclude = implode( ',', $search_exclude );
			$where['and'][] = "p.ID NOT IN ({$search_exclude})";
		}
	}
	return $where;
}

/**
 * Product variations - is parent published
 * @param $where
 * @param $params
 * @return mixed
 */
function check_variations_parent( $where, $params ) {
	if ( ! empty( $params['post_type_product_variation'] ) ) {
		global $wpdb;
		$where['and'][] = "( p.post_type NOT IN ('product_variation') OR 
								( p.post_type = 'product_variation' AND 'publish' = (
								SELECT post_status
								FROM {$wpdb->posts}
								WHERE ID = p.post_parent
							) ) )";
	}
	return $where;
}
