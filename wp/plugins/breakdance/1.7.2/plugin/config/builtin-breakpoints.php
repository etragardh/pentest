<?php

namespace Breakdance\Config\Breakpoints;

define('BASE_BREAKPOINT_ID', 'breakpoint_base');
define('FIRST_RESPONSIVE_BREAKPOINT_ID', 'breakpoint_tablet_landscape');

/**
 * @return Breakpoint[]
 */
function get_builtin_breakpoints()
{
    return [
        breakpoint(BASE_BREAKPOINT_ID, 'Desktop', '100%', []),
        breakpoint(FIRST_RESPONSIVE_BREAKPOINT_ID, 'Tablet Landscape', 1024, ['maxWidth' => 1119]),
        breakpoint('breakpoint_tablet_portrait', 'Tablet Portrait', 768, ['maxWidth' => 1023]),
        breakpoint('breakpoint_phone_landscape', 'Phone Landscape', 480, ['maxWidth' => 767]),
        breakpoint('breakpoint_phone_portrait', 'Phone Portrait', 400, ['maxWidth' => 479]),
    ];
}
