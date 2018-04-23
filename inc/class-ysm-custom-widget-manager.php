<?php
/**
 * Custom Widget Manager
 *
 * @author YummyWP
 */

class Ysm_Custom_Widget_Manager
{

	/**
	 * Array of widgets
	 * @var array|mixed|void
	 */
	protected $widgets = array();
	/**
	 * Widget id
	 * @var int
	 */
	protected $widget_id = 0;
	/**
	 * @var int
	 */
	protected $counter = 0;
	/**
	 * Option in database with widget settings
	 * @var string
	 */
	protected $wp_option = 'smart_search_custom';
	/**
	 * @var string
	 */
	protected $mode = 'custom-list';
	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Ysm_Custom_Widget_Manager constructor.
	 */
	private function __construct()
	{

		/* add ajax actions */
		add_action( 'wp_ajax_ysm_widget_delete', array($this, 'remove') );
		add_action( 'wp_ajax_ysm_widget_duplicate', array($this, 'duplicate') );

		/* add shortcode */
		add_shortcode('smart_search', array($this, 'do_shortcode'));

		/* extend visual composer shortcodes */
		add_action( 'init', array( $this, 'extend_vc' ) );

		/* register widgets */
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		/* get widgets and counter */
		$settings = get_option( $this->wp_option, null );

		if ( isset($settings['counter']) ) {
			$this->counter = (int) $settings['counter'];
			unset($settings['counter']);
		} else {
			$this->counter = 0;
		}

		/* custom widgets list or edit widget page */
		if ( !empty($_GET['page']) && $_GET['page'] === 'smart-search-custom' ) {
			$this->mode = 'custom-list';

			if ( isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['id']) ) {
				$this->mode = 'custom-edit';
				$this->widget_id = (int) $_GET['id'];
			}

		}

		/* add new custom widget page */
		if ( !empty($_GET['page']) && $_GET['page'] === 'smart-search-custom-new' ) {
			$this->mode = 'custom-new';
			$this->widget_id = 0;
		}

		/* check custom widget id and redirect to 'add new widget' page if wrong id */
		if ($this->mode === 'custom-edit' && !isset($settings[$this->widget_id])) {
			$admin_url = admin_url('admin.php?page=smart-search-custom');
			header('Location: '.$admin_url);
			exit;
		}

