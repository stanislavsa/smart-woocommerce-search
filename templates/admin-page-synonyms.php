<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$correct = [];
$wrong = [];
$sws_enable_synonyms_indexing = false;
$sws_synonyms_enhance_query = false;
?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>

	<?php \YummyWP\App\Notification::display(); ?>

		<div class="ysm-postbox ysm-widget-edit-settings ymapp-settings__content" style="display: block">

			<div class="ysm-inside ysm-synonyms-holder">

                    <fieldset class="ysm-synonyms-holder__option">
                        <input disabled class="ymapp-switcher" type="checkbox" <?php echo $sws_enable_synonyms_indexing ? 'checked' : '' ?> id="sws_enable_synonyms_indexing" name="sws_enable_synonyms_indexing">
                        <label for="sws_enable_synonyms_indexing">
                            <?php esc_html_e( 'Enable Automatic Synonyms Indexing', 'smart-woocommerce-search' ); ?>
                        </label>
						<p><?php esc_html_e( 'Automatically analyze product/post titles and tags to generate and store synonyms in a dedicated database index', 'smart-woocommerce-search' ) ?></p>
						<br>
						<input disabled class="ymapp-switcher" type="checkbox" <?php echo $sws_synonyms_enhance_query ? 'checked' : '' ?> id="sws_synonyms_enhance_query" name="sws_synonyms_enhance_query">
						<label for="sws_synonyms_enhance_query">
		                    <?php esc_html_e( 'Use Synonyms for Fallback Search', 'smart-woocommerce-search' ); ?>
						</label>
						<p><?php esc_html_e( 'If no results are found for the original search query, the system will attempt to deliver results by searching for relevant synonyms from the indexed data', 'smart-woocommerce-search' ) ?></p>
						<br>
                    </fieldset>

				<?php
				\YummyWP\App\Field::output( 'synonyms', [
					'name'        => 'synonyms',
					'type'        => 'repeater',
					'title'       =>
                        '<h3>'.
                        __( 'Add Custom Synonyms & Spelling Corrections', 'smart-woocommerce-search' ).
                        '</h3>',
					'description' => __( 'Manually define synonyms or correct common spelling errors.', 'smart-woocommerce-search' )
					                 . '<br>'
									 . __( 'Matching words in the search query will be replaced with the custom synonyms to enhance search accuracy.', 'smart-woocommerce-search' )
									. '<br>'
									. __( 'Example', 'smart-woocommerce-search' ) . ': <strong>Orange => oronge</strong>',
					'fields' => [
						'correct' => __( 'Correct', 'smart-woocommerce-search' ),
						'wrong'   => __( 'Wrong', 'smart-woocommerce-search' ),
					],
					'value'       => [
						'correct' => $correct,
						'wrong'   => $wrong,
					],
					'is_pro'      => true,
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