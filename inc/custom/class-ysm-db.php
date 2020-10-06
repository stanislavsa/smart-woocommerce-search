<?php
/**
 * Class Ysm_DB
 * Generates arguments for WP_Query
 */
class Ysm_DB {
	/**
	 * Query params
	 * @var array
	 */
	private static $query_args = array();

	/**
	 * Relevance
	 * @var array
	 */
	private static $relevance = array();

	/**
	 * Indicator whether exclude fields from wp_posts or not
	 * @var bool
	 */
	private static $is_postmeta_only = false;

	/**
	 * Set initial arguments
	 * @param array $args
	 * @return void
	 */
	public static function init( $args = array() ) {
		self::$query_args = array();
		self::$relevance = array();
		self::$is_postmeta_only = false;

		$defaults = [
			'post_type'      => array( 'post' ),
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'posts_per_page' => 10,
			'suppress_filters' => false,
			'ignore_sticky_posts' => true,
		];

		self::$query_args = wp_parse_args( $args, $defaults );

		// SearchExclude
		if ( class_exists( 'SearchExclude' ) ) {
			$search_exclude = get_option( 'sep_exclude', array() );
			if ( ! empty( $search_exclude ) && is_array( $search_exclude ) ) {
				self::$query_args['post__not_in'] = array_map( 'absint', $search_exclude );
			}
		}

		if ( \Ysm_Search::get_post_types( 'product' ) && class_exists( 'WooCommerce' ) ) {
			// Exclude out_of_stock products
			if ( \Ysm_Search::get_var( 'exclude_out_of_stock_products' ) ) {
				self::add_meta_query( array(
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => '!=',
				) );
			}
		}
	}

	/**
	 * Do the query
	 * @return WP_Query
	 */
	public static function do_query() {
		add_filter( 'posts_fields', array( __CLASS__, 'filter_posts_fields' ), 9999 );
		add_filter( 'posts_join', array( __CLASS__, 'filter_posts_join' ), 9999 );
		add_filter( 'posts_where', array( __CLASS__, 'filter_posts_where' ), 9999 );
		add_filter( 'posts_orderby', array( __CLASS__, 'filter_posts_orderby' ), 9999 );
		add_filter( 'posts_groupby', array( __CLASS__, 'filter_posts_groupby' ), 9999 );

		$the_query = new \WP_Query( self::$query_args );

		remove_filter( 'posts_fields', array( __CLASS__, 'filter_posts_fields' ), 9999 );
		remove_filter( 'posts_join', array( __CLASS__, 'filter_posts_join' ), 9999 );
		remove_filter( 'posts_where', array( __CLASS__, 'filter_posts_where' ), 9999 );
		remove_filter( 'posts_orderby', array( __CLASS__, 'filter_posts_orderby' ), 9999 );
		remove_filter( 'posts_groupby', array( __CLASS__, 'filter_posts_groupby' ), 9999 );

		return $the_query;
	}

	/**
	 * Add meta query condition
	 * @param array $meta_query
	 */
	public static function add_meta_query( $meta_query = array() ) {
		if ( ! isset( self::$query_args['meta_query'] ) || ! is_array( self::$query_args['meta_query'] ) ) {
			self::$query_args['meta_query'] = array();
		}

		self::$query_args['meta_query'][] = $meta_query;
	}

	/**
	 * Add tax query condition
	 * @param array $tax_query
	 */
	public static function add_tax_query( $tax_query = array() ) {
		if ( ! isset( self::$query_args['tax_query'] ) || ! is_array( self::$query_args['tax_query'] ) ) {
			self::$query_args['tax_query'] = array();
		}

		self::$query_args['tax_query'][] = $tax_query;
	}

	/**
	 * Set relevance
	 * @param $relevance
	 */
	public static function set_relevance( $relevance ) {
		if (
			\Ysm_Search::get_var( 'field_title' ) ||
			\Ysm_Search::get_var( 'field_content' ) ||
			\Ysm_Search::get_var( 'field_excerpt' )
		) {
			self::$relevance = (array) $relevance;
		} else {
			self::$relevance = array();
		}
	}

	/**
	 * Set indicator whether exclude fields from wp_posts or not
	 * @param $val
	 */
	public static function is_postmeta_only( $val ) {
		self::$is_postmeta_only = (bool) $val;
	}

