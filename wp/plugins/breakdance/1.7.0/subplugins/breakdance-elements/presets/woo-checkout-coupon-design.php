<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-checkout-coupon-design",
    c(
        "coupon",
        "Coupon",
        [c(
            "disable",
            "Disable",
            [],
            ['type' => 'toggle', 'layout' => 'inline'],
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

