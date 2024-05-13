<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\borders_without_shadows",
    controlSection(
        'borders_without_shadows',
        'Borders Without Shadows',
        [
            responsiveControl("radius", "Radius", ['type' => "border_radius", "layout" => "inline"]),
            responsiveControl("border", "Styling", ['type' => "border_complex", "layout" => "vertical"]),
        ],
    ),
    true
);
