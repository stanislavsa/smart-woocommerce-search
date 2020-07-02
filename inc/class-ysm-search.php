<?php

/**
 * Class Ysm_Search
 * Retrieves posts from the database depending on settings
 */
class Ysm_Search {

	/**
	 * Current widget id
	 * @var int
	 */
	protected static $w_id = 0;
	/**
	 * Registered post types list
	 * @var array
	 */
	protected static $registered_pt = array();
	/**
	 * List of post types to search through
	 * @var array
	 */
	protected static $pt = array();
	/**
	 * List of post fields to search through (title, content, excerpt)
	 * @var array
	 */
	protected static $fields = array();
	/**
	 * List of terms to search through
	 * @var array
	 */
	protected static $terms = array();
	/**
	 * List of post meta fields to search through
	 * @var array
	 */
	protected static $postmeta = array();
	/**
	 * List of suggestions
	 * @var array
	 */
	protected static $suggestions = array();
	/**
	 * List of vars
	 * @var array
	 */
	protected static $vars = array();
	/**
	 * Limitation of search results
	 * @var int
	 */
	protected static $max_posts = 0;
	/**
	 * Minimal symbols count
	 * @var int
	 */
	protected static $min_symbols = 0;
	/**
	 * List of found post id's that satisfy search query
	 * @var array
	 */
	protected static $result_post_ids = array();

	/**
	 * Debug
	 * @var bool
	 */
	protected static $debug    = false;
	private static $time_start = 0;
	private static $time_end   = 0;

	/**
	 * Initial hooks
	 */
	public static function init() {
		self::$time_start = microtime( true );

		add_action( 'pre_get_posts', array( __CLASS__, 'search_filter' ), 9999 );
		add_action( 'woocommerce_product_query', array( __CLASS__, 'search_filter' ), 9999 );
		add_action( 'wp', array( __CLASS__, 'remove_search_filter' ), 9999 );

		add_filter( 'the_title', 'ysm_accent_search_term', 9999, 1 );
		add_filter( 'get_the_excerpt', 'ysm_accent_search_term', 9999, 1 );
		add_filter( 'the_content', 'ysm_accent_search_term', 9999, 1 );

		add_filter( 'smart_search_query_results', array( __CLASS__, 'query_results_filter' ), 10 );
	}

	/**
	 * Parse widget settings to define search behavior
	 */
	public static function parse_settings() {

		self::$registered_pt = array(
			'post',
			'page',
		);

		if ( class_exists( 'WooCommerce' ) ) {
			self::$registered_pt[] = 'product';
		}

		if (self::$w_id == 'product' || self::$w_id == 'default') {
			$widgets = ysm_get_default_widgets();
		} else {
			$widgets = ysm_get_custom_widgets();
		}

		$settings = $widgets[ self::$w_id ]['settings'];

		if (self::$w_id == 'product') {

			self::$pt[ 'product' ] = 'product';

		} else {

			foreach (self::$registered_pt as $type){
				if ( ! empty($settings['post_type_'.$type]) ) {
					self::$pt[ $type ] = $type;
				}
			}

		}

		if ( ! empty( $settings['post_type_product_variation'] ) ) {
			self::$pt[ 'product_variation' ] = 'product_variation';
		}

		self::$max_posts = !empty( $settings['max_post_count'] ) ? $settings['max_post_count'] : 99;
		self::$min_symbols = !empty( $settings['char_count'] ) ? $settings['char_count'] : 3;

		/* fields to search through */
		if ( !empty( $settings['field_title'] ) ) {
			self::$fields['post_title'] = 1;
		}

		if ( !empty( $settings['field_content'] ) ) {
			self::$fields['post_content'] = 1;
		}

		if ( !empty( $settings['field_excerpt'] ) ) {
			self::$fields['post_excerpt'] = 1;
		}

		if ( !empty( $settings['allowed_product_cat'] ) ) {
			if ( ! is_array( $settings['allowed_product_cat'] ) ) {
				$settings['allowed_product_cat'] = explode( ',', $settings['allowed_product_cat'] );
			}
			self::$fields['allowed_product_cat'] = array();
			foreach ( $settings['allowed_product_cat'] as $dis_cat ) {
				$dis_cat = trim( $dis_cat );
				if ( $dis_cat ) {
					self::$fields['allowed_product_cat'][] = intval( $dis_cat );
				}
			}
		}

		if ( !empty( $settings['disallowed_product_cat'] ) ) {
			if ( ! is_array( $settings['disallowed_product_cat'] ) ) {
				$settings['disallowed_product_cat'] = explode( ',', $settings['disallowed_product_cat'] );
			}
			self::$fields['disallowed_product_cat'] = array();
			foreach ( $settings['disallowed_product_cat'] as $dis_cat ) {
				$dis_cat = trim( $dis_cat );
				if ( $dis_cat ) {
					self::$fields['disallowed_product_cat'][] = intval( $dis_cat );
				}
			}
		}

		if ( !empty( $settings['field_tag'] ) ) {
			self::$terms['post_tag'] = 'post_tag';
		}

		if ( !empty( $settings['field_category'] ) ) {
			self::$terms['category'] = 'category';
		}

		if ( !empty( $settings['field_product_tag'] ) ) {
			self::$terms['product_tag'] = 'product_tag';
		}

		if ( !empty( $settings['field_product_cat'] ) ) {
			self::$terms['product_cat'] = 'product_cat';
		}

		if ( !empty( $settings['field_product_sku'] ) ) {
			self::$postmeta['_sku'] = '_sku';
		}

		/* output items to display */

		if ( ! empty( $settings['enable_fuzzy_search'] ) && is_array( $settings['enable_fuzzy_search'] ) ) {
			$settings['enable_fuzzy_search'] = $settings['enable_fuzzy_search'][0];
		}

		if ( ! empty( $settings['popup_desc_pos'] ) ) {
			if ( is_array( $settings['popup_desc_pos'] ) ) {
				$settings['popup_desc_pos'] = $settings['popup_desc_pos'][0];
			}
		} else {
			$settings['popup_desc_pos'] = 'below_image';
		}

		self::$vars = array_merge( self::$vars, $settings );
	}

