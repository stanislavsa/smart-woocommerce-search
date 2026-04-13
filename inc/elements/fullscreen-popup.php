<?php
namespace YSWS\Elements;

/**
 * Element "Fullscreen Popup" html
 * @return string
 */
function fullscreen_popup() {
    $output = '';

    if ( ysws_get_var( 'fullscreen_mode' ) ) {
        $output = '<div class="sws_fullscreen_popup">fullscreen-popup</div>';
    }

    return $output;
}