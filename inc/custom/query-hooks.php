<?php
namespace YSM\Query;

add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\disallowed_product_cats' );
add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\allowed_product_cats' );
add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\search_exclude' );
add_filter( 'smart_search_query_where', __NAMESPACE__ . '\\check_variations_parent' );
add_filter( 'smart_search_query_join', __NAMESPACE__ . '\\disallowed_product_cats_join' );
add_filter( 'smart_search_query_join', __NAMESPACE__ . '\\terms_join' );

/**
 * Terms join
 * @param $join
 * @return mixed
 */
function terms_join( $join ) {
	global $wpdb;

	if (
		defined( 'POLYLANG_BASENAME' ) ||
		\Ysm_Search::get_var( 'taxonomies' ) ||
		\Ysm_Search::get_var( 'allowed_product_cat' ) ||
		\Ysm_Search::get_var( 'disallowed_product_cat' )
	) {
		$join['t_rel'] = "LEFT JOIN {$wpdb->term_relationships} t_rel ON p.ID = t_rel.object_id";
		$join['t_tax'] = "LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_tax.term_taxonomy_id = t_rel.term_taxonomy_id";
		$join['t'] = "LEFT JOIN {$wpdb->terms} t ON t_tax.term_id = t.term_id";
	}

	return $join;
}
/**
 * Disallowed Product Categories
 * @param $join
 * @return mixed
 */
function disallowed_product_cats_join( $join ) {
	global $wpdb;

	if ( ! \Ysm_Search::get_post_types( 'product' ) || ! class_exists( 'WooCommerce' ) ) {
		return $join;
	}

	if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
		$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
	}

	return $join;
}

/**
 * Disallowed Product Categories
 * @param $where
 * @return mixed
 */
function disallowed_product_cats( $where ) {
	global $wpdb;

	if ( ! \Ysm_Search::get_post_types( 'product' ) || ! class_exists( 'WooCommerce' ) ) {
		return $where;
	}

	if ( \Ysm_Search::get_var( 'disallowed_product_cat' ) ) {
		$disallowed_product_cats_filtered = array();
		foreach ( \Ysm_Search::get_var( 'disallowed_product_cat' ) as $disallowed_product_cat ) {
			$disallowed_product_cats_filtered[] = "'" . $disallowed_product_cat . "'";
			$children_terms = get_term_children( $disallowed_product_cat, 'product_cat' );
			if ( ! is_wp_error( $children_terms ) && is_array( $children_terms ) && $children_terms ) {
				foreach ( $children_terms as $children_term ) {
					$disallowed_product_cats_filtered[] = "'" . intval( $children_term ) . "'";
				}
			}
		}
	}

	$exclude_terms = array();
	$wc_product_visibility_term_ids = wc_get_product_visibility_term_ids();
	if ( $wc_product_visibility_term_ids['exclude-from-search'] ) {
		$exclude_terms[] = "'" . $wc_product_visibility_term_ids['exclude-from-search'] . "'";
	}
	if ( ! empty( $disallowed_product_cats_filtered ) ) {
		$exclude_terms = array_merge( $exclude_terms, $disallowed_product_cats_filtered );
	}

	if ( $exclude_terms ) {
		$where['and'][] = sprintf( "p.ID NOT IN (
							SELECT DISTINCT object_id
							FROM {$wpdb->term_relationships} t_rel
							LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
							WHERE t_tax.term_id IN (%s)
						)", implode( ',', $exclude_terms ) );

		$where['and'][] = sprintf( "( p.post_type NOT IN ('product_variation') OR 
							( p.post_type = 'product_variation' AND p.post_parent NOT IN (
								SELECT DISTINCT object_id
								FROM {$wpdb->term_relationships} t_rel
								LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
								WHERE t_tax.term_id IN (%s)
							) ) )", implode( ",", $exclude_terms ) );
	}

	return $where;
}

/**
 * Allowed Product Categories
 * restrict searching only in defined categories
 * @param $where
 * @return mixed
 */
function allowed_product_cats( $where ) {
	global $wpdb;

	if ( ! \Ysm_Search::get_post_types( 'product' ) || ! class_exists( 'WooCommerce' ) ) {
		return $where;
	}

	if ( \Ysm_Search::get_var( 'allowed_product_cat' ) ) {
		$allowed_product_cats_filtered = array();
		foreach ( \Ysm_Search::get_var( 'allowed_product_cat' ) as $allowed_product_cat ) {
			$allowed_product_cats_filtered[] = "'" . $allowed_product_cat . "'";
		}

		if ( ! empty( $allowed_product_cats_filtered ) ) {
			$allowed_product_cats_filtered = implode( ',', $allowed_product_cats_filtered );
			$where['and'][] = sprintf( "( p.post_type NOT IN ('product') OR 
													( p.post_type = 'product' AND p.ID IN (
							SELECT DISTINCT object_id
							FROM {$wpdb->term_relationships} t_rel
							LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
							WHERE t_tax.term_id IN (%s)
						) ) )", $allowed_product_cats_filtered );
			// product variations
			$where['and'][] = sprintf( "( p.post_type NOT IN ('product_variation') OR 
													( p.post_type = 'product_variation' AND p.post_parent IN (
						SELECT DISTINCT object_id
						FROM {$wpdb->term_relationships} t_rel
						LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
						WHERE t_tax.term_id IN (%s)
					) ) )", $allowed_product_cats_filtered );
		}
	}

	return $where;
}

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
 * @return mixed
 */
function check_variations_parent( $where ) {
	if ( \Ysm_Search::get_post_types( 'product_variation' ) ) {
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
