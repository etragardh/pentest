<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-checkout-totals-design",
    c(
        "totals",
        "Totals",
        [c(
            "rows",
            "Rows",
            [c(
                "spacing",
                "Spacing",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 64, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
                true,
                false,
                [],
            ), c(
                "separator",
                "Separator",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "separator_color",
                "Separator Color",
                [],
                ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.separator', 'operand' => 'is set', 'value' => '']],
                false,
                false,
                [],
            ), c(
                "separator_height",
                "Separator Height",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.separator', 'operand' => 'is set', 'value' => null]],
                false,
                false,
                [],
            )],
            ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
            false,
            false,
            [],
        ), getPresetSection(
            "EssentialElements\\WooGlobalStylerOverride",
            "Override Global Styles",
            "override_global_styles",
            ['type' => 'popout']
        )],
        ['type' => 'section'],
        false,
        false,
        [],
    ),
    true,
    null
);

