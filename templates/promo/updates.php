<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notice_option = 'sws_update_notice';
$display = false;
if ( SWS_PLUGIN_UPDATE_NOTICE > get_option( $notice_option ) ) {
	$display = true;
}

$is_free = ! sws_fs()->is_premium();
$upgrade_url = 'https://www.wpsmartsearch.com/features/?utm_source=free_plugin&utm_medium=update_notice&utm_campaign=pro_features';

?>

<?php if ( $display ) : ?>
	<div class="sws-promo-container updated" data-dismiss="<?php echo esc_attr( SWS_PLUGIN_UPDATE_NOTICE ); ?>">
		<div class="sws-promo-container__inner">

			<div class="sws-promo-container__left">
				<img src="<?php echo esc_url( SWS_PLUGIN_URI . 'assets/images/icon-128x128.png' ); ?>" width="60" height="60" alt="Smart WooCommerce Search">
			</div>

			<div class="sws-promo-container__right">

				<h3 class="sws-promo-container__heading">
					<?php esc_html_e( 'Smart WooCommerce Search — What\'s New 🚀', 'smart-woocommerce-search' ); ?>
				</h3>

				<p><?php esc_html_e( 'We\'ve just shipped improvements to make your store search smarter and faster. Here\'s what\'s new:', 'smart-woocommerce-search' ); ?></p>

				<div class="sws-promo-features">

					<div class="sws-promo-feature">
						<strong><?php esc_html_e( 'Relevance Configurator:', 'smart-woocommerce-search' ); ?></strong>
						<?php esc_html_e( 'Fine-tune how search results are ranked — boost weight for title, SKU, or description matches so the most relevant products always rise to the top.', 'smart-woocommerce-search' ); ?>
						<?php if ( $is_free ) : ?>
							<a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>" class="sws-promo-badge-pro"><?php esc_html_e( 'PRO', 'smart-woocommerce-search' ); ?></a>
						<?php endif; ?>
					</div>

					<div class="sws-promo-feature">
						<strong><?php esc_html_e( 'Google Analytics (GA4) Integration:', 'smart-woocommerce-search' ); ?></strong>
						<?php esc_html_e( 'Track every search interaction — queries, clicks, and no-results events — directly in your GA4 dashboard.', 'smart-woocommerce-search' ); ?>
						<a href="https://www.wpsmartsearch.com/docs/analytics/" target="_blank"><?php esc_html_e( 'Learn more', 'smart-woocommerce-search' ); ?></a>
					</div>

				</div>

				<?php if ( $is_free ) : ?>
					<div class="sws-promo-upgrade">
						<p class="sws-promo-upgrade__heading"><?php esc_html_e( 'Unlock all PRO features:', 'smart-woocommerce-search' ); ?></p>
						<ul class="sws-promo-upgrade__list">
							<li>✦ <a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>"><?php esc_html_e( 'Relevance Configurator', 'smart-woocommerce-search' ); ?></a> — <?php esc_html_e( 'control exactly how search results are ranked', 'smart-woocommerce-search' ); ?></li>
							<li>✦ <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"><?php esc_html_e( '"Did You Mean" Keywords & Synonyms', 'smart-woocommerce-search' ); ?></a> — <?php esc_html_e( 'automatically correct typos and suggest alternatives', 'smart-woocommerce-search' ); ?></li>
							<li>✦ <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"><?php esc_html_e( 'Search in Variations, Custom Fields & Taxonomies', 'smart-woocommerce-search' ); ?></a> — <?php esc_html_e( 'find any product by any attribute', 'smart-woocommerce-search' ); ?></li>
							<li>✦ <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"><?php esc_html_e( 'Product Labels & Add to Cart Button', 'smart-woocommerce-search' ); ?></a> — <?php esc_html_e( 'convert directly from the search popup', 'smart-woocommerce-search' ); ?></li>
							<li>✦ <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"><?php esc_html_e( 'Data Indexing & Grid Layout', 'smart-woocommerce-search' ); ?></a> — <?php esc_html_e( 'blazing fast results even for large catalogs', 'smart-woocommerce-search' ); ?></li>
							<li>✦ <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"><?php esc_html_e( 'Stop Words, WPML & Polylang support', 'smart-woocommerce-search' ); ?></a></li>
						</ul>
						<a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>" class="button button-primary sws-promo-upgrade__btn">
							<?php esc_html_e( 'Start 14-Day Free Trial', 'smart-woocommerce-search' ); ?>
						</a>
						<a href="<?php echo esc_url( $upgrade_url ); ?>" class="sws-promo-upgrade__link" target="_blank">
							<?php esc_html_e( 'View all PRO features →', 'smart-woocommerce-search' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<div class="sws-promo-container__dismiss">
					<span class="js-notice-updates-dismiss"><?php esc_html_e( 'Dismiss', 'smart-woocommerce-search' ); ?></span>
				</div>

			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php endif; ?>

<?php wp_nonce_field( 'sws_update_notice_dismiss_nonce_action', 'sws_update_notice_dismiss_nonce' ); ?>

<style>
    .sws-promo-features {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 24px;
        margin: 8px 0 12px;
    }
    .sws-promo-feature {
        flex: 1 1 300px;
        font-size: 13px;
        line-height: 1.5;
    }
    .sws-promo-feature a {
        margin-left: 6px;
    }
    .sws-promo-upgrade {
        background: #f0f6fc;
        border-left: 4px solid #2271b1;
        padding: 12px 16px;
        margin: 12px 0;
        border-radius: 0 4px 4px 0;
    }
    .sws-promo-upgrade__heading {
        font-weight: 600;
        margin: 0 0 8px;
        font-size: 13px;
    }
    .sws-promo-upgrade__list {
        margin: 0 0 12px 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        gap: 4px 24px;
    }
    .sws-promo-upgrade__list li {
        flex: 1 1 280px;
        font-size: 12px;
        line-height: 1.6;
        color: #3c434a;
    }
    .sws-promo-upgrade__list li a {
        font-weight: 600;
        text-decoration: none;
    }
    .sws-promo-upgrade__btn {
        margin-right: 12px !important;
        font-size: 13px !important;
    }
    .sws-promo-upgrade__link {
        font-size: 12px;
        color: #2271b1;
    }
    .sws-promo-badge-pro {
        display: inline-block;
        background: #f0a500;
        color: #fff !important;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        padding: 2px 6px;
        border-radius: 3px;
        text-decoration: none !important;
        vertical-align: middle;
        margin-left: 6px;
        letter-spacing: 0.5px;
    }
    .sws-promo-badge-pro:hover {
        background: #d4890a;
    }
    .sws-promo-container__dismiss {
        margin-top: 10px;
        font-size: 12px;
        color: #999;
        cursor: pointer;
    }
    .sws-promo-container__dismiss:hover {
        color: #555;
    }
</style>

<script>
	(function($) {
		$('.js-notice-updates-dismiss').on('click', function(e) {
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
				function(response) {}
			);
		});
	})(jQuery);
</script>
