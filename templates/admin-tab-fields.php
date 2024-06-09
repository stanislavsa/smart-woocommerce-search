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
						'title'       => $post_type->label,
						/* translators: %s: Name of a Post Type */
						'description' => sprintf( __( 'Enable search in %s', 'smart-woocommerce-search' ), $post_type->label ),
						'value'       => $search_in[ $id ],
					));
				}
			}
			?>

		<?php } ?>

		<?php
		ysm_setting( $w_id, 'post_type_product_variation', array(
			'type'        => 'checkbox',
			'title'       => __( 'Product Variations', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search through Product Variations', 'smart-woocommerce-search' ),
			'value'       => 0,
			'is_pro'      => true,
		));
		?>

		<?php
		$post_types_exclude = array(
			// public
			'post' => 1,
			'page' => 1,
			'attachment' => 1,
			'product' => 1,
			// not public
			'revision' => 1,
			'nav_menu_item' => 1,
			'custom_css' => 1,
			'customize_changeset' => 1,
			'oembed_cache' => 1,
			'user_request' => 1,
			'acf' => 1,
			'product_variation' => 1,
			'shop_order' => 1,
			'shop_order_refund' => 1,
			'shop_coupon' => 1,
		);
		$cpt = array_diff_key( get_post_types( array( 'public' => true ) ), $post_types_exclude );

		ysm_setting( $w_id, 'custom_post_types', array(
			'type'        => 'select',
			'title'       => 'Custom Post Types',
			'description' => __( 'Enable search through selected custom post types', 'smart-woocommerce-search' ),
			'multiple'    => true,
			'choices'     => $cpt,
			'is_pro'      => true,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Fields', 'smart-woocommerce-search' ); ?></th>

		<?php

		ysm_setting( $w_id, 'field_title', array(
			'type'        => 'checkbox',
			'title'       => __( 'Title', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in the title', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_content', array(
			'type'        => 'checkbox',
			'title'       => __( 'Content', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in the content', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_excerpt', array(
			'type'        => 'checkbox',
			'title'       => __( 'Short description (Excerpt)', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in the short description', 'smart-woocommerce-search' ),
			'value'       => 1,
		));

		ysm_setting( $w_id, 'field_product_sku', array(
			'type'        => 'checkbox',
			'title'       => __( 'Product SKU', 'smart-woocommerce-search' ),
			'description' => __( 'Enable search in product SKU', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'custom_fields', array(
			'type'        => 'textarea_list',
			'title'       => 'Custom Fields',
			'description' => __( 'Enable search in Custom Fields. Fill in each field slug on a new line', 'smart-woocommerce-search' ),
			'value'       => '',
			'is_pro'      => true,
		));

		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Taxonomies', 'smart-woocommerce-search' ); ?></th>

		<?php
		if ( 'product' !== $w_id ) {
			ysm_setting( $w_id, 'field_tag', array(
				'type'        => 'checkbox',
				'title'       => 'Post Tags',
				'description' => __( 'Enable search in post tags', 'smart-woocommerce-search' ),
				'value'       => '',
			));

			ysm_setting( $w_id, 'field_category', array(
				'type'        => 'checkbox',
				'title'       => 'Post Categories',
				'description' => __( 'Enable search in post categories', 'smart-woocommerce-search' ),
				'value'       => '',
			));
		}

		ysm_setting( $w_id, 'field_product_tag', array(
			'type'        => 'checkbox',
			'title'       => 'Product Tags',
			'description' => __( 'Enable search in product tags', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		ysm_setting( $w_id, 'field_product_cat', array(
			'type'        => 'checkbox',
			'title'       => 'Product Categories',
			'description' => __( 'Enable search in product categories', 'smart-woocommerce-search' ),
			'value'       => '',
		));

		/* Custom Tax */
		$tax_list = array();
		$exclude_taxes = array(
			'product_type' => 1,
			'product_visibility' => 1,
			'product_cat' => 1,
			'product_tag' => 1,
			'product_shipping_class' => 1,
		);
		$taxonomies = get_taxonomies( array(
			'_builtin' => false,
		) );

		if ( ! is_wp_error( $taxonomies ) && $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( ! isset( $exclude_taxes[ $taxonomy ] ) ) {
					$tax_list[ $taxonomy ] = $taxonomy;
				}
			}
		}
		ysm_setting( $w_id, 'custom_tax', array(
			'type'        => 'select',
			'title'       => 'Custom Taxonomies',
			'description' => __( 'Enable search in selected custom taxonomies', 'smart-woocommerce-search' ),
			'multiple'    => true,
			'choices'     => $tax_list,
			'is_pro'      => true,
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

	<a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/content-types/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
</div>