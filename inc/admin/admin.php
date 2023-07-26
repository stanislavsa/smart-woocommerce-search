<?php
namespace YSWS\Admin;

add_action( 'admin_init', __NAMESPACE__ . '\\on_admin_init' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_menu_pages' );

/**
 * Admin init hook
 * @return void
 */
function on_admin_init() {

}

/**
 * Add Pages to Admin Menu
 */
function add_menu_pages() {
	add_menu_page( __( 'Smart Search', 'smart-woocommerce-search' ),
		__( 'Smart Search', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search',
		null,
		'dashicons-search',
		'39.9'
	);

	add_submenu_page( 'smart-search',
		__( 'Search Widgets', 'smart-woocommerce-search' ),
		__( 'Widgets', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search',
		__NAMESPACE__ . '\\display_admin_page_widgets'
	);

	add_submenu_page( 'smart-search',
		__( 'Add New Search Widget', 'smart-woocommerce-search' ),
		__( 'Add New', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-custom-new',
		__NAMESPACE__ . '\\display_admin_page_widget_new'
	);

	add_submenu_page( 'smart-search',
		__( 'Synonyms', 'smart-woocommerce-search' ),
		__( 'Synonyms', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-synonyms',
		__NAMESPACE__ . '\\display_admin_page_synonyms'
	);

	add_submenu_page( 'smart-search',
		__( 'Stop Words', 'smart-woocommerce-search' ),
		__( 'Stop Words', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-stop-words',
		__NAMESPACE__ . '\\display_admin_page_stop_words'
	);

	add_submenu_page( 'smart-search',
		__( 'Upgrade to Pro', 'smart-woocommerce-search' ),
		__( 'Upgrade to Pro', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-update-to-pro',
		__NAMESPACE__ . '\\display_admin_page_update_to_pro'
	);
}

function display_admin_page_widgets() {
	include_once YSM_DIR . 'templates/admin-page-widgets.php';
}

function display_admin_page_widget_new() {
	include_once YSM_DIR . 'templates/admin-page-widget-new.php';
}

function display_admin_page_synonyms() {
	include_once YSM_DIR . 'templates/admin-page-synonyms.php';
}

function display_admin_page_stop_words() {
	include_once YSM_DIR . 'templates/admin-page-stop-words.php';
}

function display_admin_page_update_to_pro() {
	if ( 'smart-search-update-to-pro' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) {
		wp_redirect( 'https://yummywp.com/plugins/smart-woocommerce-search/#smart-search-compare' );
		die;
	}
}
