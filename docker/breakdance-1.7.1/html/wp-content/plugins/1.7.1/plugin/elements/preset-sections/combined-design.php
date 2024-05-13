<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\combined_design",
    controlSection(
        'combined_design',
        'Design',
        [
            getPresetSection("EssentialElements\\typography", 'Typography', 'typography', ['type' => 'popout']),
            getPresetSection("EssentialElements\\background", 'Background', 'background', ['type' => 'popout']),
            getPresetSection("EssentialElements\\spacing_padding_all", 'Spacing', 'spacing', ['type' => 'popout']),
            getPresetSection("EssentialElements\\borders", 'Borders', 'borders', ['type' => 'popout']),
        ]
    ),
    true
);
