<?php

namespace Breakdance\Themeless;

use function Breakdance\Subscription\makeConditionProOnlyByDefault;

/**
 * @param string $category
 * @param TemplateType $templateType
 * @return void
 * 
 * SLUG MUST BE UNIQUE, EVEN ACROSS CATEGORIES
 * 
 * $category = 'Category 1', $templateType = ['slug' => 'MySlug'...]
 * 
 * $category = 'Category 2', $templateType = ['slug' => 'MySlug'...]
 * 
 * WILL CREATE A COLLISION.
 */
function registerTemplateType($category, $templateType)
{
    ThemelessController::getInstance()->registerTemplateType($category, $templateType);
}

/**
 * @param string $category
 * @return void
 */
function registerTemplateTypeCategory($category)
{
    ThemelessController::getInstance()->registerTemplateTypeCategory($category);
}

/**
 * @param TemplateCondition $condition
 * @return void
 */
function registerCondition($condition)
{
    ThemelessController::getInstance()->registerCondition(
        makeConditionProOnlyByDefault($condition)
    );
}
