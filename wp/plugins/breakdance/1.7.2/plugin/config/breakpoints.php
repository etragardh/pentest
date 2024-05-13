<?php

namespace Breakdance\Config\Breakpoints;

use function Breakdance\Preferences\get_preferences;

/**
 * @return Breakpoint[]
 */
function get_breakpoints()
{
    $prefs = get_preferences();

    $builtinBreakpoints = get_builtin_breakpoints();

    $customBreakpoints = $prefs['customBreakpoints'];

    $breakpoints = array_merge($builtinBreakpoints, $customBreakpoints);

    return $breakpoints;
}

/**
 * @param string $id
 * @param string $label
 * @param int|'100%' $defaultPreviewWidth
 * @param array{minWidth?:int,maxWidth?:int} $widths
 * @return Breakpoint
 */
function breakpoint(
    $id,
    $label,
    $defaultPreviewWidth,
    $widths
) {

    return array_merge(
        [
        'id' => $id,
        'label' => $label,
        'defaultPreviewWidth' => $defaultPreviewWidth,
        ],
        $widths
    );
}

/**
 * @param Breakpoint $breakpoint
 * @return string|false
 */
function mediaQueryString($breakpoint)
{

    $minWidth = $breakpoint['minWidth'] ?? false;
    $maxWidth = $breakpoint['maxWidth'] ?? false;

    $q = false;

    if (!$minWidth && $maxWidth) {
        $q = "@media (max-width: " . $maxWidth . "px)";
    } elseif ($minWidth && !$maxWidth) {
        $q = "@media (min-width: " . $minWidth . "px)";
    } elseif ($minWidth && $maxWidth) {
        $q = "@media (min-width: " . $minWidth . "px) and (max-width: " . $maxWidth . "px)";
    }

    return $q;
}
