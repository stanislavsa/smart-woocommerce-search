<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $wpdb;
// ── Table reference ────────────────────────────────────────────────────────────
$sws_table_exists = false;
// ── Active tab (URL-based, no JS required) ─────────────────────────────────────
$sws_active_tab = 'ga4';
if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], ['ga4', 'stats'], true ) ) {
    $sws_active_tab = $_GET['tab'];
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
// ── Tab URLs ───────────────────────────────────────────────────────────────────
$sws_base_url = admin_url( 'admin.php?page=smart-search-analytics' );
$sws_stats_url = $sws_base_url . '&tab=stats';
$sws_ga4_url = $sws_base_url . '&tab=ga4';
// ── Period filter (stats tab only) ────────────────────────────────────────────
$sws_allowed_periods = [7, 30, 90];
$sws_period = ( isset( $_GET['period'] ) && in_array( (int) $_GET['period'], $sws_allowed_periods, true ) ? (int) $_GET['period'] : 30 );
$sws_since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$sws_period} days", current_time( 'timestamp', true ) ) );
// ── Event log table ────────────────────────────────────────────────────────────
$sws_event_table_exists = false;
// ── DB queries (only on stats tab and only if table exists) ───────────────────
$sws_total = 0;
$sws_with_results = 0;
$sws_no_results = 0;
$sws_unique = 0;
$sws_top_searches = [];
$sws_no_result_searches = [];
$sws_top_products = [];
// ── Helpers ───────────────────────────────────────────────────────────────────
$sws_date_format = get_option( 'date_format' );
$sws_datetime_format = $sws_date_format . ' ' . get_option( 'time_format' );
?>
<div class="wrap">
	<h1><span><?php 
echo esc_html( get_admin_page_title() );
?></span></h1>

	<?php 
\YummyWP\App\Notification::display();
?>

	<!-- ── Tab navigation ──────────────────────────────────────────────────── -->
	<nav class="nav-tab-wrapper" id="sws-analytics-tab-nav" style="margin-bottom: 0; border-bottom: 1px solid #c3c4c7;">
		<a href="<?php 
echo esc_url( $sws_stats_url );
?>"
		   class="nav-tab<?php 
echo ( 'stats' === $sws_active_tab ? ' nav-tab-active' : '' );
?>">
			<span class="dashicons dashicons-chart-bar" style="font-size:16px;width:16px;height:16px;margin-right:4px;vertical-align:text-bottom;" aria-hidden="true"></span>
			<?php 
esc_html_e( 'Search Stats', 'smart-woocommerce-search' );
?>
		</a>
		<a href="<?php 
echo esc_url( $sws_ga4_url );
?>"
		   class="nav-tab<?php 
echo ( 'ga4' === $sws_active_tab ? ' nav-tab-active' : '' );
?>">
			<span class="dashicons dashicons-google" style="font-size:16px;width:16px;height:16px;margin-right:4px;vertical-align:text-bottom;" aria-hidden="true"></span>
			<?php 
esc_html_e( 'Google Analytics 4', 'smart-woocommerce-search' );
?>
		</a>
	</nav>


	<?php 
/* ══════════════════════════════════════════════════════════════════
     TAB: Search Stats
   ══════════════════════════════════════════════════════════════════ */
