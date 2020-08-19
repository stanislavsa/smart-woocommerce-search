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
	 * List of protected vars, that can't be overwritten
	 * @var array
	 */
	protected static $protected_vars = array(
		'post_types',
		'taxonomies',
		'postmeta',
	);
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

		$registered_pt = array(
			'post',
			'page',
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$registered_pt[] = 'product';
			$registered_pt[] = 'product_variation';
		}

		$widget_id = self::get_widget_id();

		if ( in_array( $widget_id, array( 'product', 'default' ), true ) ) {
			$widgets = ysm_get_default_widgets();
		} else {
			$widgets = ysm_get_custom_widgets();
		}

		$settings = $widgets[ $widget_id ]['settings'];

		$settings['post_types'] = array();
		if ( 'product' === $widget_id ) {
			$settings['post_types']['product'] = 'product';
		} else {
			foreach ( $registered_pt as $type ) {
				if ( ! empty( $settings[ 'post_type_' . $type ] ) ) {
					$settings['post_types'][ $type ] = $type;
				}
			}
		}

		// posts_per_page
		if ( empty( $settings['max_post_count'] ) ) {
			$settings['max_post_count'] = 10;
		}

		if ( empty( $settings['char_count'] ) ) {
			$settings['char_count'] = 3;
		}

		if ( ! empty( $settings['allowed_product_cat'] ) ) {
			if ( ! is_array( $settings['allowed_product_cat'] ) ) {
				$settings['allowed_product_cat'] = explode( ',', $settings['allowed_product_cat'] );
			}
			$allowed_cats = array();
			foreach ( $settings['allowed_product_cat'] as $dis_cat ) {
				$dis_cat = intval( trim( $dis_cat ) );
				if ( $dis_cat ) {
					$allowed_cats[] = $dis_cat;
				}
			}
			$settings['allowed_product_cat'] = $allowed_cats;
		}

		if ( ! empty( $settings['disallowed_product_cat'] ) ) {
			if ( ! is_array( $settings['disallowed_product_cat'] ) ) {
				$settings['disallowed_product_cat'] = explode( ',', $settings['disallowed_product_cat'] );
			}
			$disallowed_cats = array();
			foreach ( $settings['disallowed_product_cat'] as $dis_cat ) {
				$dis_cat = intval( trim( $dis_cat ) );
				if ( $dis_cat ) {
					$disallowed_cats[] = $dis_cat;
				}
			}
			$settings['disallowed_product_cat'] = $disallowed_cats;
		}

		// taxonomies
		$settings['taxonomies'] = array();

		if ( ! empty( $settings['field_tag'] ) ) {
			$settings['taxonomies']['post_tag'] = 'post_tag';
		}

		if ( ! empty( $settings['field_category'] ) ) {
			$settings['taxonomies']['category'] = 'category';
		}

		if ( ! empty( $settings['field_product_tag'] ) ) {
			$settings['taxonomies']['product_tag'] = 'product_tag';
		}

		if ( ! empty( $settings['field_product_cat'] ) ) {
			$settings['taxonomies']['product_cat'] = 'product_cat';
		}

		/* post meta */
		$settings['postmeta'] = array();
		if ( ! empty( $settings['field_product_sku'] ) ) {
			$settings['postmeta']['_sku'] = '_sku';
		}

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

			$w_id = filter_input( INPUT_GET, 'search_id', FILTER_SANITIZE_STRING );
			if ( ! in_array( $w_id, array( 'default', 'product' ), true ) ) {
				$w_id = (int) $w_id;
			}

			if ( $w_id ) {
				$wp_posts = array();

				self::set_widget_id( $w_id );
				self::parse_settings();

				if ( self::get_var( 'search_page_default_output' ) ) {
					return $query;
				}
				if ( ! self::get_post_types() ) {
					return $query;
				}

				self::set_var( 'max_post_count', -1 );

				self::set_s( $s );
				$posts = self::search_posts();

				foreach ( $posts as $post ) {
					$wp_posts[] = $post->ID;
				}

				if ( empty( $wp_posts ) ) {
					$wp_posts[] = 0;
				}

				$query->set( 's', '' );
				$query->set( 'post__in', $wp_posts );

				$product_orderby = filter_input( INPUT_GET, 'product_orderby', FILTER_SANITIZE_STRING );
				if ( empty( $product_orderby ) ) {
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
		if ( ! is_admin() &&  ! empty( filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING ) ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
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
		$limit = self::get_var( 'max_post_count' );

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

		if ( self::get_var( 'field_title' ) ) {
			$where['or'][] = self::make_like_query( 'lower(p.post_title)' );
			$relevance['p.post_title'] = 30;
		}

		if ( self::get_var( 'field_content' ) ) {
			$where['or'][] = self::make_like_query( 'lower(p.post_content)' );
			$relevance['p.post_content'] = 10;
		}

		if ( self::get_var( 'field_excerpt' ) ) {
			$where['or'][] = self::make_like_query( 'lower(p.post_excerpt)' );
			$relevance['p.post_excerpt'] = 10;
		}

		/* tags and categories */
		if ( self::get_var( 'taxonomies' ) ) {
			$s_terms = array();

			foreach ( self::get_var( 'taxonomies' ) as $term ) {
				$s_terms[] = "'" . $term . "'";
			}

			$s_terms = implode( ',', $s_terms );

			$where['or'][] = "( t_tax.taxonomy IN ({$s_terms}) AND (" . self::make_like_query( 'lower(t.name)' ) . ") )";
		}

		/* product visibility */
		if ( self::get_post_types( 'product' ) ) {

			if ( self::get_var( 'exclude_out_of_stock_products' ) ) {
				$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
				$where['and'][] = "( p.post_type NOT IN ('product', 'product_variation') OR ( p.post_type IN ('product', 'product_variation') AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
			}
		}

		if ( !empty($where['or']) ) {
			$where['and'][] = "(" . implode(' OR ', $where['or']) . ")";
		}

		/* SELECT part */
		$select = array(
			"DISTINCT p.ID"
		);

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

		if ( $limit !== -1 ) {
			$query .= " LIMIT " . (int) $limit;
		}

		$posts = $wpdb->get_results($query, OBJECT_K);
		if ( ! $posts || ! is_array( $posts ) ) {
			$posts = array();
		}

		if ( self::get_var( 'postmeta' ) && ( -1 === $limit || count( $posts ) < $limit ) ) {
			if ( $limit !== -1 ) {
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

			if ( self::get_var( 'exclude_out_of_stock_products' ) ) {
				$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
				$where['and'][] = "( p.post_type NOT IN ('product', 'product_variation') OR ( p.post_type IN ('product', 'product_variation') AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
			}
		}

		/* GROUP BY part */
		$groupby = "p.ID";

		/* ORDER BY part */
		$orderby = array();

		// post meta fields
		if ( self::get_var( 'postmeta' ) ) {

			foreach ( self::get_var( 'postmeta' ) as $postmeta_field ) {
				$where['or'][] = "( pm.meta_key = '{$postmeta_field}' AND (" . self::make_like_query( 'lower(pm.meta_value)' ) . ") )";
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

		$query = "SELECT DISTINCT p.ID" .
			" FROM {$wpdb->posts} p
                 " . implode(' ', $join) .
			" WHERE " . implode(' AND ', $where['and']) .
			" GROUP BY " . $groupby .
			" ORDER BY " . implode(' , ', $orderby);

		if ( $limit !== -1 ) {
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
	public static function get_suggestions( $posts ) {

		foreach ( $posts as $post ) {
			$post = get_post( $post->ID );
			$wc_product = null;
			$image = '';
			$post_classes = array(
				'smart-search-post',
				'post-' . intval( $post->ID ),
			);

			if ( ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) && class_exists( 'WooCommerce' ) ) {
				$wc_product = wc_get_product( $post->ID );
			}

			/* featured image */
			if ( self::get_var( 'display_icon' ) && has_post_thumbnail( $post ) ) {

				$image = get_the_post_thumbnail(
					$post,
					apply_filters( 'smart_search_suggestions_image_size', 'post-thumbnail' ),
					apply_filters( 'smart_search_suggestions_image_attributes', array() )
				);

				if ( empty( $image ) ) {
					$post_classes[] = 'smart-search-no-thumbnail';
				}
			}

			$output = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="' . esc_attr( implode( ' ', $post_classes ) ) . '">';

			if ( $image ) {
				$output .= '<div class="smart-search-post-icon">' . $image . '</div>';
			}

			/* holder open */
			$output .=      '<div class="smart-search-post-holder">';

			/* title */
			$post_title = esc_html( wp_strip_all_tags( $post->post_title ) );
			$post_title = ysm_text_replace( $post_title );
			$output .=    '<div class="smart-search-post-title">' . $post_title . '</div>';

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
				$output .= '<div class="smart-search-post-excerpt">' . wp_kses_post( $post_excerpt ) . '</div>';
			}

			if ( $wc_product ) {
				$output .= '<div class="smart-search-post-price-holder">';
				/* product price */
				if ( self::get_var( 'display_price' ) ) {
					// @codingStandardsIgnoreStart
					$output .= '<div class="smart-search-post-price">' . $wc_product->get_price_html() . '</div>';
					// @codingStandardsIgnoreEnd
				}
				/* product sku */
				if ( self::get_var( 'display_sku' ) ) {
					$output .= '<div class="smart-search-post-sku">' . esc_html( $wc_product->get_sku() ) . '</div>';
				}
				$output .= '</div>';
			}

			if ( ! empty( $post_excerpt ) && 'below_price' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . wp_kses_post( $post_excerpt ) . '</div>';
			}

			$output .= '<div class="smart-search-clear"></div>';
			$output .= '</div><!--.smart-search-post-holder-->';
			$output .= '<div class="smart-search-clear"></div>';

			if ( ! empty( $post_excerpt ) && 'below_image' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . wp_kses_post( $post_excerpt ) . '</div>';
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
	    $url = add_query_arg( array( 's' => $param, 'search_id' => self::get_widget_id() ), home_url('/') );

	    if ( ! self::get_var( 'search_page_layout_posts' ) ) {
		    if ( 'product' === self::get_widget_id() || self::get_post_types( 'product' ) ) {
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
	 * Compare by relevance
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
		$post_types = self::get_var( 'post_types' );
		if ( $type ) {
			return isset( $post_types[ $type ] );
		}
		return $post_types;
	}

	/**
	 * Get search terms
	 * @return array
	 */
	public static function get_search_terms() {
		return (array) self::$vars['search_terms'];
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
			if ( strlen( $search_term ) >= self::get_var( 'char_count' ) ) {
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

	/**
	 * Set var
	 * @param $name
	 * @param $value
	 * @return void
	 */
	public static function set_var( $name, $value ) {
		if ( ! in_array( $name, self::$protected_vars, true ) ) {
			self::$vars[ $name ] = $value;
		}
	}
}
