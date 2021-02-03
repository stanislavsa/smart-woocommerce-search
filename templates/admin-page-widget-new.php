<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$w_id = 0;
?>
<div class="wrap">
	<h1><span><?php echo esc_html( get_admin_page_title() ); ?></span></h1>
	<?php include_once YSM_DIR . 'templates/admin-page-form.php'; ?>
</div>