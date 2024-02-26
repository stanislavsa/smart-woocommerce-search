=== Smart WooCommerce Search ===
Contributors: yummy-wp
Tags: woocommerce search, ajax search, woocommerce, genesis, elementor, divi, avada, enfold, filters, product filter, woo search, relevanssi, jetpack, woocommerce search by sku, woocommerce search shortcod, product search, product filter, woocommerce search results, instant search, woocommerce search plugin, woocommerce search form, search for woocommerce, woocommerce search page, search, woocommerce product search, search woocommerce, shop, shop search, autocomplete, autosuggest, search for wp, search for wordpress, search plugin, woocommerce search by sku, search results,  woocommerce search shortcode, search products, search autocomplete, woocommerce advanced search, woocommerce predictive search, woocommerce live search, woocommerce single product, woocommerce site search, products, shop, category search, custom search, predictive search, relevant search, search product, woocommerce plugin, yith, woof, wp search, wordpress search
Stable tag: 2.8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.9
Tested up to: 6.4
Requires PHP: 7.0

Smart WooCommerce Search enhances your users' experience with a sophisticated AJAX search bar featuring real-time suggestions.

== Description ==

= Smart WooCommerce Search enhances your users' experience with a sophisticated AJAX search bar featuring real-time suggestions. =

[Check Demo](https://smart-woocommerce-search.yummywp.com/)

While WooCommerce's default search is basic, lacking live product search and SKU search functionality, **Smart WooCommerce Search** delivers advanced search capabilities along with live suggestions.

In today's market, instant search suggestions are essential. According to the latest research, 96% of major e-commerce sites now offer search autocomplete, auto-suggest, or instant search features. This functionality is crucial for retaining customers and optimizing sales by saving users time and streamlining the shopping process.

= Features Free: =

 * Search results with images and prices
 * Simple integration into default search widgets
 * Elementor compatible
 * DIVI compatible
 * WPBakery Page Builder compatible
 * Visual Composer compatible
 * Search by product SKU
 * Search in product Categories
 * Search in product Tags
 * Multisite support
 * Allow/disallow searching in selected product categories
 * Exclude "Out of stock" products from search results
 * Multiple words search
 * Search Exclude plugin compatible

This plugin is indispensable for sites with a large number of product items.

You can completely customize your smart search. In addition, the plugin integrates seamlessly into the WooCommerce Product Search widget.

= Upgrade to PRO and boost your sales: =

   * Search in Variations
   * Search in Custom Post Types
   * Search in Custom Taxonomies
   * Search in Custom Fields
   * Search in Product Attributes
   * Search in Product Brands
   * Product labels
   * Synonyms
   * Stop words
   * Advanced caching
   * WPML compatibility
   * Polylang compatibility
   * ACF plugin compatibility
   * Custom Post Type UI plugin compatibility
   * Ultimate WooCommerce Brands plugin compatibility
   * Add to Cart button
   * More customization and styling settings
   * 1 year of support
   * 1 year of updates

[Plugin Documentation](https://yummywp.com/docs/smart-search/)

Do you have any questions or concerns? Feel free to contact us via the [contact form](https://www.wpsmartsearch.com/contact/).

== Installation ==

1. Unzip the downloaded .zip file.
2. Upload the plugin folder to the `wp-content/plugins/` directory of your WordPress site.
3. Go to the 'Plugins' menu in WordPress and activate the Smart WooCommerce Search plugin.
4. Configure the plugin (/wp-admin/admin.php?page=smart-search)
5. Place the search plugin shortcode `<?php echo do_shortcode('[smart_search id="1"]');  ?>` into your template or simply use the built-in widget.

== Frequently Asked Questions ==

= How to add Smart Search widget into the theme template (a PHP file) =

Just add these PHP code `<?php echo do_shortcode('[smart_search id="1"]');  ?>`
In example used widget with ID = 1, so don't forget to change widget ID to needed value.

= Is it compatible with my WordPress themes? =

Plugin not tested with all themes, but it easily integrated in any theme, and may be require some styling to make it match nicely.

= How to manage plugin settings? =

Please read a [documentation](http://yummywp.com/docs/smart-search/).

= Is there a way to show the ‘shop search output’ from WooCommerce, for example 3 items per row? =

Yes, when using products search or when you set option to search only through products in default or in custom search widget (shortcode) the search page displays WooCommerce product layout.

== Screenshots ==

1. Smart WooCommerce Search Custom Widget
2. Smart WooCommerce Search Settings Page - General tab
3. Smart WooCommerce Search Settings Page - Items to Search through tab
4. Smart WooCommerce Search Settings Page - Styling tab

== Changelog ==

= 2.8.0 =
* Fixed: issue with the "Highlight Search Terms on the Search Results Page" setting
* Tweak: Raised the minimum required PHP version to 7.0

= 2.7.0 =
* Added: option to disable default WooCommerce redirect to product page if there is only one search result
* Updates: small improvements

= 2.6.3 =
* Updates: bug fixes / code optimization / improvements

= 2.6.2 =
* Updates: php code optimization / improvements
* Updates: caching improvements

= 2.6.1 =
* Added: Declaration for WooCommerce High-Performance order storage compatibility

= 2.6.0 =
* Added: "Display Category" and "Category Color" options to output product/post category in the search results popup

= 2.5.2 =
* Fixed: deprecation warnings related to PHP 8
* Compatibility: WooCommerce 7.8

= 2.5.1 =
* Fixed: vulnerability issue related to missing capability checks on the duplicate() & remove() functions called via AJAX actions

= 2.5.0 =
* Added: target "_blank" option for "View All" button
* Compatibility: WooCommerce 7.5

= 2.4.0 =
* Added: hook for altering search results output 'ysm_suggestion_image_output'
* Compatibility: WooCommerce 7.0

= 2.3.1 =
* Fixed: issue when search results page doesn't display any products
* Updates: localization files and strings

= 2.3.0 =
* Fixed: Smart Search hooks priority issue when search results page doesn't displays needed products
* Fixed: issue when additional woocommerce filters do not applies to the products in search results
* Added: option to disable AJAX functionality (results popup)

= 2.2.7 =
* restored SKU search
* Compatibility: WooCommerce 5.9

= 2.2.6 =
* small fixes
* Compatibility: WooCommerce 5.8

= 2.2.5 =
* Fixed: issue with popup results click event
* Compatibility: WordPress 5.8
* Compatibility: WooCommerce 5.5

= 2.2.4 =
* Fixed: issue with non-latin encoding
* Compatibility: WooCommerce 5.2

= 2.2.3 =
* Fixed: errors while saving options on the multisite installation
* Tweaks: under the hood improvements

= 2.2.2 =
* Tweaks: optimizations for the query that runs on the search results page

= 2.2.1 =
* Fixed: tweaks for multiple words issue

= 2.2.0 =
* Added: option to extend default Avada's search bar
* Fixed: issue when multiple words separated with a space

= 2.1.1 =
* Fixed: fatal error when no custom widgets added

= 2.1.0 =
* Fixed: special character issue
* Updated: admin pages structure
* Compatibility: WooCommerce 5.0

= 2.0.3 =
* Tweaks: search results order
* Tweaks: input styles
* Compatibility: WooCommerce 4.8 and WordPress 5.6

= 2.0.2 =
* Fixed: popup height issue on mobiles
* Fixed: product link on the product title
* Compatibility: WooCommerce 4.7

= 2.0.1 =
* Fixed: issue with removing Elementor widget
* Fixed: error when searching in custom fields

= 2.0.0 =
* Database queries optimization
* Code optimization
* UI improvements
* Fixed: View All button link

= 1.6.2 =
* Tweaks: WooCommerce 4.4 compatibility
* small fixes

= 1.6.1 =
* WordPress 5.5 compatibility

= 1.6.0 =
* code refactoring and optimization
* Elementor compatibility
* Accessibility fix

= 1.5.20 =
* code refactoring and optimization
* WooCommerce 4.3 compatibility

= 1.5.19 =
* Fixed: issue related to single quote in a search terms
* code refactoring

= 1.5.18 =
* Fixed: issue when empty results on search page
* Fixed: empty placeholder in search bar
* Some code refactoring

= 1.5.17 =
* Fixed: search results page pre_get_posts filter

= 1.5.16 =
* Fixed: compatibility with Avada

= 1.5.15 =
* Updated: po file
* Small tweaks

= 1.5.14 =
* Compatibility with WooCommerce 4.0

= 1.5.13 =
* Added compatibility with WOOF plugin
* Fixed - issue with query parameters in View All button

= 1.5.12 =
* Fixed - WP 5.3 compatibility issue
* Fixed - issue with '+' symbol in the query

= 1.5.11 =
* Fixed - Polylang compatibility issue
* Fixed - Variations displaying when not in stock

= 1.5.10 =
* Fixed - DIVI shortcodes issue in search results

= 1.5.9 =
* Fixed - outputting variations when parent product in draft
* Code optimizations

= 1.5.8 =
* Improved - search results page output
* Improved - visibility and exclusion query parameters

= 1.5.7 =
* Fixed - search results page query
* Improved - autocomplete function

= 1.5.6 =
* Added - "Search Field Background Color" option
* Improved - "Fuzzy Search" option

= 1.5.5 =
* Fixed - php warning

= 1.5.4 =
* Fixed - results order (by title) issue when "fuzzy search" option enabled
* Fixed - issue on mobiles when results popup can't be closed

= 1.5.3 =
* Added - "Search Page Layout with Posts" option
* Fixed - displaying of "View all" link when no suggestions

= 1.5.2 =
* Fixed - "Disallowed Product Categories" option
* Fixed - searching by SKU

= 1.5.1 =
* Added "Disallowed Product Categories" option
* Support for WooCommerce 3.4.2

= 1.5.0 =
* Added excerpt position option

= 1.4.8 =
* Fixed duplicating of results when searching by SKU;
* Fixed outputting of all posts instead of no results;

= 1.4.7 =
* Fixed Search Page results amount issue

= 1.4.6 =
* Added Search Exclude plugin compatibility
* Under the hood improvements

= 1.4.5 =
* Fixed View All Button link
* Fixed Search results page output

= 1.4.4 =
* Speed optimizations
* Fixed form submitting link

= 1.4.3 =
* Added option - Exclude "Out of stock" products from results
* Under the hood improvements

= 1.4.2 =
* Fixed fuzzy search
* Fixed issue with '/' in search text

= 1.4.1 =
* Fixed syntax error for php version less than 5.4

= 1.4.0 =
* Added option "Fuzzy search (multiple word search)"
* Under the hood improvements

= 1.3.0 =
* Added option "Enable search through Variable Product Variations"
* Added option "Accent Words on Search Page"
* Under the hood improvements

= 1.2.3 =
* Fixed issue with a border in "No results" block

= 1.2.2 =
* Fixed issue with a special characters like "őűóíúéá"
* Fixed issue with a multibyte strings

= 1.2.1 =
* Fixed WooCommerce 3.0 compatibility issue, when new products didn't shows up in the search results
* Small improvements

= 1.2.0 =
* Added option to display a product SKU in the results popup
* Added option to restrict the product searching by selected categories
* Added option to set custom widget input border width
* Small improvements

= 1.1.3 =
* Prevent redirecting to the Search results page when a customer presses Enter and the number of typed characters less then characters number set in the plugin settings
* Fixed popup content scrolling by mouse wheel (Windows OS browsers)

= 1.1.2 =
* Fixed searching by sku
* Fixed css cross browser issues

= 1.1.1 =

* Fixed issue with fonts (fontello) conflict

= 1.1.0 =

* Changed search results page output - when using products search or when you set option to search only through products in default or in custom search widget (shortcode) the search page displays WooCommerce product layout
* Added option ("Display default search output on search results page") to disable plugin filter that overrides default WordPress or WooCommerce search query

= 1.0.0 =

* Initial release
