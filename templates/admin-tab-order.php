<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="order_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<!--		<th class="ymapp-settings__title">--><?php //esc_html_e( 'Order By', 'smart-woocommerce-search' ); ?><!--</th>-->

		<?php

		ysm_setting( $w_id, 'order_by', array(
			'type'        => 'select',
			'title'       => __( 'Sorting', 'smart-woocommerce-search' ),
			'description' => __( 'Sort retrieved results by parameter', 'smart-woocommerce-search' ),
			'choices'     => array(
				'relevance'     => 'Relevance',
				'post_title'    => __( 'Title', 'smart-woocommerce-search' ),
				'post_date'     => __( 'Publish Date', 'smart-woocommerce-search' ),
				'post_modified' => __( 'Modified Date', 'smart-woocommerce-search' ),
			),
		));

		ysm_setting( $w_id, 'order', array(
			'type'        => 'select',
			'title'       => 'ASC/DESC',
			'description' => __( 'Ascending means smallest to largest, 0 to 9, and/or A to Z and Descending means largest to smallest, 9 to 0, and/or Z to A', 'smart-woocommerce-search' ),
			'choices'     => array(
				'ASC'  => __( 'ASC', 'smart-woocommerce-search' ),
				'DESC' => __( 'DESC', 'smart-woocommerce-search' ),
			),
			'value'       => 'DESC',
		));

		?>

		</tbody>
	</table>
</div>