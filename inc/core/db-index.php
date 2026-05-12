<?php
namespace YSWS\Core\DB_Index;

const INDEXED_META_KEY      = 'sws_indexed';
const RECURRING_CRON_ACTION = 'sws_bulk_index_recurring_hook';

add_action( 'save_post', __NAMESPACE__ . '\\post_save_action', 999 );
add_action( 'woocommerce_save_product_variation', __NAMESPACE__ . '\\variation_save_action', 999 );
add_action( 'woocommerce_api_save_product_variation', __NAMESPACE__ . '\\variation_save_action', 999 );
add_action( 'woocommerce_rest_save_product_variation', __NAMESPACE__ . '\\variation_save_action', 999 );
add_action( 'untrashed_post', __NAMESPACE__ . '\\post_save_action', 999 );
add_action( 'wp_trash_post', __NAMESPACE__ . '\\post_trash_action', 999 );

add_action( 'admin_init', __NAMESPACE__ . '\\install_tables', 10 );
add_action( 'admin_init', __NAMESPACE__ . '\\recurring_cron' );
add_action( 'admin_notices', __NAMESPACE__ . '\\show_activation_message', 10 );
sws_fs()->add_action( 'after_uninstall', __NAMESPACE__ . '\\drop_tables' );
add_filter( 'is_protected_meta', __NAMESPACE__ . '\\protected_meta', 10, 2 );

add_action( 'sws_bulk_index_hook', __NAMESPACE__ . '\\bulk_index_posts' ); // cron
add_action( RECURRING_CRON_ACTION, __NAMESPACE__ . '\\recurring_bulk_index_posts' ); // cron
add_action( 'sws_widget_settings_saved', __NAMESPACE__ . '\\widget_settings_saved', 10, 3 );
add_action( 'wp_ajax_sws_index_button_click', __NAMESPACE__ . '\\ajax_index_button_click' );
add_action( 'wp_ajax_sws_index_button_click_check', __NAMESPACE__ . '\\ajax_index_button_click_check' );
add_action( 'wp_ajax_sws_index_button_delete', __NAMESPACE__ . '\\ajax_index_button_delete' );
add_action( 'wp_ajax_sws_index_auto_update_on_save', __NAMESPACE__ . '\\ajax_index_auto_update_on_save' );
add_action( 'wp_ajax_sws_message_index_now_dismiss', __NAMESPACE__ . '\\ajax_message_index_now_dismiss' );
add_action( 'wp_ajax_woocommerce_feature_product', __NAMESPACE__ . '\\update_featured_product', 1 );


function widget_settings_saved( $widget_id, $old_settings, $new_settings ) {
	if ( ! get_option( 'sws_index_update_on_widget_save' ) ) {
		return;
	}

	$should_update = false;
	$should_update_settings_list = [
//		'post_type_post',
//		'post_type_page',
//		'post_type_product',
//		'post_type_product_variation',
		'custom_post_types',
		'custom_fields',
		'field_tag',
		'field_category',
		'field_product_tag',
		'field_product_cat',
		'custom_tax',
	];

	foreach ( $should_update_settings_list as $item ) {
		if (
			( isset( $old_settings[ $item ] ) && ! isset( $new_settings[ $item ] ) )
			|| ( ! isset( $old_settings[ $item ] ) && isset( $new_settings[ $item ] ) )
			|| (  isset( $old_settings[ $item ] ) && isset( $new_settings[ $item ] ) && $old_settings[ $item ] !== $new_settings[ $item ] )
		) {
			$should_update = true;
			break;
		}
	}

	if ( ! $should_update ) {
		return;
	}

	$timestamp = time();
	update_option( 'sws_bulk_index_lock', $timestamp );

//	wp_clear_scheduled_hook(
//		'sws_bulk_index_hook',
//		[]
//	);
//	wp_schedule_single_event(
//		$timestamp + 30,
//		'sws_bulk_index_hook',
//		[]
//	);
}

