<?php

namespace Breakdance\Preferences;

/**
 * @return Preferences
 */
function getDefaultPreferences()
{

    return [
        'customFonts' => [],
        'designSets' => [],
        'customBreakpoints' => [],
        'uiSettings' => [
            'autoScrollEnabled' => true,
            'showExperimentalElements' => false,
        ],
    ];
}