	/**
	 * Filter 'posts_join'
	 * @param $join
	 * @return mixed
	 *
	 */
	public static function filter_posts_join( $join ) {
		global $wpdb;

		$join_parts = array();

		if ( \Ysm_Search::get_var( 'taxonomies' ) && ! self::$is_postmeta_only ) {
			$join_parts['ysm_t_rel'] = "LEFT JOIN {$wpdb->term_relationships} ysm_t_rel ON {$wpdb->posts}.ID = ysm_t_rel.object_id";
			$join_parts['ysm_t_tax'] = "LEFT JOIN {$wpdb->term_taxonomy} ysm_t_tax ON ysm_t_tax.term_taxonomy_id = ysm_t_rel.term_taxonomy_id";
			$join_parts['ysm_t'] = "LEFT JOIN {$wpdb->terms} ysm_t ON ysm_t_tax.term_id = ysm_t.term_id";
		}

		if ( $join_parts ) {
			$join .= ' ' . implode( ' ', $join_parts ) . ' ';
		}

		return $join;
	}

	/**
	 * Filter 'posts_fields'
	 * @param $fields
	 * @return string
	 */
	public static function filter_posts_fields( $fields ) {
		global $wpdb;

		if ( self::$relevance ) {
			$relevance = array();

			if ( \Ysm_Search::get_var( 'field_title' ) ) {
				$relevance[ "{$wpdb->posts}.post_title" ] = 30;
			}

			if ( \Ysm_Search::get_var( 'field_content' ) ) {
				$relevance[ "{$wpdb->posts}.post_content" ] = 10;
			}

			if ( \Ysm_Search::get_var( 'field_excerpt' ) ) {
				$relevance[ "{$wpdb->posts}.post_excerpt" ] = 10;
			}

			if ( $relevance ) {
				$relevance_query = array();

				foreach ( $relevance as $k => $v ) {
					$relevance_query[] = '( CASE WHEN (' . self::make_like_query( "lower($k)" ) . ') THEN ' . (int) $v . ' ELSE 0 END )';
				}

				$fields .= ', ( ' . implode( ' + ', $relevance_query ) . ' ) as relevance';
			}
		}

		return $fields;
	}

