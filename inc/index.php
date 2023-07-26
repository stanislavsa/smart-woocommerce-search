<?php
namespace YSMF_APP;

const VERSION = '1.0.0';

function set_ymapp_version( $ver ) {
	if ( version_compare( $ver, VERSION, '<' ) ) {
		return VERSION;
	}
	return $ver;
}
add_filter( 'ymapp_version', __NAMESPACE__ . '\\set_ymapp_version' );

function init_app() {
	$version = apply_filters( 'ymapp_version', VERSION );
	if ( VERSION === $version ) {
		if ( ! class_exists( 'YMAPP\Abstract_Setting_Field' ) ) {
			include_once __DIR__ . '/app/index.php';
		}
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_app', 10 );

// core
include_once __DIR__ . '/custom/functions.php';
include_once __DIR__ . '/custom/hooks.php';
include_once __DIR__ . '/custom/rest.php';
include_once __DIR__ . '/custom/inline-styles-functions.php';
include_once __DIR__ . '/custom/class-ysm-search.php';
include_once __DIR__ . '/custom/class-ysm-db.php';
include_once __DIR__ . '/custom/class-ysm-widget-manager.php';
include_once __DIR__ . '/custom/class-ysm-search-widget.php';
include_once __DIR__ . '/custom/class-ysm-style-generator.php';
\Ysm_Widget_Manager::init();
\Ysm_Search::init();

// compatibility with other plugins
include_once __DIR__ . '/compat/compat-visual-composer.php';
include_once __DIR__ . '/compat/compat-elementor.php';

// elements
include_once __DIR__ . '/elements/view-all-button.php';
include_once __DIR__ . '/elements/title.php';
include_once __DIR__ . '/elements/category.php';
include_once __DIR__ . '/elements/excerpt.php';
include_once __DIR__ . '/elements/thumbnail.php';
include_once __DIR__ . '/elements/product-price.php';
include_once __DIR__ . '/elements/product-sku.php';

// admin hooks
include_once __DIR__ . '/admin/class-field.php';
include_once __DIR__ . '/admin/class-notification.php';
include_once __DIR__ . '/admin/admin.php';