function ajax_index_button_click() {
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'sws_index_button_nonce_action' ) ) {
		exit;
	}

	$timestamp = time();
	$index_lock = $timestamp;

	wp_clear_scheduled_hook(
		'sws_bulk_index_hook',
		[]
	);

	update_option( 'sws_bulk_index_lock', $index_lock );
	set_index_status('doing');
	truncate_tables();

	$query_args = [
		'post_type'      => ysm_get_post_types(),
		'post_status'    => [ 'publish' ],
		'paged'          => 1,
		'fields'         => 'ids',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'OR',
			[
				'key'     => INDEXED_META_KEY,
				'compare' => 'NOT EXISTS'
			],
			[
				'key'     => INDEXED_META_KEY,
				'value'   => $index_lock,
				'compare' => '!='
			]
		]
	];

	$query = new \WP_Query( $query_args );
	$total_not_indexed = $query->found_posts;

	echo wp_json_encode( [
		'status'  => 'doing',
		'not_indexed' => $total_not_indexed,
		'indexed' => 0,
	] );
	exit;
}

function ajax_index_button_click_check() {
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'sws_index_button_nonce_action' ) ) {
		exit;
	}

	$res = bulk_index_posts( true );
	$index_lock = get_option( 'sws_bulk_index_lock' );
	$index_status = get_index_status();

	echo wp_json_encode( [
		'status' => $index_status,
		'indexed' => count_indexed_posts(),
		'posts_left' => $res['posts_left'],
		'lock' => $index_lock,
	] );
	exit;
}

function ajax_index_button_delete() {
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! wp_verify_nonce( $nonce, 'sws_index_button_nonce_action' ) ) {
		exit;
	}

	set_index_status('empty');
	delete_option( 'sws_db_version' );
	install_tables();
	truncate_tables();

	wp_clear_scheduled_hook(
		'sws_bulk_index_hook',
		[]
	);

	exit;
}

function ajax_index_auto_update_on_save() {
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$value = filter_input( INPUT_POST, 'value', FILTER_DEFAULT );

	if ( ! wp_verify_nonce( $nonce, 'sws_index_button_nonce_action' ) ) {
		exit;
	}

	if ( $value ) {
		update_option( 'sws_index_update_on_widget_save', 1 );
	} else {
		delete_option('sws_index_update_on_widget_save');
	}

	echo wp_json_encode( [
		'status' => 'updated',
	] );
	exit;
}

function ajax_message_index_now_dismiss() {
	update_option( 'sws_message_index_now_dismiss', 1 );
	exit;
}

function recurring_cron() {
	if ( ! wp_next_scheduled( RECURRING_CRON_ACTION ) ) {
		wp_schedule_event( time(), 'hourly', RECURRING_CRON_ACTION );
	}
}

/**
 * cron callback
 * @return void
 */
function recurring_bulk_index_posts() {
	bulk_index_posts();
}

function bulk_index_posts( $batch = false ) {
	if ( ! $batch ) {
		set_index_status('doing');
	}

	$lock = get_option( 'sws_bulk_index_lock' );

	// index posts
	global $wpdb;
	$posts_per_page   = defined( 'ICL_LANGUAGE_CODE' ) ? 20 : 100;
	$post_types       = ysm_get_post_types();
	$pt_placeholders  = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );

	// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
	$sql_query = $wpdb->prepare(
		"SELECT {$wpdb->posts}.ID
		 FROM {$wpdb->posts}
		     LEFT JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = %s )
		     LEFT JOIN {$wpdb->postmeta} AS mt1 ON ( {$wpdb->posts}.ID = mt1.post_id )
		 WHERE (
			  {$wpdb->postmeta}.post_id IS NULL
			  OR
			  ( mt1.meta_key = %s AND mt1.meta_value != %s )
			)
		   AND {$wpdb->posts}.post_type IN ($pt_placeholders)
		   AND {$wpdb->posts}.post_status = 'publish'
		 GROUP BY {$wpdb->posts}.ID
		 ORDER BY {$wpdb->posts}.post_date DESC
		 LIMIT 0, {$posts_per_page}",
		array_merge( [ INDEXED_META_KEY, INDEXED_META_KEY, $lock ], $post_types )
	);
	$sql_query_count = $wpdb->prepare(
		"SELECT COUNT(DISTINCT {$wpdb->posts}.ID)
		 FROM {$wpdb->posts}
		     LEFT JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = %s )
		     LEFT JOIN {$wpdb->postmeta} AS mt1 ON ( {$wpdb->posts}.ID = mt1.post_id )
		 WHERE (
			  {$wpdb->postmeta}.post_id IS NULL
			  OR
			  ( mt1.meta_key = %s AND mt1.meta_value != %s )
			)
		   AND {$wpdb->posts}.post_type IN ($pt_placeholders)
		   AND {$wpdb->posts}.post_status = 'publish'",
		array_merge( [ INDEXED_META_KEY, INDEXED_META_KEY, $lock ], $post_types )
	);
	// phpcs:enable
	$posts_left = $wpdb->get_var($sql_query_count);

	do {
		$post_ids = $wpdb->get_col($sql_query);
		foreach ( $post_ids as $post_id ) {
			if ( is_post_indexable( $post_id ) ) {
				update_post_with_children( $post_id );
			}
			$posts_left--;
		}

		if ( $batch ) {
			break;
		}

		sleep( 1 );

	} while ( $posts_per_page === count($post_ids) );

	if ( ! $batch || 0 === $posts_left ) {
		set_index_status('ready');
	}

	return [
		'status'  => get_index_status(),
		'posts_left'  => $posts_left,
		'indexed' => count_indexed_posts(),
	];
}

