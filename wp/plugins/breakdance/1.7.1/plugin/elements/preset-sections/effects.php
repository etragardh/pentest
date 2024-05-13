<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\removeHoverFromControlAndChildren;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Effectspreset::designControls();

    $effects = reset($controls);

    if ($effects) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\effects",
            $effects,
            true
        );

        $effectsNoHover = removeHoverFromControlAndChildren($effects);

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\effects_no_hover",
            $effectsNoHover,
            true
        );
    }




}, 19);

