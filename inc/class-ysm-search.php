<?php

/**
 * Class Ysm_Search
 * Retrieves posts from the database depending on settings
 */
class Ysm_Search
{

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
	 * List of elements that should be displayed in the widget
	 * @var array
	 */
	protected static $display_opts = array();
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
	 * Search query
	 * @var string
	 */
	protected static $s = '';
	/**
	 * Search words
	 * @var string
	 */
	protected static $s_words = array();

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
		// ajax
		if ( isset( $_REQUEST['wc-ajax'] ) ) {
			add_action( 'wc_ajax_ysm_default_search', array( __CLASS__, 'default_search' ) );
			add_action( 'wc_ajax_ysm_product_search', array( __CLASS__, 'product_search' ) );
			add_action( 'wc_ajax_ysm_custom_search', array( __CLASS__, 'custom_search' ) );
		} else {
			add_action( 'wp_ajax_ysm_default_search', array( __CLASS__, 'default_search' ) );
			add_action( 'wp_ajax_ysm_product_search', array( __CLASS__, 'product_search' ) );
			add_action( 'wp_ajax_ysm_custom_search', array( __CLASS__, 'custom_search' ) );
			add_action( 'wp_ajax_nopriv_ysm_default_search', array( __CLASS__, 'default_search' ) );
			add_action( 'wp_ajax_nopriv_ysm_product_search', array( __CLASS__, 'product_search' ) );
			add_action( 'wp_ajax_nopriv_ysm_custom_search', array( __CLASS__, 'custom_search' ) );
		}

		add_action('pre_get_posts', array(__CLASS__, 'search_filter'), 9999);
		add_action('wp', array(__CLASS__, 'remove_search_filter'));

		add_filter('the_title', array(__CLASS__, 'accent_search_words'), 9999, 1);
		add_filter('get_the_excerpt', array(__CLASS__, 'accent_search_words'), 9999, 1);
		add_filter('the_content', array(__CLASS__, 'accent_search_words'), 9999, 1);

		self::$registered_pt = array(
			'post',
			'page',
		);

