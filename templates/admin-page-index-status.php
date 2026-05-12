<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tables_exists    = false;
$status           = '';
$is_running       = false;
$status_name      = __( 'Empty', 'smart-woocommerce-search' );
$status_mod       = 'empty';
$post_types       = ysm_get_post_types();
$post_types_count = [];

$tables_exists = \YSWS\Core\DB_Index\tables_exists();
$status        = \YSWS\Core\DB_Index\get_index_status();

if ( $post_types ) {
	foreach ( $post_types as $pt ) {
		$post_types_count[ $pt ] = \YSWS\Core\DB_Index\count_indexed_posts( $pt );
	}
}

if ( 'failed' === $status ) {
	$status_name = __( 'Failed', 'smart-woocommerce-search' );
	$status_mod  = 'failed';
} elseif ( 'doing' === $status ) {
	$status_name = __( 'In progress', 'smart-woocommerce-search' );
	$status_mod  = 'doing';
	$is_running  = true;
} elseif ( $tables_exists && 'ready' === $status ) {
	$status_name = __( 'Ready', 'smart-woocommerce-search' );
	$status_mod  = 'ready';
}

// Button label reflects current state on page load.
$create_btn_label = $is_running
	? __( 'Indexing…', 'smart-woocommerce-search' )
	: __( 'Create Index', 'smart-woocommerce-search' );
