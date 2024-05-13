<?php

namespace Breakdance\Render;

define('INCLUDE_DEFAULT_CSS_RULES_IN_POST_CSS_CACHE', true);

/**
 * @return string
 */
function getDefaultCssForAllElements()
{

    return array_reduce(
        \Breakdance\Elements\get_element_classnames(),
        /**
         * @param string $acc
         * @param string $element
         * @return string
         */
        function ($acc, $element) {
            /** @psalm-suppress InvalidStringClass */
            return $acc . "\n\n" . $element::defaultCss();
        },
        ''
    );
}
