<?php

namespace Breakdance\Themeless\ThemeDisabler;

add_action('after_setup_theme', function() {
    if (is_theme_disabled()) {

        add_theme_support("post-thumbnails");
        add_theme_support("title-tag");

    }
});
