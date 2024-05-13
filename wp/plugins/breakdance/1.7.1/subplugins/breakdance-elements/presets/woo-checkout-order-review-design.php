<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-checkout-order-review-design",
    c(
        "your_order",
        "Your Order",
        [c(
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
    ['relativePropertyPathsToWhitelistInFlatProps' => [], 'relativeDynamicPropertyPaths' => [], 'codeHelp' => null]
);

