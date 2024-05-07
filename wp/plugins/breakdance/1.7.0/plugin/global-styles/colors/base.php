<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;

/**
 * @return Control
 */
function COLORS_SECTION()
{
    return c(
        "colors",
        "Colors",
        [c(
            "brand",
            "Brand",
            [],
            ['type' => 'color', 'layout' => 'inline'],
            false,
            true,
            [],
        ),
            c(
                "text",
                "Text",
                [],
                ['type' => 'color', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "headings",
                "Headings",
                [],
                ['type' => 'color', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "links",
                "Links",
                [],
                ['type' => 'color', 'layout' => 'inline'],
                false,
                true,
                [],
            ), c(
                "background",
                "Background",
                [],
                ['type' => 'color', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "palette",
                "Palette",
                [
                    control('colors', 'Colors', ['type' => 'global_colors', 'layout' => 'vertical']),
                    control('gradients', 'Gradients', [
                        'type' => 'global_colors',
                        'layout' => 'vertical',
                        'globalColorOptions' => [
                            'type' => 'gradientOnly',
                            'name' => 'Gradient'
                        ]
                    ])
                ],
                ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
                false,
                false,
                [],
            )],
        ['type' => 'section'],
        false,
        false,
        [],
    );
}

/**
 * @return string
 */
function COLORS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/colors.css.twig');
}

// Output global gradients as SVG.
add_action('wp_body_open', function () {
    $settings = \Breakdance\Data\get_global_settings_array();

    /** @var array{label:string, cssVariableName:string, value: array{svgValue:string}}[] $globalGradients */
    $globalGradients = $settings['settings']['colors']['palette']['gradients'] ?? [];

    if (!$globalGradients) return;

    $symbols = array_map(function ($gradient) {
        return str_replace('%%GRADIENTID%%', $gradient['cssVariableName'], $gradient['value']['svgValue']);
    }, $globalGradients);

    echo '<svg class="breakdance-global-gradients-sprite" aria-hidden="true">';
        echo implode("", $symbols);
    echo '</svg>';
});
