<div id="spellcheck_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'synonyms', array(
			'type'        => 'pro',
			'title'       => __( 'Synonyms', 'smart-woocommerce-search' ),
			'description' => __( 'Add synonyms for the words that, for your opinion, can be entered with spell errors. Example Orange => Ornge', 'smart-woocommerce-search' ),
		));
		?>

		</tbody>
	</table>
</div>