<?php

namespace Breakdance\WPWidgets;

use function Breakdance\Elements\controlSection;

/**
 * @param WPWidget[] $widgets
 * @return array{text:string,value:string}[]
 */
function getWidgetsAsDropdownItems($widgets) {

    return array_map(
        function($widget) {
            return [
                'text' => $widget['name'],
                'value' => $widget['slug']
            ];
        },
        array_values($widgets)
    );

}


/**
 * @param WPWidget[] $widgets 
 * @return Control[]
 */
function getWidgetControlSections($widgets) {
    return array_map(
        function($widget) {
            return controlSection(
                $widget['slug'],
                $widget['name'].' Options',
                $widget['controls'],
                [
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.widget', 'operand' => 'equals', 'value' => $widget['slug']]
                ],
                'popout'
            );
        },
        array_values($widgets)
    );
}
