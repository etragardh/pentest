<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\CheckmarkList",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class CheckmarkList extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'CheckListIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Checkmark List';
    }

    static function className()
    {
        return 'bde-checkmark-list';
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
        return ['content' => ['content' => ['list' => ['0' => ['icon' => 'yes', 'text' => 'Lifetime unlimited license'], '1' => ['icon' => 'yes', 'text' => 'Everything you need and more'], '2' => ['icon' => 'yes', 'text' => 'All the features you want'], '3' => ['text' => 'Gutenberg integration', 'icon' => 'no'], '4' => ['icon' => 'no', 'text' => 'Composite elements']], 'positive_icon' => [], 'negative_icon' => []]], 'design' => ['icon' => ['background' => true, 'size' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']], 'radius' => ['breakpoint_base' => ['number' => 2, 'unit' => 'px', 'style' => '2px']], 'padding' => ['breakpoint_base' => ['number' => 6, 'unit' => 'px', 'style' => '6px']], 'positive_color' => '#0DA532', 'negative_color' => '#D4351E'], 'layout' => ['alignment_when_vertical' => 'left', 'horizontal' => null, 'force_vertical_stacking' => null, 'text_indent' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'space_between_items' => ['breakpoint_base' => ['number' => 28, 'unit' => 'px', 'style' => '28px']]]]];
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
        "icon",
        "Icon",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 8, 'max' => 50]],
        true,
        false,
        [],
      ), c(
        "positive_color",
        "Positive Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background.enable', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "negative_color",
        "Negative Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background.enable', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "icon_color",
        "Icon Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => ''], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50]],
        true,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => ''], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 40]],
        true,
        false,
        [],
      ), c(
        "advanced",
        "Advanced",
        [c(
        "positive_nudge",
        "Positive Nudge",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "negative_nudge",
        "Negative Nudge",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "layout",
        "Layout",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "text_indent",
        "Text Indent",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 1, 'max' => 30]],
        true,
        false,
        [],
      ), c(
        "space_between_items",
        "Space Between Items",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 75]],
        true,
        false,
        [],
      ), c(
        "alignment_when_vertical",
        "Alignment When Vertical",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'right', 'icon' => 'AlignRightIcon']], 'condition' => ['path' => 'design.layout.stacking', 'operand' => 'not equals', 'value' => 'horizontal']],
        false,
        false,
        [],
      ), c(
        "horizontal",
        "Horizontal",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Vertical', 'value' => 'vertical'], '1' => ['value' => 'horizontal', 'text' => 'Horizontal']]],
        false,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'space-between', 'text' => 'Between'], '1' => ['text' => 'Around', 'value' => 'space-around'], '2' => ['text' => 'Center', 'value' => 'center'], '3' => ['text' => 'Start', 'value' => 'flex-start'], '4' => ['text' => 'End', 'value' => 'flex-end']], 'condition' => ['path' => 'design.layout.horizontal', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "force_vertical_stacking",
        "Force Vertical Stacking",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.horizontal', 'operand' => 'is set', 'value' => 'horizontal']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'accordion']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Typography",
      "typography",
       ['type' => 'popout']
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
        "list",
        "List",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'yes', 'text' => 'Positive'], '1' => ['text' => 'Negative', 'value' => 'no']]],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{text}', 'defaultTitle' => 'List Item', 'buttonName' => '']],
        false,
        false,
        [],
      ), c(
        "positive_icon",
        "Positive Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "negative_icon",
        "Negative Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
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
        return false;
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
        return false;
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
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
        return 500;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.list[].text']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.layout.force_vertical_stacking'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