?>
<style>
/* Status badge — replaces inline style="background-color:X" on <code> */
.sws-index-status-badge {
	display: inline-block;
	padding: 2px 10px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 600;
	color: #fff;
	background: #999;
	vertical-align: middle;
	margin-left: 8px;
	font-family: inherit;
}
.sws-index-status-badge--ready  { background: #46b450; }
.sws-index-status-badge--doing  { background: #2271b1; }
.sws-index-status-badge--failed { background: #dc3232; }

/* Button area — replaces <br> spacers */
.sws-index-actions {
	margin-top: 20px;
}
.sws-index-actions .sws-action-group {
	margin-bottom: 16px;
}
.sws-index-actions .sws-page-description {
	margin: 4px 0 0;
	color: #646970;
	font-size: 12px;
}

/* Loader + message sit inline with the button */
.sws-index-now-button-holder {
	display: flex;
	align-items: center;
	gap: 12px;
	flex-wrap: wrap;
}
.sws-index-now-button-loader {
	display: none;
}
.sws-index-now-button-loader img {
	display: block;
}
.sws-index-now-button-message {
	font-size: 13px;
	color: #646970;
}

/* "Saved" confirmation — hidden by default, shown via JS opacity transition */
.field-updated {
	opacity: 0;
	color: #46b450;
	font-size: 13px;
	margin-top: 8px;
	transition: opacity 0.3s ease;
}

/* Inline notice for missing tables */
.sws-tables-missing-notice {
	margin: 6px 0 0 !important;
}

/* Doc link — styling handled globally in _buttons.scss */

#sws-index-now-button,
#sws-index-now-delete {
	margin-left: 0;
}

/* Settings panel — always visible on this page (overrides any tab-hide logic) */
.ysm-widget-edit-settings.ymapp-settings__content {
	display: block;
}
</style>

<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

	<form method="post" action="" enctype="multipart/form-data">

		<?php wp_nonce_field( 'sws_index_button_nonce_action', 'sws_index_button_nonce' ); ?>

		<div class="ysm-widget-edit-settings ymapp-settings__content">

			<table class="form-table">
				<tbody>

				<tr>
					<th class="ymapp-settings__title">
						<?php esc_html_e( 'Index Status', 'smart-woocommerce-search' ); ?>
						<span class="sws-index-status-badge sws-index-status-badge--<?php echo esc_attr( $status_mod ); ?>">
							<?php echo esc_html( $status_name ); ?>
						</span>
					</th>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Tables are created', 'smart-woocommerce-search' ); ?></th>
					<td>
						<?php if ( $tables_exists ) : ?>
							<?php esc_html_e( 'Yes', 'smart-woocommerce-search' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'No', 'smart-woocommerce-search' ); ?>
							<div class="notice notice-warning inline sws-tables-missing-notice">
								<p><?php esc_html_e( 'Index tables are missing. Please deactivate and reactivate the plugin to recreate them.', 'smart-woocommerce-search' ); ?></p>
							</div>
						<?php endif; ?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Indexed Post Types', 'smart-woocommerce-search' ); ?></th>
					<td>
						<?php if ( $post_types ) : ?>
							<ul>
								<?php foreach ( $post_types_count as $pt => $count ) : ?>
									<li><?php echo esc_html( $pt ); ?> &mdash; <?php echo esc_html( $count ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</td>
				</tr>

				<?php
				ysm_setting( 0, 'sws_index_auto_update_on_save', array(
					'type'        => 'checkbox',
					'title'       => __( 'Re-create index when settings updated', 'smart-woocommerce-search' ),
					'description' => __( 'Re-create the data index automatically when the search widget settings were updated', 'smart-woocommerce-search' ),
					'value'       => (bool) get_option( 'sws_index_update_on_widget_save' ),
				) );
				?>

				</tbody>
			</table>

			<div class="field-updated"><?php esc_html_e( 'Saved', 'smart-woocommerce-search' ); ?></div>

			<div class="sws-index-actions">

				<div class="sws-action-group">
					<div class="sws-index-now-button-holder">
						<a href="#" id="sws-index-now-button" class="ymapp-button-small<?php echo $is_running ? ' in-background disabled' : ''; ?>">
							<?php echo esc_html( $create_btn_label ); ?>
						</a>
						<span class="sws-index-now-button-loader">
							<img class="ysm-loader-preview" src="<?php echo esc_url( SWS_PLUGIN_URI . 'assets/images/loader5.gif' ); ?>" alt="" width="20" height="20">
						</span>
						<span class="sws-index-now-button-message"></span>
					</div>
					<p class="sws-page-description"><?php esc_html_e( 'Delete all indexed data and create a fresh index.', 'smart-woocommerce-search' ); ?></p>
				</div>

				<div class="sws-action-group">
						<a href="#" id="sws-index-now-delete" class="ymapp-button-small<?php echo $is_running ? ' disabled' : ''; ?>">
							<?php esc_html_e( 'Delete Index', 'smart-woocommerce-search' ); ?>
						</a>
						<p class="sws-page-description"><?php esc_html_e( 'Delete all indexed data.', 'smart-woocommerce-search' ); ?></p>
					</div>

			</div>

		</div>

	</form>

	<a class="ymapp-settings__doc_link" href="https://www.wpsmartsearch.com/docs/data-indexing/" target="_blank">
		<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
		<?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?>
	</a>
</div>
<script>
	(function($) {
		var notIndexed    = 0;
		var $createBtn    = $('#sws-index-now-button');
		var $deleteBtn    = $('#sws-index-now-delete');
		var labelCreate    = <?php echo wp_json_encode( __( 'Create Index',    'smart-woocommerce-search' ) ); ?>;
		var labelIndexing  = <?php echo wp_json_encode( __( 'Indexing…',       'smart-woocommerce-search' ) ); ?>;
		/* translators: 1: posts processed so far, 2: total posts to process */
		var labelProcessed = <?php echo wp_json_encode( __( 'Processed %1$d/%2$d', 'smart-woocommerce-search' ) ); ?>;

		// #5 — centralised state: updates both buttons + loader + button label together.
		function setRunningState( running ) {
			if ( running ) {
				$createBtn.addClass('disabled').text( labelIndexing );
				$deleteBtn.addClass('disabled');
				$('.sws-index-now-button-loader').show();
			} else {
				$createBtn.removeClass('disabled in-background').text( labelCreate );
				$deleteBtn.removeClass('disabled');
				$('.sws-index-now-button-loader').hide();
			}
		}

		function showMessage( text, isError ) {
			$('.sws-index-now-button-message')
				.css('color', isError ? '#dc3232' : '#646970')
				.text( text );
		}

		var checkStatus = function() {
			$.post(
				ajaxurl,
				{
					action: 'sws_index_button_click_check',
					nonce: $('#sws_index_button_nonce').val()
				},
				function( res ) {
					if ( ! res || ! res['status'] ) {
						return;
					}

					if ( notIndexed && res['indexed'] ) {
						showMessage( labelProcessed.replace( '%1$d', Math.min( res['indexed'], notIndexed ) ).replace( '%2$d', notIndexed ) );
					}

					if ( res['status'] === 'ready' ) {
						location.reload();
					} else if ( res['status'] === 'failed' ) {
						setRunningState( false );
						showMessage( <?php echo wp_json_encode( __( 'Indexing failed. Please try again.', 'smart-woocommerce-search' ) ); ?>, true );
					} else {
						setTimeout( checkStatus, 1000 );
					}
				},
				'json'
			).fail(function() {
				setRunningState( false );
				showMessage( <?php echo wp_json_encode( __( 'Connection error while checking index status. Please reload the page.', 'smart-woocommerce-search' ) ); ?>, true );
			});
		};

		// Resume polling if indexing was already running when the page loaded.
		if ( $createBtn.hasClass('in-background') ) {
			$('.sws-index-now-button-loader').show();
			showMessage( <?php echo wp_json_encode( __( 'Running in background…', 'smart-woocommerce-search' ) ); ?> );
			checkStatus();
		}

		$createBtn.on('click', function(e) {
			e.preventDefault();

			if ( $(this).hasClass('disabled') ) {
				return;
			}

			if ( $(this).hasClass('in-background') ) {
				if ( ! confirm( <?php echo wp_json_encode( __( 'Index is already running in the background. Do you want to stop it and start new indexing?', 'smart-woocommerce-search' ) ); ?> ) ) {
					return;
				}
			}

			notIndexed = 0;
			setRunningState( true );
			showMessage( <?php echo wp_json_encode( __( 'Running in background…', 'smart-woocommerce-search' ) ); ?> );

			$.post(
				ajaxurl,
				{
					action: 'sws_index_button_click',
					nonce: $('#sws_index_button_nonce').val()
				},
				function( res ) {
					if ( res && res.not_indexed ) {
						notIndexed = res.not_indexed;
						showMessage( labelProcessed.replace( '%1$d', 0 ).replace( '%2$d', notIndexed ) );
					}
					checkStatus();
				},
				'json'
			).fail(function() {
				setRunningState( false );
				showMessage( <?php echo wp_json_encode( __( 'Failed to start indexing. Please reload the page and try again.', 'smart-woocommerce-search' ) ); ?>, true );
			});
		});

		$deleteBtn.on('click', function(e) {
			e.preventDefault();

			if ( $(this).hasClass('disabled') ) {
				return;
			}

			if ( ! confirm( <?php echo wp_json_encode( __( 'Are you sure you want to delete all indexed data? This cannot be undone.', 'smart-woocommerce-search' ) ); ?> ) ) {
				return;
			}

			$(this).addClass('disabled');

			$.post(
				ajaxurl,
				{
					action: 'sws_index_button_delete',
					nonce: $('#sws_index_button_nonce').val()
				},
				function() {
					location.reload();
				}
			).fail(function() {
				$deleteBtn.removeClass('disabled');
				alert( <?php echo wp_json_encode( __( 'Failed to delete index. Please reload the page and try again.', 'smart-woocommerce-search' ) ); ?> );
			});
		});

		$('#sws_index_auto_update_on_save').on('change', function(e) {
			var $saved = $('.field-updated');
			$saved.css('opacity', 1);
			$.post(
				ajaxurl,
				{
					action: 'sws_index_auto_update_on_save',
					value: e.target.checked ? 1 : 0,
					nonce: $('#sws_index_button_nonce').val()
				},
				function( response ) {
					if ( response ) {
						setTimeout(function() {
							$saved.css('opacity', 0);
						}, 2000);
					}
				}
			).fail(function() {
				$saved.css('opacity', 0);
			});
		});
	})(jQuery);
</script>
