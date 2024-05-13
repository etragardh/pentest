<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
    */
    $controls = \EssentialElements\Woopresetstock::designControls();

    $wooPresetStockDesign = reset($controls);

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\wooPresetStockDesign",
        $wooPresetStockDesign,
        true
    );
}, 19);

