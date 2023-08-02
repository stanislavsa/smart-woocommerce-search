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
	protected static $widgets = [];
	/**
	 * Array of widgets
	 * @var array|mixed|void
	 */
	protected static $options = [];
	/**
	 * Widget id
	 * @var int|string
	 */
	protected static $widget_id = 0;
	/**
	 * Option in database with widget settings
	 * @var string
	 */
	protected static $wp_option = 'smart_search_custom'; // smart_search_default
	/**
	 * @var string
	 */
	protected static $mode = 'custom-list';

	private function __clone() {}

	private function __construct() {}

	public static function init() {
		/* add ajax actions */
		add_action( 'wp_ajax_ysm_widget_delete', array( __CLASS__, 'remove' ) );
		add_action( 'wp_ajax_ysm_widget_duplicate', array( __CLASS__, 'duplicate' ) );

		/* add shortcode */
		add_shortcode( 'smart_search', array( __CLASS__, 'do_shortcode' ) );

		/* register widgets */
		add_action( 'widgets_init', array( __CLASS__, 'register_widgets' ) );

		add_action( 'admin_init', array( __CLASS__, 'on_admin_init' ) );
	}

	public static function on_admin_init() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $page ) || ! in_array( $page, [ 'smart-search-custom-new', 'smart-search' ], true ) ) {
			return;
		}

		// custom widgets list or edit widget page
		if ( 'smart-search' === $page ) {
			self::$mode = 'custom-list';
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			if ( isset( $action ) && 'edit' === $action && ! empty( $id ) ) {
				self::$mode = 'custom-edit';

				if ( ysm_get_default_widgets_names( $id ) ) {
					self::$wp_option = 'smart_search_default';
					self::$widget_id = $id;
				} else {
					self::$widget_id = (int) $id;
				}
			}
		}

		// get widgets
		$settings = self::get_option( self::$wp_option );

		if ( ! is_array( $settings ) ) {
			$settings = [];
		}

		if ( 'smart_search_default' === self::$wp_option ) {
			foreach ( ysm_get_default_widgets_ids() as $default_widgets_id ) {
				if ( ! isset( $settings[ $default_widgets_id ] ) ) {
					$settings[ $default_widgets_id ] = [];
				}
			}
		}

		// add new custom widget
		if ( 'smart-search-custom-new' === $page ) {
			self::$mode = 'custom-new';
			self::$widget_id = 0;
		}

		// check custom widget id and redirect to 'add new widget' page if wrong id
		if ( 'custom-edit' === self::$mode && ! ysm_get_default_widgets_names( self::$widget_id ) && ! isset( $settings[ self::$widget_id ] ) ) {
			header( 'Location: ' . admin_url( 'admin.php?page=smart-search' ) );
			exit;
		}

		self::$widgets = $settings;

		// save custom widget
		if ( filter_input( INPUT_POST, 'save', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) {
			$wpnonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( ! empty( $wpnonce ) && wp_verify_nonce( $wpnonce, self::$wp_option ) ) {
				self::save();
			}
		}
	}

	/**
	 * Add widget setting
	 * @param $w_id|int
	 * @param $id|string
	 * @param array $args
	 */
	public static function add( $w_id, $id, $args = array() ) {
		self::$widgets[ $w_id ]['settings'][ $id ] = $args;
	}

	/**
	 * Get widget setting
	 * @param $w_id|int
	 * @param $id|string
	 * @return null|string
	 */
	public static function get( $w_id, $id ) {
		if ( ysm_get_default_widgets_names( $w_id ) ) {
			$wp_option = 'smart_search_default';
		} else {
			$wp_option = self::$wp_option;
		}
		$settings = self::get_option( $wp_option );

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
	 */
	public static function duplicate() {
		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! wp_verify_nonce( $nonce, 'ysm_widgets_nonce_action' ) ) {
			exit;
		}

		// Verify permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			exit;
		}

		$id = (int) $id;
		$res = [
			'id' => 0,
			'name' => '',
		];

		$settings = self::get_option( self::$wp_option );

		if ( isset( $settings[ $id ] ) ) {
			$copy = $settings[ $id ];
			$copy['name'] .= ' copy';

			$new_widget_id = ! empty( $settings ) && is_array( $settings ) ? max( array_keys( $settings ) ) + 1 : 1;
			$settings[ $new_widget_id ] = $copy;
			self::update_option( self::$wp_option, $settings );

			$res['id'] = $new_widget_id;
			$res['name'] = esc_html( $copy['name'] );
		}
		if ( ! $res['id'] ) {
			exit;
		}

		echo wp_json_encode( $res );
		exit;
	}

	/**
	 * Remove widget
	 * @param $id
	 */
	public static function remove( $id ) {
		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! wp_verify_nonce( $nonce, 'ysm_widgets_nonce_action' ) ) {
			exit;
		}

		// Verify permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			exit;
		}

		$id = (int) $id;
		$settings = self::get_option( self::$wp_option );

		if ( isset( $settings[ $id ] ) ) {
			unset( $settings[ $id ] );
			self::update_option( self::$wp_option, $settings );

			echo 1;
		}

		exit;
	}

	/**
	 * @return array
	 */
	public static function get_all_widgets( $type = '' ) {
		$wp_option = self::$wp_option;
		if ( 'default' === $type ) {
			$wp_option = 'smart_search_default';
		}
		$widgets = self::get_option( $wp_option );

		if ( ! is_array( $widgets ) ) {
			$widgets = array();
		}

		return $widgets;
	}

	/**
	 * Save widget settings
	 */
	protected static function save() {
		if ( in_array( self::$mode, array( 'custom-edit', 'custom-new' ), true ) ) {
			$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$settings = self::$widgets;

			if ( ysm_get_default_widgets_names( self::$widget_id ) ) {
				$settings[ self::$widget_id ] = array(
					'settings' => ! empty( $_POST['settings'] ) ? (array) $_POST['settings'] : array(),
				);
			} else {
				if ( ! self::$widget_id ) {
					self::$widget_id = ! empty( $settings ) && is_array( $settings ) ? max( array_keys( $settings ) ) + 1 : 1;
				}

				$settings[ self::$widget_id ] = array(
					'name' => ! empty( $name ) ? sanitize_text_field( $name ) : '',
					'settings' => ! empty( $_POST['settings'] ) ? (array) $_POST['settings'] : array(),
				);
			}

			self::$widgets = $settings;
			self::update_option( self::$wp_option, $settings );

			ysm_add_message( __( 'Your changes have been saved.', 'smart-woocommerce-search' ) );

			/* redirect to edit widget page after new widget has been created */
			if ( 'custom-new' === self::$mode ) {
				header( 'Location: ' . admin_url( 'admin.php?page=smart-search&action=edit&id=' . self::$widget_id ) );
				exit;
			}
		}
	}

	/**
	 * Display shortcode with search widget
	 * @param $attr
	 * @return string
	 */
	public static function do_shortcode( $attr ) {
		/* @codingStandardsIgnoreLine */
		extract( shortcode_atts( array(
			'id' => 0,
		), $attr ) );

		ob_start();
		self::display( $attr );
		return ob_get_clean();

	}

	/**
	 * Display search input
	 * @param array $attr
	 * @param string $content
	 * @param string $key
	 * @return void
	 */
	public static function display( $attr = array(), $content = '', $key = '' ) {
		$w_id = isset( $attr['id'] ) ? (int) $attr['id'] : 0;

		if ( ! $w_id ) {
			return;
		}

		$widgets = ysm_get_custom_widgets();

		if ( ! isset( $widgets[ $w_id ]['settings'] ) ) {
			return;
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
					<span class="screen-reader-text"><?php esc_attr_e( $settings['placeholder'], 'smart-woocommerce-search' ); ?></span>
					<input type="search" name="s" value="<?php echo get_search_query(); ?>" id="<?php echo esc_attr( $uniq_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( $settings['placeholder'], 'smart-woocommerce-search' ); ?>" />
					<input type="hidden" name="search_id" value="<?php echo esc_attr( $w_id ); ?>" />
					<?php if ( 'product' === $layout ) : ?>
						<input type="hidden" name="post_type" value="product" />
					<?php endif; ?>
					<?php if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) : ?>
						<input type="hidden" name="amp" value="1" />
					<?php endif; ?>
					<button type="submit" class="search-submit" aria-label="<?php echo esc_html_x( 'Search', 'submit button', 'smart-woocommerce-search' ); ?>"><span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'smart-woocommerce-search' ); ?></span></button>
				</label>
			</form>
		</div>
		<?php
	}

	/**
	 * Register widget
	 */
	public static function register_widgets() {
		register_widget( 'Ysm_Search_Widget' );
	}

	/**
	 * Get widget settings from wp_options table or from cache
	 * @param $name
	 * @return false|mixed|void
	 */
	protected static function get_option( $name ) {
		if ( isset( self::$options[ $name ] ) ) {
			return self::$options[ $name ];
		}
		$cached = wp_cache_get( $name, 'ywp_sws_cache' );
		if ( false !== $cached ) {
			if ( isset( $value['counter'] ) ) {
				unset( $value['counter'] );
			}
			$value = $cached;
		} else {
			$value = get_option( $name, null );
			if ( isset( $value['counter'] ) ) {
				unset( $value['counter'] );
			}
			wp_cache_set( $name, $value, 'ywp_sws_cache', MONTH_IN_SECONDS );
		}
		self::$options[ $name ] = $value;

		return $value;
	}

	/**
	 * Update widget settings in the wp_options table and in the cache
	 * @param $name
	 * @param $value
	 * @return void
	 */
	protected static function update_option( $name, $value ) {
		self::$options[ $name ] = $value;
		update_option( $name, $value, 'no' );
		wp_cache_set( $name, $value, 'ywp_sws_cache', MONTH_IN_SECONDS );
	}
}
