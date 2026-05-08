<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

	<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display: block">
		<div class="ysm-inside">

			<h4 class="ymapp-settings__title"><?php esc_html_e( 'Google Analytics 4', 'smart-woocommerce-search' ); ?></h4>

			<?php $sws_ga4_enabled = (bool) get_option( 'sws_enable_google_analytics' ); ?>

			<div class="sws-ga-event-row">
				<div style="text-align: left; margin: 60px 0 8px;display: flex;">
					<input type="checkbox"
						   id="sws-ga4-enable"
						   class="ymapp-switcher sws-ga4-toggle"
						   <?php checked( $sws_ga4_enabled ); ?>
					/>
					<label for="sws-ga4-enable"></label>
					<span class="sws-ga-event-label"><?php esc_html_e( 'Enable Google Analytics 4 tracking.', 'smart-woocommerce-search' ); ?> <?php esc_html_e( 'Event:', 'smart-woocommerce-search' ); ?> <code>swsEvent</code></span>
					<span class="sws-ga4-spinner spinner"></span>
				</div>
			</div>

			<div class="sws-ga-event-details" style="<?php echo $sws_ga4_enabled ? '' : 'display:none;'; ?>">

				<h4 style="margin-bottom: 8px;"><?php esc_html_e( 'Event Categories', 'smart-woocommerce-search' ); ?></h4>
				<p class="description">
					<?php esc_html_e( 'Toggle which events are sent to Google Analytics.', 'smart-woocommerce-search' ); ?><br>
					<code><?php esc_html_e( 'Note: to accurately track click events, clicked links will open in a new window.', 'smart-woocommerce-search' ); ?></code>
				</p>

				<?php
				$sws_ga_enabled_events = (array) get_option( 'sws_ga_enabled_events', [] );
				$sws_ga_event_labels   = [
					'search_term_has_results'    => [ 'label' => __( '[Search term] has results',              'smart-woocommerce-search' ), 'description' => __( 'When user saw search results',                                  'smart-woocommerce-search' ), 'premium' => false ],
					'search_term_no_results'     => [ 'label' => __( '[Search term] no results',               'smart-woocommerce-search' ), 'description' => __( 'When user saw a "no results" message',                          'smart-woocommerce-search' ), 'premium' => false ],
					'search_results_link_click'  => [ 'label' => __( '[Found Suggestions] link click',         'smart-woocommerce-search' ), 'description' => __( 'When user clicked on a suggestion',                             'smart-woocommerce-search' ), 'premium' => false ],
					'search_results_cart_click'  => [ 'label' => __( '[Found Suggestions] add-to-cart click',  'smart-woocommerce-search' ), 'description' => __( 'When user clicked a suggestion\'s Add to Cart button',          'smart-woocommerce-search' ), 'premium' => true  ],
					'view_all_click'             => [ 'label' => __( '[View All] button click',                'smart-woocommerce-search' ), 'description' => __( 'When user clicked the View All button',                          'smart-woocommerce-search' ), 'premium' => false ],
					'promo_banner_click'         => [ 'label' => __( '[Promo banner] link click',              'smart-woocommerce-search' ), 'description' => __( 'When user clicked a banner in the fullscreen popup',             'smart-woocommerce-search' ), 'premium' => true  ],
					'selected_categories_click'  => [ 'label' => __( '[Selected Categories] link click',       'smart-woocommerce-search' ), 'description' => __( 'When user clicked a category in the fullscreen popup',           'smart-woocommerce-search' ), 'premium' => false  ],
					'recommended_products_click' => [ 'label' => __( '[Recommended Products] link click',      'smart-woocommerce-search' ), 'description' => __( 'When user clicked a recommended product in the fullscreen popup','smart-woocommerce-search' ), 'premium' => true  ],
				];
				?>

				<div class="sws-ga-events-wrap">
				<?php foreach ( $sws_ga_event_labels as $sws_event_key => $sws_event ) :
					if ( $sws_event['premium'] && ! sws_fs()->is__premium_only() ) continue;
					$sws_event_enabled = in_array( $sws_event_key, $sws_ga_enabled_events, true );
				?>
					<div class="sws-ga-event-row">
						<input type="checkbox"
						       id="sws-ga-event-<?php echo esc_attr( $sws_event_key ); ?>"
						       class="ymapp-switcher sws-ga-event-toggle"
						       data-event="<?php echo esc_attr( $sws_event_key ); ?>"
						       <?php checked( $sws_event_enabled ); ?>
						/>
						<label for="sws-ga-event-<?php echo esc_attr( $sws_event_key ); ?>"></label>
						<span class="sws-ga-event-label">
							<code><?php echo esc_html( $sws_event['label'] ); ?></code>
							<span class="description">&mdash; <?php echo esc_html( $sws_event['description'] ); ?></span>
						</span>
						<span class="sws-ga-event-spinner spinner"></span>
					</div>
				<?php endforeach; ?>
				</div>

				<?php wp_nonce_field( 'ysm_widgets_nonce_action', 'ysm_widgets_nonce' ); ?>

			</div><!-- .sws-ga-event-details -->

			<?php wp_nonce_field( 'ysm_analytics_nonce_action', 'ysm_analytics_nonce' ); ?>

		</div>
	</div>

	<a class="ymapp-settings__doc_link" href="https://www.wpsmartsearch.com/docs/analytics/" target="_blank">
		<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
		<?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?>
	</a>
</div>