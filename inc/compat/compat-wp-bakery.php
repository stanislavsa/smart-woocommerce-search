<?php
namespace YSWS\Compat\WPBakery;

add_action( 'vc_before_init', __NAMESPACE__ . '\\extend' );

/**
 * Add the search widget to the WPBakery Page Builder element library.
 */
function extend() {

	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	$widgets_list = ysm_get_custom_widgets();
	$opts = array(
		__( 'No value', 'smart-woocommerce-search' ) => '',
	);

	if ( ! empty( $widgets_list ) ) {
		foreach ( $widgets_list as $id => $obj ) {
			$opts[ __( $obj['name'], 'smart-woocommerce-search' ) ] = $id;
		}
	}

	vc_map( array(
		'name'        => 'Sokol Smart Search',
		'description' => '',
		'base'        => 'smart_search',
		'icon'        => SWS_PLUGIN_URI . 'assets/images/icon-128x128.png',
		'category'    => __( 'Content', 'js_composer' ),
		'params'      => array(
			array(
				'admin_label' => true,
				'type'        => 'dropdown',
				'holder'      => 'hidden',
				'class'       => '',
				'heading'     => __( 'Widget name', 'smart-woocommerce-search' ),
				'param_name'  => 'id',
				'value'       => $opts,
				'description' => __( 'Select one of search widgets', 'smart-woocommerce-search' ),
			),
		),
	));
}
