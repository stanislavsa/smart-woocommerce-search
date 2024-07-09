<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

	<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display: block">

		<div class="ysm-inside ysm-synonyms-holder">
			<?php
			\YummyWP\App\Field::output( 'synonyms', [
				'name'        => 'synonyms',
				'type'        => 'pro',
				'title'       =>
					__( 'Spelling Correction', 'smart-woocommerce-search' )
					. '. '
					. __( 'Applies globally to all widgets.', 'smart-woocommerce-search' ),
				'description' => __( 'Add synonyms for words that you think could be misspelled', 'smart-woocommerce-search' )
								 . '<br>'
								 . __( 'Example', 'smart-woocommerce-search' ) . ' Orange => Ornge',
			] );
			?>
			<br><br>
			<a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>" class="ymapp-button">
				<?php esc_html_e( 'Upgrade to Pro', 'smart-woocommerce-search' ); ?>
			</a>

		</div>

	</div>


	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" style="margin-top: 30px;" href="https://www.wpsmartsearch.com/docs/synonyms/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>