<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\AtomV1Button",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class AtomV1Button extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SquareIcon';
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
        return 'Atom V1 Button';
    }

    static function className()
    {
        return 'bde-atom-v1-button';
    }

    static function category()
    {
        return 'other';
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
        return false;
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
        "button",
        "Button",
        [c(
        "style",
        "Style",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'primary', 'text' => 'Primary'], '1' => ['text' => 'Secondary', 'value' => 'secondary'], '2' => ['text' => 'Custom', 'value' => 'custom'], '3' => ['text' => 'Text', 'value' => 'text']]],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [getPresetSection(
      "EssentialElements\\typography_with_hoverable_everything",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), c(
        "fancy_underline",
        "Fancy Underline",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'style-1', 'text' => 'Style 1'], '1' => ['text' => 'Style 2', 'value' => 'style-2'], '2' => ['text' => 'Style 3', 'value' => 'style-3'], '3' => ['text' => 'Style 4', 'value' => 'style-4'], '4' => ['text' => 'Style 5', 'value' => 'style-5'], '5' => ['text' => 'Style 6', 'value' => 'style-6'], '6' => ['text' => 'Style 7', 'value' => 'style-7'], '7' => ['text' => 'Style 8', 'value' => 'style-8']]],
        false,
        false,
        [],
      ), c(
        "underline_color",
        "Underline Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "arrow",
        "Arrow",
        [c(
        "arrow",
        "Arrow",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'arrow-1', 'text' => 'Arrow 1'], '1' => ['text' => 'Arrow 2', 'value' => 'arrow-2'], '2' => ['text' => 'Arrow 3', 'value' => 'arrow-3'], '3' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "custom_arrow",
        "Custom Arrow",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.arrow', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "space_before",
        "Space Before",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hover_transform",
        "Hover Transform",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.arrow', 'operand' => 'not equals', 'value' => 'arrow-3']],
        false,
        false,
        [],
      ), c(
        "arrow_size",
        "Arrow Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => '%%CURRENTPATH%%.style', 'operand' => 'equals', 'value' => 'text']],
        false,
        false,
        [],
      ), c(
        "custom",
        "Custom",
        [c(
        "size",
        "Size",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Custom', 'value' => 'custom'], '1' => ['text' => 'Small', 'value' => 'small'], '2' => ['value' => 'default', 'text' => 'Default'], '3' => ['text' => 'Large', 'value' => 'large']], 'buttonBarOptions' => ['layout' => 'multiline', 'size' => 'small']],
        true,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.size', 'operand' => 'equals', 'value' => 'custom']],
        true,
        false,
        [],
      ), c(
        "override_width",
        "Override Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "full_width_at",
        "Full Width At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        true,
        [],
      ), c(
        "outline",
        "Outline",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'items' => ['0' => ['value' => 'solid', 'text' => 'Solid'], '1' => ['text' => 'Outline', 'value' => 'outline']]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.outline', 'operand' => 'is set', 'value' => '']],
        false,
        true,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 20, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.outline', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "no_fill_on_hover",
        "No Fill On Hover",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.outline', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_hoverable_color",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "corners",
        "Corners",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'square', 'text' => 'Square'], '1' => ['text' => 'Round', 'value' => 'round'], '2' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "corner_radius",
        "Corner Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.corners', 'operand' => 'equals', 'value' => 'custom']],
        true,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'before', 'text' => 'Before'], '1' => ['text' => 'After', 'value' => 'after']]],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "space_before",
        "Space Before",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "space_after",
        "Space After",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "hover_transform",
        "Hover Transform",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "effects",
        "Effects",
        [c(
        "transition_duration",
        "Transition Duration",
        [],
        ['type' => 'unit', 'layout' => 'vertical', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms'], 'rangeOptions' => ['min' => 0, 'max' => 1000, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "scale_on_hover",
        "Scale On Hover",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 3, 'step' => 0.1]],
        true,
        false,
        [],
      ), c(
        "shadow",
        "Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        true,
        true,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => '%%CURRENTPATH%%.style', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "link",
        "Link",
        [],
        ['type' => 'link', 'layout' => 'vertical'],
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
        return false;
    }

    static function addPanelRules()
    {
        return ['alwaysHide' => true];
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
        return [];
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
        return 0;
    }

    static function dynamicPropertyPaths()
    {
        return false;
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
        return ['design.button.styles'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
