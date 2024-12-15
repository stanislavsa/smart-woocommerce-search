<div id="fullscreen_tab" class="ymapp-settings__content">
    <div data-href="#fullscreen_tab" class="sws_tab_mobile_heading js-sws-tab-mobile-heading">
        <div class="sws_tab_mobile_heading__inner">
            <span class="sws_nav_sidebar__icon material-symbols-rounded">fullscreen</span>
            <?php echo __( 'Fullscreen Mode', 'smart-woocommerce-search' ) ?>
        </div>
    </div>
    <div class="sws_tab_content">
        <table class="form-table form-table--fullscreen_mod">
            <tbody>
            <?php
            ysm_setting( $w_id, 'fullscreen_mode', array(
                'type'        => 'select',
                'title'       => __( 'Full Screen Mode', 'smart-woocommerce-search' ),
                'description' =>  __( 'Enable fullscreen mode to display the search input and results within an expanded popup', 'smart-woocommerce-search' ),
                'value'       => '',
                'is_pro'      => false,
                'choices'     => array(
                    'disable' => __( 'Disable', 'smart-woocommerce-search' ),
                    'enable' => __( 'Enable', 'smart-woocommerce-search' ),
                    'mobile_only' => __( 'Only on mobile', 'smart-woocommerce-search' ),
                    'desktop_only' => __( 'Only on desktop', 'smart-woocommerce-search' ),
                ),
            ));
            ?>

            </tbody>
        </table>
        <a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/layout-settings/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
    </div>


</div>