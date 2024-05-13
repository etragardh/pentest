<?php

namespace Breakdance\Render;

use Breakdance\MatthiasMullie\Minify;

/**
 * @param  string  $css
 *
 * @return string
 */
function formatCss($css)
{
    /**
     * @psalm-suppress UndefinedClass
     * @var mixed
     */
    $minifier = new Minify\CSS($css);

    /** @psalm-suppress MixedMethodCall */
    return (string) $minifier->minify();
}
