<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\BasicList",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class BasicList extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ListIcon';
    }

    static function tag()
    {
        return 'ul';
    }

    static function tagOptions()
    {
        return ['ol', 'ul'];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Basic List';
    }

    static function className()
    {
        return 'bde-basic-list';
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
        return ['content' => ['content' => ['items' => ['0' => ['text' => 'This is a list element.'], '1' => ['text' => 'Spice it up with an emoji bullet point.'], '2' => ['text' => 'Control spacing between the items.'], '3' => ['text' => 'Or clear the styles and get a basic HTML list.']]]], 'design' => ['list' => ['marker_type' => 'emoji', 'emoji' => '👉', 'below_item' => ['number' => 15, 'unit' => 'px', 'style' => '15px'], 'marker_position' => 'outside', 'padding_inline_start' => ['list' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'item' => ['unit' => 'px', 'style' => '15px', 'number' => 15]]], 'spacing' => null, 'typography' => ['color' => null, 'custom' => ['breakpoint_base' => ['fontSize' => ['unit' => 'px', 'number' => 16, 'style' => '16px'], 'fontWeight' => '500', 'textTransform' => 'none', 'fontStyle' => 'normal', 'letterSpacing' => ['unit' => 'custom', 'number' => 'normal', 'style' => 'normal'], 'textDirection' => 'ltr', 'lineHeight' => ['unit' => 'custom', 'number' => 'normal', 'style' => 'normal'], 'textDecoration' => 'none']]], 'size' => null]];
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
        "list",
        "List",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "marker_type",
        "Marker Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'None', 'value' => 'none'], '1' => ['value' => 'disc', 'text' => 'Disc'], '2' => ['value' => 'square', 'text' => 'Square'], '3' => ['text' => 'Emoji', 'value' => 'emoji'], '4' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "emoji",
        "Emoji",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'design.list.marker_type', 'operand' => 'equals', 'value' => 'emoji']],
        false,
        false,
        [],
      ), c(
        "custom_hint",
        "Custom Hint",
        [],
        ['type' => 'alert_box', 'layout' => 'vertical', 'alertBoxOptions' => ['style' => 'info', 'content' => '<p><strong>Marker Type - Custom<br></strong>Use any value supported by the <em>list-style-type</em> CSS property.</p>']],
        false,
        false,
        [],
      ), c(
        "custom",
        "Custom",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'design.list.marker_type', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "marker_color",
        "Marker Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.list.marker_type', 'operand' => 'not equals', 'value' => 'emoji']],
        false,
        true,
        [],
      ), c(
        "below_item",
        "Below Item",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 30, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        false,
        false,
        [],
      ), c(
        "padding_inline_start",
        "Padding",
        [c(
        "list",
        "List",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "item",
        "Item",
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
        "marker_position",
        "Marker Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'inside', 'text' => 'Inside'], '1' => ['text' => 'Outside', 'value' => 'outside']]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align_with_hoverable_color_and_effects",
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
        "items",
        "Items",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
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
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{text}', 'defaultTitle' => 'List Item', 'buttonName' => '']],
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
        return 475;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'url', 'path' => 'content.content.items[].link.url'], '1' => ['accepts' => 'string', 'path' => 'content.content.items[].text']];
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
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
