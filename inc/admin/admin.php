<?php
namespace YSWS\Admin;

add_action( 'admin_init', __NAMESPACE__ . '\\on_admin_init' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_menu_pages' );
add_filter( 'plugin_action_links_' . SWS_PLUGIN_BASENAME, __NAMESPACE__ . '\\add_action_links' );
add_filter( 'admin_title', __NAMESPACE__ . '\\change_admin_title', 10, 2 );

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
	add_menu_page(
		__( 'Smart Search', 'smart-woocommerce-search' ),
		__( 'Smart Search', 'smart-woocommerce-search' ) . ( sws_fs()->is_premium() ? ' <sup>PRO</sup>' : '' ),
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
    if ( !sws_fs()->is_premium() ) {
        add_submenu_page(
            'smart-search',
            __( 'Start Trial', 'smart-woocommerce-search' ),
            __( 'Start Trial&nbsp;&nbsp;➤', 'smart-woocommerce-search' ),
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
function add_action_links(  $links  ) {
    $links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=smart-search' ), __( 'Settings', 'smart-woocommerce-search' ) );
    return $links;
}

/**
 * Filter Admin title
 */
function change_admin_title(  $admin_title, $title  ) {
    $is_smart_search = false;
    $cur_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    if ( $cur_page && 'smart-search' === $cur_page ) {
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( $action && 'edit' === $action && !empty( $id ) ) {
            $is_smart_search = true;
            if ( ysm_get_default_widgets_names( $id ) ) {
                $id = ysm_get_default_widgets_names( $id );
            }
            /* translators: %s: Name/id of a widget */
            $title = sprintf( __( 'Edit Widget: %s', 'smart-woocommerce-search' ), $id );
        }
    }
    if ( $is_smart_search ) {
        if ( is_network_admin() ) {
            /* translators: %s: Name of a site */
            $admin_title = sprintf( __( 'Network Admin: %s', 'smart-woocommerce-search' ), esc_html( get_current_site()->site_name ) );
        } elseif ( is_user_admin() ) {
            /* translators: %s: Name of a site */
            $admin_title = sprintf( __( 'User Dashboard: %s', 'smart-woocommerce-search' ), esc_html( get_current_site()->site_name ) );
        } else {
            $admin_title = get_bloginfo( 'name' );
        }
        /* translators: Admin screen title. 1: Admin screen name, 2: Network or site name. */
        $admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress', 'smart-woocommerce-search' ), $title, $admin_title );
    }
    return $admin_title;
}
