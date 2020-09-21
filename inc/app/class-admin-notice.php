<?php

namespace YMAPP;

if ( ! class_exists( 'YMAPP\Admin_Notice' ) ) :
	/**
	 * Class Admin_Notice
	 * @author YummyWP
	 */
	class Admin_Notice {

		/**
		 * @var array
		 */
		protected $messages = array();
		/**
		 * @var array
		 */
		protected $errors = array();
		/**
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Constructor.
		 */
		private function __construct() {
			add_action( 'ymapp_notice', array( $this, 'display' ) );
		}

		/**
		 * Cloning is not allowed
		 */
		private function __clone() {}

		/**
		 * @return null|Admin_Notice
		 */
		public static function init() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Add message to messages array
		 * @param $text
		 */
		public function add_message( $text ) {
			$this->messages[] = $text;
		}

		/**
		 * Add error to errors array
		 * @param $text
		 */
		public function add_error( $text ) {
			$this->errors[] = $text;
		}

		/**
		 * Display messages and errors
		 */
		public function display() {
			if ( ! empty( $this->errors ) ) {
				foreach ( $this->errors as $error ) {
					echo '<div class="error"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
				}
			}

			if ( ! empty( $this->messages ) ) {
				foreach ( $this->messages as $message ) {
					echo '<div class="updated notice is-dismissible"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
				}
			}
		}

	}
endif;
