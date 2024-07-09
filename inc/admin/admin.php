<?php
namespace YSWS\Admin;

add_action( 'admin_init', __NAMESPACE__ . '\\on_admin_init' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_menu_pages' );
add_filter( 'plugin_action_links_' . SWS_PLUGIN_BASENAME, __NAMESPACE__ . '\\add_action_links' );
add_filter( 'admin_title', __NAMESPACE__ . '\\change_admin_title', 10, 2 );

add_action( 'wp_ajax_sws_promo_dismiss', __NAMESPACE__ . '\\promo_dismiss' );

/**
 * Admin init hook
 * @return void
 */
function on_admin_init() {
	\YummyWP\App\Notification::add_template( SWS_PLUGIN_DIR . 'templates/promo/discount.php' );
}

/**
 * Add Pages to Admin Menu
 */
function add_menu_pages() {
	add_menu_page(
		'Smart Search',
		'Smart Search' . ( sws_fs()->is_premium() ? ' <sup>PRO</sup>' : '' ),
		'manage_options',
		'smart-search',
		null,
		'dashicons-search',
		'39.9'
	);
    add_submenu_page(
        'smart-search',
		__( 'Search Widgets', 'smart-woocommerce-search' ),
		__( 'Widgets', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search',
		__NAMESPACE__ . '\\display_admin_page_widgets'
	);
    add_submenu_page(
        'smart-search',
		__( 'Add New Search Widget', 'smart-woocommerce-search' ),
		__( 'Add New', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-custom-new',
		__NAMESPACE__ . '\\display_admin_page_widget_new'
	);
    add_submenu_page(
        'smart-search',
		__( 'Index Status', 'smart-woocommerce-search' ),
		__( 'Index Status', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-index-status',
		__NAMESPACE__ . '\\display_admin_page_index_status'
	);
    add_submenu_page(
        'smart-search',
		__( 'Synonyms', 'smart-woocommerce-search' ),
		__( 'Synonyms', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-synonyms',
		__NAMESPACE__ . '\\display_admin_page_synonyms'
	);
    add_submenu_page(
        'smart-search',
		__( 'Stop Words', 'smart-woocommerce-search' ),
		__( 'Stop Words', 'smart-woocommerce-search' ),
		'manage_options',
		'smart-search-stop-words',
		__NAMESPACE__ . '\\display_admin_page_stop_words'
	);
	if ( ! sws_fs()->is_premium() ) {
        add_submenu_page(
            'smart-search',
			__( 'Start Trial', 'smart-woocommerce-search' ),
			__( 'Start Trial', 'smart-woocommerce-search' ) . '&nbsp;&nbsp;➤',
			'manage_options',
			'smart-search-pro-trial',
			__NAMESPACE__ . '\\display_admin_page_update_to_pro'
		);
	}
}

function display_admin_page_widgets() {
	include_once SWS_PLUGIN_DIR . 'templates/admin-page-widgets.php';
}

function display_admin_page_widget_new() {
	include_once SWS_PLUGIN_DIR . 'templates/admin-page-widget-new.php';
}

function display_admin_page_synonyms() {
	include_once SWS_PLUGIN_DIR . 'templates/admin-page-synonyms.php';
}

function display_admin_page_stop_words() {
	include_once SWS_PLUGIN_DIR . 'templates/admin-page-stop-words.php';
}

function display_admin_page_index_status() {
	include_once SWS_PLUGIN_DIR . 'templates/admin-page-index-status.php';
}

function display_admin_page_update_to_pro() {
	if ( 'smart-search-pro-trial' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) {
		wp_redirect( sws_fs()->get_trial_url() );
		die;
	}
}

/**
 * Add plugin action links
 * @param $links
 * @return array
 */
function add_action_links( $links ) {
	$links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=smart-search' ), __( 'Settings', 'smart-woocommerce-search' ) );
	return $links;
}

/**
 * Filter Admin title
 */
function change_admin_title(  $admin_title, $title  ) {
	$cur_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	if ( $cur_page && 'smart-search' === $cur_page ) {
		$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $action && 'edit' === $action && !empty( $id ) ) {
			if ( ysm_get_default_widgets_names( $id ) ) {
				$id = ysm_get_default_widgets_names( $id );
			}
			/* translators: %s: Name/id of a widget */
			$admin_title = sprintf( __( 'Edit Widget: %s', 'smart-woocommerce-search' ), $id );
		}
	}

	return $admin_title;
}

function promo_dismiss() {
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'sws_promo_dismiss_nonce_action' ) ) {
		exit;
	}

	update_option( $name, 1 );

	exit;
}
