<?php

namespace Breakdance\Themeless;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_template_previewable_items_with_search',
        'Breakdance\Themeless\getTemplatePreviewableItemsWithSearch',
        'client',
        true,
        [
            'args' => [
                'id' => FILTER_VALIDATE_INT,
                'search' => FILTER_UNSAFE_RAW,
            ]
        ]
    );
});

/**
 * @param int $id
 * @param string $search
 * @return array{data:TemplatePreviewableItem[]}
 */
function getTemplatePreviewableItemsWithSearch($id, $search)
{
    /** @var TemplatePreviewableItem[]|false $data */
    $data = SearchContext::getInstance()->executeInContext($search, function () use ($id) {
        return  \Breakdance\Themeless\getTemplatePreviewableItems($id);
    });

    if (!isset($data) || !$data || !count($data)) {
        return ['data' => []];
    }

    return ['data' => $data];
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_condition_values_with_search',
        'Breakdance\Themeless\getConditionValuesWithSearch',
        'client',
        true,
        [
            'args' => [
                'search' => FILTER_SANITIZE_SPECIAL_CHARS,
                'slug' => FILTER_SANITIZE_SPECIAL_CHARS,
            ]
        ]
    );
});

/**
 * @param string $search
 * @param string $conditionSlug
 * @return array{data:ConditionValuesCallbackReturnType}
 */
function getConditionValuesWithSearch($search, $conditionSlug)
{
    do_action('breakdance_register_template_types_and_conditions');

    /** @var ConditionValuesCallbackReturnType|false $values */
    $values = SearchContext::getInstance()->executeInContext($search, function () use ($conditionSlug) {
        $conditionList = ThemelessController::getInstance()->conditions;

        $requestedCondition = array_values(array_filter(
            $conditionList,
            function ($condition) use ($conditionSlug) {
                return $condition['slug'] === $conditionSlug;
            }
        ));

        $functionToCall = $requestedCondition['0']['values'] ?? null;

        return is_callable($functionToCall) ? call_user_func($functionToCall) : ['data' => []];
    });


    if (! isset($values) || (is_array($values) && ! count($values))) {
        return ['data' => []];
    }

    return ['data' => $values];
}
