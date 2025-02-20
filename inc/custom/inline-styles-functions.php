<?php
function ysm_add_inline_styles_to_stack( $v, $css_classes ) {

	foreach ( $css_classes as $css_class ) {
		$css_class = trim( $css_class );

		/* input styles */

		if ( isset( $v['settings']['input_border_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-field[type="search"]',
				),
				'props'     => array(
					'border-color' => $v['settings']['input_border_color'],
				),
			) );
		}

		if ( ! empty( $v['settings']['input_border_width'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-field[type="search"]',
				),
				'props'     => array(
					'border-width' => intval( $v['settings']['input_border_width'] ) . 'px',
				),
			) );
		}

		if ( isset( $v['settings']['input_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-field[type="search"]',
				),
				'props'     => array(
					'color' => $v['settings']['input_text_color'],
				),
			) );
		}

		if ( ! empty( $v['settings']['input_bg_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-field[type="search"]',
				),
				'props'     => array(
					'background-color' => $v['settings']['input_bg_color'],
				),
			) );
		}

		if ( ! empty( $v['settings']['input_height'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-field[type="search"]',
				),
				'props'     => array(
					'height' => intval( $v['settings']['input_height'] ) . 'px',
				),
			) );
		}

		if ( isset( $v['settings']['input_icon_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.search-submit',
                    '.ssf-search-icon-search',
                    '.sws-search-recent-list-item-trigger:hover',
                    '.sws-search-recent-list-item-delete:hover',
				),
				'props'     => array(
					'color' => $v['settings']['input_icon_color'],
				),
			) );
		}

		/* popup styles */

		if ( isset( $v['settings']['popup_thumb_size'] ) ) {
			$th_size = (int) $v['settings']['popup_thumb_size'];
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-suggestions .smart-search-post-icon',
				),
				'props'     => array(
					'width' => ! empty( $th_size ) ? $th_size . 'px' : '100%',
				),
			) );
		}

		if ( isset( $v['settings']['popup_border_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-popup',
				),
				'props'     => array(
					'border-color' => $v['settings']['popup_border_color'],
				),
			) );
		}

		if ( isset( $v['settings']['popup_bg_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-results',
				),
				'props'     => array(
					'background-color' => $v['settings']['popup_bg_color'],
				),
			) );
		}

		if ( isset( $v['settings']['popup_title_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-post-title',
				),
				'props'     => array(
					'color' => $v['settings']['popup_title_text_color'],
				),
			) );
		}

		if ( isset( $v['settings']['popup_desc_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-post-excerpt',
				),
				'props'     => array(
					'color' => $v['settings']['popup_desc_text_color'],
				),
			) );
		}

		if ( isset( $v['settings']['popup_view_all_link_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-view-all',
				),
				'props'     => array(
					'color' => $v['settings']['popup_view_all_link_text_color'],
				),
			) );
		}

		if ( isset( $v['settings']['popup_view_all_link_bg_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-view-all',
				),
				'props'     => array(
					'background-color' => $v['settings']['popup_view_all_link_bg_color'],
				),
			) );
		}

		if ( isset( $v['settings']['category_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-post-category',
				),
				'props'     => array(
					'color' => $v['settings']['category_text_color'],
				),
			) );
		}

		/* product styles*/

		if ( isset( $v['settings']['popup_price_text_color'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-post-price',
					'.smart-search-post-price .woocommerce-Price-amount',
					'.smart-search-post-price .woocommerce-Price-currencySymbol',
				),
				'props'     => array(
					'color' => $v['settings']['popup_price_text_color'],
				),
			) );
		}

		if ( ! empty( $v['settings']['popup_round_border'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-popup',
				),
				'props'     => array(
					'border-radius' => '10px',
					'-webkit-border-radius' => '10px',
					'-moz-border-radius' => '10px',
					'-o-border-radius' => '10px',
				),
			) );
		}

		if ( ! empty( $v['settings']['popup_top_gap'] ) ) {
			Ysm_Style_Generator::add_rule( $css_class, array(
				'selectors' => array(
					'.smart-search-popup',
				),
				'props'     => array(
					'margin-top' => intval( $v['settings']['popup_top_gap'] ) . 'px',
				),
			) );
		}

		if ( ! empty( $v['settings']['css_styles'] ) ) {
			Ysm_Style_Generator::$inline_css .= $v['settings']['css_styles'];
		}
	}
}