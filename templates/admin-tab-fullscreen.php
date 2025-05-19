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
            <th class="ymapp-settings__title"><?php esc_html_e( 'Recommended Products', 'smart-woocommerce-search' ); ?></th>
            <?php

            ysm_setting($w_id, 'selected_products', array(
	            'type'        => 'select',
	            'title'       => __('Select Products', 'smart-woocommerce-search'),
	            'value'       => '',
	            'multiple'    => true,
	            'is_pro'      => false,
	            'choices'     => [],
	            'placeholder' => __('Start typing product name...', 'smart-woocommerce-search'),
	            'is_pro'      => 1,
            ));

            ysm_setting( $w_id, 'selected_products_label', array(
	            'type'        => 'text',
	            'title'       => __( '"Selected Products" Label', 'smart-woocommerce-search' ),
	            'value'       => 'Recent Products',
	            'is_pro'      => 1,
            ));

            ysm_setting( $w_id, 'selected_products_mobile', array(
	            'type'        => 'checkbox',
	            'title'       => __( 'Hide on mobile', 'smart-woocommerce-search' ),
	            'value'       => 1,
	            'is_pro'      => 1,
            ));


            ?>

            <th class="ymapp-settings__title"><?php esc_html_e( 'Categories', 'smart-woocommerce-search' ); ?></th>

            <?php

            $ysm_categories = get_categories( array(
                'taxonomy'   => 'product_cat',
                'orderby' => 'name',
                'order'   => 'ASC'
            ) );
            $ysm_category_choices = array();
            foreach ($ysm_categories as $ysm_category) {
                $ysm_category_choices[$ysm_category->term_id] = $ysm_category->name;
            }

            ysm_setting( $w_id, 'selected_categories', array(
                'type'        => 'select',
                'title'       => __( 'Display selected categories', 'smart-woocommerce-search' ),
                'value'       => '',
                'is_pro'      => false,
                'multiple'    => true,
                'choices'     => $ysm_category_choices,

            ));

            ysm_setting( $w_id, 'selected_categories_label', array(
                'type'        => 'text',
                'title'       => __( '"Selected Categories" Label', 'smart-woocommerce-search' ),
                'value'       => 'Categories',
            ));

            ysm_setting( $w_id, 'selected_categories_location', array(
                'type'        => 'select',
                'title'       => __( 'Location', 'smart-woocommerce-search' ),
                'value'       => '',
                'is_pro'      => false,
                'choices'     => array(
                    'left_slot' => __( 'Left Sidebar Slot', 'smart-woocommerce-search' ),
                    'right_slot' => __( 'Right Sidebar Slot', 'smart-woocommerce-search' ),
                ),
            ));

            ysm_setting( $w_id, 'selected_categories_count', array(
                'type'        => 'checkbox',
                'title'       => __( 'Show Products Count', 'smart-woocommerce-search' ),
                'value'       => 1,
            ));
            ysm_setting( $w_id, 'selected_categories_on_open', array(
                'type'        => 'checkbox',
                'title'       => __( 'Display Selected Categories when Popup Initially Opened', 'smart-woocommerce-search' ),
                'value'       => 1,
            ));

            ysm_setting( $w_id, 'selected_categories_mobile', array(
	            'type'        => 'checkbox',
	            'title'       => __( 'Hide on mobile', 'smart-woocommerce-search' ),
	            'value'       => 1,
            ));
            ?>

            <th class="ymapp-settings__title"><?php esc_html_e( 'Promo banner', 'smart-woocommerce-search' ); ?></th>

            <?php
                ysm_setting( $w_id, 'promo_banner_location', array(
                    'type'        => 'select',
                    'title'       => __( 'Location', 'smart-woocommerce-search' ),
                    'value'       => '',
                    'is_pro'      => 1,
                    'choices'     => array(
                        'left_slot' => __( 'Left Sidebar Slot', 'smart-woocommerce-search' ),
                        'right_slot' => __( 'Right Sidebar Slot', 'smart-woocommerce-search' ),
                        'left_slot_2' => __( 'Left Sidebar Slot 2', 'smart-woocommerce-search' ),
                        'right_slot_2' => __( 'Right Sidebar Slot 2', 'smart-woocommerce-search' ),
                    ),
                ));

                ysm_setting( $w_id, 'promo_banner_image', array(
                    'type'        => 'image',
                    'title'       => __( 'Banner Image', 'smart-woocommerce-search' ),
                    'value'       => '',
	            	'is_pro'      => 1,
                ));

                ysm_setting( $w_id, 'promo_banner_link', array(
                    'type'        => 'text',
                    'title'       => __( 'Promo Banner Link', 'smart-woocommerce-search' ),
                    'value'       => '',
	            	'is_pro'      => 1,
                ));

                ysm_setting( $w_id, 'promo_banner_on_open', array(
                    'type'        => 'checkbox',
                    'title'       => __( 'Display Promo Banner when Popup Initially Opened', 'smart-woocommerce-search' ),
                    'value'       => 1,
                    'is_pro'      => 1,
                ));

                ysm_setting( $w_id, 'selected_promo_banner_mobile', array(
                    'type'        => 'checkbox',
                    'title'       => __( 'Hide on mobile', 'smart-woocommerce-search' ),
                    'value'       => 0,
                    'is_pro'      => 1,
                ));


            ?>

            </tbody>
        </table>
        <a class="ymapp-settings__doc_link ymapp-button ymapp-button-grey" href="https://www.wpsmartsearch.com/docs/layout-settings/" target="_blank"><?php esc_html_e( 'Documentation', 'smart-woocommerce-search' ); ?></a>
    </div>


</div>