	/**
	 * Hook for changing posts set on search results page
	 * @param $query
	 * @return mixed
	 */
	public static function search_filter( $query ) {
		$s = ysm_get_s();

		if ( $query->is_main_query() && ! is_admin() && ! empty( $query->query_vars['s'] ) && ! empty( $s ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			$w_id = ! empty( $_GET['search_id'] ) ? sanitize_text_field( $_GET['search_id'] ) : 0;
			if ( ! in_array( $w_id, array( 'default', 'product' ), true ) ) {
				$w_id = (int) $w_id;
			}

			if ( $w_id ) {
				self::$w_id = $w_id;
				$wp_posts = array();

				self::parse_settings();

				if ( self::get_var( 'search_page_default_output' ) ) {
					return $query;
				}

				if ( ! self::get_post_types() ) {
					return $query;
				}

				self::$max_posts = '-1';

				self::set_s( $s );
				$posts = self::search_posts();

				foreach ( $posts as $post ) {
					$wp_posts[] = $post->ID;
				}

				if ( empty( $wp_posts ) ) {
					$wp_posts[] = 0;
				}

				self::$result_post_ids = $wp_posts;
				$query->set( 's', '' );
				$query->set( 'post__in', $wp_posts );

				if ( empty( $_GET['product_orderby'] ) ) {
					$orderby = $query->get( 'orderby' );
					if ( 'relevance' === $orderby ) {
						$query->set( 'orderby', 'post__in' );
					}
				}
			}
		}
	}

	/**
	 * Remove hook that change posts set on search results page
	 */
	public static function remove_search_filter() {
		if ( ! is_admin() &&  ! empty( $_GET['s'] ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			remove_action( 'pre_get_posts', array( __CLASS__, 'search_filter' ), 9999 );
		}
	}

	/**
	 * Generate a main query to retrieve posts from database
	 * @return array|null|object
	 */
	public static function search_posts() {
		global $wpdb;

		self::set_search_terms();

		/* LIMIT */
		$limit = empty( self::$max_posts ) ? 10 : self::$max_posts;

		$fields_list = array_merge( self::$fields, self::$terms );
		unset( $fields_list['disallowed_product_cat'] );
		unset( $fields_list['allowed_product_cat'] );

		if ( ! empty( $fields_list ) ) {

			/* SELECT part */
			$select = array();
			$select[] = "DISTINCT p.ID";
			$select[] = "p.post_title";
			$select[] = "p.post_content";
			$select[] = "p.post_excerpt";
			$select[] = "p.post_type";

			/* JOIN part */
			$join = array();

			/* WHERE part */
			$where = array(
				'and' => array(),
				'or' => array(),
			);

			$where['and'][] = "p.post_status = 'publish'";

			$s_post_types = array();

			foreach ( self::get_post_types() as $type ) {
				$s_post_types[] = "'" . esc_sql( $type ) . "'";
			}

			$s_post_types = implode( ',', $s_post_types );
			$where['and'][] = "p.post_type IN ({$s_post_types})";

			/* relevance part */
			$relevance = array();

			/* GROUP BY part */
			$groupby = "p.ID";

			/* ORDER BY part */
			$orderby = array();

			/* filters */

			if ( !empty(self::$fields['post_title']) ) {
				$where['or'][] = self::make_like_query( 'lower(p.post_title)' );
				$relevance['p.post_title'] = 30;
			}

			if ( !empty(self::$fields['post_content']) ) {
				$where['or'][] = self::make_like_query( 'lower(p.post_content)' );
				$relevance['p.post_content'] = 10;
			}

			if ( !empty(self::$fields['post_excerpt']) ) {
				$where['or'][] = self::make_like_query( 'lower(p.post_excerpt)' );
				$relevance['p.post_excerpt'] = 10;
			}

			/* tags and categories */
			if ( !empty( self::$terms ) ) {
				$s_terms = array();

				foreach (self::$terms as $term){
					$s_terms[] = "'" . $term . "'";
				}

				$s_terms = implode(',', $s_terms);

				$where['or'][] = "( t_tax.taxonomy IN ({$s_terms}) AND (" . self::make_like_query( 'lower(t.name)' ) . ") )";
			}

			/* product visibility */
			if ( self::get_post_types( 'product' ) ) {

				if ( !empty( self::$fields['disallowed_product_cat'] ) ) {
					$disallowed_product_cats_filtered = array();
					foreach ( self::$fields['disallowed_product_cat'] as $disallowed_product_cat ) {
						$disallowed_product_cats_filtered[] = "'" . $disallowed_product_cat . "'";
						$children_terms = get_term_children( $disallowed_product_cat, 'product_cat' );
						if ( ! is_wp_error( $children_terms ) && is_array( $children_terms ) && $children_terms ) {
							foreach ( $children_terms as $children_term ) {
								$disallowed_product_cats_filtered[] = "'" . intval( $children_term ) . "'";
							}
						}
					}
				}

				if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
					$where['and'][] = sprintf( "p.ID NOT IN (
						SELECT DISTINCT t_rel.object_id
						FROM {$wpdb->term_relationships} t_rel
						LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
						WHERE t_tax.term_id IN (%s)
					)", implode( ",", $disallowed_product_cats_filtered ) );
					$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
					$where['and'][] = "( p.post_type NOT IN ('product') OR (p.post_type = 'product' AND pmpv.meta_key = '_visibility' AND CAST(pmpv.meta_value AS CHAR) IN ('search','visible')) )";
				} else {
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
						)", implode( ",", $exclude_terms ) );

						$where['and'][] = sprintf( "( p.post_type NOT IN ('product_variation') OR 
							( p.post_type = 'product_variation' AND p.post_parent NOT IN (
								SELECT DISTINCT object_id
								FROM {$wpdb->term_relationships} t_rel
								LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
								WHERE t_tax.term_id IN (%s)
							) ) )", implode( ",", $exclude_terms ) );
					}
				}

				if ( self::get_var( 'exclude_out_of_stock_products' ) ) {
					$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
					$where['and'][] = "( p.post_type NOT IN ('product', 'product_variation') OR ( p.post_type IN ('product', 'product_variation') AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
				}

				// restrict searching only in defined categories
				if ( !empty( self::$fields['allowed_product_cat'] ) ) {
					$allowed_product_cats_filtered = array();
					foreach ( self::$fields['allowed_product_cat'] as $allowed_product_cat ) {
						$allowed_product_cats_filtered[] = "'" . $allowed_product_cat . "'";
					}

					if ( ! empty( $allowed_product_cats_filtered ) ) {
						$allowed_product_cats_filtered = implode( ",", $allowed_product_cats_filtered );
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
			}

			if (
				defined( 'POLYLANG_BASENAME' ) ||
				!empty( self::$terms ) ||
				!empty( self::$fields['allowed_product_cat'] ) ||
				!empty( self::$fields['disallowed_product_cat'] )
			) {
				$join['t_rel'] = "LEFT JOIN {$wpdb->term_relationships} t_rel ON p.ID = t_rel.object_id";
				$join['t_tax'] = "LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_tax.term_taxonomy_id = t_rel.term_taxonomy_id";
				$join['t'] = "LEFT JOIN {$wpdb->terms} t ON t_tax.term_id = t.term_id";
			}

			if ( !empty($where['or']) ) {
				$where['and'][] = "(" . implode(' OR ', $where['or']) . ")";
			}

			if ( !empty($relevance) ) {
				$relevance_query = array();

				foreach ($relevance as $k => $v) {

					$relevance_query[] = "( CASE
			                    WHEN (" . self::make_like_query( "lower($k)" )  . ") THEN " . (int) $v ."
			                    ELSE 0
			                   END )";
				}

				$relevance_query = "( " . implode(' + ', $relevance_query) . " )";

				$select[] = "$relevance_query as relevance";
				$orderby[] = "relevance DESC";
			}

			if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != '' && ! defined( 'POLYLANG_BASENAME' ) ) {
				$join['icl'] = $wpdb->prepare( "RIGHT JOIN {$wpdb->prefix}icl_translations icl ON (p.ID = icl.element_id AND icl.language_code = '%s')", ICL_LANGUAGE_CODE );
			}

			$join = apply_filters( 'smart_search_query_join', $join );
			$where = apply_filters( 'smart_search_query_where', $where );
			$orderby[] = "p.post_title ASC";

			$query = "SELECT " . implode(' , ', $select) .
				" FROM {$wpdb->posts} p
                 " . implode(' ', $join) .
				" WHERE " . implode(' AND ', $where['and']) .
				" GROUP BY " . $groupby .
				" ORDER BY " . implode(' , ', $orderby);

			if ( $limit !== '-1' ) {
				$query .= " LIMIT " . (int) $limit;
			}

			$posts = $wpdb->get_results($query, OBJECT_K);
			if ( ! $posts || ! is_array( $posts ) ) {
				$posts = array();
			}
		} else {
			$posts = array();
		}

