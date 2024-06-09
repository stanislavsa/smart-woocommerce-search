<?php
/**
 * Get list of default widgets names
 * @return array
 */
function ysm_get_default_widgets_ids() {
	$list = array();

	if ( defined( 'AVADA_VERSION' ) ) {
		$list['avada'] = 'avada';
	}

	$list['product'] = 'product';
	$list['default'] = 'default';

	return $list;
}

/**
 * Get list of default widgets names
 * @param string $id
 * @return array|string
 */
function ysm_get_default_widgets_names( $id = '' ) {
	$list = array(
		'default' => __( 'WordPres Default Search Widget', 'smart-woocommerce-search' ),
		'product' => __( 'WooCommerce Product Search Widget', 'smart-woocommerce-search' ),
		'avada'   => __( 'Avada Search Widget', 'smart-woocommerce-search' ),
		'divi'    => __( 'DIVI Search Widget', 'smart-woocommerce-search' ),
	);

	if ( '' !== $id ) {
		if ( isset( $list[ $id ] ) ) {
			return $list[ $id ];
		} else {
			return '';
		}
	}

	return $list;
}

/**
 * Get option by $id
 * @param $w_id
 * @param $id
 * @return string|null
 */
function ysm_get_option( $w_id, $id ) {
	return Ysm_Widget_Manager::get( $w_id, $id );
}

/**
 * Get setting output
 * @param $w_id
 * @param $id
 * @param $args
 */
function ysm_setting( $w_id, $id, $args ) {

	$value = ysm_get_option( $w_id, $id );

	if ( null !== $value ) {
		$args['value'] = $value;
	}

	\YummyWP\App\Field::output( $id, $args );
}

/**
 * Add messages and errors
 * @param $text
 * @param string $type
 */
function ysm_add_message( $text, $type = 'message' ) {
	\YummyWP\App\Notification::add( $text, $type );
}

/**
 * Retrieve custom widgets list row template
 * @param array $args
 * @return string
 */
function ysm_get_widget_list_row_template( $args ) {

	$id = $args['id'];

	$template = '<tr>
					<td>' . esc_html( $id ) . '</td>
					<td>
						<a href="' . esc_url( admin_url( 'admin.php?page=smart-search&action=edit&id=' ) ) . esc_attr( $id ) . '">
							' . ( ! empty( $args['name'] ) ? esc_html( $args['name'] ) : 'no name' ) . '
						</a>
					</td>
					<td>
						<input type="text" value="[smart_search id=&quot;' . esc_attr( $id ) . '&quot;]" readonly="" />
					</td>
					<td>
						<a href="#" class="ysm-widget-duplicate" data-id="' . esc_attr( $id ) . '" title="' . __( 'Duplicate', 'smart-woocommerce-search' ) . '">
							<span class="dashicons dashicons-admin-page"></span>
						</a>
						<a href="#" class="ysm-widget-remove" data-id="' . esc_attr( $id ) . '" title="' . __( 'Delete', 'smart-woocommerce-search' ) . '">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</td>
				</tr>';

	return $template;
}

/**
 * Get list of custom widgets
 * @return array
 */
function ysm_get_custom_widgets() {
	return Ysm_Widget_Manager::get_all_widgets( 'custom' );
}

/**
 * Get list of default widgets
 * @return array
 */
function ysm_get_default_widgets() {
	return Ysm_Widget_Manager::get_all_widgets( 'default' );
}

/**
 * Get list of custom and default widgets
 * @return array
 */
function ysm_get_all_widgets() {
	static $all_widgets = [];

	if ( ! empty( $all_widgets ) ) {
		return $all_widgets;
	}

	$custom_widgets  = ysm_get_custom_widgets();
	$default_widgets = ysm_get_default_widgets();

	$all_widgets = $custom_widgets;

	$available_default_widgets_ids = ysm_get_default_widgets_ids();

	foreach ( $default_widgets as $w_key => $default_widget ) {
		if ( isset( $available_default_widgets_ids[ $w_key ] ) ) {
			$enable_key = ( 'default' === $w_key ) ? 'enable_search' : 'enable_' . $w_key . '_search';
			if ( ! empty( $default_widget['settings'][ $enable_key ] ) ) {
				$all_widgets[ $w_key ] = $default_widget;
			}
		}
	}

	return $all_widgets;
}

