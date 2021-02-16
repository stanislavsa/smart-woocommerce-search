<?php
/**
 * Widget Manager
 *
 * @author YummyWP
 */

class Ysm_Widget_Manager {

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
	 * @var int
	 */
	protected $counter = 0;
	/**
	 * Option in database with widget settings
	 * @var string
	 */
	protected $wp_option = 'smart_search_custom'; // smart_search_default
	/**
	 * @var string
	 */
	protected $mode = 'custom-list';
	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Ysm_Widget_Manager constructor.
	 */
	private function __construct() {

		/* add ajax actions */
		add_action( 'wp_ajax_ysm_widget_delete', array( $this, 'remove' ) );
		add_action( 'wp_ajax_ysm_widget_duplicate', array( $this, 'duplicate' ) );

		/* add shortcode */
		add_shortcode( 'smart_search', array( $this, 'do_shortcode' ) );

		/* register widgets */
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		/* custom widgets list or edit widget page */
		if ( ! empty( $page ) && 'smart-search' === $page ) {
			$this->mode = 'custom-list';
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
			$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );

			if ( isset( $action ) && 'edit' === $action && ! empty( $id ) ) {
				$this->mode = 'custom-edit';

				if ( ysm_get_default_widgets_names( $id ) ) {
					$this->wp_option = 'smart_search_default';
					$this->widget_id = $id;
				} else {
					$this->widget_id = (int) $id;
				}
			}
		}

		/* get widgets and counter */
		$settings = get_option( $this->wp_option, null );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( 'smart_search_default' === $this->wp_option ) {
			foreach ( ysm_get_default_widgets_ids() as $default_widgets_id ) {
				if ( ! isset( $settings[ $default_widgets_id ] ) ) {
					$settings[ $default_widgets_id ] = array();
				}
			}
		}

		if ( isset( $settings['counter'] ) ) {
			$this->counter = (int) $settings['counter'];
			unset( $settings['counter'] );
		} else {
			$this->counter = 0;
		}

		/* add new custom widget page */
		if ( ! empty( $page ) && 'smart-search-custom-new' === $page ) {
			$this->mode = 'custom-new';
			$this->widget_id = 0;
		}

		/* check custom widget id and redirect to 'add new widget' page if wrong id */
		if ( 'custom-edit' === $this->mode && ! isset( $settings[ $this->widget_id ] ) ) {
			header( 'Location: ' . admin_url( 'admin.php?page=smart-search' ) );
			exit;
		}

		$this->widgets = $settings;

