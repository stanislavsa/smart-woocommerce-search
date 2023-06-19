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
	public static $w_id = 0;
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
	 * Number of found posts
	 * @var array
	 */
	protected static $found_posts = 0;
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
	protected static $debug    = 0;
	private static $time_start = 0;
	private static $time_end   = 0;

	/**
	 * Initial hooks
	 */
	public static function init() {
		self::$time_start = microtime( true );

		add_action( 'pre_get_posts', array( __CLASS__, 'search_filter' ), PHP_INT_MAX );
		add_action( 'woocommerce_product_query', array( __CLASS__, 'search_filter' ), PHP_INT_MAX );
		add_action( 'wp', array( __CLASS__, 'remove_search_filter' ), 9999 );
		add_filter( 'found_posts', array( __CLASS__, 'alter_found_posts' ), PHP_INT_MAX, 2 );

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

		if ( in_array( $widget_id, ysm_get_default_widgets_ids(), true ) ) {
			$widgets = ysm_get_default_widgets();
		} else {
			$widgets = ysm_get_custom_widgets();
		}

		$settings = $widgets[ $widget_id ]['settings'];

		$settings['post_types'] = array();
		if ( 'product' === $widget_id ) {
			$settings['post_types']['product'] = 'product';
		}

		foreach ( $registered_pt as $type ) {
			if ( ! empty( $settings[ 'post_type_' . $type ] ) ) {
				$settings['post_types'][ $type ] = $type;
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

		/* post meta */
		$settings['postmeta'] = array();
		if ( ! empty( $settings['field_product_sku'] ) ) {
			$settings['postmeta']['_sku'] = '_sku';
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

		if ( ! is_admin() && ! empty( $query->query_vars['s'] ) && ! empty( $s ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			$w_id = filter_input( INPUT_GET, 'search_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( ! $w_id ) {
				$w_id = apply_filters( 'ysm_search_page_default_widget', 0 );
			}
			if ( ! in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) {
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

				$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$per_page = get_option( 'posts_per_page' );
				if ( $post_type && 'product' === $post_type ) {
					$columns = get_option( 'woocommerce_catalog_columns', 4 );
					if ( function_exists( 'wc_get_default_products_per_row' ) ) {
						$columns = wc_get_default_products_per_row();
					}
					if ( function_exists( 'wc_get_default_product_rows_per_page' ) ) {
						$per_page = $columns * wc_get_default_product_rows_per_page();
					}
					$posts_count = apply_filters( 'loop_shop_per_page', $per_page );
				} else {
					$posts_count = $per_page;
				}

				$paged = $query->query_vars['paged'];
				if ( ! $paged || 1 === $paged ) {
					if ( isset( $_GET['fwp_paged'] ) ) {
						$paged = (int) filter_input( INPUT_GET, 'fwp_paged', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					}
				}
				if ( 1 < $paged ) {
					$offset = ( $query->query_vars['paged'] - 1 ) * $posts_count;
				} else {
					$offset = 0;
				}

				$posts = self::search_posts( $posts_count, $offset );

				foreach ( $posts as $post ) {
					$wp_posts[] = $post->ID;
				}

				if ( empty( $wp_posts ) ) {
					$wp_posts[] = 0;
				}

				$query->set( 's', '' );
				$query->set( 'post__in', $wp_posts );
				$query->set( 'post_type', self::get_post_types() );
				$query->set( 'ysm_found_posts', self::$found_posts );
				$query->set( 'offset', 0 );
				$query->set( 'posts_per_page', $posts_count );

				$product_orderby = filter_input( INPUT_GET, 'product_orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				if ( empty( $product_orderby ) ) {
					$orderby = $query->get( 'orderby' );
					if ( 'relevance' === $orderby ) {
						$query->set( 'orderby', 'post__in' );
					}
				}
			}
		}
	}

	public static function alter_found_posts( $found_posts, $query ) {
		if ( ! empty( $query->query_vars['ysm_found_posts'] ) ) {
			$found_posts = $query->query_vars['ysm_found_posts'];
		}

		return $found_posts;
	}

	/**
	 * Remove hook that change posts set on search results page
	 */
	public static function remove_search_filter() {
		if ( ! is_admin() && ! empty( filter_input( INPUT_GET, 's', FILTER_DEFAULT ) ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			remove_action( 'pre_get_posts', array( __CLASS__, 'search_filter' ), 99999999 );
		}
	}

	/**
	 * Generate a main query to retrieve posts from database
	 * @return array|null|object
	 */
	public static function search_posts( $posts_count = 0, $offset = 0 ) {

		self::set_search_terms();

		$limit = self::get_var( 'max_post_count' );

		$posts = array();
		$postmeta = self::get_var( 'postmeta' );

		if ( -1 === $limit || count( $posts ) < $limit ) {
			Ysm_DB::init( array(
				'posts_per_page' => $limit,
				'post_type' => array_values( self::get_post_types() ),
				'order' => 'ASC',
				'orderby' => 'title',
			) );
			Ysm_DB::set_relevance( array( 'post_title', 'post_content', 'post_excerpt' ) );
			$the_query = Ysm_DB::do_query();

			$posts = array_unique( array_merge( $posts, $the_query->posts ) );
		}

		if ( $postmeta && ( -1 === $limit || count( $posts ) < $limit ) ) {
			if ( -1 !== $limit ) {
				$limit = $limit - count( $posts );
			}

			Ysm_DB::init( array(
				'posts_per_page' => $limit,
				'post_type' => array_values( self::get_post_types() ),
				'order' => 'ASC',
				'orderby' => 'title',
			) );
			Ysm_DB::is_postmeta_only( true );

			$meta_query = array( 'relation' => 'OR' );

			foreach ( $postmeta as $item ) {
				$meta_query[] = array(
					'key'     => $item,
					'value'   => 'ysm-meta-query-placeholder',
					'compare' => 'LIKE',
				);
			}

			Ysm_DB::add_meta_query( $meta_query );
			$the_query = Ysm_DB::do_query();

			$posts = array_unique( array_merge( $posts, $the_query->posts ) );
		}

		$resulted_posts = array();
		if ( $posts ) {
			self::$found_posts = count( $posts );
			if ( ! $posts_count ) {
				$posts_count = self::get_var( 'max_post_count' );
				if ( -1 === $posts_count ) {
					$posts_count = ysm_get_option( self::get_widget_id(), 'max_post_count' );
				}
			}
			$posts = array_slice( $posts, $offset, $posts_count );
			$the_query = new WP_Query( array(
				'posts_per_page' => $posts_count,
				'post_type'      => self::get_post_types(),
				'post__in'       => array_map( 'intval', $posts ),
			) );
			if ( $the_query->have_posts() ) {
				foreach ( $the_query->posts as $q_post ) {
					$resulted_posts[ $q_post->ID ] = $q_post;
				}
			}
		}

		return apply_filters( 'smart_search_query_results', $resulted_posts );
	}

	/**
	 * Prepare suggestions list
	 * @param $posts
	 */
	public static function get_suggestions( $posts ) {
		$ii = 0;
		foreach ( $posts as $post ) {
			$post = get_post( $post );
			$wc_product = null;
			$product = null;
			$output = '';
			$image = '';
			$post_excerpt = '';
			$post_classes = array(
				'smart-search-post',
				'post-' . intval( $post->ID ),
			);

			if ( ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) && class_exists( 'WooCommerce' ) ) {
				$wc_product = wc_get_product( $post->ID );
			}

			/* thumbnail */
			if ( self::get_var( 'display_icon' ) && has_post_thumbnail( $post ) ) {
				$the_post = $post;

				/**
				 * Overwrite image html in the suggestion
				 *
				 * @param $the_post WP_Post|WC_Product WordPress post object or WooCommerce product object
				 *
				 * @since 2.5.0
				 */
				$pre_image_output = apply_filters( 'ysm_suggestion_image_output', '', $the_post );
				if ( ! empty( $pre_image_output ) ) {
					$image = $pre_image_output;
				} else {
					$image = get_the_post_thumbnail(
						$the_post,
						/**
						 * Overwrite image size in the suggestion
						 * eg. 'post-thumbnail' or 'medium'
						 *
						 * @since 2.5.0
						 */
						apply_filters( 'ysm_suggestion_image_size',
							// compatibility with old version
							apply_filters( 'smart_search_suggestions_image_size', 'post-thumbnail' )
						),
						/**
						 * Overwrite image attributes in the suggestion
						 * eg. 'post-thumbnail' or 'medium'
						 *
						 * @since 2.5.0
						 */
						apply_filters( 'ysm_suggestion_image_attributes',
							// compatibility with old version
							apply_filters( 'smart_search_suggestions_image_attributes', array() )
						)
					);
				}

				if ( empty( $image ) ) {
					$post_classes[] = 'smart-search-no-thumbnail';
				}
			}

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

				$post_excerpt = wp_strip_all_tags( strip_shortcodes( $post_excerpt) );

				$excerpt_symbols_count_max = self::get_var( 'excerpt_symbols_count' )  ? (int) self::get_var( 'excerpt_symbols_count' )  : 50;
				$excerpt_symbols_count = strlen( $post_excerpt );

				$post_excerpt = mb_substr( $post_excerpt, 0, $excerpt_symbols_count_max);

				if ($excerpt_symbols_count > $excerpt_symbols_count_max) {
					$post_excerpt .= ' ...';
				}

				$post_excerpt = ysm_text_replace( $post_excerpt );
			} else {
				$post_excerpt = '';
			}

			$output .= '<a href="' . esc_url( get_the_permalink( $post->ID ) ) . '">';
			$output .= '<div class="' . esc_attr( implode( ' ', $post_classes ) ) . '">';

			/* thumbnail */
			if ( ! empty( $image ) ) {
				$output .= '<div class="smart-search-post-icon">' . $image . '</div>';
			}

			/* holder open */
			$output .=      '<div class="smart-search-post-holder">';

			/* title */
			$post_title = wp_strip_all_tags( get_the_title( $post->ID ) );
			$post_title = ysm_text_replace( $post_title );
			$output .=    '<div class="smart-search-post-title">' . wp_kses_post( $post_title ) . '</div>';

			if ( ! empty( $post_excerpt ) && 'below_title' === self::get_var( 'popup_desc_pos' ) ) {
				$output .= '<div class="smart-search-post-excerpt">' . wp_kses_post( $post_excerpt ) . '</div>';
			}

			if ( $wc_product ) {
				global $product;
				$product = $wc_product;

				$output .= '<div class="smart-search-post-price-holder">';

				/* product price */
				if ( self::get_var( 'display_price' ) ) {
					// @codingStandardsIgnoreStart
					$output .= '<div class="smart-search-post-price">' . $product->get_price_html() . '</div>';
					// @codingStandardsIgnoreEnd
				}
				/* product sku */
				if ( self::get_var( 'display_sku' ) ) {
					$output .= '<div class="smart-search-post-sku">' . esc_html( $product->get_sku() ) . '</div>';
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

			$output .= '</div>';
			$output .= '</a>';

			self::$suggestions[] = array(
				'value' => esc_js( $post->post_title ),
				'data'  => $output,
				'url'   => get_permalink( $post->ID ),
			);

			$ii++;
			if ( $ii == self::get_var( 'max_post_count' ) ) {
				break;
			}
		}
		wp_reset_postdata();
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
			if ( self::get_post_types( 'product' ) ) {
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
			// target _blank
			$target_blank = '';
			if ( self::get_var( 'view_all_link_target_blank' ) ) {
				$target_blank = ' target="_blank"';
			}
			$view_all_link = '<a class="smart-search-view-all"' . $target_blank . ' href="' . esc_url( self::get_viewall_link_url() ) . '">' . esc_html__( self::get_var( 'view_all_link_text' ), 'smart-woocommerce-search' ) . '</a>';
		}

		$res = array(
			'suggestions' => self::$suggestions,
			'view_all_link' => $view_all_link,
		);

		//debug output
		if ( self::$debug ) {
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
		$postmeta = self::get_var( 'postmeta' );
		foreach ( $res_posts as $res_post ) {
			$sorted[ $res_post->ID ] = $res_post;
			if ( ! isset( $sorted[ $res_post->ID ]->relevance ) ) {
				$sorted[ $res_post->ID ]->relevance = 0;
			} else {
				$sorted[ $res_post->ID ]->relevance = (int) $sorted[ $res_post->ID ]->relevance;
			}
			foreach ( self::get_search_terms() as $w ) {
				$pos = strpos( mb_strtolower( trim( $res_post->post_title ) ), $w );
				if ( 0 === $pos ) {
					$sorted[ $res_post->ID ]->relevance += 30;
				} elseif ( 0 < $pos ) {
					$sorted[ $res_post->ID ]->relevance += 20;
				}

				if ( ! empty( $postmeta['_sku'] ) ) {
					if ( isset( $res_post->meta_key ) && '_sku' === $res_post->meta_key ) {
						$pos = strpos( mb_strtolower( trim( $res_post->meta_value ) ), $w );
						if ( 0 === $pos ) {
							$sorted[ $res_post->ID ]->relevance += 30;
						} elseif ( 0 < $pos ) {
							$sorted[ $res_post->ID ]->relevance += 20;
						}
					}
				}
			}
		}
		usort( $sorted, array( __CLASS__, 'cmp_title' ) );
		usort( $sorted, array( __CLASS__, 'cmp_relevance' ) );
		return $sorted;
	}

	/**
	 * Compare by title
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function cmp_title( $a, $b ) {
		return strcasecmp( $a->post_title, $b->post_title );
	}

	/**
	 * Compare by relevance
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function cmp_relevance( $a, $b ) {
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
	 * Set current widget id
	 * @param $new_widget_id
	 */
	public static function set_widget_id( $new_widget_id ) {
		if ( ! in_array( $new_widget_id, ysm_get_default_widgets_ids(), true ) ) {
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
