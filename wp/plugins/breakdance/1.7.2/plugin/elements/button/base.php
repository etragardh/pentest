<?php

namespace Breakdance\Elements\AtomV1Button;

use Breakdance\Render\Twig;

/**
 * @param array $content
 * @param string $className
 * @param array $design
 * @param string $buttonStyle
 * @return string
 */
function render($content, $className, $design, $buttonStyle = 'primary')
{
    $template = getTemplate('button');

    return Twig::getInstance()->runTwig($template, [
        'content' => $content,
        'className' => $className,
        'design' => $design,
        'buttonStyle' => $buttonStyle,
    ]);
}

/**
 * @param string $text
 * @param string $className
 * @param array $design
 * @param string $buttonStyle
 * @param string $buttonId
 * @return string
 */
function renderFormButton($text, $className, $design, $buttonStyle = 'primary', $buttonId = '')
{
    $template = getTemplate('button-form');

    return Twig::getInstance()->runTwig($template, [
        'text' => $text,
        'className' => $className,
        'design' => $design,
        'buttonStyle' => $buttonStyle,
        'buttonId' => $buttonId,
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