		if ( ! empty( self::$postmeta ) && ( '-1' === $limit || count( $posts ) < $limit ) ) {
			if ( $limit !== '-1' ) {
				$limit = $limit - count( $posts );
			}
			$additional_posts = self::search_postmeta( $limit );
			$posts = array_merge( $posts, $additional_posts );
		}

		$resulted_posts = array();
		foreach ( $posts as $post ) {
			$resulted_posts[ $post->ID ] = $post;
		}

		return apply_filters( 'smart_search_query_results', $resulted_posts );
	}

	/**
	 * Extend search with postmeta
	 * @param int $limit
	 * @return array|null|object
	 */
	static function search_postmeta( $limit = 0 ) {

		global $wpdb;

		/* SELECT part */
		$select = array();
		$select[] = "DISTINCT p.ID";
		$select[] = "p.post_title";
		$select[] = "p.post_content";
		$select[] = "p.post_excerpt";
		$select[] = "p.post_type";

		/* JOIN part */
		$join = array();

		/* WHERE part */
		$where = array(
			'and' => array(),
			'or' => array(),
		);

		$where['and'][] = "p.post_status = 'publish'";

		$s_post_types = array();

		foreach ( self::get_post_types() as $type ) {
			$s_post_types[] = "'" . esc_sql( $type ) . "'";
		}

		$s_post_types = implode( ',', $s_post_types );
		$where['and'][] = "p.post_type IN ({$s_post_types})";

		/* product visibility */
		if ( self::get_post_types( 'product' ) ) {

			if ( !empty( self::$fields['disallowed_product_cat'] ) ) {
				$disallowed_product_cats_filtered = array();
				foreach ( self::$fields['disallowed_product_cat'] as $disallowed_product_cat ) {
					$disallowed_product_cats_filtered[] = "'" . $disallowed_product_cat . "'";
					$children_terms = get_term_children( $disallowed_product_cat, 'product_cat' );
					if ( ! is_wp_error( $children_terms ) && is_array( $children_terms ) && $children_terms ) {
						foreach ( $children_terms as $children_term ) {
							$disallowed_product_cats_filtered[] = "'" . intval( $children_term ) . "'";
						}
					}
				}
			}

			if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
				$where['and'][] = sprintf( "p.ID NOT IN (
						SELECT DISTINCT t_rel.object_id
						FROM {$wpdb->term_relationships} t_rel
						LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
						WHERE t_tax.term_id IN (%s)
					)", implode( ",", $disallowed_product_cats_filtered ) );
				$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
				$where['and'][] = "( p.post_type NOT IN ('product') OR (p.post_type = 'product' AND pmpv.meta_key = '_visibility' AND CAST(pmpv.meta_value AS CHAR) IN ('search','visible')) )";
			} else {
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
						)", implode( ",", $exclude_terms ) );

					$where['and'][] = sprintf( "( p.post_type NOT IN ('product_variation') OR 
							( p.post_type = 'product_variation' AND p.post_parent NOT IN (
								SELECT DISTINCT object_id
								FROM {$wpdb->term_relationships} t_rel
								LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_rel.term_taxonomy_id = t_tax.term_taxonomy_id
								WHERE t_tax.term_id IN (%s)
							) ) )", implode( ",", $exclude_terms ) );
				}
			}

			if ( self::get_var( 'exclude_out_of_stock_products' ) ) {
				$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
				$where['and'][] = "( p.post_type NOT IN ('product', 'product_variation') OR ( p.post_type IN ('product', 'product_variation') AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
			}

			// restrict searching only in defined categories
			if ( !empty( self::$fields['allowed_product_cat'] ) ) {
				$allowed_product_cats_filtered = array();
				foreach ( self::$fields['allowed_product_cat'] as $allowed_product_cat ) {
					$allowed_product_cats_filtered[] = "'" . $allowed_product_cat . "'";
				}

				if ( ! empty( $allowed_product_cats_filtered ) ) {
					$allowed_product_cats_filtered = implode( ",", $allowed_product_cats_filtered );
					$where['and'][] = sprintf( "( p.post_type NOT IN ('product') OR ( p.post_type = 'product' AND t_tax.taxonomy = 'product_cat' AND t.term_id IN (%s) ) )", $allowed_product_cats_filtered );
					// product variations
					//$where['and'][] = sprintf( "( p.post_type NOT IN ('product_variation') OR ( p.post_type = 'product_variation' AND t_tax.taxonomy = 'product_cat' AND t.term_id IN (%s) ) )", $allowed_product_cats_filtered );
				}

			}
		}

		if (
			defined( 'POLYLANG_BASENAME' ) ||
			!empty( self::$terms ) ||
			!empty( self::$fields['allowed_product_cat'] ) ||
			!empty( self::$fields['disallowed_product_cat'] )
		) {
			$join['t_rel'] = "LEFT JOIN {$wpdb->term_relationships} t_rel ON p.ID = t_rel.object_id";
			$join['t_tax'] = "LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_tax.term_taxonomy_id = t_rel.term_taxonomy_id";
			$join['t'] = "LEFT JOIN {$wpdb->terms} t ON t_tax.term_id = t.term_id";
		}

		/* GROUP BY part */
		$groupby = "p.ID";

		/* ORDER BY part */
		$orderby = array();

		// post meta fields
		if ( !empty( self::$postmeta ) ) {

			foreach (self::$postmeta as $postmeta) {
				$where['or'][] = "( pm.meta_key = '{$postmeta}' AND (" . self::make_like_query( 'lower(pm.meta_value)' ) . ") )";
			}

			$join['pm'] = "LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID";
		}

		if ( !empty($where['or']) ) {
			$where['and'][] = "(" . implode(' OR ', $where['or']) . ")";
		}

		// wpml
		if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != '' && ! defined( 'POLYLANG_BASENAME' ) ) {
			$join['icl'] = $wpdb->prepare( "RIGHT JOIN {$wpdb->prefix}icl_translations icl ON (p.ID = icl.element_id AND icl.language_code = '%s')", ICL_LANGUAGE_CODE );
		}

		$orderby[] = "p.post_title ASC";

		$join = apply_filters( 'smart_search_query_join', $join );
		$where = apply_filters( 'smart_search_query_where', $where );

		$query = "SELECT " . implode(' , ', $select) .
			" FROM {$wpdb->posts} p
                 " . implode(' ', $join) .
			" WHERE " . implode(' AND ', $where['and']) .
			" GROUP BY " . $groupby .
			" ORDER BY " . implode(' , ', $orderby);

		if ( $limit !== '-1' ) {
			$query .= " LIMIT " . (int) $limit;
		}

		$posts = $wpdb->get_results($query, OBJECT_K);

		if ( ! $posts || ! is_array( $posts ) ) {
			$posts = array();
		}
		return $posts;
	}

	protected static function make_like_query( $field ) {
		global $wpdb;
		$query = array();

		foreach ( self::get_search_terms() as $s_word ) {
			$query[] = $wpdb->prepare( "$field LIKE %s", array( "%" . trim( $s_word ) . "%" ) );
		}

		if ( '2' === self::get_var( 'enable_fuzzy_search' ) ) {
			return implode( ' AND ', $query );
		}

		return implode( ' OR ', $query );
	}
	/**
	 * Prepare suggestions list
	 * @param $posts
	 */
	public static function get_suggestions($posts) {

		foreach ($posts as $post) {

			$output = '<a href="' . esc_url( get_permalink($post->ID) ) . '" class="smart-search-post post-' . (int) $post->ID . '">';

			/* featured image */
			if ( self::get_var( 'display_icon' ) && has_post_thumbnail( $post->ID ) ) {

				$image = get_the_post_thumbnail(
					$post->ID,
					apply_filters( 'smart_search_suggestions_image_size', 'post-thumbnail' ),
					apply_filters( 'smart_search_suggestions_image_attributes', array() )
				);

				if (empty($image)) {
					$post_format = get_post_format($post->ID);
					$image = '<span class="smart-search-post-format-' . $post_format . '"></span>';
				}

				$output .= '<div class="smart-search-post-icon">' . $image . '</div>';

			}

			/* holder open */
			$output .=      '<div class="smart-search-post-holder">';

			/* title */
			$post_title = esc_html( wp_strip_all_tags( $post->post_title ) );
			$post_title = ysm_text_replace( $post_title );
			$output .=          '<div class="smart-search-post-title">' . $post_title . '</div>';

			/* excerpt */
			if ( self::get_var( 'display_excerpt' ) ) {

				if ( $post->post_excerpt != '' ) {
					$post_excerpt = $post->post_excerpt;
				} else {
					$post_excerpt = $post->post_content;
				}

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

				$post_excerpt = strip_tags( strip_shortcodes( $post_excerpt) );

				$excerpt_symbols_count_max = self::get_var( 'excerpt_symbols_count' ) ? (int) self::get_var( 'excerpt_symbols_count' ) : 50;
				$excerpt_symbols_count = strlen($post_excerpt);

				$post_excerpt = mb_substr( $post_excerpt, 0, $excerpt_symbols_count_max);

				if ($excerpt_symbols_count > $excerpt_symbols_count_max) {
					$post_excerpt .= ' ...';
				}

				$post_excerpt = ysm_text_replace( $post_excerpt );
			} else {
				$post_excerpt = '';
			}

			if ( ! empty( $post_excerpt ) && 'below_title' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			if ( ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) && class_exists( 'WooCommerce' ) ) {
				$output .= '<div class="smart-search-post-price-holder">';
				$product = wc_get_product( $post->ID );
				/* product price */
				if ( self::get_var( 'display_price' ) ) {
					$output .= '<div class="smart-search-post-price">' . $product->get_price_html() . '</div>';
				}
				/* product sku */
				if ( self::get_var( 'display_sku' ) ) {
					$output .= '<div class="smart-search-post-sku">' . esc_html( $product->get_sku() ) . '</div>';
				}
				$output .= '</div>';
			}

			if ( ! empty( $post_excerpt ) && 'below_price' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			$output .= '<div class="smart-search-clear"></div>';
			$output .= '</div><!--.smart-search-post-holder-->';
			$output .= '<div class="smart-search-clear"></div>';

			if ( ! empty( $post_excerpt ) && 'below_image' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			$output .= '</a>';

			self::$suggestions[] = array(
				'value' => esc_js($post->post_title),
				'data'  => $output,
				'url'   => esc_url( get_permalink( $post->ID ) ),
			);
		}

	}

    /**
     * Retrieve the url of View All link or redirect url of form
     * @return string
     */
    protected static function get_viewall_link_url () {
	    $param = implode( ' ', self::get_search_terms() );
	    $param = str_replace( '+', '%2b', $param );
	    $url = add_query_arg( array( 's' => $param, 'search_id' => self::$w_id ), home_url('/') );

	    if ( ! self::get_var( 'search_page_layout_posts' ) ) {
		    if ( 'product' === self::$w_id || self::get_post_types( 'product' ) ) {
			    $url = add_query_arg( array( 'post_type' => 'product' ), $url );
		    }
	    }

        return $url;
    }

	/**
	 * Output suggestions
	 */
	public static function output() {
		$view_all_link = '';

		if ( self::get_var( 'display_view_all_link' ) || self::get_var( 'view_all_link_text' ) ) {
			$view_all_link = '<a class="smart-search-view-all" href="' . esc_url( self::get_viewall_link_url() ) . '">' . esc_html__( self::get_var( 'view_all_link_text' ) , 'smart_search') . '</a>';
		}

		$res = array(
			'suggestions' => self::$suggestions,
			'view_all_link' => $view_all_link,
		);

		//debug output
		if ( self::$debug ) {
			global $wpdb;
			$res['queries'] = $wpdb->queries;

			self::$time_end = microtime( true );
			$res['time'] = self::$time_end - self::$time_start;
		} else {
			//ob_clean();
		}

		echo json_encode($res);
		exit();
	}

	/**
	 * Sort results by relevance
	 * @param $res_posts
	 * @return array
	 */
	public static function query_results_filter( $res_posts ) {
		$sorted = [];
		foreach ( $res_posts as $res_post ) {
			$sorted[ $res_post->ID ] = $res_post;
			if ( ! isset( $sorted[ $res_post->ID ]->relevance ) ) {
				$sorted[ $res_post->ID ]->relevance = 0;
			} else {
				$sorted[ $res_post->ID ]->relevance = (int) $sorted[ $res_post->ID ]->relevance;
			}
			foreach ( self::get_search_terms() as $w ) {
				$pos = strpos( mb_strtolower( trim( $res_post->post_title ) ), $w );
				if ( false !== $pos ) {
					$sorted[ $res_post->ID ]->relevance += 20;
				}
			}
		}
		usort( $sorted, array( __CLASS__, 'cmp' ) );
		return $sorted;
	}

	/**
	 * Compare
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function cmp( $a, $b ) {
		if ( $a->relevance == $b->relevance ) {
			return 0;
		}
		return ( $a->relevance < $b->relevance ) ? 1 : -1;
	}

	/**
	 * Get current widget id
	 * @return int|string
	 */
	public static function get_widget_id() {
		return self::$w_id;
	}

	/**
	 * Get current widget id
	 * @param $new_widget_id
	 */
	public static function set_widget_id( $new_widget_id ) {
		if ( ! in_array( $new_widget_id, array( 'product', 'default' ), true ) ) {
			$new_widget_id = (int) $new_widget_id;
		}
		self::$w_id = $new_widget_id;
	}

	/**
	 * Set search string
	 * @param $s
	 */
	public static function set_s( $s = '' ) {
		$s = strip_tags( trim( $s ) );
		if ( $s ) {
			$s = mb_strtolower( $s );
			self::$vars['s'] = $s;
		}
	}

	/**
	 * Get post types
	 * @param string $type
	 * @return array|bool
	 */
	public static function get_post_types( $type = '' ) {
		if ( $type ) {
			return isset( self::$pt[ $type ] );
		}
		return self::$pt;
	}

	/**
	 * Get search terms
	 * @return string
	 */
	public static function get_search_terms() {
		return self::$vars['search_terms'];
	}

	/**
	 * Set search terms
	 * @return string
	 */
	protected static function set_search_terms() {
		self::$vars['search_terms'] = array();
		$search_terms = self::get_var( 'enable_fuzzy_search' ) ? explode( ' ', self::get_var( 's' ) ) : (array) self::get_var( 's' );
		$search_terms = (array) apply_filters( 'ysm_check_words', $search_terms );
		foreach ( $search_terms as $search_term ) {
			if ( strlen( $search_term ) >= self::$min_symbols ) {
				self::$vars['search_terms'][] = $search_term;
			}
		}
	}

	/**
	 * Get var
	 * @param $name
	 * @return mixed|null
	 */
	public static function get_var( $name ) {
		return isset( self::$vars[ $name ] ) ? self::$vars[ $name ] : null;
	}
}
