<?php

namespace Breakdance\Themeless;

use Breakdance\DynamicData\DynamicDataController;
use Breakdance\DynamicData\RepeaterField;
use function Breakdance\Util\get_public_post_types_excluding_templates;

/**
 * @param int $templateId
 * @return array|false|Template
 */
function getTemplateByIdIfItExistsAndHasSettings($templateId){
    if (!in_array(get_post_type($templateId), BREAKDANCE_ALL_TEMPLATE_POST_TYPES)){
        return false;
    }

    do_action('breakdance_register_template_types_and_conditions');
    $template = getTemplateById(
        ThemelessController::getInstance()->getAllTemplates(),
        $templateId
    );

    if (! $template) {
        return false;
    }

    if (
        ! isset($template['settings'])
        || ! $template['settings']
        || ! isset($template['settings']['type'])
    ) {
        return [];
    }

    return $template;
}

/**
 * @param int $templateId
 * @param TemplatePreviewableItem|false $lastPreviewedItem
 * @return TemplatePreviewableItem[]|array|false
 *
 * returns false if ID is not a template, returns a blank array if the template has no settings / type
 */
function getTemplatePreviewableItems($templateId, $lastPreviewedItem = false)
{
    if (get_post_type($templateId) === BREAKDANCE_BLOCK_POST_TYPE) {
        $previewableItems = array_merge(
            get_posts_as_template_previewable_items(
                ['post_type' => get_public_post_types_excluding_templates()]
            ),
            get_all_archives_as_template_previewable_items(),
        );
    } else if (get_post_type($templateId) === BREAKDANCE_ACF_BLOCK_POST_TYPE) {
        $parentId = (int) get_post_meta($templateId, 'breakdance_acf_content_parent', true);
        /** @var \WP_Post $parentPost */
        $parentPost = get_post($parentId);
        $previewableItems = [[
            'label' => $parentPost->post_title,
            'type' => $parentPost->post_type,
            'url' => get_permalink($parentPost),
        ]];
    } else {

        $template = getTemplateByIdIfItExistsAndHasSettings($templateId);

        if (!$template || !count($template)){
            return $template;
        }

        if (!isset($template['settings'], $template['settings']['type']) || !$template['settings']['type']){
            return [];
        }

        /**
         * @psalm-suppress InvalidGlobal
         */
        global $globalTemplateSettingsType;
        /** @var TemplateTypeSlug $globalTemplateSettingsType */
        $globalTemplateSettingsType = $template['settings']['type'];

        $templateType               = findTemplateType(
            $globalTemplateSettingsType,
            ThemelessController::getInstance()->templateTypeCategories
        );

        if (! $templateType) {
            return [];
        }


        /**
         * We already check above if it's an empty array
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        if (templateHasRuleGroup($template)) {
            /**
             * // This is checked by templateHasRuleGroup already
             * @psalm-suppress PossiblyUndefinedArrayOffset
             * @psalm-suppress MixedArgument
             */
            $previewableItems = getPreviewableItemsForRuleGroups(
                $template['settings']['ruleGroups'],
                $templateType['postType'] ?? null
            );

            if (count($previewableItems) === 0) {
                $previewableItems = call_user_func($templateType['templatePreviewableItems']);                
            }
        } else {
            $previewableItems
                = call_user_func($templateType['templatePreviewableItems']);
        }
    }

    /** @var TemplatePreviewableItem[]|false|array $previewableItems */
    $previewableItems = $previewableItems;

    if ($lastPreviewedItem && $previewableItems) {
        $previewableItems[] = $lastPreviewedItem;
    }

    // SORT_REGULAR compares the obj
    return $previewableItems ? array_values(array_unique($previewableItems, SORT_REGULAR)) : [];
}

/**
 * @param TemplateRuleGroup[] $ruleGroups
 * @param string|null $templatePostType
 * @return TemplatePreviewableItem[]
 */
