<?php
namespace YSWS\Elements;

/**
 * Element "View All Button" html
 * @return string
 */
function view_all_button() {
	$output = '';

	if ( \Ysm_Search::get_var( 'view_all_link_text' ) ) {
		// button text
		$button_text = __( \Ysm_Search::get_var( 'view_all_link_text' ), 'smart-woocommerce-search' );

		// button url
		$param = implode( ' ', \Ysm_Search::get_search_terms() );
		$param = str_replace( '+', '%2b', $param );
		$button_url = add_query_arg( [
			's' => $param,
			'search_id' => \Ysm_Search::get_widget_id()
		], home_url('/') );

		if ( ! \Ysm_Search::get_var( 'search_page_layout_posts' ) ) {
			if ( \Ysm_Search::get_post_types( 'product' ) ) {
				$button_url = add_query_arg( [ 'post_type' => 'product' ], $button_url );
			}
		}

		// target _blank
		$target_blank = '';
		if ( \Ysm_Search::get_var( 'view_all_link_target_blank' ) ) {
			$target_blank = ' target="_blank"';
		}

		$output = '<a class="smart-search-view-all"' . $target_blank . ' href="' . esc_url( $button_url ) . '">' . esc_html( $button_text ) . '</a>';
	}

	return $output;
}
