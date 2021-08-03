<div id="stopwords_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'stop_words', array(
			'type'  => 'pro',
			'title' => __( 'Stop Words', 'smart_search' ),
			'description' => __( 'Add a list of words that should be skipped from the search request.', 'smart_search' ),
		));
		?>

		</tbody>
	</table>
</div>