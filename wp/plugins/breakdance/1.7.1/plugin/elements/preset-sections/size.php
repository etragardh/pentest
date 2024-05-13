<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\size",
    controlSection('size', 'Size', [
        responsiveControl("width", "Width", ['type' => "unit"]),
        responsiveControl("min_width", "Min Width", ['type' => "unit"]),
        responsiveControl("max_width", "Max Width", ['type' => "unit"]),
        responsiveControl("height", "Height", ['type' => "unit"]),
        responsiveControl("min_height", "Min Height", ['type' => "unit"]),
        responsiveControl("max_height", "Max Height", ['type' => "unit"]),
    ]),
    true
);