if ( 'stats' === $sws_active_tab ) {
    ?>

	<?php 
    ?>
	<div class="notice notice-warning" style="margin-top:20px;padding:16px 20px;display:flex;align-items:center;gap:16px;">
		<span class="dashicons dashicons-lock" style="font-size:24px;color:#f0a500;flex-shrink:0;"></span>
		<div>
			<strong><?php 
    esc_html_e( 'Search Statistics is a Pro feature', 'smart-woocommerce-search' );
    ?></strong><br>
			<span style="color:#555;"><?php 
    esc_html_e( 'Upgrade to Pro to unlock detailed search analytics — top queries, no-results tracking, click and cart events, and more.', 'smart-woocommerce-search' );
    ?></span>
			&nbsp;<a href="<?php 
    echo esc_url( sws_fs()->get_upgrade_url() );
    ?>" style="color:#d63638;font-weight:600;white-space:nowrap;"><?php 
    esc_html_e( 'Upgrade to Pro &rarr;', 'smart-woocommerce-search' );
    ?></a>
		</div>
	</div>
	<?php 
    ?>

	<style>
		.sws-kpi-grid {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 16px;
			margin-bottom: 28px;
		}
		@media (max-width: 1024px) {
			.sws-kpi-grid { grid-template-columns: repeat(2, 1fr); }
		}
		@media (max-width: 480px) {
			.sws-kpi-grid { grid-template-columns: 1fr 1fr; }
		}
		.sws-table-scroll {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			margin-bottom: 32px;
		}
		.sws-table-scroll--tall {
			overflow-y: auto;
			max-height: 550px; /* ~20 rows + thead */
		}
		.sws-table-scroll--tall table.wp-list-table thead th {
			position: sticky;
			top: 0;
			z-index: 1;
			background: #f6f7f7;
			box-shadow: 0 1px 0 #c3c4c7;
		}
		.sws-table-scroll table.wp-list-table {
			min-width: 480px;
			margin-bottom: 0;
			table-layout: auto;
		}
		.sws-table-scroll table.wp-list-table th:first-child,
		.sws-table-scroll table.wp-list-table td:first-child {
			min-width: 160px;
		}
	</style>

	<div id="sws-analytics-stats" style="margin-top: 20px;">

		<!-- Period filter ─────────────────────────────────────────────────── -->
		<div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
			<span style="font-size:13px;color:#666;"><?php 
    esc_html_e( 'Show:', 'smart-woocommerce-search' );
    ?></span>
			<?php 
    foreach ( $sws_allowed_periods as $p ) {
        $p_active = $p === $sws_period;
        $p_url = add_query_arg( 'period', $p, $sws_stats_url );
        ?>
				<a href="<?php 
        echo esc_url( $p_url );
        ?>"
				   style="padding:4px 14px;border:1px solid <?php 
        echo ( $p_active ? '#2727ce' : '#c3c4c7' );
        ?>;border-radius:3px;font-size:13px;text-decoration:none;background:<?php 
        echo ( $p_active ? '#2727ce' : '#fff' );
        ?>;color:<?php 
        echo ( $p_active ? '#fff' : '#444' );
        ?>;">
					<?php 
        echo esc_html( 
            /* translators: %d: number of days */
            sprintf( _n(
                'Last %d day',
                'Last %d days',
                $p,
                'smart-woocommerce-search'
            ), $p )
         );
        ?>
				</a>
			<?php 
    }
    ?>
		</div>

		<!-- KPI strip ─────────────────────────────────────────────────────── -->
		<?php 
    $sws_found_pct = ( $sws_total > 0 ? round( $sws_with_results / $sws_total * 100 ) : 0 );
    $sws_no_pct = ( $sws_total > 0 ? round( $sws_no_results / $sws_total * 100 ) : 0 );
    $sws_kpis = [
        [
            'label' => __( 'Total Searches', 'smart-woocommerce-search' ),
            'value' => number_format_i18n( $sws_total ),
            'sub'   => '',
            'color' => '#2727ce',
        ],
        [
            'label' => __( 'With Results', 'smart-woocommerce-search' ),
            'value' => number_format_i18n( $sws_with_results ),
            'sub'   => $sws_found_pct . '%',
            'color' => '#00a32a',
        ],
        [
            'label' => __( 'No Results', 'smart-woocommerce-search' ),
            'value' => number_format_i18n( $sws_no_results ),
            'sub'   => $sws_no_pct . '%',
            'color' => '#d63638',
        ],
        [
            'label' => __( 'Unique Phrases', 'smart-woocommerce-search' ),
            'value' => number_format_i18n( $sws_unique ),
            'sub'   => '',
            'color' => '#996800',
        ]
    ];
    ?>
		<div class="sws-kpi-grid">
			<?php 
    foreach ( $sws_kpis as $kpi ) {
        ?>
			<div style="background:#fff;border:1px solid #c3c4c7;border-top:3px solid <?php 
        echo esc_attr( $kpi['color'] );
        ?>;border-radius:2px;padding:16px 20px;">
				<div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
					<?php 
        echo esc_html( $kpi['label'] );
        ?>
				</div>
				<div style="font-size:30px;font-weight:700;color:#1d2327;line-height:1;">
					<?php 
        echo esc_html( $kpi['value'] );
        ?>
				</div>
				<?php 
        if ( '' !== $kpi['sub'] ) {
            ?>
				<div style="font-size:13px;color:<?php 
            echo esc_attr( $kpi['color'] );
            ?>;margin-top:5px;font-weight:500;">
					<?php 
            echo esc_html( $kpi['sub'] );
            ?>
				</div>
				<?php 
        }
        ?>
			</div>
			<?php 
    }
    ?>
		</div>

		<!-- Top Searches ──────────────────────────────────────────────────── -->
		<h2 style="font-size:15px;font-weight:600;margin:0 0 8px;">
			<?php 
    esc_html_e( 'Top Searches', 'smart-woocommerce-search' );
    ?>
		</h2>

		<?php 
    if ( $sws_top_searches ) {
        ?>
		<div class="sws-table-scroll<?php 
        echo ( count( $sws_top_searches ) > 20 ? ' sws-table-scroll--tall' : '' );
        ?>">
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th><?php 
        esc_html_e( 'Phrase', 'smart-woocommerce-search' );
        ?></th>
					<th style="width:110px;text-align:center;"><?php 
        esc_html_e( 'Times Searched', 'smart-woocommerce-search' );
        ?></th>
					<th style="width:130px;text-align:center;"><?php 
        esc_html_e( 'Avg. Found Results', 'smart-woocommerce-search' );
        ?></th>
					<th style="width:175px;"><?php 
        esc_html_e( 'Last Searched', 'smart-woocommerce-search' );
        ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
        foreach ( $sws_top_searches as $row ) {
            ?>
				<tr>
					<td><strong><?php 
            echo esc_html( $row->phrase );
            ?></strong></td>
					<td style="text-align:center;"><?php 
            echo esc_html( number_format_i18n( (int) $row->searches ) );
            ?></td>
					<td style="text-align:center;"><?php 
            echo esc_html( number_format_i18n( (int) round( $row->avg_results ) ) );
            ?></td>
					<td><?php 
            echo esc_html( get_date_from_gmt( $row->last_seen, $sws_datetime_format ) );
            ?></td>
				</tr>
				<?php 
        }
        ?>
			</tbody>
		</table>
		</div>
		<?php 
    } else {
        ?>
			<p class="description" style="margin-bottom:28px;"><?php 
        esc_html_e( 'No successful searches in this period.', 'smart-woocommerce-search' );
        ?></p>
		<?php 
    }
    ?>

		<!-- No Results ────────────────────────────────────────────────────── -->
		<h2 style="font-size:15px;font-weight:600;margin:0 0 8px;">
			<span style="color:#d63638;" aria-hidden="true">&#9888; </span>
			<?php 
    esc_html_e( 'Searches With No Results', 'smart-woocommerce-search' );
    ?>
		</h2>

		<?php 
    if ( $sws_no_result_searches ) {
        ?>
		<div class="sws-table-scroll<?php 
        echo ( count( $sws_no_result_searches ) > 20 ? ' sws-table-scroll--tall' : '' );
        ?>">
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th><?php 
        esc_html_e( 'Phrase', 'smart-woocommerce-search' );
        ?></th>
					<th style="width:130px;text-align:center;"><?php 
        esc_html_e( 'Times Searched', 'smart-woocommerce-search' );
        ?></th>
					<th style="width:175px;"><?php 
        esc_html_e( 'Last Searched', 'smart-woocommerce-search' );
        ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
        foreach ( $sws_no_result_searches as $row ) {
            $sws_count = (int) $row->searches;
            ?>
				<tr>
					<td>
						<strong><?php 
            echo esc_html( $row->phrase );
            ?></strong>
						<?php 
            if ( $sws_count >= 10 ) {
                ?>
							<span style="display:inline-block;background:#d63638;color:#fff;font-size:10px;font-weight:600;padding:1px 7px;border-radius:10px;margin-left:6px;vertical-align:middle;line-height:16px;">
								<?php 
                esc_html_e( 'Critical', 'smart-woocommerce-search' );
                ?>
							</span>
						<?php 
            } elseif ( $sws_count >= 3 ) {
                ?>
							<span style="display:inline-block;background:#dba617;color:#fff;font-size:10px;font-weight:600;padding:1px 7px;border-radius:10px;margin-left:6px;vertical-align:middle;line-height:16px;">
								<?php 
                esc_html_e( 'Repeated', 'smart-woocommerce-search' );
                ?>
							</span>
						<?php 
            }
            ?>
					</td>
					<td style="text-align:center;"><?php 
            echo esc_html( number_format_i18n( $sws_count ) );
            ?></td>
					<td><?php 
            echo esc_html( get_date_from_gmt( $row->last_seen, $sws_datetime_format ) );
            ?></td>
				</tr>
				<?php 
        }
        ?>
			</tbody>
		</table>
		</div>
		<?php 
    } else {
        ?>
			<p class="description" style="color:#00a32a;">
				&#10003; <?php 
        esc_html_e( 'No searches without results in this period.', 'smart-woocommerce-search' );
        ?>
			</p>
		<?php 
    }
    ?>

			<!-- Top Clicked Products ──────────────────────────────────────── -->
			<h2 style="font-size:15px;font-weight:600;margin:28px 0 8px;">
				<span class="dashicons dashicons-cart" style="font-size:16px;width:16px;height:16px;margin-right:4px;vertical-align:text-bottom;" aria-hidden="true"></span>
				<?php 
    esc_html_e( 'Top Clicked Products', 'smart-woocommerce-search' );
    ?>
			</h2>

			<?php 
    if ( !$sws_event_table_exists ) {
        ?>

				<p class="description" style="margin-bottom:28px;color:#999;">
					<?php 
        esc_html_e( 'Click tracking data will appear here once visitors interact with search results.', 'smart-woocommerce-search' );
        ?>
				</p>

			<?php 
    } elseif ( $sws_top_products ) {
        ?>

			<div class="sws-table-scroll<?php 
        echo ( count( $sws_top_products ) > 20 ? ' sws-table-scroll--tall' : '' );
        ?>">
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th><?php 
        esc_html_e( 'Product', 'smart-woocommerce-search' );
        ?></th>
						<th style="width:120px;text-align:center;"><?php 
        esc_html_e( 'Link Clicks', 'smart-woocommerce-search' );
        ?></th>
						<th style="width:120px;text-align:center;"><?php 
        esc_html_e( 'Cart Clicks', 'smart-woocommerce-search' );
        ?></th>
						<th style="width:175px;"><?php 
        esc_html_e( 'Last Click', 'smart-woocommerce-search' );
        ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
        foreach ( $sws_top_products as $sws_row ) {
            $sws_pid = (int) $sws_row->product_id;
            $sws_product = wc_get_product( $sws_pid );
            $sws_pname = ( $sws_product ? $sws_product->get_name() : sprintf( __( 'Product #%d', 'smart-woocommerce-search' ), $sws_pid ) );
            $sws_edit_url = ( $sws_product ? get_edit_post_link( $sws_pid ) : '' );
            ?>
					<tr>
						<td>
							<?php 
            if ( $sws_edit_url ) {
                ?>
								<a href="<?php 
                echo esc_url( $sws_edit_url );
                ?>" style="font-weight:600;">
									<?php 
                echo esc_html( $sws_pname );
                ?>
								</a>
							<?php 
            } else {
                ?>
								<strong><?php 
                echo esc_html( $sws_pname );
                ?></strong>
							<?php 
            }
            ?>
						</td>
						<td style="text-align:center;"><?php 
            echo esc_html( number_format_i18n( (int) $sws_row->link_clicks ) );
            ?></td>
						<td style="text-align:center;"><?php 
            echo esc_html( number_format_i18n( (int) $sws_row->cart_clicks ) );
            ?></td>
						<td><?php 
            echo esc_html( get_date_from_gmt( $sws_row->last_click, $sws_datetime_format ) );
            ?></td>
					</tr>
					<?php 
        }
        ?>
				</tbody>
			</table>
			</div>

			<?php 
    } else {
        ?>
				<p class="description" style="margin-bottom:28px;">
					<?php 
        esc_html_e( 'No product clicks from search in this period.', 'smart-woocommerce-search' );
        ?>
				</p>
			<?php 
    }
    ?>

	</div><!-- #sws-analytics-stats -->

	<?php 
}
// stats tab
?>


	<?php 
