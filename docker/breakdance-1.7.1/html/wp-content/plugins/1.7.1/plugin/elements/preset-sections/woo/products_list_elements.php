<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Wooproductslist::designControls();

    $elementsSection = reset($controls);

    if ($elementsSection) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wooProductsListElements",
            $elementsSection,
            true
        );
    }
}, 20);

add_action('init', function() {
    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Wooproductslist::designControls();

    $layoutSection = $controls[1] ?? false;

    if ($layoutSection) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wooProductsListLayout",
            $layoutSection,
            true
        );
    }
}, 20);

add_action('init', function() {
    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Wooproductslist::designControls();

    $layoutSection = $controls[2] ?? false;

    if ($layoutSection) {
        PresetSectionsController::getInstance()->register(
            'EssentialElements\\wooProductWrapperDesign',
            $layoutSection,
            true
        );
    }
}, 20);
