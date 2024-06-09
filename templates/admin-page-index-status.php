<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tables_exists = false;
$status = '';
$status_name = __( 'Empty', 'smart-woocommerce-search' );
$styles = 'background-color:gray;color:#fff;';
$post_types = ysm_get_post_types();
$post_types_count = [];

if ( $post_types ) {
	foreach ( $post_types as $pt ) {
		$post_types_count[ $pt ] = 0;
	}
}
?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

	<form method="post" action="" enctype="multipart/form-data">

		<div class="ysm-widget-edit-settings ymapp-settings__content" style="display: block">

			<table class="form-table">
				<tbody>

				<tr>
					<th class="ymapp-settings__title"><?php esc_html_e( 'Index Status', 'smart-woocommerce-search' ); ?>
						<code style="<?php echo esc_attr( $styles ); ?>"><?php echo esc_html( $status_name ); ?></code>
					</th>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Tables are created', 'smart-woocommerce-search' ); ?></th>
					<td><?php echo $tables_exists ? esc_html__( 'yes', 'smart-woocommerce-search' ) : esc_html__( 'no', 'smart-woocommerce-search' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Indexed Post Types', 'smart-woocommerce-search' ); ?></th>
					<td>
						<?php if ( $post_types ) { ?>
							<ul>
								<?php foreach ( $post_types_count as $pt => $count ) { ?>
									<li><?php echo esc_html( $pt ); ?> - <?php echo esc_html( $count ); ?></li>
								<?php } ?>
							</ul>
						<?php } ?>
					</td>
				</tr>

				</tbody>
			</table>

			<br><br><br><br>

			<?php if ( sws_fs()->is_premium() ) { ?>
				<?php wp_nonce_field(
					'sws_index_button_nonce_action',
					'sws_index_button_nonce'
				); ?>
				<div class="sws-index-now-button-holder">
					<a href="#" id="sws-index-now-button" class="ymapp-button-small"><?php esc_html_e( 'Create Index', 'smart-woocommerce-search' ); ?></a>
					<span class="sws-index-now-button-loader" style="display: none">
						<img style="margin-left:20px;margin-top: 10px;" class="ysm-loader-preview" src="<?php echo esc_url( SWS_PLUGIN_URI . 'assets/images/loader5.gif' ); ?>">
					</span>
					<span class="sws-index-now-button-message" style="
    margin-top: 30px;
    margin-left: 30px;
    font-size: 14px;
    margin-bottom: 15px;
"></span>
				</div>
				<div class="sws-page-description"><?php esc_html_e( 'Delete all indexed data and create a fresh index', 'smart-woocommerce-search' ); ?></div>
				<br><br>
				<a href="#" id="sws-index-now-delete" class="ymapp-button-small disabled"><?php esc_html_e( 'Delete Index', 'smart-woocommerce-search' ); ?></a>
				<div class="sws-page-description"><?php esc_html_e( 'Delete all indexed data', 'smart-woocommerce-search' ); ?></div>

			<?php } else { ?>
				<button disabled class="button"><?php esc_html_e( 'Create Index', 'smart-woocommerce-search' ); ?></button>
			<?php } ?>

		</div>

	</form>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" style="margin-top: 30px;" href="https://www.wpsmartsearch.com/docs/data-indexing/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>
