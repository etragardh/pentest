<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Simplelayoutpreset::designControls();

    $simpleLayout = reset($controls);

    if ($simpleLayout) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\simpleLayout",
            $simpleLayout,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => ['horizontal.vertical_at'],
            ]
        );
    }
}, 19);
