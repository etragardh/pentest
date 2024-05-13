<?php

namespace Breakdance\ConditionsAPI;

/**
 * @param array{
 * slug:string,
 * label:string,
 * category:string,
 * operands:string[],
 * values?:Closure():ConditionValuesCallbackReturnType,
 * callback:Closure(string=,mixed=,string=):boolean,
 * supports:string[],
 * allowMultiselect?:boolean
 * } $condition
 */
function register($condition) {

    $valuesFunction = $condition['values'] ?? /** @return false */ function() { return false; };

    $condition_ = [
        'templatePreviewableItems' => false,
        'availableForType' => ['ALL'], // todo - what is this
        'slug' => $condition['slug'],
        'label' => $condition['label'],
        'category' => $condition['category'],
        'operands' => $condition['operands'],
        'values' => $valuesFunction,
        'callback' => $condition['callback'],
        'supports' => $condition['supports'],
    ];

    if (!isset($condition['allowMultiselect']) || !$condition['allowMultiselect']) {
        $condition_['valueInputType'] = 'dropdown';
    }

    \Breakdance\Themeless\registerCondition(
        $condition_
    );

}
