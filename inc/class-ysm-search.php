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
	 * Initial hooks
	 */
	public static function init()
	{

		add_action('wp_ajax_nopriv_ysm_default_search', array(__CLASS__, 'default_search'));
		add_action('wp_ajax_ysm_default_search', array(__CLASS__, 'default_search'));
		add_action('wp_ajax_nopriv_ysm_product_search', array(__CLASS__, 'product_search'));
		add_action('wp_ajax_ysm_product_search', array(__CLASS__, 'product_search'));
		add_action('wp_ajax_nopriv_ysm_custom_search', array(__CLASS__, 'custom_search'));
		add_action('wp_ajax_ysm_custom_search', array(__CLASS__, 'custom_search'));

		add_action('pre_get_posts', array(__CLASS__, 'search_filter'));
		add_action('wp', array(__CLASS__, 'remove_search_filter'));

		$registered_pt = get_post_types();
		unset($registered_pt['attachment']);
		unset($registered_pt['revision']);
		unset($registered_pt['nav_menu_item']);

		if (ysm_is_woocommerce_active()) {
			$registered_pt['product'] = 'Product';
		}

		self::$registered_pt = array_keys($registered_pt);

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
	public static function custom_search()
	{

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
				if ( isset($settings['post_type_'.$type]) ){
					self::$pt[ $type ] = $type;
				}
			}

		}

		self::$max_posts = !empty( $settings['max_post_count'] ) ? $settings['max_post_count'] : 99;

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

		if ( !empty( $settings['search_page_default_output'] ) ) {
			self::$display_opts['search_page_default_output'] = 'search_page_default_output';
		}

	}

	/**
	 * Hook for changing posts set on search results page
	 * @param $query
	 * @return mixed
	 */
	public static function search_filter($query)
	{

		if ( !is_admin() && $query->is_main_query() ) {

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
				$query->set('s', htmlentities( strip_tags( self::$s ) ) );
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
		if ( !is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			if (!empty( $wp_the_query->query_vars['s'] ) && isset($_GET['search_id'])) {
				remove_filter( 'posts_where',   array( __CLASS__, 'posts_where' ), 9999 );
				remove_action('pre_get_posts', array( __CLASS__, 'search_filter' ));
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
		$ids = !empty(self::$result_post_ids) ? implode(' , ', self::$result_post_ids) : '0';
		$where = " AND {$wpdb->posts}.ID IN (" . $ids . ") ";
		return $where;
	}

	/**
	 * Generate a main query to retrieve posts from database
	 * @param string $s
	 * @return array|null|object
	 */
	protected static function search_posts($s = '')
	{
		global $wpdb;

		self::$s = htmlentities( strip_tags( $s ) );
		$s = strtolower($s);

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

		if ( isset(self::$pt['product']) ) {
			$join['pmpv'] = "LEFT JOIN {$wpdb->postmeta} pmpv ON pmpv.post_id = p.ID";
			$where['and'][] = "( p.post_type NOT IN ('product') OR (p.post_type = 'product' AND pmpv.meta_key = '_visibility' AND CAST(pmpv.meta_value AS CHAR) IN ('search','visible')) )";
		}

		/* relevance part */
		$relevance = array();

		/* GROUP BY part */
		$groupby = "p.ID";

		/* ORDER BY part */
		$orderby = array();

		/* LIMIT */
		$limit = self::$max_posts === '' ? 100 : self::$max_posts;

		/* filters */

		if ( !empty(self::$fields['post_title']) ) {
			$where['or'][] = "lower(p.post_title) LIKE %s";
			$relevance['p.post_title'] = 30;
		}

		if ( !empty(self::$fields['post_content']) ) {
			$where['or'][] = "lower(p.post_content) LIKE %s";
			$relevance['p.post_content'] = 10;
		}

		if ( !empty(self::$fields['post_excerpt']) ) {
			$where['or'][] = "lower(p.post_excerpt) LIKE %s";
			$relevance['p.post_excerpt'] = 10;
		}

		/* tags and categories */
		if ( !empty( self::$terms ) ) {
			$s_terms = array();

			foreach (self::$terms as $term){
				$s_terms[] = "'" . $term . "'";
			}

			$s_terms = implode(',', $s_terms);

			$where['or'][] = "( t_tax.taxonomy IN ({$s_terms}) AND lower(t.name) LIKE %s )";

			$join['t_rel'] = "LEFT JOIN {$wpdb->term_relationships} t_rel ON p.ID = t_rel.object_id";
			$join['t_tax'] = "LEFT JOIN {$wpdb->term_taxonomy} t_tax ON t_tax.term_taxonomy_id = t_rel.term_taxonomy_id";
			$join['t'] = "LEFT JOIN {$wpdb->terms} t ON t_tax.term_id = t.term_id";
		}

		if ( !empty( self::$postmeta ) ) {

			foreach (self::$postmeta as $postmeta) {
				$where['or'][] = "( pm.meta_key = '{$postmeta}' AND  lower( pm.meta_value ) LIKE %s )";
			}

			$join['pm'] = "LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID";
		}

		if ( !empty($where['or']) ) {
			$placeholder = array();
			$like_query = "(" . implode(' OR ', $where['or']) . ")";

			foreach ($where['or'] as $val) {
				$placeholder[] = "%".$s."%";
			}

			$where['and'][] = $wpdb->prepare( $like_query, $placeholder );
		}

		if ( !empty($relevance) ) {
			$placeholder = array();
			$relevance_query = array();

			foreach ($relevance as $k => $v) {

				$relevance_query[] = "( CASE
			                    WHEN ( lower($k) LIKE '%s' ) THEN " . (int) $v ."
			                    ELSE 0
			                   END )";

				$placeholder[] = "%".$s."%";
			}

			$relevance_query = "( " . implode(' + ', $relevance_query) . " )";
			$relevance_query = $wpdb->prepare( $relevance_query, $placeholder );

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

		if ($limit !== '-1') {
			$query .= " LIMIT " . (int) $limit;
		}

		$posts = $wpdb->get_results($query, OBJECT_K);

		return $posts;
	}

	/**
	 * Prepare suggestions list
	 * @param $posts
	 */
	protected static function get_suggestions($posts)
	{

		foreach ($posts as $post) {

			$output = '<a href="' . esc_url( get_permalink($post->ID) ) . '" class="smart-search-post post-' . (int) $post->ID . '">';

			/* featured image */
			if ( !empty(self::$display_opts['display_icon']) && has_post_thumbnail( $post->ID )) {

				$image = get_the_post_thumbnail($post->ID);

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
			$post_title = preg_replace( '/'.self::$s.'/i', "<strong>$0</strong>", $post_title );
			$output .=          '<div class="smart-search-post-title">' . $post_title . '</div>';

			/* price */
			if ( !empty(self::$display_opts['display_price']) && $post->post_type == 'product' && ysm_is_woocommerce_active() ) {
				$product = wc_get_product( $post->ID );
				$output .= '<div class="smart-search-post-price">' . $product->get_price_html() . '</div>';
			}

			$output .= '</div><!--.smart-search-post-holder-->';
			$output .= '<div class="smart-search-clear"></div>';

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

				$post_excerpt = preg_replace( '/'.self::$s.'/i', "<strong>$0</strong>", $post_excerpt );
				$output .= '<div class="smart-search-post-excerpt">' . $post_excerpt . '</div>';
			}

			$output .= '</a>';

			self::$suggestions[] = array(
				'value' => esc_js($post->post_title),
				'data' => $output,
			);
		}

	}

    /**
     * Retrieve the url of View All link or redirect url of form
     * @return string
     */
    protected static function get_viewall_link_url () {

        $url = home_url('/') . '?s=' . self::$s;

        //if ( empty(self::$display_opts['search_page_default_output']) ) {
            $url .= '&search_id=' . self::$w_id;
        //}

        if ( self::$w_id === 'product' || ( isset(self::$pt[ 'product' ]) && count(self::$pt) === 1 ) ) {
            $url .= '&post_type=product';
        }

        return $url;
    }

	/**
	 * Output suggestions
	 */
	protected static function output()
	{
		$view_all_link = '';

		if (!empty(self::$display_opts['display_view_all_link']) || !empty(self::$display_opts['view_all_link_text'])) {
			$view_all_link = self::get_viewall_link_url();

			$view_all_link = '<a class="smart-search-view-all" href="' . $view_all_link . '">' . __( self::$display_opts['view_all_link_text'] , 'smart_search') . '</a>';
		}

		$res = array(
			'suggestions' => self::$suggestions,
			'view_all_link' => $view_all_link
		);

		//ob_clean();
		echo json_encode($res);
		exit();
	}

}
