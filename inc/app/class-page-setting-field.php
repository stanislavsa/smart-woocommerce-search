<?php

namespace YMAPP;

if ( ! class_exists( 'YMAPP\Page_Setting_Field' ) ) :
	/**
	 * Class Page_Setting_Field
	 * @author YummyWP
	 */
	class Page_Setting_Field extends Abstract_Setting_Field {

		/**
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Constructor.
		 */
		private function __construct(){}

		/**
		 * Cloning is not allowed
		 */
		private function __clone() {}

		/**
		 * Get instance
		 * @return null|Abstract_Setting
		 */
		public static function init() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

	}
endif;
