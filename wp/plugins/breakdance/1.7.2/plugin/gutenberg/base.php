<?php

namespace Breakdance\Gutenberg;

require_once __DIR__ . '/blocks/src/init.php';
require_once __DIR__ . '/oembed.php';

/**
 * @param array{blockId?:int} $attrs
 * @return string
 */
function breakdance_block_render_callback($attrs)
{
    /** @psalm-suppress PossiblyUndefinedArrayOffset */
    $blockId = $attrs['blockId'] ?? null;

    if ($blockId) {
        return (string) \Breakdance\Render\render($blockId);
    }

    // Don't render anything in the frontend if no block is selected.
    return '';
}
