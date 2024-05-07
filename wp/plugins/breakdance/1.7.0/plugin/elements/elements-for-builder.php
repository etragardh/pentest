<?php

// @psalm-ignore-file

namespace Breakdance\Elements;

use function Breakdance\Elements\PresetSections\requirePresetsAndGetData;

function get_elements_for_builder()
{
    // Element controls will get the presets, so we must require them first.
    requirePresetsAndGetData();

    return array_map(function ($elementClassName) {
        // It's okay to instantiate this as it'll be garbage collected.
        $element = new $elementClassName(); // TODO - why are we instantiating a class with only static methods.
        return array(
            'name' => $element::name(),
            'className' => $element::className(),
            'uiIcon' => $element::uiIcon(),
            'slug' => $element::slug(),
            'category' => $element::category(),
            'badge' => $element::badge(),
            'htmlTag' => [
                'default' => $element::tag(),
                'options' => $element::tagOptions(),
                'pathToControl' => $element::tagControlPath(),
            ],
            'template' => $element::template(),
            'cssTemplate' => \Breakdance\Elements\FilteredGets\cssTemplate($element),
            'defaultCss' => $element::defaultCss(),
            'attributes' => $element::attributes(),
            'defaultProperties' => \Breakdance\Elements\FilteredGets\defaultProperties($element),
            'defaultChildren' => $element::defaultChildren(),
            'dependencies' => $element::dependencies(),
            'actions' => $element::actions(),
            'controls' => \Breakdance\Elements\FilteredGets\controls($element),
            'nestingRule' => $element::nestingRule(),
            'spacingBars' => $element::spacingBars(),
            'dynamicPropertyPaths' => \Breakdance\Elements\FilteredGets\dynamicPropertyPaths($element),
            'settings' => $element::settings(),
            'addPanelRules' => $element::addPanelRules(),
            'experimental' => $element::experimental(),
            'order' => $element::order(),
            'additionalClasses' => $element::additionalClasses(),
            'projectManagement' => $element::projectManagement(),
            'propertyPathsToWhitelistInFlatProps' => $element::propertyPathsToWhitelistInFlatProps(),
            'propertyPathsToSsrElementWhenValueChanges' => $element::propertyPathsToSsrElementWhenValueChanges(),
        );
    }, get_element_classnames());
}
