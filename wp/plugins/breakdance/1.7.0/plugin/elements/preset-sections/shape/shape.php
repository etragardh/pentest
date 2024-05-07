<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    $shapeInputDropdown = controlSection('shape_dividers_section', 'Shape Dividers', [
        repeaterControl('dividers', 'Shape Dividers', [
            control(
                'shape',
                'Shape',
                ['type' => 'dropdown', 'items' => get_divider_shapes(__DIR__)]
            ),
            control(
                'custom_shape',
                'Custom Shape',
                [
                    'type' => 'text',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.shape',
                        'operand' => 'equals',
                        'value' => 'custom',
                    ],
                    'textOptions' => ['multiline' => true]
                ]
            ),
            control(
                'color',
                'Color',
                ['type' => 'color']
            ),
            control(
                'flip_horizontally',
                'Flip Horizontally',
                ['type' => 'button_bar', 'items' =>
                    [
                        ['text' => 'Yes', 'value' => 'yes', 'icon' => 'LeftAndRightArrowsIcon'],
                    ]]
            ),
            control(
                'position',
                'Position',
                ['type' => 'button_bar', 'items' =>
                    [
                        ['text' => 'Top', 'value' => 'top', 'icon' => 'ArrowUpToLine'],
                        ['text' => 'Bottom', 'value' => 'bottom', 'icon' => 'ArrowDownToLine'],
                    ]]
            ),
            responsiveControl(
                'width',
                'Width',
                ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%'], 'rangeOptions' => ['min' => 100, 'max' => 500, 'step' => 1]],
            ),
            responsiveControl(
                'height',
                'Height',
                ['type' => 'unit']
            ),
            control(
                'bring_to_front',
                'Display in front of content',
                ['type' => 'toggle']
            ),
        ]),
    ]);
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\Shape",
        $shapeInputDropdown,
        true
    );
});


/**
 * @param string $path
 * @return string[][]
 */
function get_divider_shapes($path = __DIR__)
{

    // Shape Divider variables
    $shape_files = array_diff(scandir($path . '/shape-dividers'), array(".", ".."));

    $svgs = [['text' => 'Custom', 'value' => 'custom']];

    foreach ($shape_files as $svg) {

        $file_contents = file_get_contents($path . "/shape-dividers/" . $svg);
        $file_name = explode('.', $svg);
        $file_name = $file_name[0];
        $svg_array = ['text' => $file_name, 'value' => $file_contents];

        $svgs[] = $svg_array;
    }

    /**
     * @var array{text:string,value:string}[]
     */
    $svgs = apply_filters('breakdance_shape_dividers', $svgs);

    return $svgs;
}
