<?php
namespace YSM\Rest;

add_action( 'rest_api_init', __NAMESPACE__ . '\\rest_route' );

/**
 * REST route.
 * @return void
 */
function rest_route() {
	register_rest_route( 'ysm/v1', 'search', array(
		'methods'  => \WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\handle_request',
		'permission_callback' => '__return_true',
		'args'     => array(
			'query' => array(
				'required'          => false,
				'default'           => '',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'id' => array(
				'required'          => false,
				'default'           => '',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'lang' => array(
				'required'          => false,
				'default'           => '',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		),
	) );
}

/**
 * Handler
 * @param \WP_REST_Request $request
 */
function handle_request( \WP_REST_Request $request ) {
	$query = $request['query'];
	$res = [
		'suggestions'   => [],
		'view_all_link' => '',
        'keywords' => []
    ];

	if ( ! $query ) {
		return rest_ensure_response( $res );
	}

	\Ysm_Search::set_widget_id( $request['id'] );
	\Ysm_Search::parse_settings();

	if ( ! count( \Ysm_Search::get_post_types() ) ) {
		return rest_ensure_response( $res );
	}

	\Ysm_Search::set_s( $query );

	$post_ids = \Ysm_Search::search_posts();
	$suggestions = \Ysm_Search::get_suggestions( $post_ids );

	if ( $suggestions ) {
		$res['suggestions'] = $suggestions;
		$res['view_all_link'] = \YSWS\Elements\view_all_button();
	}

	return rest_ensure_response( $res );
}

add_filter( 'rest_post_dispatch', __NAMESPACE__ . '\\set_nonce_header', 10, 3 );

/**
 * Filters the REST API response.
 *
 * Allows modification of the response before returning.
 *
 * @since 4.4.0
 * @since 4.5.0 Applied to embedded responses.
 *
 * @param \WP_HTTP_Response $result  Result to send to the client. Usually a `WP_REST_Response`.
 * @param \WP_REST_Server   $server  Server instance.
 * @param \WP_REST_Request  $request Request used to generate the response.
 */
function set_nonce_header( $result, $server, $request ) {
	if (
		defined( 'REST_REQUEST' )
		&& REST_REQUEST
		&& isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], '/ysm/v1/search' )
	) {
		$res_data = $result->get_data();
		if ( isset( $res_data['code'] ) && 'rest_cookie_invalid_nonce' === $res_data['code'] ) {
			$result->header( 'X-Wp-Nonce', wp_create_nonce( 'wp_rest' ) );
		}
	}

	return $result;
}
