<?php

namespace Breakdance\Themeless;

/**
 * @param Template[] $templates
 * @return Template|false
 */
function getTemplateForRequest($templates)
{

    if (\Breakdance\isRequestFromBuilderIframe() && isset($_GET['breakdance_open_document'])) {

        $breakdanceOpenDocument = (int) $_GET['breakdance_open_document'];

        $postType = get_post_type($breakdanceOpenDocument);

        if (in_array($postType, [BREAKDANCE_HEADER_POST_TYPE, BREAKDANCE_FOOTER_POST_TYPE, BREAKDANCE_POPUP_POST_TYPE])) {
            return false;
        }

        if ($postType === BREAKDANCE_TEMPLATE_POST_TYPE) {
            // this is the code path when there is nothing to preview when editing a template
            // we fallback to using /breakdance_template in the iframe URL, instead of the URL to the previewed item
            // and we display an error in the ui by outlining the preview dropdown in red
            $template = getTemplateById(ThemelessController::getInstance()->templates, $breakdanceOpenDocument);
            if ($template) return $template;
        }

    }


    return _getTemplateForRequest($templates);

}

/**
 * @param Template[] $templates
 * @param boolean $useFallbackTemplates
 * @return Template
 */
function _getTemplateForRequest($templates, $useFallbackTemplates = false)
{

    $templatesThatApply = _getAllTemplatesForRequest($templates, $useFallbackTemplates);

    return getHighestPriorityTemplate($templatesThatApply);
}

/**
 * @param Template[] $templates
 * @param boolean $useFallbackTemplates
 * @return Template[]
 */
function _getAllTemplatesForRequest($templates, $useFallbackTemplates = false)
{
    if ($useFallbackTemplates) {
        $templates = array_filter($templates, function ($template) {
            return $template['settings']['fallback'] ?? false;
        });
    } else {
        $templates = array_filter($templates, function ($template) {
            return !($template['settings']['fallback'] ?? false);
        });
    }

    $templatesThatApply = array_filter($templates,
        '\Breakdance\Themeless\doesTemplateApply');

    return $templatesThatApply;
}

/**
 * @param Template[] $templates
 * @return Template
 *
 */
function getHighestPriorityTemplate($templates)
{
    usort(
        $templates,
        '\Breakdance\Themeless\compareTemplatePriority'
    );

    return array_pop($templates);
}

/**
 * @param Template $templateA
 * @param Template $templateB
 * @return int
 */
function compareTemplatePriority($templateA, $templateB)
{
    $templateAPriority = getTemplatePriority($templateA);
    $templateBPriority = getTemplatePriority($templateB);

    if ($templateAPriority > $templateBPriority) {
        return 1;
    } elseif ($templateAPriority < $templateBPriority) {
        return -1;
    } else {
        $templateAHasRules = templateHasRuleGroup($templateA);
        $templateBHasRules = templateHasRuleGroup($templateB);

        // Give preference to the template with rules
        if ($templateAHasRules && ! $templateBHasRules) {
            return 1;
        } elseif ($templateBHasRules && ! $templateAHasRules) {
            return -1;
        }

        return 0;
    }
}

/**
 * @param Template $template
 * @return int
 */
function getTemplatePriority($template)
{
    if (isset($template['settings'])
        && isset($template['settings']['priority'])
    ) {
        return (int)$template['settings']['priority'];
    }

    return 1;
}

/**
 * @param Template $template
 * @return boolean
 *
 */
function doesTemplateApply($template)
{
    if ( ! isset($template['settings'], $template['settings']['type'])
         || ! $template['settings']
    ) {
        return false;
    }

    if (($template['settings']['disabled'] ?? false) === true) {
        return false;
    }

    $templateType = findTemplateType($template['settings']['type'],
        ThemelessController::getInstance()->templateTypeCategories);

    if ( ! $templateType
         || ! call_user_func($templateType['callback'])
         || ! isset($template['settings']['ruleGroups'])
    ) {
        return false;
    }

    $ruleGroups = $template['settings']['ruleGroups'];

    if (count($ruleGroups) < 1) {
        return true;
    }

    $evaluatedRuleGroups = array_map('\Breakdance\Themeless\doesRuleGroupApply',
        $ruleGroups);

    // This is an OR. Any rule group being true is TRUE
    return in_array(true, $evaluatedRuleGroups, true);
}

