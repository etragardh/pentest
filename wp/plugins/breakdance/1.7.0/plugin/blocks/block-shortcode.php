<?php

namespace Breakdance\Blocks;

add_shortcode('breakdance_block', 'Breakdance\Blocks\block_shortcode_handler');

/**
 * @param array{blockId?:string}|mixed $atts
 * @return string
 */
function block_shortcode_handler($atts)
{
    if (!is_array($atts) || !array_key_exists('blockid', $atts)) {
        return "";
    }

    $blockId = (int) $atts['blockid'];

    if ($blockId) {
        return (string) \Breakdance\Render\render($blockId);
    }

    return "";
}
