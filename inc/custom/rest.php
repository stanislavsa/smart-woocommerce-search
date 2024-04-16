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