/**
 * @param TemplateRuleGroup $ruleGroup
 * @return boolean
 */
function doesRuleGroupApply($ruleGroup)
{
    if (count($ruleGroup) < 1) {
        return false;
    }

    $evaluatedRules = array_map('\Breakdance\Themeless\doesRuleApply', $ruleGroup);

    // This is an AND rule. All values must be true to pass.
    return ! in_array(false, $evaluatedRules, true);
}

/**
 * @param TemplateRule $rule
 * @return boolean
 */
function doesRuleApply($rule)
{
    if (!isset($rule['ruleSlug']) ||
        // the only operands that can be without a value are these
        !isset($rule['value']) && $rule['operand'] !== OPERAND_IS_EMPTY && $rule['operand'] !== OPERAND_IS_NOT_EMPTY
    ) {
        return false;
    }

    $maybeCondition = findCondition($rule['ruleSlug'],
        ThemelessController::getInstance()->conditions);

    /* if the rule is blank or the condition in the rule doesnt exist we say it doesn't apply
       Principle of least astonishment. If something that is not there applies, it can be surprising.
     */
    if (!$maybeCondition) {
        return false;
    }


    /*
    this is the stateful part
    in the callbacks, WP functions like is_single(), etc. are called
    to determine whether the condition should return true or false
     */

    $doesConditionPass = call_user_func(
        $maybeCondition['callback'],
        $rule['operand'] ?? '',
        getCleanValueFromRuleValue($rule['value'] ?? null),
        $rule['ruleDynamic'] ?? ''
    );

    /**
     * @psalm-suppress TooManyArguments
     */
    return (bool)apply_filters('breakdance_themeless_request_does_rule_apply', $doesConditionPass, $maybeCondition, $rule);
}

/**
 * @param string $conditionSlug
 * @param TemplateCondition[] $conditions
 * @return TemplateCondition|false
 */
function findCondition($conditionSlug, $conditions)
{
    $matchingConditions = array_filter($conditions,
        function ($condition) use ($conditionSlug) {
            return $condition['slug'] === $conditionSlug;
        });

    $matchingCondition = array_pop($matchingConditions);

    if ( ! $matchingCondition) {
        return false;
    }

    return $matchingCondition;
}

/**
 * @param TemplateTypeSlug $slug
 * @param TemplateTypeCategory[] $templateTypeCategories
 * @return TemplateType|false
 */
function findTemplateType($slug, $templateTypeCategories)
{
    $templateTypes = getTemplateTypesFromCategories($templateTypeCategories);

    $matchingTemplateTypes = array_filter($templateTypes,
        function ($templateType) use ($slug) {
            return $templateType['slug'] === $slug;
        });

    $matchingTemplateType = array_pop($matchingTemplateTypes);

    if ( ! $matchingTemplateType) {
        return false;
    }

    return $matchingTemplateType;
}

/**
 * @param TemplateTypeCategory[] $templateTypeCategories
 * @return TemplateType[]
 */
function getTemplateTypesFromCategories($templateTypeCategories)
{
    /** @var TemplateType[] */
    $templateTypes = [];

    foreach ($templateTypeCategories as $templateTypeCategory) {
        $templateTypes = array_merge($templateTypes,
            $templateTypeCategory['types']);
    }

    return $templateTypes;
}

/**
 * @param string|string[]|TemplateConditionValue[]|null $ruleValue
 * @return string|string[]|null
 */
function getCleanValueFromRuleValue($ruleValue)
{
    if (!$ruleValue) {
        return $ruleValue;
    }

    if (is_array($ruleValue)) {
        return array_map(function ($value) {
            if (is_string($value)) {
                return $value;
            }

            if (isset($value['value'])) {
                return $value['value'];
            }

            return '';
        }, $ruleValue);
    }

    return $ruleValue;
}
