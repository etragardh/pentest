<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;

add_action('init', function () {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Fancybackgroundpreset::designControls();

    $fancy_background = reset($controls);

    if ($fancy_background) {
        PresetSectionsController::getInstance()->register(
            'EssentialElements\\fancy_background',
            $fancy_background,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => ['image', 'overlay.image', 'image_settings.unset_image_at', 'image_settings.size', 'image_settings.height', 'image_settings.repeat', 'image_settings.position', 'image_settings.left', 'image_settings.top', 'image_settings.attachment', 'image_settings.custom_position', 'image_settings.width', 'overlay.image_settings.custom_position', 'image_size', 'overlay.image_size', 'overlay.type', 'design.layout.horizontal.vertical_at', 'image_settings', 'type'],
            ]
        );
    }
}, 19);
