<?php
namespace YSM\Hooks;

add_filter( 'get_search_query', __NAMESPACE__ . '\\get_search_query' );
add_filter( 'woocommerce_redirect_single_search_result', __NAMESPACE__ . '\\disable_product_redirect', 9999999 );

/**
 * Get search query
 * @param $var
 * @return mixed
 */
function get_search_query( $var ) {
	return ysm_get_s();
}

/**
 * Disable redirect to product page for a single search result
 * @param $val
 * @return bool
 */
function disable_product_redirect( $val ) {
	$w_id = filter_input( INPUT_GET, 'search_id', FILTER_DEFAULT );

	if ( ! empty( $w_id ) ) {
		\Ysm_Search::set_widget_id( $w_id );
		\Ysm_Search::parse_settings();

		if ( \Ysm_Search::get_var( 'search_page_disable_redirect_single_result' ) ) {
			$val = false;
		}
	}

	return $val;
}

