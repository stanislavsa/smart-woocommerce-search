<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="fields_tab" class="ymapp-settings__content">
	<table class="form-table">
		<tbody>

		<?php if ( $w_id !== 'product' ) { ?>

			<th class="ymapp-settings__title"><?php esc_html_e( 'Post Types', 'smart_search' ); ?></th>

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
						'title'       => sprintf( __( 'Search in %s', 'smart_search' ), $post_type->label ),
						'description' => sprintf( __( 'Enable search through "%s" post type', 'smart_search' ), $post_type->labels->singular_name ),
						'value'       => $search_in[ $id ],
					));
				}
			}
			?>

		<?php } ?>

		<?php
		ysm_setting( $w_id, 'custom_post_types', array(
			'type'  => 'pro',
			'title' => __( 'Search in Custom Post Types', 'smart_search' ),
			'description' => __( 'Searching will be provided in selected custom post types', 'smart_search' ),
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Product Variations', 'smart_search' ); ?></th>

		<?php
		ysm_setting( $w_id, 'post_type_product_variation', array(
			'type'  => 'checkbox',
			'title' => __( 'Search in Variations', 'smart_search' ),
			'description' => __( 'Deprecated: Will be available only in PRO version', 'smart_search' ),
			'value' => 0,
		));
		?>

		<th class="ymapp-settings__title"><?php esc_html_e( 'Fields', 'smart_search' ); ?></th>

		<?php
		$fields = array(
			'title'   => __('Title', 'smart_search'),
			'content' => __('Content', 'smart_search'),
			'excerpt' => __('Excerpt', 'smart_search'),
			'tag' => __('Post Tag', 'smart_search'),
			'category' => __('Post Category', 'smart_search'),
			'product_tag' => __('Product Tag', 'smart_search'),
			'product_cat' => __('Product Category', 'smart_search'),
			'product_sku' => __('Product SKU', 'smart_search'),
		);

		if ($w_id === 'product') {
			unset($fields['tag']);
			unset($fields['category']);
		}

		$search_in = array(
			'title' => 1,
			'content' => 1,
			'excerpt' => 1,
		);

		foreach ( $fields as $id => $field ) {

			ysm_setting( $w_id, 'field_' . $id, array(
				'type' => 'checkbox',
				'title' => sprintf(  __('Search in %s', 'smart_search' ), $field ),
				'description' => 'product_sku' === $id ? __( 'Deprecated: Will be available only in PRO version', 'smart_search' ) : sprintf( __('Enable search through "%s"', 'smart_search'), $field ),
				'value' => isset( $search_in[$id] ) ? $search_in[$id] : '',
			));

		}

		/* Custom Tax */
		ysm_setting( $w_id, 'custom_tax', array(
			'type'  => 'pro',
			'title' => __( 'Search in Custom Taxonomies', 'smart_search' ),
			'description' => __( 'Searching will be provided in selected custom taxonomies', 'smart_search' ),
		));

		ysm_setting( $w_id, 'custom_fields', array(
			'type'  => 'pro',
			'title' => __( 'Search in Custom Fields', 'smart_search' ),
			'description' => __( 'Custom Fields slug separated by comma. Searching will be provided in custom fields values', 'smart_search' ),
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
			'type' => 'select',
			'title' => __('Allowed Product Categories', 'smart_search'),
			'description' => __('Restrict product searching by chosen product categories', 'smart_search'),
			'multiple' => true,
			'choices' => $product_cats_list,
		));

		ysm_setting( $w_id, 'disallowed_product_cat', array(
			'type' => 'select',
			'title' => __('Disallowed Product Categories', 'smart_search'),
			'description' => __('Do not search in chosen product categories', 'smart_search'),
			'multiple' => true,
			'choices' => $product_cats_list,
		));

		?>

		</tbody>
	</table>
</div>