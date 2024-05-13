<?php

namespace Breakdance\ClassesSelectors;

/**
 * @return array{controls:Control[],template:string,selectors:CSSSelector[]}
 */
function getSelectorsDataForBuilder()
{
    /**
     * @psalm-suppress MixedAssignment
     */
    $selectors_json_string = \Breakdance\Data\get_global_option('breakdance_classes_json_string');
    if (!$selectors_json_string) {
        $selectors = [];
    } else {
        // TODO type decoding similar to io-ts
        /**
         * @var CSSSelector[]
         */
        $selectors = json_decode((string) $selectors_json_string);
    }

    return [
        'controls' => controls(),
        'template' => template(),
        'selectors' => $selectors,
    ];
}
