<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function () {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Pagination::designControls();

    $pagination = reset($controls);

    if ($pagination) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\pagination",
            $pagination,
            true
        );
    }

}, 19);
