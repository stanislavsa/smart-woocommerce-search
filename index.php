<?php
/**
 * Plugin Name: Smart WooCommerce Search
 * Plugin URI:  https://yummywp.com/plugins/smart-woocommerce-search/
 * Description: Ajax Smart WooCommerce Search allows you to instantly search products.
 * Tags: woocommerce search, ajax search, woocommerce, woocommerce search by sku, woocommerce search shortcod, product search, product filter, woocommerce search results, instant search, woocommerce search plugin, woocommerce search form, search for woocommerce, woocommerce search page, search, woocommerce product search, search woocommerce, shop, shop search, autocomplete, autosuggest, search for wp, search for WordPress, search plugin, woocommerce search by sku, search results,  woocommerce search shortcode, search products, search autocomplete, woocommerce advanced search, woocommerce predictive search, woocommerce live search, woocommerce single product, woocommerce site search, products, shop, category search, custom search, predictive search, relevant search, search product, woocommerce plugin, posts search, wp search, WordPress search
 * Author:      YummyWP
 * Author URI:  https://yummywp.com
 * Version:     1.5.10
 * Domain Path: /languages
 * Text Domain: smart_search
 *
 * WC requires at least: 2.2
 * WC tested up to: 3.6
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check whether pro version is activated
 */
if ( defined( 'YSM_PRO' ) ) {
	function ysm_pro_version_installed_notice() {
		?>
		<div class="error">
			<p><?php _e( 'To activate the free version of Smart WooCommerce Search you need to deactivate the Pro version.', 'smart_search' ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'ysm_pro_version_installed_notice' );
	deactivate_plugins( plugin_basename( __FILE__ ) );
	return;
}

/**
 * Define main constants
 */
if ( ! defined( 'YSM_VER' ) ) {
	define('YSM_VER', 'ysm-1.5.7');
}

if ( ! defined( 'YSM_DIR' ) ) {
	define( 'YSM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YSM_URI' ) ) {
	define( 'YSM_URI', plugin_dir_url( __FILE__ ) );
}

include_once YSM_DIR . 'inc/functions.php';
include_once YSM_DIR . 'inc/query-hooks.php';
include_once YSM_DIR . 'inc/class-ysm-search.php';
include_once YSM_DIR . 'inc/class-ysm-setting.php';
include_once YSM_DIR . 'inc/class-ysm-message.php';
include_once YSM_DIR . 'inc/class-ysm-widget-manager.php';
include_once YSM_DIR . 'inc/class-ysm-custom-widget-manager.php';
include_once YSM_DIR . 'inc/class-ysm-search-widget.php';
include_once YSM_DIR . 'inc/class-ysm-style-generator.php';

/**
 * Load plugin textdomain.
 */
if ( ! function_exists( 'ysm_load_textdomain' ) ) {
	function ysm_load_textdomain() {
		load_plugin_textdomain( 'smart_search', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	add_action( 'plugins_loaded', 'ysm_load_textdomain' );
}

/**
 * Add Pages to Admin Menu
 */
if ( ! function_exists( 'ysm_add_menu_page' ) ) {
	function ysm_add_menu_page() {
		add_menu_page( __( 'Smart Search', 'smart_search' ),
			__( 'Smart Search', 'smart_search' ),
			'manage_options',
			'smart-search',
			null,
			'dashicons-search',
			39 );

		add_submenu_page( 'smart-search',
			__( 'Default Search Widget Settings', 'smart_search' ),
			__( 'Default', 'smart_search' ),
			'manage_options',
			'smart-search',
			'ysm_display_admin_page_default',
			'',
			1 );

		add_submenu_page( 'smart-search',
			__( 'Custom Search Widgets', 'smart_search' ),
			__( 'Custom Widgets', 'smart_search' ),
			'manage_options',
			'smart-search-custom',
			'ysm_display_admin_page_custom',
			'',
			2 );

		add_submenu_page( 'smart-search',
			__( 'Add New Search Widget', 'smart_search' ),
			__( 'Add New', 'smart_search' ),
			'manage_options',
			'smart-search-custom-new',
			'ysm_display_admin_page_custom_new',
			'',
			3 );
	}
}

if ( ! function_exists( 'ysm_display_admin_page_default' ) ) {
	function ysm_display_admin_page_default() {
		include_once YSM_DIR . 'templates/admin-page-default.php';
	}
}

if ( ! function_exists( 'ysm_display_admin_page_custom' ) ) {
	function ysm_display_admin_page_custom() {
		include_once YSM_DIR . 'templates/admin-page-custom.php';
	}
}

if ( ! function_exists( 'ysm_display_admin_page_custom_new' ) ) {
	function ysm_display_admin_page_custom_new() {
		include_once YSM_DIR . 'templates/admin-page-custom-new.php';
	}
}

add_action('admin_menu', 'ysm_add_menu_page');

/**
 * Include Front Scripts
 */
if ( ! function_exists( 'ysm_enqueue_scripts' ) ) {
	function ysm_enqueue_scripts() {
		wp_enqueue_style( 'smart-search', YSM_URI . 'assets/css/general.css', array(), YSM_VER );

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'smart-search-autocomplete', YSM_URI . 'assets/js/jquery.autocomplete.js', array( 'jquery' ), false, 1 );
			wp_enqueue_script( 'smart-search-custom-scroll', YSM_URI . 'assets/js/jquery.nanoscroller.js', array( 'jquery' ), false, 1 );
			wp_enqueue_script( 'smart-search-general', YSM_URI . 'assets/js/general.js', array( 'jquery' ), time(), 1 );
		} else {
			wp_enqueue_script( 'smart-search-general', YSM_URI . 'assets/js/min/main.min.js', array( 'jquery' ), YSM_VER, 1 );
		}

		if ( ysm_is_woocommerce_active() && version_compare( WC()->version, '2.4.0', '>' ) ) {
			$ajaxurl = WC_AJAX::get_endpoint( '' ) . '=';
		} else {
			$ajaxurl = admin_url( 'admin-ajax.php', 'relative' ) . '?action=';
		}

		$localized                          = array();
		$localized['ajaxurl']               = $ajaxurl;
		$localized['enable_search']         = (int) ysm_get_option( 'default', 'enable_search' );
		$localized['enable_product_search'] = (int) ysm_get_option( 'product', 'enable_product_search' );
		$localized['loader_icon']           = YSM_URI . 'assets/images/loader6.gif';

		$custom_widgets = ysm_get_custom_widgets();
		$def_widgets    = ysm_get_default_widgets();
		$widgets        = $custom_widgets + $def_widgets;

		foreach ( $widgets as $k => $v ) {

			if ( $k === 'default' ) {
				$css_id  = '.widget_search.ysm-active';
				$js_pref = '';
			} elseif ( $k === 'product' ) {
				$css_id  = '.widget_product_search.ysm-active';
				$js_pref = 'product_';
			} else {
				$css_id  = '.ysm-search-widget-' . $k;
				$js_pref = 'custom_' . $k . '_';
			}

			if ( isset( $v['settings']['char_count'] ) ) {
				$localized[ $js_pref . 'char_count' ] = (int) $v['settings']['char_count'];
			}

			if ( isset( $v['settings']['no_results_text'] ) ) {
				$localized[ $js_pref . 'no_results_text' ] = __( $v['settings']['no_results_text'], 'smart_search' );
			}

			$pt_list = array();

			if ( ! empty( $v['settings']['post_type_product'] ) ) {
				$pt_list['product'] = $v['settings']['post_type_product'];
			}

			if ( ! empty( $v['settings']['post_type_post'] ) ) {
				$pt_list['post'] = $v['settings']['post_type_post'];
			}

			if ( ! empty( $v['settings']['post_type_page'] ) ) {
				$pt_list['page'] = $v['settings']['post_type_page'];
			}

			if ( isset( $pt_list['product'] ) && empty( $v['settings']['search_page_layout_posts'] ) ) {
				$localized[ $js_pref . 'layout' ] = 'product';
			}

			/* input styles */

			if ( isset( $v['settings']['input_border_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.search-field[type="search"]',
					),
					'props'     => array(
						'border-color' => $v['settings']['input_border_color'],
					),
				) );
			}

			if ( ! empty( $v['settings']['input_border_width'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.search-field[type="search"]',
					),
					'props'     => array(
						'border-width' => intval( $v['settings']['input_border_width'] ) . 'px',
					),
				) );
			}

			if ( isset( $v['settings']['input_text_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.search-field[type="search"]',
					),
					'props'     => array(
						'color' => $v['settings']['input_text_color'],
					),
				) );
			}

			if ( ! empty( $v['settings']['input_bg_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.search-field[type="search"]',
					),
					'props'     => array(
						'background-color' => $v['settings']['input_bg_color'],
					),
				) );
			}

			if ( isset( $v['settings']['input_icon_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.search-submit',
					),
					'props'     => array(
						'color' => $v['settings']['input_icon_color'],
					),
				) );
			}

			/* popup styles */

			if ( isset( $v['settings']['popup_thumb_size'] ) ) {
				$th_size = (int) $v['settings']['popup_thumb_size'];
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-suggestions .smart-search-post-icon',
					),
					'props'     => array(
						'width' => ! empty( $th_size ) ? $th_size . 'px' : '100%',
					),
				) );
			}

			if ( isset( $v['settings']['popup_border_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-suggestions',
					),
					'props'     => array(
						'border-color' => $v['settings']['popup_border_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_bg_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-suggestions',
					),
					'props'     => array(
						'background-color' => $v['settings']['popup_bg_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_title_text_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-post-title',
					),
					'props'     => array(
						'color' => $v['settings']['popup_title_text_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_desc_text_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-post-excerpt',
					),
					'props'     => array(
						'color' => $v['settings']['popup_desc_text_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_price_text_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-post-price',
						'.smart-search-post-price .woocommerce-Price-amount',
						'.smart-search-post-price .woocommerce-Price-currencySymbol',
					),
					'props'     => array(
						'color' => $v['settings']['popup_price_text_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_view_all_link_text_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-view-all',
					),
					'props'     => array(
						'color' => $v['settings']['popup_view_all_link_text_color'],
					),
				) );
			}

			if ( isset( $v['settings']['popup_view_all_link_bg_color'] ) ) {
				Ysm_Style_Generator::add_rule( $css_id, array(
					'selectors' => array(
						'.smart-search-view-all',
					),
					'props'     => array(
						'background-color' => $v['settings']['popup_view_all_link_bg_color'],
					),
				) );
			}
		}

		wp_localize_script( 'smart-search-general', 'ysm_L10n', $localized );

		$styles = Ysm_Style_Generator::create();

		wp_add_inline_style( 'smart-search', $styles );

	}

	add_action('wp_enqueue_scripts', 'ysm_enqueue_scripts');
}

/**
 * Include Admin Scripts
 */
if ( ! function_exists( 'ysm_admin_enqueue_scripts' ) ) {
	function ysm_admin_enqueue_scripts() {

		if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'smart-search' ) === false ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'smart-search-admin', YSM_URI . 'assets/css/ysm-admin.css' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'smart-search-admin', YSM_URI . 'assets/js/ysm-admin.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'jquery-ui-slider',
			'underscore',
			'wp-color-picker',
			'wp-util'
		), false, 1 );

		$ysm_L10n = array(
			'column_delete' => __( 'Delete column?', 'smart_search' ),
			'row_delete'    => __( 'Delete row?', 'smart_search' ),
			'widget_delete' => __( 'Delete widget?', 'smart_search' ),
		);
		wp_localize_script( 'smart-search-admin', 'ysm_L10n', $ysm_L10n );
	}

	add_action('admin_enqueue_scripts', 'ysm_admin_enqueue_scripts');
}

