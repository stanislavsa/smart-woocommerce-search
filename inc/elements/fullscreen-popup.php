<?php
namespace YSWS\Elements;

/**
 * Element "Fullscreen Popup" html
 * @return string
 */
function fullscreen_popup() {
    $output = '';

    if ( \Ysm_Search::get_var( 'fullscreen_mode' ) ) {
        $output = '<div class="sws_fullscreen_popup">fullscreen-popup</div>';
    }

    return $output;
}