function show_activation_message() {
	if ( ! get_index_status() && ! get_option('sws_message_index_now_dismiss') ) {
		?>
		<div id="sws_message_index_now" class="notice notice-warning is-dismissible">
			<p>
				<?php echo esc_html( sprintf( __( '%s index is empty! We highly recommend to create index to improve search performance.', 'smart-woocommerce-search' ), 'Smart Search' ) ); ?>
				<a href="<?php echo esc_url( admin_url( '/admin.php?page=smart-search-index-status' ) ); ?>"><?php esc_html_e( 'Learn more here.', 'smart-woocommerce-search' ); ?></a>
			</p>
		</div>
		<script>
			jQuery(document).on( 'click', '#sws_message_index_now .notice-dismiss', function() {
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'sws_message_index_now_dismiss'
					}
				})
			})
		</script>
		<?php
	}
}

/**
 * Add/update post index on 'save_post' action
 * @param $post_id
 *
 * @return void
 */
function post_save_action( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! is_post_indexable( $post_id ) ) {
		delete_post_with_children( $post_id );
		return;
	}

	delete_post_with_children( $post_id, 'fields' );
	update_post_with_children( $post_id );
}

/**
 * Add/update variation index on 'save' action
 * @param $post_id
 *
 * @return void
 */
function variation_save_action( $post_id ) {
	$variation = wc_get_product( $post_id );

	if ( ! $variation ) {
		return;
	}

	// check if parent product is not hidden from search and published
	if ( ! is_post_indexable( $variation->get_parent_id() ) ) {
		delete_post_index( $post_id );
		return;
	}

	// check if variation is visible
	if ( ! is_variation_indexable( $post_id, $variation ) ) {
		delete_post_index( $post_id );
		return;
	}

	delete_post_index( $post_id, 'fields' );
	update_post_index( $post_id );
}

/**
 * Delete post index on 'wp_trash_post' action
 * @param $post_id
 *
 * @return void
 */
function post_trash_action( $post_id ) {
	delete_post_with_children( $post_id );
}

/**
 * Process product and its variations
 * @param $post_id
 *
 * @return void
 */
function update_post_with_children( $post_id ) {
	$the_post = get_post( $post_id );

	if ( $the_post ) {
		update_post_index( $the_post->ID );

		if ( 'product' === $the_post->post_type ) {
			$wc_product = wc_get_product( $the_post->ID );
			if ( $wc_product && 'variable' === $wc_product->get_type() ) {
				$variation_ids = get_children( [
					'post_parent' => $the_post->ID,
					'post_type'   => 'product_variation',
					'numberposts' => -1,
					'post_status' => 'any',
					'fields'      => 'ids',
				], ARRAY_A );

				if ( $variation_ids && is_array( $variation_ids ) ) {
					foreach ( $variation_ids as $variation_id ) {

						// check if variation is visible
						if ( ! is_variation_indexable( $variation_id ) ) {
							delete_post_index( $variation_id );
							continue;
						}

						update_post_index( $variation_id );
					}
				}
			}
		}
	}
}

