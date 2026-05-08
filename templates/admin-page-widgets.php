<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$widgets = ysm_get_custom_widgets();
$w_id = 0;
$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$default_widgets_option = get_option('smart_search_default');

if ( $action && 'edit' === $action && $id ) {
	if ( ! empty( ysm_get_default_widgets_names( $id ) ) ) {
		$w_id = $id;
	} elseif ( isset( $widgets[ $id ] ) ) {
		$w_id = (int) $id;
	}
}
?>

<div class="wrap">

<?php if ( $w_id ) { ?>

	<h1><span><?php esc_html_e( 'Edit Widget', 'smart-woocommerce-search' ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

	<?php include_once SWS_PLUGIN_DIR . 'templates/admin-page-form.php'; ?>

<?php } else { ?>

	<h1>
		<span><?php echo esc_html( get_admin_page_title() ); ?></span>
	</h1>

	<?php
	$sws_enhance_widget_id  = (string) get_option( 'sws_enhance_default', '' );
	$sws_enhance_is_taken   = '' !== $sws_enhance_widget_id;

	$active_default_widgets = [];
	if ($default_widgets_option) {
		foreach ( ysm_get_default_widgets_ids() as $default_id ) {
			if (isset($default_widgets_option[$default_id])) {
				$type    = 'default' === $default_id ? '' : '_' . $default_id;
				$enabled = ysm_get_option( $default_id, 'enable' . $type . '_search' );
				$active_default_widgets[ $default_id ] = ysm_get_default_widgets_names( $default_id );
				if ( !$sws_enhance_widget_id && $enabled ) {
					$sws_enhance_widget_id = $default_id;
				}
			}
		}
	}
	?>

	<?php if ( $active_default_widgets ) : ?>

	<h2 class="ysm-widgets-title"><?php esc_html_e( 'Enhance Standard Widgets', 'smart-woocommerce-search' ); ?></h2>

	<div class="ysm-widgets-list ysm-default-widgets-list">

		<table>
			<thead>
			<tr>
				<td width="10%"><?php esc_html_e( 'ID', 'smart-woocommerce-search' ); ?></td>
				<td width="35%"><?php esc_html_e( 'Title', 'smart-woocommerce-search' ); ?></td>
				<td width="20%"></td>
				<td width="20%"><?php esc_html_e( 'Enhance Default', 'smart-woocommerce-search' ); ?></td>
				<td width="15%"></td>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $active_default_widgets as $default_id => $default_name ) :
				$is_current = ( $sws_enhance_widget_id === $default_id );
				$is_disabled = ( $sws_enhance_is_taken && ! $is_current );
			?>
				<tr>
					<td><?php echo esc_html( $default_id ); ?></td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-search&action=edit&id=' . $default_id ) ); ?>">
							<?php echo esc_html( $default_name ); ?>
						</a>
					</td>
					<td>

					</td>
					<td>
						<input type="checkbox" value="1"
						       id="sws-enhance-<?php echo esc_attr( $default_id ); ?>"
						       class="ymapp-switcher sws-enhance-toggle<?php echo $is_disabled ? ' sws-enhance-locked' : ''; ?>"
						       data-widget-id="<?php echo esc_attr( $default_id ); ?>"
						       <?php checked( $is_current ); ?>
						/>
						<label for="sws-enhance-<?php echo esc_attr( $default_id ); ?>"></label>
						<span class="sws-enhance-spinner spinner"></span>
					</td>
					<td>
						<a href="#" class="ysm-widget-remove" data-id="<?php echo esc_attr( $default_id ); ?>" title="<?php esc_attr_e( 'Delete', 'smart-woocommerce-search' ); ?>">
							<span class="dashicons dashicons-trash"></span>
						</a>
						<span class="spinner ysm-action-spinner"></span>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

	</div>

	<?php endif; ?>

	<h2 class="ysm-widgets-title"><?php esc_html_e( 'Search Widgets', 'smart-woocommerce-search' ); ?></h2>

	<div class="ysm-widgets-list ysm-custom-widgets-list">

		<table>
			<thead>
				<tr>
					<td width="10%"><?php esc_html_e( 'ID', 'smart-woocommerce-search' ); ?></td>
					<td width="35%"><?php esc_html_e( 'Title', 'smart-woocommerce-search' ); ?></td>
					<td width="20%"><?php esc_html_e( 'Shortcode', 'smart-woocommerce-search' ); ?></td>
					<td width="20%"><?php esc_html_e( 'Enhance Default', 'smart-woocommerce-search' ); ?></td>
					<td width="15%"></td>
				</tr>
			</thead>
			<tbody>
			<?php
			if ( $widgets ) {
				foreach ( $widgets as $id => $widget ) {
					$is_current = ( $sws_enhance_widget_id === (string) $id );
					/* @codingStandardsIgnoreLine */
					echo ysm_get_widget_list_row_template( array(
						'id'               => (int) $id,
						'name'             => $widget['name'],
						'enhance_checked'  => $is_current,
						'enhance_disabled' => ( $sws_enhance_is_taken && ! $is_current ),
					) );
				}
			}
			?>
			</tbody>
		</table>

	</div>
	<?php wp_nonce_field(
		'ysm_widgets_nonce_action',
		'ysm_widgets_nonce'
	); ?>
	<br>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-search-custom-new' ) ); ?>" class="ymapp-button-small"><?php esc_html_e( 'Add New', 'smart-woocommerce-search' ); ?></a>

<?php } ?>

</div>

<script type="text/html" id="tmpl-ysm-widget-list-row">
	<?php
	/* @codingStandardsIgnoreLine */
	echo ysm_get_widget_list_row_template( array(
		'id'   => '{{ data.id }}',
		'name' => '{{ data.name }}',
	) );
	?>
</script>
