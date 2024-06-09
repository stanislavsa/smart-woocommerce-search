<div id="spellcheck_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php
		ysm_setting( $w_id, 'synonyms', array(
			'type'        => 'repeater',
			'title'       => __( 'Synonyms', 'smart-woocommerce-search' ),
			'description' =>
				__( 'Add synonyms for words that you think could be misspelled', 'smart-woocommerce-search' )
				. '<br>'
				. __( 'Example', 'smart-woocommerce-search' ) . ' Orange => Ornge'
				. '<br>'
				. __( 'Applies only to the current widget', 'smart-woocommerce-search' ),
			'fields' => [
				'correct' => __( 'Correct', 'smart-woocommerce-search' ),
				'error'   => __( 'Wrong', 'smart-woocommerce-search' ),
			],
			'is_pro'      => true,
		));
		?>

		</tbody>
	</table>

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/synonyms/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>