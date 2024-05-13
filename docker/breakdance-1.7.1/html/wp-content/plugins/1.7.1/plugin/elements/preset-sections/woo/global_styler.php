<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\makeSectionsPopouts;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
    */
    $wooGlobalStylerDesign = \EssentialElements\Wooglobalstyler::designControls();

    $section = controlSection(
        'woocommerce',
        'WooCommerce',
        makeSectionsPopouts($wooGlobalStylerDesign),
        null
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\WooGlobalStylerOverride",
        $section,
        true
    );
}, 20); // global styler relies on other presets, which is added with priority 19

