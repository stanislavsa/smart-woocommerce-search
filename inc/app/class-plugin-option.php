<?php

namespace YMAPP;

if ( ! class_exists( 'YMAPP\Plugin_Option' ) ) :
	/**
	 * Plugin option class
	 *
	 * @author YummyWP
	 */

	class Plugin_Option {

		/**
		 * Array of settings
		 * @var array
		 */
		protected $settings = array();

		/**
		 * Option in database with widget settings
		 * @var string
		 */
		protected $wp_option = '';

		/**
		 * @var null|Plugin_Option
		 */
		private static $_instance = null;

		/**
		 * Plugin_Option constructor.
		 */
		private function __construct() {
			$this->wp_option = apply_filters( 'ymapp_wp_option', '' );
			if ( $this->wp_option ) {
				$this->settings = get_option( $this->wp_option, null );
				add_action( 'admin_init', array( $this, 'save' ) );
			}
		}

		/**
		 *
		 */
		private function __clone() {}

		/**
		 * @return null|Plugin_Option
		 */
		public static function init() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Check option name and update if is not valid
		 * Needed when cron is doing
		 */
		protected function check_wp_option() {
			$wp_option = apply_filters( 'ymapp_wp_option', '' );
			if ( $this->wp_option !== $wp_option ) {
				$this->wp_option = $wp_option;
				$this->settings  = get_option( $this->wp_option, null );
			}
		}

		/**
		 * Add widget setting
		 *
		 * @param $id|string
		 * @param array $args
		 */
		public function add( $id, $args = array() ) {
			$this->settings[ $id ] = $args;
		}

		/**
		 * Get widget setting
		 *
		 * @param $id|string
		 * @return null|string
		 */
		public function get( $id ) {
			$this->check_wp_option();

			if ( isset( $this->settings[ $id ] ) ) {
				return $this->settings[ $id ];
			} else {
				return null;
			}
		}


		/**
		 * Save widget settings
		 */
		public function save() {
			$is_save  = filter_input( INPUT_POST, 'save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$_wpnonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			if ( ! $is_save ) {
				return;
			}

			require_once ABSPATH . 'wp-includes/pluggable.php';

			if ( current_user_can( 'edit_posts' ) && wp_verify_nonce( $_wpnonce, 'ymapp_nonce_' . $this->wp_option ) ) {

				$settings = array_map( 'sanitize_text_field', $_POST['settings'] );

				if ( ! empty( $settings ) ) {
					update_option( $this->wp_option, $settings, 'no' );
				}

				$this->settings = $settings;

				do_action( 'ymapp_save_options' );

				add_admin_notice( __( 'Your settings have been saved.', 'ymapp' ) );

			}

		}

	}
endif;
