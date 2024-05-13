<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\form-container",
    c(
        "container",
        "Container",
        [c(
            "width",
            "Width",
            [],
            ['type' => 'unit', 'layout' => 'inline'],
            true,
            false,
            [],
        ), c(
            "background",
            "Background",
            [],
            ['type' => 'color', 'layout' => 'inline'],
            false,
            false,
            [],
        ), getPresetSection(
            "EssentialElements\\spacing_padding_all",
            "Padding",
            "padding",
            ['type' => 'popout']
        ), getPresetSection(
            "EssentialElements\\borders",
            "Borders",
            "borders",
            ['type' => 'popout']
        )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout', 'preset' => ['slug' => 'EssentialElements\\form-container']]],
        false,
        false,
        [],
    ),
    true,
    null
);
