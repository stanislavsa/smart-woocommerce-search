<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summer_2024_promo_name = 'sws_promo_dismiss_summer_2024';
$display = true;
if ( ! get_option( $summer_2024_promo_name ) ) {
	$display = false;
}

$time_end = strtotime('2 December 2024');
$time_now = current_time( 'U' );
if ( $time_now > $time_end ) {
	$display = false;
}

$label = 'Black Friday Sale';
$enddate = '1st December';
$promocode = 'blackfriday24';
$percentage = 20;
$utm_campaign = 'sale2024';
?>

<?php if ( $display ) : ?>
<div class="sws-promo-container updated" data-dismiss="<?php echo esc_attr( $summer_2024_promo_name ); ?>">
	<div class="sws-promo-container__inner">
		<div class="sws-promo-container__left">
			<img src="<?php echo esc_url( SWS_PLUGIN_URI . '/assets/images/icon-128x128.png' ); ?>" width="60" height="60">
		</div>
		<div class="sws-promo-container__right">
			<h3 class="sws-promo-container__heading">
				<?php echo esc_html( $percentage ); ?>% off - <?php echo esc_html( $label ); ?>
			</h3>
			<p>Make your site's search even faster with the PRO data indexing feature, synonyms and stop words.
				Search in Variations, Custom Post Types, Custom Taxonomies, Custom Fields, Product Attributes.
				Check how it works and looks on the <a href="https://demo.wpsmartsearch.com/" target="_blank">Demo</a>.
				Pro is compatible with WPML and Polylang plugins.
				<br><br>Save <?php echo esc_html( $percentage ); ?>% off with the promo code <b><?php echo esc_html( $promocode ); ?></b> at <a href="https://www.wpsmartsearch.com/features/?utm_source=free_plugin&utm_medium=banner&utm_campaign=<?php echo esc_html( $utm_campaign ); ?>" target="_blank">Checkout</a>. Hurry, offer ends <?php echo esc_html( $enddate ); ?>!</p>
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