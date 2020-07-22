<?php
namespace YSM\Compat\VC;

add_filter( 'init', __NAMESPACE__ . '\\extend' );

/**
 * Add search widget to visual composer shortcodes list
 */
function extend() {

	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	$widgets_list = ysm_get_custom_widgets();
	$opts = array(
		__( 'No value', 'smart_search' ) => '',
	);

	if ( ! empty( $widgets_list ) ) {
		foreach ( $widgets_list as $id => $obj ) {
			$opts[ __( $obj['name'], 'smart_search' ) ] = $id;
		}
	}

	vc_map( array(
		'name'        => __( 'Smart Search', 'smart_search' ),
		'description' => '',
		'base'        => 'smart_search',
		'icon'        => YSM_URI . 'assets/images/search-icon.png',
		'category'    => __( 'Content', 'js_composer' ),
		'params'      => array(
			array(
				'admin_label' => true,
				'type'        => 'dropdown',
				'holder'      => 'hidden',
				'class'       => '',
				'heading'     => __( 'Widget name', 'smart_search' ),
				'param_name'  => 'id',
				'value'       => $opts,
				'description' => __( 'Select one of search widgets', 'smart_search' ),
			),
		),
	));
}
