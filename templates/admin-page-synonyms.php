<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display: block">

		<div class="ysm-inside ysm-synonyms-holder">
			<?php
			\YummyWP\App\Field::output( 'synonyms', [
				'name'        => 'synonyms',
				'type'        => 'pro',
				'title'       =>
					__( 'Spell Correction.', 'smart-woocommerce-search' )
					. ' '
					. __( 'Applies globally to all widgets.', 'smart-woocommerce-search' ),
				'description' => __( 'Add synonyms for words that you think could be misspelled.<br> Example Orange => Ornge', 'smart-woocommerce-search' ),
			] );
			?>
			<br><br>
			<a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>" class="ymapp-button">
				<?php esc_html_e( 'Upgrade to Pro', 'smart-woocommerce-search' ); ?>
			</a>

		</div>

	</div>

</div>