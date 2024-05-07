<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\borders",
    controlSection(
        'borders',
        'Borders',
        [
            responsiveControl("radius", "Radius", ['type' => "border_radius", "layout" => "vertical"]),
            responsiveControl("border", "Styling", ['type' => "border_complex", "layout" => "vertical"]),
            responsiveControl("shadow", "Shadow", ['type' => "shadow", "layout" => "vertical"]),

        ],
    ),
    true
);
