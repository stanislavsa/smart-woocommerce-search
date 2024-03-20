<div id="spellcheck_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'synonyms', array(
			'type'        => 'repeater',
			'title'       => __( 'Synonyms', 'smart-woocommerce-search' ),
			'description' =>
				__( 'Add synonyms for words that you think could be misspelled.<br> Example Orange => Ornge', 'smart-woocommerce-search' )
				. '<br>'
				. __( 'Applies only to the current widget.', 'smart-woocommerce-search' ),
			'fields' => [
				'correct' => __( 'Correct', 'smart-woocommerce-search' ),
				'error'   => __( 'Wrong', 'smart-woocommerce-search' ),
			],
			'is_pro'      => true,
		));
		?>

		</tbody>
	</table>
</div>