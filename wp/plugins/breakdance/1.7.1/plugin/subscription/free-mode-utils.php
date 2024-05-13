<?php

namespace Breakdance\Subscription;

use function Breakdance\Elements\getSsrErrorMessage;

/**
 * @param string $label
 * @return string
 */
function appendProToLabelInFreeMode($label)
{
    return isFreeMode() ? "$label [PRO]" : $label;
}

/**
 * @param TemplateCondition $condition 
 * @return TemplateCondition 
 */
function makeConditionProOnlyByDefault($condition) {
    if (!array_key_exists('proOnly', $condition)) {
        $condition['proOnly'] = true;
    }
    return $condition;
}

/**
 * @param string $message
 * @return string
 */
function getFreeModeErrorMessage($message){
    return <<<HTML
<div class="breakdance-pro-only-element-notice">
    {$message}
</div>
HTML;
}

/**
 * @param string $elementName
 * @return string
 */
function getFreeModeOnFrontendError($elementName)
{
    return getFreeModeErrorMessage("The <b>\"$elementName\"</b> element is only available in Breakdance Pro.");
}

/**
 * @param string $elementName
 * @return string
 */
function getProOnlyConditionMessage($elementName)
{
    return getFreeModeErrorMessage("A Pro-only visibility condition was used on a \"$elementName\" element.");
}
