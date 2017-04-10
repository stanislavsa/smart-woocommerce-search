<?php
/**
 * Widget Manager for default search widget and
 * for woocommerce product search widget
 *
 * @author YummyWP
 */

class Ysm_Widget_Manager
{

	/**
	 * Array of widgets
	 * @var array|mixed|void
	 */
	protected $widgets = array();
	/**
	 * Widget id
	 * @var int|string
	 */
	protected $widget_id = 0;
	/**
	 * Option in database with widget settings
	 * @var string
	 */
	protected $wp_option = 'smart_search_default';
	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Ysm_Widget_Manager constructor.
	 */
	private function __construct()
	{

		/* get widgets */
		$settings = get_option( $this->wp_option, null );

		$this->widgets = $settings;

		$this->widget_id = !empty($_GET['type']) ? $_GET['type'] : 'default';

		if (isset($_POST['save'])) {

			require_once ABSPATH . 'wp-includes/pluggable.php';

			if ( !empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $this->wp_option ) ) {
				$this->save();
			}

		}

	}

	/**
	 *
	 */
	private function __clone() {}

	/**
	 * @return null|Ysm_Widget_Manager
	 */
	public static function init()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Get widget setting
	 * @param $w_id|int
	 * @param $id|string
	 * @return null|string
	 */
	public function get($w_id, $id)
	{
		if (!isset($this->widgets[$w_id])) {
			return null;
		}

		if ( isset($this->widgets[$w_id]['settings'][$id]) ) {
			return $this->widgets[$w_id]['settings'][$id];
		} else {
			return '';
		}
	}

	/**
	 * @return array
	 */
	public function get_all_widgets()
	{
		$widgets = (array) $this->widgets;

		return $widgets;
	}

	/**
	 * Save widget settings
	 */
	protected function save()
	{

		$settings = $this->widgets;

		$settings[$this->widget_id] = array(
			'settings' => !empty($_POST['settings']) ? (array) $_POST['settings'] : array(),
		);

		update_option($this->wp_option, $settings);
		$this->widgets = $settings;

		ysm_add_message( __( 'Your settings have been saved.', 'smart_search' ) );

	}

}
