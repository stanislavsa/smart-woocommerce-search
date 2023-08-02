<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$widgets = ysm_get_custom_widgets();
$w_id = 0;
$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

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

		<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

	<?php } else { ?>

		<h1>
			<span><?php echo esc_html( get_admin_page_title() ); ?></span>
		</h1>

		<h2 class="ysm-widgets-title"><?php esc_html_e( 'Extend Default Widgets', 'smart-woocommerce-search' ); ?></h2>

		<div class="ysm-widgets-list">

			<table>
				<thead>
				<tr>
					<td width="10%"><?php esc_html_e( 'ID', 'smart-woocommerce-search' ); ?></td>
					<td width="40%"><?php esc_html_e( 'Name', 'smart-woocommerce-search' ); ?></td>
					<td width="25%"></td>
					<td width="15%"></td>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( ysm_get_default_widgets_ids() as $id ) {
					$type = 'default' === $id ? '' : '_' . $id;
					$enabled = ysm_get_option( $id, 'enable' . $type . '_search' );
					echo '<tr>
						<td>' . esc_html( $id ) . '</td>
						<td>
							<a href="' . esc_url( admin_url( 'admin.php?page=smart-search&action=edit&id=' . $id ) ) . '">
								' . esc_html( ysm_get_default_widgets_names( $id ) ) . '
							</a>
						</td>
						<td>' .
						(
						! empty( $enabled ) ?
							'<span style="color:green">' . esc_html__( 'Enabled', 'smart-woocommerce-search' ) . '</span>' :
							'<span style="color:#ccc">' . esc_html__( 'Disabled', 'smart-woocommerce-search' ) . '</span>'
						) . '</td>
						<td></td>
					</tr>';
				}
				?>
				</tbody>
			</table>

		</div>

		<h2 class="ysm-widgets-title"><?php esc_html_e( 'Custom Widgets', 'smart-woocommerce-search' ); ?></h2>

		<div class="ysm-widgets-list ysm-custom-widgets-list">

			<table>
				<thead>
				<tr>
					<td width="10%"><?php esc_html_e( 'ID', 'smart-woocommerce-search' ); ?></td>
					<td width="40%"><?php esc_html_e( 'Name', 'smart-woocommerce-search' ); ?></td>
					<td width="25%"><?php esc_html_e( 'Shortcode', 'smart-woocommerce-search' ); ?></td>
					<td width="15%"></td>
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
