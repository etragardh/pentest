<?php

namespace Breakdance\WooCommerce;

/**
 * @param string $template
 * @param string $templateName
 * @param string $templatePath
 */
function debugTemplate($template, $templateName, $templatePath)
{
    echo "<b>Template (woocommerce_locate_template)</b>";

    \Breakdance\Debug\pre_print_r(
        [
            'template' => $template,
            'templateName' => $templateName,
            'templatePath' => $templatePath
        ]
    );

    echo "<br /><br />";
}

add_filter('woocommerce_template_overrides_scan_paths', function($paths) {
    $paths[] = ['breakdance' => BREAKDANCE_WOO_TEMPLATES_DIR];
    return $paths;
});

/*
 * wc_get_template_part handles caching
 * i don't think this code does though. it looks like it just calls load_template on the returned path
 * does that cache? i don't think so.
 * so we are calling load template for the same file 27 times in a lot of cases
 (shop loop, for example)
-*/

add_filter(
    "wc_get_template_part",
    /**
     * @param string $template
     * @param string $slug
     * @param string|null $name
     * @return string
     */
    function($template, $slug, $name) {
        if (defined('DEBUG_WOO_TEMPLATES') && DEBUG_WOO_TEMPLATES) {
            debugTemplate($template, $slug, $name);
        }

        $file = $name ? "{$slug}-{$name}.php" : "{$slug}.php";
        $path = BREAKDANCE_WOO_TEMPLATES_DIR . $file;
        return file_exists($path) ? $path : $template;
    },
    10, 3
);

// Get path for all other templates.
add_filter('wc_get_template', function($template, $templateName, $templatePath) {
    if (defined('DEBUG_WOO_TEMPLATES') && DEBUG_WOO_TEMPLATES) {
        debugTemplate($template, $templateName, $templatePath);
    }

    $path = BREAKDANCE_WOO_TEMPLATES_DIR . $templateName;
    return file_exists($path) ? $path : $template;
}, 10, 3);
