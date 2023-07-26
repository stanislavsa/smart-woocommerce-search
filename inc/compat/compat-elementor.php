<?php
namespace YSWS\Compat\Elementor;

add_filter( 'elementor/widgets/widgets_registered', __NAMESPACE__ . '\\extend' );

/**
 * Add search widget to Elementor widgets list
 */
function extend( \Elementor\Widgets_Manager $self ) {
	include_once __DIR__ . '/class-module-elementor.php';
	$self->register( new Elementor_Smart_Search_Widget );
}
