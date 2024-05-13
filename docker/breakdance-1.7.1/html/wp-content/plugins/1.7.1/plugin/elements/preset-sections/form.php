<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\form",
    controlSection('form', 'Form', [
        control('theme', 'Theme', [
            'type' => 'dropdown',
            'layout' => 'vertical',
            'items' => [
                ['text' => 'Default', 'value' => 'default'],
                ['text' => 'Material Design', 'value' => 'material'],
                ['text' => 'Material Design Outlined', 'value' => 'material-outlined'],
            ],
        ]),
        control('primary_color', 'Primary Color', [
            'type' => 'color'
        ]),
        control('secondary_color', 'Secondary Color', [
            'type' => 'color'
        ]),
        control('text_color', 'Text Color', [
            'type' => 'color'
        ]),
        control('border_color', 'Border Color', [
            'type' => 'color'
        ]),
    ]),
    false
);
