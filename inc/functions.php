<?php
/**
 * Check if Woocommerce plugin is active or not
 * @return bool
 */
function ysm_is_woocommerce_active() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if (is_plugin_active('woocommerce/woocommerce.php')) {
		return true;
	} else {
		return false;
	}
}

/*
 * Get option by $id
 */
function ysm_get_option($w_id, $id) {

	if ($w_id === 'default' || $w_id === 'product') {
		$manager = Ysm_Widget_Manager::init();
		$value = $manager->get($w_id, $id);
	} else {
		$manager = Ysm_Custom_Widget_Manager::init();
		$value = $manager->get($w_id, $id);
	}

	return $value;
}

/**
 * Get setting output
 */
function ysm_setting($w_id, $id, $args) {

	$value = ysm_get_option($w_id, $id);

	if ($value !== null) {
		$args['value'] = $value;
	}

	$setting = Ysm_Setting::init();
	$setting->get_setting_html($id, $args);
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
 */
function ysm_add_message($text, $type = 'message') {
	$message = Ysm_Message::init();

	if ($type === 'message') {
		$message->add_message($text);
	} elseif ($type === 'error') {
		$message->add_error($text);
	}
}

/**
 * Retrieve custom widgets list row template
 *
 * @param $id
 * @param array $args
 * @return string
 */
function ysm_get_widget_list_row_template($args) {

	$id = $args['id'];

	$template = '<tr>
					<td>' . $id . '</td>
					<td>
						<a href="' . admin_url( 'admin.php?page=smart-search-custom&action=edit&id=' . $id ) . '">
							' . ( ! empty( $args['name'] ) ? esc_html( $args['name'] ) : 'no name' ) . '
						</a>
					</td>
					<td>
						<input type="text" value="[smart_search id=&quot;' . $id . '&quot;]" readonly="" />
					</td>
					<td>
						<a href="#" class="ysm-widget-duplicate" data-id="' . $id . '" title="' . __('Duplicate', 'smart_search') . '">
							<span class="dashicons dashicons-admin-page"></span>
						</a>
						<a href="#" class="ysm-widget-remove" data-id="' . $id . '" title="' . __('Delete', 'smart_search') . '">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</td>
				</tr>';

	return $template;
}

function ysm_get_custom_widgets() {
	$widget_manager = Ysm_Custom_Widget_Manager::init();
	return $widget_manager->get_all_widgets();
}

function ysm_get_default_widgets() {
	$widget_manager = Ysm_Widget_Manager::init();
	return $widget_manager->get_all_widgets();
}
