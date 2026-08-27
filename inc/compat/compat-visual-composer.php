<?php
/** Compatibility with Visual Composer Website Builder. */
namespace YSWS\Compat\VCWB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vcv:api', __NAMESPACE__ . '\\register_element' );

/**
 * Register the Smart Search element when Visual Composer initializes its API.
 *
 * @param object $api Visual Composer API factory.
 */
function register_element( $api ) {
	if ( ! is_object( $api ) ) {
		return;
	}

	$tag = 'smartSearch';
	$manifest_path = __DIR__ . '/elements/' . $tag . '/manifest.json';

	if ( ! is_readable( $manifest_path ) ) {
		return;
	}

	$plugin_base_url = defined( 'SWS_PLUGIN_URI' )
		? rtrim( SWS_PLUGIN_URI, '\\/' ) . '/inc/compat'
		: rtrim( plugins_url( '', __FILE__ ), '\\/' );

	// VCWB caches discovered element controllers. Clear that cache so changes
	// to this third-party element (including its PHP variable provider) load
	// immediately instead of waiting for the transient to expire.
	if ( function_exists( 'vchelper' ) ) {
		vchelper( 'Options' )->deleteTransient( 'elements:autoload:all' );
	}

	$api->elements->add(
		$manifest_path,
		$plugin_base_url . '/elements/' . $tag
	);
}
