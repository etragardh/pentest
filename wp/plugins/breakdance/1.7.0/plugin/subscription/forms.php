<?php
namespace Breakdance\Subscription;

use Breakdance\Forms\Actions\ActionProvider;
use function Breakdance\Elements\getSsrErrorMessage;
use function Breakdance\Forms\fieldTypes;

/**
 * @return string[]
 */
function getProOnlyFieldTypes()
{
    return array_values(
        array_map(
            fn($field) => $field['slug'],
            array_filter(fieldTypes(), fn($field) => $field['proOnly'] ?? false)
        )
    );
}

/**
 * @param array $props
 * @param array[] $fields
 * @return array{controls?: boolean, fieldTypes?: boolean, actions?: boolean, advancedConditional?: boolean}|false
 */
function proOnlyFeaturesTheFormIsUsing($props, $fields)
{
    if (!freeModeOnFrontend()) return false;

    // TODO free mode: update these with the correct paths
    $proOnlyPropertyPaths = [
        'content.advanced.honeypot_enabled',
        'content.advanced.form_id'
    ];
    $proOnlyActionSlugs = getProOnlyFormActionSlugs();

    $isUsingProOnlyControls = count(
            array_filter(
                $proOnlyPropertyPaths,
                /**
                 * @param string $propertyPath
                 * @return bool
                 */
                function($propertyPath) use ($props){
                    /** @var bool */
                    return !!readFromArrayByPath($props, $propertyPath, false);
                }
            )
        ) > 0;

    $isUsingProOnlyFieldTypes = count(
            array_filter(
                $fields,
                fn($field) => in_array($field['type'] ?? '', \Breakdance\Subscription\getProOnlyFieldTypes())
            )
        ) > 0;

    /** @var string[] */
    $actions = $props['content']['actions']['actions'] ?? [];
    $isUsingProOnlyActions = count(
            array_filter(
                $actions,
                /**
                 * @param string $action
                 * @return bool
                 */
                fn($action) => in_array($action, $proOnlyActionSlugs)
            )
        ) > 0;

    $isUsingAdvancedConditionalInAnyField = count(
            array_filter(
                $fields,
                /**
                 * @param array $field
                 * @return bool
                 */
                function ($field) {
                    /** @var bool */
                    return !!($field['advanced']['conditional'] ?? false);
                }
            )
        ) > 0;

    $proFeatures = [];

    // add each value as a key to proFeatures when its true

    if ($isUsingProOnlyControls) $proFeatures['controls'] = true;
    if ($isUsingProOnlyFieldTypes) $proFeatures['fieldTypes'] = true;
    if ($isUsingProOnlyActions) $proFeatures['actions'] = true;
    if ($isUsingAdvancedConditionalInAnyField) $proFeatures['advancedConditional'] = true;

    if (count($proFeatures) === 0) return false;

    return $proFeatures;
}

/**
 * @param array $proFeaturesUsed
 * @return string
 */
function getProOnlyFormFeaturesMessage($proFeaturesUsed)
{
    $proFeaturesUsedMessage = '';

    if ($proFeaturesUsed['controls'] ?? false ) $proFeaturesUsedMessage .= 'Options, ';
    if ($proFeaturesUsed['fieldTypes'] ?? false ) $proFeaturesUsedMessage .= 'Field Types, ';
    if ($proFeaturesUsed['actions'] ?? false) $proFeaturesUsedMessage .= 'Actions, ';
    if ($proFeaturesUsed['advancedConditional']?? false) $proFeaturesUsedMessage .= 'Advanced Field Conditionals';

    $proFeaturesUsedMessage = rtrim($proFeaturesUsedMessage, ', ');

    return getFreeModeErrorMessage("Pro only features were used in a form ($proFeaturesUsedMessage).");
}

/**
 * @return array
 */
function getProOnlyFormActionSlugs()
{
    $actions = ActionProvider::getInstance();
    /** @var \Breakdance\Forms\Actions\Action[] */
    $actions = $actions->getActions();
    $proOnlyActions = array_filter(
        $actions,
        /**
         * @param \Breakdance\Forms\Actions\Action $action
         * @return string
         */
        function($action) {
            /** @var string */
            return $action::proOnly();
        }
    );
    $proOnlyActionsSlug = array_map(
        fn($action) => $action::slug(),
        $proOnlyActions
    );

    return array_values($proOnlyActionsSlug);
}

/**
 * @param \Breakdance\Forms\Actions\Action[] $actions
 * @return array{text: string, value: string}[]
 */
function getActionItemsWithProAppendedToProOnlyActions($actions)
{
    return array_map(
        fn($action) => [
            'text' => $action::proOnly() ? appendProToLabelInFreeMode($action::name()) : $action::name(),
            'value' => $action::slug()
        ],
        $actions
    );
}

/**
 * @param FormFieldType[] $fields
 * @return array{text: string, value: string}[]
 */
function getFieldItemsWithProLabelForProOnlyFields($fields){
    return array_map(
        fn($field) => [
            'text' => ($field['proOnly'] ?? false) ? appendProToLabelInFreeMode($field['label']) : $field['label'],
            'value' => $field['slug']
        ],
        $fields
    );
}