/**
 * Handle product with its variations when removed
 * @param $post_id
 * @param $what
 *
 * @return void
 */
function delete_post_with_children( $post_id, $what = 'all' ) {
	delete_post_index( $post_id, $what );

	$the_post = get_post( $post_id );

	if ( $the_post && 'product' === $the_post->post_type ) {
		$variation_ids = get_children( [
			'post_parent' => $the_post->ID,
			'post_type'   => 'product_variation',
			'numberposts' => -1,
			'post_status' => 'any',
			'fields'      => 'ids',
		], ARRAY_A );

		if ( $variation_ids && is_array( $variation_ids ) ) {
			foreach ( $variation_ids as $variation_id ) {
				delete_post_index( $variation_id, $what );
			}
		}
	}
}

/**
 * Add/update index data for post
 * @param $post_id
 *
 * @return void
 */
function update_post_index( $post_id ) {
	global $wpdb;

	$the_post = get_post( $post_id );

	if ( ! $the_post ) {
		return;
	}

	$wc_product = null;
	if ( class_exists( 'WooCommerce' ) && ( 'product' === $the_post->post_type || 'product_variation' === $the_post->post_type ) ) {
		$wc_product = wc_get_product( $the_post->ID );
	}

	// sws_post_data
	$post_data = [
		'ID'            => $the_post->ID,
		'post_title'    => prepare_string_to_index( $the_post->post_title ),
		'post_content'  => prepare_string_to_index( $the_post->post_content ),
		'post_excerpt'  => prepare_string_to_index( $the_post->post_excerpt ),
		'post_type'     => $the_post->post_type,
		'post_parent'   => $the_post->post_parent,
		'post_date'     => $the_post->post_date,
		'post_modified' => $the_post->post_modified,
		'sub_type'      => '',
		'sku'           => '',
		'ean'           => '',
		'stock_status'  => '',
		'onsale'        => '',
		'featured'      => '',
		'lang'          => '',
	];

	if ( $wc_product ) {
		$product_ean = get_post_meta( $the_post->ID, '_global_unique_id', true );
		if ( ! $product_ean ) {
			$product_ean = get_post_meta( $the_post->ID, '_alg_ean', true );
		}
		$post_data['post_content'] = prepare_string_to_index( $wc_product->get_description() );
		$post_data['post_excerpt'] = prepare_string_to_index( $wc_product->get_short_description() );
		$post_data['sub_type']     = $wc_product->get_type();
		$post_data['sku']          = prepare_string_to_index( $wc_product->get_sku() );
		$post_data['ean']          = prepare_string_to_index( $product_ean );
		$post_data['stock_status'] = $wc_product->get_stock_status();
		$post_data['onsale']       = $wc_product->is_on_sale() ? 'y' : '';
		// todo: update on ajax
		if ( 'product_variation' === $the_post->post_type ) {
			$post_data['featured'] = wc_get_product( $wc_product->get_parent_id() )->is_featured() ? 'y' : '';
		} else {
			$post_data['featured'] = $wc_product->is_featured() ? 'y' : '';
		}
	}

	if ( defined( 'POLYLANG_BASENAME' ) ) {
		$polylang_slug = pll_get_post_language( $post_id, 'slug' );
		if ( ! empty( $polylang_slug ) ) {
			$post_data['lang'] = $polylang_slug;
		} else {
//			$post_data['lang'] = pll_default_language();;
		}
	} elseif ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE !== '' ) {
		$wpml_details = apply_filters( 'wpml_post_language_details', '', $post_id );
		if ( ! empty( $wpml_details['language_code'] ) ) {
			$post_data['lang'] = $wpml_details['language_code'];
		}
	}

	// update index - sws_post_data
	// REPLACE INTO handles both INSERT and UPDATE by primary key (ID),
	// avoiding the need for a preceding SELECT and manual LOCK TABLES.
	$wpdb->replace(
		$wpdb->prefix . 'sws_post_data',
		$post_data,
		[ '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
	);

	// sws_post_fields
	$rows = [];

	$custom_fields = ysm_get_all_widgets_custom_fields();
	foreach ( $custom_fields as $field ) {
		if ( in_array( $field, [ '_sku', '_alg_ean', '_global_unique_id' ], true ) ) {
			continue;
		}
		$field_value = get_post_meta( $the_post->ID, $field, true );
		if ( ! $field_value && 'product_variation' === $the_post->post_type ) {
			if ( ! in_array( $field, [ '_variation_description' ], true ) ) {
				$field_value = get_post_meta( $the_post->post_parent, $field, true );
			}
		}
		if ( $field_value ) {
			$rows[] = [
				'post_id' => $the_post->ID,
				'type'    => 'custom-field',
				'name'    => $field,
				'value'   => prepare_string_to_index( $field_value ),
			];
		}
	}

	if ( $wc_product ) {
		$attributes = $wc_product->get_attributes();

		if ( $attributes ) {
			if ( 'product_variation' === $the_post->post_type ) {
				foreach ( $attributes as $attribute_key => $attribute_val ) {
					if ( ! empty( $attribute_val ) ) {
						$rows[] = [
							'post_id' => $the_post->ID,
							'type'    => 'custom-field',
							'name'    => 'attribute_' . $attribute_key,
							'value'   => prepare_string_to_index( $attribute_val ),
						];
					}
				}
			} else {
				foreach ( $attributes as $attribute_key => $attribute_obj ) {
					if ( is_object( $attribute_obj ) && $attribute_obj->get_options() ) {
						$rows[] = [
							'post_id' => $the_post->ID,
							'type'    => 'custom-field',
							'name'    => 'attribute_' . $attribute_key,
							'value'   => prepare_string_to_index( implode( '|', $attribute_obj->get_options() ) ),
						];
					}
				}
			}
		}
	}

	$taxonomy_terms = ysm_get_all_widgets_taxonomy_terms();
	foreach ( $taxonomy_terms as $tax_slug ) {
		if ('product_variation' === $the_post->post_type ) {
			if ( in_array( $tax_slug, [ 'post_tag', 'category' ], true ) || str_contains( $tax_slug, 'pa_' ) ) {
				continue;
			}
			$terms = wp_get_post_terms( $the_post->post_parent, $tax_slug, [ 'fields' => 'names' ] );
		} else {
			$terms = wp_get_post_terms( $the_post->ID, $tax_slug, [ 'fields' => 'names' ] );
		}
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term_name ) {
				$rows[] = [
					'post_id' => $the_post->ID,
					'type'    => 'taxonomy',
					'name'    => $tax_slug,
					'value'   => prepare_string_to_index( $term_name ),
				];
			}
		}
	}

	// update index - sws_post_fields
	delete_post_index( $post_id, 'fields' );
	if ( $rows ) {
		$placeholders = implode( ', ', array_fill( 0, count( $rows ), '(%d, %s, %s, %s)' ) );
		$values = [];
		foreach ( $rows as $row ) {
			array_push( $values, $row['post_id'], $row['type'], $row['name'], $row['value'] );
		}
		$wpdb->query( $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}sws_post_fields (post_id, type, name, value) VALUES {$placeholders}",
			$values
		) );
	}

	if ( ! $the_post->post_parent ) {
		update_post_meta( $the_post->ID, INDEXED_META_KEY, get_option( 'sws_bulk_index_lock' ) );
	}
}

