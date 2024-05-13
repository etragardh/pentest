<?php

namespace Breakdance\ClassesSelectors;

/**
 * @return Control[]
 */
function controls()
{
    return [
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\background"),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\layout"),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\size"),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\typography_with_effects_and_align"),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\spacing_all", 'Spacing', 'spacing'),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\borders_without_shadows", "Borders", 'borders'),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\effects_no_hover"),
        \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\custom_css"),
    ];
}

/**
 * @param boolean $withoutCustomCssSupport
 * @return string
 */
function template($withoutCustomCssSupport = false)
{

    $customCssSupport = $withoutCustomCssSupport ? "" : "{{ macros.css(custom_css) }}";

    return '
        %%SELECTOR%% {
            {{ macros.classOrSelectorProperties(background, layout, size, typography, globalSettings, spacing, borders, effects) }}
        }

    ' . $customCssSupport;
}
