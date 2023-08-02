<?php
/**
 * Plugin Name: Smart WooCommerce Search
 * Plugin URI:  https://yummywp.com/plugins/smart-woocommerce-search/
 * Description: Ajax Smart WooCommerce Search allows you to instantly search products.
 * Tags: woocommerce search, ajax search, woocommerce, woocommerce search by sku, woocommerce search shortcod, product search, product filter, woocommerce search results, instant search, woocommerce search plugin, woocommerce search form, search for woocommerce, woocommerce search page, search, woocommerce product search, search woocommerce, shop, shop search, autocomplete, autosuggest, search for wp, search for WordPress, search plugin, woocommerce search by sku, search results,  woocommerce search shortcode, search products, search autocomplete, woocommerce advanced search, woocommerce predictive search, woocommerce live search, woocommerce single product, woocommerce site search, products, shop, category search, custom search, predictive search, relevant search, search product, woocommerce plugin, posts search, wp search, WordPress search
 * Author:      YummyWP
 * Author URI:  https://yummywp.com
 * Version:     2.6.3
 * Domain Path: /languages
 * Text Domain: smart-woocommerce-search
 *
 * Requires PHP: 5.4
 *
 * WC requires at least: 4.0
 * WC tested up to: 7.9
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check whether pro version is activated
 */
if ( defined( 'YSM_PRO' ) ) {
	function ysm_pro_version_installed_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'To activate the free version of Smart WooCommerce Search you need to deactivate the Pro version.', 'smart-woocommerce-search' ); ?></p>
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
	define( 'YSM_VER', 'ysws-2.6.3' );
}

if ( ! defined( 'YSM_DIR' ) ) {
	define( 'YSM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YSM_URI' ) ) {
	define( 'YSM_URI', plugin_dir_url( __FILE__ ) );
}

include_once YSM_DIR . 'inc/index.php';

/**
 * Load plugin textdomain.
 */
if ( ! function_exists( 'ysm_load_textdomain' ) ) {
	function ysm_load_textdomain() {
		load_plugin_textdomain( 'smart-woocommerce-search', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	add_action( 'plugins_loaded', 'ysm_load_textdomain' );
}

/**
 * Include Front Scripts
 */
if ( ! function_exists( 'ysm_enqueue_scripts' ) ) {
	function ysm_enqueue_scripts() {
		wp_enqueue_style( 'smart-search', YSM_URI . 'assets/dist/css/general.css', array(), YSM_VER );

		$rest_url = rest_url( 'ysm/v1/search' ) . '?';

		$localized                          = array();
		$localized['restUrl']               = $rest_url;
		$localized['loader_icon']           = YSM_URI . 'assets/images/loader6.gif';

		$active_widgets  = ysm_get_custom_widgets();
		$default_widgets = ysm_get_default_widgets();

		$available_default_widgets_ids = ysm_get_default_widgets_ids();

		foreach ( $default_widgets as $w_key => $default_widget ) {
			if ( isset( $available_default_widgets_ids[ $w_key ] ) ) {
				$enable_key = ( 'default' === $w_key ) ? 'enable_search' : 'enable_' . $w_key . '_search';
				if ( ! empty( $default_widget['settings'][ $enable_key ] ) ) {
					$active_widgets[ $w_key ] = $default_widget;
				}
			}
		}

		foreach ( $active_widgets as $k => $v ) {

			if ( $k === 'default' ) {
				$css_id  = '.widget_search.ysm-active';
				$js_pref = '';
			} elseif ( $k === 'product' ) {
				$css_id  = '.widget_product_search.ysm-active';
				$js_pref = $k . '_';
			} elseif ( $k === 'avada' ) {
				$css_id  = '.fusion-search-form.ysm-active';
				$js_pref = $k . '_';
			} else {
				$css_id  = '.ysm-search-widget-' . $k;
				$js_pref = 'custom_' . $k . '_';
			}

			if ( false === strpos( $js_pref, 'custom_' ) ) {
				$localized[ 'enable_' . $js_pref . 'search' ] = 1;
			}

			// check if AJAX disabled
			if ( ! empty( $v['settings']['disable_ajax'] ) ) {
				$localized[ $js_pref . 'disable_ajax' ] = (bool) $v['settings']['disable_ajax'];
			} else {
				$localized[ $js_pref . 'disable_ajax' ] = false;
			}

			if ( isset( $v['settings']['char_count'] ) ) {
				$localized[ $js_pref . 'char_count' ] = (int) $v['settings']['char_count'];
			}

			if ( isset( $v['settings']['no_results_text'] ) ) {
				$localized[ $js_pref . 'no_results_text' ] = __( $v['settings']['no_results_text'], 'smart-woocommerce-search' );
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

			if ( ! empty( $v['settings']['search_page_default_output'] ) ) {
				$localized[ $js_pref . 'default_output' ] = (bool) $v['settings']['search_page_default_output'];
			}

			ysm_add_inline_styles_to_stack( $v, $css_id );
		}

		$styles = Ysm_Style_Generator::create();

		wp_add_inline_style( 'smart-search', $styles );

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'smart-search-autocomplete', YSM_URI . 'assets/src/js/jquery.autocomplete.js', array( 'jquery' ), false, 1 );
			wp_enqueue_script( 'smart-search-custom-scroll', YSM_URI . 'assets/src/js/jquery.nanoscroller.js', array( 'jquery' ), false, 1 );
			wp_enqueue_script( 'smart-search-general', YSM_URI . 'assets/src/js/general.js', array( 'jquery' ), time(), 1 );
		} else {
			wp_enqueue_script( 'smart-search-general', YSM_URI . 'assets/dist/js/main.js', array( 'jquery' ), YSM_VER, 1 );
		}

		wp_localize_script( 'smart-search-general', 'ysm_L10n', $localized );

	}
	add_action( 'wp_enqueue_scripts', 'ysm_enqueue_scripts' );
}

/**
 * Include Admin Scripts
 */
if ( ! function_exists( 'ysm_admin_enqueue_scripts' ) ) {
	function ysm_admin_enqueue_scripts() {
		$cur_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $cur_page || false === strpos( $cur_page, 'smart-search' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'smart-search-admin', YSM_URI . 'assets/dist/css/admin.css', [], YSM_VER );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'smart-search-admin', YSM_URI . 'assets/dist/js/admin.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'jquery-ui-slider',
			'underscore',
			'wp-color-picker',
			'wp-util',
		), YSM_VER, 1 );

		wp_localize_script( 'smart-search-admin', 'ysm_L10n', array(
			'column_delete' => __( 'Delete column?', 'smart-woocommerce-search' ),
			'row_delete'    => __( 'Delete row?', 'smart-woocommerce-search' ),
			'widget_delete' => __( 'Delete widget?', 'smart-woocommerce-search' ),
		) );

		// Select2
		wp_enqueue_style( 'ysrs-select2', YSM_URI . 'assets/dist/css/select2.min.css', array(), YSM_VER );
		wp_enqueue_script( 'ysrs-select2', YSM_URI . 'assets/dist/js/select2.min.js', array(), YSM_VER, true );
	}
	add_action( 'admin_enqueue_scripts', 'ysm_admin_enqueue_scripts' );
}