/**
 * Filter Admin title
 */
if ( ! function_exists( 'ysm_change_admin_title' ) ) {
	function ysm_change_admin_title( $admin_title, $title ) {

		$is_smart_search = false;

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'smart-search-custom' ) {

			if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {

				$is_smart_search = true;
				$title           = __( 'Edit Custom Search Widget', 'smart_search' );

			}

		}

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'smart-search' ) {

			if ( isset( $_GET['tab'] ) ) {

				$tabs = array(
					'default' => __( 'Default Search', 'smart_search' ),
					'product' => __( 'Product Search', 'smart_search' ),
				);

				$is_smart_search = true;
				$title           = sprintf( __( 'Edit %s Search Widget', 'smart_search' ), $tabs[ $_GET['tab'] ] );

			}

		}

		if ( $is_smart_search ) {
			if ( is_network_admin() ) {
				$admin_title = sprintf( __( 'Network Admin: %s', 'smart_search' ), esc_html( get_current_site()->site_name ) );

			} elseif ( is_user_admin() ) {
				$admin_title = sprintf( __( 'User Dashboard: %s', 'smart_search' ), esc_html( get_current_site()->site_name ) );

			} else {
				$admin_title = get_bloginfo( 'name' );
			}


			$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress', 'smart_search' ), $title, $admin_title );
		}

		return $admin_title;

	}

	add_filter( 'admin_title', 'ysm_change_admin_title', 10, 2 );
}

/**
 * Change the admin footer text on Smart Search admin pages.
 */
if ( ! function_exists( 'ysm_change_admin_footer_text' ) ) {
	function ysm_change_admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		$ysm_screens    = array(
			'toplevel_page_smart-search',
			'smart-search_page_smart-search-custom',
			'smart-search_page_smart-search-custom-new',
		);

		if ( isset( $current_screen->id ) && in_array( $current_screen->id, $ysm_screens ) ) {
			//if ( ! get_option( 'ysm_admin_footer_text_rate' ) ) {
			$rate_link   = '<a href="https://wordpress.org/support/plugin/smart-woocommerce-search/reviews?rate=5#new-post" target="_blank" id="ysm-rate-plugin">&#9733;&#9733;&#9733;&#9733;&#9733;</a>';
			$footer_text = sprintf( 'If you like <strong>Smart Search</strong> plugin please leave us a %s rating.', $rate_link );
			//}
		}

		return $footer_text;
	}

	add_filter( 'admin_footer_text', 'ysm_change_admin_footer_text', 1 );
}

/**
 * Init Search
 */
Ysm_Setting::init();
Ysm_Widget_Manager::init();
Ysm_Custom_Widget_Manager::init();
Ysm_Search::init();
