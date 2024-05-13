<?php

namespace Breakdance\Elements\FilteredGets;

/**
 * @param \Breakdance\Elements\Element $element
 * @return string
 */
function cssTemplate($element)
{
    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var string
     */
    // BEWARE: JS relies on the text '\n{#%---' to exist to cleanup CSS.
    $cssTemplateWithComment = $element::cssTemplate() . "\n{#%--- Auto Generated Twig Code: ---%#}";

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @psalm-suppress TooManyArguments
     * @var string
     */
    return apply_filters('breakdance_element_css_template', $cssTemplateWithComment, $element);
}

/**
 * @return ElementAttribute[]
 */
function externalAttributes()
{
    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var ElementAttribute[]
     */
    return apply_filters(
        'breakdance_element_attributes',
        []
    );
}


/**
 * @return ElementDependenciesAndConditions[]
 */
function externalDependencies()
{
    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var ElementDependenciesAndConditions[]
     */
    return apply_filters(
        'breakdance_element_dependencies',
        []
    );
}

/**
 * @psalm-suppress MixedReturnTypeCoercion
 * @return BuilderActions[]|false
 */
function externalActions()
{
    /**
     * @var BuilderActions[]|false
     */
    $allExternalActions = apply_filters('breakdance_element_actions', []);

    if (!$allExternalActions || count($allExternalActions) === 0) {
        return [];
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    return array_merge_recursive(...$allExternalActions);
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return mixed
 */
function defaultProperties($element)
{
    return apply_filters('breakdance_element_default_properties', $element::defaultProperties());
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return BuilderElementControls
 */
function controls($element)
{

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @psalm-suppress TooManyArguments
     * @var BuilderElementControls
     */
    return apply_filters('breakdance_element_controls', [
        'contentSections' => $element::contentControls(),
        'designSections' => $element::designControls(),
        'settingsSections' => $element::settingsControls(),
    ], $element);
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return DynamicPropertyPath[]
 */
function dynamicPropertyPaths($element) {

    /**
     * @var DynamicPropertyPath[]
     */

    $paths = $element::dynamicPropertyPaths();

    /* 
    we use ? : instead of ?? because: https://github.com/soflyy/breakdance/issues/6114
    */

    /**
    * @var DynamicPropertyPath[]
    */
    return apply_filters('breakdance_element_dynamic_property_paths', $paths ? $paths : []);
}
