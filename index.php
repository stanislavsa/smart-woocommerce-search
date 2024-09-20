<?php
/**
 * Plugin Name: Smart WooCommerce Search
 * Plugin URI:  https://www.wpsmartsearch.com/
 * Description: Smart Ajax Search allows you to instantly search WooCommerce products.
 * Tags: woocommerce search, ajax search, woocommerce, woocommerce search by sku, woocommerce search shortcode, product search, product filter, woocommerce search results, instant search, woocommerce search plugin, woocommerce search form, search for woocommerce, woocommerce search page, search, woocommerce product search, search woocommerce, shop, shop search, autocomplete, autosuggest, search for wp, search for WordPress, search plugin, woocommerce search by sku, search results,  woocommerce search shortcode, search products, search autocomplete, woocommerce advanced search, woocommerce predictive search, woocommerce live search, woocommerce single product, woocommerce site search, products, shop, category search, custom search, predictive search, relevant search, search product, woocommerce plugin, posts search, wp search, WordPress search
 * Author:      YummyWP
 * Author URI:  https://www.wpsmartsearch.com/
 * Version:     2.11.7
 * Domain Path: /languages
 * Text Domain: smart-woocommerce-search
 * Requires at least: 5.9
 * Requires PHP: 7.0
 *
 * WC requires at least: 4.0
 * WC tested up to: 9.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether other versions is activated
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

if ( function_exists( 'sws_fs' ) ) {
	sws_fs()->set_basename( false, __FILE__ );
} else {
	if ( ! function_exists( 'sws_fs' ) ) {
		require_once __DIR__ . '/inc/fs.php';
	}

	/**
	 * Define main constants
	 */
	define( 'SWS_PLUGIN_VERSION', '2.11.7' );
	define( 'SWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'SWS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
	define( 'SWS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	/**
	 * Load plugin textdomain.
	 */
	function sws_plugin_load_textdomain() {
		load_plugin_textdomain( 'smart-woocommerce-search', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
	add_action( 'plugins_loaded', 'sws_plugin_load_textdomain' );

	/**
	 * Declaring plugin compatibility with WooCommerce Features
	 */
	function sws_plugin_wc_features_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			// High-Performance order storage (COT)
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			// New product editor
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', __FILE__, true );
		}
	}
	add_action( 'before_woocommerce_init', 'sws_plugin_wc_features_compatibility' );

	include_once __DIR__ . '/inc/app.php';
	include_once __DIR__ . '/inc/core.php';
}
