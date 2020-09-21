<?php
if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_script( 'postbox' );

$tabs = array(
	'general_tab'    => __( 'General', 'smart_search' ),
	'fields_tab'     => __( 'Items to Search through', 'smart_search' ),
	'layout_tab'     => __( 'Layout', 'smart_search' ),
	'styles_tab'     => __( 'Styling', 'smart_search' ),
	'spellcheck_tab' => __( 'Spell Correction', 'smart_search' ),
	'stopwords_tab'  => __( 'Stop Words', 'smart_search' ),
);
?>

<div class="ysm-wrap-left">
<form method="post" action="" enctype="multipart/form-data">

	<input type="submit" value="<?php esc_html_e( 'Save', 'smart_search' ); ?>" name="save" class="ymapp-button ymapp-hide-on-mobile" style="float:right;" />

	<?php
	if ( $w_id === 'product' || $w_id === 'default' ) {
		wp_nonce_field( 'smart_search_default' );
	} else {
		wp_nonce_field( 'smart_search_custom' );
	}
	?>

	<?php
	if ( $w_id !== 'product' && $w_id !== 'default' ) {
		$w_title = ! empty( $w_id ) ? $widgets[ $w_id ]['name'] : '';
		?>
		<div class="ysm-widget-edit-title-wrap">
			<input type="text" name="name" size="30" value="<?php echo esc_html( $w_title ); ?>" placeholder="<?php esc_html_e( 'Enter name', 'smart_search' ); ?>" autocomplete="off">
		</div>
	<?php } else { ?>
		<div class="clear"></div>
		<h2 class="nav-tab-wrapper" id="ymapp-settings__nav">
			<?php foreach ( $tabs_def as $id => $title ) { ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-search&type=' . $id ) ); ?>" class="nav-tab<?php echo $current_tab === $id ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $title ); ?></a>
			<?php } ?>
		</h2>
	<?php } ?>

	<div class="clear"></div>

	<div class="meta-box-sortables">

		<div class="postbox ysm-widget-edit-settings">

			<button type="button" class="handlediv button-link" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Settings', 'smart_search' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>

			<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Settings', 'smart_search' ); ?></span></h2>

			<div class="inside">

				<h2 class="nav-tab-wrapper" id="ymapp-settings__nav">
					<?php foreach ( $tabs as $id => $title ) { ?>
						<span data-href="#<?php echo esc_html( $id ); ?>" class="nav-tab<?php echo $id === 'general_tab' ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $title ); ?></span>
					<?php } ?>
				</h2>

				<?php include 'admin-page-general-tab.php'; ?>

				<?php include 'admin-page-fields-tab.php'; ?>

				<?php include 'admin-page-layout-tab.php'; ?>

				<?php include 'admin-page-styling-tab.php'; ?>

				<?php include 'admin-page-spellcheck-tab.php'; ?>

				<?php include 'admin-page-stopwords-tab.php'; ?>

			</div>

		</div>

	</div>

	<p class="submit" style="float: right;">
		<input type="submit" value="<?php esc_html_e( 'Save', 'smart_search' ); ?>" name="save" class="ymapp-button" />
	</p>

</form>
</div>
<div class="ysm-wrap-right">
	<?php include_once __DIR__ . '/promo/promo-banners.php'; ?>
</div>