<?php
namespace Breakdance\Animations\Entrance;

add_filter('breakdance_element_dependencies', '\Breakdance\Animations\Entrance\addDependencies', 100, 1);

/**
 * @param ElementDependenciesAndConditions[] $deps
 *
 * @return ElementDependenciesAndConditions[]
 */
function addDependencies($deps)
{
    $condition = "return !!'{{settings.animations.entrance_animation.animation_type}}';";

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => $condition,
        "scripts" => [
            "%%BREAKDANCE_REUSABLE_GSAP%%",
            "%%BREAKDANCE_REUSABLE_SCROLL_TRIGGER%%",
            "%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/imagesloaded@4/imagesloaded.pkgd.min.js",
        ],
    ];

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => $condition,
        "scripts" => [
            BREAKDANCE_PLUGIN_URL . 'plugin/animations/entrance/js/entrance.js',
        ],
        "styles" => [
            BREAKDANCE_PLUGIN_URL . 'plugin/animations/entrance/css/entrance.css',
        ],
    ];

    $deps[] = [
        "frontendCondition" => $condition,
        "builderCondition" => "return false;",
        "inlineScripts" => [
            "new BreakdanceEntrance(
              '%%SELECTOR%%',
              {{ settings.animations.entrance_animation|json_encode }}
            )"
        ],
    ];

    return $deps;
}
