<div id="stopwords_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'stop_words', array(
			'type'        => 'repeater',
			'title'       => __( 'Stop Words', 'smart-woocommerce-search' ),
			'description' =>
				__( 'Add a list of words that should be skipped from the search request.', 'smart-woocommerce-search' )
				. '<br>'
				. __( 'Applies only to the current widget', 'smart-woocommerce-search' ),
			'fields'      => [
				'word' => '',
			],
			'is_pro'      => true,
		));
		?>

		</tbody>
	</table>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/stop-words/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>