function ysm_get_post_types() {
	$pt = [
		'post' => 'post',
		'page' => 'page',
		'product' => 'product',
	];

	foreach ( ysm_get_all_widgets() as $v ) {
		if ( ! empty( $v['settings']['custom_post_types'] ) ) {
			$pt = array_merge( $pt, (array) $v['settings']['custom_post_types'] );
		}
	}

	$pt = array_unique( $pt );

	$pt_exists = [];
	foreach ( $pt as $pt_name ) {
		if ( $pt_name && 'product_variation' !== $pt_name && post_type_exists( $pt_name ) ) {
			$pt_exists[ $pt_name ] = $pt_name;
		}
	}

	return array_values( $pt_exists );
}

function ysm_get_all_widgets_custom_fields() {
	static $custom_fields = [];

	if ( ! empty( $custom_fields ) ) {
		return $custom_fields;
	}

	foreach ( ysm_get_all_widgets() as $v ) {
		if ( ! empty( $v['settings']['field_product_sku'] ) ) {
			$custom_fields['_sku'] = '_sku';
		}
		if ( ! empty( $v['settings']['custom_fields'] ) ) {
			$cf_list = explode( ',', $v['settings']['custom_fields'] );
			foreach ( $cf_list as $cf ) {
				$cf = trim( $cf );
				if ( $cf ) {
					$custom_fields[$cf] = $cf;
				}
			}
		}
	}

	if ( isset( $custom_fields['_product_attributes'] ) ) {
		unset( $custom_fields['_product_attributes'] );
	}

	return $custom_fields;
}

function ysm_get_all_widgets_taxonomy_terms() {
	static $taxonomy_terms = [];

	if ( ! empty( $taxonomy_terms ) ) {
		return $taxonomy_terms;
	}

	foreach ( ysm_get_all_widgets() as $v ) {
		if ( ! empty( $v['settings']['field_tag'] ) ) {
			$taxonomy_terms['post_tag'] = 'post_tag';
		}
		if ( ! empty( $v['settings']['field_category'] ) ) {
			$taxonomy_terms['category'] = 'category';
		}
		if ( ! empty( $v['settings']['field_product_tag'] ) ) {
			$taxonomy_terms['product_tag'] = 'product_tag';
		}
		if ( ! empty( $v['settings']['field_product_cat'] ) ) {
			$taxonomy_terms['product_cat'] = 'product_cat';
		}
		if ( ! empty( $v['settings']['custom_tax'] ) && is_array( $v['settings']['custom_tax'] ) ) {
			foreach ( $v['settings']['custom_tax'] as $custom_t ) {
				$taxonomy_terms[ $custom_t ] = $custom_t;
			}
		}
	}

	$taxonomy_terms['product_cat'] = 'product_cat';

	return $taxonomy_terms;
}

/**
 * Get 's' query var
 * @return string
 */
function ysm_get_s() {
	$s = filter_input( INPUT_GET, 's', FILTER_DEFAULT );
	$woof_text = filter_input( INPUT_GET, 'woof_text', FILTER_DEFAULT );
	if ( ! empty( $woof_text ) ) {
		$s = $woof_text;
	}
	if ( $s ) {
		$s = html_entity_decode( $s );
		$s = wp_strip_all_tags( $s );
		$s = sanitize_text_field( $s );
	}

	return $s;
}

/**
 * Wrap search terms in <strong> tag on search page
 * @param $text
 * @return mixed
 */
function ysm_accent_search_term( $text ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return $text;
	}

	if ( ! is_search() ) {
		return $text;
	}

	$w_id = filter_input( INPUT_GET, 'search_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$s    = ysm_get_s();

	if ( empty( $w_id ) || empty( $s ) ) {
		return $text;
	}

	\Ysm_Search::set_widget_id( $w_id );
	\Ysm_Search::parse_settings();

	if ( empty( \Ysm_Search::get_var( 'search_page_default_output' ) ) && ! empty( \Ysm_Search::get_var( 'accent_words_on_search_page' ) ) ) {
		$text = ysm_text_replace( wp_strip_all_tags( $text ) );
	}

	return $text;
}

/**
 * Wrap search terms in <strong> tag
 * @param $text
 * @return string
 */
function ysm_text_replace( $text ) {
	$words = \Ysm_Search::get_search_terms();

	foreach ( $words as &$w ) {
		$w = preg_quote( trim( $w ) );
		$w = str_replace( '/', '\/', $w );
	}

	return preg_replace( '/' . implode( '|', $words ) . '/i', '<strong>$0</strong>', $text );
}

/**
 * Get woocommerce product rewrite base slug
 * @param $w_id
 * @return string|null
 */
function ysw_get_woocommerce_product_slug( $w_id ) {
	return 'product';
}
