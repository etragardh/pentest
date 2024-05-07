<?php

namespace Breakdance\Preferences;

/**
 * @return Preferences
 */
function get_preferences()
{

    $default_breakdance_preferences = getDefaultPreferences();

    /**
     * @var string
     */
    $preferences = \Breakdance\Data\get_global_option('preferences');

    /**
     * @var Preferences|mixed
     */
    $user_breakdance_preferences = json_decode($preferences, true);

    if (!is_array($user_breakdance_preferences)) {
        $user_breakdance_preferences = [];
    }

    /**
     * @var Preferences
     */
    $prefs = array_merge($default_breakdance_preferences, $user_breakdance_preferences);

    return $prefs;
}
