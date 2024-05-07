<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

// --- "All" ---
PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_all",
    controlSection('spacing_all', 'Spacing (All)', [
        responsiveControl("margin", "Margin", ['type' => "spacing_complex", "layout" => "vertical"]),
        responsiveControl("padding", "Padding", ['type' => "spacing_complex", "layout" => "vertical"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_all_not_responsive",
    controlSection('spacing_all_not_responsive', 'Spacing (All - not responsive)', [
        control("margin", "Margin", ['type' => "spacing_complex", "layout" => "vertical"]),
        control("padding", "Padding", ['type' => "spacing_complex", "layout" => "vertical"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_all_y",
    controlSection('spacing_all_y', 'Spacing (All Top & Bottom)', [
        responsiveControl("margin_top", "Margin Top", ['type' => "unit"]),
        responsiveControl("margin_bottom", "Margin Bottom", ['type' => "unit"]),
        responsiveControl("padding_top", "Padding Top", ['type' => "unit"]),
        responsiveControl("padding_bottom", "Padding Bottom", ['type' => "unit"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_all_x",
    controlSection('spacing_all_x', 'Spacing (All Left & Right)', [
        responsiveControl("margin_left", "Margin Left", ['type' => "unit"]),
        responsiveControl("margin_right", "Margin Right", ['type' => "unit"]),
        responsiveControl("padding_left", "Padding Left", ['type' => "unit"]),
        responsiveControl("padding_right", "Padding Right", ['type' => "unit"]),
    ]),
    true
);

// --- Padding ---

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_padding_all",
    controlSection('spacing_padding_all', 'Padding (All)', [
        responsiveControl("padding", "Padding", ['type' => "spacing_complex", "layout" => "vertical"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_padding_y",
    controlSection('spacing_padding_y', 'Padding (Top & Bottom)', [
        responsiveControl("padding_top", "Padding Top", ['type' => "unit"]),
        responsiveControl("padding_bottom", "Padding Bottom", ['type' => "unit"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_padding_x",
    controlSection('spacing_padding_x', 'Padding (Left & Right)', [
        responsiveControl("padding_left", "Padding Left", ['type' => "unit"]),
        responsiveControl("padding_right", "Padding Right", ['type' => "unit"]),
    ]),
    true
);

// --- Margin ---

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_margin_all",
    controlSection('spacing_margin_all', 'Margin (All)', [
        responsiveControl("margin", "Margin", ['type' => "spacing_complex", "layout" => "vertical"]),
    ]),
    true
);


PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_margin_y",
    controlSection('spacing_margin_y', 'Margin (Top & Bottom)', [
        responsiveControl("margin_top", "Margin Top", ['type' => "unit"]),
        responsiveControl("margin_bottom", "Margin Bottom", ['type' => "unit"]),
    ]),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\spacing_margin_x",
    controlSection('spacing_margin_x', 'Margin (Left & Right)', [
        responsiveControl("margin_left", "Margin Left", ['type' => "unit"]),
        responsiveControl("margin_right", "Margin Right", ['type' => "unit"]),
    ]),
    true
);