function getPreviewableItemsForRuleGroups($ruleGroups, $templatePostType)
{
    // Remove any empty ruleGroup
    $cleanRuleGroups = array_map(
        function ($ruleGroup) {
            return array_filter($ruleGroup, function ($rule) {
                return isset($rule['ruleSlug']) && ! ! $rule['ruleSlug'];
            });
        },
        $ruleGroups
    );

    $previewableItems
        = array_map(
            function ($ruleGroup) use ($templatePostType) {
                return \Breakdance\Themeless\getPreviewableItemsForRuleGroup(
                    $ruleGroup,
                    $templatePostType
                );
            },
            $cleanRuleGroups
        );

    return array_merge(...$previewableItems);
}

/**
 * @param TemplateRuleGroup $ruleGroup
 * @param string|null $templatePostType
 * @return TemplatePreviewableItem[]
 */
function getPreviewableItemsForRuleGroup($ruleGroup, $templatePostType)
{
    $previewableItemsForEachRule
        = array_map(
            function ($rule) use ($templatePostType) {
                return \Breakdance\Themeless\getPreviewableItemsForRule(
                    $rule,
                    $templatePostType
                );
            },
            $ruleGroup
        );

    // Remove 'false' rules as these don't apply for preview items and should be ignored
    $validPreviewableItemsForEachRule = array_filter(
        $previewableItemsForEachRule,
        function ($rule) {
            return ! ! $rule;
        }
    );

    if (count($validPreviewableItemsForEachRule) > 1) {
        /** @var TemplatePreviewableItem[] */
        return array_intersect_for_deep_array($validPreviewableItemsForEachRule);
    } elseif (count($validPreviewableItemsForEachRule) === 1){
        return array_merge(...$validPreviewableItemsForEachRule);
    } else {
        return [];
    }
}

/**
 *
 * @param TemplateRule $rule
 * @param string|null $templatePostType
 * @return TemplatePreviewableItem[]|false
 */
function getPreviewableItemsForRule($rule, $templatePostType)
{
    if (! isset($rule['ruleSlug'], $rule['operand'], $rule['value'])) {
        return false;
    }

    $maybeCondition = findCondition(
        $rule['ruleSlug'],
        ThemelessController::getInstance()->conditions
    );

    if (! $maybeCondition || ! $maybeCondition['templatePreviewableItems']) {
        // return false since so this condition doesn't apply
        return false;
    }

    /* this is the stateful part. */
    $result = call_user_func(
        $maybeCondition['templatePreviewableItems'],
        $rule['operand'],
        getCleanValueFromRuleValue($rule['value']),
        $templatePostType ?? ''
    );

    return $result ?: false;
}

/**
 * @param string[] $arrayOfItems
 * @param string|false $searchTerm
 * @return string[]
 */
function filterBySearchOrReturnOriginal($arrayOfItems, $searchTerm = false)
{
    if ($searchTerm) {
        // 'array_values' creates an indexed array
        return array_values(
            array_filter($arrayOfItems, function ($haystack) use ($searchTerm) {
                if (
                    strpos(strtolower($haystack), strtolower($searchTerm))
                    !== false
                ) {
                    return true;
                }

                return false;
            })
        );
    }

    return $arrayOfItems;
}

/**
 * @param array[] $arrayWithAllArraysToIntersect
 * @return array
 */
function array_intersect_for_deep_array($arrayWithAllArraysToIntersect)
{
    // array_intersect doesn't play nice with nested arrays
    // serialize the items so that it compares them correctly
    $serializedArray = array_map(function ($arrayWithDeepArrays) {
        return array_map(function ($array) {
            return serialize($array);
        }, $arrayWithDeepArrays);
    }, $arrayWithAllArraysToIntersect);

    // find the common items present in all arrays and unserialize them
    return array_map(
        "unserialize",
        array_intersect(...$serializedArray)
    );
}
