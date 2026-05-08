<?php
namespace YSWS\Core\Analytics;

add_action( 'wp_ajax_sws_ga_event_toggle', __NAMESPACE__ . '\\ga_event_toggle' );
add_action( 'wp_ajax_sws_ga4_toggle', __NAMESPACE__ . '\\ga4_toggle' );


/**
 * Toggle a single GA event on/off.
 * Disabled events are stored as an array in option `sws_ga_disabled_events`.
 */
function ga_event_toggle() {
	$event_key = filter_input( INPUT_POST, 'event_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$enabled   = filter_input( INPUT_POST, 'enabled', FILTER_VALIDATE_BOOLEAN );
	$nonce     = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'ysm_widgets_nonce_action' ) ) {
		wp_send_json_error( 'invalid_nonce', 403 );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}

	$allowed_events = [
		'search_term_has_results',
		'search_term_no_results',
		'promo_banner_click',
		'selected_categories_click',
		'recommended_products_click',
		'search_results_link_click',
		'search_results_cart_click',
		'view_all_click',
	];

	if ( ! in_array( $event_key, $allowed_events, true ) ) {
		wp_send_json_error( 'invalid_event', 400 );
	}

	$enabled_events = (array) get_option( 'sws_ga_enabled_events', [] );

	if ( $enabled ) {
		if ( ! in_array( $event_key, $enabled_events, true ) ) {
			$enabled_events[] = $event_key;
		}
	} else {
		$enabled_events = array_values( array_diff( $enabled_events, [ $event_key ] ) );
	}

	update_option( 'sws_ga_enabled_events', $enabled_events );
	wp_send_json_success();
}

/**
 * Toggle the master GA4 tracking option on/off.
 */
function ga4_toggle() {
	$enabled = filter_input( INPUT_POST, 'enabled', FILTER_VALIDATE_BOOLEAN );
	$nonce   = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'ysm_analytics_nonce_action' ) ) {
		wp_send_json_error( 'invalid_nonce', 403 );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}

	if ( $enabled ) {
		update_option( 'sws_enable_google_analytics', 1 );
	} else {
		delete_option( 'sws_enable_google_analytics' );
	}

	wp_send_json_success();
}
