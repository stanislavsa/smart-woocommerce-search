=== Smart WooCommerce Search ===
Contributors: yummy-wp
Tags: woocommerce search, ajax search, woocommerce, genesis, elementor, divi, avada, enfold, filters, product filter, woo search, woocommerce search by sku, woocommerce search shortcod, product search, product filter, woocommerce search results, instant search, woocommerce search plugin, woocommerce search form, search for woocommerce, woocommerce search page, search, woocommerce product search, search woocommerce, shop, shop search, autocomplete, autosuggest, search for wp, search for wordpress, search plugin, woocommerce search by sku, search results,  woocommerce search shortcode, search products, search autocomplete, woocommerce advanced search, woocommerce predictive search, woocommerce live search, woocommerce single product, woocommerce site search, products, shop, category search, custom search, predictive search, relevant search, search product, woocommerce plugin, yith, woof, wp search, wordpress search
Requires at least: 4.2
Tested up to: 5.5
Stable tag: 1.6.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart WooCommerce Search allows you to instantly search products.

== Description ==

= Improve customer experience and boost your sales with Smart WooCommerce Search plugin for WordPress websites. Powerful, amazing and efficient! =

[Read more](https://yummywp.com/plugins/smart-woocommerce-search/).

[View plugin Demo](https://smart-woocommerce-search.yummywp.com/).

**Smart WooCommerce Search** is an Instant Ajax WooCommerce search plugin that lets your customers search any product in your website. Simply enter a relevant keyword, and the plugin will browse all WooCommerce products one at a time.

It is fast, and efficient for websites with a huge amount of items.

**Smart Search** is fully customisable and could be easily integrated into the default WooCommerce Product Search widget.

You could create a custom Smart Search widget and insert it to any page ( use 'Code' widget in **DIVI**) through a shortcode [smart_search id="1"] or add it to a sidebar using "Smart Search" widget.
Also you can replace default theme search with a shortcode (for example header search in Avada theme).

Also **WooCommerce Search** is compatible with **Elementor** (you can find appropriate widget in the Elementor widgets list) as well as with **WPBakery Page Builder** plugin.

Give it a try and enjoy:)

= Features =

 * Search results with images and prices
 * Simple integration into default search widgets
 * Elementor compatible
 * DIVI compatible
 * WPBakery Page Builder ( Visual Composer ) compatible
 * Search by product SKU
 * Search in product Categories
 * Search in product Tags
 * Search in Variations
 * Allow/disallow searching in selected product categories
 * Exclude "Out of stock" products from search results
 * Fuzzy search
 * Translation ready
 * Search Exclude plugin compatible

[View plugin Demo](https://smart-woocommerce-search.yummywp.com/).

[Plugin Documentation](https://yummywp.com/docs/smart-search/).

 = PRO Features =

  * Search in **product attributes**
  * Search in custom fields
  * Search in custom taxonomies
  * Search in product brands
  * Search in custom post types
  * **WPML compatible**
  * **Polylang compatible**
  * **Stop words**
  * **Synonyms**
  * Search results with "Out of stock" label
  * Search results with "Sale" label
  * Search results with "Featured" label
  * Search results with "Add to Cart" button
  * ACF plugin compatible
  * Custom Post Type UI plugin compatible
  * Ultimate WooCommerce Brands plugin compatible
  * More customization and styling settings

[Read More](https://yummywp.com/plugins/smart-woocommerce-search/).

[View plugin Demo](https://smart-woocommerce-search.yummywp.com/).

[Plugin Documentation](https://yummywp.com/docs/smart-search/).

== Installation ==

1. Unzip the downloaded .zip file.
2. Upload the plugin folder to the `wp-content/plugins/` directory of your WordPress site.
3. Go to the 'Plugins' menu in WordPress and activate the Smart WooCommerce Search plugin.
4. Configure the plugin (/wp-admin/admin.php?page=smart-search)
5. Place the search plugin shortcode <?php echo do_shortcode('[smart_search id="1"]');  ?> into your template or simply use the built-in widget.

== Frequently Asked Questions ==

= Is it compatible with my WordPress themes? =

Plugin not tested with all themes, but it easily integrated in any theme, and may be require some styling to make it match nicely.

= How to manage plugin settings? =

Please read a [documentation](http://yummywp.com/docs/smart-search/).

= Is there a way to show the ‘shop search output’ from WooCommerce, for example 3 items per row? =

Yes, when using products search or when you set option to search only through products in default or in custom search widget (shortcode) the search page displays WooCommerce product layout.

= How to add Smart Search widget into the theme template (a PHP file) =

Just add these code <?php echo do_shortcode('[smart_search id="1"]');  ?>
In example used widget with ID = 1, so don't forget to change widget ID to needed value.

== Screenshots ==

1. Smart WooCommerce Search Custom Widget
2. Smart WooCommerce Search Settings Page - General tab
3. Smart WooCommerce Search Settings Page - Items to Search through tab
4. Smart WooCommerce Search Settings Page - Styling tab

== Changelog ==

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
