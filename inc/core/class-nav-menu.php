<?php
namespace YSWS\Core;

/**
 * Smart Search – Navigation Menu Integration
 *
 * Adds a "Smart Search" panel to Appearance > Menus so that any saved
 * Smart Search widget can be placed directly inside a WordPress nav menu.
 *
 * How it works:
 *  1. A meta-box is registered on the nav-menus.php screen listing all
 *     custom Smart Search widgets as checkboxes.
 *  2. Items are saved as type="custom" with a placeholder title
 *     "ysm_search_{widget_id}" so WordPress handles the AJAX natively.
 *  3. On the front-end the walker_nav_menu_start_el filter detects
 *     the placeholder in post_title and renders the search form.
 *  4. A Layout dropdown is shown in the menu item edit panel and stored
 *     as post meta, then applied as a CSS class on the front-end.
 *
 * @author YummyWP
 */
class Nav_Menu {

	/** Prefix used in the menu item title to identify our items */
	const PLACEHOLDER_PREFIX = 'ysm_search_';

	/** Post meta key for the layout setting */
	const META_LAYOUT = '_ysm_menu_item_layout';

	private function __clone() {}
	private function __construct() {}

	public static function init() {
		if ( is_admin() ) {
			add_action( 'admin_head-nav-menus.php', array( __CLASS__, 'setup_meta_box' ) );
			add_action( 'admin_footer-nav-menus.php', array( __CLASS__, 'admin_footer_scripts' ) );

			// Layout dropdown inside the menu item edit row
			add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'item_custom_fields' ), 10, 2 );