		if (ysm_is_woocommerce_active()) {
			self::$registered_pt[] = 'product';
		}
	}

	/**
	 * Default search widget case
	 */
	public static function default_search()
	{
		self::$w_id = 'default';
		self::parse_settings();

		$s = $_REQUEST['query'];

		if (!$s) {
			self::output();
		}

		if (count(self::$pt) === 0){
			self::output();
		}

		$posts = self::search_posts($s);
		self::get_suggestions($posts);
		self::output();
	}

	/**
	 * Default woocommerce product search widget case
	 */
	public static function product_search()
	{
		self::$w_id = 'product';
		self::parse_settings();

		$s = $_REQUEST['query'];

		if (!$s) {
			self::output();
		}

		if (count(self::$pt) === 0){
			self::output();
		}

		$posts = self::search_posts($s);
		self::get_suggestions($posts);
		self::output();
	}

	/**
	 * Custom search widget case
	 */
	public static function custom_search() {
		if (isset($_REQUEST['id'])) {
			self::$w_id = (int) $_REQUEST['id'];
		}

		self::parse_settings();

		$s = $_REQUEST['query'];

		if (!$s) {
			self::output();
		}

		if (count(self::$pt) === 0){
			self::output();
		}

		$posts = self::search_posts($s);
		self::get_suggestions($posts);
		self::output();
	}

	/**
	 * Parse widget settings to define search behavior
	 */
	public static function parse_settings()
	{

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
		if ( !empty( $settings['display_icon'] ) ) {
			self::$display_opts['display_icon'] = 'display_icon';
		}

		if ( !empty( $settings['display_excerpt'] ) ) {
			self::$display_opts['display_excerpt'] = 'display_excerpt';
		}

		if ( !empty( $settings['excerpt_symbols_count'] ) ) {
			self::$display_opts['excerpt_symbols_count'] = $settings['excerpt_symbols_count'];
		}

		if ( !empty( $settings['display_view_all_link'] ) ) {
			self::$display_opts['display_view_all_link'] = 'display_view_all_link';
		}

		if ( !empty( $settings['view_all_link_text'] ) ) {
			self::$display_opts['view_all_link_text'] = $settings['view_all_link_text'];
		}

		if ( !empty( $settings['display_price'] ) ) {
			self::$display_opts['display_price'] = 'display_price';
		}

		if ( !empty( $settings['display_sku'] ) ) {
			self::$display_opts['display_sku'] = 'display_sku';
		}

		if ( !empty( $settings['search_page_default_output'] ) ) {
			self::$display_opts['search_page_default_output'] = 'search_page_default_output';
		}

		if ( !empty( $settings['search_page_layout_posts'] ) ) {
			self::$display_opts['search_page_layout_posts'] = 'search_page_layout_posts';
		}

		if ( !empty( $settings['accent_words_on_search_page'] ) ) {
			self::$display_opts['accent_words_on_search_page'] = 'accent_words_on_search_page';
		}

		if ( !empty( $settings['enable_fuzzy_search'] ) ) {
			self::$display_opts['enable_fuzzy_search'] = 'enable_fuzzy_search';
		}

		if ( !empty( $settings['exclude_out_of_stock_products'] ) ) {
			self::$display_opts['exclude_out_of_stock_products'] = 'exclude_out_of_stock_products';
		}

		if ( ! empty( $settings['popup_desc_pos'] ) ) {
			self::$display_opts['popup_desc_pos'] = $settings['popup_desc_pos'];
		} else {
			self::$display_opts['popup_desc_pos'] = 'below_image';
		}

	}

	/**
	 * Hook for changing posts set on search results page
	 * @param $query
	 * @return mixed
	 */
	public static function search_filter($query)
	{

		if ( $query->is_main_query() ) {

			if ( $query->is_search && isset($_GET['search_id']) ) {

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX  ) {
					return $query;
				}

				$wp_posts = array();
				$w_id = $_GET['search_id'];
				$s = $_GET['s'];

				if (empty($w_id)) {
					return $query;
				}

				if (empty($s)) {
					return $query;
				}

				if ($w_id == 'product') {
					self::$w_id = 'product';
				} else if ($w_id == 'default') {
					self::$w_id = 'default';
				} else {
					self::$w_id = (int) $w_id;
				}

				self::parse_settings();

                if (!empty(self::$display_opts['search_page_default_output'])) {
                    return $query;
                }

				if (count(self::$pt) === 0){
					return $query;
				}

				self::$max_posts = '-1';

				$posts = self::search_posts($s);

				foreach ($posts as $post) {
					$wp_posts[] = $post->ID;
				}

				self::$result_post_ids = $wp_posts;
				$query->set('s', implode( ' ', self::$s_words ) );
				$query->set('post__in', $wp_posts );
				$query-> set('orderby' ,'post__in');

				add_filter( 'posts_where',   array( __CLASS__, 'posts_where' ), 9999 );
			}

		}

	}

	/**
	 * Remove hook that change posts set on search results page
	 */
	public static function remove_search_filter()
	{
		global $wp_the_query;
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			if (!empty( $wp_the_query->query_vars['s'] ) && isset($_GET['search_id'])) {
				remove_filter( 'posts_where',   array( __CLASS__, 'posts_where' ), 9999 );
				remove_action('pre_get_posts', array( __CLASS__, 'search_filter' ), 9999);
			}

		}
	}

	/**
	 * Filter that set posts id's on search results page
	 * @param $where
	 * @return string
	 */
	public static function posts_where($where )
	{
		global $wpdb;
		$ids = !empty(self::$result_post_ids) ? implode(' , ', array_map( 'absint', self::$result_post_ids ) ) : '0';
		$where = " AND {$wpdb->posts}.ID IN (" . $ids . ") ";
		return $where;
	}

	/**
	 * Generate a main query to retrieve posts from database
	 * @param string $s
	 * @return array|null|object
	 */
	protected static function search_posts($s = '') {
		global $wpdb;

		// define search words
		$s = esc_attr( strip_tags( trim( $s ) ) );
		$s = mb_strtolower( $s );
		$s_words = array();

		if ( ! empty( self::$display_opts['enable_fuzzy_search'] ) ) {
			$s_words_temp = explode( ' ', $s );

			foreach ( $s_words_temp as $word ) {
				if ( strlen( $word ) >= self::$min_symbols ) {
					$s_words[] = $word;
				}
			}
		} else {
			$s_words[] = $s;
		}
		self::$s_words = $s_words;

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

			foreach (self::$pt as $type){
				$s_post_types[] = "'" . esc_sql($type) . "'";
			}

			$s_post_types = implode(',', $s_post_types);
			$where['and'][] = "p.post_type IN ({$s_post_types})";

			/* search exclude */
			if ( class_exists( 'SearchExclude' ) ) {
				$search_exclude = get_option( 'sep_exclude', array() );
				if ( ! empty( $search_exclude ) && is_array( $search_exclude ) ) {
					$search_exclude = implode(',', $search_exclude);
					$where['and'][] = "p.ID NOT IN ({$search_exclude})";
				}
			}

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
			if ( isset(self::$pt['product']) ) {

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
						WHERE t_rel.term_taxonomy_id IN (%s)
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

					$where['and'][] = sprintf( "p.ID NOT IN (
						SELECT DISTINCT object_id
						FROM {$wpdb->term_relationships}
						WHERE term_taxonomy_id IN (%s)
					)", implode( ",", $exclude_terms ) );
				}

				if ( ! empty( self::$display_opts['exclude_out_of_stock_products'] ) ) {
					$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
					$where['and'][] = "( p.post_type NOT IN ('product') OR ( p.post_type = 'product' AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
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

			if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != '' ) {
				$join['icl'] = $wpdb->prepare( "RIGHT JOIN {$wpdb->prefix}icl_translations icl ON (p.ID = icl.element_id AND icl.language_code = '%s')", ICL_LANGUAGE_CODE );
			}

			$join = apply_filters('smart_search_query_join', $join);
			$where = apply_filters('smart_search_query_where', $where);
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

		foreach (self::$pt as $type){
			$s_post_types[] = "'" . esc_sql($type) . "'";
		}

		$s_post_types = implode(',', $s_post_types);
		$where['and'][] = "p.post_type IN ({$s_post_types})";

		/* search exclude */
		if ( class_exists( 'SearchExclude' ) ) {
			$search_exclude = get_option( 'sep_exclude', array() );
			if ( ! empty( $search_exclude ) && is_array( $search_exclude ) ) {
				$search_exclude = implode(',', $search_exclude);
				$where['and'][] = "p.ID NOT IN ({$search_exclude})";
			}
		}

		/* product visibility */
		if ( isset(self::$pt['product']) ) {

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
						WHERE t_rel.term_taxonomy_id IN (%s)
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

				$where['and'][] = sprintf( "p.ID NOT IN (
						SELECT DISTINCT object_id
						FROM {$wpdb->term_relationships}
						WHERE term_taxonomy_id IN (%s)
					)", implode( ",", $exclude_terms ) );
			}

			if ( ! empty( self::$display_opts['exclude_out_of_stock_products'] ) ) {
				$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
				$where['and'][] = "( p.post_type NOT IN ('product') OR ( p.post_type = 'product' AND pmpv.meta_key = '_stock_status' AND CAST(pmpv.meta_value AS CHAR) NOT IN ('outofstock') ) )";
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
		if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != '' ) {
			$join['icl'] = $wpdb->prepare( "RIGHT JOIN {$wpdb->prefix}icl_translations icl ON (p.ID = icl.element_id AND icl.language_code = '%s')", ICL_LANGUAGE_CODE );
		}

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
		return $posts;
	}

	protected static function make_like_query( $field ) {
		global $wpdb;
		$query = array();

		foreach ( self::$s_words as $s_word ) {
			$query[] = $wpdb->prepare( "$field LIKE %s", array( "%" . trim( $s_word ) . "%" ) );
		}

		return implode( ' OR ', $query );
	}
	/**
	 * Prepare suggestions list
	 * @param $posts
	 */
	protected static function get_suggestions($posts) {

		foreach ($posts as $post) {

			$output = '<a href="' . esc_url( get_permalink($post->ID) ) . '" class="smart-search-post post-' . (int) $post->ID . '">';

			/* featured image */
			if ( !empty(self::$display_opts['display_icon']) && has_post_thumbnail( $post->ID )) {

				$image = get_the_post_thumbnail( $post->ID, 'post-thumbnail', apply_filters( 'smart_search_suggestions_image_attributes', array() ) );

				if (empty($image)) {
					$post_format = get_post_format($post->ID);
					$image = '<span class="smart-search-post-format-' . $post_format . '"></span>';
				}

				$output .= '<div class="smart-search-post-icon">' . $image . '</div>';

			}

			/* holder open */
			$output .=      '<div class="smart-search-post-holder">';

			/* title */
			$post_title = esc_html( $post->post_title );
			$post_title = self::text_replace( $post_title );
			$output .=          '<div class="smart-search-post-title">' . $post_title . '</div>';

			/* excerpt */
			if (!empty(self::$display_opts['display_excerpt'])) {

				if ( $post->post_excerpt != '' ) {
					$post_excerpt = $post->post_excerpt;
				} else {
					$post_excerpt = $post->post_content;
				}

				$post_excerpt = strip_tags( strip_shortcodes( $post_excerpt) );

				$excerpt_symbols_count_max = !empty( self::$display_opts['excerpt_symbols_count']  ) ? (int) self::$display_opts['excerpt_symbols_count'] : 50;
				$excerpt_symbols_count = strlen($post_excerpt);

				$post_excerpt = mb_substr( $post_excerpt, 0, $excerpt_symbols_count_max);

				if ($excerpt_symbols_count > $excerpt_symbols_count_max) {
					$post_excerpt .= ' ...';
				}

				$post_excerpt = self::text_replace( $post_excerpt );
			} else {
				$post_excerpt = '';
			}

			if ( ! empty( $post_excerpt ) && 'below_title' === self::$display_opts['popup_desc_pos'] ) {
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			if ( ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) && ysm_is_woocommerce_active() ) {
				$output .= '<div class="smart-search-post-price-holder">';
				$product = wc_get_product( $post->ID );
				/* product price */
				if ( !empty( self::$display_opts['display_price'] ) ) {
					$output .= '<div class="smart-search-post-price">' . $product->get_price_html() . '</div>';
				}
				/* product sku */
				if ( !empty( self::$display_opts['display_sku'] ) ) {
					$output .= '<div class="smart-search-post-sku">' . esc_html( $product->get_sku() ) . '</div>';
				}
				$output .= '</div>';
			}

			if ( ! empty( $post_excerpt ) && 'below_price' === self::$display_opts['popup_desc_pos'] ) {
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			$output .= '<div class="smart-search-clear"></div>';
			$output .= '</div><!--.smart-search-post-holder-->';
			$output .= '<div class="smart-search-clear"></div>';

			if ( ! empty( $post_excerpt ) && 'below_image' === self::$display_opts['popup_desc_pos'] ) {
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

        $url = home_url('/') . '?s=' . implode( ' ', self::$s_words );
        $url .= '&search_id=' . self::$w_id;

	    if ( empty( self::$display_opts['search_page_layout_posts'] ) ) {
		    if ( 'product' === self::$w_id || isset( self::$pt['product'] ) ) {
			    $url .= '&post_type=product';
		    }
	    }

        return $url;
    }

	/**
	 * Output suggestions
	 */
	protected static function output() {
		$view_all_link = '';

		if (!empty(self::$display_opts['display_view_all_link']) || !empty(self::$display_opts['view_all_link_text'])) {
			$view_all_link = self::get_viewall_link_url();

			$view_all_link = '<a class="smart-search-view-all" href="' . $view_all_link . '">' . __( self::$display_opts['view_all_link_text'] , 'smart_search') . '</a>';
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

	public static function accent_search_words( $text ) {

		if ( is_search() && isset($_GET['search_id']) ) {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return $text;
			}

			$w_id = $_GET['search_id'];
			$s    = $_GET['s'];

			if ( empty( $w_id ) || empty( $s ) ) {
				return $text;
			}

			if ($w_id == 'product') {
				self::$w_id = 'product';
			} else if ($w_id == 'default') {
				self::$w_id = 'default';
			} else {
				self::$w_id = (int) $w_id;
			}

			self::parse_settings();

			if ( empty( self::$display_opts['search_page_default_output'] ) && ! empty( self::$display_opts['accent_words_on_search_page'] ) ) {
				$text = self::text_replace( $text );
			}

		}

		return $text;
	}

	public static function text_replace( $text ) {
		$words = self::$s_words;

		foreach ( $words as &$w ) {
			$w = preg_quote( trim( $w ) );
			$w = str_replace( '/', '\/', $w );
		}

		/* replace pattern */
		$pattern = '/' . implode( '|', $words ) . '/i';
		return preg_replace( $pattern, "<strong>$0</strong>", $text );
	}

}
