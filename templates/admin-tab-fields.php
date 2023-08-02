<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="fields_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php if ( $w_id !== 'product' ) { ?>

			<th class="ymapp-settings__title"><?php esc_html_e( 'Post Types', 'smart-woocommerce-search' ); ?></th>

			<?php
			$post_types = get_post_types( array( 'public' => true ), 'object' );

			$search_in = array(
				'post' => 0,
				'page' => 0,
				'product' => 1,
			);

			foreach ( $post_types as $id => $post_type ) {

				if ( isset( $search_in[ $id ] ) ) {
					ysm_setting( $w_id, 'post_type_' . $id, array(
						'type'        => 'checkbox',
						/* translators: %s: Name of a field to */
						'title'       => sprintf( __( '%s', 'smart-woocommerce-search' ), $post_type->label ),
						/* translators: %s: Name of a Post Type */
						'description' => sprintf( __( 'Enable search through %s', 'smart-woocommerce-search' ), $post_type->label ),
						'value'       => $search_in[ $id ],
					));
				}
			}
			?>

		<?php } ?>

		<?php
		ysm_setting( $w_id, 'post_type_product_variation', array(
			'type'        => 'pro',
			'title'       => __( 'Product Variations', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search through Product Variations', 'smart-woocommerce-search' ),
			'value' => 0,
		));
		?>

		<?php
		ysm_setting( $w_id, 'custom_post_types', array(
			'type'        => 'pro',
			'title'       => __( 'Custom Post Types', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search through selected custom post types', 'smart-woocommerce-search' ),
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Fields', 'smart-woocommerce-search' ); ?></th>

		<?php

		ysm_setting( $w_id, 'field_title', array(
			'type'        => 'checkbox',
			'title'       => __( 'Title', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in post / product title', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_content', array(
			'type'        => 'checkbox',
			'title'       => __( 'Content', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in post / product content', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_excerpt', array(
			'type'        => 'checkbox',
			'title'       => __( 'Excerpt', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in post / product excerpt', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_product_sku', array(
			'type'        => 'checkbox',
			'title'       => __( 'Product SKU', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in product SKU', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'custom_fields', array(
			'type'        => 'pro',
			'title'       => __( 'Custom Fields', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in Custom Fields. Fill in field slugs separated by comma', 'smart-woocommerce-search' ),
		));

		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Taxonomies', 'smart-woocommerce-search' ); ?></th>

		<?php
		if ( 'product' !== $w_id ) {
			ysm_setting( $w_id, 'field_tag', array(
				'type'        => 'checkbox',
				'title'       => __( 'Post Tag', 'smart-woocommerce-search' ),
				'description' => __( 'Enable search in post tag', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'field_category', array(
				'type'        => 'checkbox',
				'title'       => __( 'Post Category', 'smart-woocommerce-search' ),
				'description' => __( 'Enable search in post category', 'smart-woocommerce-search' ),
				'value'       => '',
			));
		}

		ysm_setting( $w_id, 'field_product_tag', array(
			'type'        => 'checkbox',
			'title'       => __( 'Product Tag', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in product tag', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'field_product_cat', array(
			'type'        => 'checkbox',
			'title'       => __( 'Product Category', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in product category', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		/* Custom Tax */
		ysm_setting( $w_id, 'custom_tax', array(
			'type'        => 'pro',
			'title'       => __( 'Custom Taxonomies', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in selected custom taxonomies', 'smart-woocommerce-search' ),
		));

		/* Product categories */
		$product_cats = get_terms( array(
			'taxonomy'   => 'product_cat',
			'orderby'    => 'id',
			'order'      => 'asc',
			'hide_empty' => false,
		) );
		$product_cats_list = array();

		if ( ! is_wp_error( $product_cats ) && is_array( $product_cats ) ) {
			foreach ( $product_cats as $product_cat ) {
				$product_cats_list[ $product_cat->term_id ] = $product_cat->name;
			}
		}

		ysm_setting( $w_id, 'allowed_product_cat', array(
			'type'        => 'select',
			'title'       => __( 'Allowed Product Categories', 'smart-woocommerce-search' ),
			'description' => __( 'Limit customers\' search to selected product categories', 'smart-woocommerce-search' ),
			'multiple'    => true,
			'choices'     => $product_cats_list,
		));

		ysm_setting( $w_id, 'disallowed_product_cat', array(
			'type'        => 'select',
			'title'       => __( 'Disallowed Product Categories', 'smart-woocommerce-search' ),
			'description' => __( 'Disallow search in selected product categories', 'smart-woocommerce-search' ),
			'multiple'    => true,
			'choices'     => $product_cats_list,
		));

		?>

		</tbody>
	</table>
</div>