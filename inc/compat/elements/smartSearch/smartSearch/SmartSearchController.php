<?php
namespace YSWS\Compat\VCWB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;

class SmartSearchController extends Container implements Module {
	use EventsFilters;
	use WpFiltersActions;

	public function __construct() {
		$this->addFilter( 'vcv:editor:variables vcv:editor:variables/smartSearch', 'get_widgets' );
	}

	protected function get_widgets( $variables ) {
		$widgets = [];
		foreach ( ysm_get_custom_widgets() as $id => $widget ) {
			$widgets[] = [ 'label' => $widget['name'], 'value' => (string) $id ];
		}
		$variables[] = [ 'key' => 'swsVcWidgets', 'value' => $widgets ];
		return $variables;
	}
}
