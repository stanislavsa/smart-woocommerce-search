<?php
if ( !defined('ABSPATH') ) exit;

$tabs = array(
	'default' => __('WordPres Default Search', 'smart_search'),
	'product' => __('WooCommerce Default Product Search', 'smart_search'),
);
$tabs = apply_filters('smart_search_admin_tabs', $tabs);
$current_tab = !empty($_GET['type']) && isset($tabs[$_GET['type']]) ? $_GET['type'] : 'default';
$widgets = ysm_get_default_widgets();
$w_id = $current_tab;
?>
<div class="wrap">

	<h1><?php echo sprintf( __( 'Extend %s Widget', 'smart_search' ), $tabs[$current_tab] ); ?></h1>

	<h2 class="nav-tab-wrapper ysm-nav-tab-wrapper">
		<?php foreach ($tabs as $id => $title) { ?>
		<a href="<?php echo admin_url( 'admin.php?page=smart-search&type='.$id ) ?>" class="nav-tab<?php echo $current_tab === $id ? ' nav-tab-active' : '' ?>"><?php echo $title ?></a>
		<?php } ?>
	</h2>

	<?php ysm_message(); ?>

	<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>

</div>