/**
 * Delete index data for post
 * @param $post_id
 * @param $what what should be deleted
 *  - 'all' delete post index data in all tables
 *  - 'data' delete post index data in 'sws_post_data' table
 *  - 'fields' delete post index data in 'sws_post_fields' table
 *
 * @return void
 */
function delete_post_index( $post_id, $what = 'all' ) {
	global $wpdb;

	// remove data from index
	if ( 'all' === $what || 'data' === $what ) {
		$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}sws_post_data WHERE ID = %d", $post_id );
		$wpdb->query( $sql );
	}
	if ( 'all' === $what || 'fields' === $what ) {
		$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}sws_post_fields WHERE post_id = %d", $post_id );
		$wpdb->query( $sql );
	}
}


/**
 * Get status of index:
 *  'doing'  - in progress
 *  'ready'  - built successfully
 *  'failed' - built with errors
 * @return mixed|string
 */
function get_index_status() {
	$status = get_option( 'sws_plugin_index_status' );
	if ( $status && in_array( $status, [ 'doing', 'ready', 'failed' ] ) ) {
		return $status;
	}

	return '';
}

/**
 * Set status of index
 * @param $status
 *
 * @return void
 */
function set_index_status( $status ) {
	if ( $status && in_array( $status, [ 'doing', 'ready', 'failed', 'empty' ] ) ) {
		update_option( 'sws_plugin_index_status', $status );
	}
}

