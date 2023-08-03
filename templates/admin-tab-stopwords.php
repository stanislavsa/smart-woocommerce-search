<div id="stopwords_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'stop_words', array(
			'type'        => 'pro',
			'title'       => __( 'Stop Words', 'smart-woocommerce-search' ),
			'description' =>
				__( 'Add a list of words that should be skipped from the search request.', 'smart-woocommerce-search' )
				. '<br>'
				. __( 'Applies only to the current widget.', 'smart-woocommerce-search' ),
		));
		?>

		</tbody>
	</table>
</div>