/**
 * Filter Admin title
 */
if ( ! function_exists( 'ysm_change_admin_title' ) ) {
	function ysm_change_admin_title( $admin_title, $title ) {
		$is_smart_search = false;
		$cur_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $cur_page && 'smart-search' === $cur_page ) {
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( $action && 'edit' === $action && ! empty( $id ) ) {
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
 * Add plugin action links
 * @param $links
 * @return array
 */
if ( ! function_exists( 'ysm_plugin_action_links' ) ) {
	function ysm_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=smart-search' ), __( 'Settings', 'smart-woocommerce-search' ) );
		$links[] = sprintf( '<a href="%s" target="_blank" class="ysm-update-to-pro-link">%s</a>', 'https://yummywp.com/plugins/smart-woocommerce-search/#smart-search-compare', __( 'Upgrade to Pro', 'smart-woocommerce-search' ) );

		return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ysm_plugin_action_links' );
}

function ysm_admin_head() {
	?>
	<style>
		.ysm-update-to-pro-link,
		#toplevel_page_smart-search a[href="admin.php?page=smart-search-update-to-pro"] {
			color: red;
			font-weight: bold;
		}
	</style>
	<?php
}
add_filter( 'admin_head', 'ysm_admin_head' );

if ( ! function_exists( 'ysm_gettext' ) ) {
	function ysm_gettext( $translation, $text, $domain ) {
		if ( ! $translation || $translation === $text ) {
			$translations = get_translations_for_domain( 'smart_search' );
			$fallback_translation  = $translations->translate( $text );
			if ( $fallback_translation && $fallback_translation !== $text ) {
				$translation = $fallback_translation;
			}
		}

		return $translation;
	}
	add_filter( 'gettext_smart-woocommerce-search', 'ysm_gettext', 99, 3 );
}

/**
 * Declaring plugin compatibility with WooCommerce Features
 */
if ( ! function_exists( 'ysm_wc_features_compatibility' ) ) {
	function ysm_wc_features_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			// High-Performance order storage (COT)
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			// New product editor
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', __FILE__, true );
		}
	}
	add_action( 'before_woocommerce_init', 'ysm_wc_features_compatibility' );
}