/**
 * Get number of indexed posts
 * @param $post_type
 *
 * @return int
 */
function count_indexed_posts( $post_type = '' ) {
	global $wpdb;

	$sql = "SELECT COUNT(DISTINCT(ID)) FROM {$wpdb->prefix}sws_post_data";

	if ( $post_type && post_type_exists( $post_type ) ) {
		$sql .= $wpdb->prepare( " WHERE post_type = %s", $post_type );
	} else {
		$post_types   = ysm_get_post_types();
		$placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );
		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$sql .= $wpdb->prepare( " WHERE post_type IN ($placeholders)", $post_types );
	}

	$count = $wpdb->get_var( $sql );

	if ( empty( $count ) ) {
		$count = 0;
	}

	return (int) $count;
}

/**
 * Remove data in the index tables
 * @return void
 */
function truncate_tables() {
	global $wpdb;

	$tables = get_tables();

	foreach ( $tables as $table_name => $table_sql ) {
		$sql = "TRUNCATE TABLE $table_name";
		$wpdb->query( $sql );
	}
}

/**
 * Set up the database tables which the plugin needs to function.
 * @return void
 */
function install_tables() {
	global $wpdb;

	$ver = get_option( 'sws_db_version' );
	if ( $ver === SWS_DB_VERSION ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$tables = get_tables();

	foreach ( $tables as $table_name => $table_sql ) {
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		if ( $wpdb->get_var( $query ) !== $table_name ) {
			$wpdb->query( $table_sql );
		}
	}

	dbDelta( implode( "\n", $tables ) );
	update_option( 'sws_db_version', SWS_DB_VERSION );
}

/**
 * Drop plugin tables
 * @return void
 */
function drop_tables() {
	global  $wpdb ;

	$tables = get_tables();

	foreach ( $tables as $table_name => $table_sql ) {
		$sql = $wpdb->prepare( "SHOW TABLES LIKE %s;", $table_name );

		if ( $wpdb->get_var( $sql ) === $table_name ) {
			$wpdb->query( "DROP TABLE {$table_name};" );
		}
	}

	delete_option( 'sws_plugin_index_status' );
	delete_option( 'sws_message_index_now_dismiss' );
	delete_option( 'sws_db_version' );
	delete_option( 'sws_bulk_index_lock' );
}

/**
 * Check if plugin tables exist
 * @return bool
 */
function tables_exists() {
	global  $wpdb ;

	$tables = get_tables();

	foreach ( $tables as $table_name => $table_sql ) {
		$sql = $wpdb->prepare( "SHOW TABLES LIKE %s;", $table_name );

		if ( $wpdb->get_var( $sql ) !== $table_name ) {
			return false;
		}
	}

	return true;
}

/**
 * Get list of tables sql
 * @return string[]
 */
function get_tables() {
	global $wpdb;

	$collate = $wpdb->get_charset_collate();

	return [
		$wpdb->prefix . 'sws_post_data' => "CREATE TABLE {$wpdb->prefix}sws_post_data (
											  	ID bigint(20) unsigned NOT NULL,
												post_type varchar(20) NOT NULL default 'post',
												sub_type varchar(20) NOT NULL default '',
												post_parent bigint(20) unsigned NOT NULL default '0',
												sku varchar(255) NOT NULL default '',
												ean varchar(255) NOT NULL default '',
												lang varchar(20) NOT NULL default '',
												stock_status varchar(20) NOT NULL default '',
												onsale varchar(20) NOT NULL default '',
												featured varchar(20) NOT NULL default '',
												post_title text NOT NULL,
												post_content longtext NOT NULL,
												post_excerpt text NOT NULL,
												post_date datetime NOT NULL default '0000-00-00 00:00:00',
												post_modified datetime NOT NULL default '0000-00-00 00:00:00',
												min_price decimal(19,4) NULL default NULL,
  												max_price decimal(19,4) NULL default NULL,
												PRIMARY KEY (ID),
												KEY type_date (post_type,post_date,ID),
												KEY post_parent (post_parent),
												KEY sku (sku),
												KEY ean (ean),
												KEY lang (lang),
												KEY stock_status (stock_status),
												KEY min_max_price (`min_price`, `max_price`)
											) $collate;",

		$wpdb->prefix . 'sws_post_fields' => "CREATE TABLE {$wpdb->prefix}sws_post_fields (
											  	post_id bigint(20) unsigned NOT NULL default 0,
												type varchar(255) default NULL,
												name varchar(255) default NULL,
  												value text,
												KEY `post_id` (`post_id`),
												KEY `type` (`type`),
												KEY `name` (`name`),
												KEY `type_name` (`type`, `name`)
											) $collate;",
	];
}

