<?php
namespace YSWS\Core;

class Analytics {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 */
	private function __construct() {}

	/**
	 * Disallow clone method
	 */
	private function __clone() {}

	/**
	 * Get instance
	 * @return null
	 */
	public static function init() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Save
	 */
	public static function save() {
        $is_save  = filter_input( INPUT_POST, 'save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $sws_enable_google_analytics  = filter_input( INPUT_POST, 'sws_enable_google_analytics');
		$nonce = filter_input( INPUT_POST, 'ysm_analytics_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $is_save || ! $nonce ) {
			return;
		}

		// include wp_verify_nonce
		require_once ABSPATH . 'wp-includes/pluggable.php';

		if (
			! current_user_can( 'edit_posts' )
			|| ! wp_verify_nonce( $nonce, 'ysm_analytics_nonce_action' )
		) {
			return;
		}

        // check if automate synonyms enabled
        if ($sws_enable_google_analytics) {
            update_option('sws_enable_google_analytics', true);
        } else {
            delete_option('sws_enable_google_analytics');
        }

		ysm_add_message( __( 'Your changes have been saved.', 'ymapp' ) );
	}
}
