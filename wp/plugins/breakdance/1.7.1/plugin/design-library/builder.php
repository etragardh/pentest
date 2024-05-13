<?php

namespace Breakdance\DesignLibrary;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_element_controls', 'Breakdance\DesignLibrary\addControls', 69, 2);

/**
 * @param Control[] $controls
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function addControls($controls, $element)
{
    $copyableElements = getCopyableElements();
    $isCopyable = in_array($element::slug(), $copyableElements);

    if (!isDesignLibraryEnabled()) return $controls;
    if (!$isCopyable) return $controls;

    $controls['settingsSections'][] = controlSection(
        'design_library',
        'Design Library',
        [
            control('disabled', 'Disable For This Element', [
                'type' => 'toggle',
                'layout' => 'vertical'
            ]),
        ],
        ['isExternal' => true]
    );

    /** @var Control[] $controls */
    return $controls;
}
