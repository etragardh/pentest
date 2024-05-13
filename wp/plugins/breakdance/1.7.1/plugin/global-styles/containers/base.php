<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control
 */
function CONTAINERS_SECTION()
{

    return controlSection('containers', 'Containers', [
        c('sections', 'Sections', [
            control('container_width', 'Container Width', ['type' => 'unit']),
            control('vertical_padding', 'Vertical Padding', ['type' => 'unit'], true),
            control('horizontal_padding', 'Horizontal Padding', ['type' => 'unit'], true),
        ], ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']], false, false, []),
        control('column_gap', 'Column Gap', ['type' => 'unit'], true),
    ]);
}

/**
 * @return string
 */
function CONTAINERS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/containers.css.twig');
}
