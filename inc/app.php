<?php
namespace YSWS\App;

const VERSION = '1.0.1';

function set_version( $ver ) {
	if ( version_compare( $ver, VERSION, '<' ) ) {
		return VERSION;
	}
	return $ver;
}
add_filter( 'yummywp_app_version', __NAMESPACE__ . '\\set_version' );

function init_app() {
	$version = apply_filters( 'yummywp_app_version', VERSION );
	if ( VERSION === $version ) {
		if ( ! class_exists( '\YummyWP\App\Field' ) ) {
			include_once __DIR__ . '/app/index.php';
		}
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_app', 10 );
