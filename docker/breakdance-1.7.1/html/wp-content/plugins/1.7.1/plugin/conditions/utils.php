<?php

namespace Breakdance\Conditions;

use Breakdance\Themeless\ThemelessController;
use function Breakdance\Themeless\findCondition;

/**
 * @param array $node
 * @return TemplateRuleGroup[]|null
 */
function getSettingsConditions($node)
{
    /** @var TemplateRuleGroup[]|null */
    return $node['data']['properties']['settings']['conditions']['conditions'] ?? null;
}

