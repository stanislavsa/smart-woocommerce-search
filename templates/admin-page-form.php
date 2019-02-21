<?php
if ( !defined('ABSPATH') ) exit;

wp_enqueue_script('postbox');

$tabs = array(
	'ysm_w_settings_general' => __('General', 'smart_search'),
	'ysm_w_settings_fields' => __('Items to Search through', 'smart_search'),
	'ysm_w_settings_styles' => __('Styling', 'smart_search'),
);
?>

<div class="ysm-wrap-left">
<form method="post" action="" enctype="multipart/form-data">

	<?php if ($w_id !== 'product' && $w_id !== 'default') { ?>
	<p class="submit" style="float: right;">
		<input type="submit" value="<?php _e('Save', 'smart_search') ?>" name="save" class="button-primary" />
		<?php
		if ($w_id === 'product' || $w_id === 'default') {
			wp_nonce_field( 'smart_search_default' );
		} else {
			wp_nonce_field( 'smart_search_custom' );
		}
		?>
	</p>
	<?php } ?>

	<?php
	if ($w_id !== 'product' && $w_id !== 'default') {

		$w_title = !empty($w_id) ? $widgets[$w_id]['name'] : '';
		?>
		<div class="ysm-widget-edit-title-wrap">

			<input type="text" name="name" size="30" value="<?php echo esc_html($w_title) ?>" placeholder="<?php _e('Enter name', 'smart_search') ?>" autocomplete="off">

		</div>
		<?php
	}
	?>

	<div class="clear"></div>

	<div class="meta-box-sortables">

		<div class="postbox ysm-widget-edit-settings">

			<button type="button" class="handlediv button-link" aria-expanded="true">
				<span class="screen-reader-text"><?php _e('Toggle panel: Settings', 'smart_search') ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>

			<h2 class="hndle ui-sortable-handle"><span><?php _e('Settings', 'smart_search') ?></span></h2>

			<div class="inside">

				<h2 class="nav-tab-wrapper" id="ysm-widget-settings-nav-wrapper">
					<?php foreach ($tabs as $id => $title) { ?>
						<a href="#<?php echo esc_html($id) ?>" class="nav-tab<?php echo $id === 'ysm_w_settings_general' ? ' nav-tab-active' : '' ?>"><?php echo esc_html($title) ?></a>
					<?php } ?>
				</h2>

				<br>

				<?php require_once __DIR__ . '/admin-page-form-general.php'; ?>

				<?php require_once __DIR__ . '/admin-page-form-fields.php'; ?>

				<?php require_once __DIR__ . '/admin-page-form-styles.php'; ?>

			</div>

		</div>

	</div>

	<p class="submit">
		<input type="submit" value="<?php _e('Save', 'smart_search') ?>" name="save" class="button-primary" />
		<?php
		if ($w_id === 'product' || $w_id === 'default') {
			wp_nonce_field( 'smart_search_default' );
		} else {
			wp_nonce_field( 'smart_search_custom' );
		}
		?>
	</p>

</form>
</div>
<div class="ysm-wrap-right">
	<?php include_once __DIR__ . '/promo/promo-banners.php'; ?>
</div>