/* ══════════════════════════════════════════════════════════════════
     TAB: Google Analytics 4
   ══════════════════════════════════════════════════════════════════ */
if ( 'ga4' === $sws_active_tab ) {
    ?>

	<div id="sws-analytics-ga4">
		<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display:block;">
			<div class="ysm-inside">

				<h4 class="ymapp-settings__title"><?php 
    esc_html_e( 'Google Analytics 4', 'smart-woocommerce-search' );
    ?></h4>

				<?php 
    $sws_ga4_enabled = (bool) get_option( 'sws_enable_google_analytics' );
    ?>

				<div class="sws-ga-event-row">
					<div style="text-align:left;margin:60px 0 8px;display:flex;">
						<input type="checkbox"
							   id="sws-ga4-enable"
							   class="ymapp-switcher sws-ga4-toggle"
							   <?php 
    checked( $sws_ga4_enabled );
    ?>
						/>
						<label for="sws-ga4-enable"></label>
						<span class="sws-ga-event-label">
							<?php 
    esc_html_e( 'Enable Google Analytics 4 tracking.', 'smart-woocommerce-search' );
    ?>
							<?php 
    esc_html_e( 'Event:', 'smart-woocommerce-search' );
    ?>
							<code>swsEvent</code>
						</span>
						<span class="sws-ga4-spinner spinner"></span>
					</div>
				</div>

				<div class="sws-ga-event-details" style="<?php 
    echo ( $sws_ga4_enabled ? '' : 'display:none;' );
    ?>">

					<h4 style="margin-bottom:8px;"><?php 
    esc_html_e( 'Event Categories', 'smart-woocommerce-search' );
    ?></h4>
					<p class="description">
						<?php 
    esc_html_e( 'Toggle which events are sent to Google Analytics.', 'smart-woocommerce-search' );
    ?><br>
						<code><?php 
    esc_html_e( 'Note: to accurately track click events, clicked links will open in a new window.', 'smart-woocommerce-search' );
    ?></code>
					</p>

					<?php 
    $sws_ga_enabled_events = (array) get_option( 'sws_ga_enabled_events', [] );
    $sws_ga_event_labels = [
        'search_term_has_results'    => [
            'label'       => __( '[Search term] has results', 'smart-woocommerce-search' ),
            'description' => __( 'When user saw search results', 'smart-woocommerce-search' ),
            'premium'     => false,
        ],
        'search_term_no_results'     => [
            'label'       => __( '[Search term] no results', 'smart-woocommerce-search' ),
            'description' => __( 'When user saw a "no results" message', 'smart-woocommerce-search' ),
            'premium'     => false,
        ],
        'search_results_link_click'  => [
            'label'       => __( '[Found Suggestions] link click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked on a suggestion', 'smart-woocommerce-search' ),
            'premium'     => false,
        ],
        'search_results_cart_click'  => [
            'label'       => __( '[Found Suggestions] add-to-cart click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked a suggestion\'s Add to Cart button', 'smart-woocommerce-search' ),
            'premium'     => true,
        ],
        'view_all_click'             => [
            'label'       => __( '[View All] button click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked the View All button', 'smart-woocommerce-search' ),
            'premium'     => false,
        ],
        'promo_banner_click'         => [
            'label'       => __( '[Promo banner] link click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked a banner in the fullscreen popup', 'smart-woocommerce-search' ),
            'premium'     => true,
        ],
        'selected_categories_click'  => [
            'label'       => __( '[Selected Categories] link click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked a category in the fullscreen popup', 'smart-woocommerce-search' ),
            'premium'     => false,
        ],
        'recommended_products_click' => [
            'label'       => __( '[Recommended Products] link click', 'smart-woocommerce-search' ),
            'description' => __( 'When user clicked a recommended product in the fullscreen popup', 'smart-woocommerce-search' ),
            'premium'     => true,
        ],
    ];
    ?>

					<div class="sws-ga-events-wrap">
					<?php 
    foreach ( $sws_ga_event_labels as $sws_event_key => $sws_event ) {
        if ( $sws_event['premium'] && !sws_fs()->is__premium_only() ) {
            continue;
        }
        $sws_event_enabled = in_array( $sws_event_key, $sws_ga_enabled_events, true );
        ?>
						<div class="sws-ga-event-row">
							<input type="checkbox"
							       id="sws-ga-event-<?php 
        echo esc_attr( $sws_event_key );
        ?>"
							       class="ymapp-switcher sws-ga-event-toggle"
							       data-event="<?php 
        echo esc_attr( $sws_event_key );
        ?>"
							       <?php 
        checked( $sws_event_enabled );
        ?>
							/>
							<label for="sws-ga-event-<?php 
        echo esc_attr( $sws_event_key );
        ?>"></label>
							<span class="sws-ga-event-label">
								<code><?php 
        echo esc_html( $sws_event['label'] );
        ?></code>
								<span class="description">&mdash; <?php 
        echo esc_html( $sws_event['description'] );
        ?></span>
							</span>
							<span class="sws-ga-event-spinner spinner"></span>
						</div>
					<?php 
    }
    ?>
					</div>

					<?php 
    wp_nonce_field( 'ysm_widgets_nonce_action', 'ysm_widgets_nonce' );
    ?>

				</div><!-- .sws-ga-event-details -->

				<?php 
    wp_nonce_field( 'ysm_analytics_nonce_action', 'ysm_analytics_nonce' );
    ?>

			</div>
		</div>

		<a class="ymapp-settings__doc_link" href="https://www.wpsmartsearch.com/docs/analytics/" target="_blank">
			<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
			<?php 
    esc_html_e( 'Documentation', 'smart-woocommerce-search' );
    ?>
		</a>
	</div><!-- #sws-analytics-ga4 -->

	<?php 
}
// ga4 tab
?>

</div><!-- .wrap -->
