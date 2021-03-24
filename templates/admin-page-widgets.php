<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$widgets = ysm_get_custom_widgets();
$w_id = 0;
$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );

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

		<h1><span><?php esc_html_e( 'Edit Widget', 'smart_search' ); ?></span></h1>

		<?php ysm_message(); ?>

		<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

	<?php } else { ?>

		<h1>
			<span><?php echo esc_html( get_admin_page_title() ); ?></span>
		</h1>

		<h2 class="ysm-widgets-title"><?php esc_html_e( 'Extend Default Widgets', 'smart_search' ); ?></h2>

		<div class="ysm-widgets-list">

			<table>
				<thead>
				<tr>
					<td width="25%"><?php esc_html_e( 'Name', 'smart_search' ); ?></td>
					<td width="25%"></td>
					<td width="25%"></td>
					<td width="25%"></td>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( ysm_get_default_widgets_ids() as $id ) {
					$type = 'default' === $id ? '' : '_' . $id;
					$enabled = ysm_get_option( $id, 'enable' . $type . '_search' );
					echo '<tr>
						<td>
							<a href="' . esc_url( admin_url( 'admin.php?page=smart-search&action=edit&id=' . $id ) ) . '">
								' . esc_html( ysm_get_default_widgets_names( $id ) ) . '
							</a>
						</td>
						<td>' .
						(
						! empty( $enabled ) ?
							'<span style="color:green">' . esc_html__( 'Enabled', 'smart_search' ) . '</span>' :
							'<span style="color:#ccc">' . esc_html__( 'Disabled', 'smart_search' ) . '</span>'
						) . '</td>
						<td></td>
						<td></td>
					</tr>';
				}
				?>
				</tbody>
			</table>

		</div>

		<h2 class="ysm-widgets-title"><?php esc_html_e( 'Custom Widgets', 'smart_search' ); ?></h2>

		<div class="ysm-widgets-list ysm-custom-widgets-list">

			<table>
				<thead>
				<tr>
					<td><?php esc_html_e( 'ID', 'smart_search' ); ?></td>
					<td><?php esc_html_e( 'Name', 'smart_search' ); ?></td>
					<td><?php esc_html_e( 'Shortcode', 'smart_search' ); ?></td>
					<td></td>
				</tr>
				</thead>
				<tbody>
				<?php
				if ( $widgets ) {
					foreach ( $widgets as $id => $widget ) {
						/* @codingStandardsIgnoreLine */
						echo ysm_get_widget_list_row_template( array(
							'id'   => (int) $id,
							'name' => $widget['name'],
						) );
					}
				}
				?>
				</tbody>
			</table>

		</div>

		<br>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-search-custom-new' ) ); ?>" class="ymapp-button-small"><?php esc_html_e( 'Add New', 'smart_search' ); ?></a>

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
