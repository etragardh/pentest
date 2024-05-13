<?php
namespace Breakdance\Subscription;


use Breakdance\Themeless\ThemelessController;
use function Breakdance\Themeless\findCondition;
use function Breakdance\Themeless\getPopupTriggers;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;

/**
 * @param TemplateRuleGroup[]|null $ruleGroups
 * @return bool
 */
function conditionsContainProOnly($ruleGroups)
{
    if ($ruleGroups) {
        $flattenedRuleGroups = array_merge(...$ruleGroups);

        return count(
                array_filter($flattenedRuleGroups, function ($rule) {
                    $maybeCondition = findCondition(
                        $rule['ruleSlug'] ?? '',
                        ThemelessController::getInstance()->conditions
                    );

                    if (!$maybeCondition) return false;

                    return ($maybeCondition['proOnly'] ?? false) === true;
                })
            ) > 0;
    }

    return false;
}

/**
 * @param int $templateId
 * @param string $label
 * @return string
 */
function getProOnlyTemplateConditionMessageIfConditionsContainProOnly($templateId, $label)
{
    $templateSettings = getTemplateSettingsFromDatabase($templateId);

    if (is_array($templateSettings) && conditionsContainProOnly($templateSettings['ruleGroups'] ?? null)) {
        $locationText = $label ? " in this <b>$label</b>" : '';
        return getFreeModeErrorMessage("A pro condition was used $locationText.");
    }

    return '';
}

/**
 * @param int $templateId
 * @return bool
 */
function isPopupUsingProOnlyTriggers($templateId) {
    $popupSettings = getTemplateSettingsFromDatabase($templateId);
    $triggers = $popupSettings['triggers'] ?? [];


    if (!count($triggers)) return false;

    /** @var array{slug: string, options: array}[] $triggers */
    $triggers = $triggers;

    $proTriggers = array_filter(getPopupTriggers(), fn($trigger) => $trigger['proOnly'] ?? false);
    $proTriggersSlug = array_column($proTriggers, 'value');

    return count(array_filter($triggers, fn($trigger) => in_array($trigger['slug'], $proTriggersSlug))) > 0;
}
