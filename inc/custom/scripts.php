<?php
namespace YSWS\Core\Scripts;

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_scripts' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\front_scripts' );

/**
 * Include Front Scripts
 */
function front_scripts() {
	wp_enqueue_style( 'smart-search', SWS_PLUGIN_URI . 'assets/dist/css/general.css', array(), SWS_PLUGIN_VERSION );

    $l10n = [
        'restUrl'       => rest_url( 'ysm/v1/search' ) . '?',
        'searchPageUrl' => home_url( '/' ),
		'type' => sws_fs()->is_trial() ? 't' : ( sws_fs()->is_premium() ? 'p' : 'f' ),
        'v' => SWS_PLUGIN_VERSION,
        'widgets'       => [],
    ];
    foreach ( ysm_get_all_widgets() as $k => $v ) {
        $widget_params = [];
        $css_classes = [];

	    if ( $k === 'default' ) {
		    $css_classes['.widget_search'] = '.widget_search';
		    $css_classes['.wp-block-search.sws-search-block-default'] = '.wp-block-search.sws-search-block-default';
	    } elseif ( $k === 'product' ) {
		    $css_classes['.widget_product_search'] = '.widget_product_search';
		    $css_classes['.wp-block-search.sws-search-block-product'] = '.wp-block-search.sws-search-block-product';
	    } elseif ( $k === 'avada' ) {
		    $css_classes['.fusion-search-form'] = '.fusion-search-form';
	    } else {
		    $css_classes[ '.ysm-search-widget-' . $k ] = '.ysm-search-widget-' . $k;
	    }

	    $extra_bar_css_classes = apply_filters( 'sws_search_bar_css_selectors', [], $k );

	    if ( $extra_bar_css_classes && is_array( $extra_bar_css_classes ) ) {
		    $css_classes = array_merge( $css_classes, $extra_bar_css_classes );
	    }

	    $widget_params['selector'] = implode( ', ', $css_classes );
	    $widget_params['charCount'] = isset( $v['settings']['char_count'] ) ? (int) $v['settings']['char_count'] : 3;
	    $widget_params['disableAjax'] = ! empty( $v['settings']['disable_ajax'] );
	    $widget_params['noResultsText'] = ! empty( $v['settings']['no_results_text'] ) ? __( $v['settings']['no_results_text'], 'smart-woocommerce-search' ) : '';
	    $widget_params['defaultOutput'] = ! empty( $v['settings']['search_page_default_output'] );
	    $widget_params['layoutPosts'] = ! empty( $v['settings']['search_page_layout_posts'] );
	    $widget_params['popupHeight'] = ! empty( $v['settings']['popup_height'] ) ? intval( $v['settings']['popup_height'] ) : 500;
	    $widget_params['popupHeightMobile'] = ! empty( $v['settings']['popup_height_mobile'] ) ? intval( $v['settings']['popup_height_mobile'] ) : 400;
	    $widget_params['productSlug'] = 'product';
	    $widget_params['preventBadQueries'] = true;
	    $widget_params['loaderIcon'] = SWS_PLUGIN_URI . 'assets/images/loader1.gif';
	    $widget_params['productSku'] = ! empty( $v['settings']['field_product_sku'] );
	    $widget_params['multipleWords'] = ! empty( $v['settings']['enable_fuzzy_search'] ) ? $v['settings']['enable_fuzzy_search'] : '';
	    $widget_params['excludeOutOfStock'] = ! empty( $v['settings']['exclude_out_of_stock_products'] );
	    $widget_params['layout'] = 'product' === $k ? 'product' : '';
	    $widget_params['suppressQueryParams'] = (bool) ! empty( $v['settings']['search_page_suppress_filters'] );
	    $widget_params['columns'] = 1;

	    if ( !empty( $v['settings']['post_type_product'] ) && empty( $v['settings']['search_page_layout_posts'] ) ) {
            $widget_params['layout'] = 'product';
        }

        $l10n['widgets'][$k] = $widget_params;
        ysm_add_inline_styles_to_stack( $v, $css_classes );
    }

    wp_add_inline_style( 'smart-search', \Ysm_Style_Generator::create() );

    if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		wp_enqueue_script( 'smart-search-autocomplete', SWS_PLUGIN_URI . 'assets/src/js/jquery.autocomplete.js', array( 'jquery' ), false, 1 );
		wp_enqueue_script( 'smart-search-custom-scroll', SWS_PLUGIN_URI . 'assets/src/js/jquery.nanoscroller.js', array( 'jquery' ), false, 1 );
		wp_enqueue_script( 'smart-search-general', SWS_PLUGIN_URI . 'assets/src/js/general.js', array( 'jquery' ), time(), 1 );
    } else {
		wp_enqueue_script( 'smart-search-general', SWS_PLUGIN_URI . 'assets/dist/js/main.js', array( 'jquery' ), SWS_PLUGIN_VERSION, 1 );
    }

    wp_localize_script( 'smart-search-general', 'swsL10n', $l10n );

}

/**
 * Include Admin Scripts
 */
function admin_scripts() {
    $cur_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

    if ( !$cur_page || false === strpos( $cur_page, 'smart-search' ) ) {
        return;
    }

    wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'smart-search-admin', SWS_PLUGIN_URI . 'assets/dist/css/admin.css', [], SWS_PLUGIN_VERSION );
    wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'smart-search-admin', SWS_PLUGIN_URI . 'assets/dist/js/admin.js', array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-sortable',
            'jquery-ui-slider',
            'underscore',
            'wp-color-picker',
		'wp-util',
	), SWS_PLUGIN_VERSION, 1 );

    wp_localize_script( 'smart-search-admin', 'ysm_L10n', array(
        'column_delete' => __( 'Delete column?', 'smart-woocommerce-search' ),
        'row_delete'    => __( 'Delete row?', 'smart-woocommerce-search' ),
        'widget_delete' => __( 'Delete widget?', 'smart-woocommerce-search' ),
    ) );

    // Select2
	wp_enqueue_style( 'ysrs-select2', SWS_PLUGIN_URI . 'assets/dist/css/select2.min.css', array(), SWS_PLUGIN_VERSION );
	wp_enqueue_script( 'ysrs-select2', SWS_PLUGIN_URI . 'assets/dist/js/select2.min.js', array(), SWS_PLUGIN_VERSION, true );
}
