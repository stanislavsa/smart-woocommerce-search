<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
$tabs_def = array(
	'default' => __( 'WordPres Default Search', 'smart_search' ),
	'product' => __( 'WooCommerce Product Search', 'smart_search' ),
);
$tabs_def = apply_filters( 'smart_search_admin_tabs', $tabs_def );
$current_tab = ! empty( $type ) && isset( $tabs_def[ $type ] ) ? $type : 'default';
$widgets = ysm_get_default_widgets();
$w_id = $current_tab;
?>
<div class="wrap">

	<h1><span><?php echo esc_html( sprintf( __( 'Extend %s Widget', 'smart_search' ), $tabs_def[ $current_tab ] ) ); ?></span></h1>

	<?php ysm_message(); ?>

	<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

</div>
