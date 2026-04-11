<?php
namespace YSWS\Core\Cache;

/**
 * Get cached query results
 * @param $key
 * @return bool|array
 */
function get_query_cache( $key ) {
	if ( ! empty( $key ) ) {
		$key .= get_key_version();
		$res = wp_cache_get( $key, 'ywp_sws_cache' );

		return is_array( $res ) ? $res : false;
	}
	return false;
}

/**
 * Save query results in cache
 * @param $key
 * @param $res array ids of posts
 * @return bool
 */
function set_query_cache( $key, $res ) {
	if ( ! empty( $key ) ) {
		$key .= get_key_version();
		$queries_list = (array) wp_cache_get( 'ysm_query_list', 'ywp_sws_cache' );
		if ( ! isset( $queries_list[ $key ] ) ) {
			$queries_list[ $key ] = array(
				'key'   => $key,
				'group' => 'ywp_sws_cache',
			);
			wp_cache_set( 'ysm_query_list', $queries_list, 'ywp_sws_cache', MONTH_IN_SECONDS );
		}

		if ( empty( $res ) || ! is_array( $res ) ) {
			$res = [];
		}

		return wp_cache_set( $key, $res, 'ywp_sws_cache', MONTH_IN_SECONDS );
	}

	return false;
}

function get_key_version() {
	$ver = get_option('sws_cache_key_version');
	if ( ! $ver ) {
		$ver = time();
		update_option( 'sws_cache_key_version', $ver );
	}

	return $ver;
}

/**
 * Delete query cache
 */
function delete_query_cache() {
	update_option( 'sws_cache_key_version', time() );

	$queries_list = wp_cache_get( 'ysm_query_list', 'ywp_sws_cache' );
	if ( ! $queries_list ) {
		$queries_list = get_transient( 'ysm_query_list' );
	}
	if ( ! empty( $queries_list ) && is_array( $queries_list ) ) {
		foreach ( $queries_list as $query ) {
			if ( ! empty( $query['key'] ) && ! empty( $query['group'] ) ) {
				wp_cache_delete( $query['key'], $query['group'] );
				delete_transient( $query['key'] );
			}
		}
		wp_cache_delete( 'ysm_query_list', 'ywp_sws_cache' );
		delete_transient( 'ysm_query_list' );
	}
}

/**
 * On post save
 *
 * @param  int $post_id The post ID being saved.
 * @return void
 */
function on_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( get_post_status( $post_id ) !== 'publish' ) {
		return;
	}
	if ( ! in_array( get_post_type( $post_id ), ysm_get_post_types(), true ) ) {
		return;
	}

	delete_query_cache();
}
add_action( 'save_post', __NAMESPACE__ . '\\on_save' );

add_action( 'sws_widget_settings_saved', __NAMESPACE__ . '\\delete_query_cache' );
