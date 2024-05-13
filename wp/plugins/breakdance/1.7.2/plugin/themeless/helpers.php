<?php

namespace Breakdance\Themeless;

/**
 * @param Template[] $templates
 * @param int $id
 * @return Template|false
 */
function getTemplateById($templates, $id)
{
    $matchingTemplates = array_filter(
        $templates,
        function ($template) use ($id) {
            return $template['id'] === $id;
        }
    );

    $matchingTemplate = array_pop($matchingTemplates);

    if (! $matchingTemplate) {
        return false;
    }

    return $matchingTemplate;
}

/**
 * @return TemplateConditionWithValues[]
 */
function getConditionsWithValues()
{
    $conditionsList = ThemelessController::getInstance()->conditions;

    return array_map(
        function ($condition) {
            $callbackResult
                = call_user_func($condition['values']);

            return array_merge(
                $condition,
                ['values' => $callbackResult]
            );
        },
        $conditionsList
    );
}

/**
 * @param string $postType
 * @return TemplateConditionWithValues[]
 */
function getConditionsWithValuesForPostType($postType)
{
    $conditionsList = ThemelessController::getInstance()->getConditionsForPostType($postType);

    return array_map(
        function ($condition) {
            $callbackResult
                = call_user_func($condition['values']);

            return array_merge(
                $condition,
                ['values' => $callbackResult]
            );
        },
        $conditionsList
    );
}

/**
 * @param TemplateTypeCategory[] $templateTypeCategories
 * @return JSTemplateTypeCategory[]
 */
function convertPhpTemplateTypesCategoriesToJsTemplateTypeCategories(
    $templateTypeCategories
) {
    /** @var JSTemplateTypeCategory[] */
    $jsTemplateTypeCategories = [];

    foreach ($templateTypeCategories as $cat) {
        $convertedCat               = $cat;
        $convertedCat['types']
                                    = convertTemplateTypesToArrayOfStringsAndPriorities($cat['types']);
        $jsTemplateTypeCategories[] = $convertedCat;
    }

    return $jsTemplateTypeCategories;
}

/**
 * @param TemplateType[] $templateTypes
 * @return JSTemplateType[]
 */
function convertTemplateTypesToArrayOfStringsAndPriorities($templateTypes)
{
    return array_map(function ($templateType) {
        $data = [
            'slug' => $templateType['slug'],
            'label' => $templateType['label'],
        ];

        if (isset($templateType['defaultPriority'])) {
            $data['defaultPriority'] = $templateType['defaultPriority'];
        }

        return $data;
    }, $templateTypes);
}

/**
 * @param Template|false $template
 * @return bool
 */
function templateHasRuleGroup($template)
{
    return $template !== false
           && $template['settings']
           && isset($template['settings']['ruleGroups'])
           && count($template['settings']['ruleGroups']) > 0;
}

/**
 * Options that exclude and hide the post being accessible from anywhere but the specified menu item
 * @return array
 */
function getTemplateCptsSharedArgs(){
    return [
        'public' => \Breakdance\Security\isPostTypePublicOrNot(),
        'show_ui' => false,
        'exclude_from_search' => true,
        'show_in_admin_bar' => false,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'show_in_rest' => false,
        'supports' => ['title', 'revisions'],
    ];
}
