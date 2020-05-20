<?php
namespace YSM\Hooks;

add_filter( 'get_search_query', __NAMESPACE__ . '\\get_search_query' );

/**
 * Get search query
 * @param $var
 * @return mixed
 */
function get_search_query( $var ) {
	return ysm_get_s();
}


