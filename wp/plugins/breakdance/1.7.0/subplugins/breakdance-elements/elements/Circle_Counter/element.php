<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\CircleCounter",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class CircleCounter extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'TimerIcon';
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
        return 'Circle Counter';
    }

    static function className()
    {
        return 'bde-circle-counter';
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
        "size",
        "Size",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['max' => 960, 'min' => 200, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['max' => 120, 'min' => 0, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "progress_bar",
        "Progress Bar",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 200, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "background_bar",
        "Background Bar",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 200, 'step' => 1]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "style",
        "Style",
        [c(
        "value_position",
        "Value Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Inside', 'label' => 'Label', 'value' => 'inside'], '1' => ['text' => 'Outside', 'value' => 'outside']]],
        false,
        false,
        [],
      ), c(
        "space_above",
        "Space Above",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px']], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'design.style.value_position', 'operand' => 'equals', 'value' => 'outside']],
        false,
        false,
        [],
      ), c(
        "line_cap",
        "Line Cap",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Round', 'label' => 'Label', 'value' => 'round'], '1' => ['text' => 'Flat', 'value' => 'butt']]],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "background_bar",
        "Background Bar",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bar_style",
        "Bar Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Color', 'label' => 'Label', 'value' => 'color'], '1' => ['text' => 'Gradient', 'value' => 'gradient']]],
        false,
        false,
        [],
      ), c(
        "gradient",
        "Gradient",
        [c(
        "start",
        "Start",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "end",
        "End",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.style.bar_style', 'operand' => 'equals', 'value' => 'gradient']],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [c(
        "progress_bar",
        "Progress Bar",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.style.bar_style', 'operand' => 'equals', 'value' => 'color']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Value",
      "value",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Prefix",
      "prefix",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Suffix",
      "suffix",
       ['type' => 'popout']
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
        "counter",
        "Counter",
        [c(
        "value",
        "Value",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "max_value",
        "Max Value",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "prefix",
        "Prefix",
        [],
        ['type' => 'text', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "suffix",
        "Suffix",
        [],
        ['type' => 'text', 'layout' => 'inline'],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Circle_Counter/assets/circle-counter.js'],'title' => 'Load Circle Counter logic',],'1' =>  ['inlineScripts' => ['new BreakdanceCircleCounter(\'%%SELECTOR%%\', { content: {{ content.counter|json_encode }}, design: {{ design|json_encode }} });'],'builderCondition' => 'return false;','title' => 'Init on the frontend only',],];
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

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceCircleCounterInstances) window.breakdanceCircleCounterInstances = {};

    if (window.breakdanceCircleCounterInstances && window.breakdanceCircleCounterInstances[%%ID%%]) {
      window.breakdanceCircleCounterInstances[%%ID%%].destroy();
    }

    window.breakdanceCircleCounterInstances[%%ID%%] = new BreakdanceCircleCounter(\'%%SELECTOR%%\', { content: {{ content.counter|json_encode }}, design: {{ design|json_encode }} });
  }());',
],],

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceCircleCounterInstances && window.breakdanceCircleCounterInstances[%%ID%%]) {
    window.breakdanceCircleCounterInstances[%%ID%%].destroy();
  }

  window.breakdanceCircleCounterInstances[%%ID%%] = new BreakdanceCircleCounter(\'%%SELECTOR%%\', { content: {{ content.counter|json_encode }}, design: {{ design|json_encode }} });
}());',
],],

'onBeforeDeletingElement' => [['script' => ' (function() {
    if (window.breakdanceCircleCounterInstances && window.breakdanceCircleCounterInstances[%%ID%%]) {
      window.breakdanceCircleCounterInstances[%%ID%%].destroy();
      delete window.breakdanceCircleCounterInstances[%%ID%%];
    }
  }());',
],],

'onMovedElement' => [['script' => '(function() {
      if (!window.breakdanceImageHotsportInstances) window.breakdanceImageHotsportInstances = {};

      if (window.breakdanceImageHotsportInstances && window.breakdanceImageHotsportInstances[%%ID%%]) {
        window.breakdanceImageHotsportInstances[%%ID%%].destroy();
      }

      window.breakdanceCircleCounterInstances[%%ID%%] = new BreakdanceCircleCounter(\'%%SELECTOR%%\', { content: {{ content.counter|json_encode }}, design: {{ design|json_encode }} });
    }());',
],],];
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
        return 900;
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
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'sort of', 'dynamicBehaviorWorks' => 'sort of'];
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
