<?php

namespace Breakdance\Elements\AtomV1Tabs;

use Breakdance\Render\Twig;

/**
 * @param string $id
 * @param array $tabs
 * @param array $design
 * @param array $all
 * @return string
 */
function render($id, $tabs, $design, $all)
{
    $template = getTemplate('tabs');

    return Twig::getInstance()->runTwig($template, [
        'id' => $id,
        'tabs' => $tabs,
        'design' => $design,
        'all' => $all,
    ]);
}

/**
 * @param string $name
 * @return string
 */
function getTemplate($name)
{
    /** @var array<string, string> $cache */
    static $cache = [];

    $file = __DIR__ . "/{$name}.twig";

    if (!file_exists($file)) {
        return '';
    }

    if (!isset($cache[$name])) {
        $contents = file_get_contents($file);
        $cache[$name] = $contents ?: '';
    }

    return $cache[$name];
}
