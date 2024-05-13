<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Blockquote",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Blockquote extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SquareQuoteIcon';
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
        return 'Blockquote';
    }

    static function className()
    {
        return 'bde-blockquote';
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
        return ['content' => ['setting' => ['author' => null], 'content' => ['author' => 'Charles Darwin', 'text' => 'It is not the strongest of the species that survives, nor the most intelligent, but the one most responsive to change.']], 'design' => ['container' => ['width' => ['breakpoint_base' => ['number' => 480, 'unit' => 'px', 'style' => '480px']], 'background' => '#FFFFFFFF', 'padding' => ['padding' => ['breakpoint_base' => ['top' => ['number' => 45, 'unit' => 'px', 'style' => '45px'], 'bottom' => ['number' => 45, 'unit' => 'px', 'style' => '45px'], 'left' => ['number' => 55, 'unit' => 'px', 'style' => '55px'], 'right' => ['number' => 55, 'unit' => 'px', 'style' => '55px']], 'breakpoint_phone_landscape' => ['left' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'right' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'top' => ['number' => 35, 'unit' => 'px', 'style' => '35px'], 'bottom' => ['number' => 35, 'unit' => 'px', 'style' => '35px']]]], 'borders' => ['radius' => ['breakpoint_base' => ['number' => 4, 'unit' => 'px', 'style' => '4px']], 'border' => ['breakpoint_base' => ['top' => [], 'bottom' => [], 'left' => ['color' => '#6115EFFF', 'width' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'style' => 'solid'], 'right' => []]], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#0000001A', 'x' => '0', 'y' => '0', 'blur' => '20', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 20px 0px #0000001A']]], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#0000001A', 'x' => '0', 'y' => '0', 'blur' => '20', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 20px 0px #0000001A']]], 'spacing' => ['wpapper' => null, 'before_author' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']], 'after_quote' => ['breakpoint_base' => ['number' => 15, 'unit' => 'px', 'style' => '15px']], 'wrapper' => null], 'quotes' => ['icon' => ['slug' => 'icon-quotes-left', 'name' => 'quotes left', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-quotes-left" viewBox="0 0 32 32">
<path d="M7.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.357-0.056 0.724-0.086 1.097-0.086zM25.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.358-0.056 0.724-0.086 1.097-0.086z"/>
</svg>'], 'icon_position' => 'above', 'size' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'color' => '#E8E8E8FF', 'alignment' => null], 'typography' => ['text' => ['color' => null], 'author' => ['color' => null]]]];
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
        "container",
        "Container",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => '%', '4' => 'vw', '5' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 100, 'max' => 1200, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "min_height",
        "Min Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => '%', '4' => 'vw', '5' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 100, 'max' => 1200, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Text",
      "text",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Author",
      "author",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "quotes",
        "Quotes",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'iconOptions' => ['suggestions' => []]],
        false,
        false,
        [],
      ), c(
        "icon_position",
        "Icon Position",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'inline', 'text' => 'Inline'], '1' => ['text' => 'Above Text', 'value' => 'above']]],
        false,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'right', 'icon' => 'AlignRightIcon']], 'condition' => ['path' => 'design.quotes.icon_position', 'operand' => 'equals', 'value' => 'above']],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 10, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'design.quotes.icon', 'operand' => 'is set', 'value' => null]],
        true,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.quotes.icon', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [c(
        "after_quote",
        "After Quote",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "before_author",
        "Before Author",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'calc'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 80, 'step' => 1]],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Wrapper",
      "wrapper",
       ['type' => 'popout']
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
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
        false,
        false,
        [],
      ), c(
        "author",
        "Author",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
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
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_bottom.%%BREAKPOINT%%']];
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
        return 600;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.author'], '1' => ['accepts' => 'string', 'path' => 'content.content.text']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes', 'dynamicBehaviorWorks' => 'yes'];
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
