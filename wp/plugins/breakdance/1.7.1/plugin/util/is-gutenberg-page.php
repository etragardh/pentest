<?php

namespace Breakdance;

/**
 * Determine whether the current post is gutenberg or not
 * @return bool
 */
function is_gutenberg_page()
{
    if (function_exists('is_gutenberg_page') &&
        \is_gutenberg_page()
    ) {
        // The Gutenberg plugin is on.
        return true;
    }

    $current_screen = get_current_screen();

    if (!isset($current_screen) || !method_exists($current_screen, 'is_block_editor')) {
        return false;
    }

    if ($current_screen->is_block_editor()) {
        // Gutenberg page on 5+.
        return true;
    }

    return false;
}
