<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\dumbfuck_column_layout",
        controlSection('dumbfuck_column_layout', 'Layout', [
            responsiveControl("alignment", "Alignment", [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => 'Left', 'value' => 'flex-start'),
                        array('text' => 'Center', 'value' => 'center'),
                        array('text' => 'Right', 'value' => 'flex-end'),
                    ],
            ]),
            responsiveControl("vertical_alignment", "Vertical Alignment", [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => 'top', 'value' => 'flex-start'),
                        array('text' => 'center', 'value' => 'center'),
                        array('text' => 'bottom', 'value' => 'flex-end'),
                    ],
            ]),
        ]),
        true
    );
});
