<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
	<h1><span><?php 
echo esc_html( get_admin_page_title() );
?></span></h1>

	<?php 
\YummyWP\App\Notification::display();
?>

	<form method="post" action="" enctype="multipart/form-data">

		<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display: block">
			<div class="clear"></div>

			<div class="ysm-inside">

				<table class="form-table">
					<tbody>
					<tr>
						<th class="ymapp-settings__title"><?php 
esc_html_e( 'Google Analytics 4', 'smart-woocommerce-search' );
?>
						</th>
					</tr>

				<?php 
\YummyWP\App\Field::output( 'sws_enable_google_analytics', [
    'name'        => 'sws_enable_google_analytics',
    'type'        => 'checkbox',
    'title'       => __( 'Enable Google Analytics 4 tracking', 'smart-woocommerce-search' ),
    'description' => '',
    'value'       => get_option( 'sws_enable_google_analytics' ),
] );
?>
					</tbody>
				</table>

				<div style="text-align: left;">
					<h4><?php 
_e( 'Analytics Event:', 'smart-woocommerce-search' );
?></h4>
					<code>swsEvent</code>
					<h4><?php 
_e( 'Event category values:', 'smart-woocommerce-search' );
?></h4>
					<div>
						<code>[Search term] has results</code> - <?php 
_e( 'When user saw search results', 'smart-woocommerce-search' );
?> <br>
						<code>[Search term] no results</code> - <?php 
_e( 'When user saw "no results" message', 'smart-woocommerce-search' );
?> <br>
						<code>[Found Suggestions] link click</code> - <?php 
_e( 'When user clicked on a suggestion', 'smart-woocommerce-search' );
?> <br>
						<?php 
?>
						<code>[View All] button click</code> - <?php 
_e( 'When user clicked on a "View All" button', 'smart-woocommerce-search' );
?> <br>
						<?php 
?>
					</div>
				</div>
			</div>

			<?php 
wp_nonce_field( 'ysm_analytics_nonce_action', 'ysm_analytics_nonce' );
?>
			<input type="submit" value="<?php 
esc_attr_e( 'Save', 'smart-woocommerce-search' );
?>" name="save" class="ymapp-button" style="float: right;" />
			<div class="clear"></div>
		</div>

	</form>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" style="margin-top: 30px;" href="https://www.wpsmartsearch.com/docs/analytics/" target="_blank"><?php 
esc_html_e( 'Documentation', 'smart-woocommerce-search' );
?></a>
</div>