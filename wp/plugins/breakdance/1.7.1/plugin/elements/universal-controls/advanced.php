<?php

namespace Breakdance\Elements\UniversalControls;

use function Breakdance\CustomCSS\getResponsiveCssControl;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control[]
 */
function cssControls() {
    return [
        getResponsiveCssControl(),
        control(
            'classes',
            'Classes',
            ['type' => 'class', 'layout' => 'vertical']
        )
    ];
}


/**
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function htmlControls($element)
{

    $htmlControls = [];

    $htmlControls[] = getIdHtmlControl();
    $htmlControls[] = getAttributesHtmlControl();

    /**
     * @var Control|null $maybeTagControl
     */
    $maybeTagControl = getTagHtmlControl($element);

    if ($maybeTagControl) {
        $htmlControls[] = $maybeTagControl;
    }

    return $htmlControls;
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function draftForSections($element){
    if(\Breakdance\Elements\isElementASection($element)){
        return [
            control(
                "draft",
                "Draft",
                ['type' => 'toggle', 'layout' => 'inline'],
            )
        ];
    }

    return [];
}

/**
 * @return Control[]
 */
function wrapperStylesControls()
{
    // todo we don't need to call this 898 times we could call it once outside the function
    $modifiedClassControls = \Breakdance\ClassesSelectors\controls();

    array_pop($modifiedClassControls); // remove the custom CSS section. lol.

    $modifiedClassControls = array_map(function($controlSection) {
        /**
         * @psalm-suppress MixedArrayAssignment
         */
        $controlSection['options']['sectionOptions']['type'] = 'popout';
        return $controlSection;
    }, $modifiedClassControls);

    $section = controlSection(
        'wrapper',
        'Wrapper',
        $modifiedClassControls,
        ['isExternal' => true],
        'popout'
    );

    // $section['enableHover'] = true; // lol can we enable hover on a section? nope.

    $section2 = controlSection(
        'wrapper_hover',
        'Wrapper Hover',
        $modifiedClassControls,
        ['isExternal' => true],
        'popout'
    );

    return [$section, $section2];

}


add_filter('breakdance_element_controls', '\Breakdance\Elements\UniversalControls\addAdvancedControlsToElement', 69, 2);

/**
 * @param BuilderElementControls $controls
 * @param \Breakdance\Elements\Element $element
 * @return BuilderElementControls
 */
function addAdvancedControlsToElement($controls, $element)
{
    // todo - don't call this 80090 times. cache it
    $controls['settingsSections'][] = controlSection(
        'advanced',
        'Advanced',
        array_merge(
            cssControls(),
            wrapperStylesControls(),
            htmlControls($element),
            draftForSections($element)
        )
    );
    return $controls;

}


add_filter('breakdance_element_css_template', 'Breakdance\Elements\UniversalControls\template', 100, 1);

/**
 * @param DynamicPropertyPath[] $dynamicPropertyPaths
 * @return DynamicPropertyPath[]
 */
function addAdvancedDynamicPropertyPathsToElement($dynamicPropertyPaths) {
    $dynamicPropertyPaths[] = [
        'path' => 'settings.advanced.attributes[].value',
        'accepts' => 'string'
    ];

    return $dynamicPropertyPaths;
}
add_filter('breakdance_element_dynamic_property_paths', 'Breakdance\Elements\UniversalControls\addAdvancedDynamicPropertyPathsToElement', 100, 1);

/**
 * @param string $cssTemplate
 * @return string
 */
function template($cssTemplate)
{
    return $cssTemplate . "\n\n" . '
    %%SELECTOR%% {
        {{ macros.classOrSelectorProperties(
            settings.advanced.wrapper.background,
            settings.advanced.wrapper.layout,
            settings.advanced.wrapper.size,
            settings.advanced.wrapper.typography,
            globalSettings,
            settings.advanced.wrapper.spacing,
            settings.advanced.wrapper.borders,
            settings.advanced.wrapper.effects) }}
    }

    %%SELECTOR%%:hover {
        {{ macros.classOrSelectorProperties(
            settings.advanced.wrapper_hover.background,
            settings.advanced.wrapper_hover.layout,
            settings.advanced.wrapper_hover.size,
            settings.advanced.wrapper_hover.typography,
            globalSettings,
            settings.advanced.wrapper_hover.spacing,
            settings.advanced.wrapper_hover.borders,
            settings.advanced.wrapper_hover.effects) }}
    }
    ' . "\n\n" . '{{ settings.advanced.css }}' . "\n";
}
