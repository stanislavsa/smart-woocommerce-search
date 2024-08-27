<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summer_2024_promo_name = 'sws_promo_dismiss_summer_2024';
?>

<?php if ( ! get_option( $summer_2024_promo_name ) ) : ?>
<div class="sws-promo-container updated" data-dismiss="<?php echo esc_attr( $summer_2024_promo_name ); ?>">
	<div class="sws-promo-container__inner">
		<div class="sws-promo-container__left">
			<img src="<?php echo esc_url( SWS_PLUGIN_URI . '/assets/images/icon-128x128.png' ); ?>" width="60" height="60">
		</div>
		<div class="sws-promo-container__right">
			<h3 class="sws-promo-container__heading">
				30% off - Endless Summer Sale
			</h3>
			<p>Make your site's search even faster with the PRO data indexing feature, synonyms and stop words.
				Search in Variations, Custom Post Types, Custom Taxonomies, Custom Fields, Product Attributes.
				Check how it works and looks on the <a href="https://demo.wpsmartsearch.com/" target="_blank">Demo</a>.
				Pro is compatible with WPML and Polylang plugins.
				<br><br>Save 30% off with the promo code <b>summer2024promo</b> at <a href="https://www.wpsmartsearch.com/features/?utm_source=free_plugin&utm_medium=banner&utm_campaign=sale2024" target="_blank">Checkout</a>. Hurry, offer ends 30th September!</p>
			<div class="sws-promo-container__dismiss">
				<a href="#">Dismiss</a>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php endif; ?>

<?php wp_nonce_field( 'sws_promo_dismiss_nonce_action', 'sws_promo_dismiss_nonce' ); ?>

<script>
	(function($) {
		$('.sws-promo-container__dismiss > a').on('click', function (e) {
			e.preventDefault();

			var promoContainer = $(this).parents('.sws-promo-container');

			promoContainer.slideUp();

			$.post(
				ajaxurl,
				{
					action: 'sws_promo_dismiss',
					name: promoContainer.attr('data-dismiss'),
					nonce: $('#sws_promo_dismiss_nonce').val()
				},
				function( response ) {}
			);
		});
	})(jQuery);
</script>