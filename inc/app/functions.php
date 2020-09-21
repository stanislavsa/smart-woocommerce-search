<?php
/**
 * Useful functions for a plugin
 * @author YummyWP
 */

namespace YMAPP;

if ( ! function_exists( 'YMAPP\get_plugin_option' ) ) :
	/**
	 * Get plugin option
	 * @param $id
	 * @param bool $default
	 * @return mixed
	 */
	function get_plugin_option( $id, $default = false ) {
		$value = Plugin_Option::init()->get( $id );
		return $value;
	}
endif;

if ( ! function_exists( 'YMAPP\the_setting_field' ) ) :
	/**
	 * Get setting output
	 * @param $id
	 * @param $args
	 */
	function the_setting_field( $id, $args ) {
		$value = get_plugin_option( $id );

		if ( null !== $value ) {
			$args['value'] = $value;
		}

		Page_Setting_Field::init()->get_setting_html( $id, $args );
	}
endif;

if ( ! function_exists( 'YMAPP\add_admin_notice' ) ) :
	/**
	 * Add messages and errors
	 * @param $text
	 * @param string $type
	 */
	function add_admin_notice( $text, $type = 'message' ) {

		if ( 'message' === $type ) {
			Admin_Notice::init()->add_message( $text );
		} elseif ( 'error' === $type ) {
			Admin_Notice::init()->add_error( $text );
		}
	}
endif;

if ( ! function_exists( 'YMAPP\sanitize' ) ) :
	/**
	 * Sanitize
	 * @param $var
	 * @param string $filter - default is sanitize_text_field
	 * @return array|mixed
	 */
	function sanitize( $var, $filter = 'sanitize_text_field' ) {

		if ( is_array( $var ) ) {
			return array_map( $filter, $var );
		} else {
			return is_scalar( $var ) ? call_user_func( $filter, $var ) : $var;
		}
	}
endif;

if ( ! function_exists( 'YMAPP\is_woocommerce' ) ) :
	/**
	 * Check if Woocommerce plugin is active or not
	 * @return bool
	 */
	function is_woocommerce() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'YMAPP\is_edd' ) ) :
	/**
	 * Check if EDD plugin is active or not
	 * @return bool
	 */
	function is_edd() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
			return true;
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'YMAPP\is_jigoshop' ) ) :
	/**
	 * Check if Jigoshop plugin is active or not
	 * @return bool
	 */
	function is_jigoshop() {
		return false;
	}
endif;
