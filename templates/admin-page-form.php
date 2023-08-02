<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_script( 'postbox' );

$tabs = array(
	'general_tab'    => __( 'General', 'smart-woocommerce-search' ),
	'fields_tab'     => __( 'Items to Search Through', 'smart-woocommerce-search' ),
	'layout_tab'     => __( 'Layout', 'smart-woocommerce-search' ),
	'styles_tab'     => __( 'Styling', 'smart-woocommerce-search' ),
	'spellcheck_tab' => __( 'Spell Correction', 'smart-woocommerce-search' ),
	'stopwords_tab'  => __( 'Stop Words', 'smart-woocommerce-search' ),
);
?>
<form method="post" action="" enctype="multipart/form-data">

	<input type="submit" value="<?php esc_attr_e( 'Save', 'smart-woocommerce-search' ); ?>" name="save" class="ymapp-button ymapp-hide-on-mobile" style="float:right;" />

	<?php if ( in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) { ?>
		<?php wp_nonce_field( 'smart_search_default' ); ?>
		<div class="ysm-widget-edit-title-wrap">
			<h2 class="ysm-widgets-title"><?php echo esc_html( ysm_get_default_widgets_names( $w_id ) ); ?></h2>
		</div>
	<?php } else { ?>
		<?php wp_nonce_field( 'smart_search_custom' ); ?>
		<div class="ysm-widget-edit-title-wrap">
			<input type="text" name="name" size="30" value="<?php echo isset( $widgets[ $w_id ] ) ? esc_html( $widgets[ $w_id ]['name'] ) : ''; ?>" placeholder="<?php esc_html_e( 'Enter name', 'smart-woocommerce-search' ); ?>" autocomplete="off">
		</div>
	<?php } ?>

	<div class="clear"></div>

	<div class="meta-box-sortables">

		<div class="postbox ysm-widget-edit-settings">

			<button type="button" class="handlediv button-link" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Settings', 'smart-woocommerce-search' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>

			<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Settings', 'smart-woocommerce-search' ); ?></span></h2>

			<div class="inside">

				<h2 class="nav-tab-wrapper" id="ymapp-settings__nav">
					<?php foreach ( $tabs as $id => $title ) { ?>
						<span data-href="#<?php echo esc_attr( $id ); ?>" class="nav-tab<?php echo 'general_tab' === $id ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $title ); ?></span>
					<?php } ?>
				</h2>

				<?php include 'admin-tab-general.php'; ?>

				<?php include 'admin-tab-fields.php'; ?>

				<?php include 'admin-tab-layout.php'; ?>

				<?php include 'admin-tab-styling.php'; ?>

				<?php include 'admin-tab-spellcheck.php'; ?>

				<?php include 'admin-tab-stopwords.php'; ?>

			</div>

		</div>

	</div>

	<p class="submit" style="float: right;">
		<input type="submit" value="<?php esc_attr_e( 'Save', 'smart-woocommerce-search' ); ?>" name="save" class="ymapp-button" />
	</p>

</form>

<?php include_once __DIR__ . '/promo/promo-banners.php'; ?>
