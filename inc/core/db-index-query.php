<?php
namespace YSWS\Core\DB_Index_Query;

use function YSWS\Core\DB_Index\prepare_string_to_index;

function get_posts( $args = [] ) {
	global $wpdb;

	$post_ids = [];
	$defaults = [
		'search_terms'  => [],
		'max_count'     => 100,
		'offset'        => 0,
		'post_type'     => [],
		'order'         => '',
		'orderby'       => '',
		'lang'          => '',
		'relevance'     => [],
		'search_in'     => [
			'fields'        => [],
			'custom_fields' => [],
			'taxonomies'    => [],
		],
	];

	$args = wp_parse_args( $args, $defaults );

	$args['max_count'] = (int) $args['max_count'];

	if ( empty( $args['post_type'] ) || empty( $args['search_terms'] ) ) {
		return $post_ids;
	}
	if ( empty( $args['search_in']['fields'] ) && empty( $args['search_in']['custom_fields'] ) && empty( $args['search_in']['taxonomies'] ) ) {
		return $post_ids;
	}

	$args['post_type'] = (array) $args['post_type'];
	$args['search_terms'] = (array) $args['search_terms'];

	$where_parts = [
		'AND' => [],
		'OR'  => [],
	];
	$join = "LEFT JOIN {$wpdb->prefix}sws_post_fields pf ON pd.ID = pf.post_id ";
	$relevance_parts = [];

	$pt_list_sql = [];
	foreach ( $args['post_type'] as $pt ) {
		$pt_list_sql[] = "'" . esc_sql( $pt ) . "'";
	}
	$where_parts['AND'][] = "post_type IN (" . implode( ',', $pt_list_sql ) . ")";

	if ( $args['lang'] ) {
		$where_parts['AND'][] = "lang = '" . esc_sql( $args['lang'] ) . "'";
	}

	if ( ysws_get_var( 'exclude_out_of_stock_products' ) ) {
		$where_parts['AND'][] = "stock_status != 'outofstock'";
	}

	// allowed product categories
	if ( ysws_get_var( 'allowed_product_cat' ) && is_array( ysws_get_var( 'allowed_product_cat' ) ) ) {
		$allowed_product_cats = array_map( 'intval', ysws_get_var( 'allowed_product_cat' ) );
		$terms = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'include'    => array_unique( $allowed_product_cats ),
		] );

		if ( $terms && ! is_wp_error( $terms ) ) {
			$terms_names = wp_list_pluck( $terms, 'name' );
			$terms_names_filtered = [];
			foreach ( $terms_names as $terms_name ) {
				$terms_names_filtered[] = "'" . esc_sql( prepare_string_to_index( $terms_name ) ) . "'";
			}
			$join .= sprintf( "LEFT JOIN {$wpdb->prefix}sws_post_fields pf1 ON ( pd.ID = pf1.post_id AND pf1.type = 'taxonomy' AND pf1.name ='product_cat' AND pf1.value IN (%s)  ) ", implode( ',', $terms_names_filtered ) );
			$where_parts['AND'][] = "pf1.post_id IS NOT NULL";
		}
	}

	// disallowed Product Categories
	if ( ysws_get_var( 'disallowed_product_cat' ) && is_array( ysws_get_var( 'disallowed_product_cat' ) ) ) {
		$disallowed_product_cats = array_map( 'intval', ysws_get_var( 'disallowed_product_cat' ) );
		$terms = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'include'    => array_unique( $disallowed_product_cats ),
		] );

		if ( $terms && ! is_wp_error( $terms ) ) {
			$terms_names = wp_list_pluck( $terms, 'name' );
			$terms_names_filtered = [];
			foreach ( $terms_names as $terms_name ) {
				$terms_names_filtered[] = "'" . esc_sql( prepare_string_to_index( $terms_name ) ) . "'";
			}
			$join .= sprintf( "LEFT JOIN {$wpdb->prefix}sws_post_fields pf2 ON ( pd.ID = pf2.post_id AND pf2.type = 'taxonomy' AND pf2.name ='product_cat' AND pf2.value IN (%s)  ) ", implode( ',', $terms_names_filtered ) );
			$where_parts['AND'][] = "pf2.post_id IS NULL";
		}
	}

	// search terms lookup
	if ( '3' === ysws_get_var( 'enable_fuzzy_search' ) ) {
		foreach ( $args['search_terms'] as $search_term ) {
			$sub_where_parts = [];
			foreach ( $args['search_in']['fields'] as $field => $def_rel ) {
				$field = esc_sql( $field );

				$sub_where_parts[] = make_like_query(
					"( %s )",
					'pd.' . $field,
					[ $search_term ]
				);
			}
			foreach ( $args['search_in']['custom_fields'] as $field => $def_rel ) {
				$field = esc_sql( $field );

				$sub_where_parts[] = make_like_query(
					"( pf.name ='{$field}' AND pf.type = 'custom-field' AND ( %s ) )",
					'pf.value',
					[ $search_term ]
				);
			}
			foreach ( $args['search_in']['taxonomies'] as $field => $def_rel ) {
				$field = esc_sql( $field );

				$sub_where_parts[] = make_like_query(
					"( pf.name ='{$field}' AND pf.type = 'taxonomy' AND ( %s ) )",
					'pf.value',
					[ $search_term ]
				);
			}

			$where_parts['AND'][] = '(' . implode( ' OR ', $sub_where_parts ) . ')';
		}
	} else {
		foreach ( $args['search_in']['fields'] as $field => $def_rel ) {
			$field = esc_sql( $field );

			$where_parts['OR'][] = make_like_query(
				"( %s )",
				'pd.' . $field,
				$args['search_terms']
			);
		}
		foreach ( $args['search_in']['custom_fields'] as $field => $def_rel ) {
			$field = esc_sql( $field );

			$where_parts['OR'][] = make_like_query(
				"( pf.name ='{$field}' AND pf.type = 'custom-field' AND ( %s ) )",
				'pf.value',
				$args['search_terms']
			);
		}
		foreach ( $args['search_in']['taxonomies'] as $field => $def_rel ) {
			$field = esc_sql( $field );

			$where_parts['OR'][] = make_like_query(
				"( pf.name ='{$field}' AND pf.type = 'taxonomy' AND ( %s ) )",
				'pf.value',
				$args['search_terms']
			);
		}

		$where_parts['AND'][] = '(' . implode( ' OR ', $where_parts['OR'] ) . ')';
	}

	$orderby = $args['orderby'];
	if ( ! in_array( $orderby, [ 'post_title', 'post_date', 'post_modified', 'relevance' ], true ) ) {
		$orderby = 'relevance';
	}
	$order = strtoupper( $args['order'] );
	if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
		$order = 'DESC';
	}

	// relevance
	foreach ( $args['search_in']['fields'] as $field => $relevance ) {
		if ( $relevance ) {
			$field = esc_sql( $field );
			$relevance_parts[] = make_case_when_query(
				'pd.' . $field . ' LIKE %s',
				$relevance,
				$args['search_terms']
			);
		}
	}
	foreach ( $args['search_in']['custom_fields'] as $field => $relevance ) {
		if ( $relevance ) {
			$field = esc_sql( $field );
			$relevance_parts[] = make_case_when_query(
				"pf.name ='{$field}' AND pf.type = 'custom-field' AND pf.value LIKE %s",
				$relevance,
				$args['search_terms']
			);
		}
	}
	foreach ( $args['search_in']['taxonomies'] as $field => $relevance ) {
		if ( $relevance ) {
			$field = esc_sql( $field );
			$relevance_parts[] = make_case_when_query(
				"pf.name ='{$field}' AND pf.type = 'taxonomy' AND pf.value LIKE %s",
				$relevance,
				$args['search_terms']
			);
		}
	}

	$relevance = '';
	if ( $relevance_parts && $orderby === 'relevance' ) {
		$relevance .= ', MAX( ' . implode( ' + ', $relevance_parts ) . ' ) as relevance';
	}

	$sql = "SELECT pd.ID, pd.post_type, pd.post_parent $relevance FROM {$wpdb->prefix}sws_post_data pd ";
	$sql .= $join;
	$sql .= "WHERE " . implode( ' AND ', $where_parts['AND'] );
	$sql .= " GROUP BY pd.ID";
	$sql .= " ORDER BY {$orderby} {$order}";
	if ( -1 !== $args['max_count'] ) {
		$sql .= $wpdb->prepare( " LIMIT %d OFFSET %d", $args['max_count'], $args['offset'] );
	}

	$res = $wpdb->get_results( $sql );

	if ( $res ) {
		foreach ( $res as $obj ) {
			if ( $relevance && isset( $obj->relevance ) ) {
				if ( ! isset( $post_ids[ $obj->ID ] ) || intval( $post_ids[ $obj->ID ] ) < intval( $obj->relevance ) ) {
					$post_ids[ $obj->ID ] = $obj->relevance;
				}
			} else {
				$post_ids[ $obj->ID ] = 1;
			}
		}
	}

	$post_ids = array_unique( array_values( array_keys( $post_ids ) ) );
	$found_posts = count( $post_ids );

	if ( $post_ids ) {
		$count_sql = "SELECT COUNT(DISTINCT pd.ID)
              FROM {$wpdb->prefix}sws_post_data pd
              {$join}
              WHERE " . implode( ' AND ', $where_parts['AND'] );

		$found_posts = (int) $wpdb->get_var( $count_sql );
	}

	return [
		'post_ids'    => $post_ids,
		'found_posts' => $found_posts,
	];
}

function make_like_query( $query_pattern, $field, $search_terms = [] ) {
	global $wpdb;

	$query_parts = [];
	$field = esc_sql( $field );

	foreach ( $search_terms as $search_term ) {
		// @codingStandardsIgnoreStart - escaped previously
		$query_parts[] = $wpdb->prepare(
			$field . ' LIKE %s', '%' . trim( $search_term ) . '%'
		);
	}

	if ( '2' === ysws_get_var( 'enable_fuzzy_search' ) ) {
		$sub_query = implode( ' AND ', $query_parts );
	} else {
		$sub_query = implode( ' OR ', $query_parts );
	}

	return sprintf( $query_pattern, $sub_query );
}

function make_case_when_query( $query_pattern, $relevance, $search_terms = [] ) {
	global $wpdb;

	$query_parts = [];

	foreach ( $search_terms as $search_term ) {
		// @codingStandardsIgnoreStart - escaped previously
		$query_parts[] = sprintf( '( CASE WHEN (%s) THEN %d ELSE 0 END )',
			$wpdb->prepare(
				$query_pattern, '%' . trim( $search_term ) . '%'
			),
			(int) $relevance
		);
	}


	return implode( ' + ', $query_parts );
}
