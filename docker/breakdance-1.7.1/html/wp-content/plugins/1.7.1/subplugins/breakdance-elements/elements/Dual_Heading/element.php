<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\DualHeading",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class DualHeading extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'HeadingIcon';
    }

    static function tag()
    {
        return 'h1';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return "content.content.tag";
    }

    static function name()
    {
        return 'Dual Heading';
    }

    static function className()
    {
        return 'bde-dual-heading';
    }

    static function category()
    {
        return 'blocks';
    }

    static function badge()
    {
        return false;
    }

    static function slug()
    {
        return get_class();
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function defaultProperties()
    {
        return ['content' => ['content' => ['words' => ['0' => ['text' => 'This is'], '1' => ['text' => 'amazing!', 'secondary_style' => true]]]], 'design' => ['secondary' => ['annotate' => true]]];
    }

    static function defaultChildren()
    {
        return false;
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
            "size",
            "Size",
            [c(
                "width",
                "Width",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => '%', '4' => 'vw', '5' => 'calc', '6' => 'custom']]],
                true,
                false,
                [],
            )],
            ['type' => 'section'],
            false,
            false,
            [],
        ), getPresetSection(
            "EssentialElements\\typography_with_effects_and_align",
            "Both",
            "both",
            ['type' => 'popout']
        ), c(
            "primary",
            "Primary",
            [getPresetSection(
                "EssentialElements\\typography_with_effects",
                "Typography",
                "typography",
                ['type' => 'popout']
            ), c(
                "annotate",
                "Annotate",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "effect",
                "Effect",
                [c(
                    "type",
                    "Type",
                    [],
                    ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'underline', 'label' => 'Label', 'value' => 'underline'], '1' => ['text' => 'box', 'value' => 'box'], '2' => ['text' => 'circle', 'value' => 'circle'], '3' => ['text' => 'strike-through', 'value' => 'strike-through'], '4' => ['text' => 'crossed-off', 'value' => 'crossed-off'], '5' => ['text' => 'bracket', 'value' => 'bracket']]],
                    false,
                    false,
                    [],
                ), c(
                    "color",
                    "Color",
                    [],
                    ['type' => 'color', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "duration",
                    "Duration",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "stroke_width",
                    "Stroke Width",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "multiline",
                    "Multiline",
                    [],
                    ['type' => 'toggle', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "iterations",
                    "Iterations",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "rtl",
                    "RTL",
                    [],
                    ['type' => 'toggle', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "brackets",
                    "Brackets",
                    [c(
                        "left",
                        "Left",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "right",
                        "Right",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "top",
                        "Top",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "bottom",
                        "Bottom",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    )],
                    ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'design.primary.effect.type', 'operand' => 'equals', 'value' => 'bracket']],
                    false,
                    false,
                    [],
                )],
                ['type' => 'section', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.primary.annotate', 'operand' => 'is set', 'value' => '']],
                false,
                false,
                [],
            )],
            ['type' => 'section'],
            false,
            false,
            [],
        ), c(
            "secondary",
            "Secondary",
            [getPresetSection(
                "EssentialElements\\typography_with_effects",
                "Typography",
                "typography",
                ['type' => 'popout']
            ), c(
                "annotate",
                "Annotate",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "effect",
                "Effect",
                [c(
                    "type",
                    "Type",
                    [],
                    ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'underline', 'label' => 'Label', 'value' => 'underline'], '1' => ['text' => 'box', 'value' => 'box'], '2' => ['text' => 'circle', 'value' => 'circle'], '3' => ['text' => 'strike-through', 'value' => 'strike-through'], '4' => ['text' => 'crossed-off', 'value' => 'crossed-off'], '5' => ['text' => 'bracket', 'value' => 'bracket']]],
                    false,
                    false,
                    [],
                ), c(
                    "color",
                    "Color",
                    [],
                    ['type' => 'color', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "duration",
                    "Duration",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "stroke_width",
                    "Stroke Width",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "multiline",
                    "Multiline",
                    [],
                    ['type' => 'toggle', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "iterations",
                    "Iterations",
                    [],
                    ['type' => 'number', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "rtl",
                    "RTL",
                    [],
                    ['type' => 'toggle', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                ), c(
                    "brackets",
                    "Brackets",
                    [c(
                        "left",
                        "Left",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "right",
                        "Right",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "top",
                        "Top",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    ), c(
                        "bottom",
                        "Bottom",
                        [],
                        ['type' => 'toggle', 'layout' => 'inline'],
                        false,
                        false,
                        [],
                    )],
                    ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'design.secondary.effect.type', 'operand' => 'equals', 'value' => 'bracket']],
                    false,
                    false,
                    [],
                )],
                ['type' => 'section', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.secondary.annotate', 'operand' => 'is set', 'value' => '']],
                false,
                false,
                [],
            )],
            ['type' => 'section'],
            false,
            false,
            [],
        ), getPresetSection(
            "EssentialElements\\spacing_margin_y",
            "Spacing",
            "spacing",
            ['type' => 'popout']
        )];
    }

    static function contentControls()
    {
        return [c(
            "content",
            "Content",
            [c(
                "words",
                "Words",
                [c(
                    "text",
                    "Text",
                    [],
                    ['type' => 'text', 'layout' => 'vertical'],
                    false,
                    false,
                    [],
                ), c(
                    "secondary_style",
                    "Secondary Style",
                    [],
                    ['type' => 'toggle', 'layout' => 'inline'],
                    false,
                    false,
                    [],
                )],
                ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{text}', 'defaultTitle' => '', 'buttonName' => 'Add Words']],
                false,
                false,
                [],
            ), c(
                "tag",
                "Tag",
                [],
                ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'h1', 'value' => 'h1'], '1' => ['text' => 'h2', 'value' => 'h2'], '2' => ['text' => 'h3', 'value' => 'h3'], '3' => ['text' => 'h4', 'value' => 'h4'], '4' => ['text' => 'h5', 'value' => 'h5'], '5' => ['text' => 'h6', 'value' => 'h6']]],
                false,
                false,
                [],
            )],
            ['type' => 'section', 'layout' => 'vertical'],
            false,
            false,
            [],
        )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return ['0' => ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/rough-notation@0.5/rough-notation.iife.js', '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Dual_Heading/assets/dual-heading.js'], 'frontendCondition' => '{% if design.primary.annotate
  or design.secondary.annotate %}
  return true;
{% else %}
  return false;
{% endif %}', 'title' => 'Rough Notation lib + Breakdance dual heading '], '1' => ['frontendCondition' => '{% if design.primary.annotate or design.secondary.annotate %}
  return true;
{% else %}
  return false;
{% endif %}', 'inlineScripts' => ['new BreakdanceDualHeading(\'%%SELECTOR%%\',  { content: {{ content.content|escape|json_encode }}, primary: {{ design.primary.effect|json_encode }}, secondary: {{ design.secondary.effect|json_encode }}  })'], 'builderCondition' => 'return false;', 'title' => 'Init in the frontend']];
    }

    static function settings()
    {
        return ['proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

            'onMountedElement' => [['script' => '

(function() {
            if (!window.breakdanceDualHeadingInstances) window.breakdanceDualHeadingInstances = {};

            if (window.breakdanceDualHeadingInstances && window.breakdanceDualHeadingInstances[%%ID%%]) {
            window.breakdanceDualHeadingInstances[%%ID%%].destroy();
            }

            window.breakdanceDualHeadingInstances[%%ID%%] = new BreakdanceDualHeading(\'%%SELECTOR%%\',  { content: {{ content.content|escape|json_encode }}, primary: {{ design.primary.effect|json_encode }}, secondary: {{ design.secondary.effect|json_encode }}  });

        }());

',
            ]],

            'onPropertyChange' => [['script' => '(function() {

            if (!window.breakdanceDualHeadingInstances) window.breakdanceDualHeadingInstances = {};

            if (window.breakdanceDualHeadingInstances && window.breakdanceDualHeadingInstances[%%ID%%]) {
            window.breakdanceDualHeadingInstances[%%ID%%].destroy();
            }

            window.breakdanceDualHeadingInstances[%%ID%%] = new BreakdanceDualHeading(\'%%SELECTOR%%\',  { content: {{ content.content|escape|json_encode }}, primary: {{ design.primary.effect|json_encode }}, secondary: {{ design.secondary.effect|json_encode }} });

        }());

',
            ]]];
    }

    static function nestingRule()
    {
        return ["type" => "final"];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
    }

    static function attributes()
    {
        return false;
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 1250;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.words[].text']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