		$this->widgets = $settings;

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
	 * @return null|Ysm_Custom_Widget_Manager
	 */
	public static function init()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Add widget setting
	 * @param $w_id|int
	 * @param $id|string
	 * @param array $args
	 */
	public function add($w_id, $id, $args = array())
	{
		$this->widgets[$w_id]['settings'][$id] = $args;
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
	 * Duplicate widget
	 * @param $id
	 */
	public function duplicate($id)
	{

		if (isset($_POST['action'])) {
			$id = (int) $_POST['id'];
			$res = array(
				'id' => 0,
				'name' => '',
			);
		}

		if ( isset($this->widgets[$id]) ) {
			$original = $this->widgets[$id];
			$original['name'] .= ' copy';

			$settings = $this->widgets;
			$settings['counter'] = ++$this->counter;
			$settings[ $this->counter ] = $original;
			update_option($this->wp_option, $settings);

			if (isset($_POST['action'])) {
				$res['id'] = $this->counter;
				$res['name'] = esc_html($original['name']);
			}
		}

		if (isset($_POST['action'])) {
			echo json_encode($res);
			exit;
		}
	}

	/**
	 * Remove widget
	 * @param $id
	 */
	public function remove($id)
	{

		if (isset($_POST['action'])) {
			$id = (int) $_POST['id'];
		}

		if ( isset($this->widgets[$id]) ) {
			unset($this->widgets[$id]);
			$settings = $this->widgets;
			$settings['counter'] = $this->counter;
			update_option($this->wp_option, $settings);

			if (isset($_POST['action'])) {
				echo 1;
			}
		}

		if (isset($_POST['action'])) {
			exit;
		}
	}

	/**
	 * @return array
	 */
	public function get_all_widgets()
	{
		$widgets = (array) $this->widgets;

		if (isset($widgets['counter'])) {
			unset($widgets['counter']);
		}

		return $widgets;
	}

	/**
	 * Save widget settings
	 */
	protected function save()
	{

		if ( $this->mode === 'custom-edit' || $this->mode === 'custom-new' ) {

			$settings = $this->widgets;

			/* if new widget */
			if (!$this->widget_id) {
				$this->widget_id = ++$this->counter;
			}

			$settings['counter'] = $this->counter;

			$settings[$this->widget_id] = array(
				'name' => !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
				'settings' => !empty($_POST['settings']) ? (array) $_POST['settings'] : array(),
			);

			update_option($this->wp_option, $settings);
			$this->widgets = $settings;

			ysm_add_message( __( 'Your settings have been saved.', 'smart_search' ) );

			/* redirect to edit widget page after new have been created */
			if ( $this->mode == 'custom-new' ) {
				$admin_url = admin_url('admin.php?page=smart-search-custom&action=edit&id=' . $this->widget_id);
				header('Location: '.$admin_url);
				exit;
			}

		}

	}

	/**
	 * Display shortcode with search widget
	 * @param $attr
	 * @return string
	 */
	function do_shortcode($attr )
	{
		extract( shortcode_atts( array(
			'id' => 0,
		), $attr ) );

		ob_start();
		$this->display($attr);
		return ob_get_clean();

	}

	/**
	 * Display search input
	 * @param array $attr
	 * @param string $content
	 * @param string $key
	 * @return string
	 */
	public function display($attr = array(), $content = '', $key = '')
	{
		if (isset($attr['id'])) {
			$w_id = (int) $attr['id'];
		} else {
			$w_id = 0;
		}

		if ($w_id) {
			$widgets = ysm_get_custom_widgets();
		} else {
			return '';
		}

		if ( !isset( $widgets[ $w_id ]['settings'] ) ) {
			return '';
		}

		$settings = $widgets[ $w_id ]['settings'];

		$w_classes = 'ysm-search-widget';
		$w_classes .= ' ysm-search-widget-' . $w_id;

		if ( !empty( $settings['input_round_border'] ) ) {
			$w_classes .= ' bordered';
		}

		?>
		<div class="<?php echo $w_classes; ?>">
		<form data-id="<?php echo $w_id ?>" role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label>
				<span class="screen-reader-text"><?php _e( esc_attr( $settings['placeholder'], 'smart_search' ) ) ?></span>
				<input type="search" class="search-field" placeholder="<?php _e( esc_attr( $settings['placeholder'], 'smart_search' ) ) ?>" value="<?php echo get_search_query(); ?>" name="s" />
				<button type="submit" class="search-submit"><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'smart_search' ); ?></span></button>
			</label>
		</form>
		</div>
		<?php
	}

	/**
	 * Register widget
	 */
	public function register_widgets()
	{
		register_widget('Ysm_Search_Widget');
	}

	/**
	 * Add search widget to visual composer shortcodes list
	 */
	public function extend_vc()
	{

		if ( !function_exists('vc_map') ) {
			return;
		}

		$widgets_list = array();

		if (!empty($this->widgets)) {
			foreach ($this->widgets as $id => $obj) {
				$widgets_list[ __( $obj['name'], 'smart_search') ] = $id;
			}
		}

		vc_map( array(
			"name" => __('Smart Search', 'smart_search'),
			"description" => __('Insert a search widget', 'smart_search'),
			"base" => 'smart_search',
			"icon" => YSM_URI . 'assets/images/search-icon.png',
			"category" => __('Content', 'js_composer'),
			"params" => array(
				array(
					"admin_label" => true,
					"type" => 'dropdown',
					"holder" => 'hidden',
					"class" => '',
					"heading" => __('Widget name', 'smart_search'),
					"param_name" => 'id',
					"value" => $widgets_list,
					"description" => __('Select one of search widgets', 'smart_search')
				)
			)
		));
	}

}
