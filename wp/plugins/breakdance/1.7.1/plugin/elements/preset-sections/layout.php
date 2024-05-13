<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;
use function Breakdance\Elements\controlSection;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Layoutpreset::designControls();

    $layout = reset($controls);

    if ($layout) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\layout",
            $layout,
            true
        );
    }
}, 19);
