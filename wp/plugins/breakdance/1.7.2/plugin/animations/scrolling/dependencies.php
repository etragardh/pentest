<?php

namespace Breakdance\Animations\Scrolling;

add_filter('breakdance_element_dependencies', '\Breakdance\Animations\Scrolling\addDependencies', 100, 1);

/**
 * @param ElementDependenciesAndConditions[] $deps
 * @return ElementDependenciesAndConditions[]
 */
function addDependencies($deps)
{
    $condition = "return !!'{{settings.animations.scrolling_animation.enabled}}';";

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => $condition,
        "scripts" => [
            "%%BREAKDANCE_REUSABLE_GSAP%%",
            "%%BREAKDANCE_REUSABLE_SCROLL_TRIGGER%%",
            "%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/imagesloaded@4/imagesloaded.pkgd.min.js",
        ]
    ];

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => $condition,
        "scripts" => [
            BREAKDANCE_PLUGIN_URL . 'plugin/animations/scrolling/js/blur-plugin.js',
            BREAKDANCE_PLUGIN_URL . 'plugin/animations/scrolling/js/parallax.js',
        ],
    ];

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => "return false;",
        "inlineScripts" => [
            "new BreakdanceParallax(
              '%%SELECTOR%%',
              {{ settings.animations.scrolling_animation|json_encode }}
            )"
        ],
    ];

    return $deps;
}
