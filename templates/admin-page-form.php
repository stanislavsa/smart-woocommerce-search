<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_script( 'postbox' );

$tabs = array(
    'general_tab' => array(
        'label' => __( 'General', 'smart-woocommerce-search' ),
        'icon'  => 'home',
    ),
    'fields_tab' => array(
        'label' => __( 'Items to Search Through', 'smart-woocommerce-search' ),
        'icon'  => 'search',
    ),
    'order_tab' => array(
        'label' => __( 'Sorting', 'smart-woocommerce-search' ),
        'icon'  => 'sort',
    ),

    'fullscreen_tab' => array(
        'label' => __( 'Fullscreen Mode', 'smart-woocommerce-search' ),
        'icon'  => 'fullscreen',
    ),

    'layout_tab' => array(
        'label' => __( 'Layout', 'smart-woocommerce-search' ),
        'icon'  => 'responsive_layout',
    ),
    'styles_tab' => array(
        'label' => __( 'Styling', 'smart-woocommerce-search' ),
        'icon'  => 'palette',
    ),
);
?>
<form method="post" action="" enctype="multipart/form-data">

	<input type="submit" value="<?php esc_attr_e( 'Save', 'smart-woocommerce-search' ); ?>" name="save" class="ymapp-button ymapp-hide-on-mobile" style="float:right;" />

	<?php if ( in_array( $w_id, ysm_get_default_widgets_ids(), true ) ) { ?>
		<?php wp_nonce_field( 'smart_search_default' ); ?>
		<div class="ysm-widget-edit-title-wrap">
			<h2 class="ysm-widgets-title"><?php echo esc_html( ysm_get_default_widgets_names( $w_id ) ); ?></h2>
		</div>
	<?php } else { ?>
		<?php wp_nonce_field( 'smart_search_custom' ); ?>
		<div class="ysm-widget-edit-title-wrap">
			<input type="text" name="name" size="30" value="<?php echo isset( $widgets[ $w_id ] ) ? esc_html( $widgets[ $w_id ]['name'] ) : ''; ?>" placeholder="<?php esc_html_e( 'Enter title', 'smart-woocommerce-search' ); ?>" autocomplete="off">
		</div>
	<?php } ?>

	<div class="clear"></div>

	<div class="meta-box-sortables">

        <div class="sws_box ysm-widget-edit-settings">
            <h2 class="sws_box__title"><span><?php esc_html_e( 'Settings', 'smart-woocommerce-search' ); ?></span></h2>

            <div class="inside sws_inside">
                <div class="sws_tabs_wrapper">
                    <nav class="sws_nav_sidebar">
                        <ul class="sws_nav_sidebar__list">
                            <?php foreach ( $tabs as $id => $item ) { ?>
                                <li data-href="#<?php echo esc_attr( $id ); ?>" class="js-sws-nav-sidebar-item sws_nav_sidebar__item <?php echo 'general_tab' === $id ? ' nav-tab-active' : ''; ?>">
                                    <span class="sws_nav_sidebar__icon material-symbols-rounded"><?php echo esc_html( $item['icon'] ); ?></span>
                                    <span><?php echo esc_html( $item['label'] ); ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                    <div class="sws_tabs_content">
                        <?php include 'admin-tab-general.php'; ?>

                        <?php include 'admin-tab-fields.php'; ?>

                        <?php include 'admin-tab-order.php'; ?>

                        <?php include 'admin-tab-fullscreen.php'; ?>

                        <?php include 'admin-tab-layout.php'; ?>

                        <?php include 'admin-tab-styling.php'; ?>

                    </div>
                </div>


                <!--				<h2 class="nav-tab-wrapper" id="ymapp-settings__nav">-->
                <!--					--><?php //foreach ( $tabs as $id => $title ) { ?>
                <!--						<span data-href="#--><?php //echo esc_attr( $id ); ?><!--" class="nav-tab--><?php //echo 'general_tab' === $id ? ' nav-tab-active' : ''; ?><!--">--><?php //echo esc_html( $title ); ?><!--</span>-->
                <!--					--><?php //} ?>
                <!--				</h2>-->



            </div>

        </div>


    </div>

	<p class="submit" style="float: right;">
		<input type="submit" value="<?php esc_attr_e( 'Save', 'smart-woocommerce-search' ); ?>" name="save" class="ymapp-button" />
	</p>

</form>