	/**
	 * Filter 'posts_where'
	 * @param $where
	 * @return string
	 */
	public static function filter_posts_where( $where ) {
		global $wpdb;

		if ( false !== strpos( $where, 'ysm-meta-query-placeholder' ) ) {
			$where = preg_replace( '@(\S+)?\.meta_value LIKE \'({[^}]*})?ysm-meta-query-placeholder({[^}]*})?\'@', '( ' . self::make_like_query( 'lower($1.meta_value)' ) . ' ) ', $where );
		}

		// where OR
		$where_parts = array();

		if ( ! self::$is_postmeta_only ) {
			if ( \Ysm_Search::get_var( 'field_title' ) ) {
				$where_parts[] = self::make_like_query( "lower({$wpdb->posts}.post_title)" );
			}

			if ( \Ysm_Search::get_var( 'field_content' ) ) {
				$where_parts[] = self::make_like_query( "lower({$wpdb->posts}.post_content)" );
			}

			if ( \Ysm_Search::get_var( 'field_excerpt' ) ) {
				$where_parts[] = self::make_like_query( "lower({$wpdb->posts}.post_excerpt)" );
			}

			if ( \Ysm_Search::get_var( 'taxonomies' ) ) {
				$filtered_taxs = array();

				foreach ( \Ysm_Search::get_var( 'taxonomies' ) as $tax ) {
					$filtered_taxs[] = "'" . esc_sql( $tax ) . "'";
				}

				$where_parts[] = sprintf( '( ysm_t_tax.taxonomy IN (%s) AND (%s) )', implode( ',', $filtered_taxs ), self::make_like_query( 'lower(ysm_t.name)' ) );
			}
		}

		if ( $where_parts ) {
			$where .= ' AND (' . implode( ' OR ', $where_parts ) . ')';
		}

		// where AND
		$where_parts = array();

		if ( class_exists( 'WooCommerce' ) ) {
			// allowed product categories
			if ( \Ysm_Search::get_var( 'allowed_product_cat' ) && ( \Ysm_Search::get_post_types( 'product' ) || \Ysm_Search::get_post_types( 'product_variation' ) ) ) {
				$allowed_product_cats_filtered = array();
				foreach ( \Ysm_Search::get_var( 'allowed_product_cat' ) as $allowed_product_cat ) {
					$allowed_product_cats_filtered[] = "'" . esc_sql( $allowed_product_cat ) . "'";
				}
				$allowed_product_cats_filtered = implode( ',', $allowed_product_cats_filtered );

				if ( \Ysm_Search::get_post_types( 'product' ) ) {
					$where_parts[] = sprintf( "( {$wpdb->posts}.post_type NOT IN ('product') OR 
													( {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.ID IN (
							SELECT DISTINCT object_id
							FROM {$wpdb->term_relationships} t_rel
							LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
							WHERE t_tax.term_id IN (%s)
						) ) )", $allowed_product_cats_filtered );
				}
				if ( \Ysm_Search::get_post_types( 'product_variation' ) && 'parent' !== \Ysm_Search::get_var( 'product_variation_visibility' ) ) {
					// product variations
					$where_parts[] = sprintf( "( {$wpdb->posts}.post_type NOT IN ('product_variation') OR
													( {$wpdb->posts}.post_type = 'product_variation' AND {$wpdb->posts}.post_parent IN (
						SELECT DISTINCT object_id
						FROM {$wpdb->term_relationships} t_rel
						LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
						WHERE t_tax.term_id IN (%s)
					) ) )", $allowed_product_cats_filtered );
				}
			}

			// disallowed Product Categories
			if ( \Ysm_Search::get_post_types( 'product' ) || \Ysm_Search::get_post_types( 'product_variation' ) ) {
				$disallowed_product_cats = array();
				$wc_product_visibility_term_ids = wc_get_product_visibility_term_ids();
				if ( ! empty( $wc_product_visibility_term_ids['exclude-from-search'] ) ) {
					$disallowed_product_cats[] = "'" . esc_sql( $wc_product_visibility_term_ids['exclude-from-search'] ) . "'";
				}
				if ( \Ysm_Search::get_var( 'disallowed_product_cat' ) ) {
					foreach ( \Ysm_Search::get_var( 'disallowed_product_cat' ) as $disallowed_product_cat ) {
						$disallowed_product_cats[] = "'" . esc_sql( $disallowed_product_cat ) . "'";
					}
				}
				if ( $disallowed_product_cats ) {
					if ( \Ysm_Search::get_post_types( 'product' ) ) {
						$where_parts[] = sprintf( "( {$wpdb->posts}.post_type NOT IN ('product') OR 
							( {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.ID NOT IN (
								SELECT DISTINCT object_id
								FROM {$wpdb->term_relationships} t_rel
								LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
								WHERE t_tax.term_id IN (%s)
							) ) )", implode( ',', $disallowed_product_cats ) );
					}
					if ( \Ysm_Search::get_post_types( 'product_variation' ) && 'parent' !== \Ysm_Search::get_var( 'product_variation_visibility' ) ) {
						$where_parts[] = sprintf( "( {$wpdb->posts}.post_type NOT IN ('product_variation') OR 
							( {$wpdb->posts}.post_type = 'product_variation' AND {$wpdb->posts}.post_parent NOT IN (
								SELECT DISTINCT object_id
								FROM {$wpdb->term_relationships} t_rel
								LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
								WHERE t_tax.term_id IN (%s)
							) ) )", implode( ',', $disallowed_product_cats ) );
					}
				}
			}

			// product variation
			if ( \Ysm_Search::get_post_types( 'product_variation' ) ) {
				// is parent published
				if ( 'parent' !== \Ysm_Search::get_var( 'product_variation_visibility' ) ) {
					$where_parts[] = "( {$wpdb->posts}.post_type NOT IN ('product_variation') OR 
								( {$wpdb->posts}.post_type = 'product_variation' AND 'publish' = (
								SELECT pp.post_status
								FROM {$wpdb->posts} pp
								WHERE pp.ID = {$wpdb->posts}.post_parent
							) ) )";
				}
			}
		}

		if ( $where_parts ) {
			$where .= ' AND ' . implode( ' AND ', $where_parts );
		}

		return $where;
	}

	/**
	 * Filter 'posts_orderby'
	 * @param $orderby
	 * @return string
	 */
	public static function filter_posts_orderby( $orderby ) {
		if ( self::$relevance ) {
			$orderby = 'relevance DESC, ' . $orderby;
		}

		return $orderby;
	}

	/**
	 * Filter 'posts_groupby'
	 * @param $groupby
	 * @return string
	 */
	public static function filter_posts_groupby( $groupby ) {
		global $wpdb;
		$groupby = $wpdb->posts . '.ID';

		return $groupby;
	}

	/**
	 * Make like query
	 * @param $field
	 * @return string
	 */
	protected static function make_like_query( $field ) {
		global $wpdb;
		$query = array();

		foreach ( \Ysm_Search::get_search_terms() as $s_word ) {
			$query[] = $wpdb->prepare( esc_sql( $field ) . ' LIKE %s', '%' . trim( $s_word ) . '%' );
		}

		if ( '2' === \Ysm_Search::get_var( 'enable_fuzzy_search' ) ) {
			return implode( ' AND ', $query );
		}

		return implode( ' OR ', $query );
	}
}
