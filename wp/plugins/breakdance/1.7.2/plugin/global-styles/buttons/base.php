<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return Control
 */
function BUTTONS_SECTION()
{
    return controlSection(
        'buttons',
        'Buttons',
        [
            getPresetSection("EssentialElements\\AtomV1CustomButtonDesignNoResponsive", "Primary", "primary", ['type' => 'popout']),
            getPresetSection("EssentialElements\\AtomV1CustomButtonDesignNoResponsive", "Secondary", "secondary", ['type' => 'popout']),
        ]
    );
}

/**
 * @return string
 */
function BUTTONS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-buttons.css.twig');
}