		if ( filter_input( INPUT_POST, 'save', FILTER_SANITIZE_STRING ) ) {

			require_once ABSPATH . 'wp-includes/pluggable.php';

			$wpnonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			if ( ! empty( $wpnonce ) && wp_verify_nonce( $wpnonce, $this->wp_option ) ) {
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
	public static function init() {
		if ( null === self::$_instance ) {
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
	public function add( $w_id, $id, $args = array() ) {
		$this->widgets[ $w_id ]['settings'][ $id ] = $args;
	}

	/**
	 * Get widget setting
	 * @param $w_id|int
	 * @param $id|string
	 * @return null|string
	 */
	public function get( $w_id, $id ) {
		if ( ysm_get_default_widgets_names( $w_id ) ) {
			$wp_option = 'smart_search_default';
		} else {
			$wp_option = $this->wp_option;
		}
		$settings = get_option( $wp_option, null );

		if ( ! isset( $settings[ $w_id ] ) ) {
			return null;
		}

		if ( isset( $settings[ $w_id ]['settings'][ $id ] ) ) {
			return $settings[ $w_id ]['settings'][ $id ];
		} else {
			return null;
		}
	}

	/**
	 * Duplicate widget
	 * @param $id
	 */
	public function duplicate( $id ) {
		$w_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		if ( $action ) {
			$id = (int) $w_id;
			$res = array(
				'id' => 0,
				'name' => '',
			);
		}

		if ( isset( $this->widgets[ $id ] ) ) {
			$original = $this->widgets[ $id ];
			$original['name'] .= ' copy';

			$settings = $this->widgets;
			$settings['counter'] = ++$this->counter;
			$settings[ $this->counter ] = $original;
			update_option( $this->wp_option, $settings );

			if ( $action ) {
				$res['id'] = $this->counter;
				$res['name'] = esc_html( $original['name'] );
			}
		}

		if ( $action ) {
			echo json_encode( $res );
			exit;
		}
	}

	/**
	 * Remove widget
	 * @param $id
	 */
	public function remove( $id ) {
		$w_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( $action ) {
			$id = (int) $w_id;
		}

		if ( isset( $this->widgets[ $id ] ) ) {
			unset( $this->widgets[ $id ] );
			$settings = $this->widgets;
			$settings['counter'] = $this->counter;
			update_option( $this->wp_option, $settings );

			if ( $action ) {
				echo 1;
			}
		}

		if ( $action ) {
			exit;
		}
	}

	/**
	 * @return array
	 */
	public function get_all_widgets( $type = '' ) {
		$wp_option = $this->wp_option;
		if ( 'default' === $type ) {
			$wp_option = 'smart_search_default';
		}
		$widgets = get_option( $wp_option, null );

		if ( isset( $widgets['counter'] ) ) {
			unset( $widgets['counter'] );
		}

		if ( ! is_array( $widgets ) ) {
			$widgets = array();
		}

		return $widgets;
	}

	/**
	 * Save widget settings
	 */
	protected function save() {
		if ( in_array( $this->mode, array( 'custom-edit', 'custom-new' ), true ) ) {
			$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
			$settings = $this->widgets;

			if ( ysm_get_default_widgets_names( $this->widget_id ) ) {
				$settings[ $this->widget_id ] = array(
					'settings' => ! empty( $_POST['settings'] ) ? (array) $_POST['settings'] : array(),
				);
			} else {
				/* if new widget */
				if ( ! $this->counter ) {
					$this->counter = 0;
				}

				if ( ! $this->widget_id ) {
					$this->widget_id = ++$this->counter;
				}
				$settings['counter'] = $this->counter;

				$settings[ $this->widget_id ] = array(
					'name' => ! empty( $name ) ? sanitize_text_field( $name ) : '',
					'settings' => ! empty( $_POST['settings'] ) ? (array) $_POST['settings'] : array(),
				);
			}

			update_option( $this->wp_option, $settings );
			$this->widgets = $settings;

			ysm_add_message( __( 'Your settings have been saved.', 'smart_search' ) );

			/* redirect to edit widget page after new have been created */
			if ( 'custom-new' === $this->mode ) {
				header( 'Location: ' . admin_url( 'admin.php?page=smart-search&action=edit&id=' . $this->widget_id ) );
				exit;
			}
		}
	}

	/**
	 * Display shortcode with search widget
	 * @param $attr
	 * @return string
	 */
	function do_shortcode( $attr ) {
		/* @codingStandardsIgnoreLine */
		extract( shortcode_atts( array(
			'id' => 0,
		), $attr ) );

		ob_start();
		$this->display( $attr );
		return ob_get_clean();

	}

	/**
	 * Display search input
	 * @param array $attr
	 * @param string $content
	 * @param string $key
	 * @return string
	 */
	public function display( $attr = array(), $content = '', $key = '' ) {
		$w_id = isset( $attr['id'] ) ? (int) $attr['id'] : 0;

		if ( ! $w_id ) {
			return '';
		}

		$widgets = ysm_get_custom_widgets();

		if ( ! isset( $widgets[ $w_id ]['settings'] ) ) {
			return '';
		}

		$settings = $widgets[ $w_id ]['settings'];

		$w_classes  = 'ysm-search-widget';
		$w_classes .= ' ysm-search-widget-' . $w_id;

		if ( ! empty( $settings['input_round_border'] ) ) {
			$w_classes .= ' bordered';
		}

		$layout = '';
		if ( ! empty( $settings['post_type_product'] ) && empty( $settings['search_page_layout_posts'] ) ) {
			$layout = 'product';
		}

		$uniq_id = 'ysm-smart-search-' . $w_id . '-' . uniqid();
		?>
		<div class="<?php echo esc_attr( $w_classes ); ?>">
			<form data-id="<?php echo esc_attr( $w_id ); ?>" role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="<?php echo esc_attr( $uniq_id ); ?>">
					<span class="screen-reader-text"><?php esc_attr_e( $settings['placeholder'], 'smart_search' ); ?></span>
					<input type="search" name="s" value="<?php echo get_search_query(); ?>" id="<?php echo esc_attr( $uniq_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( $settings['placeholder'], 'smart_search' ); ?>" />
					<input type="hidden" name="search_id" value="<?php echo esc_attr( $w_id ); ?>" />
					<?php if ( 'product' === $layout ) : ?>
						<input type="hidden" name="post_type" value="product" />
					<?php endif; ?>
					<?php if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) : ?>
						<input type="hidden" name="amp" value="1" />
					<?php endif; ?>
					<button type="submit" class="search-submit" aria-label="<?php echo esc_html_x( 'Search', 'submit button', 'smart_search' ); ?>"><span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'smart_search' ); ?></span></button>
				</label>
			</form>
		</div>
		<?php
	}

	/**
	 * Register widget
	 */
	public function register_widgets() {
		register_widget( 'Ysm_Search_Widget' );
	}
}
