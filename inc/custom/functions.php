<?php
/**
 * Get option by $id
 * @param $w_id
 * @param $id
 * @return string|null
 */
function ysm_get_option( $w_id, $id ) {
	if ( in_array( $w_id, array( 'default', 'product' ), true ) ) {
		$manager = Ysm_Widget_Manager::init();
		$value   = $manager->get( $w_id, $id );
	} else {
		$manager = Ysm_Custom_Widget_Manager::init();
		$value   = $manager->get( $w_id, $id );
	}

	return $value;
}

/**
 * Get setting output
 * @param $w_id
 * @param $id
 * @param $args
 */
function ysm_setting( $w_id, $id, $args ) {

	$value = ysm_get_option( $w_id, $id );

	if ( null !== $value ) {
		$args['value'] = $value;
	}

	$setting = Ysm_Setting::init();
	$setting->get_setting_html( $id, $args );
}

/**
 * Output messages
 */
function ysm_message() {
	$message = Ysm_Message::init();
	$message->display();
}

/**
 * Add messages and errors
 * @param $text
 * @param string $type
 */
function ysm_add_message( $text, $type = 'message' ) {
	$message = Ysm_Message::init();

	if ( 'message' === $type ) {
		$message->add_message( $text );
	} elseif ( 'error' === $type ) {
		$message->add_error( $text );
	}
}

/**
 * Retrieve custom widgets list row template
 * @param array $args
 * @return string
 */
function ysm_get_widget_list_row_template( $args ) {

	$id = $args['id'];

	$template = '<tr>
					<td>' . esc_html( $id ) . '</td>
					<td>
						<a href="' . esc_url( admin_url( 'admin.php?page=smart-search-custom&action=edit&id=' . $id ) ) . '">
							' . ( ! empty( $args['name'] ) ? esc_html( $args['name'] ) : 'no name' ) . '
						</a>
					</td>
					<td>
						<input type="text" value="[smart_search id=&quot;' . esc_attr( $id ) . '&quot;]" readonly="" />
					</td>
					<td>
						<a href="#" class="ysm-widget-duplicate" data-id="' . esc_attr( $id ) . '" title="' . __( 'Duplicate', 'smart_search' ) . '">
							<span class="dashicons dashicons-admin-page"></span>
						</a>
						<a href="#" class="ysm-widget-remove" data-id="' . esc_attr( $id ) . '" title="' . __( 'Delete', 'smart_search' ) . '">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</td>
				</tr>';

	return $template;
}

/**
 * Get list of custom widgets
 * @return array
 */
function ysm_get_custom_widgets() {
	$widget_manager = Ysm_Custom_Widget_Manager::init();
	return $widget_manager->get_all_widgets();
}

/**
 * Get list of default widgets
 * @return array
 */
function ysm_get_default_widgets() {
	$widget_manager = Ysm_Widget_Manager::init();
	return $widget_manager->get_all_widgets();
}

/**
 * Get 's' query var
 * @return string
 */
function ysm_get_s() {
	$s = '';
	if ( ! empty( $_GET['woof_text'] ) ) {
		$s = sanitize_text_field( $_GET['woof_text'] );
	} elseif ( ! empty( $_GET['s'] ) ) {
		$s = sanitize_text_field( $_GET['s'] );
	}

	return $s;
}

/**
 * Wrap search terms in <strong> tag on search page
 * @param $text
 * @return mixed
 */
function ysm_accent_search_term( $text ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return $text;
	}

	if ( ! is_search() ) {
		return $text;
	}

	$w_id = filter_input( INPUT_GET, 'search_id', FILTER_SANITIZE_STRING );
	$s    = ysm_get_s();

	if ( empty( $w_id ) || empty( $s ) ) {
		return $text;
	}

	\Ysm_Search::set_widget_id( $w_id );
	\Ysm_Search::parse_settings();

	if ( empty( \Ysm_Search::get_var( 'search_page_default_output' ) ) && ! empty( \Ysm_Search::get_var( 'accent_words_on_search_page' ) ) ) {
		$text = ysm_text_replace( $text );
	}

	return $text;
}

/**
 * Wrap search terms in <strong> tag
 * @param $text
 * @return string
 */
function ysm_text_replace( $text ) {
	$words = \Ysm_Search::get_search_terms();

	foreach ( $words as &$w ) {
		$w = preg_quote( trim( $w ) );
		$w = str_replace( '/', '\/', $w );
	}

	return preg_replace( '/' . implode( '|', $words ) . '/i', '<strong>$0</strong>', $text );
}
