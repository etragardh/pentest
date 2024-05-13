<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function () {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $designControls = \EssentialElements\AtomV1Button::designControls();

    $atomV1ButtonDesign = reset($designControls);

    if ($atomV1ButtonDesign) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1ButtonDesign",
            $atomV1ButtonDesign,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => ['custom.size.full_width_at', 'styles'],
            ]
        );
    }

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $contentControls = \EssentialElements\AtomV1Button::contentControls();

    $atomV1ButtonContent = reset($contentControls);

    if ($atomV1ButtonContent) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1ButtonContent",
            $atomV1ButtonContent,
            true,
            [
                'relativeDynamicPropertyPaths' => [['accepts' => 'string', 'path' => 'text'], ['accepts' => 'string', 'path' => 'link.url']],
            ]
        );
    }
});