function prepare_string_to_index( $string ) {
	if ( empty( $string ) ) {
		return '';
	}

	$string = function_exists('mb_strtolower') ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
	$string = strip_shortcodes( $string );
	$string = wp_strip_all_tags( $string );
	$string = ysws_strip_punctuation( $string );

	return $string;
}

/**
 * Check if post/product should be indexed
 * @param $post_id
 *
 * @return bool
 */
function is_post_indexable( $post_id ) {
	$the_post = get_post( $post_id );

	if ( ! $the_post ) {
		return false;
	}

	if ( ! in_array( $the_post->post_type, ysm_get_post_types(), true ) ) {
		return false;
	}

	if ( 'publish' !== $the_post->post_status ) {
		return false;
	}

	if ( class_exists( 'SearchExclude' ) ) {
		$search_exclude = get_option( 'sep_exclude', array() );
		if ( ! empty( $search_exclude ) && is_array( $search_exclude ) && in_array( $post_id, array_map( 'absint', $search_exclude ), true ) ) {
			return false;
		}
	}

	if ( 'product' === $the_post->post_type && function_exists( 'wc_get_product_visibility_term_ids' ) ) {
		$wc_product_visibility_term_ids = wc_get_product_visibility_term_ids();
		if ( ! empty( $wc_product_visibility_term_ids['exclude-from-search'] ) ) {
			if ( has_term( $wc_product_visibility_term_ids['exclude-from-search'], 'product_visibility', $post_id ) ) {
				return false;
			}
		}
	}

	return true;
}

/**
 * Check if variation should be indexed
 * @param $post_id
 * @param $variation
 *
 * @return bool
 */
function is_variation_indexable( $post_id, $variation = null ) {
	if ( ! $variation ) {
		$variation = wc_get_product( $post_id );
	}

	if ( ! $variation || ! $variation->exists() ) {
		return false;
	}

	// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
	if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $variation->get_parent_id(), $variation ) && ! $variation->variation_is_visible() ) {
		return false;
	}

	return true;
}

/**
 * Set the meta as protected.
 *
 * @param  boolean $protected Whether or not to protect the meta key.
 * @param  string  $meta_key  The meta key potentially being output.
 * @return boolean            Whether or not to protect the meta key.
 */
function protected_meta( $protected, $meta_key ) {
	if ( INDEXED_META_KEY === $meta_key ) {
		$protected = true;
	}

	return $protected;
}

function update_featured_product() {
	if ( current_user_can( 'edit_products' ) && check_admin_referer( 'woocommerce-feature-product' ) && isset( $_GET['product_id'] ) ) {
		global $wpdb;

		$product_id = absint( $_GET['product_id'] );
		$product    = wc_get_product( $product_id );

		if ( $product ) {
			// WooCommerce toggles the value after this hook fires (priority 1),
			// so the current state is still the old value — invert it.
			$featured = $product->get_featured() ? '' : 'y';

			$updated = $wpdb->update(
				$wpdb->prefix . 'sws_post_data',
				[ 'featured' => $featured ],
				[ 'ID'       => $product->get_id() ],
				[ '%s' ],
				[ '%d' ]
			);

//			if ( false !== $updated && 0 === $updated && is_post_indexable( $product_id ) ) {
//				add_action( 'shutdown', function() use ( $product_id ) {
//					update_post_with_children( $product_id );
//					//update_post_index( $product_id );
//				} );
//			}
		}
	}
}