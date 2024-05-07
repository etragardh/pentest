<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ScrollProgress",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ScrollProgress extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ArrowDownIcon';
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
        return 'Scroll Progress';
    }

    static function className()
    {
        return 'bde-scroll-progress';
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
        return ['design' => ['container' => ['sticky' => true, 'vertical_align' => 'top', 'offset_x' => ['breakpoint_base' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'offset_y' => ['breakpoint_base' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'horizontal_align' => 'right'], 'tracker' => ['direction' => 'left']], 'content' => ['tracker' => ['type' => 'line', 'show_percentage' => false, 'relative_to' => 'page', 'track_progress_of' => null]]];
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
        "tracker",
        "Tracker",
        [c(
        "circle",
        "Circle",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 20, 'max' => 500, 'step' => 1], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'circle']],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'circle']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly']],
        false,
        false,
        [],
      ), c(
        "border_color",
        "Border Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_width",
        "Border Width",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient'], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "progress",
        "Progress",
        [c(
        "border_color",
        "Border Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_width",
        "Border Width",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient'], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'circle']],
        false,
        false,
        [],
      ), c(
        "line",
        "Line",
        [c(
        "background",
        "Background",
        [c(
        "color",
        "Color",
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
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        false,
        false,
        [],
      ), c(
        "progress",
        "Progress",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'line']],
        false,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'line']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        false,
        false,
        [],
      ), c(
        "direction",
        "Direction",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Right', 'value' => 'right'], '2' => ['text' => 'Both', 'value' => 'both']]],
        false,
        false,
        [],
      ), c(
        "percentage",
        "Percentage",
        [getPresetSection(
      "EssentialElements\\typography",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "align",
        "Align",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'row-reverse', 'text' => 'Left', 'icon' => 'FlexAlignLeftIcon'], '1' => ['text' => 'Right', 'value' => 'row', 'icon' => 'FlexAlignRightIcon']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        true,
        false,
        [],
      ), c(
        "space_after",
        "Space After",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'content.tracker.show_percentage', 'operand' => 'is set', 'value' => ''], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "container",
        "Container",
        [c(
        "sticky",
        "Sticky",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "offset_x",
        "Offset X",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']], '1' => ['0' => ['path' => 'content.tracker.type', 'operand' => 'equals', 'value' => 'circle']]]],
        true,
        false,
        [],
      ), c(
        "offset_y",
        "Offset Y",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "vertical_align",
        "Vertical Align",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'top', 'text' => 'Top', 'icon' => 'FlexAlignTopIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'FlexAlignCenterVerticalIcon'], '2' => ['text' => 'Bottom', 'value' => 'bottom', 'icon' => 'FlexAlignBottomIcon']], 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "horizontal_align",
        "Horizontal Align",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'FlexAlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'FlexAlignCenterHorizontalIcon'], '2' => ['text' => 'Right', 'value' => 'right', 'icon' => 'FlexAlignRightIcon']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'content.tracker.type', 'operand' => 'not equals', 'value' => 'circle']],
        true,
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
       ['condition' => ['path' => 'design.container.sticky', 'operand' => 'is not set', 'value' => ''], 'type' => 'popout']
     )];
    }

    static function contentControls()
    {
        return [c(
        "tracker",
        "Tracker",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'line', 'text' => 'Line'], '1' => ['text' => 'Circle', 'value' => 'circle']]],
        false,
        false,
        [],
      ), c(
        "show_percentage",
        "Show Percentage",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "track_progress_of",
        "Track Progress Of",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'page', 'text' => 'Page'], '1' => ['text' => 'Custom Selector', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "custom_selector",
        "Custom Selector",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => '#id, .custom_classs', 'condition' => ['0' => ['0' => ['path' => 'content.tracker.track_progress_of', 'operand' => 'equals', 'value' => 'custom']]]],
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
        return ['0' =>  ['title' => 'Scroll Trigger','scripts' => ['%%BREAKDANCE_REUSABLE_SCROLL_TRIGGER%%'],],'1' =>  ['title' => 'GSAP','scripts' => ['%%BREAKDANCE_REUSABLE_GSAP%%'],],'2' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-scroll-progress@1/breakdance-scroll-progress.js'],'title' => 'Load Scroll Progress',],'3' =>  ['inlineScripts' => ['new BreakdanceScrollProgress(\'%%SELECTOR%%\', { type: {{ content.tracker.type|json_encode }}, track: {{ content.tracker.track_progress_of|json_encode }}, selector: {{ content.tracker.custom_selector|json_encode }}, direction: {{ design.tracker.direction|json_encode }} } );'],'builderCondition' => 'return false;','title' => 'Load Scroll Progress Frontend',],];
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

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceScrollProgressInstances[%%ID%%]) {
    window.breakdanceScrollProgressInstances[%%ID%%].destroy();
  }

  window.breakdanceScrollProgressInstances[%%ID%%] = new BreakdanceScrollProgress(\'%%SELECTOR%%\', { type: {{ content.tracker.type|json_encode }}, track: {{ content.tracker.track_progress_of|json_encode }}, selector: {{ content.tracker.custom_selector|json_encode }}, direction: {{ design.tracker.direction|json_encode }} } );
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceScrollProgressInstances) window.breakdanceScrollProgressInstances = {};

    if (window.breakdanceScrollProgressInstances && window.breakdanceScrollProgressInstances[%%ID%%]) {
      window.breakdanceScrollProgressInstances[%%ID%%].destroy();
    }

    window.breakdanceScrollProgressInstances[%%ID%%] = new BreakdanceScrollProgress(\'%%SELECTOR%%\', { type: {{ content.tracker.type|json_encode }}, track: {{ content.tracker.track_progress_of|json_encode }}, selector: {{ content.tracker.custom_selector|json_encode }}, direction: {{ design.tracker.direction|json_encode }} } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceScrollProgressInstances && window.breakdanceScrollProgressInstances[%%ID%%]) {
    window.breakdanceScrollProgressInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceScrollProgressInstances && window.breakdanceScrollProgressInstances[%%ID%%]) {
      window.breakdanceScrollProgressInstances[%%ID%%].destroy();
      delete window.breakdanceScrollProgressInstances[%%ID%%];
    }
  }());',
],],];
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
        return 4000;
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