			// Save layout when the menu is saved
			add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_item_fields' ), 10, 3 );
		} else {
			add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'render_item' ), 50, 2 );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Layout options                                                        */
	/* ------------------------------------------------------------------ */

	public static function get_layout_options() {
		return array(
			'default'           => __( 'Default', 'smart-woocommerce-search' ),
			'classic'           => __( 'Search bar', 'smart-woocommerce-search' ),
			'icon'              => __( 'Search icon', 'smart-woocommerce-search' ),
			'icon-mobile'     => __( 'Icon on mobile, bar on desktop', 'smart-woocommerce-search' ),
			'icon-desktop' => __( 'Icon on desktop, bar on mobile', 'smart-woocommerce-search' ),
		);
	}

	/* ------------------------------------------------------------------ */
	/* Admin – meta box panel                                               */
	/* ------------------------------------------------------------------ */

	public static function setup_meta_box() {
		add_meta_box(
			'ysm-nav-menu-meta-box',
			__( 'Sokol Smart Search', 'smart-woocommerce-search' ),
			array( __CLASS__, 'meta_box_html' ),
			'nav-menus',
			'side',
			'default'
		);
	}

	public static function meta_box_html() {
		$widgets = ysm_get_custom_widgets();

		if ( empty( $widgets ) ) {
			echo '<p>' . esc_html__( 'No Smart Search widgets found. Please create one first.', 'smart-woocommerce-search' ) . '</p>';
			return;
		}
		?>
		<div id="posttype-ysm-search" class="posttypediv">
			<div id="tabs-panel-ysm-search" class="tabs-panel tabs-panel-active">
				<ul id="ysm-search-checklist" class="categorychecklist form-no-clear">
					<?php foreach ( $widgets as $id => $widget ) :
						$item_key    = (int) $id * -1;
						$placeholder = self::PLACEHOLDER_PREFIX . (int) $id;
					?>
					<li>
						<label class="menu-item-title">
							<input type="checkbox"
								class="menu-item-checkbox"
								name="menu-item[<?php echo esc_attr( $item_key ); ?>][menu-item-object-id]"
								value="-1"
							/>
							<?php echo esc_html( $widget['name'] ); ?>
						</label>
						<input type="hidden" class="menu-item-type"
							name="menu-item[<?php echo esc_attr( $item_key ); ?>][menu-item-type]"
							value="custom"
						/>
						<input type="hidden" class="menu-item-title"
							name="menu-item[<?php echo esc_attr( $item_key ); ?>][menu-item-title]"
							value="<?php echo esc_attr( $placeholder ); ?>"
						/>
						<input type="hidden" class="menu-item-url"
							name="menu-item[<?php echo esc_attr( $item_key ); ?>][menu-item-url]"
							value="#"
						/>
						<input type="hidden" class="menu-item-classes"
							name="menu-item[<?php echo esc_attr( $item_key ); ?>][menu-item-classes]"
							value="ysm-nav-menu-item"
						/>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<p class="button-controls wp-clearfix">
				<span class="list-controls hide-if-no-js">
					<a href="#" class="select-all"><?php esc_html_e( 'Select All', 'smart-woocommerce-search' ); ?></a>
				</span>
				<span class="add-to-menu">
					<button type="submit"
						class="button-secondary submit-add-to-menu right"
						name="add-post-type-menu-item"
						id="submit-ysm-search"
					><?php esc_html_e( 'Add to Menu', 'smart-woocommerce-search' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ */
	/* Admin – Layout field inside the menu item edit row                   */
	/* ------------------------------------------------------------------ */

	/**
	 * Output the Layout dropdown inside the edit panel of our menu items.
	 *
	 * @param int     $item_id
	 * @param WP_Post $item
	 */
	public static function item_custom_fields( $item_id, $item ) {
		if ( strpos( $item->post_title, self::PLACEHOLDER_PREFIX ) !== 0 ) {
			return;
		}

		$layout = get_post_meta( $item_id, self::META_LAYOUT, true );
		if ( empty( $layout ) ) {
			$layout = 'default';
		}
		?>
		<p class="field-ysm-layout description description-wide">
			<label for="edit-menu-item-ysm-layout-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Layout', 'smart-woocommerce-search' ); ?><br/>
				<select
					id="edit-menu-item-ysm-layout-<?php echo esc_attr( $item_id ); ?>"
					name="menu-item-ysm-layout[<?php echo esc_attr( $item_id ); ?>]"
					class="widefat ysm-layout-select"
				>
					<?php foreach ( self::get_layout_options() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $layout ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<?php
	}

	/**
	 * Save the Layout value when the menu is saved.
	 *
	 * @param int   $menu_id
	 * @param int   $item_db_id
	 * @param array $args
	 */
	public static function save_item_fields( $menu_id, $item_db_id, $args ) {
		if ( ! isset( $args['menu-item-title'] ) ||
			strpos( $args['menu-item-title'], self::PLACEHOLDER_PREFIX ) !== 0 ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$layout = isset( $_POST['menu-item-ysm-layout'][ $item_db_id ] )
			? sanitize_key( $_POST['menu-item-ysm-layout'][ $item_db_id ] )
			: 'default';

		update_post_meta( $item_db_id, self::META_LAYOUT, $layout );
	}

	/* ------------------------------------------------------------------ */
	/* Front-end rendering                                                  */
	/* ------------------------------------------------------------------ */

	/**
	 * Replace the menu item link with the Smart Search widget HTML.
	 *
	 * @param string  $output
	 * @param WP_Post $item
	 * @return string
	 */
	public static function render_item( $output, $item ) {
		if ( strpos( $item->post_title, self::PLACEHOLDER_PREFIX ) !== 0 ) {
			return $output;
		}

		$widget_id = (int) str_replace( self::PLACEHOLDER_PREFIX, '', $item->post_title );

		if ( ! $widget_id ) {
			return $output;
		}

		$widgets = ysm_get_custom_widgets();
		if ( ! isset( $widgets[ $widget_id ] ) ) {
			return $output;
		}

		$layout = get_post_meta( $item->ID, self::META_LAYOUT, true );
		if ( empty( $layout ) ) {
			$layout = 'default';
		}

		ob_start();
		\Ysm_Widget_Manager::display( array( 'id' => $widget_id ), $layout );
		$html = ob_get_clean();

		// Wrap with a layout class so themes/CSS can target specific modes
		$html = '<div class="ysm-menu-layout-' . esc_attr( $layout ) . '">' . $html . '</div>';

		return $html;
	}

	/* ------------------------------------------------------------------ */
	/* Admin scripts                                                        */
	/* ------------------------------------------------------------------ */

	public static function admin_footer_scripts() {
		$widgets = ysm_get_custom_widgets();
		$map     = array();
		foreach ( $widgets as $id => $widget ) {
			$map[ self::PLACEHOLDER_PREFIX . (int) $id ] = 'Sokol Smart Search [' . $widget['name'] . ']';
		}
		?>
		<script>
		(function( $ ) {
			var placeholderMap = <?php echo wp_json_encode( $map ); ?>;

			// ── Replace placeholder titles with real names ───────────────────
			function replaceLabels() {
				$( '#menu-to-edit .menu-item-title' ).each( function() {
					var $el   = $( this );
					var title = $.trim( $el.text() );
					if ( placeholderMap[ title ] ) {
						$el.text( placeholderMap[ title ] );
						$el.closest( '.menu-item' ).find( '.field-url' ).hide();
					}
				} );
			}

			$( document ).ready( replaceLabels );

			// ── Add to Menu ──────────────────────────────────────────────────
			$( '#submit-ysm-search' ).on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var $checked = $( '#ysm-search-checklist .menu-item-checkbox:checked' );
				if ( ! $checked.length ) {
					return;
				}

				var $btn     = $( this );
				var $spinner = $btn.siblings( '.spinner' );

				$btn.prop( 'disabled', true );
				$spinner.addClass( 'is-active' );

				var menuId = $( '#nav-menu-meta-object-id' ).val();
				var nonce  = $( '#menu-settings-column-nonce' ).val();

				var postData = {
					action: 'add-menu-item',
					menu: menuId,
					'menu-settings-column-nonce': nonce
				};

				$checked.each( function() {
					var $li  = $( this ).closest( 'li' );
					var key  = $( this ).attr( 'name' ).match( /\[(-?\d+)\]/ )[ 1 ];

					postData[ 'menu-item[' + key + '][menu-item-object-id]' ] = $( this ).val();

					$li.find( 'input[type="hidden"]' ).each( function() {
						postData[ $( this ).attr( 'name' ) ] = $( this ).val();
					} );
				} );

				$.post( ajaxurl, postData )
					.done( function( response ) {
						if ( response && $.trim( response ) !== '0' ) {
							var $items = $( response ).hide();
							$( '#menu-to-edit' ).append( $items );
							$items.slideDown( 'normal' );
							replaceLabels();

							try { $( '#menu-to-edit' ).sortable( 'refresh' ); } catch ( err ) {}
							try {
								if ( window.wpNavMenu ) {
									wpNavMenu.refreshKeyboardAccessibility();
									wpNavMenu.refreshAdvancedAccessibility();
									wpNavMenu.registerChange();
								}
							} catch ( err ) {}
						}
						$checked.prop( 'checked', false );
					} )
					.always( function() {
						$btn.prop( 'disabled', false );
						$spinner.removeClass( 'is-active' );
					} );
			} );

		} )( jQuery );
		</script>
		<?php
	}
}
