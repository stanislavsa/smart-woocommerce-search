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
		),
	) );
}

/**
 * Handler
 * @param \WP_REST_Request $request
 */
function handle_request( \WP_REST_Request $request ) {
	$query = $request['query'];
	if ( ! $query ) {
		\Ysm_Search::output();
	}

	\Ysm_Search::set_widget_id( $request['id'] );
	\Ysm_Search::parse_settings();

	if ( ! count( \Ysm_Search::get_post_types() ) ) {
		\Ysm_Search::output();
	}

	\Ysm_Search::set_s( $query );
	$posts = \Ysm_Search::search_posts();
	\Ysm_Search::get_suggestions( $posts );
	\Ysm_Search::output();

	die();
}
