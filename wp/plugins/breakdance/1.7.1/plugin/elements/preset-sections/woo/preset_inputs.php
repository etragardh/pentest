<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Woopresetinputs::designControls();

    $wooInputsDesign = reset($controls);

    $wooInputsDesignNoSelect2 = reset($controls);

    if ($wooInputsDesign) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wooPresetInputsDesign",
            $wooInputsDesign,
            true
        );
    }

    if ($wooInputsDesignNoSelect2) {
        array_pop($wooInputsDesignNoSelect2); // todo - how can this be correct? lol. makes no sense.

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wooPresetInputsDesignNoSelect2",
            $wooInputsDesign,
            true
        );
    }

}, 19);

