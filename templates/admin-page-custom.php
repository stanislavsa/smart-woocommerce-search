<?php
if ( !defined('ABSPATH') ) exit;

$widgets = ysm_get_custom_widgets();
$w_id = 0;

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
	$w_id = (int) $_GET['id'];
}

?>

<div class="wrap">

<?php if ( $w_id && isset($widgets[$w_id]) ) { ?>

	<h1><?php echo __('Edit Widget', 'smart_search'); ?></h1>

	<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

<?php } else { ?>

	<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
		<a href="<?php echo admin_url('admin.php?page=smart-search-custom-new') ?>" class="add-new-h2"><?php _e('Add New', 'smart_search') ?></a>
	</h1>

	<div class="ysm-widgets-list">

		<table>
			<thead>
				<tr>
					<td><?php _e('ID', 'smart_search') ?></td>
					<td><?php _e('Name', 'smart_search') ?></td>
					<td><?php _e('Shortcode', 'smart_search') ?></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($widgets as $id => $widget) {

				$args = array(
					'id' => (int) $id,
					'name' => esc_html( $widget['name'] ),
				);
				echo ysm_get_widget_list_row_template($args);

			} ?>
			</tbody>
		</table>

	</div>

<?php } ?>

</div>

<script type="text/html" id="tmpl-ysm-widget-list-row">
	<?php
	$args = array(
		'id' => '{{ data.id }}',
		'name' => '{{ data.name }}',
	);
	echo ysm_get_widget_list_row_template($args);
	?>
</script>
