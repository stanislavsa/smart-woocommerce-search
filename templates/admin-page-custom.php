<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$widgets = ysm_get_custom_widgets();
$w_id = 0;
$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );

if ( $action && 'edit' === $action && $id ) {
	$w_id = (int) $id;
}

?>

<div class="wrap">

	<?php if ( $w_id && isset( $widgets[ $w_id ] ) ) { ?>

		<h1><span><?php esc_html_e( 'Edit Widget', 'smart_search' ); ?></span></h1>

		<?php ysm_message(); ?>

		<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

	<?php } else { ?>

		<h1>
			<span><?php echo esc_html( get_admin_page_title() ); ?></span>
		</h1>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-search-custom-new' ) ); ?>" class="ymapp-button-small"><?php esc_html_e( 'Add New', 'smart_search' ); ?></a>

		<div class="ysm-widgets-list">

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
				foreach ( $widgets as $id => $widget ) {
					/* @codingStandardsIgnoreLine */
					echo ysm_get_widget_list_row_template( array(
						'id'   => (int) $id,
						'name' => $widget['name'],
					) );
				}
				?>
				</tbody>
			</table>

		</div>

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
