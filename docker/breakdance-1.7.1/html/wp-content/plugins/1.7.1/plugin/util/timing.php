<?php

namespace Breakdance\Util\Timing;


/**
 * @param string $name
 * @param string|null $desc
 * @return array{start: float, name: string, desc: string|null}
 */
function start(string $name, string $desc = null)
{
    return [
        'start' => microtime(true),
        'name' => $name,
        'desc' => $desc
    ];
}

/**
 * @param array{start: float, name: string, desc: string|null} $start
 * @return void
 */
function finish(array $start)
{
    /** @var bool|null $enable */
    static $enable = null;

    if ($enable === null) {
        $enable = (bool)\Breakdance\Data\get_global_option('enable_render_performance_debug');
    }

    if (!$enable) {
        return;
    }

    $dur_ms = (microtime(true) - $start['start']) * 1000;

    header(sprintf('Server-Timing: %s;dur=%.02f;desc="%s"', $start['name'], $dur_ms, $start['desc'] ?? ''), false);
}
