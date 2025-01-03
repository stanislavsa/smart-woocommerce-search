<?php
namespace YummyWP\App;

/**
 * Notification manager
 * @author YummyWP
 */
class Notification {

	/**
	 * @var array
	 */
	protected static $messages = [];
	/**
	 * @var array
	 */
	protected static $errors = [];
	/**
	 * @var array
	 */
	protected static $templates = [];

	/**
	 * Constructor.
	 */
	private function __construct() {}

	/**
	 * Cloning is not allowed
	 */
	private function __clone() {}

	/**
	 * Add message or error notification
	 * @param $text
	 * @param $type
	 * @return void
	 */
	public static function add( $text, $type = 'message' ) {
		if ( 'message' === $type ) {
			self::add_message( $text );
		} elseif ( 'error' === $type ) {
			self::add_error( $text );
		} elseif ( 'template' === $type ) {
			self::add_template( $text );
		}
	}

	/**
	 * Add message to messages array
	 * @param $text
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}

	/**
	 * Add error to errors array
	 * @param $text
	 */
	public static function add_error( $text ) {
		self::$errors[] = $text;
	}

	public static function add_template( $path ) {
		self::$templates[ $path ] = $path;
	}

	/**
	 * Display messages and errors
	 */
	public static function display() {
		if ( ! empty( self::$errors ) ) {
			foreach ( self::$errors as $error ) {
				echo '<div class="error"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		}

		if ( ! empty( self::$messages ) ) {
			foreach ( self::$messages as $message ) {
				echo '<div class="updated notice is-dismissible"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}

	/**
	 * Display messages and errors
	 */
	public static function display_templates() {
		if ( ! empty( self::$templates ) ) {
			foreach ( self::$templates as $template_path ) {
				if ( file_exists( $template_path ) ) {
					include_once $template_path;
				}
			}
		}
	}
}
