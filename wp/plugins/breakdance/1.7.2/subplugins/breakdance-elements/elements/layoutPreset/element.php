<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Layoutpreset",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Layoutpreset extends \Breakdance\Elements\Element
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
        return 'LayoutPreset';
    }

    static function className()
    {
        return 'bde-layoutpreset';
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
        "layout",
        "Layout",
        [c(
        "display",
        "Display",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'block', 'label' => 'Label', 'value' => 'block'], '1' => ['text' => 'inline-block', 'value' => 'inline-block'], '2' => ['text' => 'inline', 'value' => 'inline'], '3' => ['text' => 'flex', 'value' => 'flex'], '4' => ['text' => 'inline-flex', 'value' => 'inline-flex'], '5' => ['text' => 'none', 'value' => 'none']]],
        true,
        false,
        [],
      ), c(
        "flex_direction",
        "Flex Direction",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'column', 'label' => 'Label', 'value' => 'column'], '1' => ['text' => 'row', 'value' => 'row'], '2' => ['text' => 'column-reverse', 'value' => 'column-reverse'], '3' => ['text' => 'row-reverse', 'value' => 'row-reverse']], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "align_items",
        "Align Items",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'flex-start', 'value' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['text' => 'stretch', 'value' => 'stretch'], '4' => ['text' => 'baseline', 'value' => 'baseline']], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "justify_content",
        "Justify Content",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'flex-start', 'value' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['text' => 'space-between', 'value' => 'space-between'], '4' => ['text' => 'space-around', 'value' => 'space-around'], '5' => ['text' => 'space-evenly', 'value' => 'space-evenly']], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "flex_wrap",
        "Flex Wrap",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'nowrap', 'value' => 'nowrap'], '1' => ['text' => 'wrap', 'value' => 'wrap'], '2' => ['text' => 'wrap-reverse', 'value' => 'wrap-reverse']], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "align_content",
        "Align Content",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'flex-start', 'value' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['text' => 'space-around', 'value' => 'space-around'], '4' => ['text' => 'stretch', 'value' => 'stretch'], '5' => ['text' => 'space-evenly', 'value' => 'space-evenly']], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "gap",
        "Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 500], 'condition' => ['path' => '%%CURRENTPATH%%.display', 'operand' => 'is one of', 'value' => ['0' => 'flex', '1' => 'inline-flex']]],
        true,
        false,
        [],
      ), c(
        "advanced",
        "Advanced",
        [c(
        "z_index",
        "Z-Index",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "overflow",
        "Overflow",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'visible', 'label' => 'Label', 'value' => 'visible'], '1' => ['text' => 'hidden', 'value' => 'hidden'], '2' => ['text' => 'scroll', 'value' => 'scroll'], '3' => ['text' => 'auto', 'value' => 'auto']]],
        true,
        false,
        [],
      ), c(
        "legacy",
        "Legacy",
        [c(
        "float",
        "Float",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'none', 'label' => 'Label', 'value' => 'none'], '1' => ['text' => 'left', 'value' => 'left'], '2' => ['text' => 'right', 'value' => 'right']]],
        true,
        false,
        [],
      ), c(
        "clear",
        "Clear",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'none', 'label' => 'Label', 'value' => 'none'], '1' => ['text' => 'left', 'value' => 'left'], '2' => ['text' => 'right', 'value' => 'right'], '3' => ['text' => 'both', 'value' => 'both']]],
        true,
        false,
        [],
      ), c(
        "visibility",
        "Visibility",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'visible', 'label' => 'Label', 'value' => 'visible'], '1' => ['text' => 'hidden', 'value' => 'hidden']]],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [c(
        "position",
        "Position",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'static', 'label' => 'Label', 'value' => 'static'], '1' => ['text' => 'relative', 'label' => 'Label', 'value' => 'relative'], '2' => ['text' => 'absolute', 'label' => 'Label', 'value' => 'absolute'], '3' => ['text' => 'fixed', 'label' => 'Label', 'value' => 'fixed'], '4' => ['text' => 'sticky', 'label' => 'Label', 'value' => 'sticky']]],
        true,
        false,
        [],
      ), c(
        "top",
        "Top",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "left",
        "Left",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "right",
        "Right",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "bottom",
        "Bottom",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "flex_child",
        "Flex Child",
        [c(
        "align_self",
        "Align Self",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'auto', 'label' => 'Label', 'value' => 'auto'], '1' => ['text' => 'flex-start', 'label' => 'Label', 'value' => 'flex-start'], '2' => ['text' => 'center', 'label' => 'Label', 'value' => 'center'], '3' => ['text' => 'flex-end', 'label' => 'Label', 'value' => 'flex-end'], '4' => ['text' => 'stretch', 'label' => 'Label', 'value' => 'stretch'], '5' => ['text' => 'baseline', 'label' => 'Label', 'value' => 'baseline']]],
        true,
        false,
        [],
      ), c(
        "order",
        "Order",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "flex_grow",
        "Flex Grow",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "flex_shrink",
        "Flex Shrink",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "flex_basis",
        "Flex Basis",
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
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'accordion']],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [];
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
        return false;
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
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
