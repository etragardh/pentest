<?php

// @psalm-ignore-file

namespace Breakdance\Elements\PresetSections;

add_action('init', function() {

    /**
     * @var Control[]
     */
    $controls = \EssentialElements\MenuBuilder::designControls();

    $desktop = $controls[0]['children'];
    $dropdown = $desktop[1]['children'];


    PresetSectionsController::getInstance()->register(
        "EssentialElements\\AtomV1MenuDropdownLinkDesign",
        $dropdown[1],
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\AtomV1MenuColumnDesign",
        $dropdown[2],
        true
    );

    PresetSectionsController::getInstance()->register(
        'EssentialElements\\AtomV1MenuDropdownLinkGraphicDesign',
        $dropdown[1]['children'][2],
        true
    );
});

add_action('init', function() {

    /**
     * @var Control[]
     */
    $controls = \EssentialElements\MenuBuilder::designControls();

    $desktop = $controls[0]['children'];
    $dropdown = $desktop[1]['children'];

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\AtomV1MenuAdditionalSectionDesign",
        $dropdown[3],
        true
    );
});
