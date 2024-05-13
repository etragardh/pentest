<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return Control
 */
function FORMS_SECTION()
{
    return getPresetSection("EssentialElements\\AtomV1FormDesignGlobalSettings", 'Forms', 'forms', ['type' => 'accordion']);

}

/**
 * @return string
 */
function FORMS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-forms.css.twig');
}
