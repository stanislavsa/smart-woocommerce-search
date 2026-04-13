<?php
namespace YSWS\Elements;

/**
 * Element "View All Button" html
 * @return string
 */
function view_all_button() {
	$output = '';

	if ( ysws_get_var( 'view_all_link_text' ) ) {
		// button text
		$button_text = ysws_get_var( 'view_all_link_text' );

		if ( ysws_get_var( 'view_all_link_found_posts' ) ) {
			$button_text .= ( 1 !== \Ysm_Search::get_found_posts_count() ) ? ' %d results' : ' %d result';
			$button_text = sprintf( esc_html__( $button_text, 'smart-woocommerce-search' ), \Ysm_Search::get_found_posts_count() );
		} else {
			$button_text = __( $button_text, 'smart-woocommerce-search' );
		}

		/**
		 * Modify text in the "View All" button
		 *
		 * @param string $button_text Text in the button.
		 * @param int $found_posts Number of found posts.
		 */
		$button_text = apply_filters( 'sws_view_all_button_text', $button_text, \Ysm_Search::get_found_posts_count() );

		if ( ! $button_text ) {
			return '';
		}

		// button url
		$param = implode( ' ', \Ysm_Search::get_search_terms() );
		$param = str_replace( '+', '%2b', $param );
		$home_url = home_url( '/' );
		$lang = ysws_get_var( 'lang' );

		if ( function_exists( 'pll_home_url' ) ) {
			$home_url = pll_home_url( $lang );
		} elseif ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE !== '' ) {
			$home_url = apply_filters( 'wpml_home_url', $home_url );
		}

		$button_url = add_query_arg( [
			's' => $param,
			'search_id' => \Ysm_Search::get_widget_id()
		], $home_url );

		if ( ! ysws_get_var( 'search_page_layout_posts' ) ) {
			if ( \Ysm_Search::get_post_types( 'product' ) ) {
				$button_url = add_query_arg( [
					'post_type' => ysw_get_woocommerce_product_slug( \Ysm_Search::get_widget_id() )
				], $button_url );
			}
		}

		// target _blank
		$target_blank = '';
		if ( ysws_get_var( 'view_all_link_target_blank' ) ) {
			$target_blank = ' target="_blank"';
		}

		$output = '<a class="smart-search-view-all"' . $target_blank . ' href="' . esc_url( $button_url ) . '">' . esc_html( $button_text ) . '</a>';
	}

	return $output;
}
