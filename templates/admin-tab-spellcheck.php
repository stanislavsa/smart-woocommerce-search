<div id="spellcheck_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'synonyms', array(
			'type'        => 'pro',
			'title'       => __( 'Synonyms', 'smart-woocommerce-search' ),
			'description' =>
				__( 'Add synonyms for words you think could be misspelled.<br> Example Orange => Ornge', 'smart-woocommerce-search' )
				. '<br>'
				. __( 'Applies only to the current widget.', 'smart-woocommerce-search' ),
		));
		?>

		</tbody>
	</table>
</div>