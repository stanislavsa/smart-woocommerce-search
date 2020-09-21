<?php
namespace YMAPP;

include_once __DIR__ . '/class-abstract-setting-field.php';
include_once __DIR__ . '/class-page-setting-field.php';
include_once __DIR__ . '/class-admin-notice.php';
include_once __DIR__ . '/class-plugin-option.php';
include_once __DIR__ . '/functions.php';

if ( ! function_exists( __NAMESPACE__ . '\\admin_init' ) ) :
	/**
	 * Init settings
	 */
	function admin_init() {
		Plugin_Option::init();
	}
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\admin_init' );
endif;
