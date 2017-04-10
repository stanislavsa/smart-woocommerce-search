<?php

/**
 * Generates styles for widget
 */
class Ysm_Style_Generator
{

	/**
	 * @var array
	 */
	private static $rules = array();

	/**
	 * @param $widget_id
	 * @param $args
	 */
	public static function add_rule($widget_id, $args)
	{
		$defaults = array(
			'selectors' => array(),
			'props' => array(),
		);

		$rule = wp_parse_args( $args, $defaults );

		self::$rules[$widget_id][] = $rule;
	}

	/**
	 * Generate string with css rules
	 * @return string
	 */
	public static function create()
	{
		if (!empty(self::$rules)) {

			$css = '';

			foreach(self::$rules as $widget_id => $rules) {

				foreach($rules as $rule) {

					$selectors_string = self::validate_selectors( $widget_id, $rule['selectors'] );
					$props_string = self::validate_props( $rule['props'] );

					if (!empty($selectors_string) && !empty($props_string)) {
						$css .= $selectors_string . '{' . $props_string . '}';
					}

				}

			}

			return $css;

		}
	}

	/**
	 * @param $widget_id
	 * @param array $ar
	 * @return string
	 */
	private static function validate_selectors($widget_id, $ar = array())
	{
		$sel = '';

		foreach($ar as $selector) {
			$sel .= $widget_id . ' ' . trim($selector, ',') . ',';
		}

		return trim($sel, ',');
	}

	/**
	 * @param array $ar
	 * @return string
	 */
	private static function validate_props($ar = array())
	{
		$properties = '';

		foreach($ar as $prop => $val) {

			if (!empty($val)) {

				if ($prop === 'color' || $prop === 'background-color' || $prop === 'border-color') {
					$val = '#' . trim($val, '#');
				}

				$properties .= $prop . ':' . trim($val, ';') . ';';

			}


		}

		return $properties;
	}

}