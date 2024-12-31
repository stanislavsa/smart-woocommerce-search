<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notice_option = 'sws_update_notice';
$display = false;
if ( SWS_PLUGIN_UPDATE_NOTICE > get_option( $notice_option ) ) {
	$display = true;
}

?>

<?php if ( $display ) : ?>
	<div class="sws-promo-container updated" data-dismiss="<?php echo esc_attr(SWS_PLUGIN_UPDATE_NOTICE) ?>">
		<div class="sws-promo-container__inner">
			<div class="sws-promo-container__left">
				<img src="<?php echo esc_url( SWS_PLUGIN_URI . 'assets/images/icon-128x128.png' ); ?>" width="60" height="60">
			</div>
			<div class="sws-promo-container__right">
				<h3 class="sws-promo-container__heading">
					Welcome to the New Smart WooCommerce Search! 🚀
				</h3>
				<p>We’ve just made the search experience even better! Here's what’s new in this update:</p>
				<p>
					<strong>Fullscreen Mode:</strong> the search bar and results now look stunning in a fullscreen popup.
					<br>
					<a href="https://www.wpsmartsearch.com/docs/fullscreen-mode/" target="_blank">Learn more</a>
				</p>
				<p>
					<strong>Recent Searches:</strong> recent search keywords displayed below the search bar.
					<br>
					<a href="https://www.wpsmartsearch.com/docs/general-settings/#12-toc-title" target="_blank">Learn more</a>
				</p>
				<p>
					<strong>"Did You Mean?": </strong> smart keyword suggestions below the search bar based on the search query.
				</p>
				<p style="display:flex;align-items: center; column-gap: 10px">
					<a href="https://www.wpsmartsearch.com/docs/synonyms/" target="_blank">Learn more</a>
					<?php if (!sws_fs()->is_premium()) { ?>
						<i>Available only in PRO version</i>
						<strong>
							<a href="https://www.wpsmartsearch.com/features/?utm_source=free_plugin&utm_medium=banner&utm_campaign=new_features" target="_blank">Upgrade to Pro</a>
						</strong>
					<?php } ?>
				</p>
				<div class="sws-promo-container__dismiss">
					<span class="js-notice-updates-dismiss">Dismiss</span>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php endif; ?>

<?php wp_nonce_field( 'sws_update_notice_dismiss_nonce_action', 'sws_update_notice_dismiss_nonce' ); ?>

<script>
	(function($) {
		$('.js-notice-updates-dismiss').on('click', function (e) {
			e.preventDefault();

			var promoContainer = $(this).parents('.sws-promo-container');

			promoContainer.slideUp();

			$.post(
				ajaxurl,
				{
					action: 'sws_notice_dismiss',
					version: promoContainer.attr('data-dismiss'),
					nonce: $('#sws_update_notice_dismiss_nonce').val()
				},
				function( response ) {}
			);
		});
	})(jQuery);
</script>